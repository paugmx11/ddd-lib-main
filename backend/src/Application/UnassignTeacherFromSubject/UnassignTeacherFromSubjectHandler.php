<?php

declare(strict_types=1);

namespace App\Application\UnassignTeacherFromSubject;

use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;

final class UnassignTeacherFromSubjectHandler
{
    public function __construct(
        private TeacherRepository $teacherRepository,
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(UnassignTeacherFromSubjectCommand $command): void
    {
        $teacher = $this->teacherRepository->find(new TeacherId($command->teacherId));
        if ($teacher === null) {
            throw new \RuntimeException('Teacher not found');
        }

        $subject = $this->subjectRepository->find(new SubjectId($command->subjectId));
        if ($subject === null) {
            throw new \RuntimeException('Subject not found');
        }

        if (!$subject->hasTeacher()) {
            throw new \RuntimeException('Subject has no teacher assigned');
        }

        if (!$subject->hasTeacherId($teacher->id()->value())) {
            throw new \RuntimeException('Subject is not assigned to this teacher');
        }

        $subject->removeTeacher($teacher);
        $this->subjectRepository->save($subject);
    }
}
