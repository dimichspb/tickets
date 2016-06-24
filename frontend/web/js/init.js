$(document).ready(function() {

    var apiUrl = "http://api.biletracker.com/v1/locations";

    $.get(apiUrl, function(data){
        $("#origin-input").typeahead('val',data.name);
    })
});
