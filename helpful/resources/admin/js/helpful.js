jQuery(function() {
    jQuery( ".tabs" ).tabs();

    jQuery('.color').wpColorPicker();
});


function helpful_toggleGA(checkbox){
    if(jQuery(checkbox).is(":checked"))
        jQuery(".ga_disabled").removeClass("ga_disabled").addClass("ga_enabled");
    else
        jQuery(".ga_enabled").removeClass("ga_enabled").addClass("ga_disabled");
}

function helpful_toggleGAtracking(radio, idArray, status){
    for(var i = 0; i < idArray.length; i++) jQuery(idArray[i]).prop("disabled", status);
}
