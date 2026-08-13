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
}
