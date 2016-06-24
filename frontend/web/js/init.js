$(document).ready(function() {

    var apiUrl = "http://api.biletracker.com/v1/locations";

    $.ajax({
        url: apiUrl,
        dataType: "jsonp",
        success: function (data) {
            console.log(data)
            $("#origin-input").typeahead('val',data.name);
        }
    });
});
