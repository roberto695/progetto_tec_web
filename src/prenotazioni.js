// ============================================================
// prenotazione.js – Riepilogo dinamico + validazione client
// ============================================================

(function () {
    'use strict';

    // Dati degli slot occupati passati dal PHP via attributo data-*
    // (letti dai radio button disabled)

    // Mappa data -> label leggibile (dal DOM)
    function buildDataMap() {
        const map = {};
        document.querySelectorAll('input[name="data_esame"]').forEach(function (radio) {
            const label = radio.nextElementSibling; // .day-label
            if (label) {
                const dow   = label.querySelector('.day-label__dow')?.textContent  ?? '';
                const num   = label.querySelector('.day-label__num')?.textContent  ?? '';
                const month = label.querySelector('.day-label__month')?.textContent ?? '';
                map[radio.value] = dow + ' ' + num + ' ' + month;
            }
        });
        return map;
    }

    const dataMap = buildDataMap();

    // Riferimenti al riepilogo
    const riepilogoData = document.getElementById('riepilogo-data');
    const riepilogoOra  = document.getElementById('riepilogo-ora');

    // Aggiorna il riepilogo quando si sceglie un giorno
    document.querySelectorAll('.day-label').forEach(function (label) {
        // Rendi focusabile
        label.setAttribute('tabindex', '0');
        
        // Aggiungi role per accessibilità
        label.setAttribute('role', 'radio');
        
        label.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const radio = document.getElementById(this.getAttribute('for'));
                if (radio && !radio.disabled) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                    
                    // Aggiorna il riepilogo data
                    if (riepilogoData) {
                        const dow = this.querySelector('.day-label__dow')?.textContent ?? '';
                        const num = this.querySelector('.day-label__num')?.textContent ?? '';
                        const month = this.querySelector('.day-label__month')?.textContent ?? '';
                        riepilogoData.textContent = dow + ' ' + num + ' ' + month;
                    }
                    
                    // Aggiorna gli orari disponibili
                    aggiornaOrari(radio.value);
                    
                    // Focus sul primo orario disponibile
                    const primoOrario = document.querySelector('input[name="ora_esame"]:not(:disabled)');
                    if (primoOrario) {
                        const labelOrario = document.querySelector('label[for="' + primoOrario.id + '"]');
                        if (labelOrario) labelOrario.focus();
                    }
                }
            }
        });
    });

    // ✅ AGGIUNTA: Gestione navigazione tastiera per orari
    document.querySelectorAll('.time-option label').forEach(function (label) {
        // Rendi focusabile
        label.setAttribute('tabindex', '0');
        
        // Aggiungi role per accessibilità
        label.setAttribute('role', 'radio');
        
        label.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const radio = document.getElementById(this.getAttribute('for'));
                if (radio && !radio.disabled) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                    
                    // Aggiorna il riepilogo ora
                    if (riepilogoOra) {
                        riepilogoOra.textContent = radio.value;
                    }
                }
            }
        });
    });

    // Aggiorna il riepilogo quando si sceglie un giorno
    document.querySelectorAll('input[name="data_esame"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (riepilogoData) {
                riepilogoData.textContent = dataMap[this.value] ?? this.value;
            }
            // Aggiorna disponibilità orari per la data scelta (visivo)
            aggiornaOrari(this.value);
        });
    });

    // Aggiorna il riepilogo quando si sceglie un orario
    document.querySelectorAll('input[name="ora_esame"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (riepilogoOra) {
                riepilogoOra.textContent = this.value;
            }
        });
    });

    // Imposta il valore iniziale dell'ora nel riepilogo
    const oraChecked = document.querySelector('input[name="ora_esame"]:checked');
    if (oraChecked && riepilogoOra) {
        riepilogoOra.textContent = oraChecked.value;
    }

    // Navigazione tra orari con frecce
    document.addEventListener('keydown', function (e) {
        // Solo se stiamo navigando sugli orari
        const target = e.target;
        if (!target.closest('.time-option')) return;
        
        const orari = document.querySelectorAll('.time-option label:not([aria-disabled="true"])');
        let currentIndex = -1;
        
        orari.forEach(function (el, index) {
            if (el === target) currentIndex = index;
        });
        
        if (currentIndex === -1) return;
        
        let newIndex = currentIndex;
        if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
            e.preventDefault();
            newIndex = Math.min(currentIndex + 1, orari.length - 1);
        } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
            e.preventDefault();
            newIndex = Math.max(currentIndex - 1, 0);
        }
        
        if (newIndex !== currentIndex) {
            orari[newIndex].focus();
            // Attiva il radio corrispondente
            const radio = document.getElementById(orari[newIndex].getAttribute('for'));
            if (radio && !radio.disabled) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
                if (riepilogoOra) {
                    riepilogoOra.textContent = radio.value;
                }
            }
        }
    });

    // ============================================================
    // Aggiornamento disponibilità orari al cambio giorno
    // Gli slot occupati sono resi disponibili/disabilitati tramite
    // un attributo data-occupati sull'elemento griglia, popolato
    // inline dal PHP (vedi sotto). Se non presente, funzione no-op.
    // ============================================================
    function aggiornaOrari(dataSelezionata) {
        const griglia = document.getElementById('griglia-orari');
        if (!griglia) return;

        // Leggi JSON degli slot occupati per giorno (iniettato dal PHP)
        const rawJson = griglia.getAttribute('data-occupati') || '{}';
        let occupati = {};
        try { occupati = JSON.parse(rawJson); } catch (e) { return; }

        const slotsOccupatiOggi = occupati[dataSelezionata] || [];

        let primoLibero = null;

        document.querySelectorAll('input[name="ora_esame"]').forEach(function (radio) {
            const ora        = radio.value + ':00';
            const isOccupato = slotsOccupatiOggi.includes(ora);
            const wrapper    = radio.closest('.time-option');
            const label      = wrapper ? wrapper.querySelector('label') : null;

            radio.disabled = isOccupato;

            if (wrapper) {
                if (isOccupato) {
                    wrapper.classList.add('time-option--disabled');
                    radio.checked = false;
                    if (label) {
                        label.setAttribute('aria-disabled', 'true');
                        label.setAttribute('tabindex', '-1');
                    }
                } else {
                    wrapper.classList.remove('time-option--disabled');
                    if (label) {
                        label.removeAttribute('aria-disabled');
                        label.setAttribute('tabindex', '0');
                    }
                    if (!primoLibero) primoLibero = radio;
                }
            }

            // Aggiorna aria-label
            radio.setAttribute('aria-label',
                radio.value + (isOccupato ? ' – non disponibile' : '')
            );
        });

        // Seleziona il primo slot libero
        if (primoLibero) {
            primoLibero.checked = true;
            if (riepilogoOra) riepilogoOra.textContent = primoLibero.value;
        } else {
            if (riepilogoOra) riepilogoOra.textContent = '—';
        }
    }

    // ============================================================
    // Validazione client-side al submit
    // ============================================================
    const form = document.getElementById('form-prenotazione');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const errori = [];

        const dataScelta = document.querySelector('input[name="data_esame"]:checked');
        const oraScelta  = document.querySelector('input[name="ora_esame"]:checked');

        if (!dataScelta) {
            errori.push('Seleziona un giorno per il prelievo.');
        }

        if (!oraScelta) {
            errori.push('Seleziona un orario per il prelievo.');
        } else if (oraScelta.disabled) {
            errori.push('L\'orario selezionato non è disponibile. Scegli un altro orario.');
        }

        if (errori.length > 0) {
            e.preventDefault();
            mostraErrori(errori);
            // Focus sul primo gruppo di selezione
            const primo = document.querySelector('input[name="data_esame"]');
            if (primo) primo.focus();
        }
    });

    function mostraErrori(errori) {
        let summary = document.querySelector('.error-summary');
        if (!summary) {
            summary = document.createElement('div');
            summary.className = 'error-summary';
            summary.setAttribute('role', 'alert');
            summary.setAttribute('aria-live', 'assertive');
            form.insertAdjacentElement('beforebegin', summary);
        }
        const n = errori.length;
        summary.innerHTML = `
            <h2><span aria-hidden="true">⚠</span> Si ${n === 1 ? 'è verificato 1 errore' : 'sono verificati ' + n + ' errori'}</h2>
            <ul>${errori.map(e => `<li>${e}</li>`).join('')}</ul>
        `;
        summary.focus?.();
    }

})();