<?php
declare(strict_types=1);
// Optional private PHP config is outside the public document root on production.
// Local var/ is denied by both routers and excluded from Git.
$privatePath = getenv('TRIBE_AVAILABILITY_CONFIG') ?: __DIR__ . '/../var/availability.local.php';
$private = is_file($privatePath) ? require $privatePath : [];
return array_replace([
    'dsn' => getenv('TRIBE_DB_DSN') ?: '',
    'db_user' => getenv('TRIBE_DB_USER') ?: '',
    'db_password' => getenv('TRIBE_DB_PASSWORD') ?: '',
    'admin_user' => getenv('TRIBE_ADMIN_USER') ?: '',
    'admin_password_hash' => getenv('TRIBE_ADMIN_PASSWORD_HASH') ?: '',
    'timezone' => 'Asia/Kolkata',
    'unit' => 'the-beginning',
    'security_dir' => __DIR__ . '/../var/admin-security',
], is_array($private) ? $private : []);
