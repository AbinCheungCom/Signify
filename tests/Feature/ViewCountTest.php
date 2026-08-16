<?php

namespace Tests\Feature;

use App\Models\Entrepreneur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 访问详情页计入 1 次浏览量，并写入访客记录
     */
    public function test_visiting_detail_page_increments_view_count(): void
    {
        $entrepreneur = Entrepreneur::factory()->create();

        $this->get('/entrepreneurs/'.$entrepreneur->id)->assertStatus(200);

        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'view_count' => 1,
        ]);
        $this->assertDatabaseCount('entrepreneur_views', 1);
    }

    /**
     * 同一会话（24h 内）刷新不重复计数
     */
    public function test_same_session_does_not_double_count(): void
    {
        $entrepreneur = Entrepreneur::factory()->create();

        $this->get('/entrepreneurs/'.$entrepreneur->id);
        $this->get('/entrepreneurs/'.$entrepreneur->id);

        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'view_count' => 1,
        ]);
        $this->assertDatabaseCount('entrepreneur_views', 1);
    }

    /**
     * 新会话（模拟不同访客）再次计入
     */
    public function test_new_session_counts_again(): void
    {
        $entrepreneur = Entrepreneur::factory()->create();

        $this->get('/entrepreneurs/'.$entrepreneur->id);
        $this->flushSession();
        $this->get('/entrepreneurs/'.$entrepreneur->id);

        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'view_count' => 2,
        ]);
        $this->assertDatabaseCount('entrepreneur_views', 2);
    }

    /**
     * 档案本人浏览自己的页面不计入访客数
     */
    public function test_owner_viewing_own_profile_not_counted(): void
    {
        $user = User::factory()->create();
        $entrepreneur = Entrepreneur::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get('/entrepreneurs/'.$entrepreneur->id);

        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'view_count' => 0,
        ]);
        $this->assertDatabaseCount('entrepreneur_views', 0);
    }

    /**
     * 详情页展示浏览量（超过阈值 10 时显示，含本次访问计入）
     */
    public function test_detail_page_shows_view_count(): void
    {
        $entrepreneur = Entrepreneur::factory()->create(['view_count' => 11]);

        $this->get('/entrepreneurs/'.$entrepreneur->id)->assertSee('12 人浏览过');
    }

    /**
     * 浏览量未超过阈值 10 时不显示
     */
    public function test_view_count_hidden_when_low(): void
    {
        $entrepreneur = Entrepreneur::factory()->create(['view_count' => 3]);

        $this->get('/entrepreneurs/'.$entrepreneur->id)->assertDontSee('人浏览过');
    }

    /**
     * 后台企业家列表展示浏览量
     */
    public function test_admin_list_shows_view_count(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Entrepreneur::factory()->create(['view_count' => 5]);

        $this->actingAs($admin)
            ->get('/admin/entrepreneurs')
            ->assertStatus(200)
            ->assertSee('5');
    }
}
