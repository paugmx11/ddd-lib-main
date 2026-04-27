<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller\Api;

use App\Application\CreateSubject\CreateSubjectCommand;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;
use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;

final class SubjectApiController
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private CourseRepository $courseRepository,
        private CreateSubjectHandler $createSubjectHandler
    ) {}

    public function index(): void
    {
        $subjects = $this->subjectRepository->findAll();
        $payload = array_map([$this, 'serializeSubject'], $subjects);
        $this->jsonResponse($payload, 200);
    }

    public function show(string $id): void
    {
        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            $this->jsonResponse(['error' => 'Subject not found'], 404);
            return;
        }

        $this->jsonResponse($this->serializeSubject($subject), 200);
    }

    public function store(): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = trim((string) ($data['name'] ?? ''));
        $courseId = trim((string) ($data['courseId'] ?? ''));

        if ($name === '' || $courseId === '') {
            $this->jsonResponse(['error' => 'name and courseId are required'], 422);
            return;
        }

        try {
            $subjectId = SubjectId::generate()->value();
            $command = new CreateSubjectCommand(
                $subjectId,
                $name,
                $courseId
            );
            $this->createSubjectHandler->handle($command);
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

        header('Location: /api/subjects/' . $subjectId);
        $this->jsonResponse(['id' => $subjectId], 201);
    }

    public function update(string $id): void
    {
        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            $this->jsonResponse(['error' => 'Subject not found'], 404);
            return;
        }

        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = array_key_exists('name', $data) ? trim((string) $data['name']) : $subject->name();
        $courseIdRaw = array_key_exists('courseId', $data) ? trim((string) $data['courseId']) : $subject->courseId()->value();

        if ($name === '' || $courseIdRaw === '') {
            $this->jsonResponse(['error' => 'name and courseId cannot be empty'], 422);
            return;
        }

        $course = $this->courseRepository->find(new CourseId($courseIdRaw));
        if ($course === null) {
            $this->jsonResponse(['error' => 'Course not found'], 404);
            return;
        }

        $existingSubjects = $this->subjectRepository->findByCourse($courseIdRaw);
        foreach ($existingSubjects as $existingSubject) {
            if ($existingSubject->name() === $name && $existingSubject->id()->value() !== $subject->id()->value()) {
                $this->jsonResponse(['error' => 'A subject with this name already exists in this course'], 409);
                return;
            }
        }

        try {
            $subject->update($name, $course);
            $this->subjectRepository->save($subject);
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Unexpected error'], 500);
            return;
        }

        $this->jsonResponse($this->serializeSubject($subject), 200);
    }

    public function destroy(string $id): void
    {
        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            $this->jsonResponse(['error' => 'Subject not found'], 404);
            return;
        }

        try {
            $this->subjectRepository->delete($subject);
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

    private function serializeSubject(Subject $subject): array
    {
        return [
            'id' => $subject->id()->value(),
            'name' => $subject->name(),
            'courseId' => $subject->courseId()->value(),
            'teacherIds' => $subject->teacherIds(),
        ];
    }
}
