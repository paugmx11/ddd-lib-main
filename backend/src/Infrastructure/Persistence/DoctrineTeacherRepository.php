<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTeacherRepository implements TeacherRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(TeacherId $id): ?Teacher
    {
        return $this->entityManager->find(Teacher::class, $id->value());
    }

    public function findByEmail(string $email): ?Teacher
    {
        return $this->entityManager->getRepository(Teacher::class)
            ->findOneBy(['email' => $email]);
    }

    public function save(Teacher $teacher): void
    {
        $this->entityManager->persist($teacher);
        $this->entityManager->flush();
    }

    public function delete(Teacher $teacher): void
    {
        $this->entityManager->remove($teacher);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Teacher::class)
            ->findAll();
    }
}
