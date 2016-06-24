$(document).ready(function() {

    var apiUrl1 = "http://www.travelpayouts.com/whereami";

    $.ajax({
        url: apiUrl1,
        data: {},
        dataType: "jsonp",
        success: function (data) {
            console.log(data)
            var iata = data.iata;
        }
    });

    var apiUrl2 = "http://api.biletracker.com/v1/locations";

    $.ajax({
        url: apiUrl2,
        data: data,
        dataType: "jsonp",
        success: function (data) {
            console.log(data)
            $("#origin-input").typeahead('val',data.name);
        }
    });
});
