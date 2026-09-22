<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Password arrives via stdin, not command-line history; only its hash is saved.
$path = getenv('TRIBE_AVAILABILITY_CONFIG') ?: __DIR__ . '/../var/availability.local.php';
$username = $argv[1] ?? '';
$password = rtrim(stream_get_contents(STDIN), "\r\n");
if (!preg_match('/^[a-zA-Z0-9._-]{3,120}$/D', $username) || strlen($password)<12 || strlen($password)>72) {
    fwrite(STDERR, "Use a 3–120 character username and a 12–72 byte password.\n"); exit(1);
}
try {
    $existing = is_file($path) ? require $path : [];
    if (!is_array($existing)) throw new RuntimeException();
    $existing['admin_user'] = $username;
    $existing['admin_password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    $password = '';
    $directory = dirname($path);
    if (!is_dir($directory) && !mkdir($directory, 0700, true)) throw new RuntimeException();
    $temporary = tempnam($directory, 'tribe-admin-');
    if (!$temporary || file_put_contents($temporary, "<?php\nreturn " . var_export($existing, true) . ";\n", LOCK_EX) === false) throw new RuntimeException();
    chmod($temporary, 0600);
    if (!rename($temporary, $path)) throw new RuntimeException();
    echo "Admin configured. Existing database settings preserved. Previous sessions will expire.\n";
} catch (Throwable $error) { fwrite(STDERR,"Could not save private configuration. Check permissions.\n"); exit(1); }
