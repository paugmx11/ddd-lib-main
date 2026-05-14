<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Application\LoginWithGoogle\LoginWithGoogleHandler;
use App\Application\LoginWithGoogle\LoginWithGoogleCommand;
use App\Infrastructure\Auth\OAuth\GoogleOAuthClientInterface;
use App\Infrastructure\Auth\OAuth\GoogleUser;
use App\Domain\User\UserRepository;
use App\Domain\User\UserId;
use App\Domain\User\User;

// Fake Google client that returns a deterministic GoogleUser
class FakeGoogleClient implements GoogleOAuthClientInterface
{
    public function fetchUserFromAuthorizationCode(string $code, ?string $redirectUriOverride = null): GoogleUser
    {
        // ignore the code, return a fake user
        return new GoogleUser('google_sub_' . ($code ?: 'demo'), 'tester+' . ($code ?: 'demo') . '@example.com', 'Fake Tester');
    }
}

// In-memory UserRepository for testing
class InMemoryUserRepository implements UserRepository
{
    private array $byId = [];
    private array $byEmail = [];

    public function find(App\Domain\User\UserId $id): ?User
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
}

// Run the test
$fakeClient = new FakeGoogleClient();
$repo = new InMemoryUserRepository();
$handler = new LoginWithGoogleHandler($fakeClient, $repo);

// Simulate calling with code 'abc123'
$command = new LoginWithGoogleCommand('abc123');
$user = $handler->handle($command);

echo "User created/found:\n";
echo "id: " . $user->id()->value() . "\n";
echo "name: " . $user->name() . "\n";
echo "email: " . $user->email() . "\n";

// Call again with same email to ensure it finds existing
$user2 = $handler->handle(new LoginWithGoogleCommand('abc123'));
echo "\nSecond call returns same id: " . $user2->id()->value() . "\n";
