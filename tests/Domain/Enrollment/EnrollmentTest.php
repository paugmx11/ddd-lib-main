<?php

declare(strict_types=1);

namespace Tests\Domain\Enrollment;

use App\Domain\Enrollment\Enrollment;
use App\Domain\Enrollment\EnrollmentId;
use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use PHPUnit\Framework\TestCase;

final class EnrollmentTest extends TestCase
{
    public function test_enrollment_can_be_created(): void
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

        $this->assertStringStartsWith('enrollment_', $enrollment->id()->value());
        $this->assertEquals('student-1', $enrollment->studentId()->value());
        $this->assertEquals('course-1', $enrollment->courseId()->value());
        $this->assertTrue($enrollment->isActive());
        $this->assertInstanceOf(\DateTimeImmutable::class, $enrollment->enrolledAt());
    }

    public function test_cannot_enroll_in_course_that_has_ended(): void
    {
        $student = new Student(
            new StudentId('student-1'),
            'John Doe',
            'john@example.com'
        );

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('-30 days'),
            new \DateTimeImmutable('-1 day')
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cannot enroll in a course that has already ended');

        Enrollment::enroll($student, $course);
    }

    public function test_enrollment_can_be_completed(): void
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
        $this->assertTrue($enrollment->isActive());

        $enrollment->complete();
        $this->assertFalse($enrollment->isActive());
        $this->assertEquals('completed', $enrollment->status());
    }

    public function test_enrollment_can_be_cancelled(): void
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
        $this->assertTrue($enrollment->isActive());

        $enrollment->cancel();
        $this->assertFalse($enrollment->isActive());
        $this->assertEquals('cancelled', $enrollment->status());
    }

    public function test_enrollment_id_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('EnrollmentId cannot be empty');

        new EnrollmentId('');
    }

    public function test_enrollment_id_can_be_generated(): void
    {
        $id = EnrollmentId::generate();

        $this->assertStringStartsWith('enrollment_', $id->value());
    }

    public function test_enrollment_id_equals(): void
    {
        $id1 = new EnrollmentId('enrollment-1');
        $id2 = new EnrollmentId('enrollment-1');
        $id3 = new EnrollmentId('enrollment-2');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
