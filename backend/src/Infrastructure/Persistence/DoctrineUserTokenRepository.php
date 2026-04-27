<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\User\UserId;
use App\Domain\User\UserToken;
use App\Domain\User\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserTokenRepository implements UserTokenRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(string $token): ?UserToken
    {
        return $this->entityManager->find(UserToken::class, $token);
    }

    public function save(UserToken $userToken): void
    {
        $this->entityManager->persist($userToken);
        $this->entityManager->flush();
    }

    public function deleteByUser(UserId $userId): void
    {
        $tokens = $this->entityManager->getRepository(UserToken::class)
            ->findBy(['userId' => $userId->value()]);

        foreach ($tokens as $token) {
            $this->entityManager->remove($token);
        }

        $this->entityManager->flush();
    }
}
