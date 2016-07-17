$(document).ready(function() {
    ymaps.ready(init);
});

function init() {

    var apiUrl = "http://api.biletracker.com/v1/locations";

    var geolocation = ymaps.geolocation;
    console.log(geolocation);
    $.ajax({
        url: apiUrl,
        data: {
            'name': geolocation.city
        },
        dataType: "json",
        success: function (data) {
            console.log(data)
            $("#origin-input").typeahead('val', geolocation.city);
            $("#request-origin").val(data.id);
        }
    });
}
