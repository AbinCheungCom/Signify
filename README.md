# Signify - 企业家形象资产数字化系统

> 安全、可控、专业的企业家形象管理平台 · Laravel 11 + Vue 3 + Inertia.js

---

## ⚡ 一键部署（传统服务器 / 宝塔面板）

### 环境要求（传统服务器）

| 项目 | 版本要求 | 说明 |
|------|----------|------|
| **PHP** | 8.0+ | 推荐 8.1（性能更好） |
| **MySQL** | 5.7+ | 需 InnoDB 引擎支持 |
| **Nginx** | 1.28+ | 或 Apache 2.4+ |
| **phpMyAdmin** | 5.0+ | 数据库管理（可选） |

**PHP 扩展（必须）：** `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `gd`

> ⚠️ **重要：** MySQL 5.7 不支持 Laravel 11 默认的 enum 类型，项目已改用 `string` 类型替代，迁移文件完全兼容。

### 部署步骤

#### 第一步：创建站点和数据库

1. 登录**宝塔面板** → **网站** → **添加站点**
2. 配置：
   - **域名**：填写你的域名或IP
   - **根目录**：`/www/wwwroot/signify`
   - **PHP版本**：选择 **8.0** 或 **8.1**
   - **创建数据库**：✅ 勾选，MySQL 版本选 **5.7**，设置数据库名/用户/密码

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

# 运行数据库迁移（兼容 MySQL 5.7）
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

    # PHP-FPM（传统服务器配置）
    # 常见路径：/tmp/php-cgi.sock 或 127.0.0.1:9000
    # 请根据服务器实际情况选择正确的连接方式
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi.sock;
        # 如使用 TCP 连接，取消下面两行注释:
        # fastcgi_pass 127.0.0.1:9000;
        # fastcgi_keep_conn on;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # 静态资源缓存
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 30d;
        add_header Cache-Control "public";
    }

    # 禁止访问敏感文件
    location ~ /\.env {
        deny all;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip 压缩（低配服务器建议开启）
    gzip on;
    gzip_vary on;
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
| **敏感文件** | Nginx 禁止访问 `.env` |
| **生产模式** | `APP_DEBUG=false` + `APP_ENV=production` |

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
| 后端框架 | Laravel 11（PHP 8.0+） |
| 前端框架 | Vue 3 + Inertia.js |
| 样式 | Tailwind CSS (Tesla 风格) |
| 数据库 | MySQL 5.7+（兼容 enum 替代方案） |
| 认证 | Laravel Breeze |
| 服务器 | Nginx 1.28+ / Apache 2.4+ |

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
│   ├── migrations/           # 数据库迁移（兼容 MySQL 5.7）
│   ├── factories/            # 测试工厂
│   └── seeders/              # 管理员种子
├── deploy/
│   ├── nginx.conf            # Nginx 配置模板（传统服务器）
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