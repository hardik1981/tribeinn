<?php
declare(strict_types=1);

// Server-side settings only. Set environment values in the Hostinger PHP environment.
// Keep disabled until the site's mail transport is configured; never report a false success.
return [
    'recipient' => 'contact@tribeinn.com',
    'from' => getenv('TRIBE_MAIL_FROM') ?: 'contact@tribeinn.com',
    'transport' => getenv('TRIBE_MAIL_TRANSPORT') ?: 'disabled', // disabled | mail
    'whatsapp_number' => getenv('TRIBE_WHATSAPP_NUMBER') ?: '', // International digits, no + or spaces.
    'timezone' => 'Asia/Kolkata',
    'purposes' => ['Holiday', 'Workation', 'Long stay', 'Creating something', 'Visiting family/friends', 'Something else'],
];
