<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller;

use App\Application\CreateSubject\CreateSubjectCommand;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Domain\Course\CourseId;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;
use App\Domain\Course\CourseRepository;

final class SubjectController
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private CourseRepository $courseRepository,
        private CreateSubjectHandler $createSubjectHandler
    ) {}

    public function index(): void
    {
        $subjects = $this->subjectRepository->findAll();
        $courses = $this->courseRepository->findAll();
        
        include __DIR__ . '/../../../../views/subject/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $command = new CreateSubjectCommand(
                    SubjectId::generate()->value(),
                    $_POST['name'],
                    $_POST['courseId']
                );
                
                $this->createSubjectHandler->handle($command);
                
                header('Location: /subject?success=Subject created successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $courses = $this->courseRepository->findAll();
        
        include __DIR__ . '/../../../../views/subject/create.php';
    }

    public function edit(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Subject id is required';
            $this->index();
            return;
        }

        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            $error = 'Subject not found';
            $this->index();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = trim((string) ($_POST['name'] ?? ''));
                $courseId = (string) ($_POST['courseId'] ?? '');

                $course = $this->courseRepository->find(new CourseId($courseId));
                if ($course === null) {
                    throw new \RuntimeException('Course not found');
                }

                $existingSubjects = $this->subjectRepository->findByCourse($courseId);
                foreach ($existingSubjects as $existingSubject) {
                    if (
                        $existingSubject->name() === $name
                        && $existingSubject->id()->value() !== $subject->id()->value()
                    ) {
                        throw new \RuntimeException('A subject with this name already exists in this course');
                    }
                }

                $subject->update($name, $course);
                $this->subjectRepository->save($subject);

                header('Location: /subject?success=Subject updated successfully');
                exit;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $courses = $this->courseRepository->findAll();

        include __DIR__ . '/../../../../views/subject/edit.php';
    }

    public function delete(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        if ($id === '') {
            $error = 'Subject id is required';
            $this->index();
            return;
        }

        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            $error = 'Subject not found';
            $this->index();
            return;
        }

        try {
            $this->subjectRepository->delete($subject);
            header('Location: /subject?success=Subject deleted successfully');
            exit;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->index();
        }
    }
}
