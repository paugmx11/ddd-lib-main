<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthStoresBackendTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_stores_backend_token_in_session(): void
    {
        config()->set('backend.base_url', 'http://backend.test');

        Http::fake([
            'http://backend.test/api/auth/login' => Http::response([
                'token' => 'backend-token-123',
                'user' => ['email' => 't@example.com'],
            ], 200),
        ]);

        User::query()->create([
            'name' => 'Tester',
            'email' => 't@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'email' => 't@example.com',
            'password' => 'secret123',
        ])->assertRedirect('/');

        $this->assertSame('backend-token-123', session('backend_token'));
    }
}

