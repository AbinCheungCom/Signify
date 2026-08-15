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
     * 本人可保存社交主页网址
     */
    public function test_user_can_update_social_link(): void
    {
        $user = User::factory()->create();
        $entrepreneur = Entrepreneur::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch('/my/profile', [
            'social_url' => 'https://www.xiaohongshu.com/user/profile/abc',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('entrepreneurs', [
            'id' => $entrepreneur->id,
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
     * 名片按域名识别展示社交图标与链接
     */
    public function test_card_shows_social_icon_link(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://www.xiaohongshu.com/user/profile/abc',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/xiaohongshu.svg');
        $response->assertSee('https://www.xiaohongshu.com/user/profile/abc');
    }

    /**
     * 域名识别：weibo.com 匹配微博图标
     */
    public function test_domain_maps_to_weibo_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://weibo.com/u/123',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/weibo.svg');
    }

    /**
     * 域名识别：微博手机域名 weibo.cn 也匹配微博图标
     */
    public function test_domain_maps_weibo_cn_to_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://m.weibo.cn/u/123',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/weibo.svg');
    }

    /**
     * 未知域名使用默认 google 图标
     */
    public function test_unknown_domain_uses_default_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://example.com',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/google.svg');
    }

    /**
     * 非 http(s) 协议不渲染可点击链接（协议白名单）
     */
    public function test_non_http_scheme_not_linked(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'ftp://example.com',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertDontSee('ftp://example.com');
        $response->assertDontSee('icons/google.svg');
    }

    /**
     * 品牌域名使用 logo 图标
     */
    public function test_brand_domain_uses_logo_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://www.abincheung.com/about',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/logo.svg');
    }

    /**
     * 品牌子域名（xxx.abincheung.com 等）同样使用 logo 图标
     */
    public function test_brand_subdomain_uses_logo_icon(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://xxx.61ml.com/about',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/logo.svg');
    }

    /**
     * 仿冒域名（结尾非主域）不命中品牌 logo，回退默认图标
     */
    public function test_lookalike_domain_not_matched(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => 'https://www.61lm.com.cn/about',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('icons/google.svg');
        $response->assertDontSee('icons/logo.svg');
    }

    /**
     * 未设置社交链接时不渲染图标
     */
    public function test_card_hides_social_icon_when_no_url(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'social_url' => null,
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertDontSee('icons/xiaohongshu.svg');
    }
}
