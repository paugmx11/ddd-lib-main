<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCourseRepository implements CourseRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(CourseId $id): ?Course
    {
        return $this->entityManager->find(Course::class, $id->value());
    }

    public function findByName(string $name): ?Course
    {
        return $this->entityManager->getRepository(Course::class)
            ->findOneBy(['name' => $name]);
    }

    public function save(Course $course): void
    {
        $this->entityManager->persist($course);
        $this->entityManager->flush();
    }

    public function delete(Course $course): void
    {
        $this->entityManager->remove($course);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Course::class)
            ->findAll();
    }

    public function findActive(): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->entityManager->getRepository(Course::class)
            ->createQueryBuilder('c')
            ->where('c.startDate <= :today')
            ->andWhere('c.endDate >= :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult();
    }
}
