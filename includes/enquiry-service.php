<?php
declare(strict_types=1);

function enquirySession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('tribeinn_enquiry');
    session_start(['use_strict_mode' => true, 'cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    header('Cache-Control: no-store');
}

function enquiryTokens(): array {
    $_SESSION['enquiry_csrf'] ??= bin2hex(random_bytes(32));
    $now = time();
    $_SESSION['enquiry_requests'] = array_filter($_SESSION['enquiry_requests'] ?? [], fn($entry) => $entry['created'] > $now - 86400);
    if (count($_SESSION['enquiry_requests']) >= 30) array_shift($_SESSION['enquiry_requests']);
    $id = bin2hex(random_bytes(24));
    $_SESSION['enquiry_requests'][$id] = ['created' => $now, 'sent' => false];
    return ['csrf' => $_SESSION['enquiry_csrf'], 'request_id' => $id];
}

function enquiryValidate(array $input, array $config, ?DateTimeImmutable $today = null): array {
    $data = [];
    $errors = [];
    foreach (['checkin'=>10,'checkout'=>10,'adults'=>2,'children'=>2,'name'=>120,'email'=>254,'phone'=>40,'purpose'=>80,'message'=>2000] as $key => $limit) {
        $raw = $input[$key] ?? '';
        if (!is_string($raw) || strlen($raw) > $limit * 4 || preg_match('//u', $raw) !== 1) {
            $errors[$key] = 'Please shorten this entry or use plain text.'; $raw = '';
        }
        $value = trim(strip_tags($raw));
        $value = preg_replace($key === 'message' ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u', '', $value);
        if (function_exists('mb_strlen') ? mb_strlen($value) > $limit : strlen($value) > $limit) $errors[$key] = 'Please shorten this entry.';
        // Email is used in Reply-To: reject line breaks rather than silently combining them.
        if ($key === 'email' && preg_match('/[\r\n]/', $raw)) $errors[$key] = 'Please enter a valid email address.';
        $data[$key] = $value;
    }
    $zone = new DateTimeZone($config['timezone']);
    $today ??= new DateTimeImmutable('today', $zone);
    $dates = [];
    foreach (['checkin'=>'check-in','checkout'=>'check-out'] as $key => $label) {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data[$key], $zone);
        if (!$date || $date->format('Y-m-d') !== $data[$key]) $errors[$key] = "Please choose a valid $label date.";
        else $dates[$key] = $date;
    }
    if (isset($dates['checkin']) && $dates['checkin'] < $today) $errors['checkin'] = 'Check-in cannot be in the past.';
    if (isset($dates['checkin'], $dates['checkout']) && $dates['checkout'] <= $dates['checkin']) $errors['checkout'] = 'Check-out needs to be after check-in.';
    foreach (['adults'=>1,'children'=>0] as $key => $minimum) {
        if (!ctype_digit($data[$key]) || (int)$data[$key] < $minimum || (int)$data[$key] > 99) $errors[$key] = $key === 'adults' ? 'Please enter at least one adult (up to 99).' : 'Please enter a number from 0 to 99.';
    }
    if ($data['name'] === '') $errors['name'] = 'Please tell us your name.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email so we can reply.';
    if ($data['phone'] !== '' && !preg_match('/^[+()\d .-]{5,40}$/', $data['phone'])) $errors['phone'] = 'Please use a phone number, including its country code if needed.';
    if (!in_array($data['purpose'], $config['purposes'], true)) $errors['purpose'] = 'Please choose what brings you to Goa.';
    return [$data, $errors];
}

function enquiryMessage(array $data, string $property): string {
    return implode("\n", [
        'TribeInn date request', '', 'Property: ' . $property,
        'Name: ' . $data['name'], 'Email: ' . $data['email'],
        ...($data['phone'] !== '' ? ['Phone: ' . $data['phone']] : []),
        '', 'Check-in: ' . $data['checkin'], 'Check-out: ' . $data['checkout'],
        'Adults: ' . $data['adults'], 'Children: ' . $data['children'],
        'Purpose: ' . $data['purpose'], '', 'Message:', $data['message'] ?: '(No additional message)',
        '', 'This is an enquiry only. Availability has not been confirmed.',
    ]);
}

function enquirySend(array $data, array $config, string $property): bool {
    if ($config['transport'] !== 'mail') return false;
    if (!filter_var($config['from'], FILTER_VALIDATE_EMAIL) || !filter_var($config['recipient'], FILTER_VALIDATE_EMAIL)) return false;
    $headers = ['From' => 'TribeInn <' . $config['from'] . '>', 'Reply-To' => $data['email'], 'MIME-Version' => '1.0', 'Content-Type' => 'text/plain; charset=UTF-8'];
    // mail() true means accepted by the configured mail transport, not verified inbox delivery.
    return @mail($config['recipient'], 'TribeInn date request - ' . $property, enquiryMessage($data, $property), $headers);
}

/** The session remains locked through transport acceptance, preventing concurrent duplicates. */
function enquirySubmit(array $input, array &$session, array $config, string $property, callable $send): array {
    $csrf = $input['csrf'] ?? null;
    $id = $input['request_id'] ?? null;
    if (!is_string($csrf) || !is_string($id) || !isset($session['enquiry_csrf'], $session['enquiry_requests'][$id]) || !hash_equals($session['enquiry_csrf'], $csrf) || $session['enquiry_requests'][$id]['created'] < time() - 86400) {
        return [403, ['ok'=>false,'message'=>'This form has expired. Please reload the page and try again.']];
    }
    if (($input['channel'] ?? '') !== 'email') return [400, ['ok'=>false,'message'=>'Please choose how you would like to contact us.']];
    if ($session['enquiry_requests'][$id]['sent']) return [200, ['ok'=>true]];
    if (!empty($input['website'])) return [422, ['ok'=>false,'message'=>'We couldn’t process this request. Please contact us by email.']];
    [$data, $errors] = enquiryValidate($input, $config);
    if ($errors) return [422, ['ok'=>false,'message'=>'Please check the highlighted details.','errors'=>$errors]];
    $attempts = array_values(array_filter($session['enquiry_attempts'] ?? [], fn($time) => $time > time() - 900));
    if (count($attempts) >= 5) return [429, ['ok'=>false,'message'=>'Please wait a little before trying again, or email contact@tribeinn.com directly.']];
    $attempts[] = time();
    $session['enquiry_attempts'] = $attempts;
    try { $sent = $send($data, $config, $property); } catch (Throwable $error) { $sent = false; }
    if (!$sent) return [503, ['ok'=>false,'message'=>'We couldn’t send your request just now. Your details are still here. Please try again or email contact@tribeinn.com directly.']];
    $session['enquiry_requests'][$id]['sent'] = true;
    return [200, ['ok'=>true]];
}
