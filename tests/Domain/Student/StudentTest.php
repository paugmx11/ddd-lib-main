<?php

declare(strict_types=1);

namespace Tests\Domain\Student;

use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Enrollment\Enrollment;
use PHPUnit\Framework\TestCase;

final class StudentTest extends TestCase
{
    public function test_student_can_be_created(): void
    {
        $student = new Student(
            new StudentId('student-1'),
            'John Doe',
            'john@example.com'
        );

        $this->assertEquals('student-1', $student->id()->value());
        $this->assertEquals('John Doe', $student->name());
        $this->assertEquals('john@example.com', $student->email());
    }

    public function test_student_name_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Student name cannot be empty');

        new Student(
            new StudentId('student-1'),
            '',
            'john@example.com'
        );
    }

    public function test_student_email_must_be_valid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new Student(
            new StudentId('student-1'),
            'John Doe',
            'invalid-email'
        );
    }

    public function test_student_can_be_enrolled_in_course(): void
    {
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

        $enrollment = Enrollment::enroll($student, $course);

        $student->addEnrollment($enrollment);

        $this->assertTrue($student->isEnrolledIn('course-1'));
    }

    public function test_student_cannot_be_enrolled_twice_in_same_course(): void
    {
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

        $enrollment1 = Enrollment::enroll($student, $course);
        $student->addEnrollment($enrollment1);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Student is already enrolled in this course');

        $enrollment2 = Enrollment::enroll($student, $course);
        $student->addEnrollment($enrollment2);
    }

    public function test_student_id_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('StudentId cannot be empty');

        new StudentId('');
    }

    public function test_student_id_can_be_generated(): void
    {
        $id = StudentId::generate();

        $this->assertStringStartsWith('student_', $id->value());
    }

    public function test_student_id_equals(): void
    {
        $id1 = new StudentId('student-1');
        $id2 = new StudentId('student-1');
        $id3 = new StudentId('student-2');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
