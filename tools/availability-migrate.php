<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Use migration credentials (DDL privileges), never the public application account.
try {
    $dsn = getenv('TRIBE_MIGRATION_DSN') ?: '';
    if (!str_starts_with($dsn, 'mysql:')) throw new RuntimeException();
    $pdo = new PDO($dsn, getenv('TRIBE_MIGRATION_USER') ?: '', getenv('TRIBE_MIGRATION_PASSWORD') ?: '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $pdo->exec(file_get_contents(__DIR__ . '/../database/001-availability.sql'));
    echo "Availability schema installed. Existing blocks are preserved.\n";
} catch (Throwable $error) {
    fwrite(STDERR, "Migration failed. Check the migration environment and MySQL grants. No credentials or database errors are displayed.\n");
    exit(1);
}
