<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineSubjectRepository implements SubjectRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(SubjectId $id): ?Subject
    {
        return $this->entityManager->find(Subject::class, $id->value());
    }

    public function findByName(string $name): ?Subject
    {
        return $this->entityManager->getRepository(Subject::class)
            ->findOneBy(['name' => $name]);
    }

    public function findByNameAndCourse(string $name, string $courseId): ?Subject
    {
        return $this->entityManager->getRepository(Subject::class)
            ->createQueryBuilder('s')
            ->where('s.name = :name')
            ->andWhere('s.course = :courseId')
            ->setParameter('name', $name)
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByCourse(string $courseId): array
    {
        return $this->entityManager->getRepository(Subject::class)
            ->createQueryBuilder('s')
            ->where('s.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getResult();
    }

    public function findByTeacher(string $teacherId): array
    {
        return $this->entityManager->getRepository(Subject::class)
            ->createQueryBuilder('s')
            ->where('s.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getResult();
    }

    public function save(Subject $subject): void
    {
        $this->entityManager->persist($subject);
        $this->entityManager->flush();
    }

    public function delete(Subject $subject): void
    {
        $this->entityManager->remove($subject);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Subject::class)
            ->findAll();
    }
}
