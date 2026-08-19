// ============================================================
// login.js – Validazione client-side login e toggle password
// ============================================================

(function () {
    'use strict';

    // Toggle mostra/nascondi password
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
    
    // Validazione client-side al submit
    const form = document.getElementById('form-login');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const errori = [];

        const cf = document.getElementById('cf');
        const pwd = document.getElementById('password');

        clearErrors();

        // Valida CF
        const cfVal = cf.value.trim().toUpperCase();
        const reservedCF = ['admin', 'user'];
        if (cfVal === '') {
            errori.push({ field: cf, msg: 'Il Codice Fiscale è obbligatorio.' });
        } else if (!reservedCF.includes(cfVal.toLowerCase()) && !/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/.test(cfVal)) {
            errori.push({ field: cf, msg: 'Codice Fiscale o password non corretti.' });
        }

        // Valida password
        const pwdVal = pwd.value;
        if (pwdVal === '') {
            errori.push({ field: pwd, msg: 'La password è obbligatoria.' });
        } else if (pwdVal.length < 4) {
            errori.push({ field: pwd, msg: 'La password deve contenere almeno 4 caratteri.' });
        }

        if (errori.length > 0) {
            e.preventDefault();
            showErrors(errori);
            errori[0].field.focus();
        }
    });

    function showErrors(errori) {
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
            <ul aria-label="Elenco degli errori">${errori.map(e => `<li>${e.msg}</li>`).join('')}</ul>
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
        document.querySelectorAll('.form-input--error').forEach(el => {
            el.classList.remove('form-input--error');
            el.removeAttribute('aria-invalid');
        });
        document.querySelectorAll('[id$="-error-js"]').forEach(el => el.remove());
        const summary = form ? form.querySelector('.error-summary') : null;
        if (summary) summary.remove();
    }

})();