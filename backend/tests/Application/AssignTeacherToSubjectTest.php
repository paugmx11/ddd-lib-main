<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectCommand;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class AssignTeacherToSubjectTest extends TestCase
{
    public function test_teacher_can_be_assigned_to_subject(): void
    {
        // Arrange: Crear mocks dels repositoris
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

        // Crear teacher, course i subject reals (domini pur)
        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );

        $subject = new Subject(
            new SubjectId('subject-1'),
            'Domain Driven Design',
            $course
        );

        // Configurar mocks
        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('teacher-1')))
            ->willReturn($teacher);

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('subject-1')))
            ->willReturn($subject);

        // Expectatives: Verificar que es criden els save
        $subjectRepository->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($subject));

        // Act: Executar el handler
        $handler = new AssignTeacherToSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $command = new AssignTeacherToSubjectCommand('teacher-1', 'subject-1');
        $handler->handle($command);

        // Assert: Verificar que l'assignatura té el teacher assignat
        $this->assertTrue($subject->hasTeacher());
        $this->assertSame(['teacher-1'], $subject->teacherIds());
        $this->assertTrue($teacher->hasSubject('subject-1'));
    }

    public function test_assign_teacher_not_found_throws_exception(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('non-existent')))
            ->willReturn(null);

        $handler = new AssignTeacherToSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Teacher not found');

        $command = new AssignTeacherToSubjectCommand('non-existent', 'subject-1');
        $handler->handle($command);
    }

    public function test_assign_subject_not_found_throws_exception(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $teacherRepository->method('find')
            ->willReturn($teacher);

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('non-existent')))
            ->willReturn(null);

        $handler = new AssignTeacherToSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Subject not found');

        $command = new AssignTeacherToSubjectCommand('teacher-1', 'non-existent');
        $handler->handle($command);
    }

    public function test_assign_teacher_to_subject_can_have_multiple_teachers(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

        $teacher1 = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $teacher2 = new Teacher(
            new TeacherId('teacher-2'),
            'Bob Wilson',
            'bob@example.com'
        );

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );

        $subject = new Subject(
            new SubjectId('subject-1'),
            'Domain Driven Design',
            $course
        );

        // Assignar teacher1 primer
        $subject->assignTeacher($teacher1);

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('teacher-2')))
            ->willReturn($teacher2);

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('subject-1')))
            ->willReturn($subject);

        $handler = new AssignTeacherToSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $command = new AssignTeacherToSubjectCommand('teacher-2', 'subject-1');
        $handler->handle($command);

        $this->assertSame(['teacher-1', 'teacher-2'], $subject->teacherIds());
    }
}
