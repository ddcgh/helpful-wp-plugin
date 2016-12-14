<?php
    if(@$settings["style"]){
?>
<style>
<?php
        if($settings["style"]["yes_color"]){
?>
    button.yes{
        background: <?php echo $settings["style"]["yes_color"];?> !important;
        border-color: <?php echo $settings["style"]["yes_color"];?> !important;
    }
<?php
        }
?>
<?php
        if($settings["style"]["yes_title_color"]){
?>
    button.yes{
        color: <?php echo $settings["style"]["yes_title_color"];?> !important;
    }
<?php
        }
?>
<?php
        if($settings["style"]["no_color"]){
?>
    button.no{
        background: <?php echo $settings["style"]["no_color"];?> !important;
        border-color: <?php echo $settings["style"]["no_color"];?> !important;
    }
<?php
        }
?>
<?php
        if($settings["style"]["no_title_color"]){
?>
    button.no{
        color: <?php echo $settings["style"]["no_title_color"];?> !important;
    }
<?php
        }
?>
<?php
        if($settings["style"]["title_size"]){
?>
    div.helpful div#click0 .title{
        font-size: <?php echo $settings["style"]["title_size"];?>px !important;
    }
<?php
        }
?>
<?php
        if($settings["style"]["bg_color"]){
?>
    div.helpful.form{
        background: <?php echo $settings["style"]["bg_color"];?> !important;
    }
<?php
        }
?>
<?php
        if($settings["style"]["css_advanced"]){
            echo $settings["style"]["css_advanced"];
        }
?>
</style>
<?php
    }
?>