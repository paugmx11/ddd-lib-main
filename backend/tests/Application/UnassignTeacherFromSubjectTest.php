<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectCommand;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;
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
final class UnassignTeacherFromSubjectTest extends TestCase
{
    public function test_teacher_can_be_unassigned_from_subject(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

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

        $subject->assignTeacher($teacher);

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('teacher-1')))
            ->willReturn($teacher);

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('subject-1')))
            ->willReturn($subject);

        $subjectRepository->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($subject));

        $handler = new UnassignTeacherFromSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $command = new UnassignTeacherFromSubjectCommand('teacher-1', 'subject-1');
        $handler->handle($command);

        $this->assertFalse($subject->hasTeacher());
        $this->assertSame([], $subject->teacherIds());
    }

    public function test_unassign_subject_not_found_throws_exception(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('non-existent')))
            ->willReturn(null);

        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('teacher-1')))
            ->willReturn($teacher);

        $handler = new UnassignTeacherFromSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Subject not found');

        $command = new UnassignTeacherFromSubjectCommand('teacher-1', 'non-existent');
        $handler->handle($command);
    }

    public function test_unassign_subject_without_teacher_throws_exception(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

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

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('subject-1')))
            ->willReturn($subject);

        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('teacher-1')))
            ->willReturn($teacher);

        $handler = new UnassignTeacherFromSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Subject has no teacher assigned');

        $command = new UnassignTeacherFromSubjectCommand('teacher-1', 'subject-1');
        $handler->handle($command);
    }

    public function test_unassign_teacher_not_found_throws_exception(): void
    {
        $teacherRepository = $this->createMock(TeacherRepository::class);
        $subjectRepository = $this->createMock(SubjectRepository::class);

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('non-existent')))
            ->willReturn(null);

        $handler = new UnassignTeacherFromSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Teacher not found');

        $command = new UnassignTeacherFromSubjectCommand('non-existent', 'subject-1');
        $handler->handle($command);
    }

    public function test_unassign_subject_not_assigned_to_teacher_throws_exception(): void
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
        $subject->assignTeacher($teacher2);

        $teacherRepository->method('find')
            ->with($this->equalTo(new TeacherId('teacher-1')))
            ->willReturn($teacher1);

        $subjectRepository->method('find')
            ->with($this->equalTo(new SubjectId('subject-1')))
            ->willReturn($subject);

        $handler = new UnassignTeacherFromSubjectHandler(
            $teacherRepository,
            $subjectRepository
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Subject is not assigned to this teacher');

        $command = new UnassignTeacherFromSubjectCommand('teacher-1', 'subject-1');
        $handler->handle($command);
    }
}
