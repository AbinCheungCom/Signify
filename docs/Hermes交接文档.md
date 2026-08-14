# Signify · Hermes 交接文档

> 交接对象：**Hermes**（下一轮协作方）
> 交接时间：2026-08-14
> 仓库：`https://github.com/AbinCheungCom/Signify`
> 分支：`main`（已与 `origin/main` 同步，HEAD = `ef498bb`）
> 技术栈：Laravel 11 + Blade + Alpine.js + 静态 Tailwind（服务器零构建）

---

## 1. 本会话改动（提交链 `e27dd23 → ef498bb`）

| commit | 内容 | 涉及文件 |
|---|---|---|
| `e27dd23` | **城市选择器**：港澳台置底、手机端全屏展示、定位按钮换 SVG 图标 | `config/cities.php`、`resources/views/profile/edit.blade.php`、`public/js/geolocate.min.js`、`public/icon/dingwei.svg`、`public/css/app.css` |
| `95ea0be` | **简介 `&quot;` 实体双重编码修复**：数据修复迁移 + og-meta 转义（此提交含一个会导致 500 的写法，已被下方 `ecd5986` 撤销） | `database/migrations/2026_08_14_000001_fix_entrepreneur_html_entities.php`、`layouts/app.blade.php` |
| `ecd5986` | 撤销 `95ea0be` 中 `{{ @yield(...) }}` 写法（该写法致全站 500） | `layouts/app.blade.php` |
| `702fe70` | **批量导入艺术家命令** `app:import-artists` + 数据模板 | `app/Console/Commands/ImportArtists.php`、`database/data/artists.json`、`database/data/README-artists.md`、`database/data/avatars/` |
| `ef498bb` | **og-meta 转义改到数据源头**：在 `@section('og-title'/'og-description')` 用 `e()` 转义，layout 保持 `@yield` 不变 | `resources/views/entrepreneurs/show.blade.php` |

**分支状态**：`main` 与远程同步。另有：
- `backup/city-work` @ `212974a`：存有**被回退的 F1-F4 完整提交链**（见 §4.1）
- `refactor/blade-v2` @ `e45990c`：旧的方案B 重构分支（历史）

---

## 2. 系统当前状态

| 维度 | 现状 |
|---|---|
| 城市选择 | profile 页 Alpine 选择器：桌面下拉 + 手机全屏；城市列表港澳台在末尾；定位用 `dingwei.svg` 图标 |
| 简介展示 | 智库列表/详情/后台均 `{{ }}` 输出；历史实体数据待迁移清理 |
| 批量导入 | `php artisan app:import-artists` 就绪，按姓名去重可重复执行 |
| 智库展示条件 | **必须 `status=approved` 且 `is_featured=true`**（列表只显示"已通过+推荐"） |
| 已知回退 | F1-F4 的邮箱小写 / 密码提示 / Markdown 预览 / 前端测试 **均未恢复**（远程回退状态） |

---

## 3. 待办事项（上线前必须做）

按顺序执行：

```bash
# 1. 数据修复迁移：把 bio 等字段里的 &quot; 实体解码回原字符
php artisan migrate

# 2. 头像目录软链（已有则跳过）
php artisan storage:link

# 3. 批量导入艺术家（先填好 database/data/artists.json，name 留空的行自动跳过）
php artisan app:import-artists
```

**艺术家数据**：10 个空位模板已就绪（`database/data/artists.json`），字段见 `README-artists.md`。头像图放 `database/data/avatars/`。

---

## 4. 重要背景与坑（Hermes 必读）

### 4.1 远程曾 force-push 回退 F1-F4 ⚠️
`origin/main` 曾被人为 force-push 重写（`4fda375`），**整个 F1-F4 前端功能被回退**（城市列表、定位、Markdown 预览、邮箱小写等全没了，`config/cities.php`、`geolocate.min.js` 等文件被删）。
本会话已把**城市选择功能**以新提交（`e27dd23`）恢复到当前 base 上。**其余 F1-F4 内容仍在回退状态**，存于 `backup/city-work` 分支。如需恢复需重新评估（当初回退疑似因 CSP 弹窗 bug）。

> **严禁直接 force-push main**。多台机器/会话协作时先 `git pull --rebase` 再推。

### 4.2 `{{ @yield(...) }}` 会导致全站 500 💀
Blade 的 `@yield` 放在 `{{ }}` 里**不会被编译成 PHP**，页面直接 500（`ecd5986` 就是这么回退的）。og/twitter meta 里的用户内容转义，**必须在 `@section` 源头用 `e()`**（见 `ef498bb`），layout 里保持裸 `@yield`。

### 4.3 服务器硬约束
1.8G 内存 · 无 Docker · **缺 fileinfo** · **服务器零构建**（构建产物工作站产出后入库）。
头像上传校验已绕开 finfo（扩展名白名单 + 可选 GD）。

### 4.4 数据修复迁移说明
`2026_08_14_000001_fix_entrepreneur_html_entities.php`：解码 `name/title/industry/city/bio/contact_phone/contact_email` 里的 `&quot; &#34; &lt; &gt; &amp; &#39;`，**反复解码至稳定**（兼容二次编码），仅更新有变化的行。**在本地和产线都要跑**。

---

## 5. 安全隐患 ⚠️⚠️（最高优先级）

- GitHub PAT（`ghp_S8…` 开头）曾在会话记录中**明文泄露**（两份 `.jsonl` 文件），且进入过模型上下文。
- **必须尽快到 GitHub Settings → Developer settings → Personal access tokens 吊销/轮换该 token。**
- 任何情况下：**不要把 token 写进 skill 文件、代码、配置文件**。推送走 `gh auth login`（Windows 凭据管理器）或 git credential manager。
- 交接后若发现仍有残留明文 token 的会话文件，先擦除再分享。

---

## 6. 给 Hermes 的下一步

1. `git pull` 对齐 `main`（`ef498bb`）
2. 跑 `php artisan migrate`，确认实体修复迁移生效（简介里 `&quot;` 应消失）
3. 等艺术家名单，填入 `database/data/artists.json`，跑 `app:import-artists`
4. 评估是否恢复 F1-F4 其余功能（从 `backup/city-work` 提取，注意 CSP/弹窗问题）
5. 处理 §5 的 token 轮换

---

*本交接文档随下一轮协作结束更新。*
