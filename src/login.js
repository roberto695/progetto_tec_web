// ============================================================
// js/login.js – Validazione client-side login + toggle password
// ============================================================

(function () {
    'use strict';

    // --- Toggle mostra/nascondi password ---
    const toggleBtn = document.getElementById('toggle-password');
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

    // --- Validazione client-side al submit ---
    const form = document.getElementById('form-login');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const errori = [];

        const cf = document.getElementById('cf');
        const pwd = document.getElementById('password');

        // Pulisci errori precedenti
        clearErrors();

        // Valida CF
        const cfVal = cf.value.trim().toUpperCase();
        if (cfVal === '') {
            errori.push({ field: cf, msg: 'Il Codice Fiscale è obbligatorio.' });
        } else if (!/^[A-Z0-9]{16}$/.test(cfVal)) {
            errori.push({ field: cf, msg: 'Il Codice Fiscale deve essere di 16 caratteri alfanumerici.' });
        }

        // Valida password
        const pwdVal = pwd.value;
        if (pwdVal === '') {
            errori.push({ field: pwd, msg: 'La password è obbligatoria.' });
        }

        if (errori.length > 0) {
            e.preventDefault();
            showErrors(errori);
            errori[0].field.focus();
        }
    });

    // Converti CF in maiuscolo mentre si digita
    const cfInput = document.getElementById('cf');
    if (cfInput) {
        cfInput.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }

    function showErrors(errori) {
        // Crea o aggiorna il riepilogo in cima
        let summary = document.querySelector('.error-summary');
        if (!summary) {
            summary = document.createElement('div');
            summary.className = 'error-summary';
            summary.setAttribute('role', 'alert');
            summary.setAttribute('aria-live', 'assertive');
            form.insertAdjacentElement('beforebegin', summary);
        }

        const titolo = errori.length === 1 ? '1 errore' : errori.length + ' errori';
        summary.innerHTML = `
            <h2><span aria-hidden="true">⚠</span> Si sono verificati ${titolo}</h2>
            <ul>${errori.map(e => `<li>${e.msg}</li>`).join('')}</ul>
        `;

        // Mostra errore accanto al campo
        errori.forEach(function ({ field, msg }) {
            field.classList.add('form-input--error');
            field.setAttribute('aria-invalid', 'true');

            const errorId = field.id + '-error-js';
            let errorEl = document.getElementById(errorId);
            if (!errorEl) {
                errorEl = document.createElement('span');
                errorEl.id = errorId;
                errorEl.className = 'form-error';
                errorEl.setAttribute('role', 'alert');
                field.parentElement.insertAdjacentElement('afterend', errorEl);
            }
            errorEl.textContent = msg;
            field.setAttribute('aria-describedby', errorId);
        });
    }

    function clearErrors() {
        document.querySelectorAll('.form-input--error').forEach(function (el) {
            el.classList.remove('form-input--error');
            el.removeAttribute('aria-invalid');
        });
        document.querySelectorAll('[id$="-error-js"]').forEach(function (el) {
            el.remove();
        });
    }

})();