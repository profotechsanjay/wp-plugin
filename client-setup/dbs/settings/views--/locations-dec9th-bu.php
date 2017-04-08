<?php

include_once 'common.php';
global $wpdb;
//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php

$billing_enable = '0';
$billing_enable = BILLING_ENABLE;

//pr($locations_package_prices);

$lp_price = $locations_package_prices->lp_price;
$lp_location_price = $locations_package_prices->lp_location_price;
$lp_locations = $locations_package_prices->lp_locations;
if(empty($lp_locations)){
    $lp_locations = 5;
}

$default_locations = $lp_locations;

$count_noof_locations_active = $wpdb->get_var( "SELECT COUNT(*) FROM `wp_client_location` WHERE `status` = '1'" );
if($count_noof_locations_active > $default_locations){
    $default_locations = $count_noof_locations_active;
    $extra_locations = $count_noof_locations_active - $lp_locations;
    if($extra_locations > 0){
        $next_pending_payment = $lp_price + ($extra_locations*$lp_location_price);
        $extra_locations = $extra_locations;
    }else{
        $next_pending_payment = $lp_price;
        $extra_locations = 0;
    }
    
}else{
    $default_locations = $lp_locations;
    $next_pending_payment = $lp_price;
    $extra_locations = 0;
}
//echo $default_locations;

$key_field_limit = $default_locations*$locations_package_prices->lp_key_range;

$ckey_field_limit = $default_locations*$locations_package_prices->lp_ckey_range;

$keyo_field_limit = $default_locations*$locations_package_prices->lp_keyo_range;

$pages_field_limit = $default_locations*$locations_package_prices->lp_page_range;

$audit_field_limit = $default_locations*$locations_package_prices->lp_audit_range;

$citation_field_limit = $default_locations*$locations_package_prices->lp_citation_range;

$check_lp_limit = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields`");
if($check_lp_limit == 0){
    $limit_insert_query = "INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES
                    ('keywords', '".$key_field_limit."', '0', '0', '0'),
                    ('comp_keywords', '".$ckey_field_limit."', '0', '0', '0'),
                    ('keyword_opp', '".$keyo_field_limit."', '0', '0', '0'),
                    ('pages', '".$pages_field_limit."', '0', '0', '0'),
                    ('site_audit', '".$audit_field_limit."', '0', '0', '0'),
                    ('citation_run', '".$citation_field_limit."', '0', '0', '0'),
                    ('location', '".$lp_locations."', '".$extra_locations."', '0', '".$count_noof_locations_active."')";
    
    $limit_insert = $wpdb->query($limit_insert_query);
}
/*
else{
    $check_locations_added = $wpdb->get_var("SELECT COUNT(*) FROM `wp_client_location` WHERE `status` = '1'");
    
    $key_field_limit = $check_locations_added*$locations_package_prices->lp_key_range;
    $ckey_field_limit = $check_locations_added*$locations_package_prices->lp_ckey_range;
    $keyo_field_limit = $check_locations_added*$locations_package_prices->lp_keyo_range;
    $pages_field_limit = $check_locations_added*$locations_package_prices->lp_page_range;
    $audit_field_limit = $check_locations_added*$locations_package_prices->lp_audit_range;
    $citation_field_limit = $check_locations_added*$locations_package_prices->lp_citation_range;
    
    $lp_already_added_locations = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
    foreach($lp_already_added_locations as $lp_already_added_location){
        //pr($lp_already_added_location);
        $field_name = $lp_already_added_location->lpf_field;
        if($field_name == "keywords"){
            
            $limit = $key_field_limit;
        } elseif($field_name == "comp_keywords"){

            $limit = $ckey_field_limit;
        } elseif($field_name == "keyword_opp"){

            $limit = $keyo_field_limit;
        } elseif($field_name == "pages"){

            $limit = $pages_field_limit;
        } elseif($field_name == "site_audit"){

            $limit = $audit_field_limit;
        } elseif($field_name == "citation_run"){

            $limit = $citation_field_limit;
        }
        
        $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$limit."' WHERE `lpf_field`='".$field_name."'");
    }
} */

//echo client_location();
$base_url = site_url();
$locations = $wpdb->get_results
    (
        $wpdb->prepare
        (
                "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC",""
        )
   );
$available_noof_locations = $wpdb->num_rows;
//$available_noof_locations = '5';

$number_of_locations = get_user_meta('1', 'number_of_locations', true);
$location_package = get_user_meta('1', 'location_package', true);
$location_initial_payment = get_user_meta('1', 'location_initial_payment', true);
$location_next_pending_payment = get_user_meta('1', 'location_next_pending_payment', true);
$per_location_price = get_user_meta('1', 'per_location_price', true);

if ($number_of_locations == '') {    
    add_user_meta( '1', 'number_of_locations', '0');
    $number_of_locations = get_user_meta('1', 'number_of_locations', true);
} else {
    $number_of_locations = get_user_meta('1', 'number_of_locations', true);
}

if($billing_enable == 0){
    update_user_meta('1', 'number_of_locations', $lp_locations);
}elseif($billing_enable == 1){    
    update_user_meta('1', 'number_of_locations', '0');
}
$number_of_locations = get_user_meta('1', 'number_of_locations', true);

if ($location_package == '') {    
    add_user_meta( '1', 'location_package', 'pending');
    $location_package = get_user_meta('1', 'location_package', true);
} else {
    $location_package = get_user_meta('1', 'location_package', true);
}

if ($location_initial_payment == '') {    
    add_user_meta( '1', 'location_initial_payment', $lp_price);
    $location_initial_payment = get_user_meta('1', 'location_initial_payment', true);
} else {
    $location_initial_payment = get_user_meta('1', 'location_initial_payment', true);
}

if ($location_next_pending_payment == '') {    
    add_user_meta( '1', 'location_next_pending_payment', $next_pending_payment);
    $location_next_pending_payment = get_user_meta('1', 'location_next_pending_payment', true);
} else {
    $location_next_pending_payment = get_user_meta('1', 'location_next_pending_payment', true);
}

if ($per_location_price == '') {    
    add_user_meta( '1', 'per_location_price', $lp_location_price);
    $per_location_price = get_user_meta('1', 'per_location_price', true);
} else {
    $per_location_price = get_user_meta('1', 'per_location_price', true);
}

$ar = array();
$arrwebsite = array();

//$number_of_locations = 0;
//$location_package = "pending";
?>

<div class="contaninerinner">         
    <h4>Locations</h4>
    <?php
    //echo $location_package;
    //echo $billing_enable = '1';
    //if(($number_of_locations == 0) && ($location_package == 'pending') && ($billing_enable == 1)){ 
    if(($location_package == 'pending') && ($billing_enable == 1)){ ?>
        <div class="panel panel-primary">        
            <div class="panel-heading">Location Info</div>
            <div class="panel-body">
                <?php
                require 'payment_for_locations.php';  //This section active if agency Not pay initial payment for Locations
                ?>
            </div>
        </div>
    <?php } elseif((($location_package == 'paid') && ($billing_enable == '1')) || ($billing_enable == '0')) { ?>
        <div class="pull-right">
            <?php /* if($available_noof_locations < $number_of_locations){ ?>
                <a href="<?php echo ST_LOC_PAGE; ?>?parm=functions&type=location_add" class="btn btn-success">Create New Location</a>        
                <a href="javascript:;" class="btn btn-warning add_exiting_account">Add Existing Location</a> 
            <?php } else { ?>
                <a href="<?php echo ST_LOC_PAGE; ?>?parm=add_paid_location" class="btn btn-success">Pay Now For Add More Locations</a>
            <?php } */ ?>
            <a href="<?php echo ST_LOC_PAGE; ?>?parm=functions&type=location_add" class="btn btn-success">Create New Location</a>
            <a href="javascript:;" class="btn btn-warning add_exiting_account">Add Existing Location</a> 
            <a href="javascript:;" class="btn btn-danger remove_exiting_account">Unassign Location From List</a>        
        </div>
        <div class="panel panel-primary">        
            <div class="panel-heading">Location Info</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover" id="data_location" >
                    <thead>
                        <tr>
                            <th style="width: 6%;">SNo</th>
                            <th style="width: 20%;">Website</th>
                            <th style="width: 18%;">Name</th>
                            <th style="width: 20%;">Date</th>
                            <th style="width: 26%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($locations as $location) {
                            $i = $i + 1;                                                
                            array_push($ar, $location->MCCUserId);                                                
                            $website = get_user_meta($location->MCCUserId,'website',TRUE);

                            $brand = get_user_meta($location->MCCUserId, 'BRAND_NAME',TRUE);
                            if(empty($brand)){
                                $brand = get_user_meta($location->MCCUserId, 'company_name',TRUE);                            
                            }                      
                            $arvals = array('id' => $location->id, 'site' => $brand);
                            array_push($arrwebsite, $arvals);
                            $name = get_user_meta($location->MCCUserId,'BRAND_NAME',TRUE);                        
                            $eurl = ST_LOC_PAGE."?parm=new_location&location_id=" . $location->id;
                            $url = ST_LOC_PAGE."?parm=location_sites&location_id=" . $location->id;
                            $k_url = ST_LOC_PAGE."?parm=keywords&location_id=" . $location->id;
                            $date = date("Y-m-d H:i:s",  strtotime($location->created_dt));
                            if($location->status == 0){
                                $website = $name = '<i>--Draft Created--</i>';                            
                            }

                            ?>
                                <tr class="rowmod" data-id="<?php echo $location->id; ?>">
                                    <td><?php echo $i; ?></td> 
                                    <td><?php echo $website; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $date; ?></td>                            
                                    <td class="actiontd acttd">       
                                        <?php if($location->status == 1){ ?>
                                        <div class="margin-bottom-10">                                        
                                            <a href="<?php echo $eurl; ?>" class="btn new_btn_class" title="Sites">Edit</a>
                                            <a href="<?php echo $url; ?>" class="btn new_btn_class" title="Sites">Sites</a>
                                            <a href="<?php echo $k_url; ?>" class="btn new_btn_class" title="Site Keywords">Site Keywords</a>
                                        </div>
                                        <div>
                                            <a href="<?php echo ST_LOC_PAGE; ?>?parm=assign_users&location_id=<?php echo $location->id; ?>" class="btn new_btn_class" title="Assign Users">Assign Users</a>                                        
                                            <a href="javascript:;" data-id="<?php echo $location->id; ?>" title="Delete Location" class="del_client_loc btn btn-danger">Delete</a>
                                        </div>
                                        <?php } else {
                                            ?>
                                            <a href="<?php echo $eurl; ?>" class="btn new_btn_class" title="Sites">Add Information</a>
                                            <a href="javascript:;" data-id="<?php echo $location->id; ?>" title="Delete Location" class="del_client_loc btn btn-danger">Delete</a>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>


            </div>
        </div>
    <?php } ?>

</div>

<?php

$usersacc = new stdClass();
$args = array(    
    'exclude' => $ar,    
    'fields' => 'all',
);
$usersacc = get_users( $args );


?>
<div id="removeaccount" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><strong>Unassign Location</strong></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" class="form-horizontal" method="post">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Select Location (Account)</label>
                                <div class="col-md-8">
                                    <select required class="form-control chosen" name="rmlocationid" id="rmlocationid">
                                        <option value="">Select Location (Account)</option>
                                        <?php
                                            foreach($arrwebsite as $website){
                                               
                                                ?>
                                                <option value="<?php echo $website['id']; ?>"><?php echo $website['site']; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <input type="button" class="btn btn-success" style="background:none" id="btn_remove_location" value="Unassign">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>        
</div>

<div id="existingaccount" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><strong>Add Existing Location</strong></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" class="form-horizontal" method="post">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Select Location (Account)</label>
                                <div class="col-md-8">
                                    <select required class="form-control chosen" name="locationname" id="locationname">
                                        <option value="">Select Location (Account)</option>
                                        <?php
                                            foreach($usersacc as $userac){
                                                $brand = get_user_meta($userac->ID, 'BRAND_NAME',TRUE);                                                
                                                if(empty($brand)){
                                                    $brand = get_user_meta($userac->ID, 'company_name',TRUE);                                                    
                                                }
                                                if(empty($brand)){
                                                    continue;
                                                }                                                
                                                ?>
                                                <option value="<?php echo $userac->ID; ?>"><?php echo $brand; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <input type="button" class="btn btn-success" style="background:none" id="btn_add_location" value="Add">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>        
</div>