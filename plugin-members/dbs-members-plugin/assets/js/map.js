jQuery(document).ready(function($){
    var map = L.map('dbs-members-map').setView([0,0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap'
    }).addTo(map);
    $.getJSON(dbsMembersMap.rest_url, function(data){
        data.forEach(function(item){
            L.marker([item.lat, item.lng]).addTo(map)
                .bindPopup(item.name);
        });
    });
});
