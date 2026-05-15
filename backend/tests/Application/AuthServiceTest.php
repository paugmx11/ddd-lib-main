<?php

declare(strict_types=1);

namespace Tests\Application;

use PHPUnit\Framework\TestCase;
use App\Application\Auth\AuthService;
use App\Domain\User\UserTokenRepository;
use App\Domain\User\UserToken;
use App\Domain\User\UserId;
use App\Domain\User\User;

class AuthServiceTest extends TestCase
{
    public function test_issue_token_and_invalidate(): void
    {
        $repo = new class implements UserTokenRepository {
            public array $tokens = [];

            public function find(string $token): ?UserToken
            {
                foreach ($this->tokens as $t) {
                    if ($t->token() === $token) {
                        return $t;
                    }
                }
                return null;
            }

            public function save(UserToken $userToken): void
            {
                $this->tokens[] = $userToken;
            }

            public function deleteByUser(UserId $userId): void
            {
                $this->tokens = array_filter($this->tokens, function (UserToken $t) use ($userId) {
                    return $t->userId()->value() !== $userId->value();
                });
                // reindex
                $this->tokens = array_values($this->tokens);
            }
        };

        $authService = new AuthService($repo);

        $user = new User(UserId::generate(), 'Tester', 't@example.com', password_hash('pass', PASSWORD_DEFAULT));

        $token = $authService->issueToken($user);
        $this->assertNotEmpty($token);
        $this->assertCount(1, $repo->tokens);

        // Issue again should remove previous and leave only one
        $token2 = $authService->issueToken($user);
        $this->assertNotSame($token, $token2);
        $this->assertCount(1, $repo->tokens);

        // Invalidate tokens
        $authService->invalidateTokensForUser($user->id());
        $this->assertCount(0, $repo->tokens);
    }
}
