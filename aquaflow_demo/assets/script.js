function confirmDeactivate() {
    return confirm('Voulez-vous vraiment désactiver votre compte ?');
}


// Filtre simple des tableaux citoyens / plombiers
var searchInputs = document.querySelectorAll('.table-search');
searchInputs.forEach(function(input) {
    input.addEventListener('input', function() {
        var tableBody = document.getElementById(input.getAttribute('data-search'));
        var rows = tableBody.querySelectorAll('tr');
        var text = input.value.toLowerCase();
        rows.forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(text) ? '' : 'none';
        });
    });
});
