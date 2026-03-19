<?php

declare(strict_types=1);

namespace App\Http;

use App\Application\CreateStudent\CreateStudentCommand;
use App\Application\CreateCourse\CreateCourseCommand;
use App\Application\CreateCourse\CreateCourseHandler;
use App\Application\CreateTeacher\CreateTeacherCommand;
use App\Application\CreateSubject\CreateSubjectCommand;
use App\Application\EnrollStudent\EnrollStudentCommand;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectCommand;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectCommand;
use App\Domain\Enrollment\Enrollment;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;
use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;
use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Subject\SubjectRepository;
use App\Domain\Teacher\TeacherRepository;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Application\EnrollStudent\EnrollStudentHandler;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\UnassignTeacherFromSubject\UnassignTeacherFromSubjectHandler;

final class ApiController
{
    public function __construct(
        private StudentRepository $studentRepository,
        private TeacherRepository $teacherRepository,
        private SubjectRepository $subjectRepository,
        private CourseRepository $courseRepository,
        private CreateStudentHandler $createStudentHandler,
        private CreateTeacherHandler $createTeacherHandler,
        private CreateSubjectHandler $createSubjectHandler,
        private CreateCourseHandler $createCourseHandler,
        private EnrollStudentHandler $enrollStudentHandler,
        private AssignTeacherToSubjectHandler $assignTeacherHandler,
        private UnassignTeacherFromSubjectHandler $unassignTeacherHandler
    ) {}

    public function listStudents(): array
    {
        return JsonResponse::ok([
            'data' => array_map(
                fn (Student $student): array => $this->serializeStudent($student),
                $this->studentRepository->findAll()
            ),
        ]);
    }

    public function getStudent(string $id): array
    {
        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            return JsonResponse::error(404, 'Student not found');
        }

        return JsonResponse::ok(['data' => $this->serializeStudent($student)]);
    }

    public function createStudent(array $payload): array
    {
        try {
            $command = new CreateStudentCommand(
                StudentId::generate()->value(),
                $this->requiredString($payload, 'name'),
                $this->requiredString($payload, 'email')
            );

            $this->createStudentHandler->handle($command);

            $student = $this->studentRepository->find(new StudentId($command->studentId));

            return JsonResponse::created(['data' => $this->serializeStudent($student)]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function updateStudent(string $id, array $payload): array
    {
        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            return JsonResponse::error(404, 'Student not found');
        }

        try {
            $name = $this->requiredString($payload, 'name');
            $email = $this->requiredString($payload, 'email');

            $existingStudent = $this->studentRepository->findByEmail($email);
            if (
                $existingStudent !== null
                && $existingStudent->id()->value() !== $student->id()->value()
            ) {
                return JsonResponse::error(409, 'A student with this email already exists');
            }

            $student->updateProfile($name, $email);
            $this->studentRepository->save($student);

            return JsonResponse::ok(['data' => $this->serializeStudent($student)]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function deleteStudent(string $id): array
    {
        $student = $this->studentRepository->find(new StudentId($id));
        if ($student === null) {
            return JsonResponse::error(404, 'Student not found');
        }

        $this->studentRepository->delete($student);

        return JsonResponse::noContent();
    }

    public function enrollStudent(string $id, array $payload): array
    {
        if ($this->studentRepository->find(new StudentId($id)) === null) {
            return JsonResponse::error(404, 'Student not found');
        }

        try {
            $courseId = $this->requiredString($payload, 'courseId');
            $this->enrollStudentHandler->handle(new EnrollStudentCommand($id, $courseId));

            return $this->getStudent($id);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function listTeachers(): array
    {
        return JsonResponse::ok([
            'data' => array_map(
                fn (Teacher $teacher): array => $this->serializeTeacher($teacher),
                $this->teacherRepository->findAll()
            ),
        ]);
    }

    public function getTeacher(string $id): array
    {
        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            return JsonResponse::error(404, 'Teacher not found');
        }

        return JsonResponse::ok(['data' => $this->serializeTeacher($teacher)]);
    }

    public function createTeacher(array $payload): array
    {
        try {
            $command = new CreateTeacherCommand(
                TeacherId::generate()->value(),
                $this->requiredString($payload, 'name'),
                $this->requiredString($payload, 'email')
            );

            $this->createTeacherHandler->handle($command);

            $teacher = $this->teacherRepository->find(new TeacherId($command->teacherId));

            return JsonResponse::created(['data' => $this->serializeTeacher($teacher)]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function updateTeacher(string $id, array $payload): array
    {
        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            return JsonResponse::error(404, 'Teacher not found');
        }

        try {
            $name = $this->requiredString($payload, 'name');
            $email = $this->requiredString($payload, 'email');

            $existingTeacher = $this->teacherRepository->findByEmail($email);
            if (
                $existingTeacher !== null
                && $existingTeacher->id()->value() !== $teacher->id()->value()
            ) {
                return JsonResponse::error(409, 'A teacher with this email already exists');
            }

            $teacher->updateProfile($name, $email);
            $this->teacherRepository->save($teacher);

            return JsonResponse::ok(['data' => $this->serializeTeacher($teacher)]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function deleteTeacher(string $id): array
    {
        $teacher = $this->teacherRepository->find(new TeacherId($id));
        if ($teacher === null) {
            return JsonResponse::error(404, 'Teacher not found');
        }

        foreach ($this->subjectRepository->findByTeacher($id) as $subject) {
            $subject->removeTeacher();
            $this->subjectRepository->save($subject);
        }

        $this->teacherRepository->delete($teacher);

        return JsonResponse::noContent();
    }

    public function listSubjects(): array
    {
        return JsonResponse::ok([
            'data' => array_map(
                fn (Subject $subject): array => $this->serializeSubject($subject),
                $this->subjectRepository->findAll()
            ),
        ]);
    }

    public function getSubject(string $id): array
    {
        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            return JsonResponse::error(404, 'Subject not found');
        }

        return JsonResponse::ok(['data' => $this->serializeSubject($subject)]);
    }

    public function createSubject(array $payload): array
    {
        try {
            $command = new CreateSubjectCommand(
                SubjectId::generate()->value(),
                $this->requiredString($payload, 'name'),
                $this->requiredString($payload, 'courseId')
            );

            $this->createSubjectHandler->handle($command);

            $subject = $this->subjectRepository->find(new SubjectId($command->subjectId));

            return JsonResponse::created(['data' => $this->serializeSubject($subject)]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function updateSubject(string $id, array $payload): array
    {
        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            return JsonResponse::error(404, 'Subject not found');
        }

        try {
            $name = $this->requiredString($payload, 'name');
            $courseId = $this->requiredString($payload, 'courseId');
            $course = $this->courseRepository->find(new CourseId($courseId));

            if ($course === null) {
                return JsonResponse::error(404, 'Course not found');
            }

            foreach ($this->subjectRepository->findByCourse($courseId) as $existingSubject) {
                if (
                    $existingSubject->name() === $name
                    && $existingSubject->id()->value() !== $subject->id()->value()
                ) {
                    return JsonResponse::error(409, 'A subject with this name already exists in this course');
                }
            }

            $subject->update($name, $course);
            $this->subjectRepository->save($subject);

            return JsonResponse::ok(['data' => $this->serializeSubject($subject)]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function deleteSubject(string $id): array
    {
        $subject = $this->subjectRepository->find(new SubjectId($id));
        if ($subject === null) {
            return JsonResponse::error(404, 'Subject not found');
        }

        $this->subjectRepository->delete($subject);

        return JsonResponse::noContent();
    }

    public function assignTeacherToSubject(string $subjectId, array $payload): array
    {
        try {
            $teacherId = $this->requiredString($payload, 'teacherId');
            $this->assignTeacherHandler->handle(
                new AssignTeacherToSubjectCommand($teacherId, $subjectId)
            );

            return $this->getSubject($subjectId);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function unassignTeacherFromSubject(string $subjectId): array
    {
        $subject = $this->subjectRepository->find(new SubjectId($subjectId));
        if ($subject === null) {
            return JsonResponse::error(404, 'Subject not found');
        }

        $teacherId = $subject->teacherId()?->value();
        if ($teacherId === null) {
            return JsonResponse::error(409, 'Subject has no teacher assigned');
        }

        try {
            $this->unassignTeacherHandler->handle(
                new UnassignTeacherFromSubjectCommand($teacherId, $subjectId)
            );

            return JsonResponse::noContent();
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    public function listCourses(): array
    {
        return JsonResponse::ok([
            'data' => array_map(
                fn (Course $course): array => $this->serializeCourse($course),
                $this->courseRepository->findAll()
            ),
        ]);
    }

    public function createCourse(array $payload): array
    {
        try {
            $command = new CreateCourseCommand(
                CourseId::generate()->value(),
                $this->requiredString($payload, 'name'),
                $this->requiredString($payload, 'startDate'),
                $this->requiredString($payload, 'endDate'),
                $this->optionalString($payload, 'description')
            );

            $this->createCourseHandler->handle($command);

            return JsonResponse::created([
                'data' => $this->serializeCourse(
                    $this->courseRepository->find(new CourseId($command->courseId))
                ),
            ]);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    private function requiredString(array $payload, string $field): string
    {
        $value = $payload[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Field "%s" is required', $field));
        }

        return trim($value);
    }

    private function optionalString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a string', $field));
        }

        return trim($value);
    }

    private function handleException(\Throwable $exception): array
    {
        $message = $exception->getMessage();
        $normalizedMessage = strtolower($message);

        if (str_contains($normalizedMessage, 'not found')) {
            return JsonResponse::error(404, $message);
        }

        if (str_contains($normalizedMessage, 'already exists')) {
            return JsonResponse::error(409, $message);
        }

        if (str_contains($normalizedMessage, 'already enrolled')) {
            return JsonResponse::error(409, $message);
        }

        if (
            str_contains($normalizedMessage, 'already has a teacher')
            || str_contains($normalizedMessage, 'has no teacher assigned')
        ) {
            return JsonResponse::error(409, $message);
        }

        return JsonResponse::error(400, $message);
    }

    private function serializeStudent(?Student $student): array
    {
        if ($student === null) {
            throw new \RuntimeException('Student not found');
        }

        $enrollments = [];
        foreach ($student->enrollments() as $enrollment) {
            if ($enrollment instanceof Enrollment) {
                $enrollments[] = [
                    'id' => $enrollment->id()->value(),
                    'courseId' => $enrollment->courseId()->value(),
                    'status' => $enrollment->status(),
                    'enrolledAt' => $enrollment->enrolledAt()->format(DATE_ATOM),
                ];
            }
        }

        return [
            'id' => $student->id()->value(),
            'name' => $student->name(),
            'email' => $student->email(),
            'enrollments' => $enrollments,
        ];
    }

    private function serializeTeacher(?Teacher $teacher): array
    {
        if ($teacher === null) {
            throw new \RuntimeException('Teacher not found');
        }

        $subjects = [];
        foreach ($teacher->subjects() as $subject) {
            $subjects[] = [
                'id' => $subject->id()->value(),
                'name' => $subject->name(),
            ];
        }

        return [
            'id' => $teacher->id()->value(),
            'name' => $teacher->name(),
            'email' => $teacher->email(),
            'subjects' => $subjects,
        ];
    }

    private function serializeSubject(?Subject $subject): array
    {
        if ($subject === null) {
            throw new \RuntimeException('Subject not found');
        }

        return [
            'id' => $subject->id()->value(),
            'name' => $subject->name(),
            'course' => [
                'id' => $subject->course()->id()->value(),
                'name' => $subject->course()->name(),
            ],
            'teacher' => $subject->teacher() === null ? null : [
                'id' => $subject->teacher()->id()->value(),
                'name' => $subject->teacher()->name(),
                'email' => $subject->teacher()->email(),
            ],
        ];
    }

    private function serializeCourse(?Course $course): array
    {
        if ($course === null) {
            throw new \RuntimeException('Course not found');
        }

        return [
            'id' => $course->id()->value(),
            'name' => $course->name(),
            'description' => $course->description(),
            'startDate' => $course->startDate()->format('Y-m-d'),
            'endDate' => $course->endDate()->format('Y-m-d'),
            'isActive' => $course->isActive(),
        ];
    }
}
