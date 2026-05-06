#!/bin/bash

# =============================================================================
# Signify 一键部署脚本
# 适用于：宝塔面板 / 传统 Linux 服务器
# 环境要求：PHP 8.0+ / MySQL 5.7+ / Nginx 1.28+
# =============================================================================

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 打印函数
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查命令是否存在
check_command() {
    if ! command -v "$1" &> /dev/null; then
        print_error "$1 未安装，请先安装 $1"
        exit 1
    fi
}

# 检查 PHP 版本
check_php_version() {
    PHP_VERSION=$(php -v | grep -oP '\d+\.\d+' | head -1)
    REQUIRED_VERSION="8.0"

    if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED_VERSION" ]; then
        print_error "PHP 版本必须 >= 8.0，当前版本: $PHP_VERSION"
        exit 1
    fi
    print_success "PHP 版本检查通过: $PHP_VERSION"
}

# 检查 PHP 扩展
check_php_extensions() {
    print_info "检查 PHP 扩展..."
    REQUIRED_EXTENSIONS=("pdo_mysql" "mbstring" "openssl" "fileinfo" "curl" "gd" "tokenizer" "xml" "ctype" "json" "bcmath")

    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if ! php -m | grep -q "^$ext$"; then
            print_error "缺少 PHP 扩展: $ext"
            exit 1
        fi
    done
    print_success "所有 PHP 扩展检查通过"
}

# 显示 banner
show_banner() {
    echo ""
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                                                              ║"
    echo "║           Signify 一键部署脚本                              ║"
    echo "║           企业家形象资产数字化系统                          ║"
    echo "║                                                              ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo ""
}

# 主部署流程
main() {
    show_banner

    # 检查是否为 root 用户
    if [ "$EUID" -ne 0 ]; then
        print_warning "建议使用 root 用户运行此脚本，否则可能遇到权限问题"
        read -p "是否继续? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi

    # 检查必要命令
    print_info "检查必要命令..."
    check_command "php"
    check_command "composer"
    check_command "mysql" || check_command "mariadb"
    print_success "必要命令检查通过"

    # 检查 PHP 版本和扩展
    check_php_version
    check_php_extensions

    # 获取部署信息
    echo ""
    print_info "请提供部署信息:"
    echo ""

    read -p "网站域名 (例如: signify.example.com): " DOMAIN
    while [ -z "$DOMAIN" ]; do
        print_error "域名不能为空"
        read -p "网站域名: " DOMAIN
    done

    read -p "网站目录 (默认: /www/wwwroot/signify): " WEB_ROOT
    WEB_ROOT=${WEB_ROOT:-/www/wwwroot/signify}

    read -p "数据库主机 (默认: 127.0.0.1): " DB_HOST
    DB_HOST=${DB_HOST:-127.0.0.1}

    read -p "数据库端口 (默认: 3306): " DB_PORT
    DB_PORT=${DB_PORT:-3306}

    read -p "数据库名 (默认: signify): " DB_DATABASE
    DB_DATABASE=${DB_DATABASE:-signify}

    read -p "数据库用户名 (默认: root): " DB_USERNAME
    DB_USERNAME=${DB_USERNAME:-root}

    read -sp "数据库密码: " DB_PASSWORD
    echo
    while [ -z "$DB_PASSWORD" ]; do
        print_error "数据库密码不能为空"
        read -sp "数据库密码: " DB_PASSWORD
        echo
    done

    read -p "管理员邮箱 (默认: admin@signify.com): " ADMIN_EMAIL
    ADMIN_EMAIL=${ADMIN_EMAIL:-admin@signify.com}

    read -sp "管理员密码 (至少8位): " ADMIN_PASSWORD
    echo
    while [ -z "$ADMIN_PASSWORD" ] || [ ${#ADMIN_PASSWORD} -lt 8 ]; do
        print_error "管理员密码不能为空且至少8位"
        read -sp "管理员密码: " ADMIN_PASSWORD
        echo
    done

    # 确认信息
    echo ""
    print_info "请确认部署信息:"
    echo "  域名: $DOMAIN"
    echo "  目录: $WEB_ROOT"
    echo "  数据库: $DB_DATABASE@$DB_HOST:$DB_PORT"
    echo "  管理员: $ADMIN_EMAIL"
    echo ""
    read -p "确认开始部署? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "已取消部署"
        exit 0
    fi

    # 开始部署
    echo ""
    print_info "开始部署..."
    echo ""

    # 创建目录
    print_info "创建网站目录..."
    mkdir -p "$WEB_ROOT"
    cd "$WEB_ROOT"

    # 检查是否已有代码
    if [ -f "composer.json" ]; then
        print_warning "检测到已有代码，执行更新..."
        git pull 2>/dev/null || print_warning "未找到 Git 仓库，跳过更新"
    else
        print_info "克隆代码..."
        git clone https://github.com/AbinCheungCom/Signify.git .
    fi

    # 创建 .env
    print_info "创建配置文件..."
    cp .env.example .env
    sed -i "s/APP_NAME=.*/APP_NAME=\"Signify\"/" .env
    sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
    sed -i "s/APP_URL=.*/APP_URL=https:\/\/$DOMAIN/" .env
    sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

    # 安装依赖
    print_info "安装 Composer 依赖..."
    composer install --no-dev --optimize-autoloader --no-interaction

    # 生成密钥
    print_info "生成应用密钥..."
    php artisan key:generate --force

    # 创建存储链接
    print_info "创建存储链接..."
    php artisan storage:link --force 2>/dev/null || print_warning "存储链接已存在或创建失败"

    # 运行迁移
    print_info "运行数据库迁移..."
    php artisan migrate --force --no-interaction

    # 创建管理员
    print_info "创建管理员账号..."
    php artisan tinker --execute="
        use App\Models\User;
        use Illuminate\Support\Facades\Hash;
        User::create([
            'name' => 'Admin',
            'email' => '$ADMIN_EMAIL',
            'password' => Hash::make('$ADMIN_PASSWORD'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    "

    # 设置权限
    print_info "设置目录权限..."
    chown -R www:www "$WEB_ROOT"
    chmod -R 755 "$WEB_ROOT/storage"
    chmod -R 755 "$WEB_ROOT/bootstrap/cache"
    chmod 600 "$WEB_ROOT/.env"

    # 清除缓存
    print_info "清除缓存..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear

    # 完成
    echo ""
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                                                              ║"
    echo "║           🎉 部署成功！                                      ║"
    echo "║                                                              ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo ""
    print_success "网站地址: https://$DOMAIN"
    print_success "管理员邮箱: $ADMIN_EMAIL"
    print_info "请立即登录后台修改默认配置"
    echo ""
    print_info "如需配置 SSL 证书，请访问宝塔面板 → 网站 → SSL"
    echo ""
}

# 执行主函数
main "$@"
