<script>
// Validation cote client : memes regles que ProfileEditController (PR 4)
(function () {
    var form = document.getElementById('profileEditForm');
    if (!form) return;

    var fields = ['prenom', 'nom', 'email', 'telephone', 'zone_service', 'experience'];
    var inputs = {};
    var errors = {};

    fields.forEach(function (name) {
        inputs[name] = document.getElementById(name);
        errors[name] = document.getElementById(name + 'Error');
    });

    function setError(name, message) {
        if (errors[name]) errors[name].textContent = message || '';
        return message === '' || !message;
    }

    function validateField(name) {
        var input = inputs[name];
        if (!input) return true;
        var value = input.value.trim();

        if (name === 'prenom' || name === 'nom') {
            if (value === '') return setError(name, 'Ce champ est obligatoire.');
            if (value.length < 2 || value.length > 50) return setError(name, 'Entre 2 et 50 caractères.');
            if (!/^[\p{L}\p{M}\-' ]+$/u.test(value)) return setError(name, 'Lettres uniquement.');
            return setError(name, '');
        }

        if (name === 'email') {
            if (value === '') return setError(name, 'Ce champ est obligatoire.');
            if (value.length > 150) return setError(name, '150 caractères maximum.');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return setError(name, 'Adresse e-mail invalide.');
            return setError(name, '');
        }

        if (name === 'telephone') {
            var phone = value.replace(/[\s-]/g, '');
            if (phone === '') return setError(name, 'Ce champ est obligatoire.');
            if (!/^(?:\+216|00216)?[2-9][0-9]{7}$/.test(phone)) return setError(name, 'Numéro tunisien invalide.');
            input.value = phone;
            return setError(name, '');
        }

        if (name === 'zone_service') {
            return setError(name, value === '' ? 'Veuillez sélectionner votre zone de service.' : '');
        }

        if (name === 'experience') {
            if (value === '' || !/^\d+$/.test(value) || Number(value) < 0 || Number(value) > 70) {
                return setError(name, 'Entier entre 0 et 70.');
            }
            return setError(name, '');
        }

        return true;
    }

    fields.forEach(function (name) {
        if (!inputs[name]) return;
        inputs[name].addEventListener('blur', function () { validateField(name); });
        inputs[name].addEventListener('input', function () {
            if (errors[name]) errors[name].textContent = '';
        });
    });

    form.addEventListener('submit', function (e) {
        var ok = true;
        fields.forEach(function (name) {
            if (inputs[name] && !validateField(name)) ok = false;
        });
        if (!ok) e.preventDefault();
        return ok;
    });
})();
</script>
