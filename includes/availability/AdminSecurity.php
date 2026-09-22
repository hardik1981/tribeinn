<?php
declare(strict_types=1);
function availabilityAdminSession(array $config): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('tribeinn_admin');
        session_start(['use_strict_mode'=>true, 'use_only_cookies'=>true, 'cookie_httponly'=>true, 'cookie_samesite'=>'Strict', 'cookie_secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    }
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header("Content-Security-Policy: frame-ancestors 'none'; form-action 'self'; base-uri 'self'");
    header('Referrer-Policy: same-origin');
    $fingerprint = hash('sha256', $config['admin_user'] . $config['admin_password_hash']);
    if (($_SESSION['admin_expires'] ?? 0) < time() || !hash_equals($fingerprint, $_SESSION['admin_fingerprint'] ?? '')) unset($_SESSION['admin_authenticated']);
    if (!empty($_SESSION['admin_authenticated'])) $_SESSION['admin_expires'] = time() + 1800;
    $_SESSION['admin_csrf'] ??= bin2hex(random_bytes(32));
}
function availabilityCsrf(array $input, array $session): bool {
    return isset($input['csrf'], $session['admin_csrf']) && is_string($input['csrf']) && hash_equals($session['admin_csrf'], $input['csrf']);
}
/** File-backed rate limit survives new sessions. Fails closed if storage is unavailable. */
function availabilityLogin(array $config, string $user, string $password, string $address): bool {
    $dir = $config['security_dir'];
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) return false;
    $file = @fopen($dir . '/' . hash('sha256', $address) . '.json', 'c+');
    if (!$file) return false;
    if (!flock($file, LOCK_EX)) { fclose($file); return false; }
    try {
        $attempts = json_decode(stream_get_contents($file), true) ?: [];
        if (!is_array($attempts)) return false;
        $attempts = array_values(array_filter($attempts, fn($at) => is_int($at) && $at > time() - 900));
        if (count($attempts) >= 5) return false;
        $attempts[] = time();
        $hash = $config['admin_password_hash'];
        $ok = strlen($password) <= 200 && strlen($user) <= 120 && $hash !== '' && password_verify($password, $hash) && $config['admin_user'] !== '' && hash_equals($config['admin_user'], $user);
        rewind($file);
        if (!ftruncate($file, 0) || fwrite($file, json_encode($ok ? [] : $attempts)) === false || !fflush($file)) return false;
        return $ok;
    } finally { flock($file, LOCK_UN); fclose($file); }
}
