<?php

namespace App\Console\Commands;

use App\Models\Entrepreneur;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportArtists extends Command
{
    /**
     * 批量导入企业家/艺术家。
     *
     * 数据源：database/data/artists.json（字段见同目录 README-artists.md）。
     * 每条自动创建：1 个用户账号 + 1 条「已通过 + 推荐」档案，头像从
     * database/data/avatars/ 复制进 storage。按姓名去重，可重复执行。
     */
    protected $signature = 'app:import-artists {--file=database/data/artists.json}';

    protected $description = '批量导入艺术家：读 artists.json，创建用户 + 已通过推荐档案';

    /** 头像扩展名白名单（与 AvatarService 一致） */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function handle(): int
    {
        $path = $this->option('file');
        if (! is_file($path)) {
            $path = base_path($path);
        }
        if (! is_file($path)) {
            $this->error("数据文件不存在：{$path}");
            return self::FAILURE;
        }

        $entries = json_decode(file_get_contents($path), true);
        if (! is_array($entries)) {
            $this->error('数据文件不是有效的 JSON 数组');
            return self::FAILURE;
        }

        $created = 0;
        $skipped = 0;
        $empty = 0;

        foreach ($entries as $i => $entry) {
            $name = trim((string) ($entry['name'] ?? ''));
            if ($name === '') {
                $empty++;
                continue;
            }

            // 幂等：按姓名去重，避免重复执行产生重复档案
            if (Entrepreneur::where('name', $name)->exists()) {
                $this->warn("[{$i}] {$name} 已存在，跳过");
                $skipped++;
                continue;
            }

            // 邮箱：未提供或非法时自动生成（艺术家不登录则无需告知）
            $email = trim((string) ($entry['email'] ?? ''));
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = "artist-{$i}-" . Str::random(4) . '@signify.local';
            }
            // 邮箱撞库时兜底生成
            if (User::where('email', $email)->exists()) {
                $email = "artist-{$i}-" . Str::random(4) . '@signify.local';
            }

            $password = Str::password(16);

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password, // hashed cast
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();

            $entrepreneur = Entrepreneur::create([
                'user_id' => $user->id,
                'name' => $name,
                'title' => trim((string) ($entry['title'] ?? '')),
                'industry' => trim((string) ($entry['industry'] ?? '')),
                'city' => trim((string) ($entry['city'] ?? '')),
                'bio' => trim((string) ($entry['bio'] ?? '')) ?: null,
                'status' => Entrepreneur::STATUS_APPROVED,
                'is_featured' => (bool) ($entry['featured'] ?? true),
            ]);

            $avatar = trim((string) ($entry['avatar'] ?? ''));
            if ($avatar !== '') {
                $avatarPath = database_path('data/avatars/'.$avatar);
                $ext = strtolower(pathinfo($avatar, PATHINFO_EXTENSION));
                if (in_array($ext, self::ALLOWED_EXTENSIONS) && is_file($avatarPath)) {
                    $stored = 'avatars/'.time().'_'.uniqid().'.'.$ext;
                    Storage::disk('public')->put($stored, file_get_contents($avatarPath));
                    $entrepreneur->update(['avatar' => $stored]);
                } else {
                    $this->warn("[{$i}] {$name} 头像文件无效或扩展名不支持：{$avatar}（已跳过头像）");
                }
            }

            $this->info("[{$i}] {$name} 已创建（email: {$email}）");
            $created++;
        }

        $this->info("完成：创建 {$created} 条，跳过重复 {$skipped} 条，忽略空行 {$empty} 条。");
        return self::SUCCESS;
    }
}
