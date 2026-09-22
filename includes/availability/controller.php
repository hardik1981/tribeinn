<?php
declare(strict_types=1);
require_once __DIR__ . '/Availability.php';
$availabilityConfig = availabilityConfig();
$today = availabilityToday($availabilityConfig);
if ($route === 'api/availability') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') { header('Allow: GET'); http_response_code(405); echo json_encode(['message'=>'Use GET.']); exit; }
    [$status, $data] = availabilityPublic($availabilityConfig, $_GET);
    http_response_code($status); echo json_encode($data); exit;
}
require_once __DIR__ . '/AdminSecurity.php';
availabilityAdminSession($availabilityConfig);
$error = ''; $notice = $_SESSION['admin_notice'] ?? ''; unset($_SESSION['admin_notice']);
$unit = $availabilityConfig['unit'];
$unitName = $properties[$unit]['name'] ?? $unit;
$redirect = static function (): never { header('Location: ' . url('admin/calendar'), true, 303); exit; };
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 8192) { http_response_code(413); $error = 'Please shorten this request.'; }
    elseif (!availabilityCsrf($_POST, $_SESSION)) { http_response_code(403); $error = 'This form has expired. Reload the page and try again.'; }
    elseif (($_POST['action'] ?? '') === 'login') {
        $user = is_string($_POST['username'] ?? null) ? $_POST['username'] : '';
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        if (availabilityLogin($availabilityConfig, $user, $password, $_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
            session_regenerate_id(true);
            $_SESSION = ['admin_authenticated'=>true, 'admin_expires'=>time()+1800, 'admin_csrf'=>bin2hex(random_bytes(32)), 'admin_fingerprint'=>hash('sha256', $availabilityConfig['admin_user'] . $availabilityConfig['admin_password_hash'])];
            $redirect();
        }
        http_response_code(401); $error = 'Unable to sign in. Check your details, or wait 15 minutes after repeated attempts.';
    } elseif (empty($_SESSION['admin_authenticated'])) { http_response_code(401); $error = 'Please sign in to manage dates.'; }
    elseif (($_POST['action'] ?? '') === 'logout') {
        $_SESSION = []; session_destroy();
        setcookie(session_name(), '', ['expires'=>time()-3600, 'path'=>'/', 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'httponly'=>true, 'samesite'=>'Strict']);
        $redirect();
    } else {
        try {
            $repository = availabilityRepository($availabilityConfig);
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) ?: null;
            if (($_POST['action'] ?? '') === 'remove' && $id && ($_POST['confirm'] ?? '') === 'yes') {
                $repository->remove($unit, $id); $_SESSION['admin_notice'] = 'Manual block removed. Other overlapping blocks still apply.';
            } elseif (($_POST['action'] ?? '') === 'save') {
                foreach (['start','end','reason'] as $field) if (!is_string($_POST[$field] ?? null)) throw new InvalidArgumentException('Please complete the date fields.');
                if (($_POST['id'] ?? '') !== '' && !$id) throw new InvalidArgumentException('Invalid block.');
                $repository->save($unit, $id, $_POST['start'], $_POST['end'], trim($_POST['reason']), $today);
                $_SESSION['admin_month'] = substr(max($_POST['start'], $today), 0, 7);
                $_SESSION['admin_notice'] = $id ? 'Manual block updated.' : 'Dates blocked.';
            } else throw new InvalidArgumentException('Confirm removal or choose a valid action.');
            $redirect();
        } catch (InvalidArgumentException $exception) { http_response_code(422); $error = $exception->getMessage(); }
        catch (Throwable $exception) { http_response_code(503); $error = 'Availability is unavailable. No change was confirmed. Check the database configuration and try again.'; }
    }
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); header('Allow: GET, POST'); $error = 'Use the calendar forms.'; }
$authenticated = !empty($_SESSION['admin_authenticated']);
$blocks = []; $ranges = null; $edit = null;
if ($authenticated) {
    try {
        $repository = availabilityRepository($availabilityConfig);
        $unitName = $repository->unit($unit)['name'];
        $blocks = $repository->manualBlocks($unit, $today);
        $ranges = (new AvailabilityService([$repository]))->ranges($unit, $today);
        foreach ($blocks as $block) if ((string)$block['id'] === ($_GET['edit'] ?? null)) $edit = $block;
        if (isset($_GET['edit']) && !$edit) { http_response_code(404); $error = 'This upcoming manual block was not found.'; }
    } catch (Throwable $exception) { http_response_code(503); $error = 'Availability could not be loaded. Check the database configuration.'; }
}
// Keep valid text after a rejected form submission, without trusting hidden IDs.
if ($authenticated && $error && ($_POST['action'] ?? '') === 'save') {
    $edit = [
        'id'=>filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) ?: '',
        'start_date'=>is_string($_POST['start'] ?? null) ? $_POST['start'] : '',
        'end_date'=>is_string($_POST['end'] ?? null) ? $_POST['end'] : '',
        'reason'=>is_string($_POST['reason'] ?? null) ? $_POST['reason'] : '',
    ];
}
$pageTitle = 'Availability admin'; $isInner = true; $isExperience = false; $found = true;
require __DIR__ . '/../header.php';
require __DIR__ . '/../../pages/admin-calendar.php';
require __DIR__ . '/../footer.php';
exit;
