<?php

declare(strict_types=1);

return [
    ['GET', '#^/api/subjects$#', 'subjectApi.index'],
    ['POST', '#^/api/subjects$#', 'subjectApi.store'],
    ['GET', '#^/api/subjects/([^/]+)$#', 'subjectApi.show'],
    ['PUT', '#^/api/subjects/([^/]+)$#', 'subjectApi.update'],
    ['PATCH', '#^/api/subjects/([^/]+)$#', 'subjectApi.update'],
    ['DELETE', '#^/api/subjects/([^/]+)$#', 'subjectApi.destroy'],
];
