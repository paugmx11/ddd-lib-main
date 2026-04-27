<?php

declare(strict_types=1);

namespace App\Application\CreateTeacher;

use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;

final class CreateTeacherHandler
{
    public function __construct(
        private TeacherRepository $teacherRepository
    ) {}

    public function handle(CreateTeacherCommand $command): void
    {
        // Verificar que no existeix un teacher amb el mateix email
        $existingTeacher = $this->teacherRepository->findByEmail($command->email);
        if ($existingTeacher !== null) {
            throw new \RuntimeException('A teacher with this email already exists');
        }

        $teacher = new Teacher(
            new TeacherId($command->teacherId),
            $command->name,
            $command->email
        );

        $this->teacherRepository->save($teacher);
    }
}
