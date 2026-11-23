<script>
// recherche dans le tableau (meme logique que aquaflow_final_mvc_v2)
var search = document.getElementById('search');
if (search) {
    search.addEventListener('keyup', function () {
        var text = search.value.toLowerCase();
        var rows = document.querySelectorAll('#usersTable tbody tr');
        for (var i = 0; i < rows.length; i++) {
            rows[i].style.display = rows[i].textContent.toLowerCase().indexOf(text) >= 0 ? '' : 'none';
        }
    });
}

// modale Bloquer / Debloquer (remplace l'ancien confirm())
(function () {
    var modal = document.getElementById('blockModal');
    if (!modal) return;

    var form        = document.getElementById('blockModalForm');
    var idInput     = document.getElementById('blockUserId');
    var actionInput = document.getElementById('blockAction');
    var title       = document.getElementById('blockModalTitle');
    var text        = document.getElementById('blockModalText');
    var reasonBox   = document.getElementById('blockReasonField');
    var reason      = document.getElementById('blockReason');
    var reasonError = document.getElementById('blockReasonError');
    var confirmBtn  = document.getElementById('blockConfirmBtn');

    function openModal(action, id, name) {
        var isBlock = action === 'block';
        actionInput.value = action;
        idInput.value = id;
        title.textContent = isBlock ? 'Bloquer ce compte' : 'Débloquer ce compte';
        text.textContent = (isBlock ? 'Bloquer' : 'Débloquer') + ' le compte de ' + name + ' ?';
        reasonBox.style.display = isBlock ? '' : 'none';
        reason.value = '';
        reasonError.textContent = '';
        confirmBtn.textContent = isBlock ? 'Bloquer' : 'Débloquer';
        confirmBtn.className = isBlock ? 'btn btn-danger' : 'btn';
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        (isBlock ? reason : confirmBtn).focus();
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    var triggers = document.querySelectorAll('.block-trigger');
    for (var i = 0; i < triggers.length; i++) {
        triggers[i].addEventListener('click', function () {
            openModal(this.getAttribute('data-action'), this.getAttribute('data-id'),
                      this.getAttribute('data-name') || '');
        });
    }

    var closers = modal.querySelectorAll('[data-close-modal]');
    for (var j = 0; j < closers.length; j++) {
        closers[j].addEventListener('click', closeModal);
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    form.addEventListener('submit', function (e) {
        var value = reason.value.trim();
        if (actionInput.value === 'block' && value !== '' && (value.length < 5 || value.length > 255)) {
            e.preventDefault();
            reasonError.textContent = 'Le motif doit contenir entre 5 et 255 caractères.';
            reason.focus();
            return false;
        }
        return true;
    });
})();
</script>
