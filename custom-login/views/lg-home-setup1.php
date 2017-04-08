<?php
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

session_start();

$profile_exist = get_option('profile_campaign');
$dash_url_site=site_url().'/dashboard/';

if($profile_exist){
  header("Location: $dash_url_site");
}else{
  wp_redirect(site_url() . '/custom-agency-login'); 
}


if (!isset($_SESSION['customuser'])) {
    header("location:" . site_url() . "/custom-agency-login/");
}
?>
<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/css/dashboard.css"/>
<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/css/intlTelInput.css"/>
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">
<style>
/*body.processing:before{
     background:rgba(255, 255, 255, 0.6) url("../images/loader.gif") no-repeat scroll 50% 50%;
    top: 0;
    left: 0;
    position: fixed;
    content: "";
    z-index: 100000000000000;    
    height: 100%;
    width: 100%;      
} 
body.processing {    
    position: relative;    
}*/
</style>
<div class="fix-header">
    <div class="col-md-12 pad_top_15 pad_bottom_15 border-bottom white-bg">
        <div class="logo"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/logo.png"></div>
    </div>

</div>


<div class="container">
    <div class="row three_colon_div">
        <div class="col-md-12 col-xs-12">
            <h2 class="text-center font_30 m-t-20">Welcome to <strong class="blue-color upper-text">Enfusen</strong></h2>
        </div>
        <!-- Email Confirmation DIV -->

        <div class="col-md-4 col-xs-12">
            <?php include_once LG_COUNT_PLUGIN_DIR . '/views/wizards/lg-mail.php'; ?>
        </div>
        <!-- Email Confirmation Ends Here Completed Profile DIV -->

        <!-- Show Profile Div -->

        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/wizards/lg-details.php'; ?>

        <!-- Show Profile Div Ends --> 

        <!-- Campaign Div -->

        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/wizards/lg-campaign.php'; ?>

        <!-- ../First Campaign Create Ends Here ! -->
    </div>
</div>

<?php include_once LG_COUNT_PLUGIN_DIR . '/views/lg-footer.php'; ?>






