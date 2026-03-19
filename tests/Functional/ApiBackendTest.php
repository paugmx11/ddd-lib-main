<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\CreateCourse\CreateCourseHandler;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;
use App\Http\ApiController;
use App\Http\Router;
use App\Infrastructure\Persistence\DoctrineCourseRepository;
use App\Infrastructure\Persistence\DoctrineEnrollmentRepository;
use App\Infrastructure\Persistence\DoctrineStudentRepository;
use App\Infrastructure\Persistence\DoctrineSubjectRepository;
use App\Infrastructure\Persistence\DoctrineTeacherRepository;
use PHPUnit\Framework\TestCase;

final class ApiBackendTest extends TestCase
{
    private string $databaseUrl;
    private string $databasePath;
    private Router $router;

    protected function setUp(): void
    {
        $this->databasePath = sys_get_temp_dir() . '/school-api-' . bin2hex(random_bytes(8)) . '.sqlite';
        $this->databaseUrl = 'sqlite:///' . $this->databasePath;

        putenv('DATABASE_URL=' . $this->databaseUrl);
        $_ENV['DATABASE_URL'] = $this->databaseUrl;
        $_SERVER['DATABASE_URL'] = $this->databaseUrl;

        $entityManager = require __DIR__ . '/../../bootstrap.php';

        $studentRepository = new DoctrineStudentRepository($entityManager);
        $teacherRepository = new DoctrineTeacherRepository($entityManager);
        $courseRepository = new DoctrineCourseRepository($entityManager);
        $subjectRepository = new DoctrineSubjectRepository($entityManager);
        $enrollmentRepository = new DoctrineEnrollmentRepository($entityManager);

        $this->router = new Router(new ApiController(
            $studentRepository,
            $teacherRepository,
            $subjectRepository,
            $courseRepository,
            new CreateStudentHandler($studentRepository),
            new CreateTeacherHandler($teacherRepository),
            new CreateSubjectHandler($subjectRepository, $courseRepository),
            new CreateCourseHandler($courseRepository),
            new EnrollStudentHandler($studentRepository, $courseRepository, $enrollmentRepository),
            new AssignTeacherToSubjectHandler($teacherRepository, $subjectRepository),
            new UnassignTeacherFromSubjectHandler($teacherRepository, $subjectRepository)
        ));
    }

    protected function tearDown(): void
    {
        putenv('DATABASE_URL');
        unset($_ENV['DATABASE_URL'], $_SERVER['DATABASE_URL']);

        if (file_exists($this->databasePath)) {
            unlink($this->databasePath);
        }
    }

    public function test_student_endpoints_support_rest_flow(): void
    {
        $courseResponse = $this->request('POST', '/api/courses', [
            'name' => 'DAW 2 Backend',
            'startDate' => '2026-03-01',
            'endDate' => '2026-06-30',
            'description' => 'Course for API tests',
        ]);
        $this->assertSame(201, $courseResponse['status']);
        $courseId = $courseResponse['body']['data']['id'];

        $createResponse = $this->request('POST', '/api/students', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
        ]);
        $this->assertSame(201, $createResponse['status']);
        $studentId = $createResponse['body']['data']['id'];

        $showResponse = $this->request('GET', '/api/students/' . $studentId);
        $this->assertSame(200, $showResponse['status']);
        $this->assertSame('Ada Lovelace', $showResponse['body']['data']['name']);

        $updateResponse = $this->request('PUT', '/api/students/' . $studentId, [
            'name' => 'Ada Byron',
            'email' => 'ada.byron@example.com',
        ]);
        $this->assertSame(200, $updateResponse['status']);
        $this->assertSame('Ada Byron', $updateResponse['body']['data']['name']);

        $enrollResponse = $this->request('POST', '/api/students/' . $studentId . '/enrollments', [
            'courseId' => $courseId,
        ]);
        $this->assertSame(200, $enrollResponse['status']);
        $this->assertCount(1, $enrollResponse['body']['data']['enrollments']);

        $deleteResponse = $this->request('DELETE', '/api/students/' . $studentId);
        $this->assertSame(204, $deleteResponse['status']);

        $missingResponse = $this->request('GET', '/api/students/' . $studentId);
        $this->assertSame(404, $missingResponse['status']);
    }

    public function test_teacher_and_subject_endpoints_support_assignment_flow(): void
    {
        $courseResponse = $this->request('POST', '/api/courses', [
            'name' => 'DAW 2 Subjects',
            'startDate' => '2026-03-01',
            'endDate' => '2026-06-30',
        ]);
        $this->assertSame(201, $courseResponse['status']);
        $courseId = $courseResponse['body']['data']['id'];

        $teacherResponse = $this->request('POST', '/api/teachers', [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
        ]);
        $this->assertSame(201, $teacherResponse['status']);
        $teacherId = $teacherResponse['body']['data']['id'];

        $subjectResponse = $this->request('POST', '/api/subjects', [
            'name' => 'Arquitectura REST',
            'courseId' => $courseId,
        ]);
        $this->assertSame(201, $subjectResponse['status']);
        $subjectId = $subjectResponse['body']['data']['id'];

        $assignResponse = $this->request('PUT', '/api/subjects/' . $subjectId . '/teacher', [
            'teacherId' => $teacherId,
        ]);
        $this->assertSame(200, $assignResponse['status']);
        $this->assertSame($teacherId, $assignResponse['body']['data']['teacher']['id']);

        $teacherShowResponse = $this->request('GET', '/api/teachers/' . $teacherId);
        $this->assertSame(200, $teacherShowResponse['status']);
        $this->assertCount(1, $teacherShowResponse['body']['data']['subjects']);

        $unassignResponse = $this->request('DELETE', '/api/subjects/' . $subjectId . '/teacher');
        $this->assertSame(204, $unassignResponse['status']);

        $subjectShowResponse = $this->request('GET', '/api/subjects/' . $subjectId);
        $this->assertSame(200, $subjectShowResponse['status']);
        $this->assertNull($subjectShowResponse['body']['data']['teacher']);
    }

    public function test_invalid_json_returns_bad_request(): void
    {
        $response = $this->router->dispatch('POST', '/api/students', '{"name":');

        $this->assertSame(400, $response['status']);
        $this->assertSame('Invalid JSON body', $response['body']['error']);
    }

    private function request(string $method, string $uri, ?array $payload = null): array
    {
        return $this->router->dispatch(
            $method,
            $uri,
            $payload === null ? '' : json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }
}
