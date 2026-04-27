<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller;

use App\Application\CreateStudent\CreateStudentCommand;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\EnrollStudent\EnrollStudentCommand;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;
use App\Domain\Course\CourseRepository;

final class StudentController
{
    public function __construct(
        private StudentRepository $studentRepository,
        private CourseRepository $courseRepository,
        private CreateStudentHandler $createStudentHandler,
        private EnrollStudentHandler $enrollStudentHandler
    ) {}

    public function index(): void
    {
        $students = $this->studentRepository->findAll();
        $courses = $this->courseRepository->findAll();
        
        include __DIR__ . '/../../../../views/student/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $command = new CreateStudentCommand(
                    StudentId::generate()->value(),
                    $_POST['name'],
                    $_POST['email']
                );
                
                $this->createStudentHandler->handle($command);
                
                header('Location: /student?success=Student created successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        include __DIR__ . '/../../../../views/student/create.php';
    }

    public function edit(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Student id is required';
            $this->index();
            return;
        }

        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            $error = 'Student not found';
            $this->index();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = trim((string) ($_POST['name'] ?? ''));
                $email = trim((string) ($_POST['email'] ?? ''));

                $existingStudent = $this->studentRepository->findByEmail($email);
                if (
                    $existingStudent !== null
                    && $existingStudent->id()->value() !== $student->id()->value()
                ) {
                    throw new \RuntimeException('A student with this email already exists');
                }

                $student->updateProfile($name, $email);
                $this->studentRepository->save($student);

                header('Location: /student?success=Student updated successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        include __DIR__ . '/../../../../views/student/edit.php';
    }

    public function delete(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Student id is required';
            $this->index();
            return;
        }

        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            $error = 'Student not found';
            $this->index();
            return;
        }

        try {
            $this->studentRepository->delete($student);
            header('Location: /student?success=Student deleted successfully');
            exit;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->index();
        }
    }

    public function enroll(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $command = new EnrollStudentCommand(
                    $_POST['studentId'],
                    $_POST['courseId']
                );
                
                $this->enrollStudentHandler->handle($command);
                
                header('Location: /student?success=Student enrolled successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $students = $this->studentRepository->findAll();
        $courses = $this->courseRepository->findAll();
        
        include __DIR__ . '/../../../../views/student/enroll.php';
    }
}
