<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
<?php
session_start();
wp_enqueue_style('billing_form_style.css', SET_COUNT_PLUGIN_URL .'/assets/css/billing_form_style.css','', SET_VERSION); 
include_once 'common.php';
global $wpdb;
global $billing_enable;

include_once ABSPATH . "wp-content/plugins/settings/agency_packages.php";
//pr($agency_package_price);
?>

<?php
$billing_discount = $wpdb->get_row("SELECT * FROM `wp_billingdiscount` WHERE `bd_status` = 'active' ORDER BY `bd_id` DESC LIMIT 1");
//pr($billing_discount->bd_dcid);
$bdrowcount = $wpdb->num_rows;
if($bdrowcount > 0){
    $discount_dcname = $billing_discount->bd_dcname;
    $discount_dcid = $billing_discount->bd_dcid;
}else{
    $discount_dcname = '';
    $discount_dcid = '';
}
?>

<?php
$get_addons_for_location = get_addons_for_location();

foreach($get_addons_for_location as $get_addons_for_locations){
    $field_slug = $get_addons_for_locations->addons_slug;
    $field_cost = $get_addons_for_locations->addons_cost;
    $field_value = $get_addons_for_locations->addons_value;
    
    if($field_slug == 'location'){
        $location_price = $get_addons_for_locations->addons_cost;
        $location_limit = 1;
    }
}
?>

<?php if(isset($_SESSION["packagetype"]) && ($_SESSION["packagetype"] == 'discount_coupon')){$dis_selected = 'class="package_selected"';}else{$dis_selected = '';}?>

<div class="contaninerinner ifbillingenable">
    <div class="panel panel-primary">        
        <div class="panel-heading">Choose Your Package</div>
        <div class="panel-body">
            <div class="row">
                <div class="plan_sec">
                    <div class="plan_div">
                        
                        <?php
                        $i = 0;
                        foreach($agency_package_price as $agency_package){
                            if(isset($_SESSION["packagetype"]) && ($_SESSION["packagetype"] == 'agency_coupon')){
                                if($agency_package->dc_id == $_SESSION["packageid"]){
                                    $dis_selected = 'class="package_selected"';
                                }else{
                                    $dis_selected = '';
                                }
                            }else{
                                 $dis_selected = '';             
                            }
                            echo '<div class="col-sm-12 col-sm-5 col-md-5">';
                            echo '<form method="post" action"">';
                            echo '<input type="hidden" name="package_type" value="agency_coupon">';
                            echo '<input type="hidden" name="package_id" value="'.$agency_package->dc_id.'">';
                            echo '<input type="hidden" name="package_name" value="'.$agency_package->dc_name.'">';
                            ?>
                            <?php if($i == 0){ ?>
                                
                                    <div class="plan plan_1">
                                        <div class="plan_hd">
                                            <span class="title">Light <br> <div class="plan_border"></div> </span> <span class="price"><edge>$</edge><?php echo $agency_package->dc_cost;?></span> <span class="month">per month</span>
                                        </div>
                                        <div class="plan_mid">
                                            <ul>
                                                <li><?php echo $agency_package->dc_location;?> Locations (each additional $<?php echo $agency_package->dc_location_cost;?>pm)</li>
                                                <li>Unlimited User</li>
                                                <li><?php echo $agency_package->dc_keywords;?> Keywords</li>
                                                <li><?php echo $agency_package->dc_comp_key;?> Competitor Keywords</li>
                                                <li><?php echo $agency_package->dc_siteaudit;?> Site Audits Per Month</li>
                                                <li><?php echo $agency_package->dc_citation;?> Citation Reports Per Month</li>
                                            </ul>
                                        </div>

                                        <div class="plan_btn"><?php echo '<input type="submit" '.$dis_selected.' name="select_package" value="Choose Plan">';?></div>
                                    </div>
                                
                            <?php }else{ ?>
                                
                                    <div class="plan plan_2">
                                        <div class="plan_hd">
                                            <span class="title">Professional <br> <div class="plan_border"></div> </span> <span class="price"><edge>$</edge><?php echo $agency_package->dc_cost;?></span> <span class="month">per month</span>
                                        </div>
                                        <div class="plan_mid">
                                            <ul>
                                                <li><?php echo $agency_package->dc_location;?> Locations (each additional $<?php echo $agency_package->dc_location_cost;?>pm)</li>
                                                <li>Unlimited User</li>
                                                <li><?php echo $agency_package->dc_keywords;?> Keywords</li>
                                                <li><?php echo $agency_package->dc_comp_key;?> Competitor Keywords</li>
                                                <li><?php echo $agency_package->dc_siteaudit;?> Site Audits Per Month</li>
                                                <li><?php echo $agency_package->dc_citation;?> Citation Reports Per Month</li>
                                            </ul>
                                        </div>

                                        <div class="plan_btn"><?php echo '<input type="submit" '.$dis_selected.' name="select_package" value="Choose Plan">';?></div>
                                    </div>
                                
                            <?php } ?>
                            <?php
                            $i = $i + 1;
                            echo '</form>';
                            echo '</div>';
                        }
                        ?>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.package_selected {
  background: #E58B04 !important;
}
</style>

