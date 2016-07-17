$(document).ready(function() {

    ymaps.ready(init);


    var apiUrl1 = "http://www.travelpayouts.com/whereami";
    var apiUrl2 = "http://api.biletracker.com/v1/locations";
/*
    $.ajax({
        url: apiUrl1,
        data: {},
        dataType: "json",
        success: function (data1) {
            console.log(data1)
            $.ajax({
                url: apiUrl2,
                data: {
                    'iata': data1.iata
                },
                dataType: "json",
                success: function (data2) {
                    console.log(data2)
                    $("#origin-input").typeahead('val',data1.name);
                    $("#request-origin").val(data2.id);
                }
            });
        }
    });*/
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
