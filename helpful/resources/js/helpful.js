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

    var params  = "action=feedback"
                + "&id=" + jQuery("#helpful_id").val()
                + "&type=" + jQuery("#helpful_type").val()
                + "&nonce=" + jQuery("#helpful_nonce").val()
                + "&responseName=" + name
                + "&response=" + response
                + "&email=" + email;

    jQuery.getJSON('//freegeoip.net/json/?callback=?', function(data) {
        params += "&country=" + escape(data["country_name"]);
        params += "&ipaddr=" + escape(data["ip"]);
        console.log(data);
        jQuery.ajax({
        url: jQuery("#helpful_url").val(),
        data: params,
        method: 'POST',
        success: function( data, textStatus, jqXHR ){
                if(name == ""){
                    jQuery("#helpful_event").attr("data-helpful-response", response);
                }else{
                    jQuery("#helpful_event").attr("data-helpful-comment", response);
                }
                jQuery("#click0").children().fadeOut('fast');
                jQuery("#click1").children().fadeOut('fast');
                if(next.indexOf("#") == 0) jQuery(next).fadeIn('slow');

                helpful_fireEvent();
            }
        });
    });
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

    if(gaType == "universal"){
        try{
            if(gaTracking.indexOf(",event,") != -1){
                ga('send', 'event', category, event, response);
                console.log("Done! " + gaType + " for " + gaTracking + " category, event, response: " + category + ", " + event + ", " + response);
            }
            if(gaTracking.indexOf(",custom,") != -1){
                var metric  = response == "yes" ? "metric1" : "metric2";
                ga("set", {
                  "dimension1": event,
                  metric: 1
                });
                ga('send', 'pageview');
                console.log("Done! " + gaType + " for " + gaTracking + " metric, event: " + metric + ", " + event);
            }

        }catch(error){
            console.log("Error in " + gaType + " for " + gaTracking + " = " + error);
        }
    }else{
        try{
            _gaq.push(['_trackEvent', category, event, response]);
            console.log("Done! " + gaType + " for " + gaTracking + " category, event, response: " + category + ", " + event + ", " + response);
        }catch(error){
            console.log("Error in " + gaType + " for " + gaTracking + " = " + error);
        }
    }

    try{
        if(gaTagMgr == 1){
            dataLayer.push({
                'pageTitle': category,
                'pageTitle': event,
                'pageTitle': comment,
            });
            console.log("Done! gaTagMgr");
        }
    }catch(error){
            console.log("Error in gaTagMgr = " + error);
    }
}

