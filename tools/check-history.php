<?php
declare(strict_types=1);
require __DIR__ . '/../includes/availability/Availability.php';
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function url(string $v): string { return '/' . $v; }
$today='2026-11-09'; $authenticated=true; $unitName='History test'; $error=$notice=''; $edit=null; $_SESSION=['admin_csrf'=>'fixture'];
$blocks=[['id'=>1,'start_date'=>'2025-12-28','end_date'=>'2026-01-03','reason'=>'First private fixture'],['id'=>2,'start_date'=>'2026-01-01','end_date'=>'2026-01-05','reason'=>'Second private fixture']];
$ranges=array_map(fn($b)=>['start'=>$b['start_date'],'end'=>$b['end_date']],$blocks);
ob_start(); require __DIR__.'/../pages/admin-calendar.php'; $html=ob_get_clean();
$count=0;
foreach (['First private fixture','Second private fixture','Historical record','Confirm removal','data-block-start="2025-12-28"'] as $text) { if(!str_contains($html,$text)) throw new RuntimeException($text); $count++; }
if(str_contains($html,'?edit=1')) throw new RuntimeException('Expired edit should not be offered'); $count++;
$authenticated=false; ob_start(); require __DIR__.'/../pages/admin-calendar.php'; $anonymous=ob_get_clean();
if(str_contains($anonymous,'private fixture')) throw new RuntimeException('Private data exposed'); $count++;
$provider=new class($ranges) implements AvailabilityProvider {
    public function __construct(private array $data) {}
    public function ranges(string $unit,string $today): array { return $this->data; }
};
$service=new AvailabilityService([$provider]);
if($service->ranges('test','1000-01-01')!==[['start'=>'2025-12-28','end'=>'2026-01-05']]) throw new RuntimeException('Historical overlaps'); $count++;
if($service->ranges('test',$today)!==[]) throw new RuntimeException('Public history filtering'); $count++;
if($service->check('test','2026-01-05','2026-01-06',$today)===null) throw new RuntimeException('Public past arrival'); $count++;
if(AvailabilityDates::overlaps('2026-01-05','2026-01-06',['start'=>'2026-01-01','end'=>'2026-01-05'])) throw new RuntimeException('Checkout boundary'); $count++;
if(in_array('--preview',$argv,true)) {
    $dir=__DIR__.'/../var/history-preview'; if(!is_dir($dir)) mkdir($dir,0700,true);
    file_put_contents($dir.'/index.html','<!doctype html><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="http://127.0.0.1:8080/assets/css/site.css"><link rel="stylesheet" href="http://127.0.0.1:8080/assets/css/availability.css">'.$html.'<script src="http://127.0.0.1:8080/assets/js/availability.js"></script>');
}
echo "PASS: $count history, privacy and overlap checks; no database mutations.\n";
