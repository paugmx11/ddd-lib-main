<?php

declare(strict_types=1);

namespace App\Domain\User;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_tokens')]
class UserToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 80)]
    private string $token;

    #[ORM\Column(type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $token, UserId $userId)
    {
        if ($token === '') {
            throw new \InvalidArgumentException('Token cannot be empty');
        }

        $this->token = $token;
        $this->userId = $userId->value();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function token(): string
    {
        return $this->token;
    }

    public function userId(): UserId
    {
        return new UserId($this->userId);
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
