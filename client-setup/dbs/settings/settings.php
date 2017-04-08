<?php
/*
  Plugin Name: Account Settings
  Plugin URI: http://www.rudrainnovatives.com
  Description: This plugin is used for account settings
  Author: Rudra Innnovative Software
  Version: 1.0
  Author URI: http://www.rudrainnovatives.com
 */

include_once ABSPATH . "/global_config.php";


if (!defined('SET_DEBUG_MODE'))
    define('SET_DEBUG_MODE', false);
if (!defined('SET_FILE'))
    define('SET_FILE', __FILE__);
if (!defined('SET_CONTENT_DIR'))
    define('SET_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('SET_CONTENT_URL'))
    define('SET_CONTENT_URL', site_url() . '/wp-content');
if (!defined('SET_PLUGIN_DIR'))
    define('SET_PLUGIN_DIR', SET_CONTENT_DIR . '/plugins');
if (!defined('SET_PLUGIN_URL'))
    define('SET_PLUGIN_URL', SET_CONTENT_URL . '/plugins');
if (!defined('SET_PLUGIN_FILENAME'))
    define('SET_PLUGIN_FILENAME', basename(__FILE__));
if (!defined('SET_PLUGIN_DIRNAME'))
    define('SET_PLUGIN_DIRNAME', plugin_basename(dirname(__FILE__)));
if (!defined('SET_COUNT_PLUGIN_DIR'))
    define('SET_COUNT_PLUGIN_DIR', SET_PLUGIN_DIR . '/' . SET_PLUGIN_DIRNAME);
if (!defined('SET_COUNT_PLUGIN_URL'))
    define('SET_COUNT_PLUGIN_URL', site_url() . '/wp-content/plugins/' . SET_PLUGIN_DIRNAME);
if (!defined('SET_VERSION'))
    define('SET_VERSION', '1.2');

if (!defined('ST_PWD_REST'))
    define('ST_PWD_REST', 'password-reset');

if (!defined('EMAIL_TYPE'))
    define('EMAIL_TYPE', '');
if (!defined('ST_REPORT_FULL_NAME'))
    define('ST_REPORT_FULL_NAME', 'Executive Summary Report');
if (!defined('ST_ADMIN_EXE_REPORT'))
    define('ST_ADMIN_EXE_REPORT', 'admin_executive_report');


// Report Name
if (!defined('ST_COMPETIROR_REPORT_NAME'))
    define('ST_COMPETIROR_REPORT_NAME', 'Competitors Report');
if (!defined('ST_COMPETIROR_REPORT'))
    define('ST_COMPETIROR_REPORT', 'admin_competitors_report');
if (!defined('ST_KEYWORD_REPORT'))
    define('ST_KEYWORD_REPORT', 'admin_keyword_report');
if (!defined('ST_TRAFFIC_REPORT'))
    define('ST_TRAFFIC_REPORT', 'admin_traffic_report');
if (!defined('ST_RANK_REPORT'))
    define('ST_RANK_REPORT', 'admin_target_vs_ranking_report');
if (!defined('ST_CONVERSION_REPORT'))
    define('ST_CONVERSION_REPORT', 'admin_conversion_report');


if (!defined('ST_MAX_TRAFFIC_REC_TO_SHOW'))
    define('ST_MAX_TRAFFIC_REC_TO_SHOW', 100);

if (!defined('ST_EXCLUDE_ROLES'))
    define('ST_EXCLUDE_ROLES', 'RepToolBox User,RepToolBox BETA,SEONitro User,Event Contributor');

$proto = 'http://';
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
    $proto = 'https://';
}

function client_company_info() {
    global $wpdb;
    return $wpdb->prefix . 'client_company_info';
}

function client_location() {
    global $wpdb;
    return $wpdb->prefix . 'client_location';
}

function location_mapping() {
    global $wpdb;
    return $wpdb->prefix . 'location_mapping';
}


// special redirect to admin GA connect
if(isset($_SESSION['analytic_loc_id']) && $_SESSION['analytic_loc_id'] > 0){
    $locid =$_SESSION['analytic_loc_id'];
    unset($_SESSION['analytic_loc_id']);
    $code = $_REQUEST['code'];
    $redurl = site_url().'/'.ST_LOC_PAGE.'?parm=ga_connect&location_id='.$locid.'&code='.$code;
    header('Location: '.$redurl);
    exit();
}
// special redirect to admin GA connect

function SET_install_table() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    if (count($wpdb->get_var('SHOW TABLES LIKE "' . client_company_info() . '"')) == 0) {
        $sql = 'CREATE TABLE ' . client_company_info() . '(
            id int UNSIGNED NOT NULL AUTO_INCREMENT,               
            company_info longtext not null,            
            is_white_label int(1) default "0",
            logo varchar(500),
            white_label_url varchar(200),
            original_url varchar(200),
            fb_connect_code text,
	    google_tag_manager text,
	    client_success_manager text,
	    intercom_chat text,
	    extra_footer_code text,
            created_dt datetime not null,
            updated_dt timestamp not null,
            PRIMARY KEY (id)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . client_company_info() . " (created_dt) VALUES('%s')", date("Y-m-d H:i:s")
                )
        );
    }

    if (count($wpdb->get_var('SHOW TABLES LIKE "' . client_location() . '"')) == 0) {
        $sql = 'CREATE TABLE ' . client_location() . '(
            id int UNSIGNED NOT NULL AUTO_INCREMENT,               
            MCCUserId int(20) not null,            
            status int(1) default 0,
            conv_verified int(1) default "0",
            created_by int(20) not null,
            created_dt datetime not null,            
            updated_dt timestamp not null,
            PRIMARY KEY (id)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
    }

    if (count($wpdb->get_var('SHOW TABLES LIKE "' . location_mapping() . '"')) == 0) {
        $sql = 'CREATE TABLE ' . location_mapping() . '(
            id int UNSIGNED NOT NULL AUTO_INCREMENT,   
            location_id int(11),
            user_id int(11) not null,                
            created_dt datetime not null,
            updated_dt timestamp not null,
            PRIMARY KEY (id)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
    }
    
    
    // tables for adons - 22 nov 2016
    
    
    if (count($wpdb->get_var('SHOW TABLES LIKE "wp_addons_purchase"')) == 0) {
        $sql = 'CREATE TABLE IF NOT EXISTS `wp_addons_purchase` (
            `addons_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `addons_type` varchar(150) NOT NULL,
            `addons_date` varchar(150) NOT NULL,
            `addons_amount` varchar(150) NOT NULL,
            `addons_status` varchar(150) NOT NULL,
            `status` varchar(150) NOT NULL,
            `minus_amount` varchar(150) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
    }
    
    if (count($wpdb->get_var('SHOW TABLES LIKE "wp_location_package_fields"')) == 0) {
        $sql = 'CREATE TABLE IF NOT EXISTS `wp_location_package_fields` (
            `lpf_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `lpf_field` varchar(150) NOT NULL,
            `lpf_limit` varchar(150) NOT NULL,
            `lpf_addons_add` varchar(150) NOT NULL,
            `lpf_addons_delete` varchar(150) NOT NULL,
            `lpf_used` varchar(150) NOT NULL,
            `lpf_locations_delete` varchar(150) NOT NULL,
            `lpf_location_addon_field` varchar(150) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
        $query = "INSERT INTO `wp_location_package_fields` (`lpf_id`, `lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`, `lpf_locations_delete`, `lpf_location_addon_field`) VALUES
(1, 'keywords', '500', '0', '0', '0', '0', '100'),
(2, 'comp_keywords', '1000', '0', '0', '0', '0', '200'),
(3, 'keyword_opp', '1500', '0', '0', '0', '0', '250'),
(4, 'pages', '5000', '0', '0', '0', '0', '1000'),
(5, 'site_audit', '5', '0', '0', '0', '0', '1'),
(6, 'citation_run', '5', '0', '0', '0', '0', '1'),
(7, 'location', '5', '0', '0', '0', '0', '1');";
        
        $wpdb->query($query);
    }
    
    
    // tables for adons - 22 nov 2016
    
    // tables for adons - 28 nov 2016
    
    if (count($wpdb->get_var('SHOW TABLES LIKE "wp_add_locations_status"')) == 0) {
        $sql = 'CREATE TABLE IF NOT EXISTS `wp_add_locations_status` (
            `als_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `als_created_date` datetime NOT NULL,
            `als_update_date` datetime NOT NULL,
            `als_status` varchar(200) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
    }
    
    if (count($wpdb->get_var('SHOW TABLES LIKE "wp_location_extraDataConsume"')) == 0) {
        $sql = 'CREATE TABLE IF NOT EXISTS `wp_location_extraDataConsume` (
            `bi_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `cron_insert` varchar(500) NOT NULL,
            `arb_update` varchar(500) NOT NULL,
            `create_on` datetime NOT NULL,
            `update_on` datetime NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
    }
    
    if (count($wpdb->get_var('SHOW TABLES LIKE "wp_pay_for_locations"')) == 0) {
        $sql = 'CREATE TABLE IF NOT EXISTS `wp_pay_for_locations` (
            `payment_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `SubscriptionId` varchar(200) NOT NULL,
            `orderInvoiceNumber` varchar(200) NOT NULL,
            `customerEmail` varchar(200) NOT NULL,
            `creditCardCardNumber` varchar(200) NOT NULL,
            `intervalLength` varchar(200) NOT NULL,
            `intervalUnit` varchar(200) NOT NULL,
            `amount` varchar(200) NOT NULL,
            `noof_locations` varchar(200) NOT NULL,
            `trans_id` varchar(200) NOT NULL,
            `subscription_paynum` varchar(200) NOT NULL,
            `startDate` datetime NOT NULL,
            `modifyDate` datetime NOT NULL,
            `status` varchar(200) NOT NULL,
            `customerProfileId` varchar(200) NOT NULL,
            `customerPaymentProfileId` varchar(200) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
    }
    
    // tables for adons - 28 nov 2016
    
    // tables for adons - 15 dec 2016
    
    if (count($wpdb->get_var('SHOW TABLES LIKE "wp_billingdiscount"')) == 0) {
        $sql = 'CREATE TABLE IF NOT EXISTS `wp_billingdiscount` (
            `bd_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `bd_dcid` varchar(200) NOT NULL,
            `bd_dcname` varchar(200) NOT NULL,
            `bd_dcprice` varchar(200) NOT NULL,
            `bd_price` varchar(200) NOT NULL,
            `bd_assign` datetime NOT NULL,
            `bd_status` varchar(200) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
        dbDelta($sql);
        
        
    }
    
    // tables for adons - 15 dec 2016
    
    

    /*
      $usermeta = $wpdb->prefix."usermeta";
      $db = DB_NAME;
      $hascol = $wpdb->get_row
      (
      $wpdb->prepare
      (
      "SELECT count(*) as total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$db'
      AND TABLE_NAME = '$usermeta' AND COLUMN_NAME = 'location_id'"
      )
      );
      if($hascol->total == 0){

      // array of tables for mcc DB where to add location
      $arrtbls = array($usermeta,$wpdb->prefix."keywords_update_history");

      foreach($arrtbls as $tbl){
      $wpdb->query
      (
      $wpdb->prepare
      (
      "ALTER TABLE $tbl ADD location_id int(11)"
      )
      );
      }

      }
     */

    /** Core PHP Connection string for analytic table * */
    /*
      $servername = database_host;
      $db_name = database_name;
      $db_user = database_user;
      $db_password = database_password;
      $conn = new mysqli($servername, $db_user, $db_password, $db_name);
      $iserror = 0;
      if ($conn->connect_error) {
      $iserror = 1;
      }
      $completbl = "competitor_report"; $completbl_hist = "competitor_report_history";
      if($iserror == 0){
      $sql = "SELECT count(*) as total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$db_name'
      AND TABLE_NAME = '$completbl' AND COLUMN_NAME = 'location_id'";

      $result = mysqli_query($conn, $sql);
      $row = $result->fetch_object();
      if($row->total == 0){

      // array of tables for analytics DB where to add location
      $anatbls = array('competitor_report','competitor_report_history');
      foreach($anatbls as $anatbl){
      $sql = "ALTER TABLE $anatbl ADD location_id int(11)";
      mysqli_query($conn, $sql);
      }
      }
      }
     */
    /** Core PHP Connection string for analytic table * */
}

register_activation_hook(__FILE__, 'SET_install_table');

// Remove location users 
add_action('pre_user_query', 'mccsite_pre_user_query');

function mccsite_pre_user_query($user_search) {

    global $wpdb;
    $locations_users = st_get_location_users();
    if (!empty($locations_users)) {
        $locations_users = implode(",", $locations_users);
        $user_search->query_where = str_replace('WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.ID NOT IN ($locations_users)", $user_search->query_where);
    }
}

// Remove location users 

function st_get_location_users() {
    global $wpdb;
    $locations = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT MCCUserId FROM " . client_location(),""
            )
    );
    $ar = array();
    foreach ($locations as $location) {
        array_push($ar, $location->MCCUserId);
    }
    return $ar;
}

function checkwhitelbl_function() {
    global $wpdb;
    $whitelbl = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT is_white_label,white_label_url FROM " . client_company_info(), ""
            )
    );
    if (!empty($whitelbl)) {
        if ($whitelbl->is_white_label == 1) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['slogin'])) {
                $guid = '';
                if (isset($_REQUEST['temp_login']))
                    $guid = isset($_REQUEST['guid']) ? trim(htmlspecialchars($_REQUEST['guid'])) : '';

                $dbguid = md5(DB_NAME);
                if ($guid != $dbguid) {
                    $servername = $_SERVER['SERVER_NAME'];
                    $servername = trim(str_replace(array("http://", "https://"), array("", ""), $servername));
                    $servername = preg_replace('{/$}', '', $servername);

                    $whiteurl = trim(str_replace(array("http://", "https://"), array("", ""), $whitelbl->white_label_url));
                    $whiteurl = preg_replace('{/$}', '', $whiteurl);
                    // if not equal, means another url opened                 
                    if ($servername != $whiteurl) {
                        wp_redirect($whitelbl->white_label_url);
                        die;
                    }
                } else if ($guid == $dbguid) {
                    $_SESSION['slogin'] = 1;
                    header("Refresh:0");
                    die;
                }
            }
        }
    }
}

add_action('init', 'checkwhitelbl_function');

add_action('wp_head', 'SET_pluginname_ajaxurl');

function SET_pluginname_ajaxurl() {
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo str_replace(array('https', 'HTTPS'), array('http', 'HTTP'), admin_url('admin-ajax.php')); ?>';
        </script>
        <?php
    }
}

if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
        case "settings_lib":
            add_action('admin_init', 'settings_lib');

            function settings_lib() {
                global $wpdb;
                include_once SET_COUNT_PLUGIN_DIR . '/library/settings-lib.php';
            }

            break;
        case "rudra_api":
            add_action('admin_init', 'rudra_api');

            function rudra_api() {
                global $wpdb;
                include_once SET_COUNT_PLUGIN_DIR . '/library/child-api.php';
            }

            break;
    }
}

function SET_filter_plugin_updates($value) {
    if (isset($value->response['settings/settings.php'])) {
        unset($value->response['settings/settings.php']);
    }
    return $value;
}

add_filter('site_transient_update_plugins', 'SET_filter_plugin_updates');


add_action('init', 'formposts');

function formposts() {
    global $wpdb;
    if (isset($_POST['__change_pwd']) && $_POST['__change_pwd'] == 1) {

        $old_pwd = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];
        $confirmnewpassword = $_POST['confirmnewpassword'];
        @session_start();
        if ($newpassword != $confirmnewpassword) {
            $ar = json_encode(array("sts" => 0, 'msg' => 'Password mismatch'));
            $_SESSION['passmsg'] = $ar;
            header('Refresh:0');
        }
        $c_id = get_current_user_id();

        $get_account_info = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . $wpdb->prefix . "users WHERE id = %d", $c_id
                )
        );

        $res = wp_check_password($old_pwd, $get_account_info->user_pass, $c_id);

        $user_id = $get_account_info->ID;
        if (!$res) {
            $ar = json_encode(array("sts" => 0, 'msg' => 'Wrong old password'));
            $_SESSION['passmsg'] = $ar;
            header('Refresh:0');
            exit;
        }

        wp_set_password($newpassword, $user_id);
        $ar = json_encode(array("sts" => 1, 'msg' => 'Password successfully changed'));
        $_SESSION['passmsg'] = $ar;

        wp_clear_auth_cookie();
        wp_set_current_user($user_id, $user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_login);
        header('Refresh:0');
        die;
    } else if (isset($_POST['__logo_upload']) && $_POST['__logo_upload'] == 1) {
        if (isset($_POST['save_css']) && $_POST['save_css'] == 1) {
            
            $height = isset($_POST['heightlogo'])?intval($_POST['heightlogo']):0;
            if($height == 0){
                $height = 'auto';
            }            
            $width = isset($_POST['widthlogo'])?intval($_POST['widthlogo']):178;            
            $toplogo = isset($_POST['toplogo'])?intval($_POST['toplogo']):0;            
            
            $ar = array(
                'height' => $height,
                'width' => $width,
                'toplogo' => $toplogo
            );
            
            $ar = json_encode($ar);                                                
            if($wpdb->query("SHOW COLUMNS FROM ".client_company_info()." LIKE 'logo_css'") == 0){
                // add column
                $wpdb->query("ALTER TABLE ".client_company_info()." ADD COLUMN logo_css varchar(200)");                
            }
            
            $res = $wpdb->get_row
            (
                $wpdb->prepare
                (
                        "select id from " . client_company_info(),""                        
                )
            );
            
            if(!empty($res)){
                        
                    $id = $res->id;
                    $rs = $wpdb->query
                    (
                        $wpdb->prepare
                        (
                                "UPDATE " . client_company_info() . " SET logo_css = %s"
                                . "WHERE id = %d", 
                                $ar, $id
                        )
                    );                    
                }
                else{

                    $rs = $wpdb->query
                    (
                        $wpdb->prepare
                        (
                                "INSERT INTO " . client_company_info() . " (logo_css, created_dt) "
                                . "VALUES (%s,'%s')", 
                                $ar, $now
                        )
                    );

                }
            
            $ar = json_encode(array("sts" => 1, 'msg' => 'Logo positioning saved successfully.'));
            $_SESSION['uploadmsg'] = $ar;            
            header('Refresh:0');
            die;
            
        } else {
            $ext = pathinfo($_FILES['application_logo']['name'], PATHINFO_EXTENSION);
            if ($ext == 'php' || $ext == 'js') {
                $ar = json_encode(array("sts" => 0, 'msg' => 'Invalid File Type'));
                $_SESSION['uploadmsg'] = $ar;
                header('Refresh:0');
                die;
            }

            $target_file = SET_COUNT_PLUGIN_URL . '/uploads/' . 'logo.' . $ext;
            $target_dir = SET_COUNT_PLUGIN_DIR . '/uploads/' . 'logo.' . $ext;
            $themelogo = ABSPATH . "/wp-content/themes/twentytwelve/images/new/logo-2.png";
            if (move_uploaded_file($_FILES['application_logo']['tmp_name'], $target_dir)) {

                copy($target_dir, $themelogo); // logo also move to theme folder
                //                list($width, $height, $type, $attr) = getimagesize($target_dir);
                //                
                //                require_once('resize.php');
                //                // crop save functionality
                //                $w =  300;        
                //                $h =  100;
                //                
                //                if($width < $w)
                //                    $w = $width;
                //                
                //                if($height < $h)
                //                    $h = $height;
                //                
                //                $image = new SimpleImage();
                //                $image->load($target_dir);
                //                $image->resize($w, $h);                
                //                $image->save($target_dir);

                $res = $wpdb->get_row
                        (
                        $wpdb->prepare
                                (
                                "select id from " . client_company_info(), ""
                        )
                );
                $filesave = $target_file . '?' . time();
                if (!empty($res)) {

                    $id = $res->id;
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE " . client_company_info() . " SET logo = %s WHERE id = %d", $filesave, $id
                            )
                    );
                } else {

                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . client_company_info() . " (logo, created_dt) "
                                    . "VALUES (%s, '%s')", $filesave, date("Y-m-d H:i:s")
                            )
                    );
                }

                $ar = json_encode(array("sts" => 1, 'msg' => 'Image uploaded successfully'));
                $_SESSION['uploadmsg'] = $ar;
                header('Refresh:0');
                die;
            } else {
                $ar = json_encode(array("sts" => 0, 'msg' => 'Image upload failed'));
                $_SESSION['uploadmsg'] = $ar;
                header('Refresh:0');
            }
        }
    } else if (isset($_POST['__logo_report']) && $_POST['__logo_report'] == 1) {

        $ext = pathinfo($_FILES['pdf_logo']['name'], PATHINFO_EXTENSION);
        if ($ext == 'php' || $ext == 'js') {
            $ar = json_encode(array("sts" => 0, 'msg' => 'Invalid File Type'));
            $_SESSION['reportmsg'] = $ar;
            header('Refresh:0');
            die;
        }
        $target_file = SET_COUNT_PLUGIN_URL . '/uploads/' . 'pdf_logo.jpg';
        $target_dir = SET_COUNT_PLUGIN_DIR . '/uploads/' . 'pdf_logo.jpg';

        if (move_uploaded_file($_FILES['pdf_logo']['tmp_name'], $target_dir)) {

            $ar = json_encode(array("sts" => 1, 'msg' => 'PDF Report logo uploaded successfully'));
            $_SESSION['reportmsg'] = $ar;
            header('Refresh:0');
            die;
        } else {
            $ar = json_encode(array("sts" => 0, 'msg' => 'PDF Report logo upload failed'));
            $_SESSION['reportmsg'] = $ar;
            header('Refresh:0');
        }
    }
}

include_once 'custom_functions.php';

add_shortcode('shortcode_dropdownsites', 'shortcode_dropdownsites_function');

function shortcode_dropdownsites_function() {
    include_once SET_COUNT_PLUGIN_DIR . '/views/front_dropdownsites.php';
}

add_shortcode('admin_setting_menu', 'admin_setting_menu_function');

function admin_setting_menu_function() {
    include_once SET_COUNT_PLUGIN_DIR . '/views/front_admin_setting_menu.php';
}

function st_session_on_logout() {
    unset($_SESSION['location']);
    unset($_SESSION['Current_user_live']);
}

add_action('wp_logout', 'st_session_on_logout');

function location_settings_shortcode() {

    global $wpdb;
    $c_id = get_current_user_id();
    $user = new WP_User($c_id);
    $u_role = $user->roles[0];
    if ($u_role == 'administrator' || administrator_permission()) {
        echo "<div class='page-content'><div class='container-fluid'><div class='portlet light '>";

        if (isset($_GET['parm']) && $_GET['parm'] == 'execution') {
            // functions
            include_once SET_COUNT_PLUGIN_DIR . '/views/executions.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'company_info') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/company_info.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'functions') {

            include_once SET_COUNT_PLUGIN_DIR . '/library/functions.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'master-user-list') {

            include_once SET_COUNT_PLUGIN_DIR . '/views/userlist.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'locations') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/locations.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'add_ons') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/add_ons.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'billing_info') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/billing_info.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'billing_payment') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/billing_payment.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'payment_history') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/payment_history.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'purchased_addons') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/purchased_addons.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'invoices_recieved') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/recieved_invoices_info.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'add_paid_location') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/add_paid_location.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'new_location') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/new_location.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'location_sites') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/location_sites.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'edit_location') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/edit_location.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'assign_users') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/assign_locations.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'keywords') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/keywords.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'csv') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/csv_keyword_list.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'reports') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/reports.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'tracking-code') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/tracking-code.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'conversion-urls') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/conversion-urls.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'ga_connect') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/ga_connect.php';
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'competitor_url') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/competitor_url.php';
        }else if (isset($_GET['parm']) && $_GET['parm'] == 'training-tool') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/training-tool.php';
        
        } else if (isset($_GET['parm']) && $_GET['parm'] == 'cancelSubscription') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/cancelSubscription.php';
        }else if (isset($_GET['parm']) && $_GET['parm'] == 'mail-config') {
            include_once SET_COUNT_PLUGIN_DIR . '/views/settings-smtp-configuration.php';
        }else {
            include_once SET_COUNT_PLUGIN_DIR . '/views/account_settings.php';
        }
        echo "</div></div></div>";
    } else {
        //wp_update_user( array ('ID' => 1, 'role' => esc_attr('administrator') ) );
        echo '<div style="text-align: center; font-weight: bold; margin-top: 25px; font-size: 18px;">Account access has been limited. Please contact your agency representative for more information.</div> ';
        die;
    }
}

function return_404() {
    status_header(404);
    nocache_headers();
    include( get_404_template() );
    exit;
}

add_shortcode('location_settings', 'location_settings_shortcode');

function st_loc_page() {
    global $wpdb;
    $wp_rewrite = new WP_Rewrite();

    $slug = ST_LOC_PAGE;

    if (!st_isslugexists($slug)) {
        $_p = array();
        $_p['post_title'] = "Location Settings";
        $_p['post_content'] = "[location_settings]";
        $_p['post_status'] = 'publish';
        $_p['post_slug'] = $slug;
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        wp_insert_post($_p);
    }


    if (!st_isslugexists(ST_PWD_REST)) {
        $_p = array();
        $_p['post_title'] = "Password Reset";
        $_p['post_content'] = "[reset_pwd]";
        $_p['post_status'] = 'publish';
        $_p['post_slug'] = ST_PWD_REST;
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        wp_insert_post($_p);
    }
}

register_activation_hook(__FILE__, 'st_loc_page');


add_shortcode('reset_pwd', 'reset_pwd_shortcode');

function reset_pwd_shortcode() {
    include_once SET_COUNT_PLUGIN_DIR . '/views/reset_pwd.php';
}

function st_isslugexists($post_name) {
    global $wpdb;
    $posts = $wpdb->prefix . "posts";
    $sql = "SELECT post_name FROM $posts WHERE post_name = '" . $post_name . "'";
    if ($wpdb->get_row($sql, 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

function billing_session_unset() {
    unset($_SESSION['packagetype']);
    unset($_SESSION['packageid']);
    unset($_SESSION["showpackages"]);
    unset($_SESSION['packageprice']);
    unset($_SESSION["old_packagetype"]);
    unset($_SESSION["old_packageid"]);
}
add_action('wp_logout', 'billing_session_unset');
add_action('wp_login', 'billing_session_unset');

$limit_keywords_notification = '10';
$limit_ckeywords_notification = '25';
$limit_keywordso_notification = '250';
$limit_pages_notification = '100';
$limit_site_audit_notification = '0';
$limit_citation_run_notification = '0';
$billing_enable = BILLING_ENABLE;

add_action('init','checkifbillingenable');

function checkifbillingenable() {    
    global $wpdb;

    if (BILLING_ENABLE == 1 && user_id() > 0) {
                
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $linkparts = explode("wp-admin", $actual_link);
        if(count($linkparts) < 2){
            $count_paid = $wpdb->get_var("SELECT COUNT(*) FROM `wp_pay_for_locations`");
            include_once ABSPATH . "wp-content/plugins/settings/check_trial_period.php";

            $trial_status = $trial_package->status;
            $trial_activetill = $trial_package->expire_date;
            //$trial_activetill = date('Y-m-d',strtotime('2017-01-09')); //temp code
            $currentdate = date("Y-m-d");
            $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");

            if (!empty($billing_cancelon)) {
                $agency_stop_date = $billing_cancelon->meta_value;
                //print_r($agency_stop_date);
            } else {
                $agency_stop_date = '';
            }

            if ((($count_paid == 0) && ($trial_activetill < $currentdate)) || (($count_paid > 0) && (!empty($billing_cancelon)) && ($agency_stop_date < $currentdate))) {
                if ($_GET['parm'] != 'billing_payment' && $_GET['param'] != 'checkcode') {
                    $billing_url = site_url() . "/location-settings/?parm=billing_payment";
                    header("Location: " . $billing_url);
                    exit;
                }
            }
        }
    }
}

add_action('init','check_discountcode_used');

function check_discountcode_used() {
    include_once( plugin_dir_path( __FILE__ ) . 'check_discountcode_used.php' );
}
