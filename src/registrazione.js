// ============================================================
// js/registrazione.js – Validazione client-side registrazione
// ============================================================

(function () {
    'use strict';

    // --- Toggle password ---
    const toggleBtn     = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleBtn.setAttribute('aria-label', isPassword ? 'Nascondi password' : 'Mostra password');
            toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            toggleBtn.textContent = isPassword ? '🔒' : '👁';
        });
    }

    // --- CF in maiuscolo mentre si digita ---
    const cfInput = document.getElementById('cf');
    if (cfInput) {
        cfInput.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }

    // --- Validazione al submit ---
    const form = document.getElementById('form-registrazione');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        clearErrors();
        const errori = [];

        const nome     = document.getElementById('nome');
        const cognome  = document.getElementById('cognome');
        const cf       = document.getElementById('cf');
        const email    = document.getElementById('email');
        const password = document.getElementById('password');
        const privacy  = document.getElementById('privacy');

        if (nome.value.trim() === '') {
            errori.push({ field: nome, msg: 'Il nome è obbligatorio.' });
        }

        if (cognome.value.trim() === '') {
            errori.push({ field: cognome, msg: 'Il cognome è obbligatorio.' });
        }

        const cfVal = cf.value.trim().toUpperCase();
        if (cfVal === '') {
            errori.push({ field: cf, msg: 'Il Codice Fiscale è obbligatorio.' });
        } else if (!/^[A-Z0-9]{16}$/.test(cfVal)) {
            errori.push({ field: cf, msg: 'Il Codice Fiscale deve essere di 16 caratteri alfanumerici.' });
        }

        if (email.value.trim() === '') {
            errori.push({ field: email, msg: "L'indirizzo email è obbligatorio." });
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            errori.push({ field: email, msg: 'Inserisci un indirizzo email valido.' });
        }

        if (password.value === '') {
            errori.push({ field: password, msg: 'La password è obbligatoria.' });
        } else if (password.value.length < 6) {
            errori.push({ field: password, msg: 'La password deve contenere almeno 6 caratteri.' });
        }

        if (!privacy.checked) {
            errori.push({ field: privacy, msg: 'Devi accettare la privacy policy.' });
        }

        if (errori.length > 0) {
            e.preventDefault();
            showErrors(errori);
            // Focus sul primo campo errato (non checkbox)
            const primoField = errori[0].field;
            primoField.focus();
        }
    });

    function showErrors(errori) {
        // Riepilogo in cima al form
        let summary = form.querySelector('.error-summary');
        if (!summary) {
            summary = document.createElement('div');
            summary.className = 'error-summary';
            summary.setAttribute('role', 'alert');
            summary.setAttribute('aria-live', 'assertive');
            form.insertAdjacentElement('afterbegin', summary);
        }

        const n = errori.length;
        summary.innerHTML = `
            <h2><span aria-hidden="true">⚠</span> Si ${n === 1 ? 'è verificato 1 errore' : 'sono verificati ' + n + ' errori'}</h2>
            <ul>${errori.map(e => `<li>${e.msg}</li>`).join('')}</ul>
        `;

        errori.forEach(function ({ field, msg }) {
            if (field.type === 'checkbox') {
                // Errore privacy sotto al label
                const label = field.closest('label') || field.parentElement;
                let errEl = document.getElementById('privacy-error-js');
                if (!errEl) {
                    errEl = document.createElement('span');
                    errEl.id = 'privacy-error-js';
                    errEl.className = 'form-error';
                    errEl.setAttribute('role', 'alert');
                    label.insertAdjacentElement('afterend', errEl);
                }
                errEl.textContent = msg;
                field.setAttribute('aria-describedby', 'privacy-error-js');
                return;
            }

            field.classList.add('form-input--error');
            field.setAttribute('aria-invalid', 'true');

            const errorId = field.id + '-error-js';
            let errEl = document.getElementById(errorId);
            if (!errEl) {
                errEl = document.createElement('span');
                errEl.id = errorId;
                errEl.className = 'form-error';
                errEl.setAttribute('role', 'alert');
                // Inserisci dopo il wrapper password o dopo l'input
                const parent = field.closest('.password-wrapper') || field;
                parent.insertAdjacentElement('afterend', errEl);
            }
            errEl.textContent = msg;
            field.setAttribute('aria-describedby',
                (field.getAttribute('aria-describedby') || '').replace(errorId, '').trim() + ' ' + errorId
            );
        });
    }

    function clearErrors() {
        document.querySelectorAll('.form-input--error').forEach(el => {
            el.classList.remove('form-input--error');
            el.removeAttribute('aria-invalid');
        });
        document.querySelectorAll('[id$="-error-js"]').forEach(el => el.remove());
        const summary = form ? form.querySelector('.error-summary') : null;
        if (summary) summary.remove();
    }

})();