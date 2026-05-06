<?php

namespace Tests\Feature;

use App\Models\Entrepreneur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理员可以访问后台
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * 普通用户不能访问后台
     */
    public function test_regular_user_cannot_access_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * 管理员可以审批通过企业家
     */
    public function test_admin_can_approve_entrepreneur(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_PENDING,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/entrepreneurs/{$entrepreneur->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'status' => Entrepreneur::STATUS_APPROVED,
        ]);
    }

    /**
     * 管理员可以拒绝企业家
     */
    public function test_admin_can_reject_entrepreneur(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_PENDING,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/entrepreneurs/{$entrepreneur->id}/reject");

        $response->assertRedirect();
        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'status' => Entrepreneur::STATUS_REJECTED,
        ]);
    }

    /**
     * 管理员可以设置推荐企业家
     */
    public function test_admin_can_toggle_featured(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $entrepreneur = Entrepreneur::factory()->create([
            'is_featured' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/entrepreneurs/{$entrepreneur->id}/toggle-featured");

        $response->assertRedirect();
        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'is_featured' => true,
        ]);
    }

    /**
     * 管理员可以删除企业家档案
     */
    public function test_admin_can_delete_entrepreneur(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $entrepreneur = Entrepreneur::factory()->create();

        $this->actingAs($admin);

        $response = $this->delete("/admin/entrepreneurs/{$entrepreneur->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('entrepreneurs', [
            'id' => $entrepreneur->id,
        ]);
    }

    /**
     * 待审核企业家不会出现在首页
     */
    public function test_pending_entrepreneur_not_on_home(): void
    {
        $pending = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_PENDING,
            'is_featured' => true,
        ]);

        $response = $this->get('/');

        $response->assertDontSee($pending->name);
    }

    /**
     * 已拒绝企业家不会出现在列表页
     */
    public function test_rejected_entrepreneur_not_in_list(): void
    {
        $rejected = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_REJECTED,
        ]);

        $response = $this->get('/entrepreneurs');

        $response->assertDontSee($rejected->name);
    }

    /**
     * 后台显示正确的统计数据
     */
    public function test_dashboard_shows_correct_stats(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Entrepreneur::factory()->count(3)->create(['status' => Entrepreneur::STATUS_APPROVED]);
        Entrepreneur::factory()->count(2)->create(['status' => Entrepreneur::STATUS_PENDING]);
        Entrepreneur::factory()->count(1)->create(['status' => Entrepreneur::STATUS_REJECTED]);

        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('3'); // approved
        $response->assertSee('2'); // pending
        $response->assertSee('1'); // rejected
    }
}
