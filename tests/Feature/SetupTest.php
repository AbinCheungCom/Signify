<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_shows_wizard_when_not_installed(): void
    {
        // 无任何用户 → 未安装 → 显示安装向导
        $response = $this->get('/setup');

        $response->assertStatus(200);
        $response->assertSee('系统安装');
    }

    public function test_setup_shows_installed_when_user_exists(): void
    {
        // 已有用户 → 已安装 → 提示不可重复安装
        User::factory()->create();

        $response = $this->get('/setup');

        $response->assertStatus(200);
        $response->assertSee('系统已安装');
    }

    /**
     * 安装锁文件存在时，安装接口一律 403（即使 users 表为空）
     * 锁文件是权威依据：防止已安装站点在 DB 异常期间被重新接管
     */
    public function test_setup_endpoints_blocked_when_lock_file_exists(): void
    {
        $lock = storage_path('app/installed.lock');
        file_put_contents($lock, 'regression-lock-test');
        try {
            $this->postJson('/setup/test-db', [
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'signify',
                'username' => 'root',
                'password' => '',
            ])->assertStatus(403);

            $this->postJson('/setup/install', [])->assertStatus(403);

            $this->get('/setup')->assertSee('系统已安装');
        } finally {
            @unlink($lock);
        }
    }
}
