<?php
declare(strict_types=1);
require __DIR__ . '/../includes/availability/Availability.php';
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function url(string $value): string { return '/' . $value; }
$authenticated = true; $unitName = 'The Beginning'; $error = $notice = '';
$today = '2026-10-02'; $ranges = []; $_SESSION = ['admin_csrf'=>'fixture'];
$count = 0;
foreach ([['2026-09-25','2026-10-07','25 Sept 2026 → 7 Oct 2026','12 nights'],['2026-10-10','2026-10-15','10 Oct 2026 → 15 Oct 2026','5 nights'],['2026-10-10','2026-10-11','10 Oct 2026 → 11 Oct 2026','1 night']] as [$start,$end,$range,$label]) {
    $edit = ['id'=>1,'start_date'=>$start,'end_date'=>$end,'reason'=>'']; $blocks = [$edit];
    ob_start(); require __DIR__ . '/../pages/admin-calendar.php'; $html = ob_get_clean();
    foreach ([$range.' · '.$label,'Total: '.$label,'Checkout date is not occupied.','value="'.$start.'"','value="'.$end.'"'] as $expected) {
        if (!str_contains($html,$expected)) throw new RuntimeException($expected);
        $count++;
    }
}
$edit = []; $blocks = [];
ob_start(); require __DIR__ . '/../pages/admin-calendar.php'; $html = ob_get_clean();
if (!str_contains($html,'id="block-total" role="status" hidden')) throw new RuntimeException('Empty total should be hidden');
$count++;
echo "PASS: $count admin display checks; no database access or mutations.\n";
