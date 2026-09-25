/* Calendar-day ordinals, independent of timezone and daylight-saving changes. */
const tribeStayDates = {
    ordinal(value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) return NaN;
        let [year, month, day] = value.split('-').map(Number);
        const leap = year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0);
        if (year < 1000 || month < 1 || month > 12 || day < 1 || day > [31,leap?29:28,31,30,31,30,31,31,30,31,30,31][month-1]) return NaN;
        year -= month <= 2 ? 1 : 0;
        const era = Math.floor(year/400), y = year-era*400;
        return era*146097 + y*365 + Math.floor(y/4) - Math.floor(y/100) + Math.floor((153*(month+(month>2?-3:9))+2)/5) + day-1;
    },
    nights(start, end) {
        const count = this.ordinal(end)-this.ordinal(start);
        return Number.isFinite(count) && count > 0 ? count : null;
    },
    label(start, end) {
        const count = this.nights(start,end);
        return count === null ? '' : `${count} ${count === 1 ? 'night' : 'nights'}`;
    },
    date(value, month='short', year=false) {
        return new Date(`${value}T12:00:00Z`).toLocaleDateString('en-GB', {day:'numeric',month,...(year?{year:'numeric'}:{}),timeZone:'UTC'});
    },
    summary(start, end) {
        return `${this.date(start,'long',true)} → ${this.date(end,'long',true)} · ${this.label(start,end)}\nDates selected; confirmation is still required.`;
    }
};
if (typeof module !== 'undefined') module.exports = tribeStayDates;
/* One calendar UI for public availability and private manual range selection. */
(() => {
    if (typeof document === 'undefined') return;
    const root = document.querySelector('[data-calendar]');
    if (!root) return;
    const admin = root.dataset.admin === 'true';
    const start = document.querySelector(root.dataset.start), end = document.querySelector(root.dataset.end);
    let today = root.dataset.today;
    let month = root.dataset.month || today.slice(0, 7), ranges = [], loaded = false, selectingEnd = !!start.value && !end.value, pending = false;
    const failure = "Availability couldn't be loaded right now. You can still send us your dates and we'll confirm them with you.";
    const parse = value => new Date(`${value}T12:00:00Z`);
    const iso = date => date.toISOString().slice(0, 10);
    const validDate = value => /^\d{4}-\d{2}-\d{2}$/.test(value) && !Number.isNaN(parse(value).getTime());
    start.max = end.max = '9999-12-31';
    const occupied = day => ranges.some(r => day >= r.start && day < r.end);
    const overlaps = (a, b) => ranges.some(r => a < r.end && b > r.start);
    const label = day => parse(day).toLocaleDateString('en-GB', { day:'numeric', month:'long', year:'numeric', timeZone:'UTC' });
    root.className = 'availability-calendar';
    root.innerHTML = '<div class="availability-calendar-top"><h2>Choose your dates</h2><button type="button" class="availability-clear">Clear</button></div><p class="availability-status" role="status" aria-live="polite"></p><div class="availability-month-nav"><button type="button" aria-label="Previous month">←</button><h3 aria-live="polite"></h3><button type="button" aria-label="Next month">→</button></div><div class="availability-week" aria-hidden="true"><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span></div><div class="availability-days" role="group" aria-label="Calendar dates"></div><p class="availability-legend"><span>○ Available</span><span>× Unavailable night</span><span>● Selected</span></p><p class="availability-help">Choose check-in, then check-out. Checkout does not occupy that night.</p>';
    if (admin) root.querySelector('h2').textContent = 'Occupied nights';
    const status = root.querySelector('.availability-status'), grid = root.querySelector('.availability-days');
    const nav = root.querySelectorAll('.availability-month-nav button');
    function message(text) { status.textContent = text; }
    function render() {
        root.dataset.verified = loaded ? 'true' : 'false';
        root.querySelector('.availability-legend').hidden = !loaded;
        root.querySelector('h3').textContent = parse(`${month}-01`).toLocaleDateString('en-GB', { month:'long', year:'numeric', timeZone:'UTC' });
        nav[0].disabled = month <= today.slice(0, 7);
        nav[1].disabled = month >= '9999-12';
        const first = parse(`${month}-01`), offset = (first.getUTCDay()+6)%7;
        const count = new Date(Date.UTC(first.getUTCFullYear(), first.getUTCMonth()+1, 0)).getUTCDate();
        grid.replaceChildren();
        for (let i=0; i<offset; i++) grid.append(document.createElement('span'));
        for (let day=1; day<=count; day++) {
            const date = `${month}-${String(day).padStart(2,'0')}`;
            const blocked = occupied(date), past = date < today;
            const validEnd = selectingEnd && start.value && date > start.value && !overlaps(start.value, date);
            const validSelection = admin || (loaded && start.value >= today && (!end.value || (end.value > start.value && !overlaps(start.value,end.value))) && !occupied(start.value));
            const selected = validSelection && (date === start.value || date === end.value || (start.value && end.value && date > start.value && date < end.value));
            const button = document.createElement('button'); button.type='button'; button.dataset.date = date;
            button.textContent = String(day);
            button.className = [blocked?'is-unavailable':'',past?'is-past':'',selected?'is-selected':'',date===start.value?'is-start':'',date===end.value?'is-end':''].join(' ');
            // A blocked night may be a checkout boundary, but never an occupied night.
            button.disabled = past || !loaded || (!admin && (selectingEnd ? !validEnd : blocked));
            button.setAttribute('aria-label', `${label(date)}${past?', past':blocked?', unavailable night':''}${validEnd && blocked?', checkout only':''}${date===start.value?', check-in selected':date===end.value?', checkout selected':selected?', selected night':''}`);
            button.setAttribute('aria-pressed', selected?'true':'false');
            button.addEventListener('click', () => {
                if (!selectingEnd || date <= start.value) { start.value=date; end.value=''; selectingEnd=true; }
                else { end.value=date; selectingEnd=false; }
                start.dispatchEvent(new Event('change',{bubbles:true}));
                end.dispatchEvent(new Event('change',{bubbles:true}));
                message(end.value ? (admin ? `${label(start.value)} → ${label(end.value)}. Ready to block.` : tribeStayDates.summary(start.value,end.value)) : 'Now choose your checkout date.');
                render();
                grid.querySelector(`[data-date="${date}"]`)?.focus();
            });
            grid.append(button);
        }
    }
    function changeMonth(step) {
        const date=parse(`${month}-01`); date.setUTCMonth(date.getUTCMonth()+step); month=iso(date).slice(0,7); render();
    }
    nav[0].addEventListener('click',()=>changeMonth(-1)); nav[1].addEventListener('click',()=>changeMonth(1));
    root.querySelector('.availability-clear').addEventListener('click',()=>{start.value='';end.value='';selectingEnd=false;start.dispatchEvent(new Event('change',{bubbles:true}));message(loaded?'Choose your check-in date.':failure);render();});
    grid.addEventListener('keydown', event => {
        const offsets={ArrowLeft:-1,ArrowRight:1,ArrowUp:-7,ArrowDown:7};
        if (!(event.key in offsets) || !event.target.dataset.date) return;
        event.preventDefault();
        const date=parse(event.target.dataset.date); date.setUTCDate(date.getUTCDate()+offsets[event.key]);
        if(iso(date)<today) return;
        month=iso(date).slice(0,7); render();
        const buttons=[...grid.querySelectorAll('button:not(:disabled)')];
        (buttons.find(b=>b.dataset.date===iso(date)) || (offsets[event.key]>0?buttons.find(b=>b.dataset.date>iso(date)):buttons.reverse().find(b=>b.dataset.date<iso(date))))?.focus();
    });
    [start,end].forEach(input=>input.addEventListener('change',()=>{
        selectingEnd=!!start.value&&!end.value;
        if (validDate(input.value) && input.value >= today) month = input.value.slice(0,7);
        if (admin && validDate(start.value) && start.value < '9999-12-31') {
            const next = parse(start.value); next.setUTCDate(next.getUTCDate()+1); end.min = iso(next);
        }
        if(!admin && loaded && start.value && end.value && overlaps(start.value,end.value)) message('These dates include unavailable nights. Please choose another stay.');
        else if (!admin && loaded) message(tribeStayDates.nights(start.value,end.value)!==null && start.value>=today ? tribeStayDates.summary(start.value,end.value) : 'Choose check-in, then check-out.');
        render();
    }));
    async function refresh(check=false) {
        const url=new URL(root.dataset.api,location.href); url.searchParams.set('unit',root.dataset.unit);
        if(check) { url.searchParams.set('start',start.value);url.searchParams.set('end',end.value); }
        const controller=new AbortController(), timer=setTimeout(()=>controller.abort(),6000);
        try {
            const response=await fetch(url,{cache:'no-store',signal:controller.signal,headers:{Accept:'application/json'}});
            const data=await response.json();
            if(response.status===422) { message(data.message); return false; }
            if(!response.ok || data.unit!==root.dataset.unit || !Array.isArray(data.unavailable) || !/^\d{4}-\d{2}-\d{2}$/.test(data.today) || data.unavailable.some(r=>!r || !/^\d{4}-\d{2}-\d{2}$/.test(r.start) || !/^\d{4}-\d{2}-\d{2}$/.test(r.end) || r.end<=r.start) || (check && typeof data.valid!=='boolean')) throw new Error('Unavailable');
            today=data.today;start.min=today;
            if(start.form?.id==='date-request') start.form.dataset.today=today;
            ranges=data.unavailable;loaded=true;
            if(check && data.valid===false) { message('These dates include unavailable nights. Please choose another stay.'); render();return false; }
            message(tribeStayDates.nights(start.value,end.value)!==null?tribeStayDates.summary(start.value,end.value):selectingEnd?'Now choose your checkout date.':'Choose your check-in date.');render();return true;
        } catch(error) {
            loaded=false;message(failure);render();
            // Retain known blocks during an outage. Never show all dates as available.
            if(check && overlaps(start.value,end.value)) { message('These dates include previously reported unavailable nights. Please choose another stay.');return false; }
            return true;
        } finally { clearTimeout(timer); }
    }
    if(admin) {
        const data=JSON.parse(root.dataset.ranges);loaded=Array.isArray(data);ranges=data||[];
        if(start.value && start.value>=today) month=start.value.slice(0,7);
        selectingEnd=!!start.value&&!end.value;
        message(loaded?'Select a range to block.':'Calendar unavailable. No changes can be made.');render();
    } else {
        message('Loading availability…');render();const initialLoad=refresh();
        window.tribeAvailability={verify:async()=>{
            if(pending) return false;
            if(!validDate(start.value)||!validDate(end.value)||start.value<today||end.value<=start.value) {message('Choose a future stay with checkout after check-in.');return false;}
            const requestedStart=start.value, requestedEnd=end.value;
            pending=true;
            await initialLoad;
            const valid=await refresh(true);pending=false;
            if(start.value!==requestedStart || end.value!==requestedEnd) {message('Your dates changed while checking. Please continue again to verify them.');status.tabIndex=-1;status.focus();return false;}
            if(!valid) {status.tabIndex=-1;status.focus();}
            document.querySelector('#dates-availability-warning')?.remove();
            if(!loaded) {const warning=document.createElement('p');warning.id='dates-availability-warning';warning.className='availability-alert';warning.textContent=failure;document.querySelector('#dates-request-summary').after(warning);}
            return valid;
        }};
    }
})();
