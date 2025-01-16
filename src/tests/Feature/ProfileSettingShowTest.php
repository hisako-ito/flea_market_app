<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ProfileSettingShowTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testProfileSettingShow()
    {
        $user = User::factory()->create([
            'user_name' => '山田太郎',
            'email' => 'taro@example.com',
            'user_image' => 'storage/user_images/dog.jpg',
            'postal_code' => '111-1111',
            'address' => '東京都新宿区',
            'building' => '新宿ビル',
        ]);
        /** @var \App\Models\User $user */

        $this->actingAs($user);

        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);

        $response->assertSee('<img src="' . asset($user->user_image) . '"', false);
        $response->assertSee('山田太郎');
        $response->assertSee('111-1111');
        $response->assertSee('東京都新宿区');
        $response->assertSee('新宿ビル');
    }
}
