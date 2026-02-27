<?php

declare(strict_types=1);

namespace App\Application\AssignTeacherToSubject;

use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;

final class AssignTeacherToSubjectHandler
{
    public function __construct(
        private TeacherRepository $teacherRepository,
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(AssignTeacherToSubjectCommand $command): void
    {
        // 1. Buscar el teacher
        $teacher = $this->teacherRepository->find(new TeacherId($command->teacherId));
        if ($teacher === null) {
            throw new \RuntimeException('Teacher not found');
        }

        // 2. Buscar l'assignatura
        $subject = $this->subjectRepository->find(new SubjectId($command->subjectId));
        if ($subject === null) {
            throw new \RuntimeException('Subject not found');
        }

        // 3. Verificar que l'assignatura no té ja un teacher assignat (regla de negoci)
        if ($subject->hasTeacher()) {
            throw new \RuntimeException('Subject already has a teacher assigned');
        }

        // 4. Assignar el teacher a l'assignatura (regla de negoci al domini)
        $subject->assignTeacher($teacher);

        // 5. Afegir l'assignatura al teacher
        $teacher->addSubject($subject);

        // 6. Persistir
        $this->subjectRepository->save($subject);
        $this->teacherRepository->save($teacher);
    }
}
