function useriata(json) {
    console.log(json);
    $("#origin-input").typeahead('val',json.name);
    $("#origin-input").trigger('typeahead:select');
}

$(document).ready(function() {

    var apiUrl = "http://www.travelpayouts.com/whereami";
    var locale = "ru";
    var callback = "useriata";
    //var userIP = "145.255.238.136";
    var query = {
        'locale': locale,
        'callback': callback,
        //'ip': userIP,
    };
    $.ajax({
        type:"GET",
        //Location of the cfc
        url: apiUrl,
        //Function name and url variables to send
        data: query,
        //Set to JSONP here
        dataType:"jsonp",
        //This defaults to true if you are truly working cross-domain
        //But you can change this for force JSONP if testing on same server
        crossDomain:true,
        //Function run on success takes the returned jsonp object and reads values.
    });
});
