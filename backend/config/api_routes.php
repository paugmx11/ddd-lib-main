<?php

declare(strict_types=1);

return [
    ['POST', '#^/api/auth/register$#', 'authApi.register'],
    ['POST', '#^/api/auth/login$#', 'authApi.login'],
    ['POST', '#^/api/auth/logout$#', 'authApi.logout'],

    ['GET', '#^/api/courses$#', 'courseApi.index'],
    ['POST', '#^/api/courses$#', 'courseApi.store'],
    ['GET', '#^/api/courses/([^/]+)$#', 'courseApi.show'],
    ['PUT', '#^/api/courses/([^/]+)$#', 'courseApi.update'],
    ['PATCH', '#^/api/courses/([^/]+)$#', 'courseApi.update'],
    ['DELETE', '#^/api/courses/([^/]+)$#', 'courseApi.destroy'],

    ['GET', '#^/api/students$#', 'studentApi.index'],
    ['POST', '#^/api/students$#', 'studentApi.store'],
    ['GET', '#^/api/students/([^/]+)$#', 'studentApi.show'],
    ['PUT', '#^/api/students/([^/]+)$#', 'studentApi.update'],
    ['PATCH', '#^/api/students/([^/]+)$#', 'studentApi.update'],
    ['DELETE', '#^/api/students/([^/]+)$#', 'studentApi.destroy'],
    ['POST', '#^/api/students/([^/]+)/enroll$#', 'studentApi.enroll'],

    ['GET', '#^/api/teachers$#', 'teacherApi.index'],
    ['POST', '#^/api/teachers$#', 'teacherApi.store'],
    ['GET', '#^/api/teachers/([^/]+)$#', 'teacherApi.show'],
    ['PUT', '#^/api/teachers/([^/]+)$#', 'teacherApi.update'],
    ['PATCH', '#^/api/teachers/([^/]+)$#', 'teacherApi.update'],
    ['DELETE', '#^/api/teachers/([^/]+)$#', 'teacherApi.destroy'],
    ['POST', '#^/api/teachers/([^/]+)/assign$#', 'teacherApi.assign'],
    ['POST', '#^/api/teachers/([^/]+)/unassign$#', 'teacherApi.unassign'],

    ['GET', '#^/api/subjects$#', 'subjectApi.index'],
    ['POST', '#^/api/subjects$#', 'subjectApi.store'],
    ['GET', '#^/api/subjects/([^/]+)$#', 'subjectApi.show'],
    ['PUT', '#^/api/subjects/([^/]+)$#', 'subjectApi.update'],
    ['PATCH', '#^/api/subjects/([^/]+)$#', 'subjectApi.update'],
    ['DELETE', '#^/api/subjects/([^/]+)$#', 'subjectApi.destroy'],
];
