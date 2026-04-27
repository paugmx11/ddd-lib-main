<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller\Api;

use App\Application\CreateCourse\CreateCourseCommand;
use App\Application\CreateCourse\CreateCourseHandler;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;

final class CourseApiController
{
    public function __construct(
        private CourseRepository $courseRepository,
        private CreateCourseHandler $createCourseHandler
    ) {}

    public function index(): void
    {
        $courses = $this->courseRepository->findAll();
        $payload = array_map([$this, 'serializeCourse'], $courses);

        $this->jsonResponse($payload, 200);
    }

    public function show(string $id): void
    {
        $course = $this->courseRepository->find(new CourseId($id));
        if ($course === null) {
            $this->jsonResponse(['error' => 'Course not found'], 404);
            return;
        }

        $this->jsonResponse($this->serializeCourse($course), 200);
    }

    public function store(): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = trim((string) ($data['name'] ?? ''));
        $startDate = (string) ($data['startDate'] ?? '');
        $endDate = (string) ($data['endDate'] ?? '');
        $description = isset($data['description']) ? trim((string) $data['description']) : null;

        if ($name === '' || $startDate === '' || $endDate === '') {
            $this->jsonResponse(['error' => 'name, startDate and endDate are required'], 422);
            return;
        }

        try {
            $courseId = CourseId::generate()->value();
            $command = new CreateCourseCommand(
                $courseId,
                $name,
                $startDate,
                $endDate,
                $description === '' ? null : $description
            );
            $this->createCourseHandler->handle($command);
        } catch (\RuntimeException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 409);
            return;
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        header('Location: /api/courses/' . $courseId);
        $this->jsonResponse(['id' => $courseId], 201);
    }

    public function update(string $id): void
    {
        $course = $this->courseRepository->find(new CourseId($id));
        if ($course === null) {
            $this->jsonResponse(['error' => 'Course not found'], 404);
            return;
        }

        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = array_key_exists('name', $data) ? trim((string) $data['name']) : $course->name();
        $description = array_key_exists('description', $data) ? trim((string) $data['description']) : $course->description();
        $startDateRaw = array_key_exists('startDate', $data) ? (string) $data['startDate'] : $course->startDate()->format('Y-m-d');
        $endDateRaw = array_key_exists('endDate', $data) ? (string) $data['endDate'] : $course->endDate()->format('Y-m-d');

        if ($name === '' || $startDateRaw === '' || $endDateRaw === '') {
            $this->jsonResponse(['error' => 'name, startDate and endDate cannot be empty'], 422);
            return;
        }

        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', $startDateRaw);
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d', $endDateRaw);

        if ($startDate === false || $endDate === false) {
            $this->jsonResponse(['error' => 'Invalid date format. Use Y-m-d'], 422);
            return;
        }

        $existingCourse = $this->courseRepository->findByName($name);
        if ($existingCourse !== null && $existingCourse->id()->value() !== $course->id()->value()) {
            $this->jsonResponse(['error' => 'A course with this name already exists'], 409);
            return;
        }

        try {
            $course->updateDetails(
                $name,
                $startDate,
                $endDate,
                $description === '' ? null : $description
            );
            $this->courseRepository->save($course);
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        $this->jsonResponse($this->serializeCourse($course), 200);
    }

    public function destroy(string $id): void
    {
        $course = $this->courseRepository->find(new CourseId($id));
        if ($course === null) {
            $this->jsonResponse(['error' => 'Course not found'], 404);
            return;
        }

        try {
            $this->courseRepository->delete($course);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        http_response_code(204);
    }

    private function readJsonBody(): ?array
    {
        $raw = (string) file_get_contents('php://input');
        if ($raw === '') {
            $this->jsonResponse(['error' => 'Empty request body'], 400);
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON body'], 400);
            return null;
        }

        return $data;
    }

    private function jsonResponse(array $payload, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
    }

    private function serializeCourse(Course $course): array
    {
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
