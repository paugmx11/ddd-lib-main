<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineStudentRepository implements StudentRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(StudentId $id): ?Student
    {
        return $this->entityManager->find(Student::class, $id->value());
    }

    public function findByEmail(string $email): ?Student
    {
        return $this->entityManager->getRepository(Student::class)
            ->findOneBy(['email' => $email]);
    }

    public function save(Student $student): void
    {
        $this->entityManager->persist($student);
        $this->entityManager->flush();
    }

    public function delete(Student $student): void
    {
        $this->entityManager->remove($student);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Student::class)
            ->findAll();
    }
}
