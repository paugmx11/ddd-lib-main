<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Enrollment\Enrollment;
use App\Domain\Enrollment\EnrollmentId;
use App\Domain\Enrollment\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineEnrollmentRepository implements EnrollmentRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(EnrollmentId $id): ?Enrollment
    {
        return $this->entityManager->find(Enrollment::class, $id->value());
    }

    public function findByStudentAndCourse(string $studentId, string $courseId): ?Enrollment
    {
        return $this->entityManager->getRepository(Enrollment::class)
            ->createQueryBuilder('e')
            ->where('e.student = :studentId')
            ->andWhere('e.course = :courseId')
            ->andWhere('e.status = :status')
            ->setParameter('studentId', $studentId)
            ->setParameter('courseId', $courseId)
            ->setParameter('status', 'active')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByStudent(string $studentId): array
    {
        return $this->entityManager->getRepository(Enrollment::class)
            ->createQueryBuilder('e')
            ->where('e.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->getQuery()
            ->getResult();
    }

    public function findByCourse(string $courseId): array
    {
        return $this->entityManager->getRepository(Enrollment::class)
            ->createQueryBuilder('e')
            ->where('e.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getResult();
    }

    public function save(Enrollment $enrollment): void
    {
        $this->entityManager->persist($enrollment);
        $this->entityManager->flush();
    }

    public function delete(Enrollment $enrollment): void
    {
        $this->entityManager->remove($enrollment);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Enrollment::class)
            ->findAll();
    }
}
