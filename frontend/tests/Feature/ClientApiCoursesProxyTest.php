<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class ClientApiCoursesProxyTest extends TestCase
{
    use RefreshDatabase;

    public function test_courses_proxies_backend_response_when_token_present(): void
    {
        config()->set('backend.base_url', 'http://backend.test');

        Http::fake([
            'http://backend.test/*' => Http::response([
                ['id' => 'c1', 'name' => 'Course 1'],
            ], 200, ['Content-Type' => 'application/json']),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['backend_token' => 'abc-token'])
            ->getJson('/client-api/courses')
            ->assertStatus(200)
            ->assertJson([
                ['id' => 'c1', 'name' => 'Course 1'],
            ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://backend.test/api/courses'
                && $request->hasHeader('Authorization', 'Bearer abc-token');
        });
    }
}

