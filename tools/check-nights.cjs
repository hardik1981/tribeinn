const assert = require('node:assert/strict');
const {execFileSync} = require('node:child_process');
const dates = require('../assets/js/availability.js');
const {tribeRequestSummary, tribeWhatsAppMessage} = require('../assets/js/check-dates.js');
const cases = [['2026-09-28','2026-09-29',1],['2026-09-28','2026-09-30',2],['2026-09-30','2026-10-03',3],['2026-12-31','2027-01-02',2],['2028-02-28','2028-03-01',2],['2026-03-07','2026-03-09',2],['2026-10-31','2026-11-02',2]];
let checks = 0;
for (const adults of ['1','2','3']) for (const children of ['0','1','2','3']) {
    const data = {checkin:'2026-09-28',checkout:'2026-09-30',adults,children};
    const adultText = `${adults} ${adults==='1'?'adult':'adults'}`;
    const childText = `${children} ${children==='1'?'child':'children'}`;
    assert.equal(tribeRequestSummary(data,'The Beginning')[1],`${adultText} · ${childText}`);
    assert.ok(tribeWhatsAppMessage(data,'The Beginning').includes(`Guests: ${adultText}, ${childText}\n`));
    checks+=2;
}
for (const timezone of ['UTC','Asia/Kolkata','America/New_York','Europe/London']) {
    process.env.TZ = timezone;
    for (const [checkin,checkout,nights] of cases) {
        const data = {checkin,checkout,adults:2,children:0,name:'Test',email:'guest@example.com',phone:'',purpose:'Workation',message:''};
        const label = `${nights} ${nights===1?'night':'nights'}`;
        assert.equal(dates.nights(checkin,checkout),nights);
        assert.equal(dates.label(checkin,checkout),label);
        assert.ok(dates.summary(checkin,checkout).includes(`· ${label}\nDates selected; confirmation is still required.`));
        assert.ok(tribeRequestSummary(data,'The Beginning')[0].endsWith(`· ${label}`));
        assert.equal(tribeRequestSummary(data,'The Beginning')[1],'2 adults · 0 children');
        assert.ok(tribeWhatsAppMessage(data,'The Beginning').includes(`Number of nights: ${nights}\n`));
        checks+=6;
    }
}
for (const pair of [['',''],['2026-02-30','2026-03-02'],['2026-09-30','2026-09-28'],['2026-09-28','2026-09-28']]) {
    assert.equal(dates.nights(...pair),null); checks++;
}
const php = `require 'includes/enquiry-service.php'; $cases=json_decode(stream_get_contents(STDIN),true); foreach($cases as [$start,$end,$count]) { if(AvailabilityDates::nights($start,$end)!==$count) exit(1); $data=['checkin'=>$start,'checkout'=>$end,'name'=>'Test','email'=>'guest@example.com','phone'=>'','adults'=>'2','children'=>'0','purpose'=>'Workation','message'=>'']; if(!str_contains(enquiryMessage($data,'The Beginning'),"Number of nights: $count\\n")) exit(2); }`;
execFileSync('php',['-r',php],{cwd:require('node:path').resolve(__dirname,'..'),input:JSON.stringify(cases)});
checks+=cases.length*2;
console.log(`PASS: ${checks} night-count, calendar/modal summary, WhatsApp and server-email checks. No messages sent.`);
