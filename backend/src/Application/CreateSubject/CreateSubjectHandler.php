<?php

declare(strict_types=1);

namespace App\Application\CreateSubject;

use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;

final class CreateSubjectHandler
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private CourseRepository $courseRepository
    ) {}

    public function handle(CreateSubjectCommand $command): void
    {
        // Verificar que el curs existeix
        $course = $this->courseRepository->find(new CourseId($command->courseId));
        if ($course === null) {
            throw new \RuntimeException('Course not found');
        }

        // Verificar que no existeix una assignatura amb el mateix nom al mateix curs
        $existingSubjects = $this->subjectRepository->findByCourse($command->courseId);
        foreach ($existingSubjects as $existingSubject) {
            if ($existingSubject->name() === $command->name) {
                throw new \RuntimeException('A subject with this name already exists in this course');
            }
        }

        $subject = new Subject(
            new SubjectId($command->subjectId),
            $command->name,
            $course
        );

        $this->subjectRepository->save($subject);
    }
}
