<?php

include_once 'common.php';
global $wpdb;

$billing_enable = '0';
$billing_enable = BILLING_ENABLE;

$location_limits = $wpdb->get_row("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` = 'location'");
//pr($location_limits);
//$deleted_locations = $location_limits->lpf_addons_delete;
$deleted_locations = $location_limits->lpf_locations_delete;

$get_lpf_limit = $location_limits->lpf_limit;
$get_lpf_addons_add = $location_limits->lpf_addons_add;
$get_lpf_addons_delete = $location_limits->lpf_addons_delete;
$get_lpf_used = $location_limits->lpf_used;





$location_deleted = unserialize(urldecode($deleted_locations));
//pr($location_deleted);
$locations_string = rtrim(implode(',', $location_deleted), ',');


if($deleted_locations == 0){
    $location_deleted = array();
}

if(empty($deleted_locations)){
    $location_deleted = array();
}else{
    $location_deleted = unserialize(urldecode($deleted_locations));
    //pr($location_deleted);
}
//pr($location_deleted);
$count_deleted_locations = count($location_deleted);

/************************************************/
$limit_add_delete = $count_deleted_locations + $get_lpf_addons_add - $get_lpf_addons_delete;

$extra_locations = $get_lpf_used - $get_lpf_limit;

//if(($limit_add_delete > $extra_locations) && ($extra_locations > 0)){}

/************************************************/

$locations_string = rtrim(implode(',', $location_deleted), ',');
if(empty($locations_string)){
    $loc_query = "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC";
}else{
    $loc_query = "SELECT * FROM " . client_location() . " WHERE `MCCUserId` NOT IN (".$locations_string.") ORDER BY created_dt DESC";
}

$locations = $wpdb->get_results
    (
        $wpdb->prepare
        (
                $loc_query,""
        )
   );

//echo "SELECT * FROM `wp_client_location` WHERE `MCCUserId` NOT IN (".$locations_string.") ORDER BY created_dt DESC";
//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php
/*
echo "<pre>";
print_r($locations_package_prices);
echo "</pre>";
 * 
 */
$date = date('Y-m-d H:i:s');

//pr($locations_package_prices);

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
$location_field_limit = 1;
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
    //pr($_POST); die;
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
    } elseif($field_name == "location"){
        $amount = $location_price;
        $limit = $location_field_limit;
    }
    
    $additional_payment = $amount;
    $subsc_action = "add_ons_stop";
    require(SET_COUNT_PLUGIN_DIR.'/views/updateSubscription.php');  //Update Recurring Payment & $subsc_action use in this file
    
    $wpdb->query("UPDATE `wp_addons_purchase` SET `status` = 'inactive', `minus_amount` = '".$additional_payment."' WHERE `addons_id` = '".$addons_id."'");
    
    $addons_inactive = $wpdb->get_row("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` = '".$field_name."'");
    
    $lpf_addons_delete = $addons_inactive->lpf_addons_delete;
    $new_lpf_addons_delete = $lpf_addons_delete + 1;
    
    if($resultsnew == 'success'){
        if($field_name == "location"){
            if($limit_add_delete > $extra_locations){
                $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_addons_delete` = '".$new_lpf_addons_delete."' WHERE `lpf_field` = '".$field_name."'");
            }elseif($extra_locations > 0){
                $addons_id = $_POST['addons_id'];            
                $active_locations = $_POST['active_locations'];
                $curent_delete_location = array();
                $curent_delete_location[] = $active_locations;
                $deleted_location_serialize = serialize(array_merge($location_deleted,$curent_delete_location));
                $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_addons_delete` = '".$new_lpf_addons_delete."', `lpf_locations_delete` = '".$deleted_location_serialize."' WHERE `lpf_field` = '".$field_name."'");
            }
            
        }else{
            $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_addons_delete` = '".$new_lpf_addons_delete."' WHERE `lpf_field` = '".$field_name."'");
        }
    }
    
    
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
                                    if($addons_type != "location"){
                                        if($addons_type != "keyword_opp" && $addons_type != "site_audit" && $addons_type != "citation_run" && $addons_type != "pages"){ ?>
                                            <form method="post" action="" onsubmit="return confirm('Are you sure to Stop Add Ons?');">
                                                <input type="hidden" name="field_name" value="<?php echo $addons_type;?>">
                                                <input type="hidden" name="addons_id" value="<?php echo $addons_id;?>">
                                                <input type="submit" name="stop_addons" value="Stop Add Ons">
                                            </form>
                                        <?php } else {echo "Complete";}
                                    }else{ ?>
                                        <!--<form method="post" action="" onsubmit="return submitResult();">
                                            <input type="hidden" name="field_name" value="<?php echo $addons_type;?>">
                                            <input type="hidden" name="addons_id" value="<?php echo $addons_id;?>">
                                            <input type="submit" name="stop_addons" value="Stop Add Ons">
                                        </form>-->
                                        <?php
                                        if($limit_add_delete > $extra_locations){ ?>
                                            <form method="post" action="" onsubmit="return submitResult();">
                                                <input type="hidden" name="field_name" value="<?php echo $addons_type;?>">
                                                <input type="hidden" name="addons_id" value="<?php echo $addons_id;?>">
                                                <input type="submit" name="stop_addons" value="Stop Add Ons">
                                            </form>
                                        <?php }elseif($extra_locations > 0){ ?>
                                            <button type="button" class="" onclick='jQuery("#location_stop_Modal").modal();'>Stop Add Ons</button>
                                            <!-- Modal -->
                                            <div id='location_stop_Modal' class="modal fade" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                  <div class="modal-content">
                                                    <div class="modal-header">
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                      <h4 class="modal-title">Select Location which is delete after next payment Cycle with Stop Location Add-Ons.</h4>
                                                    </div>
                                                    <form method="post" action="" onsubmit="return submitResult();">
                                                        <div class="modal-body">
                                                                <input type="hidden" name="field_name" value="<?php echo $addons_type;?>">
                                                                <input type="hidden" name="addons_id" value="<?php echo $addons_id;?>">
                                                                <?php
                                                                //pr($locations);
                                                                foreach($locations as $locationss){
                                                                    $MCCUserId = $locationss->MCCUserId;
                                                                    $name = get_user_meta($locationss->MCCUserId,'BRAND_NAME',TRUE);
                                                                    ?>
                                                                    <input type="radio" required name="active_locations" value="<?php echo $MCCUserId;?>"> <?php echo $name;?><br>
                                                                    <?php
                                                                }
                                                                ?>

                                                        </div>

                                                        <div class="modal-footer">
                                                          <h5 style="float: left;padding: 0 5px;text-align: left;width: 100%;">Note :- Selected Location delete Automatically after next Payment cycle.</h5>
                                                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                          <input type="submit" class="btn btn-primary" name="stop_addons" value="Stop Add Ons">
                                                        </div>
                                                    </form>
                                                  </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                                        <?php }    
                                    }
                                    
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

<script>
function submitResult() {
    var check_confirm = confirm("Are you sure to Stop Add-Ons?");
    //alert(check_confirm);
    if(check_confirm){
        return true;
    }else{
        return false;
    }   
}
</script>

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