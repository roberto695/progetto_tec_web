// ============================================================
// dashboard.js – Modifica dati personali e prenotazioni
// ============================================================

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggle-modifica');
        const formModifica = document.getElementById('form-modifica');
        const datiVis = document.getElementById('dati-visualizzazione');
        const annullaBtn = document.getElementById('annulla-modifica');

        if (!toggleBtn || !formModifica || !datiVis) return;

        function mostraForm(mostra) {
            if (mostra) {
                formModifica.classList.add('is-visible');
                datiVis.classList.add('is-hidden');
                toggleBtn.textContent = 'Nascondi modifica';
                toggleBtn.setAttribute('aria-expanded', 'true');
                
                const successAlert = document.querySelector('.alert--success');
                    if (successAlert) {
                        successAlert.remove();
                    }

                // Focus sul primo campo del form
                const primoCampo = formModifica.querySelector('input:not([type="hidden"])');
                if (primoCampo) {
                    setTimeout(function() {
                        primoCampo.focus();
                    }, 100);
                }
            } else {
                formModifica.classList.remove('is-visible');
                datiVis.classList.remove('is-hidden');
                toggleBtn.textContent = 'Modifica dati';
                toggleBtn.setAttribute('aria-expanded', 'false');
                // Riporta il focus al pulsante
                toggleBtn.focus();
                clearErrors();
            }
        }

        // Mostra/Nascondi al click del pulsante
        toggleBtn.addEventListener('click', function () {
            const visibile = formModifica.classList.contains('is-visible');
            mostraForm(!visibile);
        });

        // Annulla: chiudi il form senza salvare
        if (annullaBtn) {
            annullaBtn.addEventListener('click', function () {
                mostraForm(false);
            });
        }

        // Se ci sono errori di salvataggio (PHP), mostra il form già aperto
        if (typeof mostraFormErrore !== 'undefined' && mostraFormErrore === true) {
            mostraForm(true);
        }

        // Premere ESC chiude il form
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && formModifica.classList.contains('is-visible')) {
                mostraForm(false);
            }
        });

    const form = document.getElementById('form-modifica-dati');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            clearErrors();

            const errori = [];
            const email = document.getElementById('edit-email');
            const telefono = document.getElementById('edit-telefono');

            // 1. Email: obbligatoria e valida
            const emailVal = email.value.trim();
            if (emailVal === '') {
                errori.push({ field: email, msg: "L'indirizzo email è obbligatorio." });
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                errori.push({ field: email, msg: "L'indirizzo email non è valido." });
            }

            // 2. Telefono: se compilato, deve essere di 10 cifre
            const telVal = telefono.value.trim();
            if (telVal !== '' && !/^\d{10}$/.test(telVal)) {
                errori.push({ field: telefono, msg: 'Il numero di telefono deve essere composto da 10 cifre.' });
            }

            // 3. Nome e Cognome: non vengono controllati (opzionali)

            if (errori.length > 0) {
                e.preventDefault();
                showErrors(errori);
                errori[0].field.focus();
                return false;
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
            <ul aria-label="Elenco degli errori">${errori.map(e => `<li>${e.msg}</li>`).join('')}</ul>
            `;

            // Mostra gli errori sotto i singoli campi
            errori.forEach(function ({ field, msg }) {
                if (field.type === 'checkbox') {
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
            document.querySelectorAll('.form-input--error').forEach(function (el) {
                el.classList.remove('form-input--error');
                el.removeAttribute('aria-invalid');
            });
            document.querySelectorAll('.form-error').forEach(function (el) {
                el.remove();
            });
            document.querySelectorAll('.error-summary').forEach(function (el) {
            el.remove();
            });
        }
    });
})();