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
<p class="description"><?php _e("You need to already have Google Analytics tracking on your website for this to work - this just adds tracking for the feedback. We do not support classic Google Analytics since it is deprecated, so your website must be using universal Google Analytics.", __HELPFUL_PLUGIN_SLUG__);?></p>
<table class="helpful_settings ga">
    <tr>
        <th><?php _e('Enable Analytics', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="checkbox" name="ga" value="1" <?php echo $ga["ga"] == 1 ? "checked" : ""?> onclick="helpful_toggleGA(this)"></td>
        <td></td>
    </tr>
    <tr class="<?php echo $ga["ga"] == 1 ? "ga_enabled" : "ga_disabled"?>">
        <th><?php _e('Google Tag Manager', __HELPFUL_PLUGIN_SLUG__);?></th>
        <td><input type="checkbox" name="ga_tag" id="ga_tag" value="1" <?php echo @$ga["ga_tag"] == 1 ? "checked" : ""?> 	</td>
        <td></td>
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
