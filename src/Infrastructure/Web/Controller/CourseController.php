<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller;

use App\Application\CreateCourse\CreateCourseCommand;
use App\Application\CreateCourse\CreateCourseHandler;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;

final class CourseController
{
    public function __construct(
        private CourseRepository $courseRepository,
        private CreateCourseHandler $createCourseHandler
    ) {}

    public function index(): void
    {
        $courses = $this->courseRepository->findAll();
        
        include __DIR__ . '/../../../../views/course/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $command = new CreateCourseCommand(
                    CourseId::generate()->value(),
                    $_POST['name'],
                    $_POST['startDate'],
                    $_POST['endDate'],
                    $_POST['description'] ?? ''
                );
                
                $this->createCourseHandler->handle($command);
                
                header('Location: /course?success=Course created successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        include __DIR__ . '/../../../../views/course/create.php';
    }

    public function edit(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Course id is required';
            $this->index();
            return;
        }

        $course = $this->courseRepository->find(new CourseId($id));
        if ($course === null) {
            $error = 'Course not found';
            $this->index();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = trim((string) ($_POST['name'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));
                $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', (string) ($_POST['startDate'] ?? ''));
                $endDate = \DateTimeImmutable::createFromFormat('Y-m-d', (string) ($_POST['endDate'] ?? ''));

                if ($startDate === false || $endDate === false) {
                    throw new \InvalidArgumentException('Invalid date format. Use Y-m-d');
                }

                $existingCourse = $this->courseRepository->findByName($name);
                if (
                    $existingCourse !== null
                    && $existingCourse->id()->value() !== $course->id()->value()
                ) {
                    throw new \RuntimeException('A course with this name already exists');
                }

                $course->updateDetails($name, $startDate, $endDate, $description === '' ? null : $description);
                $this->courseRepository->save($course);

                header('Location: /course?success=Course updated successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        include __DIR__ . '/../../../../views/course/edit.php';
    }

    public function delete(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Course id is required';
            $this->index();
            return;
        }

        $course = $this->courseRepository->find(new CourseId($id));
        if ($course === null) {
            $error = 'Course not found';
            $this->index();
            return;
        }

        try {
            $this->courseRepository->delete($course);
            header('Location: /course?success=Course deleted successfully');
            exit;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->index();
        }
    }
}
