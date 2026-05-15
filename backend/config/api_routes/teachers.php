<?php

declare(strict_types=1);

return [
    ['GET', '#^/api/teachers$#', 'teacherApi.index'],
    ['POST', '#^/api/teachers$#', 'teacherApi.store'],
    ['GET', '#^/api/teachers/([^/]+)$#', 'teacherApi.show'],
    ['PUT', '#^/api/teachers/([^/]+)$#', 'teacherApi.update'],
    ['PATCH', '#^/api/teachers/([^/]+)$#', 'teacherApi.update'],
    ['DELETE', '#^/api/teachers/([^/]+)$#', 'teacherApi.destroy'],
    ['POST', '#^/api/teachers/([^/]+)/assign$#', 'teacherApi.assign'],
    ['POST', '#^/api/teachers/([^/]+)/unassign$#', 'teacherApi.unassign'],
];
