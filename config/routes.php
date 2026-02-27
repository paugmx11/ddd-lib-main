<?php

declare(strict_types=1);

return [
    '/' => 'home',
    '/home' => 'home',

    '/student' => 'student.index',
    '/student/create' => 'student.create',
    '/student/edit' => 'student.edit',
    '/student/delete' => 'student.delete',
    '/student/enroll' => 'student.enroll',

    '/teacher' => 'teacher.index',
    '/teacher/create' => 'teacher.create',
    '/teacher/edit' => 'teacher.edit',
    '/teacher/delete' => 'teacher.delete',
    '/teacher/assign' => 'teacher.assign',
    '/teacher/unassign' => 'teacher.unassign',

    '/course' => 'course.index',
    '/course/create' => 'course.create',
    '/course/edit' => 'course.edit',
    '/course/delete' => 'course.delete',

    '/subject' => 'subject.index',
    '/subject/create' => 'subject.create',
    '/subject/edit' => 'subject.edit',
    '/subject/delete' => 'subject.delete',
];
