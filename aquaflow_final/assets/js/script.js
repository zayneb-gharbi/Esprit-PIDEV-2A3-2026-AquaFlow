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
