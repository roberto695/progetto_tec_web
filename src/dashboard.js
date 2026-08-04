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
    });

})();