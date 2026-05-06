# Signify - 企业家形象资产数字化系统

> 安全、可控、专业的企业家形象管理平台 · Laravel 11 + Vue 3 + Inertia.js

---

## ⚡ 一键部署（宝塔面板）

### 环境要求

| 项目 | 版本要求 |
|------|----------|
| PHP | 8.2+（推荐编译安装） |
| MySQL | 8.0+ |
| Nginx | 最新版 |
| 宝塔面板 | 正式版 |

**PHP 扩展（必须）：** `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`

### 部署步骤

#### 第一步：创建站点和数据库

1. 登录**宝塔面板** → **网站** → **添加站点**
2. 配置：
   - **域名**：填写你的域名或IP
   - **根目录**：`/www/wwwroot/signify`
   - **PHP版本**：选择 **8.2**
   - **创建数据库**：✅ 勾选，MySQL 版本选 **8.0**，设置数据库名/用户/密码

#### 第二步：上传代码

**方式 A - Git 克隆（推荐）：**
```bash
cd /www/wwwroot/signify
rm -rf ./*  # 清空默认文件（如有）
git clone https://github.com/AbinCheungCom/Signify.git .
```

**方式 B - 直接上传：**
下载仓库 ZIP 包，上传至 `/www/wwwroot/signify/` 并解压，确保文件在根目录下。

#### 第三步：配置 .env

```bash
cd /www/wwwroot/signify
cp .env.example .env
```

编辑 `.env` 文件（通过宝塔文件管理器或 SSH）：
```env
APP_NAME="Signify"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://你的域名.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=你创建的数据库名
DB_USERNAME=你创建的用户名
DB_PASSWORD=你的数据库密码

FILESYSTEM_DISK=public
SESSION_DRIVER=file
```

#### 第四步：安装依赖（SSH 终端）

```bash
cd /www/wwwroot/signify

# 安装 Composer 依赖
composer install --no-dev --optimize-autoloader

# 生成应用密钥
php artisan key:generate

# 创建存储链接
php artisan storage:link

# 运行数据库迁移
php artisan migrate --force

# 清除缓存
php artisan config:clear
```

#### 第五步：设置目录权限

```bash
chown -R www:www /www/wwwroot/signify
chmod -R 755 /www/wwwroot/signify/storage
chmod -R 755 /www/wwwroot/signify/bootstrap/cache
```

#### 第六步：配置网站（Nginx）

1. 宝塔面板 → **网站** → 找到你的站点 → **设置** → **配置文件**
2. 删除默认配置，替换为：

```nginx
server {
    listen 80;
    server_name 你的域名或IP;
    root /www/wwwroot/signify/public;
    index index.php;

    charset utf-8;

    # 安全响应头
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # 请求日志
    access_log /www/wwwroot/signify/storage/logs/access.log;
    error_log /www/wwwroot/signify/storage/logs/error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico {
        access_log off;
        log_not_found off;
    }

    # PHP-FPM（宝塔默认路径）
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-82.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # 静态资源缓存
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # 禁止访问敏感文件
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    # Gzip 压缩
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
}
```

3. 保存并重启 Nginx：宝塔面板 → **软件商店** → **Nginx** → **重启**

#### 第七步：SSL 证书（强烈推荐）

1. 宝塔面板 → **网站** → 你的站点 → **SSL**
2. 选择 **Let's Encrypt** → 申请免费证书
3. 开启 HTTPS 并强制跳转

#### 第八步：创建管理员账号

**方式 A - 一键安装页面：**
访问 `https://你的域名/setup`，按提示完成安装。

**方式 B - 命令行：**
```bash
cd /www/wwwroot/signify
php artisan tinker
```
```php
>>> \App\Models\User::create([
>>>     'name'=>'Admin',
>>>     'email'=>'admin@signify.com',
>>>     'password'=>\Illuminate\Support\Facades\Hash::make('你的密码'),
>>>     'is_admin'=>true,
>>>     'email_verified_at'=>now()
>>> ]);
>>> exit
```

#### 验证部署

- `https://你的域名` → 首页
- `https://你的域名/setup` → 提示已安装（或显示安装页）
- `https://你的域名/login` → 登录页
- 登录后访问 `/admin/dashboard` → 管理后台

---

## 🔐 数据安全设计

| 安全措施 | 实现方式 |
|----------|----------|
| **数据隔离** | `user_id` UNIQUE 约束 + `EntrepreneurPolicy` 验证 |
| **防注入** | LIKE 查询 `addcslashes()` 转义特殊字符 |
| **防越权** | 路由模型绑定替换为显式 `approved()` 查询 |
| **头像安全** | MIME 验证 + 扩展名白名单 + 安全文件名 |
| **管理员授权** | 中间件 + Policy 双重保护 |
| **敏感文件** | Nginx 禁止访问 `.env` / `.env.backup` |
| **安全响应头** | `X-Frame-Options` / `X-Content-Type-Options` / `X-XSS-Protection` |
| **生产调试** | `APP_DEBUG=false` + `APP_ENV=production` 强制 |

---

## 📋 一键重装（保留数据库）

```bash
cd /www/wwwroot/signify
git pull
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan config:clear
chown -R www:www /www/wwwroot/signify
```

---

## 🐳 Docker 部署（可选）

```bash
cd Signify
docker-compose up -d
# 访问 http://localhost
```

---

## 技术栈

| 层级 | 技术 |
|------|------|
| 后端框架 | Laravel 11 |
| 前端框架 | Vue 3 + Inertia.js |
| 样式 | Tailwind CSS (Tesla 风格) |
| 数据库 | MySQL 8.0 |
| 认证 | Laravel Breeze |
| PHP 版本 | 8.2+ |

---

## 核心功能

| 模块 | 说明 |
|------|------|
| **首页** | 展示推荐企业家（摄影主导，Tesla 风格） |
| **企业家库** | 搜索/筛选/分页，仅显示已认证档案 |
| **个人中心** | 头像上传/信息编辑，Policy 保护 |
| **管理后台** | 审批/拒绝/推荐/批量操作 |
| **一键安装** | 数据库连接测试 + 自动建库 |
| **数据隔离** | 用户仅可修改自己的档案 |

---

## 项目结构

```
Signify/
├── app/
│   ├── Http/Controllers/     # 控制器（Auth/Admin/Entrepreneur/Setup）
│   ├── Models/               # 数据模型
│   ├── Policies/            # 权限策略
│   └── Middleware/           # 中间件
├── config/                   # Laravel 配置
├── database/
│   ├── migrations/           # 数据库迁移
│   ├── factories/            # 测试工厂
│   └── seeders/              # 管理员种子
├── deploy/
│   ├── nginx.conf            # Nginx 配置模板
│   └── 宝塔面板部署教程.md    # 详细部署文档
├── resources/js/
│   └── Pages/                # Inertia 页面组件
├── routes/
│   ├── web.php               # 主路由
│   ├── auth.php              # 认证路由
│   └── setup.php             # 安装路由
├── tests/Feature/           # 功能测试
└── .env.example              # 环境变量模板
```

---

## License

MIT · [GitHub](https://github.com/AbinCheungCom/Signify)