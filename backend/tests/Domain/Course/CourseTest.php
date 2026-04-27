<?php

declare(strict_types=1);

namespace Tests\Domain\Course;

use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use PHPUnit\Framework\TestCase;

final class CourseTest extends TestCase
{
    public function test_course_can_be_created(): void
    {
        $startDate = new \DateTimeImmutable('+1 day');
        $endDate = new \DateTimeImmutable('+30 days');

        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD Masterclass',
            $startDate,
            $endDate,
            'Learn DDD in PHP'
        );

        $this->assertEquals('course-1', $course->id()->value());
        $this->assertEquals('PHP DDD Masterclass', $course->name());
        $this->assertEquals('Learn DDD in PHP', $course->description());
        $this->assertEquals($startDate, $course->startDate());
        $this->assertEquals($endDate, $course->endDate());
    }

    public function test_course_name_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course name cannot be empty');

        new Course(
            new CourseId('course-1'),
            '',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );
    }

    public function test_course_end_date_must_be_after_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('End date must be after start date');

        new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+30 days'),
            new \DateTimeImmutable('+1 day')
        );
    }

    public function test_course_is_active_when_current_date_between_start_and_end(): void
    {
        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+30 days')
        );

        $this->assertTrue($course->isActive());
        $this->assertTrue($course->hasStarted());
        $this->assertFalse($course->hasEnded());
    }

    public function test_course_is_not_active_before_start_date(): void
    {
        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+30 days')
        );

        $this->assertFalse($course->isActive());
        $this->assertFalse($course->hasStarted());
        $this->assertFalse($course->hasEnded());
    }

    public function test_course_is_not_active_after_end_date(): void
    {
        $course = new Course(
            new CourseId('course-1'),
            'PHP DDD',
            new \DateTimeImmutable('-30 days'),
            new \DateTimeImmutable('-1 day')
        );

        $this->assertFalse($course->isActive());
        $this->assertTrue($course->hasStarted());
        $this->assertTrue($course->hasEnded());
    }

    public function test_course_id_cannot_be_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('CourseId cannot be empty');

        new CourseId('');
    }

    public function test_course_id_can_be_generated(): void
    {
        $id = CourseId::generate();

        $this->assertStringStartsWith('course_', $id->value());
    }

    public function test_course_id_equals(): void
    {
        $id1 = new CourseId('course-1');
        $id2 = new CourseId('course-1');
        $id3 = new CourseId('course-2');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
