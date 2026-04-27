<?php

declare(strict_types=1);

namespace Tests\Domain\Teacher;

use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use PHPUnit\Framework\TestCase;

final class TeacherTest extends TestCase
{
    public function test_teacher_can_be_created(): void
    {
        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $this->assertEquals('teacher-1', $teacher->id()->value());
        $this->assertEquals('Jane Smith', $teacher->name());
        $this->assertEquals('jane@example.com', $teacher->email());
    }

    public function test_teacher_name_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Teacher name cannot be empty');

        new Teacher(
            new TeacherId('teacher-1'),
            '',
            'jane@example.com'
        );
    }

    public function test_teacher_email_must_be_valid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'invalid-email'
        );
    }

    public function test_teacher_can_have_subjects(): void
    {
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
        $teacher->addSubject($subject);

        $this->assertTrue($teacher->hasSubject('subject-1'));
    }

    public function test_teacher_subject_can_be_removed(): void
    {
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
        $teacher->addSubject($subject);
        $this->assertTrue($teacher->hasSubject('subject-1'));

        $teacher->removeSubject('subject-1');

        $this->assertFalse($teacher->hasSubject('subject-1'));
    }

    public function test_teacher_id_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('TeacherId cannot be empty');

        new TeacherId('');
    }

    public function test_teacher_id_can_be_generated(): void
    {
        $id = TeacherId::generate();

        $this->assertStringStartsWith('teacher_', $id->value());
    }

    public function test_teacher_id_equals(): void
    {
        $id1 = new TeacherId('teacher-1');
        $id2 = new TeacherId('teacher-1');
        $id3 = new TeacherId('teacher-2');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
