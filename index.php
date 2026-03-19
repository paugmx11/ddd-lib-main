<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Infrastructure\Persistence\DoctrineStudentRepository;
use App\Infrastructure\Persistence\DoctrineTeacherRepository;
use App\Infrastructure\Persistence\DoctrineCourseRepository;
use App\Infrastructure\Persistence\DoctrineSubjectRepository;
use App\Infrastructure\Persistence\DoctrineEnrollmentRepository;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\CreateCourse\CreateCourseHandler;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;
use App\Infrastructure\Web\Controller\StudentController;
use App\Infrastructure\Web\Controller\TeacherController;
use App\Infrastructure\Web\Controller\CourseController;
use App\Infrastructure\Web\Controller\SubjectController;
use App\Http\ApiController;
use App\Http\Router;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Bootstrap Doctrine EntityManager
$entityManager = require __DIR__ . '/bootstrap.php';

// Create repositories
$studentRepository = new DoctrineStudentRepository($entityManager);
$teacherRepository = new DoctrineTeacherRepository($entityManager);
$courseRepository = new DoctrineCourseRepository($entityManager);
$subjectRepository = new DoctrineSubjectRepository($entityManager);
$enrollmentRepository = new DoctrineEnrollmentRepository($entityManager);

// Create handlers
$createStudentHandler = new CreateStudentHandler($studentRepository);
$createTeacherHandler = new CreateTeacherHandler($teacherRepository);
$createCourseHandler = new CreateCourseHandler($courseRepository);
$createSubjectHandler = new CreateSubjectHandler($subjectRepository, $courseRepository);
$enrollStudentHandler = new EnrollStudentHandler($studentRepository, $courseRepository, $enrollmentRepository);
$assignTeacherHandler = new AssignTeacherToSubjectHandler($teacherRepository, $subjectRepository);
$unassignTeacherHandler = new UnassignTeacherFromSubjectHandler($teacherRepository, $subjectRepository);

// Create controllers
$studentController = new StudentController(
    $studentRepository,
    $courseRepository,
    $createStudentHandler,
    $enrollStudentHandler
);

$teacherController = new TeacherController(
    $teacherRepository,
    $subjectRepository,
    $createTeacherHandler,
    $assignTeacherHandler,
    $unassignTeacherHandler
);

$courseController = new CourseController(
    $courseRepository,
    $createCourseHandler
);

$subjectController = new SubjectController(
    $subjectRepository,
    $courseRepository,
    $createSubjectHandler
);

// Routing
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = require __DIR__ . '/config/routes.php';
$controllers = [
    'student' => $studentController,
    'teacher' => $teacherController,
    'course' => $courseController,
    'subject' => $subjectController,
];

try {
    if (is_string($path) && str_starts_with($path, '/api')) {
        $router = new Router(new ApiController(
            $studentRepository,
            $teacherRepository,
            $subjectRepository,
            $courseRepository,
            $createStudentHandler,
            $createTeacherHandler,
            $createSubjectHandler,
            $createCourseHandler,
            $enrollStudentHandler,
            $assignTeacherHandler,
            $unassignTeacherHandler
        ));

        $response = $router->dispatch(
            $method,
            $path,
            file_get_contents('php://input') ?: ''
        );

        http_response_code($response['status']);
        header('Content-Type: application/json; charset=utf-8');

        if ($response['body'] !== null) {
            echo json_encode($response['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return;
    }

    $route = $routes[$path] ?? null;

    if ($route === null) {
        http_response_code(404);
        echo '<h1>404 - Pàgina no trobada</h1>';
        echo '<p><a href="/">Tornar a l\'inici</a></p>';
        return;
    }

    if ($route === 'home') {
        include __DIR__ . '/views/home.php';
        return;
    }

    [$controllerKey, $action] = explode('.', $route);
    $controller = $controllers[$controllerKey] ?? null;

    if ($controller === null || !method_exists($controller, $action)) {
        http_response_code(500);
        echo '<h1>500 - Error del servidor</h1>';
        echo '<p>Route controller/action not configured correctly</p>';
        echo '<p><a href="/">Tornar a l\'inici</a></p>';
        return;
    }

    $controller->{$action}();
    return;
} catch (Exception $e) {
    http_response_code(500);
    echo '<h1>500 - Error del servidor</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><a href="/">Tornar a l\'inici</a></p>';
}
