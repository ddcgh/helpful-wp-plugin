var ip          = "";
var country     = "";

jQuery(function() {
    jQuery( ".helpful_button" ).bind("click", function(){
        helpful_fireAjax(this);
    });

    jQuery(window).bind("beforeunload", function(){
        //helpful_fireEvent();
    });
});


function helpful_fireAjax(obj){
    var next        = jQuery(obj).attr("data-helpful-next");
    var send        = jQuery(obj).attr("data-helpful-send");
    var name        = jQuery(obj).attr("data-helpful-name");

    if(!name) name  = "";

    var response    = send.indexOf("#") == 0 ? jQuery(send).val() : send;

    var email       = "";

    if(name != ""){
        var email       = jQuery("#email_" + name) ? jQuery("#email_" + name).val() : "";
        if(email != "" && !(email.indexOf("@") > 0 && email.indexOf(".") >= 0)){
            jQuery("#email_alert_" + name).fadeIn("fast");
            return;
        }
    }

    if(name == ""){
        jQuery("#helpful_event").attr("data-helpful-response", response);
    }else
    {
        jQuery("#helpful_event").attr("data-helpful-comment", response);
    }
    jQuery("#click0").children().fadeOut('fast');
    jQuery("#click1").children().fadeOut('fast');
    if(next.indexOf("#") == 0) jQuery(next).fadeIn('slow');

    var params  = "action=feedback"
                + "&id=" + jQuery("#helpful_id").val()
                + "&type=" + jQuery("#helpful_type").val()
                + "&nonce=" + jQuery("#helpful_nonce").val()
                + "&responseName=" + name
                + "&response=" + response
                + "&email=" + email;

    if(name == "")
    {
        jQuery.getJSON('//freegeoip.net/json/', function(data) {
                ip =  data["ip"];
                country =  data["country_name"];
                jQuery.ajax({
                    url: jQuery("#helpful_url").val(),
                    data: params,
                    method: 'POST',
                    success: function( data, textStatus, jqXHR ){
                            helpful_fireEvent();
                        }
                });
        }).fail(function()
                {
                    jQuery.ajax({
                    url: jQuery("#helpful_url").val(),
                    data: params,
                    method: 'POST',
                    success: function( data, textStatus, jqXHR ){
                            helpful_fireEvent();
                        }
                    });
                });
    }
    else
    {
        params += "&ipaddr=" + ip;
        params += "&country=" + country;
        jQuery.ajax({
                url: jQuery("#helpful_url").val(),
                data: params,
                method: 'POST',
                success: function( data, textStatus, jqXHR ){
                        helpful_fireEvent();
                }
        });
    } 
}

function helpful_fireEvent(){
    var response    = jQuery("#helpful_event").attr("data-helpful-response");
    var comment     = jQuery("#helpful_event").attr("data-helpful-comment");

    if(response == ""){
        return;
    }

    var category    = jQuery("#helpful_cat").val();
    var event       = jQuery("#helpful_title").val();

    var gaType      = jQuery("#helpful_ga").attr("data-helpful-ga-type");
    var gaTracking  = jQuery("#helpful_ga").attr("data-helpful-ga-tracking");
    var gaTagMgr    = jQuery("#helpful_ga").attr("data-helpful-ga-tag");

    if(gaTagMgr == 1)
    {
        try{
            dataLayer.push({
                'event': category,
                'pluginName': category,
                'pageTitle': event,
                'response': response
            });
            console.log("Submitted to Google Tag Manager! {event: " + category + ", pluginName: " + category + ", pageTitle: " + event + ", response: " + response + "}");
        }catch(error){
                console.log("Error in Google Tag Manager Submission! Error: " + error);
        }
    }
    else
    {
        if(gaType == "universal"){
            try{
                if(gaTracking.indexOf(",event,") != -1){
                    ga('send', 'event', category, event, response);
                    console.log("Submitted to Google Analytics! Analytics Type: " + gaType + " Tracking: " + gaTracking + " Category, Event, Response: " + category + ", " + event + ", " + response);
                }
            }catch(error){
                console.log("Error in Google Analytics Submission! Type: " + gaType + " Tracking: " + gaTracking + " Error: " + error);
            }
        }
    }
}

