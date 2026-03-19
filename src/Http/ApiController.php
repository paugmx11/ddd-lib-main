<?php

declare(strict_types=1);

namespace App\Http;

use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\CreateCourse\CreateCourseCommand;
use App\Application\CreateCourse\CreateCourseHandler;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;
use App\Domain\Student\StudentRepository;
use App\Domain\Subject\SubjectRepository;
use App\Domain\Teacher\TeacherRepository;

final class ApiController
{
    public function __construct(
        private StudentRepository $studentRepository,
        private TeacherRepository $teacherRepository,
        private SubjectRepository $subjectRepository,
        private CourseRepository $courseRepository,
        private CreateStudentHandler $createStudentHandler,
        private CreateTeacherHandler $createTeacherHandler,
        private CreateSubjectHandler $createSubjectHandler,
        private CreateCourseHandler $createCourseHandler,
        private EnrollStudentHandler $enrollStudentHandler,
        private AssignTeacherToSubjectHandler $assignTeacherHandler,
        private UnassignTeacherFromSubjectHandler $unassignTeacherHandler
    ) {}

    public function info(): array
    {
        return JsonResponse::ok([
            'message' => 'School REST API',
            'format' => 'JSON',
        ]);
    }

    public function listCourses(): array
    {
        return JsonResponse::ok([
            'data' => array_map(
                fn (Course $course): array => $this->serializeCourse($course),
                $this->courseRepository->findAll()
            ),
        ]);
    }

    public function createCourse(array $payload): array
    {
        try {
            $command = new CreateCourseCommand(
                CourseId::generate()->value(),
                $this->requiredString($payload, 'name'),
                $this->requiredString($payload, 'startDate'),
                $this->requiredString($payload, 'endDate'),
                $this->optionalString($payload, 'description')
            );

            $this->createCourseHandler->handle($command);

            return JsonResponse::created([
                'data' => $this->serializeCourse(
                    $this->courseRepository->find(new CourseId($command->courseId))
                ),
            ]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    private function requiredString(array $payload, string $field): string
    {
        $value = $payload[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Field "%s" is required', $field));
        }

        return trim($value);
    }

    private function optionalString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a string', $field));
        }

        return trim($value);
    }

    private function handleException(\Throwable $exception): array
    {
        $message = $exception->getMessage();
        $normalizedMessage = strtolower($message);

        if (str_contains($normalizedMessage, 'not found')) {
            return JsonResponse::error(404, $message);
        }

        if (str_contains($normalizedMessage, 'already exists')) {
            return JsonResponse::error(409, $message);
        }

        return JsonResponse::error(400, $message);
    }

    private function serializeCourse(?Course $course): array
    {
        if ($course === null) {
            throw new \RuntimeException('Course not found');
        }

        return [
            'id' => $course->id()->value(),
            'name' => $course->name(),
            'description' => $course->description(),
            'startDate' => $course->startDate()->format('Y-m-d'),
            'endDate' => $course->endDate()->format('Y-m-d'),
            'isActive' => $course->isActive(),
        ];
    }
}
