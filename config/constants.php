<?php

// Constantes de la aplicación

// Rutas
define('BASE_PATH', __DIR__ . '/..');

// Configuración de la aplicación
define('APP_NAME', 'School Management DDD');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Configuración de la base de datos
define('DB_DRIVER', 'pdo_sqlite');
define('DB_PATH', __DIR__ . '/../database.sqlite');

