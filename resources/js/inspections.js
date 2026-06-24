// Inspections Page Javascript
function syncOdorField(checkbox) {
    const valInput = document.getElementById('bau_tidak_sedap_val');
    if (valInput) {
        valInput.value = checkbox.checked ? 'ya' : 'tidak';
    }
}

function updateFollowUpCategory(cleanlinessVal) {
    const statusInput = document.getElementById('status_tindak_lanjut');
    const displayInput = document.getElementById('status_tindak_lanjut_display');
    if (!statusInput || !displayInput) return;

    if (cleanlinessVal === 'baik') {
        statusInput.value = 'aman';
        displayInput.value = 'Aman (Tidak perlu tindak lanjut)';
    } else if (cleanlinessVal === 'cukup') {
        statusInput.value = 'perlu dibersihkan';
        displayInput.value = 'Perlu dibersihkan segera (Pembersihan)';
    } else if (cleanlinessVal === 'buruk') {
        statusInput.value = 'perlu perbaikan';
        displayInput.value = 'Perlu perbaikan segera (Kerusakan Fisik)';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Initial sync on page load based on selected radio
    const selectedRadio = document.querySelector('input[name="kondisi_kebersihan"]:checked');
    if (selectedRadio) {
        updateFollowUpCategory(selectedRadio.value);
    }

    // Sync kebersihan radio card icons
    document.querySelectorAll('input[name="kondisi_kebersihan"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.radio-card').forEach(card => {
                const icon = card.querySelector('.material-symbols-outlined');
                if (icon) {
                    icon.textContent = 'radio_button_unchecked';
                    icon.style.color = '#94a3b8';
                }
            });
            
            const activeCard = this.nextElementSibling;
            if (activeCard) {
                const activeIcon = activeCard.querySelector('.material-symbols-outlined');
                if (activeIcon) {
                    activeIcon.textContent = 'check_circle';
                    activeIcon.style.color = '#1a56db';
                }
            }

            // Automate follow up category update
            updateFollowUpCategory(this.value);
        });
    });

    const odorSwitch = document.getElementById('odorSwitch');
    if (odorSwitch) {
        odorSwitch.addEventListener('change', function() {
            syncOdorField(this);
        });
    }
});
