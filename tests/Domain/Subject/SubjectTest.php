<?php

declare(strict_types=1);

namespace Tests\Domain\Subject;

use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use PHPUnit\Framework\TestCase;

final class SubjectTest extends TestCase
{
    public function test_subject_can_be_created(): void
    {
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

        $this->assertEquals('subject-1', $subject->id()->value());
        $this->assertEquals('Domain Driven Design', $subject->name());
        $this->assertEquals('course-1', $subject->courseId()->value());
        $this->assertNull($subject->teacher());
        $this->assertFalse($subject->hasTeacher());
    }

    public function test_subject_name_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Subject name cannot be empty');

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );

        new Subject(
            new SubjectId('subject-1'),
            '',
            $course
        );
    }

    public function test_subject_can_have_teacher_assigned(): void
    {
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

        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $subject->assignTeacher($teacher);

        $this->assertTrue($subject->hasTeacher());
        $this->assertEquals('teacher-1', $subject->teacherId()->value());
        $this->assertSame($teacher, $subject->teacher());
    }

    public function test_subject_teacher_can_be_removed(): void
    {
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

        $teacher = new Teacher(
            new TeacherId('teacher-1'),
            'Jane Smith',
            'jane@example.com'
        );

        $subject->assignTeacher($teacher);
        $this->assertTrue($subject->hasTeacher());

        $subject->removeTeacher();
        $this->assertFalse($subject->hasTeacher());
        $this->assertNull($subject->teacher());
    }

    public function test_subject_id_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('SubjectId cannot be empty');

        new SubjectId('');
    }

    public function test_subject_id_can_be_generated(): void
    {
        $id = SubjectId::generate();

        $this->assertStringStartsWith('subject_', $id->value());
    }

    public function test_subject_id_equals(): void
    {
        $id1 = new SubjectId('subject-1');
        $id2 = new SubjectId('subject-1');
        $id3 = new SubjectId('subject-2');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
