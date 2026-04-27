<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller;

use App\Application\CreateTeacher\CreateTeacherCommand;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectCommand;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectCommand;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;
use App\Domain\Subject\SubjectId;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;
use App\Domain\Subject\SubjectRepository;

final class TeacherController
{
    public function __construct(
        private TeacherRepository $teacherRepository,
        private SubjectRepository $subjectRepository,
        private CreateTeacherHandler $createTeacherHandler,
        private AssignTeacherToSubjectHandler $assignTeacherHandler,
        private UnassignTeacherFromSubjectHandler $unassignTeacherHandler
    ) {}

    public function index(): void
    {
        $teachers = $this->teacherRepository->findAll();
        $subjects = $this->subjectRepository->findAll();
        
        include __DIR__ . '/../../../../views/teacher/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $command = new CreateTeacherCommand(
                    TeacherId::generate()->value(),
                    $_POST['name'],
                    $_POST['email']
                );
                
                $this->createTeacherHandler->handle($command);
                
                header('Location: /teacher?success=Teacher created successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        include __DIR__ . '/../../../../views/teacher/create.php';
    }

    public function edit(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Teacher id is required';
            $this->index();
            return;
        }

        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            $error = 'Teacher not found';
            $this->index();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = trim((string) ($_POST['name'] ?? ''));
                $email = trim((string) ($_POST['email'] ?? ''));

                $existingTeacher = $this->teacherRepository->findByEmail($email);
                if (
                    $existingTeacher !== null
                    && $existingTeacher->id()->value() !== $teacher->id()->value()
                ) {
                    throw new \RuntimeException('A teacher with this email already exists');
                }

                $teacher->updateProfile($name, $email);
                $this->teacherRepository->save($teacher);

                header('Location: /teacher?success=Teacher updated successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        include __DIR__ . '/../../../../views/teacher/edit.php';
    }

    public function delete(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Teacher id is required';
            $this->index();
            return;
        }

        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            $error = 'Teacher not found';
            $this->index();
            return;
        }

        try {
            $this->teacherRepository->delete($teacher);
            header('Location: /teacher?success=Teacher deleted successfully');
            exit;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->index();
        }
    }

    public function assign(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $command = new AssignTeacherToSubjectCommand(
                    $_POST['teacherId'],
                    $_POST['subjectId']
                );
                
                $this->assignTeacherHandler->handle($command);
                
                header('Location: /teacher?success=Teacher assigned to subject successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $teachers = $this->teacherRepository->findAll();
        $subjects = $this->subjectRepository->findAll();
        
        include __DIR__ . '/../../../../views/teacher/assign.php';
    }

    public function unassign(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $error = 'Invalid request method';
            $this->index();
            return;
        }

        $teacherId = (string) ($_POST['teacherId'] ?? '');
        $subjectId = (string) ($_POST['subjectId'] ?? '');
        if ($teacherId === '') {
            $error = 'Teacher id is required';
            $this->index();
            return;
        }
        if ($subjectId === '') {
            $error = 'Subject id is required';
            $this->index();
            return;
        }

        $subject = $this->subjectRepository->find(new SubjectId($subjectId));
        if ($subject === null) {
            $error = 'Subject not found';
            $this->index();
            return;
        }

        try {
            $command = new UnassignTeacherFromSubjectCommand($teacherId, $subjectId);
            $this->unassignTeacherHandler->handle($command);

            header('Location: /teacher?success=Teacher unassigned from subject successfully');
            exit;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->index();
        }
    }
}
