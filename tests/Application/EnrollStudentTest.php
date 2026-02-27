<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Application\EnrollStudent\EnrollStudentCommand;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;
use App\Domain\Enrollment\Enrollment;
use App\Domain\Enrollment\EnrollmentRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class EnrollStudentTest extends TestCase
{
    public function test_student_can_be_enrolled_in_course(): void
    {
        // Arrange: Crear mocks dels repositoris
        $studentRepository = $this->createMock(StudentRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $enrollmentRepository = $this->createMock(EnrollmentRepository::class);

        // Crear student i course reals (domini pur)
        $student = new Student(
            new StudentId('student-1'),
            'John Doe',
            'john@example.com'
        );

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );

        // Configurar mocks
        $studentRepository->method('find')
            ->with($this->equalTo(new StudentId('student-1')))
            ->willReturn($student);

        $courseRepository->method('find')
            ->with($this->equalTo(new CourseId('course-1')))
            ->willReturn($course);

        $enrollmentRepository->method('findByStudentAndCourse')
            ->with('student-1', 'course-1')
            ->willReturn(null);

        // Expectatives: Verificar que es criden els save
        $enrollmentRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Enrollment::class));

        $studentRepository->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($student));

        // Act: Executar el handler
        $handler = new EnrollStudentHandler(
            $studentRepository,
            $courseRepository,
            $enrollmentRepository
        );

        $command = new EnrollStudentCommand('student-1', 'course-1');
        $handler->handle($command);

        // Assert: Verificar que l'estudiant té la matrícula
        $this->assertTrue($student->isEnrolledIn('course-1'));
    }

    public function test_enroll_student_not_found_throws_exception(): void
    {
        $studentRepository = $this->createMock(StudentRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $enrollmentRepository = $this->createMock(EnrollmentRepository::class);

        $studentRepository->method('find')
            ->with($this->equalTo(new StudentId('non-existent')))
            ->willReturn(null);

        $handler = new EnrollStudentHandler(
            $studentRepository,
            $courseRepository,
            $enrollmentRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Student not found');

        $command = new EnrollStudentCommand('non-existent', 'course-1');
        $handler->handle($command);
    }

    public function test_enroll_course_not_found_throws_exception(): void
    {
        $studentRepository = $this->createMock(StudentRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $enrollmentRepository = $this->createMock(EnrollmentRepository::class);

        $student = new Student(
            new StudentId('student-1'),
            'John Doe',
            'john@example.com'
        );

        $studentRepository->method('find')
            ->willReturn($student);

        $courseRepository->method('find')
            ->with($this->equalTo(new CourseId('non-existent')))
            ->willReturn(null);

        $handler = new EnrollStudentHandler(
            $studentRepository,
            $courseRepository,
            $enrollmentRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Course not found');

        $command = new EnrollStudentCommand('student-1', 'non-existent');
        $handler->handle($command);
    }

    public function test_enroll_already_enrolled_throws_exception(): void
    {
        $studentRepository = $this->createMock(StudentRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $enrollmentRepository = $this->createMock(EnrollmentRepository::class);

        $student = new Student(
            new StudentId('student-1'),
            'John Doe',
            'john@example.com'
        );

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );

        $existingEnrollment = Enrollment::enroll($student, $course);

        $studentRepository->method('find')->willReturn($student);
        $courseRepository->method('find')->willReturn($course);
        $enrollmentRepository->method('findByStudentAndCourse')
            ->willReturn($existingEnrollment);

        $handler = new EnrollStudentHandler(
            $studentRepository,
            $courseRepository,
            $enrollmentRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Student is already enrolled in this course');

        $command = new EnrollStudentCommand('student-1', 'course-1');
        $handler->handle($command);
    }
}
