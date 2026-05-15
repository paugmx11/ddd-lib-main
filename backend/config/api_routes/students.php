<?php

declare(strict_types=1);

return [
    ['GET', '#^/api/students$#', 'studentApi.index'],
    ['POST', '#^/api/students$#', 'studentApi.store'],
    ['GET', '#^/api/students/([^/]+)$#', 'studentApi.show'],
    ['PUT', '#^/api/students/([^/]+)$#', 'studentApi.update'],
    ['PATCH', '#^/api/students/([^/]+)$#', 'studentApi.update'],
    ['DELETE', '#^/api/students/([^/]+)$#', 'studentApi.destroy'],
    ['POST', '#^/api/students/([^/]+)/enroll$#', 'studentApi.enroll'],
];
