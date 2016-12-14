<form action="" method="post">

<h2 class="helpful"><?php _e('General', __HELPFUL_PLUGIN_SLUG__);?></h2>

<?php
    $settings       = self::getSettings();
?>

<table class="helpful_settings">
    <tr>
        <th><?php _e('Enable for', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td>
            <?php
                $enableFor  = $settings["types"];
                if(!$enableFor) $enableFor  = array();
                $types      = array(
                                "post"     => "Posts",
                                "page"     => "Pages",
                );
                foreach($types as $type=>$tag){
            ?>
                <input type="checkbox" name="types[]" id="<?php echo $type;?>" value="<?php echo $type;?>" <?php echo in_array($type, $enableFor) ? "checked" : ""?>>
                <label for="<?php echo $type;?>"><?php _e($tag, __HELPFUL_PLUGIN_SLUG__);?></label>
            <?php
                }
            ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Helpful? text', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="text" name="title" value="<?php echo esc_attr($settings["title"]);?>"></td>
    </tr>
    <tr>
        <th><?php _e('Positive response button text', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="text" name="yes_title" value="<?php echo esc_attr($settings["yes_title"]);?>"></td>
    </tr>
    <tr>
        <th><?php _e('Negative response button text', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="text" name="no_title" value="<?php echo esc_attr($settings["no_title"]);?>"></td>
    </tr>
    <tr>
        <th><?php _e('Allow negative feedback', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="checkbox" name="no_allow" value="1" <?php echo $settings["no_allow"] == 1 ? "checked" : "";?>></td>
    </tr>
    <tr>
        <th><?php _e('Negative feedback request text', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="text" name="no_text" value="<?php echo esc_attr($settings["no_text"]);?>"></td>
    </tr>
    <tr>
        <th><?php _e('Allow positive feedback', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="checkbox" name="yes_allow" value="1" <?php echo $settings["yes_allow"] == 1 ? "checked" : "";?>></td>
    </tr>
    <tr>
        <th><?php _e('Positive feedback request text', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="text" name="yes_text" value="<?php echo esc_attr($settings["yes_text"]);?>"></td>
    </tr>
    <tr>
        <th><?php _e('Thank you message', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="text" name="thanks" value="<?php echo esc_attr($settings["thanks"]);?>"></td>
    </tr>
    <tr>
        <th><?php _e('Enter Email text', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td>
            <input type="text" name="email" value="<?php echo @esc_attr($settings["email"]);?>">
            <p class="description">If this is empty, then an email prompt will not show</p>
        </td>
    </tr>
</table>

<?php $ga   = $settings["ga"]; ?>

<h2 class="helpful"><?php _e('Google Analytics', __HELPFUL_PLUGIN_SLUG__);?></h2>
<p class="description"><?php _e("You need to already have Google Analytics tracking on your website for this to work - this just adds tracking for the feedback", __HELPFUL_PLUGIN_SLUG__);?></p>
<table class="helpful_settings ga">
    <tr>
        <th><?php _e('Enable Analytics', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="checkbox" name="ga" value="1" <?php echo $ga["ga"] == 1 ? "checked" : ""?> onclick="helpful_toggleGA(this)"></td>
        <td></td>
    </tr>
    <tr class="<?php echo $ga["ga"] == 1 ? "ga_enabled" : "ga_disabled"?>">
        <th><?php _e('Google Analytics Type', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td>
            <input type="radio" name="ga_type" id="ga_type_universal" value="universal" <?php echo $ga["ga_type"] == "universal" ? "checked" : ""?> onclick="helpful_toggleGAtracking(this, ['#ga_tracking_custom', '#ga_tag'], false)">
            <label for="ga_type_universal"><?php _e('Universal', __HELPFUL_PLUGIN_SLUG__);?></label>
            <p class="description"><?php _e('(current standard)', __HELPFUL_PLUGIN_SLUG__);?></p>
        </td>
        <td>
            <input type="radio" name="ga_type" id="ga_type_classic" value="classic" <?php echo $ga["ga_type"] == "classic" ? "checked" : ""?> onclick="helpful_toggleGAtracking(this, ['#ga_tracking_custom', '#ga_tag'], true)">
            <label for="ga_type_classic"><?php _e('Classic', __HELPFUL_PLUGIN_SLUG__);?></label>
            <p class="description"><?php _e('(deprecated)', __HELPFUL_PLUGIN_SLUG__);?></p>
        </td>
    </tr>
    <tr class="<?php echo $ga["ga"] == 1 ? "ga_enabled" : "ga_disabled"?>">
        <th><?php _e('Google Tag Manager', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="checkbox" name="ga_tag" id="ga_tag" value="1" <?php echo @$ga["ga_tag"] == 1 ? "checked" : ""?> <?php echo $settings["ga"]["ga_type"] == "classic" ? "disabled" : ""?></td>
        <td></td>
    </tr>
    <tr class="<?php echo $ga["ga"] == 1 ? "ga_enabled" : "ga_disabled"?>">
        <th><?php _e('Tracking Method', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td>
            <input type="hidden" name="ga_tracking[]" id="ga_tracking_event" value="event">
            <input type="checkbox" checked disabled>
            <label for="ga_tracking_event"><?php _e('Event Tracking', __HELPFUL_PLUGIN_SLUG__);?></label>
            <p class="description"><?php _e('(no additional setup required)', __HELPFUL_PLUGIN_SLUG__);?></p>
        </td>
<?php
    if(__HELPFUL_ENABLE_GA_CUSTOM__){
?>
        <td>
            <input type="checkbox" name="ga_tracking[]" id="ga_tracking_custom" value="custom" <?php echo in_array("custom", $ga["ga_tracking"]) ? "checked" : ""?> <?php echo $settings["ga"]["ga_type"] == "classic" ? "disabled" : ""?>>
            <label for="ga_tracking_custom"><?php _e('Custom Metrics & Dimensions', __HELPFUL_PLUGIN_SLUG__);?></label>
            <p class="description"><?php _e('(requires additional setup)', __HELPFUL_PLUGIN_SLUG__);?> <a href="https://support.google.com/analytics/answer/2709828?hl=en" target="_new"><?php _e('Click here', __HELPFUL_PLUGIN_SLUG__);?></a></p>
        </td>
<?php
    }
?>
    </tr>
</table>

<?php $style   = @$settings["style"]; ?>

<h2 class="helpful"><?php _e('Styling', __HELPFUL_PLUGIN_SLUG__);?></h2>

<table class="helpful_settings">
    <tr>
        <th><?php _e('Helpful? Background', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input class="color" name="bg_color" value="<?php echo @$style["bg_color"];?>"></td>
    </tr>
    <tr>
        <th><?php _e('Positive button color', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input class="color" name="yes_color" value="<?php echo @$style["yes_color"];?>"></td>
    </tr>
    <tr>
        <th><?php _e('Negative button color', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input class="color" name="no_color" value="<?php echo @$style["no_color"];?>"></td>
    </tr>
    <tr>
        <th><?php _e('Positive text color', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input class="color" name="yes_title_color" value="<?php echo @$style["yes_title_color"];?>"></td>
    </tr>
    <tr>
        <th><?php _e('Negative text color', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input class="color" name="no_title_color" value="<?php echo @$style["no_title_color"];?>"></td>
    </tr>
    <tr>
        <th><?php _e('Helpful? text size', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td>
            <input name="title_size" value="<?php echo @$style["title_size"];?>">
            <p class="description"><?php _e('pixels', __HELPFUL_PLUGIN_SLUG__);?></p>
        </td>
    </tr>
    <tr>
        <th><?php _e('CSS', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td>
            <textarea rows="10" class="large-text" name="css_advanced"><?php echo esc_html( @$style["css_advanced"] ); ?></textarea>
            <p class="description"><?php _e('Advanced users: add CSS styles', __HELPFUL_PLUGIN_SLUG__);?></p>
        </td>
    </tr>
</table>

<?php submit_button(__('Save', __HELPFUL_PLUGIN_SLUG__));?>
</form>