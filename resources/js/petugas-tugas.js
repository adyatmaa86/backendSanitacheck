document.addEventListener('DOMContentLoaded', function() {
    // Sync odor switches
    document.querySelectorAll('[id^="odorSwitch_"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const taskId = this.id.replace('odorSwitch_', '');
            const valInput = document.getElementById('bau_val_' + taskId);
            if (valInput) {
                valInput.value = this.checked ? 'ya' : 'tidak';
            }
        });
    });
});
