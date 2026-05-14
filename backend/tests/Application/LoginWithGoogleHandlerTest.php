<?php

declare(strict_types=1);

namespace Tests\Application;

use PHPUnit\Framework\TestCase;
use App\Application\LoginWithGoogle\LoginWithGoogleHandler;
use App\Application\LoginWithGoogle\LoginWithGoogleCommand;
use App\Infrastructure\Auth\OAuth\GoogleOAuthClientInterface;
use App\Infrastructure\Auth\OAuth\GoogleUser;
use App\Domain\User\UserRepository;
use App\Domain\User\UserId;
use App\Domain\User\User;

class LoginWithGoogleHandlerTest extends TestCase
{
    public function test_creates_user_on_first_call_and_reuses_on_second_call(): void
    {
        // Fake Google client that returns deterministic GoogleUser
        $fakeClient = new class implements GoogleOAuthClientInterface {
            public function fetchUserFromAuthorizationCode(string $code, ?string $redirectUriOverride = null): GoogleUser
            {
                return new GoogleUser('google_sub_' . ($code ?: 'demo'), 'tester+' . ($code ?: 'demo') . '@example.com', 'Fake Tester');
            }
        };

        // In-memory repository implementation
        $repo = new class implements UserRepository {
            private array $byId = [];
            private array $byEmail = [];

            public function find(UserId $id): ?User
            {
                return $this->byId[$id->value()] ?? null;
            }

            public function findByEmail(string $email): ?User
            {
                $key = strtolower($email);
                return $this->byEmail[$key] ?? null;
            }

            public function save(User $user): void
            {
                $this->byId[$user->id()->value()] = $user;
                $this->byEmail[strtolower($user->email())] = $user;
            }
        };

        $handler = new LoginWithGoogleHandler($fakeClient, $repo);

        $user = $handler->handle(new LoginWithGoogleCommand('abc123'));

        $this->assertNotEmpty($user->id()->value());
        $this->assertSame('Fake Tester', $user->name());
        $this->assertSame('tester+abc123@example.com', $user->email());

        // Second call with same code/email returns same id
        $user2 = $handler->handle(new LoginWithGoogleCommand('abc123'));
        $this->assertSame($user->id()->value(), $user2->id()->value());
    }
}
