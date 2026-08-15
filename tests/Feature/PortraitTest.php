<?php

namespace Tests\Feature;

use App\Models\Entrepreneur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PortraitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 本人可上传形象照（portrait）
     */
    public function test_user_can_upload_portrait(): void
    {
        $user = User::factory()->create();
        $entrepreneur = Entrepreneur::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch('/my/profile', [
            'portrait' => UploadedFile::fake()->image('portrait.jpg'),
        ]);

        $response->assertRedirect();
        $this->assertNotNull(Entrepreneur::find($entrepreneur->id)->portrait);
    }

    /**
     * 名片形象照优先使用 portrait，而非 avatar
     */
    public function test_card_prefers_portrait_over_avatar(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'avatar' => 'avatars/avatar.jpg',
            'portrait' => 'portraits/portrait.jpg',
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('storage/portraits/portrait.jpg');
        $response->assertDontSee('storage/avatars/avatar.jpg');
    }

    /**
     * 未设置形象照时回退到头像
     */
    public function test_card_falls_back_to_avatar_when_no_portrait(): void
    {
        $entrepreneur = Entrepreneur::factory()->create([
            'status' => Entrepreneur::STATUS_APPROVED,
            'avatar' => 'avatars/avatar.jpg',
            'portrait' => null,
        ]);

        $response = $this->get('/entrepreneurs/'.$entrepreneur->id);

        $response->assertStatus(200);
        $response->assertSee('storage/avatars/avatar.jpg');
    }
}
