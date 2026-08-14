# 批量导入艺术家 — 使用说明

`artists.json` 是批量导入的数据源，一条记录对应一位艺术家。

## 字段说明

| 字段 | 必填 | 说明 |
|---|---|---|
| `name` | ✅ | 姓名。**留空的行会被跳过**，不会创建任何数据 |
| `title` | 可选 | 称号，如「国画大师」「书法家」，显示在名片姓名下方 |
| `industry` | 可选 | 领域，如 书画 / 戏曲 / 民乐，用于智库筛选 |
| `city` | 可选 | 城市 |
| `bio` | 可选 | 简介，支持换行 |
| `featured` | 可选 | `true` 才会显示在智库列表（默认 `true`，请勿改成 `false`） |
| `email` | 可选 | 用户账号邮箱；留空自动生成（艺术家不登录可不用管） |
| `avatar` | 可选 | 头像图片文件名，需放到 `database/data/avatars/` 目录，支持 jpg/jpeg/png/gif/webp |

## 步骤

1. 编辑 `database/data/artists.json`，填好每位艺术家的信息（最多 10 位，只填需要放的）
2. 如有头像，把图片放进 `database/data/avatars/`
3. 在项目根目录执行：

```bash
php artisan app:import-artists
```

## 特点

- **按姓名去重**：重复执行不会产生重复档案（已存在的会跳过）
- 每条自动创建：1 个用户账号（随机密码）+ 1 条「已通过 + 推荐」档案
- 头像会自动复制进 `storage/app/public/avatars/` 并从 `public/storage` 提供访问

## 注意事项

- 头像要能显示，需先执行过 `php artisan storage:link`（部署时通常已配置）
- 数据修复迁移（`2026_08_14_000001`）应先跑：`php artisan migrate`
- 想改已导入艺术家的信息，直接在网页「个人中心」或后台修改即可，不需要重跑导入
