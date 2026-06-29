// ============================================================
// admin_dashboard.js – Filtro live, conferma annulla, sort tabella
// ============================================================

(function () {
    'use strict';

    // ============================================================
    // 1. CONFERMA prima di annullare un appuntamento
    // ============================================================
    document.querySelectorAll('.form-annulla').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const nome = this.getAttribute('data-nome') || 'questo paziente';
            const ok = window.confirm(
                'Sei sicuro di voler annullare l\'appuntamento di ' + nome + '?\n\n' +
                'Questa operazione non può essere annullata.'
            );
            if (!ok) e.preventDefault();
        });
    });

    // ============================================================
    // 2. FILTRO LIVE lato client (filtra le righe della tabella
    //    mentre si digita, senza ricaricare la pagina)
    // ============================================================
    const inputRicerca = document.getElementById('q');
    const tbody        = document.querySelector('#tabella-prenotazioni tbody');
    const contatore    = document.querySelector('[aria-live="polite"]');

    if (inputRicerca && tbody) {
        inputRicerca.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const righe = tbody.querySelectorAll('tr');
            let visibili = 0;

            righe.forEach(function (riga) {
                const testo = riga.textContent.toLowerCase();
                const mostra = query === '' || testo.includes(query);
                riga.hidden = !mostra;
                if (mostra) visibili++;
            });

            // Aggiorna il contatore accessibile
            if (contatore) {
                contatore.textContent = visibili + ' risultati trovati';
            }

            // Mostra/nascondi messaggio nessun risultato
            let emptyMsg = document.getElementById('no-results-live');
            if (visibili === 0 && query !== '') {
                if (!emptyMsg) {
                    emptyMsg = document.createElement('tr');
                    emptyMsg.id = 'no-results-live';
                    emptyMsg.innerHTML = '<td colspan="7" class="text-center text-muted" style="padding:var(--space-32);">Nessun risultato per "' + escapeHtml(query) + '"</td>';
                    tbody.appendChild(emptyMsg);
                } else {
                    emptyMsg.querySelector('td').textContent = 'Nessun risultato per "' + query + '"';
                    emptyMsg.hidden = false;
                }
            } else if (emptyMsg) {
                emptyMsg.hidden = true;
            }
        });
    }

    // ============================================================
    // 3. ORDINAMENTO COLONNE (click sulle intestazioni)
    // ============================================================
    const table = document.getElementById('tabella-prenotazioni');

    if (table) {
        let sortCol = 3;   // colonna data (default)
        let sortAsc = false;

        document.querySelectorAll('.sort-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const col = parseInt(this.getAttribute('data-col'), 10);

                if (sortCol === col) {
                    sortAsc = !sortAsc;
                } else {
                    sortCol = col;
                    sortAsc = true;
                }

                sortTable(col, sortAsc);
                updateSortIcons(this, sortAsc);
            });
        });

        function sortTable(colIndex, ascending) {
            const rows  = Array.from(tbody.querySelectorAll('tr:not(#no-results-live)'));
            const sorted = rows.sort(function (a, b) {
                const aVal = a.cells[colIndex]?.textContent.trim() ?? '';
                const bVal = b.cells[colIndex]?.textContent.trim() ?? '';

                // Prova ordinamento numerico
                const aNum = parseFloat(aVal);
                const bNum = parseFloat(bVal);
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return ascending ? aNum - bNum : bNum - aNum;
                }

                // Ordinamento stringa
                return ascending
                    ? aVal.localeCompare(bVal, 'it')
                    : bVal.localeCompare(aVal, 'it');
            });

            sorted.forEach(function (row) { tbody.appendChild(row); });
        }

        function updateSortIcons(activeBtn, ascending) {
            document.querySelectorAll('.sort-btn').forEach(function (btn) {
                const icon = btn.querySelector('[aria-hidden]');
                const th   = btn.closest('th');
                if (btn === activeBtn) {
                    if (icon) icon.textContent = ascending ? '↑' : '↓';
                    th?.setAttribute('aria-sort', ascending ? 'ascending' : 'descending');
                } else {
                    if (icon) icon.textContent = '↕';
                    th?.setAttribute('aria-sort', 'none');
                }
            });
        }
    }

    // ============================================================
    // Utility: escape HTML per output sicuro nel DOM
    // ============================================================
    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})();