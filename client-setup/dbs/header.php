<?php
global $wpdb, $current_user;

$tablinfo = $wpdb->prefix."client_company_info";
$dy_content = $wpdb->get_row($wpdb->prepare('select logo, fb_connect_code, google_tag_manager, client_success_manager FROM '.$tablinfo));

if (!is_page_template('order-content.php') && !is_page_template('master-admin.php') && !is_page_template('keywords-report.php') && !is_page_template('traffic-reports.php')) {
    $USRID = $CURENT_ID = '';
    mcc_sessionInit($USRID, $CURENT_ID);
}

$USRID = $_SESSION["Current_user_live"];
/*if (is_user_logged_in()) {
    if (current_id() == user_id() && role(current_id()) == 'worker' && !is_page_template('new-dashboard-mcc.php')) {
        wp_redirect(site_url() . '/start');
        exit;
    }
}*/
if (!empty($USRID) && !empty($_SESSION["Current_user_live"])) {

    $BRand_name = get_user_meta($USRID, "BRAND_NAME");
    $current_user_name = "";

    if (role($USRID) == 'worker') {
        $current_user_name = full_name($USRID);
    } else {

        if (empty($BRand_name[0])) {
            $current_user_name = full_name($USRID); //$current_user->user_nicename;
        } else {
            $current_user_name = $BRand_name[0];
        }
    }
}

global $mccTourSteps;
$mccTourSteps = array();

/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]><html class="ie ie7" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]><html class="ie ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,400italic&subset=latin,latin-ext" type="text/css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_template_directory_uri(); ?>/font/custom-fonts.css" />
        <meta name="msvalidate.01" content="91675594F654CCCB887DD033B715B749" />
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width" />
        <title><?php
            wp_title('|', true, 'right');

            echo ' - MCC';
            ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions.   ?>
        <!--[if lt IE 9]>
        <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
        <![endif]-->
        <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.12.1.js" type="text/javascript"></script>
        <?php
        $BRand_name_User = get_user_meta($CURENT_ID, "BRAND_NAME", true);

        $level_User = get_user_meta($CURENT_ID, "USER_LEVEL", true);

        $USER_LEVELS_User = ($level == '' ? '1' : str_replace("level_", "", $level));



        wp_enqueue_script('mcc-custom', get_template_directory_uri() . '/js/custom.js', 'jquery', '1.3', true);

        wp_localize_script('mcc-custom', 'mccJs', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'link' => site_url() . '/user?usider=',
            'result' => is_user_logged_in() ? getClients() : array(),
            'label' => $BRand_name_User,
            'uid' => $CURENT_ID,
            'level' => $USER_LEVELS_User,
            'role' => ($USER_LEVELS_User == 4 ? 'Assessment' : 'Client')
        ));



        wp_enqueue_script('jquery-ui-autocomplete');

        wp_head();



        if (isset($_GET['msg']) && $_GET['msg'] == 'log') {

            unset($_SESSION['userid']);

            unset($_SESSION['Current_user_live']);
            unset($_SESSION['pdf_report']);

            unset($_SESSION['session_all_notfc_id']);
        }
        ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_template_directory_uri(); ?>/css/n-style.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_template_directory_uri(); ?>/css/n-responsive.css" />

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/bootstrap/css/bootstrap.min.css?ver=1.0.1" rel="stylesheet" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/layouts/layout3/css/layout.min.css?ver=1.0.4" rel="stylesheet" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/layouts/layout3/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/layouts/layout3/css/custom.min.css?ver=1.0.7" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
	<?php if(isset($dy_content) && !empty($dy_content)){
		echo html_entity_decode(stripcslashes($dy_content->fb_connect_code));
	} ?>
        
    <script>var site_url = "<?php echo site_url(); ?>";</script>
    <!----------->
    <script type="text/javascript">
        function task_popup_func(task_popup_task_list_id, task_popup_user_id) {
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo site_url(); ?>/ajax-data.php',
                data: {'page': 'task_popup', 'task_popup_task_list_id': task_popup_task_list_id, 'task_popup_user_id': task_popup_user_id},
                success: function(html_data) {
                    //alert(html_data);
                }
            });
        }

        jQuery(document).ready(function($) {
            $(".task_poup").fancybox({
                'overlayShow': false,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic'
            });
        });
    </script>
    <?php include_once ('page-blocked.php'); ?>
    <!-----------------for fancy box [POPUP] -------------------->
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/js/jquery.fancybox.css?v=2.1.5" media="screen" />
    <script type="text/javascript">jQuery(".fancybox").fancybox();</script>
    <!-----------------end for fancy box [POPUP] -------------------->
</head>
<body class="page-container-bg-solid page-boxed">


<!-- Google Tag Manager -->
	<?php if(isset($dy_content) && !empty($dy_content)){
		echo html_entity_decode(stripcslashes($dy_content->google_tag_manager));
	} ?>
<!-- End Google Tag Manager -->









    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_setting_id'])) {



        $prev_id = $_SESSION["Current_user_live"];



        $next_id = $_POST['user_setting_id'];



        $rd_url = str_replace('usider=' . $prev_id, 'usider=' . $next_id, "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);



        $_SESSION["Current_user_live"] = $next_id;



        wp_redirect($rd_url);



        exit;
    }
    ?>







    <script type="text/javascript">



        function user_setting_func(user_setting_id) {



            jQuery('#user_setting_id').val(user_setting_id);



            document.forms.user_setting_Frm.submit();



        }



    </script>







    <form name="user_setting_Frm" action="" method="post">



        <input type="hidden" id="user_setting_id" name="user_setting_id" value="">



    </form> 
    <style>#primary{min-height: 610px;}</style>
    <script src="<?php echo site_url(); ?>/data-table/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="<?php echo site_url(); ?>/data-table/jquery.dataTables.css"/>
    <!------ FOR BOOTSTARP ---

        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />

        <script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

    <!------ FOR BOOTSTARP --->
    <!-- BEGIN HEADER -->
    <div class="page-header" style="margin-bottom:0px; padding:0px;">
        <div class="page-header-top">
            <div class="container-fluid">
                <a href="javascript:;" class="menu-toggler"></a>
            </div>
        </div>        
        <?php
        if (is_user_logged_in()) {
            $level = user_level($USRID);
            $level_assign = level_assign($level);
        }
        ?>
        <!-- BEGIN HEADER MENU -->
        <div class="page-header-menu">
            <?php include_once ('common/top-menu.php'); ?>
        </div>
        <!-- END HEADER MENU -->
    </div>
    <!-- END HEADER -->
    <style>
        //#loading-image{text-align: center; margin-top: 40px;}
       #loading-image{
            position:fixed;
            top:0;
            right:0px;
            width:100%;
            height:100%;
            background-color:white;
            background-image:url('<?php echo get_template_directory_uri(); ?>/images/ajax_loader.gif');
            background-repeat:no-repeat;
            background-position:center;
            z-index:10000000;
            opacity: 1;
            filter: alpha(opacity=40);
        }
    </style>
    <div id="loading-image">
        <!--<img src="<?php echo get_template_directory_uri(); ?>/images/page-loader.gif" alt="Loading..." />-->
    </div>
    <script type='text/javascript'>        
        jQuery(window).bind('load',function() {
            jQuery('#loading-image').fadeOut(1500);
        });
    </script>

	<?php if(isset($dy_content) && !empty($dy_content)){
		echo html_entity_decode(stripcslashes($dy_content->client_success_manager));
	} ?>
