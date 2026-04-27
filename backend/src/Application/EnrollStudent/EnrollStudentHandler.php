<?php

declare(strict_types=1);

namespace App\Application\EnrollStudent;

use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;
use App\Domain\Enrollment\Enrollment;
use App\Domain\Enrollment\EnrollmentRepository;

final class EnrollStudentHandler
{
    public function __construct(
        private StudentRepository $studentRepository,
        private CourseRepository $courseRepository,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function handle(EnrollStudentCommand $command): void
    {
        // 1. Buscar l'estudiant
        $student = $this->studentRepository->find(new StudentId($command->studentId));
        if ($student === null) {
            throw new \RuntimeException('Student not found');
        }

        // 2. Buscar el curs
        $course = $this->courseRepository->find(new CourseId($command->courseId));
        if ($course === null) {
            throw new \RuntimeException('Course not found');
        }

        // 3. Verificar que no està ja matriculat (regla de negoci)
        $existingEnrollment = $this->enrollmentRepository->findByStudentAndCourse(
            $command->studentId,
            $command->courseId
        );
        if ($existingEnrollment !== null) {
            throw new \RuntimeException('Student is already enrolled in this course');
        }

        // 4. Crear la matrícula (regles de negoci al domini)
        $enrollment = Enrollment::enroll($student, $course);

        // 5. Afegir la matrícula a l'estudiant
        $student->addEnrollment($enrollment);

        // 6. Persistir
        $this->enrollmentRepository->save($enrollment);
        $this->studentRepository->save($student);
    }
}
