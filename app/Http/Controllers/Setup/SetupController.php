<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use PDO;
use PDOException;

class SetupController extends Controller
{
    /**
     * 检查是否已安装
     */
    public function check()
    {
        try {
            DB::connection()->getPdo();
            $installed = $this->isInstalled();
            $dbConnected = true;
        } catch (\Exception $e) {
            $dbConnected = false;
            $installed = false;
        }

        return view('setup.index', [
            'dbConnected' => $dbConnected,
            'installed' => $installed,
            'env' => $this->getCurrentEnv(),
        ]);
    }

    /**
     * 测试数据库连接 + 自动建库
     */
    public function testDb(Request $request)
    {
        if ($this->isInstalled()) {
            return response()->json(['success' => false, 'message' => '系统已安装，请勿重复操作'], 403);
        }

        $data = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|numeric|min:1|max:65535',
            'database' => 'required|string|regex:/^[a-zA-Z0-9_]{1,64}$/',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $host = $data['host'];
        $port = (int) $data['port'];
        $database = $data['database'];
        $username = $data['username'];
        $password = $data['password'];

        try {
            // 先连接 MySQL 服务器（不指定数据库）
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // P1修复：强制字符集 + 反引号包裹数据库名，防止特殊字符问题
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 重新连接指定数据库验证
            $pdo->exec("USE `{$database}`");

            return response()->json(['success' => true, 'message' => '数据库连接成功']);
        } catch (PDOException $e) {
            // 不回显 PDO 原始错误（防数据库指纹/路径泄露），详情落日志
            \Log::warning('Signify 安装向导数据库连接失败：'.$e->getMessage());
            return response()->json(['success' => false, 'message' => '连接失败：请检查主机地址、端口、账号、密码是否正确（详情见 storage/logs/laravel.log）'], 400);
        }
    }

    /**
     * 执行安装
     */
    public function install(Request $request)
    {
        if ($this->isInstalled()) {
            return response()->json(['success' => false, 'message' => '系统已安装，请勿重复操作'], 403);
        }

        $data = $request->validate([
            'host' => 'required',
            'port' => 'required|numeric',
            'database' => 'required|string|regex:/^[a-zA-Z0-9_]+$/',
            'username' => 'required',
            'password' => 'nullable|string',
            'app_name' => 'required|string|max:50',
            'app_url' => 'required|url',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:6|confirmed',
        ]);

        try {
            // 1. 备份现有 .env（如有）
            $this->backupEnvFile();

            // 2. 写入 .env 配置
            $this->writeEnvFile($data);

            // 2.1 显式刷新当前进程的 DB 配置。
            //     Laravel 在请求启动时加载 .env，写入文件不会自动生效，
            //     不刷新会导致 migrate/createAdmin 仍连旧库（BUG 修复）。
            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $data['host'],
                'database.connections.mysql.port' => (int) $data['port'],
                'database.connections.mysql.database' => $data['database'],
                'database.connections.mysql.username' => $data['username'],
                'database.connections.mysql.password' => $data['password'],
            ]);

            // 3. 运行迁移
            Artisan::call('migrate', ['--force' => true]);

            // 4. 创建管理员账号
            $this->createAdmin($data);

            // 4.1 写入初始系统设置（站点名称）
            Setting::updateOrCreate(['key' => 'site_name'], ['value' => $data['app_name']]);
            Setting::flush();

            // 5. 创建存储链接
            try {
                Artisan::call('storage:link', ['--force' => true]);
            } catch (\Exception $e) {
                // storage:link 失败不影响主流程，但记录提示用户
                \Log::warning('storage:link failed: ' . $e->getMessage());
            }

            // 6. 清除缓存
            Artisan::call('config:clear');

            // 7. 写入安装锁：此后安装接口永久拒绝（含数据库故障期间）
            $this->writeInstallLock();

            return response()->json(['success' => true, 'message' => '安装成功']);
        } catch (\Exception $e) {
            // 不回显底层异常详情（防信息泄露），完整错误落日志
            \Log::error('Signify 安装失败：'.$e->getMessage());
            return response()->json(['success' => false, 'message' => '安装失败，请查看 storage/logs/laravel.log 排查后再试'], 500);
        }
    }

    /**
     * 备份现有 .env 文件
     */
    private function backupEnvFile()
    {
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $backupFile = base_path('.env.backup.' . date('Ymd_His'));
            copy($envFile, $backupFile);
        }
    }

    /**
     * 写入 .env 文件
     */
    private function writeEnvFile($data)
    {
        $appKey = $this->generateAppKey();
        $escapedPassword = $this->escapeEnvValue($data['password']);

        // P1修复：使用用户填写的 APP_URL，不再硬编码 http://localhost
        $appUrl = rtrim($data['app_url'], '/');

        $envContent = <<<ENV
APP_NAME="{$data['app_name']}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$appUrl}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST={$data['host']}
DB_PORT={$data['port']}
DB_DATABASE={$data['database']}
DB_USERNAME={$data['username']}
DB_PASSWORD="{$escapedPassword}"

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
ENV;

        file_put_contents(base_path('.env'), $envContent);
    }

    /**
     * 生成 App Key
     */
    private function generateAppKey(): string
    {
        $key = bin2hex(random_bytes(32));
        return 'base64:' . base64_encode(hex2bin($key));
    }

    /**
     * 转义 .env 双引号值（兼容 phpdotenv 解析规则）：
     * \ → \\，$ → \$（防变量插值），" → \"
     */
    private function escapeEnvValue(string $value): string
    {
        return str_replace(['\\', '$', '"'], ['\\\\', '\$', '\"'], $value);
    }

    /**
     * 创建管理员账号
     */
    private function createAdmin($data)
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
            'is_admin' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 检查系统是否已安装
     *
     * 锁文件（storage/app/installed.lock）是权威依据：一旦存在，
     * 无论数据库是否可用都视为已安装，防止已部署站点在 DB 瞬断期间
     * 被 /setup/install 重新开放导致 .env 被改写（指向外部数据库接管）。
     */
    private function isInstalled(): bool
    {
        if ($this->hasInstallLock()) {
            return true;
        }

        try {
            $installed = Schema::hasTable('users') && DB::table('users')->exists();
        } catch (\Exception $e) {
            // DB 异常且无锁文件（多为尚未安装的新部署）：仅此时允许安装
            return false;
        }

        // 兼容旧版本安装（无锁文件）：确认已安装后自动补写锁文件
        // testing 环境跳过，避免测试副作用写真实 storage
        if ($installed && ! app()->environment('testing')) {
            $this->writeInstallLock();
        }

        return $installed;
    }

    /**
     * 安装锁文件路径
     */
    private function installedLockPath(): string
    {
        return storage_path('app/installed.lock');
    }

    private function hasInstallLock(): bool
    {
        return is_file($this->installedLockPath());
    }

    /**
     * 写入安装锁（幂等）：安装成功后调用，此后安装接口永久拒绝
     */
    private function writeInstallLock(): void
    {
        if (! $this->hasInstallLock()) {
            @file_put_contents($this->installedLockPath(), bin2hex(random_bytes(16)).PHP_EOL);
        }
    }

    /**
     * 获取当前环境变量
     */
    private function getCurrentEnv(): array
    {
        return [
            'APP_NAME' => env('APP_NAME', 'Signify'),
            'DB_HOST' => env('DB_HOST', '127.0.0.1'),
            'DB_PORT' => env('DB_PORT', 3306),
            'DB_DATABASE' => env('DB_DATABASE', ''),
            'DB_USERNAME' => env('DB_USERNAME', 'root'),
            'APP_URL' => env('APP_URL', ''),
        ];
    }
}