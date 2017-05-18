<?php
/**
* Plugin Name: Helpful?
* Plugin URI: blah
* Description: blah
* Version: 1.0
* Author: PhiStream
* Author URI: http://www.phistream.com/
* License: GPL2
*/
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("__HELPFUL_PLUGIN_NAME__", "Helpful?");
define("__HELPFUL_PLUGIN_SLUG__", "helpful_");
define( '__HELPFUL_VERSION__', 1.0);
define( '__HELPFUL_DIR__', plugin_dir_path( __FILE__) );
define( '__HELPFUL_URL__', plugin_dir_url( __FILE__) );
define( '__HELPFUL_ROOT__', trailingslashit( plugins_url( '', __FILE__ ) ) );
define( '__HELPFUL_RESOURCES__', __HELPFUL_ROOT__ . 'resources/' );
define( '__HELPFUL_IMAGES__', __HELPFUL_RESOURCES__ . 'images/' );
define( '__HELPFUL_AJAX__', __HELPFUL_RESOURCES__ . 'ajax.php' );
define("__HELPFUL_ENABLE_GA_CUSTOM__", false);
define("__HELPFUL_DEBUG__", false);
define("__HELPFUL_TEST__", false);
define("__HELPFUL_STAGING__", false);

if(__HELPFUL_DEBUG__){
    @error_reporting(E_ALL);
    @ini_set("display_errors", "1");
}

/**
 * Abort loading if WordPress is upgrading
 */
if (defined('WP_INSTALLING') && WP_INSTALLING) return;

class HelpfulQmark{

    private $error;
    private $notice;

    public function __construct(){
        // all hooks and actions
        add_action( 'init', array( $this, 'helpful_register' ) );
        register_activation_hook( __FILE__ , array( $this, 'helpful_activate' ) );
        register_deactivation_hook( __FILE__ , array( $this, 'helpful_deactivate' ) );
        register_uninstall_hook( __FILE__ , array( "HelpfulQmark", 'helpful_uninstall' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'helpful_includeResources' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'helpful_includeResources' ) );
        add_action('plugins_loaded', array( $this, 'helpful_i18n' ) );
        add_filter('the_content', array( $this, 'helpful_content' ) );

        if(is_admin()){
            add_action( 'admin_menu', array( $this, 'helpful_add_menu' ) );
            add_filter('set-screen-option', array( $this, 'helpful_set_screen_options' ), 10, 3);
        }else{
            add_action( "wp_footer", array( $this, "helpful_wp_footer") );
        }
    }

    /**
     * Initializes the locale
     */
    function helpful_i18n(){
        $pluginDirName  = dirname( plugin_basename( __FILE__ ) );
        $domain         = __HELPFUL_PLUGIN_SLUG__;
        $locale         = apply_filters('plugin_locale', get_locale(), $domain);
        load_textdomain($domain, WP_LANG_DIR . '/' . $pluginDirName . "/" . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain( $domain, '', $pluginDirName . '/resources/lang/' );
    }

    function helpful_add_screen_options(){
        $args = array(
            'label' => __HELPFUL_PLUGIN_NAME__ . __(' feedback', __HELPFUL_PLUGIN_SLUG__),
            'default' => 10,
            'option' => __HELPFUL_PLUGIN_SLUG__ . 'comments_per_page'
        );

        add_screen_option( 'per_page', $args );
    }

    function helpful_set_screen_options($status, $option, $value){
        if ( __HELPFUL_PLUGIN_SLUG__ . 'comments_per_page' == $option ) return $value;
        return $status;
    }

    /**
     * Initializes the admin menu
     */
    function helpful_add_menu(){
        if(isset($_POST["submit-download"])){
            self::download();
            exit();
        }
        $hook   = add_menu_page(__HELPFUL_PLUGIN_NAME__, __HELPFUL_PLUGIN_NAME__, 'manage_options', __HELPFUL_PLUGIN_SLUG__, array($this, 'helpful_settings'), "dashicons-smiley");
        add_action( "load-$hook", array($this, 'helpful_add_screen_options') );
    }

    /**
     * Saves settings from the settings screen
     */
    function helpful_settings(){
        if(isset($_POST['submit'])){
            self::saveSettings();
        }
        include_once __HELPFUL_DIR__ . "resources/admin/includes/settings.php";
    }

    /**
     * Loads the JS and CSS resources
     */
    function helpful_includeResources() {
        wp_enqueue_script("jquery");

        if(is_admin()){
            wp_enqueue_script("jquery-ui-tabs");
            wp_enqueue_script( 'wp-color-picker' );
            wp_register_script("helpful-admin", __HELPFUL_RESOURCES__ . "admin/js/helpful.js");
            wp_enqueue_script("helpful-admin");

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style('helpful-ui',
                    __HELPFUL_RESOURCES__ . 'css/jquery-ui-themes-1.12.1/themes/smoothness/jquery-ui.min.css',
                    false,
                    __HELPFUL_RESOURCES__,
                    false
            );

        }else{
            wp_register_script("helpful", __HELPFUL_RESOURCES__ . "js/helpful.js");
            wp_enqueue_script("helpful");

            wp_register_script("ga-classic", (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? "https" : "http") . "://www.google-analytics.com/ga.js");
            wp_enqueue_script("ga-classic");

            wp_register_script("ga-universal", (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? "https" : "http") . "://www.google-analytics.com/analytics.js");
            wp_enqueue_script("ga-universal");
        }

        wp_register_style("helpful", __HELPFUL_RESOURCES__ . "css/helpful.css");
        wp_enqueue_style("helpful");
    }

    /**
     * Register the custom post type helpful
     */
    function helpful_register(){
        $settings       = self::getSettings();
        if(!$settings) return;

        if(in_array("post", $settings["types"])){
            add_filter('manage_posts_columns', array($this, 'add_helpful_columns'));
            add_action('manage_posts_custom_column', array($this, 'manage_helpful_columns'), 10, 2);
        }
        if(in_array("page", $settings["types"])){
            add_filter('manage_pages_columns', array($this, 'add_helpful_columns'));
            add_action('manage_pages_custom_column', array($this, 'manage_helpful_columns'), 10, 2);
        }
    }

    /**
     * Adds custom colums in the posts summary screen
     *
     * @return array
     */
	public function add_helpful_columns($columns){
        $columns['helpful'] = __HELPFUL_PLUGIN_NAME__;
		return $columns;
	}

    /**
     * Adds colum values for the custom columns in the posts summary screen
     */
	public function manage_helpful_columns($column_name, $id){
		switch ($column_name) {
			case 'helpful':
                self::showStatsFor($id);
				break;
		}
	}

    /**
     * Activate the plugin
     */
    function helpful_activate(){
        // defaults
        $settings               = array();
        $settings["types"]      = array("post", "page");
        $settings["title"]      = "Was this article helpful?";
        $settings["no_title"]   = "No";
        $settings["no_allow"]   = 1;
        $settings["no_text"]    = "Thanks! What could have made it better?";
        $settings["yes_title"]  = "Yes";
        $settings["yes_allow"]  = 1;
        $settings["yes_text"]   = "Thanks! What did you like about it? How could it have been improved?";
        $settings["thanks"]     = "Thank you for taking the time to leave feedback.";
        $settings["email"]      = "Would you like to leave your email?";

        $ga                     = array();
        $ga["ga"]               = 0;
        $ga["ga_tag"]           = 0;
        $ga["ga_type"]          = "";
        $ga["ga_tracking"]      = array("event");
        $settings["ga"]         = $ga;

        $style                  = array();
        $settings["style"]      = $style;

        self::setOption("settings-general", json_encode($settings));
    }

    /**
     * Deactivate the plugin
     */
    function helpful_uninstall(){
        define("WP_UNINSTALL_PLUGIN", true);
        $postsAndPages      = self::getAllPosts("numbers");
        if($postsAndPages){
            foreach($postsAndPages as $post){
                delete_post_meta($post->ID, __HELPFUL_PLUGIN_SLUG__ . "numbers");
                delete_post_meta($post->ID, __HELPFUL_PLUGIN_SLUG__ . "comments");
            }
        }
        $opts   = array("settings-general");
        foreach($opts as $opt){
            delete_option(__HELPFUL_PLUGIN_SLUG__ . $opt);
        }
    }

    function helpful_deactivate(){
        if(__HELPFUL_TEST__ || __HELPFUL_STAGING__){
            self::helpful_uninstall();
        }
    }

    function helpful_content($content){
        global $post;
        $settings   = self::getSettings();
        if(!$settings) return $content;

        $types      = $settings["types"];
        foreach($types as $type){
            switch($type){
                case "post":
                    if(is_single()){
                        $content    .= self::getForm($type);
                    }
                    break;
                case "page":
                    if(is_page()){
                        $content    .= self::getForm($type);
                    }
                    break;
            }
        }

        return $content;
    }

    function helpful_wp_footer(){
        //self::getGoogleTagManager();
    }

    /****************************************** Util functions ******************************************/

    /**
     * Writes to the file /tmp/log.log if DEBUG is on
     */
    public static function writeDebug($msg){
        if(__HELPFUL_DEBUG__) file_put_contents(__HELPFUL_DIR__ . "/tmp/log.log", date('F j, Y H:i:s') . " - " . $msg."\n", FILE_APPEND);
    }

    /**
     * Custom wrapper for the get_option function
     *
     * @return string
     */
    public static function getOption($field, $clean=false){
        $val = get_option(__HELPFUL_PLUGIN_SLUG__ . $field);
        return $clean ? htmlspecialchars($val) : $val;
    }

    /**
     * Custom wrapper for the update_option function
     *
     * @return mixed
     */
    public static function setOption($field, $value){
        return update_option(__HELPFUL_PLUGIN_SLUG__ . $field, $value);
    }

    /**
     * Custom wrapper for the get_post_meta function
     *
     * @return mixed
     */
    public static function getPostMeta($postID, $name, $single=true){
        return get_post_meta($postID, __HELPFUL_PLUGIN_SLUG__ . $name, $single);
    }

    /**
     * Custom wrapper for the update_post_meta function
     */
    public static function setPostMeta($postID, $name, $value){
        update_post_meta($postID, __HELPFUL_PLUGIN_SLUG__ . $name, $value);
    }

    public static function formatDate($timestamp, $format){
        $df = new DateTime();
        $df->setTimestamp($timestamp);
        return $df->format($format);
    }

    private static function download(){
        $posts      = self::getAllPosts("comments");
        $fields     = array();
        $fields[]   = array("Title", "Link", "Response", "Comment", "Date");
        foreach($posts as $post){
            $comments   = self::getPostMeta($post->ID, "comments");
            foreach($comments as $response=>$commentList){
                foreach($commentList as $comment){
                    $fields[]   = array(
                                    $post->post_title,
                                    get_permalink($post->ID),
                                    $response,
                                    $comment["comment"],
                                    self::formatDate($comment["timestamp"], 'j F Y')
                    );
                }
            }
        }

        $filename       = __HELPFUL_PLUGIN_SLUG__ . self::formatDate(time(), 'j F Y') . ".csv";

        self::exportToCSV($filename, $fields, ",");
    }

    private static function exportToCSV($filename, $fields, $delimiter){
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $filename . '";');
        $temp_memory = fopen('php://output', 'w');
        foreach ($fields as $line) {
            fputcsv($temp_memory, $line, $delimiter);
        }
        fclose($temp_memory);
    }

    private static function saveSettings(){
        $settings               = array();
        $settings["types"]      = $_POST['types'];
        $settings["title"]      = stripslashes($_POST['title']);
        $settings["no_title"]   = stripslashes($_POST['no_title']);
        $settings["no_allow"]   = isset($_POST['no_allow']) ? 1 : 0;
        $settings["no_text"]    = stripslashes($_POST['no_text']);
        $settings["yes_title"]  = stripslashes($_POST['yes_title']);
        $settings["yes_allow"]  = isset($_POST['yes_allow']) ? 1 : 0;
        $settings["yes_text"]   = stripslashes($_POST['yes_text']);
        $settings["thanks"]     = stripslashes($_POST['thanks']);
        $settings["email"]      = stripslashes($_POST['email']);

        $ga                     = array();
        $ga["ga"]               = isset($_POST['ga']) ? 1 : 0;
        $ga["ga_tag"]           = isset($_POST['ga_tag']) ? 1 : 0;
        $ga["ga_type"]          = isset($_POST['ga_type']) ? $_POST['ga_type'] : "";
        $ga["ga_tracking"]      = isset($_POST['ga_tracking']) ? $_POST['ga_tracking'] : array();
        //$ga["ga_code"]          = $_POST['ga_code'];
        $settings["ga"]         = $ga;

        $style                  = array();
        $style["yes_color"]     = $_POST['yes_color'];
        $style["no_color"]      = $_POST['no_color'];
        $style["yes_title_color"]   = $_POST['yes_title_color'];
        $style["no_title_color"]    = $_POST['no_title_color'];
        $style["title_size"]    = $_POST['title_size'];
        $style["bg_color"]      = $_POST['bg_color'];
        $style["css_advanced"]  = stripslashes($_POST['css_advanced']);
        $settings["style"]      = $style;

        self::setOption("settings-general", json_encode($settings));
    }

    public static function getSettings($type="general"){
        return json_decode(self::getOption("settings-" . $type), true);
    }

    private static function getForm($type){
        ob_start();
        include_once __HELPFUL_DIR__ . "resources/templates/form.php";
        return ob_get_clean();
    }

    public static function saveFeedback(){
        if(!wp_verify_nonce($_POST["nonce"], __HELPFUL_PLUGIN_SLUG__)){
            die();
        }

        $id             = $_POST["id"];
        $type           = $_POST["type"];
        $responseName   = $_POST["responseName"];
        $response       = $_POST["response"];

        switch($responseName){
            case "":
                $numbers    = self::getPostMeta($id, "numbers");
                if(!$numbers){
		    $numbers = array();
                    $numbers["yes"] = 0;
                    $numbers["no"]  = 0;
                }
                $numbers[$response] = $numbers[$response] + 1;
                self::setPostMeta($id, "numbers", $numbers);
                break;
            case "yes":
            case "no":
                $comments   = self::getPostMeta($id, "comments");
                if(!$comments){
                    $comments["yes"] = array();
                    $comments["no"]  = array();
                }
                $comments[$responseName][]  = array(
                                                    "comment" => $response,
                                                    "timestamp" => time(),
                                                    "email" => isset($_POST["email"]) ? $_POST["email"] : ""
                );
                self::setPostMeta($id, "comments", $comments);
                break;
            default:
                return;
        }
    }

    private static function getAllPosts($key){
        $stats          = array();
        $settings       = self::getSettings();
        if(!$settings) return $stats;

        $posts          = array();
        foreach($settings["types"] as $type){
            $args           = array(
                                'post_type'     => $type,
                                'post_status'   => 'publish',
                                'posts_per_page'   => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => __HELPFUL_PLUGIN_SLUG__ . $key,
                                        'value' => '',
                                        'compare' => '!=',
                                    )
                                )
                            );
            $results        = get_posts($args);
            self::writeDebug("$type $key " . count($results));
            $posts          = array_merge($results, $posts);
        }
        return $posts;
    }

    public static function showStatisticsTable(){
        require_once __HELPFUL_DIR__ . "/stats-list-table.php";

        $user       = get_current_user_id();
        $perPage    = get_user_meta($user, __HELPFUL_PLUGIN_SLUG__ . 'comments_per_page', true);
        if(!$perPage){
            $perPage    = 10;
        }

        $orderby    = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'yesPercent';
        $order      = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';

        $allStats   = self::getStatistics($orderby, $order);
        if(!$allStats) return;
        $settings   = self::getSettings();
?>
<div class="stats">
    <div class="overview">
        <div class="yesPercent"><?php echo $allStats["yesPercent"];?>%</div>
        <div class="title"><?php _e('of respondents find articles helpful. Read comments under Posts/Pages.', __HELPFUL_PLUGIN_SLUG__);?></div>
        <div class="hlp_buttons">
            <div><button class="yes" onclick="return false;"><?php _e($settings["yes_title"], __HELPFUL_PLUGIN_SLUG__);?>: <?php echo $allStats["yes"];?></button></div>
            <div><button class="no" onclick="return false;"><?php _e($settings["no_title"], __HELPFUL_PLUGIN_SLUG__);?>: <?php echo $allStats["no"];?></button></div>
        </div>
    </div>
    <div class="download">
        <form target="_new" method="post" action="">
            <input type="submit" class="button-primary" name="submit-download" value="<?php _e('Download all comments as CSV', __HELPFUL_PLUGIN_SLUG__);?>" >
        </form>
    </div>
    <div class="clear"></div>
</div>
<?php
        $listing                = new Stats_List_Table(array("url" => admin_url('admin.php?page=' . __HELPFUL_PLUGIN_SLUG__ . '&sub=stats')));
        $listing->settings      = $settings;
        $listing->allStats      = $allStats;
        $listing->perPage       = $perPage;
        $listing->prepare_items();
        $listing->display();

        include_once __HELPFUL_DIR__ . "resources/includes/styles.php";
    }

    public static function getStatistics($sortBy=NULL, $sortOrder=NULL){
        $posts = self::getAllPosts("numbers");
        if(!$posts) return NULL;

        $yesCount = $noCount = 0;
        foreach($posts as $post){
            $numbers = self::getPostMeta($post->ID, "numbers");
            $yesCount += $numbers["yes"];
            $noCount += $numbers["no"];
            $stats[] = array(
                            "title" => $post->post_title,
                            "link" => get_permalink($post->ID),
                            "yes" => $numbers["yes"],
                            "no" => $numbers["no"],
                            "yesPercent" => ($numbers["yes"] + $numbers["no"] > 0) ? round(($numbers["yes"] * 100/($numbers["yes"] + $numbers["no"])), 1) : 0
            );
        }


        if(!$sortBy) $sortBy    = "yesPercent";
        if(!$sortOrder) $sortOrder    = "DESC";
        uasort($stats, array( "HelpfulQmark", 'helpful_sort_stats_' . $sortBy . '_' . $sortOrder ));

        return array(
                "yes"           => $yesCount,
                "no"            => $noCount,
                "yesPercent"    => ($yesCount + $noCount > 0) ? round(($yesCount * 100/($yesCount + $noCount)), 1) : 0,
                "stats"         => $stats
        );
    }

    private static function helpful_sort_stats_yes_DESC($a, $b){
        return $a["yes"] < $b["yes"];
    }
    private static function helpful_sort_stats_yes_ASC($a, $b){
        return $a["yes"] > $b["yes"];
    }

    private static function helpful_sort_stats_no_DESC($a, $b){
        return $a["no"] < $b["no"];
    }
    private static function helpful_sort_stats_no_ASC($a, $b){
        return $a["no"] > $b["no"];
    }

    private static function helpful_sort_stats_yesPercent_DESC($a, $b){
        return $a["yesPercent"] < $b["yesPercent"];
    }
    private static function helpful_sort_stats_yesPercent_ASC($a, $b){
        return $a["yesPercent"] > $b["yesPercent"];
    }

    private static function helpful_sort_stats_timestamp_DESC($a, $b){
        return $a["timestamp"] < $b["timestamp"];
    }

    private static function showStatsFor($id){
        add_thickbox('thickbox');
        $settings   = self::getSettings();
        if(!$settings) return;
        $numbers    = self::getPostMeta($id, "numbers");
        $comments   = self::getPostMeta($id, "comments");
        $post       = get_post($id);
        $yes = $no  = 0;
        $yesC = $noC  = 0;

        if($numbers){
            $yes        = $numbers["yes"];
            $no         = $numbers["no"];
        }

        if($comments){
            $yesC       = count($comments["yes"]);
            $noC        = count($comments["no"]);
        }

?>
<div class="helpful">
    <div class="hlp_buttons">
<?php
        if($settings["yes_allow"] == 1){
?>
        <div class="hlp_button">
            <a class="button-primary <?php echo $comments && $comments["yes"] ? "thickbox" : ""?> <?php echo $yes > 0 ? "has_value" : "no_value"?>" style="vertical-align: top" href="#TB_inline?width=600&height=550&inlineId=yes_stats<?php echo $id;?>;"><?php _e($settings["yes_title"], __HELPFUL_PLUGIN_SLUG__);?>: <?php echo $yes;?></a>
            </a>
        </div>
<?php
        }
?>
<?php
        if($settings["no_allow"] == 1){
?>
        <div class="hlp_button">
            <a class="button <?php echo $comments && $comments["no"] ? "thickbox" : ""?> <?php echo $no > 0 ? "has_value" : "no_value"?>" href="#TB_inline?width=600&height=550&inlineId=no_stats<?php echo $id;?>;"><?php _e($settings["no_title"], __HELPFUL_PLUGIN_SLUG__);?>: <?php echo $no;?></a>
            </a>
        </div>
<?php
        }
?>
    </div>

    <div id="yes_stats<?php echo $id;?>" style="display:none;">
        <div class="summary">
            <h2 class="helpful"><?php echo $post->post_title;?></h2>
            <h3><?php echo $yes;?> <?php _e('Responses', __HELPFUL_PLUGIN_SLUG__);?>, <?php echo $yesC;?> <?php _e('Comments', __HELPFUL_PLUGIN_SLUG__);?></h3>
<?php
        if($comments && $comments["yes"]){
            $list    = $comments["yes"];
            uasort($list, array( "HelpfulQmark", 'helpful_sort_stats_timestamp_DESC' ));
            foreach($list as $comment){
                $email  = "Unknown";
                if($comment['email'] && strlen($comment['email']) > 0){
                    $email  = $comment['email'];
                }
?>
        <div class="item">
            <div class="heading"><?php echo self::formatDate($comment["timestamp"], 'j F Y');?> <?php _e('by', __HELPFUL_PLUGIN_SLUG__);?> <?php echo $email;?></div>
            <div class="body"><?php echo $comment["comment"];?></div>
        </div>
<?php
            }
        }else{
?>
            <p><?php _e('No comments', __HELPFUL_PLUGIN_SLUG__);?></p>
<?php
        }
?>
        </div>
    </div>
    <div id="no_stats<?php echo $id;?>" style="display:none;">
        <div class="summary">
            <h2 class="helpful"><?php echo $post->post_title;?></h2>
            <h3><?php echo $no;?> <?php _e('Responses', __HELPFUL_PLUGIN_SLUG__);?>, <?php echo $noC;?> <?php _e('Comments', __HELPFUL_PLUGIN_SLUG__);?></h3>
<?php
        if($comments && $comments["no"]){
            $list    = $comments["no"];
            uasort($list, array( "HelpfulQmark", 'helpful_sort_stats_timestamp_DESC' ));
            foreach($list as $comment){
                $email  = "Unknown";
                if($comment['email'] && strlen($comment['email']) > 0){
                    $email  = $comment['email'];
                }
?>
        <div class="item">
            <div class="heading"><?php echo self::formatDate($comment["timestamp"], 'j F Y');?> <?php _e('by', __HELPFUL_PLUGIN_SLUG__);?> <?php echo $email;?></div>
            <div class="body"><?php echo $comment["comment"];?></div>
        </div>
<?php
            }
        }else{
?>
            <p><?php _e('No comments', __HELPFUL_PLUGIN_SLUG__);?></p>
<?php
        }
?>
        </div>
    </div>
</div>
<?php
        include_once __HELPFUL_DIR__ . "resources/includes/styles.php";
    }

    private static function getGoogleTagManager(){
        $settings       = self::getSettings();
        if(!$settings || $settings["ga"]) return;

        $ga             = $settings["ga"];
        if($ga["ga"] == 1 && $ga["ga_tag"] == 1){
            $code       = $ga["ga_code"];

?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=" . <?php echo $code;?>
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $code;?>');</script>
<!-- End Google Tag Manager -->
<?php
        }
    }

}

$helpfulQmark = new HelpfulQmark();
