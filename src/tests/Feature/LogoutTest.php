<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testLogout()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout', [], ['X-CSRF-TOKEN' => csrf_token()]);

        $response->assertStatus(302);
        $this->assertGuest();
    }
}
