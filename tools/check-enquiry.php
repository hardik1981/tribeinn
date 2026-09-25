<?php
declare(strict_types=1);
require __DIR__ . '/../includes/enquiry-service.php';
$config = require __DIR__ . '/../config/enquiry.php';
$count = 0;
function check(bool $condition, string $label): void { global $count; if (!$condition) throw new RuntimeException($label); $count++; }
$valid = ['checkin'=>date('Y-m-d', strtotime('+10 days')), 'checkout'=>date('Y-m-d', strtotime('+20 days')), 'adults'=>'2','children'=>'0','name'=>'Preview Guest','email'=>'guest@example.com','phone'=>'','purpose'=>'Workation','message'=>'Coffee & writing.'];
check(enquiryValidate($valid, $config)[1] === [], 'Valid request without phone');
foreach (['checkin'=>'2020-01-01','checkout'=>$valid['checkin'],'name'=>'','email'=>'','purpose'=>'Unknown','adults'=>'0','children'=>'-1','phone'=>'letters'] as $key=>$value) check(isset(enquiryValidate(array_replace($valid,[$key=>$value]),$config)[1][$key]), 'Reject '.$key);
foreach (['2027-02-29','not-a-date','2027-13-01'] as $date) check(isset(enquiryValidate(array_replace($valid,['checkin'=>$date]),$config)[1]['checkin']), 'Reject invalid date');
check(isset(enquiryValidate(array_replace($valid,['email'=>"a@example.com\r\nBcc:x@example.com"]),$config)[1]['email']), 'Reject header injection');
check(isset(enquiryValidate(array_replace($valid,['name'=>['bad']]),$config)[1]['name']), 'Reject array input');
check(isset(enquiryValidate(array_replace($valid,['message'=>str_repeat('x',2001)]),$config)[1]['message']), 'Reject long message');
[$clean] = enquiryValidate(array_replace($valid,['name'=>' <b>Guest</b> ','message'=>"<b>Hello</b>\0\nWorld"]),$config);
check($clean['name']==='Guest' && $clean['message']==="Hello\nWorld", 'Sanitize markup and controls');
$body = enquiryMessage($valid,'The Beginning');
foreach (['Property: The Beginning','Email: guest@example.com','Adults: 2','Children: 0','Purpose: Workation','Coffee & writing.'] as $text) check(str_contains($body,$text),'Body includes '.$text);
check(!str_contains($body,'Phone:'),'Omit empty phone');
foreach ([1,2,3] as $adults) foreach ([0,1,2,3] as $children) {
    $guestBody = enquiryMessage(array_replace($valid,['adults'=>(string)$adults,'children'=>(string)$children]),'The Beginning');
    check(str_contains($guestBody, ($adults===1?'Adult: ':'Adults: ').$adults."\n"), 'Email adult grammar');
    check(str_contains($guestBody, ($children===1?'Child: ':'Children: ').$children."\n"), 'Email child grammar');
}
check(enquirySend($valid,array_replace($config,['transport'=>'disabled']),'The Beginning')===false,'Disabled transport never succeeds');
$fresh = fn()=>['enquiry_csrf'=>'token','enquiry_requests'=>['request'=>['created'=>time(),'sent'=>false]]];
$input = $valid + ['csrf'=>'token','request_id'=>'request','channel'=>'email'];
$calls=0; $sender=function()use(&$calls){$calls++;return true;};
$session=$fresh(); check(enquirySubmit($input,$session,$config,'The Beginning',$sender)[0]===200,'Success');
check(enquirySubmit($input,$session,$config,'The Beginning',$sender)[0]===200 && $calls===1,'Duplicate sends once');
foreach ([['csrf'=>'bad'],['channel'=>'whatsapp'],['email'=>''],['website'=>'spam']] as $change) { $session=$fresh(); check(enquirySubmit(array_replace($input,$change),$session,$config,'The Beginning',$sender)[0]>=400,'Reject before transport'); }
check($calls===1,'Rejected requests never call sender');
$session=$fresh(); $session['enquiry_requests']['request']['created']=time()-86401;
check(enquirySubmit($input,$session,$config,'The Beginning',$sender)[0]===403,'Expired token');
$session=$fresh(); check(enquirySubmit($input,$session,$config,'The Beginning',fn()=>false)[0]===503 && !$session['enquiry_requests']['request']['sent'],'Failure retryable');
check(enquirySubmit($input,$session,$config,'The Beginning',$sender)[0]===200,'Retry after transport failure');
$session=$fresh(); $session['enquiry_attempts']=array_fill(0,5,time());
check(enquirySubmit($input,$session,$config,'The Beginning',$sender)[0]===429,'Rate limit');
echo "$count enquiry checks passed. No email sent.\n";
