<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    public function __construct(
        private ApiController $apiController
    ) {}

    public function dispatch(string $method, string $uri, string $rawBody = ''): array
    {
        $payload = $this->decodeJson($rawBody);

        if (isset($payload['__invalid_json'])) {
            return JsonResponse::error(400, 'Invalid JSON body');
        }

        if ($method === 'GET' && $uri === '/api') {
            return JsonResponse::ok([
                'message' => 'School REST API',
                'resources' => ['students', 'teachers', 'courses'],
            ]);
        }

        if ($method === 'GET' && $uri === '/api/students') {
            return $this->apiController->listStudents();
        }

        if ($method === 'POST' && $uri === '/api/students') {
            return $this->apiController->createStudent($payload);
        }

        if ($method === 'GET' && preg_match('#^/api/students/([^/]+)$#', $uri, $matches)) {
            return $this->apiController->getStudent($matches[1]);
        }

        if (($method === 'PUT' || $method === 'PATCH') && preg_match('#^/api/students/([^/]+)$#', $uri, $matches)) {
            return $this->apiController->updateStudent($matches[1], $payload);
        }

        if ($method === 'DELETE' && preg_match('#^/api/students/([^/]+)$#', $uri, $matches)) {
            return $this->apiController->deleteStudent($matches[1]);
        }

        if ($method === 'POST' && preg_match('#^/api/students/([^/]+)/enrollments$#', $uri, $matches)) {
            return $this->apiController->enrollStudent($matches[1], $payload);
        }

        if ($method === 'GET' && $uri === '/api/teachers') {
            return $this->apiController->listTeachers();
        }

        if ($method === 'POST' && $uri === '/api/teachers') {
            return $this->apiController->createTeacher($payload);
        }

        if ($method === 'GET' && preg_match('#^/api/teachers/([^/]+)$#', $uri, $matches)) {
            return $this->apiController->getTeacher($matches[1]);
        }

        if (($method === 'PUT' || $method === 'PATCH') && preg_match('#^/api/teachers/([^/]+)$#', $uri, $matches)) {
            return $this->apiController->updateTeacher($matches[1], $payload);
        }

        if ($method === 'DELETE' && preg_match('#^/api/teachers/([^/]+)$#', $uri, $matches)) {
            return $this->apiController->deleteTeacher($matches[1]);
        }

        if ($method === 'GET' && $uri === '/api/courses') {
            return $this->apiController->listCourses();
        }

        if ($method === 'POST' && $uri === '/api/courses') {
            return $this->apiController->createCourse($payload);
        }

        return JsonResponse::error(404, 'Not Found');
    }

    private function decodeJson(string $rawBody): array
    {
        if (trim($rawBody) === '') {
            return [];
        }

        try {
            $payload = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ['__invalid_json' => true];
        }

        return is_array($payload) ? $payload : ['__invalid_json' => true];
    }
}
