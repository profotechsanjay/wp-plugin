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
 * 
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
?>
<?php

?>
<!--
<div class="billing_tabs">
    <a href="<?php echo site_url();?>/location-settings/?parm=billing_info" class="location_list_button">Billing Report</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=payment_history" class="location_list_button">Billing History</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=add_ons" class="location_list_button">Add-Ons</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=purchased_addons" class="location_list_button active">Add-Ons Report</a>
</div>
-->
<?php


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stop_addons'])){
    
    $field_name = $_POST['field_name'];
    $addons_id = $_POST['addons_id'];
    
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
    }
    
    $additional_payment = $amount;
    $subsc_action = "add_ons_stop";
    require(SET_COUNT_PLUGIN_DIR.'/views/updateSubscription.php');  //Update Recurring Payment & $subsc_action use in this file
    
    $wpdb->query("UPDATE `wp_addons_purchase` SET `status` = 'inactive', `minus_amount` = '".$additional_payment."' WHERE `addons_id` = '".$addons_id."'");
    
    $addons_inactive = $wpdb->get_row("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` = '".$field_name."'");
    
    $lpf_addons_delete = $addons_inactive->lpf_addons_delete;
    $new_lpf_addons_delete = $lpf_addons_delete + 1;
    
    
    $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_addons_delete` = '".$new_lpf_addons_delete."' WHERE `lpf_field` = '".$field_name."'");
    if($resultsnew == 'success'){
        echo '<div class="keyword_alert add_ons">Thanks, <strong>Add-Ons</strong> is Successfully Stop for next month. And next month payment of this Add-Ons is minus from Subscription.</div>';
    }
}

$user_last = get_user_meta( $user_id = '1', 'location_next_pending_payment', true );
if(empty($user_last)){
    $user_last = '0';
}
?>
<div class="contaninerinner ifbillingenable">         
    <h4>Add-Ons Report</h4>
    <div class="panel panel-primary">        
        <div class="panel-heading">Add-Ons Report</div>
        <?php
        $paid_addons = $wpdb->get_results("SELECT * FROM `wp_addons_purchase` ORDER BY `addons_id` DESC");
        ?>
        <div class="panel-body">
            <h4>Amount Deduct in Next Payment Cycle : <strong>$<?php echo $user_last;?><strong></h4>
            <table class="table table-bordered table-striped table-hover" id="purchased_addons_datatable" >
                <thead>
                    <tr>
                        <th style="width: 4%;">S.No.</th>
                        <th style="width: 20%;">Add Ons</th>
                        <th style="width: 20%;">Purchase Date</th>
                        <th style="width: 14%;">Amount</th>
                        <th style="width: 14%;">Payment Status</th>
                        <th style="width: 14%;">Action</th>
                        <th style="width: 14%;">Less From Subscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach($paid_addons as $paid_addon){
                        
                        $i = $i + 1;
                        $addons_id = $paid_addon->addons_id;
                        $addons_type = $paid_addon->addons_type;
                        $addons_date = $paid_addon->addons_date;
                        $addons_amount = $paid_addon->addons_amount;
                        $addons_status = $paid_addon->addons_status;
                        $status = $paid_addon->status;
                        $minus_amount = $paid_addon->minus_amount;
                        ?>
                        <tr class="rowmod" data-id="<?php echo $addons_id; ?>">
                            <td><?php echo $i; ?></td>
                            <td><?php echo $addons_type;?></td>
                            <td><?php echo $addons_date;?></td>
                            <td>$<?php echo round($addons_amount,2);?></td>
                            <td><?php echo $addons_status;?></td>
                            <td>
                                <?php if($status == "active"){ 
                                    if($addons_type != "keyword_opp" && $addons_type != "site_audit" && $addons_type != "citation_run" && $addons_type != "pages"){ ?>
                                        <form method="post" action="" onsubmit="return confirm('Are you sure to Stop Add Ons?');">
                                            <input type="hidden" name="field_name" value="<?php echo $addons_type;?>">
                                            <input type="hidden" name="addons_id" value="<?php echo $addons_id;?>">
                                            <input type="submit" name="stop_addons" value="Stop Add Ons">
                                        </form>
                                    <?php } else {echo "Complete";}
                                }else{
                                    echo $status;
                                } ?>
                            </td>
                            <td>$<?php echo $minus_amount;?></td>
                        </tr>
                    <?php
                    }
                    ?>  
                </tbody>
                
            </table>


        </div>
        
    </div>
</div>

<?php
$location_package = get_user_meta( $user_id = 1, 'location_package', true ); 

$billing_enable = 1;
$location_package = 'paid';

if($billing_enable == '0' || $location_package == 'pending'){ ?>
    <style>
    .contaninerinner.ifbillingenable {
      cursor: default;
      opacity: 0.25;
      pointer-events: none;
    }
    </style>
<?php } ?>