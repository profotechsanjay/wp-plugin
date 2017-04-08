<?php

/*
  Plugin Name: Custom Agency Setup
  Plugin URI: http://www.rudrainnovatives.com
  Description: Custom Agency Setup plugin is Developed to make the agency setup more flexible to admin.
  Author: Rudra Innnovative Software
  Version: 1.0
  Author URI: http://www.rudrainnovatives.com
 */


$dir = dirname(dirname(dirname(dirname(__FILE__))));
include_once $dir . '/global_config.php';
/**
 * Constants used in Plugin
 */
if (!defined('CA_DEBUG_MODE'))
    define('CA_DEBUG_MODE', false);
if (!defined('CA_FILE'))
    define('CA_FILE', __FILE__);
if (!defined('CA_CONTENT_DIR'))
    define('CA_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('CA_CONTENT_URL'))
    define('CA_CONTENT_URL', site_url() . '/wp-content');
if (!defined('CA_PLUGIN_DIR'))
    define('CA_PLUGIN_DIR', CA_CONTENT_DIR . '/plugins');
if (!defined('CA_PLUGIN_URL'))
    define('CA_PLUGIN_URL', CA_CONTENT_URL . '/plugins');
if (!defined('CA_PLUGIN_FILENAME'))
    define('CA_PLUGIN_FILENAME', basename(__FILE__));
if (!defined('CA_PLUGIN_DIRNAME'))
    define('CA_PLUGIN_DIRNAME', plugin_basename(dirname(__FILE__)));
if (!defined('CA_COUNT_PLUGIN_DIR'))
    define('CA_COUNT_PLUGIN_DIR', CA_PLUGIN_DIR . '/' . CA_PLUGIN_DIRNAME);
if (!defined('CA_COUNT_PLUGIN_URL'))
    define('CA_COUNT_PLUGIN_URL', site_url() . '/wp-content/plugins/' . CA_PLUGIN_DIRNAME);

if (!defined('PAGE_SLUG_CS_PAGE'))
    define('PAGE_SLUG_CS_PAGE', 'cs-home');

/**
 * Enqueue Files
 */
/* function cs_enqueue_files() {
  /* Style */
/* wp_enqueue_style('cs1-bootstrap.min.css', CA_COUNT_PLUGIN_URL . '/assets/css/bootstrap.min.css');
  wp_enqueue_style('cs1-sweetalert.css', CA_COUNT_PLUGIN_URL . '/assets/css/sweetalert.css');
  wp_enqueue_style('cs1-datatable.css', CA_COUNT_PLUGIN_URL . '/assets/css/jquery.dataTables.min.css');
  wp_enqueue_style('cs1-style.css', CA_COUNT_PLUGIN_URL . '/assets/css/setup-style.css');
  /* Script */
/* wp_enqueue_script('cs1-validate.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/jquery.validate.min.js');
  //wp_enqueue_script('cs1-bootstrap.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/bootstrap.min.js');
  wp_enqueue_script('cs1-sweetalert.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/sweetalert.min.js');
  wp_enqueue_script('cs1-datatable.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/jquery.dataTables.min.js');
  wp_enqueue_script('cs1-script.js', CA_COUNT_PLUGIN_URL . '/assets/js/setup-script.js');
  }

  add_action("init", "cs_enqueue_files"); */

/**
 * Admin Menus
 */
function cs_menus() {
    global $wpdb;
    $c_id = get_current_user_id();
    $user = new WP_User($c_id);
    $u_role = $user->roles[0];

    if ($u_role == 'administrator') {
        add_menu_page('Custom Setup', 'Custom Setup', 'administrator', 'customsetup', 'cs_page_shortcode');
    }
}

add_action('admin_menu', 'cs_menus');

/**
 * Adding Shortcodes
 */
function cs_page_shortcode() {
    global $wpdb;
    include_once CA_COUNT_PLUGIN_DIR . '/views/cs-page.php';
}

add_shortcode("cs-agency-setup", "cs_page_shortcode");

function cs_billing_leads_page_shortcode() {
    global $wpdb;
    include_once CA_COUNT_PLUGIN_DIR . '/views/billing-leads-page.php';
}

add_shortcode("cs-agency-leads-page", "cs_billing_leads_page_shortcode");

function cs_billing_created_page_shortcode() {
    global $wpdb;
    include_once CA_COUNT_PLUGIN_DIR . '/views/billing-created-page.php';
}

add_shortcode("cs-agency-created-page", "cs_billing_created_page_shortcode");

/**
 * Creating Page on Plugin Activation
 */
function cs_create_pages() {

    /* Sign Up Page */
    $slug = PAGE_SLUG_CS_PAGE;
    $post_page = array(
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_date' => date('Y-m-d H:i:s'),
        'post_slug' => $slug,
        'post_name' => 'Custom Agency Setup',
        'post_content' => "[cs-agency-setup]",
        'post_status' => 'publish',
        'post_title' => 'Custom Agency Setup',
        'post_type' => 'page',
    );
    //insert page and save the id
    $app_page_values = wp_insert_post($post_page, false);
    //save the id in the database
    update_option('cs_home_page', $app_page_values);
}

register_activation_hook(__FILE__, 'cs_create_pages');

/**
 * Including AJAX Request Handler
 */
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
        case "cs_lib":
            add_action('admin_init', 'cs_lib');

            function cs_lib() {
                global $wpdb;
                include_once CA_COUNT_PLUGIN_DIR . '/library/setup-library.php';
            }

            break;
    }
}
?>

