// ============================================================
// dashboard.js – Modifica dati personali (toggle)
// ============================================================

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggle-modifica');
        const formModifica = document.getElementById('form-modifica');
        const datiVis = document.getElementById('dati-visualizzazione');
        const annullaBtn = document.getElementById('annulla-modifica');

        // Se gli elementi non esistono, esci (es. se non sei in dashboard)
        if (!toggleBtn || !formModifica || !datiVis) return;

        function mostraForm(mostra) {
            if (mostra) {
                formModifica.style.display = 'block';
                datiVis.style.display = 'none';
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
                formModifica.style.display = 'none';
                datiVis.style.display = 'block';
                toggleBtn.textContent = 'Modifica dati';
                toggleBtn.setAttribute('aria-expanded', 'false');
                // Riporta il focus al pulsante
                toggleBtn.focus();
            }
        }

        // Mostra/Nascondi al click del pulsante
        toggleBtn.addEventListener('click', function () {
            const visibile = formModifica.style.display === 'block';
            mostraForm(!visibile);
        });

        // Annulla: chiudi il form senza salvare
        if (annullaBtn) {
            annullaBtn.addEventListener('click', function () {
                mostraForm(false);
            });
        }

        // Se ci sono errori di salvataggio (PHP), mostra il form aperto
        // Il flag è settato da PHP via variabile JS
        if (typeof mostraFormErrore !== 'undefined' && mostraFormErrore === true) {
            mostraForm(true);
        }

        // Accessibilità: premere ESC chiude il form
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && formModifica.style.display === 'block') {
                mostraForm(false);
            }
        });
    });

})();