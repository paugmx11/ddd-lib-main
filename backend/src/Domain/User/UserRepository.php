<?php

namespace App\Domain\User;

interface UserRepository
{
    public function find(UserId $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;
}
