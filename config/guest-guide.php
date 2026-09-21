<?php
declare(strict_types=1);

// Approved house-manual content for The Beginning. Slugs are permanent QR targets.
// Blocks are plain text, not HTML. Additional properties can supply another data file.
$p = fn(string $text) => ['type'=>'paragraph', 'text'=>$text];
$list = fn(array $items) => ['type'=>'list', 'items'=>$items];
$steps = fn(array $items) => ['type'=>'steps', 'items'=>$items];
$photo = fn(string $file, string $alt, string $caption = '', bool $guide = false) => ['src'=>'assets/images/guest-guide/'.$file,'alt'=>$alt,'caption'=>$caption,'guide'=>$guide];
$download = fn(string $file) => ['src'=>'assets/images/guest-guide/'.$file,'label'=>'Download full guide','format'=>'PNG'];
$tv = $photo('tv-soundbar-guide.png','TV and boAt soundbar quick-start guide, including AV input, no-sound troubleshooting and Bluetooth steps','TV + Soundbar Quick-Start Guide',true);
$hob = $photo('bosch-induction-hob-guide.png','Bosch induction hob quick-start guide: main switch, cookware, long-press controls, power levels and switching off','Bosch Induction Hob Quick-Start Guide',true);

$entries = [
    ['slug'=>'find-the-beginning','question'=>'How do I find The Beginning?','topic'=>['arrival'],'tags'=>['address','tower','gate'], 'answer'=>[
        $p('The Beginning is at Tata Housing Rio De Goa, Tower 4, Flat 604.'),
        $p("When you arrive at the main gate, security will check the IDs of the registered guests. Once you're through, you can ask security to point you towards Tower 4.")]],
    ['slug'=>'check-in','question'=>'How do I check in?','topic'=>['arrival'],'tags'=>['key','lockbox','arrival','ID'], 'answer'=>[
        $p('Please carry the same ID used for your booking.'),$p('The apartment key will be kept in a combination lockbox outside the apartment door.'),$p("You'll receive the combination code along with your welcome/check-in instructions before arrival.")]],
    ['slug'=>'parking','question'=>'Where do I park?','topic'=>['arrival'],'tags'=>['car','238','directions'], 'answer'=>[
        $p('Your designated parking space is 238.'),$steps(['After entering Rio De Goa through the main gate, continue straight and take the parking entrance on your right.','Once inside, take the first left and drive slowly towards the exit.',"Just before reaching the exit, look to your right — you'll find Parking 238 beside the pillar."])]],
    ['slug'=>'wifi','question'=>'What is the Wi-Fi network and password?','topic'=>['wifi'],'tags'=>['wifi','internet','connection','welcome'], 'answer'=>[$p('The Wi-Fi network name and password will be shared with your welcome details before check-in.')]],
    ['slug'=>'wifi-work','question'=>'Is the Wi-Fi suitable for working?','topic'=>['wifi'],'tags'=>['wifi','internet','150 Mbps','table','remote work'], 'answer'=>[
        $p("Yes. The Beginning has an up to 150 Mbps internet connection, which we've comfortably used for streaming YouTube and Netflix and should handle normal remote-working needs well."),$p("There isn't a dedicated office desk."),$p("Instead, there's a movable multipurpose table that can be used as extra kitchen space or moved into the living area and used as a laptop/work table with the chair or sofa.")], 'images'=>[$photo('multipurpose-table-work-setup.jpg','Movable white multipurpose table beside a patterned chair at The Beginning','A movable multipurpose table')]],
    ['slug'=>'tv-soundbar','question'=>'How do I use the TV and boAt soundbar?','topic'=>['tv'],'tags'=>['AIWA','Fire TV','AV','remote','audio','no sound'], 'answer'=>[$steps([
        "Turn on the AIWA TV using the TV remote, then use the Fire TV Stick remote to choose what you'd like to watch.", 'Turn on the boAt soundbar and press INPUT repeatedly until its display shows AV.',"If there's no sound, use the AIWA TV remote to change the TV volume to 1 and then back to 0."]),$p('The sound should then start playing through the soundbar.')], 'images'=>[$tv], 'downloads'=>[$download('tv-soundbar-guide.png')]],
    ['slug'=>'soundbar-bluetooth','question'=>'How do I connect my phone to the boAt soundbar?','topic'=>['tv'],'tags'=>['Bluetooth','BT','music','phone'], 'answer'=>[$steps([
        'Turn on the boAt soundbar and press INPUT repeatedly until the display shows BT.','Open Bluetooth settings on your phone or tablet, select the boAt soundbar and connect.','When you want to return to TV audio, press INPUT repeatedly until the display shows AV again.']),$p('The TV + Soundbar guide includes these Bluetooth instructions.')], 'downloads'=>[$download('tv-soundbar-guide.png')], 'links'=>[['href'=>'#tv-soundbar','label'=>'View the TV + Soundbar guide']]],
    ['slug'=>'tv-streaming','question'=>'What can I watch on the TV?','topic'=>['tv','wifi'],'tags'=>['Netflix','Prime Video','YouTube','Fire TV Stick','subscriptions'], 'answer'=>[
        $p('The TV has a Fire TV Stick, so you can access services such as Netflix, Prime Video, YouTube and other supported apps.'),$p('Wi-Fi is provided, but guests need to sign in using their own subscriptions/accounts for paid streaming services.')]],
    ['slug'=>'induction-hob','question'=>'How do I use the Bosch induction hob?','topic'=>['kitchen'],'tags'=>['cooking','burner','power','Boost','stove'], 'answer'=>[$steps([
        'Switch on the main power supply near the hob and place induction-compatible cookware on the burner you want to use.','Long-press the On/Off control for 1–2 seconds, then long-press the appropriate burner selector.','A red dash indicates the selected burner.','Choose a power level from 0–9. B is the Boost function.','When finished, long-press On/Off to switch off the hob.'])], 'images'=>[$hob], 'downloads'=>[$download('bosch-induction-hob-guide.png')]],
    ['slug'=>'induction-cookware','question'=>'What cookware can I use on the induction hob?','topic'=>['kitchen'],'tags'=>['pots','pans','cooking'], 'answer'=>[
        $p('Use flat, induction-compatible cookware.'),$p('The hob will only heat when suitable cookware is detected on the selected cooking zone.')], 'downloads'=>[$download('bosch-induction-hob-guide.png')], 'links'=>[['href'=>'#induction-hob','label'=>'View the Bosch hob guide']]],
    ['slug'=>'kitchen-appliances','question'=>'What other kitchen appliances are available?','topic'=>['kitchen'],'tags'=>['microwave','kettle','toaster','mixer','grinder','table'], 'answer'=>[
        $p('Along with the Bosch induction hob, the kitchen includes:'),$list(['microwave oven','electric kettle','two-slice toaster','mixer/grinder']),$p('The kettle and toaster are kept above the microwave.'),$p('The mixer/grinder and its jars are stored in the open shelf above.'),$p('The movable white table can also be used as additional kitchen preparation or support space when needed.')], 'images'=>[
            $photo('kitchen-appliances.jpg','Kettle and toaster above the microwave, with mixer/grinder and jars on the shelf','Everyday kitchen appliances'),
            $photo('kitchen-overview-multipurpose-table.jpg','Kitchen overview with the movable white multipurpose table','Extra kitchen support space')]],
    ['slug'=>'food-delivery','question'=>'Can I order groceries and food?','topic'=>['kitchen'],'tags'=>['Swiggy','Zomato','Blinkit','grocery','shops'], 'answer'=>[$p('Yes.'),$p('Swiggy, Zomato and Blinkit deliver to Rio De Goa.'),$p('There are also local grocery shops within walking distance if guests prefer to pick things up themselves.')]],
    ['slug'=>'laundry','question'=>'What are my laundry options?','topic'=>['comfort'],'tags'=>['washing machine','spin','Klean Wave','clothes'], 'answer'=>[$p("There's a top-loading washing machine in the apartment with a semi-dry/spin function."),$p("If you'd prefer a laundry service, Klean Wave Laundry is less than 200 metres away.")]],
    ['slug'=>'air-conditioning','question'=>'How do I use the air conditioning?','topic'=>['comfort'],'tags'=>['AC','remote','stabilizer','power','delay'], 'answer'=>[
        $p('The apartment has regular split air conditioners operated using their remotes.'),$p('The ACs run through voltage stabilizers.'),$p('After switching on the main power, allow a few minutes before the AC starts.'),$p('If the power trips or the AC has just been switched off, it may also take a few minutes before it can restart.'),$p('This delay is normal.')]],
    ['slug'=>'garbage','question'=>'How does garbage collection work?','topic'=>['comfort','rules'],'tags'=>['rubbish','trash','waste','8 AM'], 'answer'=>[
        $p('Please place your tied garbage bag outside the apartment door before 8:00 AM for the morning collection.'),$p('If collection arrangements change during the stay, guests will be informed.')]],
    ['slug'=>'swimming-pool','question'=>'Which swimming pool can I use?','topic'=>['rio'],'tags'=>['swimwear','cap','main pool'], 'answer'=>[
        $p('Guests of The Beginning can use the main/bigger swimming pool in front of the gym.'),$p('Proper swimwear is required.'),$p('A swimming cap is required for women and guests with long hair.')]],
    ['slug'=>'infinity-pool','question'=>'Can I use the rooftop infinity pool?','topic'=>['rio','rules'],'tags'=>['access','rooftop'], 'answer'=>[
        $p('No.'),$p('The rooftop infinity pool is not available to guests of The Beginning.'),$p('Guests can use the main swimming pool in front of the gym instead.')]],
    ['slug'=>'facility-timings','question'=>'What are the pool and gym timings?','topic'=>['rio'],'tags'=>['hours','Monday','Tuesday','closed'], 'answer'=>[
        $p('Current timings:'),$list(['Tuesday–Sunday: 7:00 AM–12:00 PM and 2:00 PM–8:00 PM.','Monday: Closed.']),$p('The gym follows the same operating timings.'),$p('Facility timings can occasionally change, so guests should follow current notices displayed by Rio De Goa.')]],
    ['slug'=>'gym','question'=>'Can I use the gym?','topic'=>['rio'],'tags'=>['exercise','fitness'], 'answer'=>[$p('Yes.'),$p('Guests of The Beginning can use the Rio De Goa gym during its operating hours.')]],
    ['slug'=>'rio-facilities','question'=>'What other facilities can I use at Rio De Goa?','topic'=>['rio'],'tags'=>['table tennis','pool table','carrom','squash','terrace','Zuari'], 'answer'=>[
        $p('Around the clubhouse/gym area are recreational facilities including:'),$list(['table tennis','pool table','carrom','squash']),$p('Access or availability can occasionally vary.'),$p('Guests can also enjoy the terrace/viewing area overlooking the Zuari side.')]],
    ['slug'=>'maintenance-help','question'=>"What should I do if there's a power or plumbing problem?",'topic'=>['help'],'tags'=>['maintenance','electricity','water','intercom','security'], 'answer'=>[
        $p('For power, plumbing or similar building-related problems, contact Rio De Goa security, who can arrange assistance.'),['type'=>'contact','text'=>'Rio De Goa Security','label'=>'+91 91120 05945','href'=>'tel:+919112005945'],$p('You can also try dialling 9 from the apartment intercom, although the intercom is not always reliable.')]],
    ['slug'=>'security','question'=>'How do I contact security?','topic'=>['help','arrival'],'tags'=>['telephone','phone','intercom','9'], 'answer'=>[
        $p('The most reliable way is to call Rio De Goa security directly:'),$p('Guests can also dial 9 from the apartment intercom, although the intercom is not always reliable.')], 'links'=>[['href'=>'tel:+919112005945','label'=>'+91 91120 05945','primary'=>true]]],
    ['slug'=>'house-rules','question'=>'What are the house rules?','topic'=>['rules'],'tags'=>['smoking','noise','guests','AC','pool'], 'answer'=>[
        $p('Make yourself at home — and please treat The Beginning and Rio De Goa as you would your own home.'),$list([
            'Keep noise reasonable, particularly at night and in common areas.','Smoking is not permitted inside the apartment.','Please use proper swimwear at the pool; swimming caps are required for women and guests with long hair.',"Follow Rio De Goa's rules when using common facilities.",'The rooftop infinity pool is not available to guests of The Beginning.',"Don't leave the AC, induction hob or other high-power appliances running when leaving the apartment.",'Dispose of rubbish responsibly and use the morning garbage collection.','Only registered guests should stay overnight.','Please take care of the apartment, furnishings and equipment.']),$p("Most importantly, enjoy the place. We're trusting you with our little home in Goa.")]],
    ['slug'=>'checkout','question'=>'What should I do before checkout?','topic'=>['rules','arrival'],'tags'=>['key','lockbox','leaving','departure'], 'answer'=>[
        $p('Before leaving:'),$list(['Switch off the ACs, lights, induction hob and other appliances.','Make sure all taps are closed.','Wash or rinse any utensils used and leave them in the drying rack.','Place rubbish outside for collection.','Check cupboards, drawers and charging points for personal belongings.','Close the windows and balcony door.','Lock the apartment.','Return the key to the combination lockbox outside the door and scramble the combination afterward.']),$p("That's it — we'll take care of the rest. Have a safe journey home.")]],
    ['slug'=>'sofa-cum-bed','question'=>'How does the sofa-cum-bed work?','topic'=>['comfort'],'tags'=>['sofa bed','sleeping','caretaker'], 'answer'=>[
        $p('The living-room sofa converts into an additional bed when needed.'),$p("Guests don't need to figure out the setup themselves."),$p('Our caretaker can help convert it and prepare it for sleeping.'),$p('If the sofa-bed will be needed during the stay, guests can let us know so it can be prepared.')], 'images'=>[
            $photo('sofa-bed-sofa-mode.jpg','Living-room sofa in its normal sofa configuration','Sofa mode'),$photo('sofa-bed-bed-mode.jpg','The same living-room sofa converted into a bed','Bed mode')]],
];
foreach ($entries as $i => &$entry) {
    $entry += ['id'=>$i+1,'images'=>[],'downloads'=>[],'links'=>[],'video'=>null,'published'=>true,'sort_order'=>($i+1)*10];
}
unset($entry);
return [
    'property'=>'The Beginning',
    'topics'=>[
        'arrival'=>['label'=>'Getting Here & Check-in','icon'=>'key'],
        'wifi'=>['label'=>'Wi-Fi & Work','icon'=>'wifi'],
        'tv'=>['label'=>'TV & Entertainment','icon'=>'tv'],
        'kitchen'=>['label'=>'Kitchen & Food','icon'=>'pot'],
        'comfort'=>['label'=>'Laundry & Comfort','icon'=>'laundry'],
        'rio'=>['label'=>'Rio De Goa Facilities','icon'=>'building'],
        'help'=>['label'=>'Help & Security','icon'=>'help'],
        'rules'=>['label'=>'House Rules & Checkout','icon'=>'rules'],
    ],
    'entries'=>$entries,
];
