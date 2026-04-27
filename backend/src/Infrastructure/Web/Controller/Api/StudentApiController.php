<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller\Api;

use App\Application\CreateStudent\CreateStudentCommand;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\EnrollStudent\EnrollStudentCommand;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;

final class StudentApiController
{
    public function __construct(
        private StudentRepository $studentRepository,
        private CreateStudentHandler $createStudentHandler,
        private EnrollStudentHandler $enrollStudentHandler
    ) {}

    public function index(): void
    {
        $students = $this->studentRepository->findAll();
        $payload = array_map([$this, 'serializeStudent'], $students);
        $this->jsonResponse($payload, 200);
    }

    public function show(string $id): void
    {
        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            $this->jsonResponse(['error' => 'Student not found'], 404);
            return;
        }

        $this->jsonResponse($this->serializeStudent($student), 200);
    }

    public function store(): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));

        if ($name === '' || $email === '') {
            $this->jsonResponse(['error' => 'name and email are required'], 422);
            return;
        }

        try {
            $studentId = StudentId::generate()->value();
            $command = new CreateStudentCommand(
                $studentId,
                $name,
                $email
            );
            $this->createStudentHandler->handle($command);
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

        header('Location: /api/students/' . $studentId);
        $this->jsonResponse(['id' => $studentId], 201);
    }

    public function update(string $id): void
    {
        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            $this->jsonResponse(['error' => 'Student not found'], 404);
            return;
        }

        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = array_key_exists('name', $data) ? trim((string) $data['name']) : $student->name();
        $email = array_key_exists('email', $data) ? trim((string) $data['email']) : $student->email();

        if ($name === '' || $email === '') {
            $this->jsonResponse(['error' => 'name and email cannot be empty'], 422);
            return;
        }

        $existingStudent = $this->studentRepository->findByEmail($email);
        if ($existingStudent !== null && $existingStudent->id()->value() !== $student->id()->value()) {
            $this->jsonResponse(['error' => 'A student with this email already exists'], 409);
            return;
        }

        try {
            $student->updateProfile($name, $email);
            $this->studentRepository->save($student);
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        $this->jsonResponse($this->serializeStudent($student), 200);
    }

    public function destroy(string $id): void
    {
        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            $this->jsonResponse(['error' => 'Student not found'], 404);
            return;
        }

        try {
            $this->studentRepository->delete($student);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        http_response_code(204);
    }

    public function enroll(string $id): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $courseId = trim((string) ($data['courseId'] ?? ''));
        if ($courseId === '') {
            $this->jsonResponse(['error' => 'courseId is required'], 422);
            return;
        }

        try {
            $command = new EnrollStudentCommand($id, $courseId);
            $this->enrollStudentHandler->handle($command);
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            if ($message === 'Student not found' || $message === 'Course not found') {
                $this->jsonResponse(['error' => $message], 404);
                return;
            }
            $this->jsonResponse(['error' => $message], 409);
            return;
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        $this->jsonResponse(['message' => 'Student enrolled'], 200);
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

    private function serializeStudent(Student $student): array
    {
        $courseIds = [];
        $activeCourseIds = [];
        foreach ($student->enrollments() as $enrollment) {
            $courseId = $enrollment->courseId()->value();
            $courseIds[] = $courseId;
            if ($enrollment->isActive()) {
                $activeCourseIds[] = $courseId;
            }
        }

        return [
            'id' => $student->id()->value(),
            'name' => $student->name(),
            'email' => $student->email(),
            'courseCount' => count(array_unique($courseIds)),
            'activeCourseCount' => count(array_unique($activeCourseIds)),
            'courseIds' => array_values(array_unique($courseIds)),
            'activeCourseIds' => array_values(array_unique($activeCourseIds)),
        ];
    }
}
