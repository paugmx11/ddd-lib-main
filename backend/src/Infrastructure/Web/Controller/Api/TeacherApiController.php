<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller\Api;

use App\Application\CreateTeacher\CreateTeacherCommand;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectCommand;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectCommand;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;
use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;

final class TeacherApiController
{
    public function __construct(
        private TeacherRepository $teacherRepository,
        private CreateTeacherHandler $createTeacherHandler,
        private AssignTeacherToSubjectHandler $assignTeacherToSubjectHandler,
        private UnassignTeacherFromSubjectHandler $unassignTeacherFromSubjectHandler
    ) {}

    public function index(): void
    {
        $teachers = $this->teacherRepository->findAll();
        $payload = array_map([$this, 'serializeTeacher'], $teachers);
        $this->jsonResponse($payload, 200);
    }

    public function show(string $id): void
    {
        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            $this->jsonResponse(['error' => 'Teacher not found'], 404);
            return;
        }

        $this->jsonResponse($this->serializeTeacher($teacher), 200);
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
            $teacherId = TeacherId::generate()->value();
            $command = new CreateTeacherCommand(
                $teacherId,
                $name,
                $email
            );
            $this->createTeacherHandler->handle($command);
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

        header('Location: /api/teachers/' . $teacherId);
        $this->jsonResponse(['id' => $teacherId], 201);
    }

    public function update(string $id): void
    {
        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            $this->jsonResponse(['error' => 'Teacher not found'], 404);
            return;
        }

        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = array_key_exists('name', $data) ? trim((string) $data['name']) : $teacher->name();
        $email = array_key_exists('email', $data) ? trim((string) $data['email']) : $teacher->email();

        if ($name === '' || $email === '') {
            $this->jsonResponse(['error' => 'name and email cannot be empty'], 422);
            return;
        }

        $existingTeacher = $this->teacherRepository->findByEmail($email);
        if ($existingTeacher !== null && $existingTeacher->id()->value() !== $teacher->id()->value()) {
            $this->jsonResponse(['error' => 'A teacher with this email already exists'], 409);
            return;
        }

        try {
            $teacher->updateProfile($name, $email);
            $this->teacherRepository->save($teacher);
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        $this->jsonResponse($this->serializeTeacher($teacher), 200);
    }

    public function destroy(string $id): void
    {
        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            $this->jsonResponse(['error' => 'Teacher not found'], 404);
            return;
        }

        try {
            $this->teacherRepository->delete($teacher);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        http_response_code(204);
    }

    public function assign(string $id): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $subjectId = trim((string) ($data['subjectId'] ?? ''));
        if ($subjectId === '') {
            $this->jsonResponse(['error' => 'subjectId is required'], 422);
            return;
        }

        try {
            $command = new AssignTeacherToSubjectCommand($id, $subjectId);
            $this->assignTeacherToSubjectHandler->handle($command);
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            if ($message === 'Teacher not found' || $message === 'Subject not found') {
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

        $this->jsonResponse(['message' => 'Teacher assigned to subject'], 200);
    }

    public function unassign(string $id): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $subjectId = trim((string) ($data['subjectId'] ?? ''));
        if ($subjectId === '') {
            $this->jsonResponse(['error' => 'subjectId is required'], 422);
            return;
        }

        try {
            $command = new UnassignTeacherFromSubjectCommand($id, $subjectId);
            $this->unassignTeacherFromSubjectHandler->handle($command);
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            if ($message === 'Teacher not found' || $message === 'Subject not found') {
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

        $this->jsonResponse(['message' => 'Teacher unassigned from subject'], 200);
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

    private function serializeTeacher(Teacher $teacher): array
    {
        $subjectIds = [];
        foreach ($teacher->subjects() as $subject) {
            $subjectIds[] = $subject->id()->value();
        }

        return [
            'id' => $teacher->id()->value(),
            'name' => $teacher->name(),
            'email' => $teacher->email(),
            'subjectCount' => count($subjectIds),
            'subjectIds' => $subjectIds,
        ];
    }
}
