<?php

include_once 'common.php';
global $wpdb;

$billing_enable = '0';
$billing_enable = BILLING_ENABLE;



//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php
/*
echo "<pre>";
print_r($locations_package_prices);
echo "</pre>";
*/
$date = date('Y-m-d H:i:s');

$key_field_price = $locations_package_prices->lp_key_price;
$key_field_limit = $locations_package_prices->lp_key_range;

$ckey_field_price = $locations_package_prices->lp_ckey_price;
$ckey_field_limit = $locations_package_prices->lp_ckey_range;

$keyo_field_price = $locations_package_prices->lp_keyo_price;
$keyo_field_limit = $locations_package_prices->lp_keyo_range;

$pages_field_price = $locations_package_prices->lp_page_price;
$pages_field_limit = $locations_package_prices->lp_page_range;

$audit_field_price = $locations_package_prices->lp_audit_price;
$audit_field_limit = $locations_package_prices->lp_audit_range;

$citation_field_price = $locations_package_prices->lp_citation_price;
$citation_field_limit = $locations_package_prices->lp_citation_range;

$location_price = $locations_package_prices->lp_location_price;
$location_limit = 1;


?>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase_addons'])){
    $field_name = $_POST['field_name'];
    
    if($field_name == "keywords"){
        $amount = $key_field_price;
        $limit = $key_field_limit;
    } elseif($field_name == "comp_keywords"){
        $amount = $ckey_field_price;
        $limit = $ckey_field_limit;
    } elseif($field_name == "keyword_opp"){
        $amount = $keyo_field_price;
        $limit = $keyo_field_limit;
    } elseif($field_name == "pages"){
        $amount = $pages_field_price;
        $limit = $pages_field_limit;
    } elseif($field_name == "site_audit"){
        $amount = $audit_field_price;
        $limit = $audit_field_limit;
    } elseif($field_name == "citation_run"){
        $amount = $citation_field_price;
        $limit = $citation_field_limit;
    } elseif($field_name == "location"){
        $amount = $location_price;
        $limit = $location_limit;
    }
    
    $per_day_charge = $amount/30;
    
    $get_field = $wpdb->get_row("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` = '".$field_name."'");
    //print_r($get_field);
    $lpf_id = $get_field->lpf_id;
    $lpf_addons_add = $get_field->lpf_addons_add;
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
    
        if($field_name == 'location') {
            $get_package_fields = $wpdb->get_results("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` != 'location'");
            foreach($get_package_fields as $get_package_field){
                $new_limit = $limit_increase = 0;
                $field_id = $get_package_field->lpf_id;
                $field_name = $get_package_field->lpf_field;
                $old_limit = $get_package_field->lpf_limit;

                if($field_name == 'keywords'){
                    $limit_increase = $locations_package_prices->lp_key_range;
                }elseif($field_name == 'comp_keywords'){
                    $limit_increase = $locations_package_prices->lp_ckey_range;
                }elseif($field_name == 'keyword_opp'){
                    $limit_increase = $locations_package_prices->lp_keyo_range;
                }elseif($field_name == 'pages'){
                    $limit_increase = $locations_package_prices->lp_page_range;
                }elseif($field_name == 'site_audit'){
                    $limit_increase = $locations_package_prices->lp_audit_range;
                }elseif($field_name == 'citation_run'){
                    $limit_increase = $locations_package_prices->lp_citation_range;
                }

                $new_limit = $old_limit + $limit_increase;

                $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$new_limit."' WHERE `lpf_id` = '".$field_id."'");
            }
        }
    
        echo '<div class="keyword_alert add_ons">Thanks, <strong>Add-Ons</strong> is Successfully add.</div>';
        $purchased_addons_url = site_url()."/location-settings/?parm=purchased_addons";
        //header( "refresh:3;url=".$purchased_addons_url );
    }
    
}
?>
<?php
/*
$location_package_limit = location_package_limit();

$get_all_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `status` = '1'");  // Neglect only current location keywords
$all_users_kw = $all_users_comp_kw = 0;
foreach($get_all_locations as $get_all_location){
    //pr($get_all_location);
    $user_id = $get_all_location->MCCUserId;
    
    $active_keywords = rd_all_active_keywords($user_id);
    //pr($active_keywords);
    $count_active_kw = count($active_keywords);
    $all_users_kw = $all_users_kw + $count_active_kw;
    
    $comp_keywords = billing_info($user_id);
    $count_comp_kw = $comp_keywords['count_comp_keywords'];
    $all_users_comp_kw = $all_users_comp_kw + $count_comp_kw;
    
}

$total_keyword_limit = $location_package_limit['keywords_limit'] + ($location_package_limit['keywords_addons']*$key_field_limit);
$used_keyword_limit = $all_users_kw;
$available_keyword_limit = $total_keyword_limit - $used_keyword_limit;

$total_comp_keywords_limit = $location_package_limit['comp_keywords_limit'] + ($location_package_limit['comp_keywords_addons']*$ckey_field_limit);
$used_comp_keywords_limit = $all_users_comp_kw;
$available_comp_keywords_limit = $total_comp_keywords_limit - $used_comp_keywords_limit;

$total_keyword_opp_limit = $location_package_limit['keyword_opp_limit'] + ($location_package_limit['keyword_opp_addons']*$keyo_field_limit);
$used_keyword_opp_limit = $location_package_limit['keyword_opp_used'];
$available_keyword_opp_limit = $total_keyword_opp_limit - $used_keyword_opp_limit;

$total_pages_limit = $location_package_limit['pages_limit'] + ($location_package_limit['pages_addons']*$pages_field_limit);
$used_pages_limit = $location_package_limit['pages_used'];
$available_pages_limit = $total_pages_limit - $used_pages_limit;

$total_site_audit_limit = $location_package_limit['site_audit_limit'] + ($location_package_limit['site_audit_addons']*$audit_field_limit);
$used_site_audit_limit = $location_package_limit['site_audit_used'];
$available_site_audit_limit = $total_site_audit_limit - $used_site_audit_limit;

$total_citation_run_limit = $location_package_limit['citation_run_limit'] + ($location_package_limit['citation_run_addons']*$citation_field_limit);
$used_citation_run_limit = $location_package_limit['citation_run_used'];
$available_citation_run_limit = $total_citation_run_limit - $used_citation_run_limit;
*/
?>
<?php

$check_lp_all_limits = check_lp_all_limits();

$total_keyword_limit = $check_lp_all_limits['keywords_total_limit'];
$used_keyword_limit = $check_lp_all_limits['keywords_used'];
$available_keyword_limit = $check_lp_all_limits['keywords_available'];

$total_comp_keywords_limit = $check_lp_all_limits['comp_keywords_total_limit'];
$used_comp_keywords_limit = $check_lp_all_limits['comp_keywords_used'];
$available_comp_keywords_limit = $check_lp_all_limits['comp_keywords_available'];

$total_keyword_opp_limit = $check_lp_all_limits['keyword_opp_total_limit'];
$used_keyword_opp_limit = $check_lp_all_limits['keyword_opp_used'];
$available_keyword_opp_limit = $check_lp_all_limits['keyword_opp_available'];

$total_pages_limit = $check_lp_all_limits['pages_total_limit'];
$used_pages_limit = $check_lp_all_limits['pages_used'];
$available_pages_limit = $check_lp_all_limits['pages_available'];

$total_site_audit_limit = $check_lp_all_limits['site_audit_total_limit'];
$used_site_audit_limit = $check_lp_all_limits['site_audit_used'];
$available_site_audit_limit = $check_lp_all_limits['site_audit_available'];

$total_citation_run_limit = $check_lp_all_limits['citation_run_total_limit'];
$used_citation_run_limit = $check_lp_all_limits['citation_run_used'];
$available_citation_run_limit = $check_lp_all_limits['citation_run_available'];

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
<div class="contaninerinner ifbillingenable">         
    <h4>Add Ons</h4>
    <div class="panel panel-primary">        
        <div class="panel-heading">Add Ons</div>
        <div class="panel-body">
            <div class="all_fields_addons">
                <div class="field_row col head coloured">
                    <div class="field_td lpfield"><b>Limit Type</b></div>
                    <div class="field_limit"><b>Total</b></div>
                    <div class="field_limit_used"><b>Used</b></div>
                    <div class="field_limit_available"><b>Available</b></div>
                    <div class="field_td lpbutton"><b></b></div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Keywords</h5>
                        <h6>($<?php echo $key_field_price;?> per <?php echo $key_field_limit;?> Keywords)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_keyword_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_keyword_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_keyword_limit;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                            <input type="hidden" name="field_name" value="keywords">
                            <input type="submit" name="purchase_addons" value="<?php //echo '$'.$key_field_price;?> Add">
                        </form>
                    </div>
                </div>
                <div class="field_row col coloured">
                    <div class="field_td lpfield"><h5>Comp Keywords</h5>
                        <h6>($<?php echo $ckey_field_price;?> per <?php echo $ckey_field_limit;?> Comp Keywords)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_comp_keywords_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_comp_keywords_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_comp_keywords_limit;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                            <input type="hidden" name="field_name" value="comp_keywords">
                            <input type="submit" name="purchase_addons" value="<?php //echo $ckey_field_price;?> Add">
                        </form>
                    </div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Keyword Opportunities</h5>
                        <h6>($<?php echo $keyo_field_price;?> per <?php echo $keyo_field_limit;?> Keyword Opp)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_keyword_opp_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_keyword_opp_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_keyword_opp_limit;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                            <input type="hidden" name="field_name" value="keyword_opp">
                            <input type="submit" name="purchase_addons" value="<?php //echo $keyo_field_price;?> Add">
                        </form>
                    </div>
                </div>
                <div class="field_row col coloured">
                    <div class="field_td lpfield"><h5>Pages</h5>
                        <h6>($<?php echo $pages_field_price;?> per <?php echo $pages_field_limit;?> Pages)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_pages_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_pages_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_pages_limit;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                            <input type="hidden" name="field_name" value="pages">
                            <input type="submit" name="purchase_addons" value="<?php //echo $pages_field_price;?> Add">
                        </form>
                    </div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Site Audit Runs</h5>
                        <h6>($<?php echo $audit_field_price;?> per <?php echo $audit_field_limit;?> Site Audit Runs)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_site_audit_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_site_audit_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_site_audit_limit;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                            <input type="hidden" name="field_name" value="site_audit">
                            <input type="submit" name="purchase_addons" value="<?php //echo $audit_field_price;?> Add">
                        </form>
                    </div>
                </div>
                <div class="field_row col coloured">
                    <div class="field_td lpfield">
                        <h5>Citation Runs</h5>
                        <h6>($<?php echo $citation_field_price;?> per <?php echo $citation_field_limit;?> Citation Runs)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_citation_run_limit;?></div>
                    <div class="field_limit_used"><?php echo $used_citation_run_limit;?></div>
                    <div class="field_limit_available"><?php echo $available_citation_run_limit;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to pay?');">
                            <input type="hidden" name="field_name" value="citation_run">
                            <input type="submit" name="purchase_addons" value="<?php //echo $citation_field_price;?> Add">
                        </form>
                    </div>
                </div>
                <div class="field_row col">
                    <div class="field_td lpfield"><h5>Location</h5>
                        <h6>($<?php echo $location_price;?> per <?php echo $location_limit;?> Location)</h6>
                    </div>
                    <div class="field_limit"><?php echo $total_location;?></div>
                    <div class="field_limit_used"><?php echo $used_location;?></div>
                    <div class="field_limit_available"><?php echo $available_location;?></div>
                    <div class="field_td lpbutton">
                        <form method="post" action="" onsubmit="return confirm('Are you sure to Buy Location Add-Ons?');">
                            <input type="hidden" name="field_name" value="location">
                            <input type="submit" name="purchase_addons" value="<?php //echo '$'.$key_field_price;?> Add">
                        </form>
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

if($billing_enable == '0' || $location_package == 'pending'){ ?>
    <style>
    .contaninerinner.ifbillingenable {
      cursor: default;
      opacity: 0.25;
      pointer-events: none;
    }
    </style>
<?php } ?>