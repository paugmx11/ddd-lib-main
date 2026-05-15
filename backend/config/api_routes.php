<?php

declare(strict_types=1);

// Load per-resource route definitions and merge them into a single array
$routes = [];
$files = [
    __DIR__ . '/api_routes/auth.php',
    __DIR__ . '/api_routes/courses.php',
    __DIR__ . '/api_routes/students.php',
    __DIR__ . '/api_routes/teachers.php',
    __DIR__ . '/api_routes/subjects.php',
];

foreach ($files as $file) {
    if (is_file($file)) {
        $part = require $file;
        if (is_array($part)) {
            $routes = array_merge($routes, $part);
        }
    }
}

return $routes;

