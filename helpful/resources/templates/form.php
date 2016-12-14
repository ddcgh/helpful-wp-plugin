<?php
    global $post;
    $settings   = self::getSettings();
    if(!$settings) return;
?>
<div class="helpful form">
    <div id="click0">
        <div class="title"><?php _e($settings["title"], __HELPFUL_PLUGIN_SLUG__);?></div>
        <div class="hlp_buttons">
<?php
    $extraClassGTM      = "";
    if($settings["ga"]["ga"] == 1){
        if($settings["ga"]["ga_tag"] == "1"){
            $extraClassGTM  = "custom-met-dim gtm";
        }
?>
            <input type="hidden" id="helpful_ga" data-helpful-ga-type="<?php echo $settings["ga"]["ga_type"];?>" data-helpful-ga-tag="<?php echo $settings["ga"]["ga_tag"];?>" data-helpful-ga-tracking="<?php echo ",".implode(",", $settings["ga"]["ga_tracking"]).",";?>">
<?php
    }
?>
            <input type="hidden" id="helpful_cat" value="<?php echo __HELPFUL_PLUGIN_NAME__;?>">
            <input type="hidden" id="helpful_event" data-helpful-response="" data-helpful-comment="No comment">
            <input type="hidden" id="helpful_title" value="<?php echo htmlspecialchars($post->post_title);?>">
            <input type="hidden" id="helpful_nonce" value="<?php echo wp_create_nonce(__HELPFUL_PLUGIN_SLUG__);?>">
            <input type="hidden" id="helpful_url" value="<?php echo __HELPFUL_AJAX__;?>">
            <input type="hidden" name="helpful_type" id="helpful_type" value="<?php echo $type;?>">
            <input type="hidden" name="helpful_id" id="helpful_id" value="<?php echo $post->ID;?>">
<?php
    if($settings["yes_allow"] == 1){
?>
            <div class="hlp_button"><button class="helpful_button yes <?php echo $extraClassGTM;?>" id="helpful_yes" data-helpful-next="#div_yes" data-helpful-send="yes"><?php _e($settings["yes_title"], __HELPFUL_PLUGIN_SLUG__);?></button></div>
<?php
    }
?>
<?php
    if($settings["no_allow"] == 1){
?>
            <div class="hlp_button"><button class="helpful_button no <?php echo $extraClassGTM;?>" id="helpful_no" data-helpful-next="#div_no" data-helpful-send="no"><?php _e($settings["no_title"], __HELPFUL_PLUGIN_SLUG__);?></button></div>
<?php
    }
?>
        </div>
        <div class="clear"></div>
    </div>
    <div id="click1">
        <div id="div_yes">
            <textarea name="yes_comments" id="yes_comments" class="placeholder_text" placeholder="<?php _e($settings["yes_text"], __HELPFUL_PLUGIN_SLUG__);?>"></textarea>
<?php
    if(@$settings["email"]){
?>
            <div>
                <input type="email" name="email" id="email_yes" placeholder="<?php _e(@$settings["email"], __HELPFUL_PLUGIN_SLUG__);?>">
                <span id="email_alert_yes" class="email_alert" style="display: none"><?php _e("Please enter a valid email ID", __HELPFUL_PLUGIN_SLUG__);?></span>
            </div>
<?php
    }
?>
            <button class="helpful_button" data-helpful-next="#div_thanks" data-helpful-send="#yes_comments" data-helpful-name="yes"><?php _e('Submit', __HELPFUL_PLUGIN_SLUG__);?></button>
        </div>
        <div id="div_no">
            <textarea name="no_comments" id="no_comments" class="placeholder_text" placeholder="<?php _e($settings["no_text"], __HELPFUL_PLUGIN_SLUG__);?>"></textarea>
<?php
    if(@$settings["email"]){
?>
            <div>
                <input type="email" name="email" id="email_no" placeholder="<?php _e(@$settings["email"], __HELPFUL_PLUGIN_SLUG__);?>">
                <span id="email_alert_no" class="email_alert" style="display: none"><?php _e("Please enter a valid email ID", __HELPFUL_PLUGIN_SLUG__);?></span>
            </div>
<?php
    }
?>
            <button class="helpful_button" data-helpful-next="#div_thanks" data-helpful-send="#no_comments" data-helpful-name="no"><?php _e('Submit', __HELPFUL_PLUGIN_SLUG__);?></button>
        </div>
    </div>
    <div id="click2">
        <div id="div_thanks"><?php _e($settings["thanks"], __HELPFUL_PLUGIN_SLUG__);?></div>
    </div>
</div>

<?php include_once __HELPFUL_DIR__ . "resources/includes/styles.php"; ?>
<div style="display:none !important">Powered by PhiStream: www.phistream.com</div>
