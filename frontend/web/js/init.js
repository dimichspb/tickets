$(document).ready(function() {

    var apiUrl1 = "http://www.travelpayouts.com/whereami";
    var apiUrl2 = "http://api.biletracker.com/v1/locations";

    $.ajax({
        url: apiUrl1,
        data: {},
        dataType: "json",
        success: function (data) {
            console.log(data)
            $.ajax({
                url: apiUrl2,
                data: {
                    'iata': data.iata
                },
                dataType: "json",
                success: function (data) {
                    console.log(data)
                    $("#origin-input").typeahead('val',data.name);
                }
            });
        }
    });
});
