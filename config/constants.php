<?php

// Constantes de la aplicación

// Rutas
defined('BASE_PATH') || define('BASE_PATH', __DIR__ . '/..');

// Configuración de la aplicación
defined('APP_NAME') || define('APP_NAME', 'School Management DDD');
defined('APP_ENV') || define('APP_ENV', getenv('APP_ENV') ?: 'development');
defined('APP_DEBUG') || define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Configuración de la base de datos
defined('DB_DRIVER') || define('DB_DRIVER', 'pdo_sqlite');
defined('DB_PATH') || define('DB_PATH', __DIR__ . '/../database.sqlite');
