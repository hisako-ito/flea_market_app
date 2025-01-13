<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーションテスト
     *
     * @dataProvider dataUserLogin
     */
    public function testLoginRequestValidation($keys, $values, $expect, $expectedErrors = [])
    {
        // $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $data = array_combine($keys, $values);

        $response = $this->postJson('/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        if ($expect) {
            $response->assertStatus(302);
        } else {
            $response->assertStatus(422);
            $response->assertJsonValidationErrors($expectedErrors);
        }
    }

    public function dataUserLogin()
    {
        return [
            '名前必須エラー' => [
                ['email', 'password'],
                [null, 'password123'],
                false,
                ['email' => 'メールアドレスを入力してください'],
            ],
            'password必須エラー' => [
                ['email', 'password'],
                ['taro@example.com', ''],
                false,
                ['password' => 'パスワードを入力してください'],
            ],
        ];
    }

    public function testSuccessfulUserLogin()
    {
        // $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ], ['X-CSRF-TOKEN' => csrf_token()]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect('/');
    }

    public function testFailedUserLogin()
    {
        // $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        User::factory()->create([
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'hanako@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHas(['errors']);
        $errors = session('errors')->getBag('default')->get('email');
        $this->assertContains('ログイン情報が登録されていません', $errors);
    }

    public function testFailedPasswordLogin()
    {
        // $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        User::factory()->create([
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'taro@example.com',
            'password' => 'password',
        ];

        $response = $this->post('/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHas(['errors']);
        $errors = session('errors')->getBag('default')->get('email');
        $this->assertContains('ログイン情報が登録されていません', $errors);
    }
}
