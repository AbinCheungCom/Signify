<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
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
            $tables = DB::select("SHOW TABLES");
            $dbConnected = true;
            $installed = count($tables) > 0 && in_array('users', array_map(fn($t) => array_values((array)$t)[0], $tables));
        } catch (\Exception $e) {
            $dbConnected = false;
            $installed = false;
        }

        return inertia('Setup/Index', [
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
        $host = $request->input('host', '127.0.0.1');
        $port = $request->input('port', 3306);
        $database = $request->input('database');
        $username = $request->input('username', 'root');
        $password = $request->input('password', '');

        try {
            // 先连接 MySQL 服务器（不指定数据库）
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 创建数据库（如果不存在）
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 重新连接指定数据库验证
            $pdo->exec("USE `{$database}`");

            return response()->json(['success' => true, 'message' => '数据库连接成功']);
        } catch (PDOException $e) {
            return response()->json(['success' => false, 'message' => '连接失败: ' . $e->getMessage()], 400);
        }
    }

    /**
     * 执行安装
     */
    public function install(Request $request)
    {
        $data = $request->validate([
            'host' => 'required',
            'port' => 'required|numeric',
            'database' => 'required|string|regex:/^[a-zA-Z0-9_]+$/',
            'username' => 'required',
            'password' => '',
            'app_name' => 'required|string|max:50',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            // 1. 备份现有 .env（如有）
            $this->backupEnvFile();

            // 2. 写入 .env 配置
            $this->writeEnvFile($data);

            // 3. 运行迁移
            Artisan::call('migrate', ['--force' => true]);

            // 4. 创建管理员账号
            $this->createAdmin($data);

            // 5. 创建存储链接
            Artisan::call('storage:link', ['--force' => true]);

            // 6. 清除缓存
            Artisan::call('config:clear');

            return response()->json(['success' => true, 'message' => '安装成功']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => '安装失败: ' . $e->getMessage()], 500);
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
        $escapedPassword = addslashes($data['password']);

        $envContent = <<<ENV
APP_NAME="{$data['app_name']}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL=http://localhost

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
        ];
    }
}
