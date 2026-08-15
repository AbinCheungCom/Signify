<?php

namespace Tests\Feature;

use App\Models\Entrepreneur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLinkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 本人可保存社交平台 + 网址
     */
    public function test_user_can_update_social_link(): void
    {
        $user = User::factory()->create();
        $entrepreneur = Entrepreneur::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch('/my/profile', [
            'social_platform' => '小红书',
            'social_url' => 'https://www.xiaohongshu.com/user/profile/abc',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
            'social_platform' => '小红书',
            'social_url' => 'https://www.xiaohongshu.com/user/profile/abc',
        ]);
    }

    /**
     * 网址格式非法被拦截
     */
    public function test_invalid_social_url_rejected(): void
    {
        $user = User::factory()->create();
        $entrepreneur = Entrepreneur::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->from('/my/profile')->patch('/my/profile', [
            'social_url' => 'not-a-url',
        ]);

        $response->assertSessionHasErrors('social_url');
    }

    /**
     * 名片展示社交图标与链接
     */
    public function test_card_shows_social_icon_link(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_platform' => '小红书',
            'social_url' => 'https://www.xiaohongshu.com/user/profile/abc',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/xiaohongshu.svg');
        $response->assertSee('https://www.xiaohongshu.com/user/profile/abc');
    }

    /**
     * 范围外平台使用默认 google 图标
     */
    public function test_unknown_platform_uses_default_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_platform' => '火星社区',
            'social_url' => 'https://example.com',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/google.svg');
    }

    /**
     * 未设置社交链接时不渲染图标
     */
    public function test_card_hides_social_icon_when_no_url(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_platform' => '小红书',
            'social_url' => null,
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertDontSee('icons/xiaohongshu.svg');
    }

    /**
     * 中文平台的英文别名也能匹配对应图标
     */
    public function test_english_alias_matches_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_platform' => 'weibo',
            'social_url' => 'https://weibo.com/u/123',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/weibo.svg');
    }

    /**
     * 非 http(s) 协议不渲染可点击链接（协议白名单）
     */
    public function test_non_http_scheme_not_linked(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_platform' => '小红书',
            'social_url' => 'ftp://example.com',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertDontSee('ftp://example.com');
        $response->assertDontSee('icons/xiaohongshu.svg');
    }
}
