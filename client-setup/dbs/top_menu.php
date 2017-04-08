<?php
global $billing_enable;
wp_enqueue_script('validate.js', get_template_directory_uri() .'/js/jquery.validate.js','','');

$image = get_template_directory_uri().'/images/new/logo-2.png';
if(isset($dy_content) && !empty($dy_content)) {
    $image = $dy_content->logo;
}

$style = 'style="width:178px; height: auto; margin-top:0;"';

$comptablinfo = $wpdb->prefix."client_company_info";
if($wpdb->query("SHOW COLUMNS FROM ".$comptablinfo." LIKE 'logo_css'") == 1){
    $css = $wpdb->get_var("SELECT logo_css FROM $comptablinfo");
    if(!empty($css)){
        $css = json_decode($css);
        $ht = $css->height.'px';
        if($css->height == 'auto'){
            $ht = $css->height;
        }
        $style = 'style="width:'.$css->width.'px; height: '.$ht.'; margin-top:'.$css->toplogo.'px;"';
    }
}
else{
    $uid = user_id();
    if ($uid > 0) {
        $crever = get_user_meta($uid, 'crever', TRUE);
        if ($crever != CREVER) {
            $style = 'style="width: 55px; height: 34px; margin-top:0;"';
        }
    }
}

$user_id = user_id();
$update_level_name = update_level_name($user_id);
if (isset($_POST['menu_control_btn'])) {
    update_option('menu_control', $_POST);
}

$menu_control = get_option('menu_control');
if (is_user_logged_in()) {
    $mccTourSteps[] = array(
        'element' => '#tour-clntSrch',
        'placement' => 'right',
        'title' => 'Client Search Bar',
        'content' => '<div style="width: 240px"><div style="width: 46%;height: 150px;float: right;"><img src="' . get_template_directory_uri() . '/images/_0033_Nutty_Professor-25.png" width="90" height="140" alt=""></div>The Marketing Grader identifies important sales and marketing statistics for your company. Signing a mutual NDA from Microsoft will give you access to a discovery form, assessment, assessment review call and marketing audit.</div>'
    );
    ?>
    <div class="container-fluid" style="padding-left:0; padding-right:0;">
        <!-- BEGIN MEGA MENU -->
        <!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
        <!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
        <div class="hor-menu hor-menu-full">
            <ul class="nav navbar-nav navbar-nav-full">
                <li>
                    <a href="<?php echo site_url(); ?>" style="padding-top:9px;padding-bottom:8px;">
                        <img <?php echo $style; ?> src="<?php echo $image; ?>" alt="logo" class="logo-default">
                    </a>
                </li>
                <li><a href="<?php echo site_url(); ?>/dashboard">&nbsp;Client List&nbsp;</a>
                  <!-- BEGIN HEADER SEARCH BOX -->
                    <!--form id="tour-clntSrch" class="search-form pull-left" method="GET">
                        <div class="input-group">
                            <?php $clntVal = isset($_GET['cname']) ? $_GET['cname'] : ''; ?>
                            <input type="text" class="form-control" placeholder="Location Search" name="cname" id="lc-search" value="<?php echo $clntVal; ?>">
                            <span class="input-group-btn">
                                <a href="javascript:;" class="btn submit">
                                    <i class="icon-magnifier"></i>
                                </a>
                            </span>
                        </div>
                    </form-->
                    <!-- END HEADER SEARCH BOX -->
                </li>
                
                <li class="menu-dropdown classic-menu-dropdown  nav-drop-arrow">
                    <a href="<?php echo site_url(); ?>/user?usider=<?php echo $USRID; ?>&action=account_info" class="nav-link"> Account Info </a>
                </li>
                <li class="menu-dropdown classic-menu-dropdown  nav-drop-arrow">
                    <a href="<?php echo site_url(); ?>/keyword-campaigns/">Keyword Tool

                    </a>
                </li>
                <?php if ($menu_control[$update_level_name]['analytics'] != 'No') { ?>
                    <li class="menu-dropdown classic-menu-dropdown  nav-drop-arrow">
                        <a href="javascript:;">Analytics
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                          <?php  if ($menu_control[$update_level_name]['traffic-reports'] != 'No') { ?>
                              <li><a href="<?php echo site_url(); ?>/traffic-reports/" class="nav-link">Traffic Report</a></li>
                              <?php } if ($menu_control[$update_level_name]['conversion-url-report'] != 'No') { ?>
                                  <li><a href="<?php echo site_url(); ?>/conversion-url-report/" class="nav-link">Conversion Report</a></li>
                              <?php }?>
                            <?php /*if ($menu_control[$update_level_name]['keywords-report'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/keywords-report/" class="nav-link">Keyword Report</a></li>
                            <?php } if ($menu_control[$update_level_name]['keyword-history-report'] != 'No') { ?>
                                <!--<li><a href="<?php echo site_url(); ?>/keyword-history-report/" class="nav-link">Historical Report</a></li>-->
                            <?php } if ($menu_control[$update_level_name]['traffic-reports'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/traffic-reports/" class="nav-link">Traffic Report</a></li>
                            <?php } if ($menu_control[$update_level_name]['kpi-tracker'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/master-admin/?type=kpi-tracker" class="nav-link">KPI Tracker</a></li>
                            <?php } if ($menu_control[$update_level_name]['conversion-url-report'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/conversion-url-report/" class="nav-link">Conversion Report</a></li>
                            <?php } if ($menu_control[$update_level_name]['ranking-url-vs-target-url'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/ranking-url-vs-target-url/" class="nav-link">Ranking Vs Target URL</a></li>
                            <?php } if ($menu_control[$update_level_name]['competitor-report'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/competitor-report/" class="nav-link">Competitor Report</a></li>
                            <?php } if ($menu_control[$update_level_name]['single-monthly-report'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/single-monthly-report/" class="nav-link">Report Scheduler</a></li>
                            <?php } if ($menu_control[$update_level_name]['executive-summary-report'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/executive-summary-report/" class="nav-link">Executive Summary Report</a></li>
                            <?php }*/ ?>
                        </ul>
                    </li>
                <?php } ?>
                
                <?php if ($menu_control[$update_level_name]['recommendations'] != 'No') { ?>
                    <li class="menu-dropdown classic-menu-dropdown  nav-drop-arrow">
                        <a href="javascript:;">Insights
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <?php if ($menu_control[$update_level_name]['re_keyword'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/keyword-campaigns/?show_all_keywords"   class="nav-link all_keys">Keywords</a></li>
                            <?php } if ($menu_control[$update_level_name]['re_content'] != 'No') { ?>
                                <li><a href="<?php echo site_url(); ?>/content-recommendation-dashboard/" class="nav-link">Content</a></li>
                            <?php } if ($menu_control[$update_level_name]['site-audit'] != 'No') { ?>
                                <li class="<?php echo $type == 'all-user' ? 'curnt' : ''; ?>"><a href="<?php echo site_url(); ?>/site-audit-url/" class="nav-link">Site Audit</a></li>
                            <?php } if ($menu_control[$update_level_name]['citation-tracker'] != 'No') { ?>
                                <li class="<?php echo $type == 'all-user' ? 'curnt' : ''; ?>"><a href="<?php echo site_url(); ?>/citation-tracker/" class="nav-link">Citations</a></li>
                            <?php } ?>

                        </ul>
                    </li>
                <?php } ?>

                <li class="nav-menu-info pull-right">
                    <?php echo website_format(get_user_meta(user_id(), 'website', true)); ?> <br /> <?php echo user_email(current_id()); ?>
                </li>
		<li class="menu-dropdown classic-menu-dropdown nav-drop-arrow">
			<?php echo do_shortcode('[shortcode_dropdownsites]') ?>
		</li>
                <?php
                $mccTourSteps[] = array(
                    'element' => '#tour-Gear',
                    'placement' => 'left',
                    'title' => 'Settings Gear Icon',
                    'content' => '<div style="width: 240px"><div style="width: 46%;height: 150px;float: right;"><img src="' . get_template_directory_uri() . '/images/_0033_Nutty_Professor-25.png" width="90" height="140" alt=""></div>The settings gear icon houses all of the administrative tools inside of MCC. You can find account and user information, the client control center, reports page, and the logout button in this menu.</div>'
                );
                ?>

                <li class="menu-dropdown classic-menu-dropdown  pull-right" id="tour-Gear">
                    <a href="javascript:;"> <i class="icon-settings"></i>
                        <span class="arrow"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li class="dropdown-submenu ">
                            <a href="javascript:;" class="nav-link nav-toggle ">
                                <i class="icon-settings"></i> Account Settings
                                <span class="arrow"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url(); ?>/profile/" class="nav-link">Personal Profile</a></li>
                                <li><a href="<?php echo site_url(); ?>/analytics-settings/" class="nav-link">Analytics</a></li>
                                <!--<li><a href="<?php echo site_url(); ?>/ranking-urls/" class="nav-link">Add Ranking URL</a></li>-->
                            </ul>
                        </li>
                        <?php echo do_shortcode('[admin_setting_menu]') ?>
                        <!--<li><a href="<?php echo wp_logout_url($redirect); ?>"><i class="icon-logout "></i>Logout</a></li>-->
                        <!--li><a class="logot" href="<?php echo wp_logout_url(site_url() . '/login-2/?msg=log'); ?>"><i class="icon-logout "></i>Logout</a><li-->
<!--li><a href="<?php echo site_url().'/dashboard'; ?>"><i class="icon-settings"></i> Campaigns</a><li-->
<li><a class="logot" href="<?php echo wp_logout_url(site_url() . '/custom-agency-login/?state=lg'); ?>"><i class="icon-logout "></i>Logout</a><li>
                    </ul>
                </li>

                <?php
                if ($menu_control[$update_level_name]['message_notification'] != 'No') {

                    if (!empty($level)) {
                        $count_unread_notifications = $wpdb->get_row("SELECT count(*) as unread_notfc FROM wp_notification where `read` = 0 and status = 'inbox' and notification_id IN($all_notfc_id)")->unread_notfc;
                        $latest_unread_notifications = $wpdb->get_results("SELECT * FROM wp_notification where notification_id IN($all_notfc_id) and status = 'inbox' and `read` = 0 order by created_date desc, notification_id desc LIMIT 10"); //`read` asc,
                        if (array_key_exists('message-board', $level_assign)) {

                            $mccTourSteps[] = array(
                                'element' => '#tour-msgBox',
                                'placement' => 'left',
                                'title' => 'Notifications Box',
                                'content' => '<div style="width: 240px"><div style="width: 46%;height: 150px;float: right;"><img src="' . get_template_directory_uri() . '/images/_0033_Nutty_Professor-25.png" width="90" height="140" alt=""></div>The Notifications Box highlights the number of unread messages in your message center. Clicking on this box will give you access to the full message center.</div>'
                            );
                            ?>

                            <li id="tour-msgBox" class="pull-right">
                                <a href="<?php echo site_url(); ?>/message-board/"> <i class="icon-bell"></i>
                                    <span class="badge badge-default" style="background:#F00"><?php echo $count_unread_notifications > 0 ? $count_unread_notifications : '0'; ?></span>
                                </a>
                            </li>

                            <?php
                        }
                    }
                }
                ?>
                <?php if ($menu_control[$update_level_name]['training'] != 'No') { ?>
                    <li class="menu-dropdown classic-menu-dropdown  nav-drop-arrow pull-right trainingicon">
                        <a href="javascript:;" target="_blank" class="nav-link"><i class="icon-graduation"></i></a>
                               <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url(); ?>/training-tool/" class="nav-link">Marketing Training</a></li>
                                <li><a href="https://www.enfusen.com/enfusen-software-training/" class="nav-link">Software Training</a></li>
                            </ul>
                    </li>
                <?php } ?>

            </ul>
        </div>
        <!-- END MEGA MENU -->
    </div>

    <?php
}?>

<?php
/*
$user = wp_get_current_user();
$allowed_roles = array('administrator');
if( array_intersect($allowed_roles, $user->roles ) ){
 *
 */
$c_id = get_current_user_id();
$user = new WP_User($c_id);
$u_role = $user->roles[0];
$count_paid = $wpdb->get_var("SELECT COUNT(*) FROM `wp_pay_for_locations`");
if(BILLING_ENABLE == 1){

    if(($u_role == 'administrator' || administrator_permission()) && ($count_paid == 0)){

        include ABSPATH . "wp-content/plugins/settings/check_trial_period.php";
        if(isset($trial_package) && !empty($trial_package)){

            $trial_status = $trial_package->status;
            $trial_activetill = date("Y-m-d", strtotime($trial_package->expire_date));
            //$trial_activetill = '2017-01-09'; //temp code

            $currentdate = date("Y-m-d");
            if($trial_activetill >= $currentdate){
                $date1 = $currentdate;
                //echo "<br>";
                $date2 = $trial_activetill;

                //Convert them to timestamps.
                $date1Timestamp = strtotime($date1);
                $date2Timestamp = strtotime($date2);

                //Calculate the difference.
                $difference = $date2Timestamp - $date1Timestamp;

                $days_left = $difference/86400;
                if($trial_activetill == $currentdate){
                    $days_left = 1;
                }
                $billing_page = site_url().'/'.ST_LOC_PAGE."?parm=billing_payment";
                ?>
                <div class="alert alert-danger" style="float: left;margin-top: 10px;padding: 3px 0;text-align: center;width: 100%;">
                    TRIAL ACCOUNT <strong ><?php echo $days_left;?> DAYS LEFT.</strong><a class="btn btn-primary" style="border-radius: 3px !important;margin: 0 0 0 17px;padding: 4px 7px !important;" href="<?php echo $billing_page;?>">Subscribe</a>
                </div>
                <?php

            } else {
			?>
                    <div class="alert alert-danger" style="float: left;margin-top: 10px;padding: 3px 0;text-align: center;width: 100%;">
                        YOUR TRIAL HAS BEEN EXPIRED. <a class="btn btn-primary" style="border-radius: 3px !important;margin: 0 0 0 17px;padding: 4px 7px !important;" href="<?php echo $billing_page;?>">Subscribe</a>
                    </div>
			<?php
		}
        }
    }
    else if(($u_role == 'administrator' || administrator_permission()) && ($count_paid > 0)){
        $currentdate = date("Y-m-d");
        $billing_cancelon = $wpdb->get_row("SELECT meta_value FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");

        if (!empty($billing_cancelon)) {
            $agency_stop_date = $billing_cancelon->meta_value;
            //print_r($agency_stop_date);
        } else {
            $agency_stop_date = '';
        }
        if (!empty($billing_cancelon) && ($agency_stop_date < $currentdate)) {
	    	?>
                    <div class="alert alert-danger" style="float: left;margin-top: 10px;padding: 3px 0;text-align: center;width: 100%;">
                        YOUR SUBSCRIPTION HAS BEEN EXPIRED. <a class="btn btn-primary" style="border-radius: 3px !important;margin: 0 0 0 17px;padding: 4px 7px !important;" href="<?php echo $billing_page;?>">Click here to subscribe again</a>
                    </div>
			<?php
  	}
        else if (!empty($billing_cancelon) && ($agency_stop_date >= $currentdate)) {
	    	?>
                    <div class="alert alert-danger" style="float: left;margin-top: 10px;padding: 3px 0;text-align: center;width: 100%;">
                        YOUR SUBSCRIPTION EXPIRATION DATE IS. <strong> <?php echo date('D d M Y',strtotime("$agency_stop_date")); ?> </strong>
                    </div>
			<?php
  	}
    }
}
?>

