<?php

/*
  Plugin Name: Custom Agency Login
  Plugin URI: http://www.rudrainnovatives.com
  Description: Custom Agency plugin is Developed to make the agency Login along with the dashboard and location managmemt.It also gives the visualization of Analytics section.
  Author: Rudra Innnovative Software
  Version: 10000.1.0
  Author URI: http://www.rudrainnovatives.com
 */

$dir = dirname(dirname(dirname(dirname(__FILE__))));
include_once $dir . '/global_config.php';
/**
 * Constants used in Plugin
 */
if (!defined('LG_DEBUG_MODE'))
    define('LG_DEBUG_MODE', false);
if (!defined('LG_FILE'))
    define('LG_FILE', __FILE__);
if (!defined('LG_CONTENT_DIR'))
    define('LG_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('LG_CONTENT_URL'))
    define('LG_CONTENT_URL', site_url() . '/wp-content');
if (!defined('LG_PLUGIN_DIR'))
    define('LG_PLUGIN_DIR', LG_CONTENT_DIR . '/plugins');
if (!defined('LG_PLUGIN_URL'))
    define('LG_PLUGIN_URL', LG_CONTENT_URL . '/plugins');
if (!defined('LG_PLUGIN_FILENAME'))
    define('LG_PLUGIN_FILENAME', basename(__FILE__));
if (!defined('LG_PLUGIN_DIRNAME'))
    define('LG_PLUGIN_DIRNAME', plugin_basename(dirname(__FILE__)));
if (!defined('LG_COUNT_PLUGIN_DIR'))
    define('LG_COUNT_PLUGIN_DIR', LG_PLUGIN_DIR . '/' . LG_PLUGIN_DIRNAME);
if (!defined('LG_COUNT_PLUGIN_URL'))
    define('LG_COUNT_PLUGIN_URL', site_url() . '/wp-content/plugins/' . LG_PLUGIN_DIRNAME);

/* Definition of Pages Slug */
if (!defined('PAGE_SLUG_LG_PAGE'))
    define('PAGE_SLUG_LG_PAGE', 'lg-login');

if (!defined('PAGE_SLUG_LG_HOME'))
    define('PAGE_SLUG_LG_HOME', 'lg-home');

if (!defined('PAGE_SLUG_LG_CRE'))
    define('PAGE_SLUG_LG_CRE', 'lg-cre');

if (!defined('PAGE_SLUG_LG_CITATION'))
    define('PAGE_SLUG_LG_CITATION', 'lg-citation');

if (!defined('PAGE_SLUG_LG_COMP'))
    define('PAGE_SLUG_LG_COMP', 'lg-comp');

if (!defined('PAGE_SLUG_LG_DASH'))
    define('PAGE_SLUG_LG_DASH', 'lg-dash');

if (!defined('PAGE_SLUG_LG_GA'))
    define('PAGE_SLUG_LG_GA', 'lg-gaconnect');

if (!defined('PAGE_SLUG_LG_GA_DIS'))
    define('PAGE_SLUG_LG_GA_DIS', 'lg-gadisconnect');

/**
 * Enqueue Files
 */
function lg_enqueue_files() {
    /* Style */
    wp_enqueue_style('lg-bootstrap.min.css', LG_COUNT_PLUGIN_URL . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('lg-sweetalert.css', LG_COUNT_PLUGIN_URL . '/assets/css/sweetalert.css');
    wp_enqueue_style('lg-datatable.css', LG_COUNT_PLUGIN_URL . '/assets/css/jquery.dataTables.min.css');
    wp_enqueue_style("lg-loader",LG_COUNT_PLUGIN_URL . '/assets/css/loader.css');
    /* Script */
    
    wp_enqueue_script('lg-validate.min.js', LG_COUNT_PLUGIN_URL . '/assets/js/jquery.validate.min.js');
    //wp_enqueue_script('cs-bootstrap.min.js', LG_COUNT_PLUGIN_URL . '/assets/js/bootstrap.min.js');
    wp_enqueue_script('lg-sweetalert.min.js', LG_COUNT_PLUGIN_URL . '/assets/js/sweetalert.min.js');
    wp_enqueue_script('lg-datatable.min.js', LG_COUNT_PLUGIN_URL . '/assets/js/jquery.dataTables.min.js');
    wp_enqueue_script('lg-script.js', LG_COUNT_PLUGIN_URL . '/assets/js/lg-script.js','','',true);
    wp_enqueue_script('lg-ga-script.js', LG_COUNT_PLUGIN_URL . '/assets/js/lg-ga-script.js');
    wp_enqueue_script("lg-initTelInput.js", LG_COUNT_PLUGIN_URL . "/assets/js/intlTelInput.js",'','',true);
    wp_enqueue_script("lg-oauthpopup.js", LG_COUNT_PLUGIN_URL . "/assets/js/oauthpopup.js");
}

add_action("wp_enqueue_scripts", "lg_enqueue_files");

/**
 * Adding Shortcodes
 */
function lg_page_shortcode() {
    global $wpdb;
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-login.php';
}

add_shortcode("lg-agency-login", "lg_page_shortcode");

function lg_home_shortcode() {
    global $wpdb;
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-home-setup1.php';
}

add_shortcode("lg-agency-home", "lg_home_shortcode");

function lg_cre_dashboard_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/cre_dashboard.php';
}

add_shortcode('lg_cre_dashboard', 'lg_cre_dashboard_shortcode');

function lg_citation_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-citation.php';
}

add_shortcode('agency-citation', 'lg_citation_shortcode');

function lg_competitor_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-competitor.php';
}

add_shortcode('agency-competitor', 'lg_competitor_shortcode');

function lg_analytics_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-analytics.php';
}

add_shortcode('agency-analytics', 'lg_analytics_shortcode');

function lg_dashbord_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-agency-dash.php';
}

add_shortcode('agency-dashboard', 'lg_dashbord_shortcode');

function lg_ga_connect_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-agency-ga-connect.php';
}

add_shortcode('ga-connect', 'lg_ga_connect_shortcode');

function lg_ga_disconnect_shortcode() {
    include_once LG_COUNT_PLUGIN_DIR . '/views/lg-agency-ga-disconnect.php';
}

add_shortcode('ga-disconnect', 'lg_ga_disconnect_shortcode');

/**
 * Creating Page on Plugin Activation
 */
function lg_create_pages() {

    /* Agency Login Page */
    $loginslug = PAGE_SLUG_LG_PAGE;
    if (!the_slug_exists($loginslug)) {
        $post_page = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_date' => date('Y-m-d H:i:s'),
            'post_slug' => $loginslug,
            'post_name' => 'Custom Agency Login',
            'post_content' => "[lg-agency-login]",
            'post_status' => 'publish',
            'post_title' => 'Custom Agency Login',
            'post_type' => 'page',
        );
        //insert page and save the id
        $app_page_values = wp_insert_post($post_page, false);
        //save the id in the database
        update_option('lg-login', $app_page_values);
    }

    /* Agency Home Page */
    $homeslug = PAGE_SLUG_LG_HOME;
    if (!the_slug_exists($homeslug)) {
        $post_home = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_date' => date('Y-m-d H:i:s'),
            'post_slug' => $homeslug,
            'post_name' => 'Agency Home',
            'post_content' => "[lg-agency-home]",
            'post_status' => 'publish',
            'post_title' => 'Agency Home',
            'post_type' => 'page',
        );
        //insert page and save the id
        $app_page_values = wp_insert_post($post_home, false);
        //save the id in the database
        update_option('lg-home', $app_page_values);
    }

    /* Agency Dashboard Page */
    $dashboardslug = PAGE_SLUG_LG_DASH;
    if (!the_slug_exists($dashboardslug)) {
        $post_dashboard = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_date' => date('Y-m-d H:i:s'),
            'post_slug' => $dashboardslug,
            'post_name' => 'Dashboard',
            'post_content' => "[agency-dashboard]",
            'post_status' => 'publish',
            'post_title' => 'Dashboard',
            'post_type' => 'page',
        );
        //insert page and save the id
        $app_page_dash = wp_insert_post($post_dashboard, false);
        //save the id in the database
        update_option('lg-dashboard', $app_page_dash);
    }

    /* Agency Dashboard Page */

    $gaconnectslug = PAGE_SLUG_LG_GA;
    if (!the_slug_exists($gaconnectslug)) {
        $post_ga = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_date' => date('Y-m-d H:i:s'),
            'post_slug' => $gaconnectslug,
            'post_name' => 'GA Connect',
            'post_content' => "[ga-connect]",
            'post_status' => 'publish',
            'post_title' => 'GA Connect',
            'post_type' => 'page',
        );
        //insert page and save the id
        $app_page_ga = wp_insert_post($post_ga, false);
        //save the id in the database
        update_option('lg-ga', $app_page_ga);
    }

$gaconnectslug = PAGE_SLUG_LG_GA_DIS;
    if (!the_slug_exists($gaconnectslug)) {
        $post_ga = array(
            'ping_status' => 'closed',
            'post_date' => date('Y-m-d H:i:s'),
            'post_slug' => $gaconnectslug,
            'post_name' => 'GA Disconnect',
            'post_content' => "[ga-disconnect]",
            'post_status' => 'publish',
            'post_title' => 'GA Disconnect',
            'post_type' => 'page',
        );
        //insert page and save the id
        $app_page_ga = wp_insert_post($post_ga, false);
        //save the id in the database
        update_option('lg-ga-dis', $app_page_ga);
    }
}

register_activation_hook(__FILE__, 'lg_create_pages');

/**
 * Including AJAX Request Handler
 */
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
        case "lg_lib":
            add_action('admin_init', 'lg_lib');

            function lg_lib() {
                global $wpdb;
                include_once LG_COUNT_PLUGIN_DIR . '/library/lg-library.php';
            }

            break;
        case "lg_cre_lib":
            add_action('admin_init', 'lg_cre_lib');

            function lg_cre_lib() {
                global $wpdb;
                include_once LG_COUNT_PLUGIN_DIR . '/library/lg-cre-library.php';
            }

            break;
    }
}
include_once LG_COUNT_PLUGIN_DIR . '/library/lg-functions.php';
// Code for "/index.php?s=Admin"
if(isset($_GET['s']) && !empty($_GET['s'])){
header("HTTP/1.1 301 Moved Permanently"); 
header("Location: /"); 
exit();
}
// code end for "/index.php?s=Admin"
?>
