$(document).ready(function() {

    var apiUrl1 = "http://www.travelpayouts.com/whereami";
    var apiUrl2 = "http://api.biletracker.com/v1/locations";

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
    });
});
