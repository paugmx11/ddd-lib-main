<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;

require_once __DIR__ . '/../vendor/autoload.php';

if (
    !isset($_ENV['DATABASE_URL'])
    && !isset($_SERVER['DATABASE_URL'])
    && getenv('DATABASE_URL') === false
) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Obtener la URL de la base de datos
$databaseUrl = $_ENV['DATABASE_URL']
    ?? $_SERVER['DATABASE_URL']
    ?? getenv('DATABASE_URL')
    ?: 'sqlite:///:memory:';

// Analizar la URL de la base de datos
if (strpos($databaseUrl, 'sqlite') !== false) {
    $driver = 'pdo_sqlite';
    $params = ['driver' => $driver];
    if (strpos($databaseUrl, ':memory:') !== false) {
        $params['memory'] = true;
    } else {
        // Extraer la ruta del archivo SQLite
        $path = str_replace('sqlite://', '', $databaseUrl);
        $params['path'] = $path;
    }
} else {
    // Para otros drivers (mysql, postgres, etc.)
    $params = parse_url($databaseUrl);
    $driver = 'pdo_' . $params['scheme'];
    $params['driver'] = $driver;
    $params['user'] = $params['user'] ?? '';
    $params['password'] = $params['pass'] ?? '';
    $params['host'] = $params['host'] ?? '';
    $params['dbname'] = ltrim($params['path'], '/');
    unset($params['scheme'], $params['user'], $params['pass'], $params['host'], $params['path'], $params['fragment'], $params['query']);
}

// Configuración de Doctrine
$paths = [__DIR__ . '/../src/Domain'];
$isDevMode = true;

// Crear configuración de metadatos
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: $paths,
    isDevMode: $isDevMode
);

// Crear conexión
$connection = DriverManager::getConnection($params, $config);

// Crear EntityManager
$entityManager = new EntityManager($connection, $config);

// Para SQLite, generar esquema automáticamente en cada arranque.
if (($params['driver'] ?? null) === 'pdo_sqlite') {
    // Legacy migration: subjects previously had a single `teacher_id` column.
    // We now support multiple teachers via join table `subject_teachers`.
    // Create the join table early and migrate existing assignments before SchemaTool can drop columns.
    try {
        $connection->executeStatement(
            'CREATE TABLE IF NOT EXISTS subject_teachers (
                subject_id VARCHAR(36) NOT NULL,
                teacher_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(subject_id, teacher_id)
            )'
        );

        $columns = $connection->createSchemaManager()->listTableColumns('subjects');
        if (isset($columns['teacher_id'])) {
            $connection->executeStatement(
                "INSERT OR IGNORE INTO subject_teachers (subject_id, teacher_id)
                 SELECT id, teacher_id FROM subjects
                 WHERE teacher_id IS NOT NULL AND teacher_id != ''"
            );
        }
    } catch (\Throwable) {
        // Best-effort; SchemaTool will still run.
    }

    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
    if (!empty($metadata)) {
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadata);
    }
}

return $entityManager;
