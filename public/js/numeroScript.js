document.addEventListener("DOMContentLoaded", function() {
    var tableBody = document.getElementById('tableBody');
    var rows = tableBody.getElementsByTagName('tr');
    for(var i = 0; i < rows.length; i++) {
        var firstCell = rows[i].getElementsByTagName('th')[0];
        firstCell.innerHTML = i + 1;
    }
});
