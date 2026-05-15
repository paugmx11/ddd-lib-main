<?php

declare(strict_types=1);

return [
    ['GET', '#^/api/courses$#', 'courseApi.index'],
    ['POST', '#^/api/courses$#', 'courseApi.store'],
    ['GET', '#^/api/courses/([^/]+)$#', 'courseApi.show'],
    ['PUT', '#^/api/courses/([^/]+)$#', 'courseApi.update'],
    ['PATCH', '#^/api/courses/([^/]+)$#', 'courseApi.update'],
    ['DELETE', '#^/api/courses/([^/]+)$#', 'courseApi.destroy'],
];
