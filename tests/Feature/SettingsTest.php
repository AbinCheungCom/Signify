<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 非管理员不能访问系统设置
     */
    public function test_regular_user_cannot_access_settings(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        $response = $this->get('/admin/settings');

        // AdminMiddleware 对已登录非管理员（非 JSON）重定向首页并提示
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    /**
     * 管理员可保存设置，Setting::get 返回新值
     */
    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);

        $response = $this->post('/admin/settings', [
            'site_name'         => '新站点',
            'site_description'  => '新的描述',
            'share_title'       => '新的分享标题',
            'share_description' => '新的分享描述',
            'share_image'       => 'https://example.com/share.png',
            'footer_copyright'  => '© 2026 新站点',
            'icp_number'        => '京ICP备12345678号',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('新站点', Setting::get('site_name'));
        $this->assertEquals('新的分享标题', Setting::get('share_title'));
        $this->assertEquals('https://example.com/share.png', Setting::get('share_image'));
        $this->assertEquals('京ICP备12345678号', Setting::get('icp_number'));
    }

    /**
     * 管理员可上传分享卡片图片文件
     */
    public function test_admin_can_upload_share_image(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);

        $response = $this->post('/admin/settings', [
            'site_name'        => '新站点',
            'share_image_file' => UploadedFile::fake()->image('share.jpg'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertNotNull(Setting::get('share_image'));
        $this->assertStringContainsString('/storage/settings/', Setting::get('share_image'));
    }

    /**
     * 设置未配置时返回默认值
     */
    public function test_settings_defaults(): void
    {
        $this->assertEquals('SIGNIFY', Setting::get('site_name', 'SIGNIFY'));
        $this->assertEquals('fallback', Setting::get('not_exist_key', 'fallback'));
        $this->assertNull(Setting::get('not_exist_key'));
    }

    /**
     * 前台布局读取设置（title / og / footer / 备案号）
     */
    public function test_layout_uses_settings(): void
    {
        Setting::updateOrCreate(['key' => 'site_name'], ['value' => '定制站点名']);
        Setting::updateOrCreate(['key' => 'site_description'], ['value' => '定制站点描述']);
        Setting::updateOrCreate(['key' => 'footer_copyright'], ['value' => '© 定制版权']);
        Setting::updateOrCreate(['key' => 'icp_number'], ['value' => '京ICP备12345678号']);
        Setting::flush();

        // 企业家库为公开页，使用 app 布局
        $response = $this->get('/entrepreneurs');

        $response->assertStatus(200);
        $response->assertSee('定制站点名', false);      // og:site_name
        $response->assertSee('定制站点描述', false);    // meta description
        $response->assertSee('© 定制版权', false);      // footer
        $response->assertSee('京ICP备12345678号', false); // 备案号
        $response->assertSee('beian.miit.gov.cn', false); // 备案链接
    }

    /**
     * 清空备案号后 footer 不再展示备案链接
     */
    public function test_clearing_icp_removes_beian_link(): void
    {
        Setting::updateOrCreate(['key' => 'icp_number'], ['value' => null]);
        Setting::flush();

        $response = $this->get('/entrepreneurs');

        $response->assertStatus(200);
        $response->assertDontSee('beian.miit.gov.cn');
    }
}
