<?php
/*
 Plugin Name: Client Setups
 Plugin URI: http://www.rudrainnovatives.com
 Description: This plugin is used to create MCC tools setup for different clients
 Author: Rudra Innnovative Software
 Version: 1.0
 Author URI: http://www.rudrainnovatives.com
*/

$dir = dirname(dirname(dirname(dirname(__FILE__))));
include_once $dir.'/global_config.php';
            
if (!defined('ST_DEBUG_MODE'))    define('ST_DEBUG_MODE',  false );
if (!defined('ST_FILE'))       define('ST_FILE',  __FILE__ );
if (!defined('ST_CONTENT_DIR'))      define('ST_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('ST_CONTENT_URL'))      define('ST_CONTENT_URL', site_url() . '/wp-content');
if (!defined('ST_PLUGIN_DIR'))       define('ST_PLUGIN_DIR', ST_CONTENT_DIR . '/plugins');
if (!defined('ST_PLUGIN_URL'))       define('ST_PLUGIN_URL', ST_CONTENT_URL . '/plugins');
if (!defined('ST_PLUGIN_FILENAME'))  define('ST_PLUGIN_FILENAME',  basename( __FILE__ ) );
if (!defined('ST_PLUGIN_DIRNAME'))   define('ST_PLUGIN_DIRNAME',  plugin_basename(dirname(__FILE__)) );
if (!defined('ST_COUNT_PLUGIN_DIR')) define('ST_COUNT_PLUGIN_DIR', ST_PLUGIN_DIR.'/'.ST_PLUGIN_DIRNAME );
if (!defined('ST_COUNT_PLUGIN_URL')) define('ST_COUNT_PLUGIN_URL', site_url().'/wp-content/plugins/'.ST_PLUGIN_DIRNAME );        
if (!defined('ST_VERSION')) define('ST_VERSION', '1.3');
if (!defined('ST_KEY')) define('ST_KEY', 'abcde4562246789947363447hdgyr785hnhyrkj8');

if (!defined('PROTOCOL')) define('PROTOCOL', 'http://');

function setup_table(){
	global $wpdb;
	return $wpdb->prefix . 'setup_table';
}

function ST_install_table()
{
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . setup_table() . '"')) == 0){
		$sql = 'CREATE TABLE ' . setup_table() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,               
		client_id int(11),
                acctype int(2) default "1",
                name varchar(300) NOT NULL,
                email varchar(300) NOT NULL,               
                login varchar(200) NOT NULL,
		password varchar(200) NOT NULL,
                prefix varchar(100),
                url varchar(500),
                dir varchar(500),   
                white_lbl varchar(500),                
                analytic_url varchar(500),
                analytic_dir varchar(500),
		status int(1) default "1",                
                db_name varchar(200),
                analytic_db varchar(200),
                grader_db varchar(200),
                db_username varchar(200),
		db_password varchar(200),
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
		
	}
	
}
register_activation_hook(__FILE__,'ST_install_table');


function ST_menus(){
    
    global $wpdb;
    add_menu_page('Client Setups', 'Client Setups', 'administrator', 'client_setups', 'client_setups',ST_COUNT_PLUGIN_URL.'/plugin_icon.png',3);
    add_submenu_page('client_setups', 'client_setups', 'Client Setups', 'administrator', 'client_setups', 'client_setups');
    add_submenu_page('client_setups', 'client_setups', 'New Setup', 'administrator', 'new_setup', 'new_setup');	
}

add_action('admin_menu', 'ST_menus');

function client_setups(){
	global $wpdb;
        if(isset($_REQUEST['deployment'])){
            include_once ST_COUNT_PLUGIN_DIR . '/views/deployment.php';
        }
	else{
            include_once ST_COUNT_PLUGIN_DIR . '/views/client_setups.php';
        }
}

function new_setup(){
	global $wpdb;
	include_once ST_COUNT_PLUGIN_DIR . '/views/new_setup.php';	
}

function ST_scriptsstyles_function(){
        
	$slug = '';
        if(isset($_REQUEST['page']) && $_REQUEST['page'] != ''){
            $slug = trim($_REQUEST['page']); 
        }
       
        $arr = array("client_setups","new_setup");        
        if(in_array($slug, $arr)) {                

                wp_enqueue_script('jquery');	                        
                wp_enqueue_script('jquery-ui.min.js', ST_COUNT_PLUGIN_URL .'/assets/js/jquery-ui.min.js');
                
                wp_enqueue_script('jquery.datetimepicker.full.js', ST_COUNT_PLUGIN_URL .'/assets/js/jquery.datetimepicker.full.js');                

                wp_enqueue_script('bootstrap.js', ST_COUNT_PLUGIN_URL .'/assets/js/bootstrap.js');
                wp_enqueue_script('jquery.visible.min.js', ST_COUNT_PLUGIN_URL .'/assets/js/jquery.visible.min.js');
                
                wp_enqueue_script('jquery.validate.js', ST_COUNT_PLUGIN_URL .'/assets/js/jquery.validate.js');
                
                wp_enqueue_script('jquery.dataTables.js', ST_COUNT_PLUGIN_URL .'/assets/js/jquery.dataTables.js');
                                                
                wp_enqueue_script('chosen.jquery.js', ST_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', ST_VERSION);
                wp_enqueue_script('script.js', ST_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', ST_VERSION);


                // style        
                                
                
                wp_enqueue_style('style.css', ST_COUNT_PLUGIN_URL .'/assets/css/style.css','', ST_VERSION);
                wp_enqueue_style('bootstrap.css', ST_COUNT_PLUGIN_URL .'/assets/css/bootstrap.css','', ST_VERSION);               
                wp_enqueue_style('jquery.datetimepicker.css', ST_COUNT_PLUGIN_URL .'/assets/css/jquery.datetimepicker.css');
                wp_enqueue_style('font-awesome.min.css', ST_COUNT_PLUGIN_URL .'/assets/css/font-awesome.min.css');
                wp_enqueue_style('jquery.dataTables.css', ST_COUNT_PLUGIN_URL .'/assets/css/jquery.dataTables.css');                  
                wp_enqueue_style('chosen.css', ST_COUNT_PLUGIN_URL .'/assets/css/chosen.css');                  
                wp_enqueue_style('components.min.css', site_url() . '/wp-content/themes/twentytwelve/report-theme/assets/global/css/components.min.css');
                
        }
        
	
}
add_action('init','ST_scriptsstyles_function');

add_action('wp_head','ST_pluginname_ajaxurl');
function ST_pluginname_ajaxurl() {
    if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){
        ?>
            <script type="text/javascript">
                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            </script>
            <?php
    }
    else{
        
            ?>
            <script type="text/javascript">
                var ajaxurl = '<?php echo str_replace(array('https','HTTPS'), array('http','HTTP'), admin_url('admin-ajax.php')); ?>';
            </script>
            <?php
        
    }

}

add_action('init','add_js_work_onall_files');

function add_js_work_onall_files(){
    //global $wpdb;
    //wp_enqueue_script('commonjsinternal.js', ST_COUNT_PLUGIN_URL .'/assets/js/commonjsinternal.js?ver=','', ST_VERSION);    
}

if(isset($_REQUEST['action'])){    
	switch($_REQUEST['action']){
		case "setup_lib":
		add_action( 'admin_init', 'setup_lib' );
		function setup_lib(){
			global $wpdb;                        
			include_once ST_COUNT_PLUGIN_DIR . '/library/client-setup-lib.php';
		}
		break;
                case "login":
		add_action( 'admin_init', 'login' );
		function login(){
			global $wpdb;                        
			include_once ST_COUNT_PLUGIN_DIR . '/library/admin_login.php';
		}
                case "rudra_api":
		add_action( 'admin_init', 'rudra_api' );
		function rudra_api(){                    
                        global $wpdb;                        
                        include_once ST_COUNT_PLUGIN_DIR . '/library/api.php';
		}
		break;
	}
	
}

function ST_filter_plugin_updates( $value ) {
    if(isset($value->response['client-setup/client-setup.php'])){
        unset( $value->response['client-setup/client-setup.php'] );
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'ST_filter_plugin_updates' );


// Remove location users 
add_action('pre_user_query','agency_user_query');
function agency_user_query($user_search) {    
    
    global $wpdb;
    $tbl = $wpdb->prefix.'setup_table';
    $clients = $wpdb->get_results("SELECT client_id FROM $tbl WHERE client_id != '' AND status = 1");
    if(!empty($clients)){
        $users = '';
        foreach($clients as $client){
            $users .= $client->client_id .',';
        }
        $users = substr($users, 0 , -1);        
        $user_search->query_where = str_replace('WHERE 1=1',
        "WHERE 1=1 AND {$wpdb->users}.ID NOT IN ($users)",$user_search->query_where);
    }
}

function httpurl($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function agencieslist_shortcode(){
	include_once ST_COUNT_PLUGIN_DIR . '/views/agencieslist.php';
}

add_shortcode('agencieslist', 'agencieslist_shortcode');

function dbexecute($db,$params){
    
    $conn = new mysqli(database_host_wp, database_user_wp, database_password_wp, $db);
    if ($conn->connect_error) {
        pr($conn->connect_error); die;
    }
    
    if($params['type'] == 'countorders'){
        $status = mysqli_real_escape_string($conn, $params['status']);
        $writer_id = mysqli_real_escape_string($conn, $params['writer_id']);
        
//        if($status == 'all-order'){
//            $sql = "SELECT COUNT(order_id) FROM wp_content_order WHERE writer_id = $writer_id AND user_id in(SELECT MCCUserId FROM wp_client_location)" ;
//        }
//        else {
//            $sql = "SELECT COUNT(order_id) FROM wp_content_order WHERE writer_id = $writer_id AND status = '$status' AND user_id in(SELECT MCCUserId FROM wp_client_location)" ;
//        }
        
        if($status == 'all-order'){
            $sql = "SELECT COUNT(order_id) FROM wp_content_order WHERE writer_id = $writer_id" ;
        }
        else {
            $sql = "SELECT COUNT(order_id) FROM wp_content_order WHERE writer_id = $writer_id AND status = '$status'" ;
        }
        
        $result = mysqli_query($conn, $sql);
        if(!$result){
            return 0;
        }
        
        $total = $result->fetch_array(MYSQLI_NUM);
        if(isset($total) && $total > 0)
            return $total[0];
        else
            return 0;
    }
    
}


/*
add_filter( 'page_template', 'announcement_page_template_tool' );

function announcement_page_template_tool($page_template){
	global $post;
	$post_slug = $post->post_name;          
	if (ST_slug_exists($post_slug) && $post_slug == 'announcement'){
		$page_template = ST_COUNT_PLUGIN_DIR. '/views/front-announcement-template.php';	                
	} 
	return $page_template;
}


function ST_slug_exists($post_name) {
    global $wpdb;
    $posts = $wpdb->prefix."posts";
    $sql ="SELECT post_name FROM $posts WHERE post_name = '" . $post_name . "'";       
    if($wpdb->get_row($sql, 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

function create_announcement_page(){
    global $wpdb;
    $wp_rewrite = new WP_Rewrite();
    
    $slug = ST_SLUG;

    if (!ST_slug_exists($slug)){
        $_p = array();
        $_p['post_title']     = "Announcement";        
        $_p['post_status']    = 'publish';
        $_p['post_slug']    = $slug;
        $_p['post_type']      = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status']    = 'closed';    
        wp_insert_post($_p);        
    } 
    
}

register_activation_hook(__FILE__,'create_announcement_page');
*/

