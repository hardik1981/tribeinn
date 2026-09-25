const stayDates = typeof module !== 'undefined' ? require('./availability.js') : tribeStayDates;
function tribeGuestCounts(data) {
    return [`${data.adults} ${Number(data.adults) === 1 ? 'adult' : 'adults'}`, `${data.children} ${Number(data.children) === 1 ? 'child' : 'children'}`];
}
function tribeRequestSummary(data, property) {
    const year = data.checkin.slice(0,4) !== data.checkout.slice(0,4);
    return [`${property} · ${stayDates.date(data.checkin,'short',year)} → ${stayDates.date(data.checkout,'short',year)} · ${stayDates.label(data.checkin,data.checkout)}`, tribeGuestCounts(data).join(' · ')];
}
function tribeEnquiryDate(value) {
    return stayDates.date(value, 'short', true).replace(/\bSept\b/, 'Sep');
}
function tribeWhatsAppMessage(data, property) {
    return [
        `Hi TribeInn, I'd like to check dates for ${property}.`, '',
        `Name: ${data.name}`, `Check-in: ${tribeEnquiryDate(data.checkin)}`, `Check-out: ${tribeEnquiryDate(data.checkout)}`,
        `Number of nights: ${stayDates.nights(data.checkin,data.checkout)}`,
        `Guests: ${tribeGuestCounts(data).join(', ')}`, `Purpose: ${data.purpose}`,
        ...(data.email ? [`Email: ${data.email}`] : []), ...(data.phone ? [`Phone: ${data.phone}`] : []),
        ...(data.message ? [`Message: ${data.message}`] : [])
    ].join('\n');
}
function tribeWhatsAppUrl(number, data, property) {
    if (!/^[1-9]\d{6,14}$/.test(number)) return null;
    return `https://wa.me/${number}?text=${encodeURIComponent(tribeWhatsAppMessage(data, property))}`;
}
if (typeof module !== 'undefined') module.exports = { tribeWhatsAppMessage, tribeWhatsAppUrl, tribeRequestSummary };

(() => {
    if (typeof document === 'undefined') return;
    const form = document.querySelector('#date-request');
    if (!form) return;
    const dialog = document.querySelector('#dates-dialog');
    const continueButton = form.querySelector('[type="submit"]');
    const errorSummary = document.querySelector('#dates-errors');
    const emailForm = document.querySelector('#email-request');
    const replyEmail = document.querySelector('#reply-email');
    const replyError = document.querySelector('#reply-email-error');
    const emailStatus = document.querySelector('#email-status');
    const whatsappStatus = document.querySelector('#dates-whatsapp-status');
    const sendButton = emailForm.querySelector('[type="submit"]');
    const emailChoice = document.querySelector('#choose-email');
    const whatsappChoice = document.querySelector('#choose-whatsapp');
    let busy = false;
    let sent = false;
    let bodyOverflow = '';
    function values() {
        return Object.fromEntries([...new FormData(form).entries()].map(([key, value]) => [key, value.trim()]));
    }
    function clearErrors() {
        errorSummary.hidden = true;
        form.querySelectorAll('[aria-invalid]').forEach(field => field.removeAttribute('aria-invalid'));
        form.querySelectorAll('.dates-field-error').forEach(field => { field.textContent = ''; });
    }
    function showErrors(errors) {
        clearErrors();
        Object.entries(errors).forEach(([key, text]) => {
            const field = form.elements.namedItem(key);
            const note = document.getElementById(`error-${key}`);
            if (field && note) { field.setAttribute('aria-invalid', 'true'); note.textContent = text; }
        });
        errorSummary.textContent = 'Please check your details: ' + Object.values(errors).join(' ');
        errorSummary.hidden = false;
        errorSummary.focus();
    }
    function validate() {
        const data = values(), errors = {};
        if (!data.checkin) errors.checkin = 'Please choose a check-in date.';
        else if (data.checkin < form.dataset.today) errors.checkin = 'Check-in cannot be in the past.';
        if (!data.checkout) errors.checkout = 'Please choose a check-out date.';
        else if (data.checkin && data.checkout <= data.checkin) errors.checkout = 'Check-out needs to be after check-in.';
        if (!/^\d+$/.test(data.adults) || +data.adults < 1 || +data.adults > 99) errors.adults = 'Please enter at least one adult (up to 99).';
        if (!/^\d+$/.test(data.children) || +data.children > 99) errors.children = 'Please enter a number from 0 to 99 for children.';
        if (!data.name) errors.name = 'Please tell us your name.';
        if (data.email && !form.elements.email.validity.valid) errors.email = 'Please enter a valid email address, or leave it empty for now.';
        if (data.phone && !/^[+()\d .-]{5,40}$/.test(data.phone)) errors.phone = 'Please use a valid phone number, or leave it empty.';
        if (!data.purpose) errors.purpose = 'Please choose what brings you to Goa.';
        if (data.message.length > 2000) errors.message = 'Please keep your message within 2,000 characters.';
        if (Object.keys(errors).length) { showErrors(errors); return false; }
        clearErrors(); return true;
    }
    function updateCheckout() {
        const arrival = form.elements.checkin.value;
        if (!arrival) { form.elements.checkout.min = form.dataset.today; return; }
        const next = new Date(`${arrival}T12:00:00Z`);
        if (!Number.isNaN(next.getTime())) {
            next.setUTCDate(next.getUTCDate() + 1);
            form.elements.checkout.min = next.toISOString().slice(0,10);
        }
    }
    form.elements.checkin.addEventListener('change', updateCheckout);
    continueButton.disabled = false;
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (busy || sent || !validate()) return;
        busy = true;
        const available = await (window.tribeAvailability?.verify() ?? Promise.resolve(true));
        busy = false;
        if (!available) return;
        const data = values();
        replyEmail.value = data.email;
        replyError.textContent = '';
        replyEmail.removeAttribute('aria-invalid');
        emailForm.hidden = true;
        emailStatus.hidden = true;
        whatsappStatus.hidden = true;
        const summary = tribeRequestSummary(data, form.dataset.property);
        document.querySelector('#dates-request-summary').replaceChildren(summary[0], document.createElement('br'), summary[1]);
        bodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        dialog.showModal();
        emailChoice.focus();
    });
    function syncReplyEmail() { form.elements.namedItem('email').value = replyEmail.value; }
    function closeDialog() { if (!busy) { if (!emailForm.hidden) syncReplyEmail(); dialog.close(); } }
    dialog.querySelector('.dates-dialog-close').addEventListener('click', closeDialog);
    dialog.querySelector('.dates-edit').addEventListener('click', closeDialog);
    dialog.addEventListener('cancel', event => { if (busy) event.preventDefault(); else if (!emailForm.hidden) syncReplyEmail(); });
    dialog.addEventListener('close', () => { document.body.style.overflow = bodyOverflow; if (!sent) continueButton.focus({ preventScroll: true }); });
    dialog.addEventListener('keydown', event => {
        if (event.key !== 'Tab') return;
        const controls = [...dialog.querySelectorAll('button:not(:disabled),input')].filter(el => el.getClientRects().length);
        const first = controls[0], last = controls[controls.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
    emailChoice.addEventListener('click', () => {
        if (busy) return;
        whatsappStatus.hidden = true;
        emailForm.hidden = false;
        replyEmail.focus();
    });
    replyEmail.addEventListener('input', () => { syncReplyEmail(); replyError.textContent = ''; replyEmail.removeAttribute('aria-invalid'); });
    replyEmail.addEventListener('change', syncReplyEmail);
    whatsappChoice.addEventListener('click', async () => {
        if (busy) return;
        busy = true;
        const available = await (window.tribeAvailability?.verify() ?? Promise.resolve(true));
        busy = false;
        if (!available) { dialog.close(); return; }
        if (!emailForm.hidden) syncReplyEmail();
        emailForm.hidden = true;
        const link = tribeWhatsAppUrl(form.dataset.whatsapp, values(), form.dataset.property);
        whatsappStatus.hidden = false;
        if (!link) {
            whatsappStatus.textContent = 'WhatsApp isn’t available here yet. Please choose Email TribeInn or contact@tribeinn.com.';
            return;
        }
        window.open(link, '_blank', 'noopener,noreferrer');
        whatsappStatus.textContent = 'Continue in WhatsApp and press Send there. Opening WhatsApp does not send your request.';
        // Async availability checks can consume the browser's popup gesture.
        const fallback = document.createElement('a');
        fallback.href = link; fallback.target = '_blank'; fallback.rel = 'noopener noreferrer';
        fallback.className = 'text-link'; fallback.textContent = 'Open WhatsApp →';
        whatsappStatus.append(document.createElement('br'), fallback);
    });
    emailForm.addEventListener('submit', async event => {
        event.preventDefault();
        if (busy || sent) return;
        busy = true;
        const available = await (window.tribeAvailability?.verify() ?? Promise.resolve(true));
        busy = false;
        if (!available) { dialog.close(); return; }
        if (!replyEmail.value.trim() || !replyEmail.validity.valid) {
            replyError.textContent = 'Please enter a valid email so we can reply.';
            replyEmail.setAttribute('aria-invalid', 'true'); replyEmail.focus(); return;
        }
        form.elements.email.value = replyEmail.value.trim();
        const payload = new FormData(form);
        busy = true;
        [sendButton,emailChoice,whatsappChoice,dialog.querySelector('.dates-dialog-close'),dialog.querySelector('.dates-edit')].forEach(button => { button.disabled = true; });
        replyEmail.readOnly = true;
        emailForm.setAttribute('aria-busy', 'true');
        sendButton.textContent = 'Sending your request…';
        emailStatus.hidden = true;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 20000);
        try {
            const response = await fetch(form.action, { method: 'POST', body: payload, credentials: 'same-origin', signal: controller.signal, headers: { Accept: 'application/json' } });
            const result = await response.json();
            if (!response.ok || !result.ok) {
                if (result.errors?.email) {
                    replyError.textContent = result.errors.email;
                    replyEmail.setAttribute('aria-invalid', 'true'); replyEmail.focus();
                } else if (result.errors) {
                    busy = false; dialog.close(); showErrors(result.errors);
                }
                emailStatus.textContent = result.message || 'We couldn’t send the request. Please try again.';
                emailStatus.hidden = false;
                return;
            }
            sent = true;
            document.querySelector('#dates-form-content').hidden = true;
            const success = document.querySelector('#dates-success');
            success.hidden = false;
            if (result.preview === true) {
                const notice = document.querySelector('#dates-preview-notice');
                notice.textContent = 'Local test preview only — no email was sent.'; notice.hidden = false;
            }
            if (result.availability_unverified === true) {
                const warning = document.createElement('p');
                warning.textContent = 'Availability could not be verified online. We’ll check your dates personally before confirming.';
                success.append(warning);
            }
            dialog.close();
            success.focus();
        } catch (error) {
            emailStatus.textContent = 'We couldn’t confirm submission. Your details are still here. You can retry safely or email contact@tribeinn.com.';
            emailStatus.hidden = false;
        } finally {
            clearTimeout(timeout);
            busy = false;
            [sendButton,emailChoice,whatsappChoice,dialog.querySelector('.dates-dialog-close'),dialog.querySelector('.dates-edit')].forEach(button => { button.disabled = false; });
            replyEmail.readOnly = false;
            emailForm.removeAttribute('aria-busy');
            sendButton.textContent = 'Send email request →';
        }
    });
})();
