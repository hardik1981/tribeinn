const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const {tribeWhatsAppUrl} = require('../assets/js/check-dates.js');
let checks = 0;
async function scenario(result, httpOk = true) {
    const nodes = new Map();
    const node = key => {
        if (!nodes.has(key)) nodes.set(key, {hidden:true,value:'',validity:{valid:true},dataset:{},style:{},handlers:{},children:[],
            addEventListener(type,fn){this.handlers[type]=fn;}, querySelector:node, querySelectorAll(){return [];},
            setAttribute(){},removeAttribute(key){delete this[key];},append(...items){this.children.push(...items);},focus(){this.focused=true;},
            close(){this.closed=true;},showModal(){this.open=true;}});
        return nodes.get(key);
    };
    const data = {checkin:'2027-10-08',checkout:'2027-10-10',adults:'1',children:'1',name:'Review Guest',email:'original@example.com',phone:'+1234567890',purpose:'Workation',message:'A quiet stay',channel:'email'};
    const form=node('#date-request'); form.dataset={whatsapp:'12345678901',property:'The Beginning',today:'2027-10-01'};
    form.elements={};
    for (const [key,value] of Object.entries(data)) form.elements[key]={value,validity:{valid:true},addEventListener(){}};
    form.elements.namedItem=key=>form.elements[key];
    node('#reply-email').value='reply@example.com';
    let requests=0, verifications=0;
    const context={location:{hash:''},module:{exports:{}},require,console,AbortController,setTimeout,clearTimeout,
        document:{querySelector:node,body:{style:{}},createElement:()=>node('created')},
        window:{tribeAvailability:{verify:async()=>{verifications++;return true;}}},
        FormData:class {constructor(form){this.data=Object.entries(form.elements).filter(([,v])=>typeof v==='object').map(([k,v])=>[k,v.value]);} entries(){return this.data[Symbol.iterator]();}},
        fetch:async()=>{requests++; form.elements.name.value='Changed after submission';return {ok:httpOk,json:async()=>result};}
    };
    context.require=path=>path==='./availability.js'?require('../assets/js/availability.js'):require(path);
    vm.runInNewContext(fs.readFileSync(require.resolve('../assets/js/check-dates.js'),'utf8'),context);
    assert.equal(node('#dates-followup').hidden,true); checks++;
    await node('#email-request').handlers.submit({preventDefault(){}});
    assert.equal(requests,1); checks++;
    if (httpOk && result.ok) {
        assert.equal(node('#dates-success').hidden,false); checks++;
        if (!result.preview) {
            assert.equal(node('#dates-followup').hidden,false);
            assert.equal(node('#dates-followup-link').href,tribeWhatsAppUrl(form.dataset.whatsapp,{...data,email:'reply@example.com'},'The Beginning'));
            assert.equal(node('#dates-followup-link').handlers.click,undefined); // Native link: no fetch, availability check or popup handler.
            checks+=3;
            node('#dates-followup-done').handlers.click();
            assert.equal(node('#dates-followup').hidden,true);
            assert.equal(node('#dates-success').hidden,false);
            await node('#email-request').handlers.submit({preventDefault(){}});
            assert.equal(requests,1); assert.equal(verifications,1); checks+=4;
        } else {assert.equal(node('#dates-followup').hidden,true); checks++;}
    } else {assert.equal(node('#dates-followup').hidden,true);assert.equal(node('#dates-success').hidden,true);checks+=2;}
}
(async()=>{
    await scenario({ok:true});
    await scenario({ok:true,availability_unverified:true});
    await scenario({ok:true,preview:true});
    await scenario({ok:false,message:'Not sent'},false);
    console.log(`PASS: ${checks} email continuation checks; mocked transport, no messages sent.`);
})().catch(error=>{console.error(error);process.exitCode=1;});
