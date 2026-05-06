# Signify - 企业家形象资产数字化系统

> 企业家形象资产数字化平台，数据隔离、安全可控

## 技术栈

- **后端**: PHP 8.2+ / Laravel 11
- **数据库**: MySQL 8.0
- **前端**: Vue 3 + Inertia.js
- **样式**: Tailwind CSS
- **认证**: Laravel Breeze

## 快速开始

### 环境要求

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0

### 安装

```bash
# 克隆仓库
git clone https://github.com/AbinCheungCom/Signify.git
cd Signify

# 安装后端依赖
composer install

# 安装前端依赖
npm install

# 环境配置
cp .env.example .env
php artisan key:generate

# 创建存储链接
php artisan storage:link

# 数据库迁移
php artisan migrate

# 构建前端资源
npm run build
```

### 开发模式

```bash
# 后端
php artisan serve

# 前端热更新（另一个终端）
npm run dev
```

## 核心功能

| 模块 | 说明 |
|------|------|
| 首页 | 展示推荐企业家形象照与基本信息 |
| 企业家库 | 搜索、筛选、分页展示已认证企业家 |
| 个人中心 | 企业家修改自己的形象照与信息 |

## 数据隔离

通过 `user_id` 字段与用户表关联，配合 Laravel Policy 实现：
- 用户仅可修改自己的企业家档案
- 首页/列表页仅展示 `status='approved'` 的记录

## 项目结构

```
├── app/
│   ├── Http/Controllers/  # 控制器
│   ├── Models/            # 数据模型
│   └── Policies/          # 权限策略
├── database/
│   ├── migrations/        # 数据库迁移
│   └── factories/         # 测试工厂
├── resources/js/
│   └── Pages/             # Inertia 页面组件
├── routes/
│   └── web.php            # 路由定义
└── tests/
    └── Feature/           # 功能测试
```

## License

MIT
