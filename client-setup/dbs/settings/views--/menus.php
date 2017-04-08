<?php
include_once ABSPATH . "wp-content/plugins/settings/get_location_package_prices.php";
$trial_status = $locations_package_prices->lp_trial;
$trial_activetill = $locations_package_prices->lp_triallastdate;
if(isset($trial_status) && !empty($trial_status) && ($trial_status == 'active')){
    $trial_status;
    $trial_activetill;
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
        if($days_left >= 0){ 
            $billing_page = site_url().'/'.ST_LOC_PAGE."?parm=billing_info";
            ?>
            <div class="alert alert-danger">
                TRIAL ACCOUNT <strong ><?php echo $days_left;?> DAYS LEFT.</strong><a class="btn btn-primary" style="border-radius: 3px !important;margin: 0 0 0 17px;" href="<?php echo $billing_page;?>">Subscribe</a>
            </div>
        <?php }
    }
}

/*
$check_trial = check_trial();
$trial_period = $check_trial['trial_period'];
if($trial_period == 'enable'){
    echo '<div style="color: #008000; float: left; font-size: 18px; font-weight: 800; text-align: right; width: 100%;">TRIAL ACCOUNT</div>';
}
 * *
 */
?>

<div class="row">
    <div class="col-lg-12">
        
        <ul class="nav nav-tabs tabstop locations_manager">
            <li class="<?php echo (!isset($_GET['parm']) || $_GET['parm'] == 'account_info')?'active':''; ?>"><a href="?parm=account_info">Account Info</a></li>
            <li class="<?php echo ($_GET['parm'] == 'company_info')?'active':''; ?>"><a href="?parm=company_info">Company Info</a></li>            
            <li class="<?php echo ($_GET['parm'] == 'billing_info' || $_GET['parm'] == 'add_ons' || $_GET['parm'] == 'payment_history' || $_GET['parm'] == 'purchased_addons')?'active':''; ?>"><a href="?parm=billing_info">Billing Info</a></li>
            <!--<li class="<?php echo ($_GET['parm'] == 'invoices_recieved')?'active':''; ?>"><a href="?parm=invoices_recieved">Invoices</a></li>-->
            <li class="<?php echo ($_GET['parm'] == 'master-user-list')?'active':''; ?>"><a href="?parm=master-user-list">Users</a></li>
            <li class="lastchild <?php echo ($_GET['parm'] == 'locations' || $_GET['parm'] == 'new_location' || $_GET['parm'] == 'edit_location'
                     || $_GET['parm'] == 'location_sites' || $_GET['parm'] == 'keywords' || $_GET['parm'] == 'assign_users')?'active':''; ?>"><a href="?parm=locations">Locations</a></li>
            <li class="lastchild <?php echo ($_GET['parm'] == 'reports')?'active':''; ?>"><a href="?parm=reports">Reports</a></li>
            <li class="<?php echo ($_GET['parm'] == 'ga_connect')?'active':''; ?>"><a href="?parm=ga_connect">GA Connect</a></li>
            <li class="<?php echo ($_GET['parm'] == 'tracking-code')?'active':''; ?>"><a href="?parm=tracking-code">Tracking Code</a></li>
            <li class="<?php echo ($_GET['parm'] == 'conversion-urls')?'active':''; ?>"><a href="?parm=conversion-urls">Global Conversion URLs</a></li>            
            <li class="<?php echo ($_GET['parm'] == 'competitor_url')?'active':''; ?>"><a href="?parm=competitor_url">Competitor Url</a></li>               
        </ul>
        
    </div>
</div>
