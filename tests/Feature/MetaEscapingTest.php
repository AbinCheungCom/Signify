<?php

namespace Tests\Feature;

use App\Models\Entrepreneur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetaEscapingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 回归测试：详情页 og-meta 与 <title> 必须转义用户可控内容（防存储型 XSS）。
     *
     * 背景：ef498bb 曾为 og-title/og-description 加上 e()，但 faeb18a 改造时丢失，
     * 且 <title> 从未转义——@yield 输出不转义，用户名/简介可携带
     * `"><svg onload=...>` 或 `</title><script>` 直接注入页面。
     */
    public function test_detail_page_meta_escapes_user_content(): void
    {
        $attrPayload = '"><svg onload=alert(1)>';
        $titlePayload = '</title><script>alert(1)</script>';

        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'name' => $titlePayload,
            'bio' => $attrPayload,
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);

        // 未转义原文不得出现在响应中（escape=false 表示按原文查找）
        $response->assertDontSee($titlePayload, false);
        $response->assertDontSee($attrPayload, false);

        // 出现的必须是 HTML 转义后的形式（assertSee 默认对断言值做 e() 转义）
        $response->assertSee($titlePayload);
        $response->assertSee($attrPayload);
    }

    /**
     * 正常内容不受转义影响（防止过度转义导致 &quot; 之类实体直接可见）
     */
    public function test_detail_page_meta_renders_normal_content(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'name' => '张三',
            'bio' => '科技行业连续创业者',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('张三');
        $response->assertSee('科技行业连续创业者');
    }
}
