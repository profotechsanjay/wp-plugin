<?php

include_once 'common.php';
global $wpdb;
global $billing_enable;

//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php
/*
echo "<pre>";
print_r($locations_package_prices);
echo "</pre>";
*/

$get_addons_for_location = get_addons_for_location();


foreach($get_addons_for_location as $get_addons_for_locations){
    $field_slug = $get_addons_for_locations->addons_slug;
    $field_cost = $get_addons_for_locations->addons_cost;
    $field_value = $get_addons_for_locations->addons_value;
    
    if($field_slug == 'keywords'){
        $key_field_price = $get_addons_for_locations->addons_cost;
        $key_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'comp_keywords'){
        $ckey_field_price = $get_addons_for_locations->addons_cost;
        $ckey_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'keyword_opp'){
        $keyo_field_price = $get_addons_for_locations->addons_cost;
        $keyo_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'pages'){
        $pages_field_price = $get_addons_for_locations->addons_cost;
        $pages_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'site_audit'){
        $audit_field_price = $get_addons_for_locations->addons_cost;
        $audit_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'citation_run'){
        $citation_field_price = $get_addons_for_locations->addons_cost;
        $citation_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'demo_accounts'){
        $demoaccounts_field_price = $get_addons_for_locations->addons_cost;
        $demoaccounts_field_limit = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'location'){
        //$location_price = $get_addons_for_locations->addons_cost;
        $location_price = used_discountcode_inagency();  // Additional Location Price get from packages
        $location_limit = 1;
        $location_fields = unserialize($get_addons_for_locations->addons_value);
        //pr($location_fields);
        $location_keywords = $location_fields['location_keywords'];
        $location_ckey = $location_fields['location_ckey'];
        $location_keyopp = $location_fields['location_keyopp'];
        $location_pages = $location_fields['location_pages'];
        $location_audit = $location_fields['location_audit'];
        $location_citation = $location_fields['location_citation'];
        $location_demoaccounts = $location_fields['location_demoaccounts'];
    }
}

$date = date('Y-m-d H:i:s');

?>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase_addons'])){
    $field_name = $_POST['field_name'];
    
    if($field_name == "keywords"){
        $amount = $key_field_price;
        $limit = $key_field_limit;
    }elseif($field_name == "comp_keywords"){
        $amount = $ckey_field_price;
        $limit = $ckey_field_limit;
    }elseif($field_name == "keyword_opp"){
        $amount = $keyo_field_price;
        $limit = $keyo_field_limit;
    }elseif($field_name == "pages"){
        $amount = $pages_field_price;
        $limit = $pages_field_limit;
    }elseif($field_name == "site_audit"){
        $amount = $audit_field_price;
        $limit = $audit_field_limit;
    }elseif($field_name == "citation_run"){
        $amount = $citation_field_price;
        $limit = $citation_field_limit;
    }elseif($field_name == "demo_accounts"){
        $amount = $demoaccounts_field_price;
        $limit = $demoaccounts_field_limit;
    }elseif($field_name == "location"){
        $amount = $location_price;
        $limit = $location_limit;
    }
    
    $per_day_charge = $amount/30;
    
    $get_field = $wpdb->get_row("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` = '".$field_name."'");
    //print_r($get_field);
    $lpf_id = $get_field->lpf_id;
    $lpf_addons_add = $get_field->lpf_addons_add;
       // add code by rudra 14-03-2017
	if($lpf_addons_add<0){
	  $lpf_addons_add=0;
	}
   // end code by rudra 14-03-2017
    $purchase_lpf_addons_add = $lpf_addons_add + 1;
    
    $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_addons_add` = '".$purchase_lpf_addons_add."' WHERE `lpf_id` = '".$lpf_id."' AND `lpf_field` = '".$field_name."'");
    
    $previous_payment_query = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` WHERE `status` = 'paid' ORDER BY `payment_id` DESC LIMIT 1");

    /**************** Check Last subscription paymet detail START *****************/
    
    $previous_payment_date = $previous_payment_query->startDate;
    $previous_payment_date = date("Y-m-d", strtotime($previous_payment_date));
    
    $previous_payment_SubscriptionId = $previous_payment_query->SubscriptionId;
    
    $total_hours = intval(abs(strtotime($date) - strtotime($previous_payment_date))/(60*60));  // Count hours from last payment cycle
    
    $next_payment_date = date('Y-m-d', strtotime('+1 month', strtotime($previous_payment_date)));

    $daylen = 60*60*24;
    
    $current_date = date("Y-m-d");
    
    $paid_for_days = intval((strtotime($next_payment_date)-strtotime($current_date))/$daylen);
    
    if($field_name == 'keywords' || $field_name == 'comp_keywords' || $field_name == 'location'){
        if($paid_for_days == '31' || $paid_for_days == '30'){
            $additional_payment = $amount + $amount;           // Amount for current month and next month add in subscription
        } else {
            $additional_payment = ($paid_for_days*$per_day_charge) + $amount;   // Amount for current month and next month add in subscription
        }
    } else {
        $additional_payment = $amount;       // Single amount add because if addons not use in this month then use any time which is already purchased
    }
    
    
    $subsc_action = "add_ons_add";
    require(SET_COUNT_PLUGIN_DIR.'/views/updateSubscription.php');  //Update Recurring Payment & $subsc_action use in this file
    
    /**************** Check Last subscription paymet detail END *****************/
    
    $wpdb->query("INSERT INTO `wp_addons_purchase`(`addons_type`, `addons_date`, `addons_amount`, `addons_status`, `status`, `minus_amount`) VALUES ('".$field_name."', '".$date."', '".$additional_payment."', 'paid', 'active', '0')");
    
    if($resultsnew == 'success'){
        echo '<div class="keyword_alert add_ons">Thanks, <strong>Add-Ons</strong> is Successfully add.</div>';
        $purchased_addons_url = site_url()."/location-settings/?parm=purchased_addons";
        //header( "refresh:3;url=".$purchased_addons_url );
    }
    
}
?>

<?php

$check_lp_all_limits = check_lp_all_limits();
//pr($check_lp_all_limits);

$package_keyword_limit = $check_lp_all_limits['keywords_package_limit'];
$total_keyword_limit = $check_lp_all_limits['keywords_total_limit'];
$used_keyword_limit = $check_lp_all_limits['keywords_used'];
$available_keyword_limit = $check_lp_all_limits['keywords_available'];

$package_comp_keywords_limit = $check_lp_all_limits['comp_keywords_package_limit'];
$total_comp_keywords_limit = $check_lp_all_limits['comp_keywords_total_limit'];
$used_comp_keywords_limit = $check_lp_all_limits['comp_keywords_used'];
$available_comp_keywords_limit = $check_lp_all_limits['comp_keywords_available'];

$package_keyword_opp_limit = $check_lp_all_limits['keyword_opp_package_limit'];
$total_keyword_opp_limit = $check_lp_all_limits['keyword_opp_total_limit'];
$used_keyword_opp_limit = $check_lp_all_limits['keyword_opp_used'];
$available_keyword_opp_limit = $check_lp_all_limits['keyword_opp_available'];

$package_pages_limit = $check_lp_all_limits['pages_package_limit'];
$total_pages_limit = $check_lp_all_limits['pages_total_limit'];
$used_pages_limit = $check_lp_all_limits['pages_used'];
$available_pages_limit = $check_lp_all_limits['pages_available'];

$package_site_audit_limit = $check_lp_all_limits['site_audit_package_limit'];
$total_site_audit_limit = $check_lp_all_limits['site_audit_total_limit'];
$used_site_audit_limit = $check_lp_all_limits['site_audit_used'];
$available_site_audit_limit = $check_lp_all_limits['site_audit_available'];

$package_citation_run_limit = $check_lp_all_limits['citation_run_package_limit'];
$total_citation_run_limit = $check_lp_all_limits['citation_run_total_limit'];
$used_citation_run_limit = $check_lp_all_limits['citation_run_used'];
$available_citation_run_limit = $check_lp_all_limits['citation_run_available'];

$package_demoaccounts_limit = $check_lp_all_limits['demoaccounts_package_limit'];
$total_demoaccounts_limit = $check_lp_all_limits['demoaccounts_total_limit'];
$used_demoaccounts_limit = $check_lp_all_limits['demoaccounts_used'];
$available_demoaccounts_limit = $check_lp_all_limits['demoaccounts_available'];

$package_location = $check_lp_all_limits['location_package_limit'];
$total_location = $check_lp_all_limits['location_total_limit'];
$used_location = $check_lp_all_limits['location_used'];
$available_location = $check_lp_all_limits['location_available'];

?>
<!--
<div class="billing_tabs">
    <a href="<?php echo site_url();?>/location-settings/?parm=billing_info" class="location_list_button">Billing Report</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=payment_history" class="location_list_button">Billing History</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=add_ons" class="location_list_button active">Add-Ons</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=purchased_addons" class="location_list_button">Add-Ons Report</a>
</div>
-->

<?php
$billing_check = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` DESC LIMIT 1");
$billing_querystatus = $billing_check->status;
$billing_querySubscriptionId = $billing_check->SubscriptionId;
if(($billing_enable == 1) && !empty($billing_querySubscriptionId) && ($billing_querystatus == 'paid')){    
    $addons_button = 'enable';   
}else{
    $addons_button = 'desable';
}
?>

<div class="contaninerinner ifbillingenable">         
    <h4>Add Ons</h4>
    <div class="panel panel-primary">        
        <div class="panel-heading">Add Ons</div>
        <div class="panel-body">
            <h5 style="color: #FF0000;float: left; margin-botton: 15px;"><strong>NOTE :</strong> <i>Total Limit = Package Limit + Addons Limit</i></h5>
            <div class="all_fields_addons">
                <div class="field_row col head coloured">
                    <div class="field_td lpfield"><b>Limit Type</b></div>
                    <div class="field_limit"><b>Package Limit</b></div>
                    <div class="field_limit"><b>Total Limit</b></div>
                    <div class="field_limit_used"><b>Used</b></div>
                    <div class="field_limit_available"><b>Available</b></div>
                    <div class="field_td lpbutton"><b></b></div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Keywords</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $key_field_price;?> per month for <?php echo $key_field_limit;?> additional Keywords)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_keyword_limit;?></div>
                    <div class="field_limit"><?php echo $total_keyword_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_keyword_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_keyword_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="keywords">
                                <input type="submit" name="purchase_addons" value="<?php //echo '$'.$key_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                <div class="field_row col coloured">
                    <div class="field_td lpfield"><h5>Comp Keywords</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $ckey_field_price;?> per month for <?php echo $ckey_field_limit;?> additional Comp Keywords)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_comp_keywords_limit;?></div>
                    <div class="field_limit"><?php echo $total_comp_keywords_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_comp_keywords_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_comp_keywords_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="comp_keywords">
                                <input type="submit" name="purchase_addons" value="<?php //echo $ckey_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Keyword Opportunities</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $keyo_field_price;?> per month for <?php echo $keyo_field_limit;?> additional Keyword Opp)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_keyword_opp_limit;?></div>
                    <div class="field_limit"><?php echo $total_keyword_opp_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_keyword_opp_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_keyword_opp_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="keyword_opp">
                                <input type="submit" name="purchase_addons" value="<?php //echo $keyo_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                <div class="field_row col coloured">
                    <div class="field_td lpfield"><h5>Pages</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $pages_field_price;?> per month for <?php echo $pages_field_limit;?> additional Pages)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_pages_limit;?></div>
                    <div class="field_limit"><?php echo $total_pages_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_pages_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_pages_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="pages">
                                <input type="submit" name="purchase_addons" value="<?php //echo $pages_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Site Audit Runs</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $audit_field_price;?> per month for <?php echo $audit_field_limit;?> additional Site Audit Runs)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_site_audit_limit;?></div>
                    <div class="field_limit"><?php echo $total_site_audit_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_site_audit_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_site_audit_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="site_audit">
                                <input type="submit" name="purchase_addons" value="<?php //echo $audit_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                <div class="field_row col coloured">
                    <div class="field_td lpfield">
                        <h5>Citation Runs</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $citation_field_price;?> per month for <?php echo $citation_field_limit;?> additional Citation Runs)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_citation_run_limit;?></div>
                    <div class="field_limit"><?php echo $total_citation_run_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_citation_run_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_citation_run_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="citation_run">
                                <input type="submit" name="purchase_addons" value="<?php //echo $citation_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                <!--
                
                <div class="field_row col coloured">
                    <div class="field_td lpfield">
                        <h5>Demo Accounts</h5>
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $demoaccounts_field_price;?> per month for <?php echo $demoaccounts_field_limit;?> additional Demo Account)</h6>
                        <?php } ?>
                        
                    </div>
                    <div class="field_limit"><?php echo $package_demoaccounts_limit;?></div>
                    <div class="field_limit"><?php echo $total_demoaccounts_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_demoaccounts_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_demoaccounts_limit;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                                <input type="hidden" name="field_name" value="demo_accounts">
                                <input type="submit" name="purchase_addons" value="<?php //echo $citation_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
                
                -->
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Location</h5>
                        
                        <?php if($addons_button == 'enable'){ ?>
                        <h6>($<?php echo $location_price;?> per month for <?php echo $location_limit;?> additional Location)</h6>
                        
                        <?php } ?>
                    </div>
                    <div class="field_limit"><?php echo $package_location;?></div>
                    <div class="field_limit"><?php echo $total_location;?></div>
                    <div class="field_limit_used"><?php echo $used_location;?></div>
                    <div class="field_limit_available"><?php echo $available_location;?></div>
                    <div class="field_td lpbutton">
                        <?php if($addons_button == 'enable'){ ?>
                            <form method="post" action="" onsubmit="return confirm('Are you sure to Buy Location Add-Ons?');">
                                <input type="hidden" name="field_name" value="location">
                                <input type="submit" name="purchase_addons" value="<?php //echo '$'.$key_field_price;?> Add">
                            </form>
                        <?php }elseif($addons_button == 'desable'){ ?>
                            <div class="disable_section">
                                <input type="button" disabled class="purchase_addons_dummy" value="Add">
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php
$location_package = get_user_meta( $user_id = 1, 'location_package', true ); 

//$billing_enable = 1;
//$location_package = 'paid';

 ?>

<style>
.all_fields_addons .field_row.col .field_td.lpbutton .purchase_addons_dummy {
  background: none repeat scroll 0 0 #337ab7;
  border: medium none;
  border-radius: 3px !important;
  color: #fff;
  font-weight: 800;
  padding: 7px 0;
  width: 108px;
}
.disable_section {
  background-color: #fff;
  opacity: 0.4;
  text-align: center;
}
.lpfield h6 {
  font-size: 10px !important;
  font-weight: 700;
}
</style>
