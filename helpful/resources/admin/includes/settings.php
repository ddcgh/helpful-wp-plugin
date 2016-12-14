<?php
    $extra      = "";
    if(isset($_REQUEST['sub'])){
        $array  = array();
        foreach($_GET as $k=>$v){
            $array[$k] = $v;
        }
        $extra  = http_build_query($array);
    }
?>
<div class="wrap">
<h2 class="helpful"><?php echo __HELPFUL_PLUGIN_NAME__ . ' ' . __('Settings', __HELPFUL_PLUGIN_SLUG__);?></h2>
<div id="helpful_tabs_nav" class="tabs">
    <ul>
        <li><a href="#tabs-settings"><?php _e('Settings', __HELPFUL_PLUGIN_SLUG__); ?></a></li>
        <li><a href="<?php echo __HELPFUL_RESOURCES__ . "admin/includes/statistics.php?" . $extra?>"><?php _e('Statistics', __HELPFUL_PLUGIN_SLUG__); ?></a></li>
    </ul>

    <div id="helpful_tabs_body">
        <div id="tabs-settings"><?php include_once __HELPFUL_DIR__ . "resources/admin/includes/settings-general.php"?></div>
    </div>
</div>
</div>

<?php
    if(isset($_REQUEST['sub'])){
?>
<script>
    jQuery(window).ready(function(){
        jQuery( ".tabs" ).tabs( "option", "active", 1 );
    });
</script>
<?php
    }
?>