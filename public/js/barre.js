$(document).ready(function() {
    
    $('form').on('submit', function(e) {
        e.preventDefault();
    });
    var searchInput = $("#searchInput");
    var tableBody = $("table tbody");

    searchInput.on("keyup", function () {
        var searchTerm = searchInput.val();
        
        $.get("/historique/search?term=" + searchTerm, function(data) {
            tableBody.empty(); // nettoyer le contenu actuel

            data.forEach(function(item) {
                tableBody.append(`
                    <tr>
                        <th class="text-center" scope="row">${item.id}</th>
                        <td class="text-center">${item.nsimul}</td>
                        <td class="text-center">${item.temperature}</td>
                        <td class="text-center">${item.velocity}</td>
                        <td class="text-center">${item.flow}</td>
                        <td class="text-center">${item.energy}</td>
                        <td class="text-center">${item.failure}</td>
                        <td class="text-center">${item.start}</td>
                        <td class="text-center">${item.duration}</td>
                        <td class="text-center">${item.moduleId}</td>
                    </tr>
                `);
            });
        });
    });
});
