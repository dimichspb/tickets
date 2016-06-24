$(document).ready(function() {

    var apiUrl1 = "http://www.travelpayouts.com/whereami";
    var responseData = '';


    $.ajax({
        url: apiUrl1,
        data: {},
        dataType: "jsonp",
        success: function (data) {
            console.log(data)
            responseData = data;
        }
    });

    var apiUrl2 = "http://api.biletracker.com/v1/locations";

    $.ajax({
        url: apiUrl2,
        data: responseData,
        callback: '',
        dataType: "jsonp",
        success: function (data) {
            console.log(data)
            $("#origin-input").typeahead('val',data.name);
        }
    });
});
