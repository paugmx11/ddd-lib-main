<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ClientApiRequiresBackendTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_courses_requires_backend_token(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/client-api/courses')
            ->assertStatus(401)
            ->assertJson([
                'error' => 'Missing backend token. Logout and login again.',
            ]);
    }
}

