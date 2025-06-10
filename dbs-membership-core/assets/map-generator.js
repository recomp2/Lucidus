function initDbsMap(data, containerId){
    if (typeof google === 'undefined' || !google.maps){
        return;
    }
    var map = new google.maps.Map(document.getElementById(containerId), {
        zoom: 4,
        center: {lat: 39.8283, lng: -98.5795}
    });
    var geocoder = new google.maps.Geocoder();
    data.forEach(function(item){
        var address = item.city + ', ' + item.state;
        geocoder.geocode({address: address}, function(results, status){
            if(status === 'OK'){
                new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                    title: item.name
                });
            }
        });
    });
}
