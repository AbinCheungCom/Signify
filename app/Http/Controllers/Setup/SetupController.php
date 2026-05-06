<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

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
     * 测试数据库连接
     */
    public function testDb(Request $request)
    {
        $host = $request->input('host', '127.0.0.1');
        $port = $request->input('port', 3306);
        $database = $request->input('database');
        $username = $request->input('username', 'root');
        $password = $request->input('password', '');

        try {
            config([
                'database.connections.mysql_test' => [
                    'driver' => 'mysql',
                    'host' => $host,
                    'port' => $port,
                    'database' => $database,
                    'username' => $username,
                    'password' => $password,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ]
            ]);
            DB::connection('mysql_test')->getPdo();
            return response()->json(['success' => true, 'message' => '数据库连接成功']);
        } catch (\Exception $e) {
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
            'database' => 'required',
            'username' => 'required',
            'password' => '',
            'app_name' => 'required',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:6',
        ]);

        try {
            // 配置数据库连接
            $this->writeEnvFile($data);

            // 运行迁移
            Artisan::call('migrate', ['--force' => true]);

            // 创建管理员
            $this->createAdmin($data);

            // 创建存储链接
            Artisan::call('storage:link', ['--force' => true]);

            return response()->json(['success' => true, 'message' => '安装成功']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 写入 .env 文件
     */
    private function writeEnvFile($data)
    {
        $envContent = <<<ENV
APP_NAME="{$data['app_name']}"
APP_ENV=local
APP_KEY=base64:{$this->generateAppKey()}
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$data['host']}
DB_PORT={$data['port']}
DB_DATABASE={$data['database']}
DB_USERNAME={$data['username']}
DB_PASSWORD={$data['password']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
ENV;

        file_put_contents(base_path('.env'), $envContent);
    }

    /**
     * 生成 App Key
     */
    private function generateAppKey()
    {
        return base64_encode(random_bytes(32));
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 获取当前环境变量
     */
    private function getCurrentEnv()
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