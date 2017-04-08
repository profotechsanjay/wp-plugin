<?php

include_once 'common.php';

//include_once '../custom_functions.php';
global $wpdb;

$base_url = site_url();

?>
<div class="billing_tabs">
    <a href="<?php echo site_url();?>/location-settings/?parm=billing_info" class="location_list_button active">Billing Report</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=payment_history" class="location_list_button">Billing History</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=add_ons" class="location_list_button">Add-Ons</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=purchased_addons" class="location_list_button">Add-Ons Report</a>
</div>
<div class="contaninerinner">     
    <h4>Billing Info</h4>
    
    <div class="panel panel-primary">
      
        <div class="panel-body new">
            
            <div class="locations_list billing_data">
                <div class="locations_row head">
                    <div class="locations_td brand"><strong>Client</strong></div>
                    <div class="locations_td"><strong>Keywords</strong></div>
                    <div class="locations_td"><strong>Comp Keywords</strong></div>
                    <div class="locations_td"><strong>Keyword Opp</strong></div>
                    <div class="locations_td"><strong>Pages</strong></div>
                    <div class="locations_td"><strong>Site Audit Runs</strong></div>
                    <div class="locations_td"><strong>Citation Runs</strong></div>
                </div>
                <?php
                
                $get_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `status` = '1'");
                $noof_locations_count = $wpdb->num_rows;
                
                $total_keywords = $total_comp_keywords = $total_audit = $total_citation = $total_opp_count = $total_pages_count = 0;
                
                foreach($get_locations as $get_location){
                    
                    $MCCUserId = $get_location->MCCUserId;
                    $brand_name = get_user_meta( $MCCUserId, 'BRAND_NAME' , true );
                    $website = get_user_meta( $MCCUserId, 'website' , true );
                    
                    $billing_info = billing_info($MCCUserId);
                    
                    $number_of_keywords = $billing_info['number_of_keywords'] ? $billing_info['number_of_keywords'] : '0';
                    $count_comp_keywords = $billing_info['count_comp_keywords'] ? $billing_info['count_comp_keywords'] : '0';
                    $key_opp_count = $billing_info['key_opp_count'] ? $billing_info['key_opp_count'] : '0';
                    $pages_count = $billing_info['pages_count'] ? $billing_info['pages_count'] : '0';
                    $audit_run_count = intval($billing_info['audit_run_count'] ? $billing_info['audit_run_count'] : '0');
                    $citation_run_count = intval($billing_info['citation_run_count'] ? $billing_info['citation_run_count'] : '0');
                    
                    $total_keywords = $total_keywords + $number_of_keywords;
                    $total_comp_keywords = $total_comp_keywords + $count_comp_keywords;
                    $total_opp_count = $total_opp_count + $key_opp_count;
                    $total_citation = $total_citation + $citation_run_count;
                    $total_audit = $total_audit + $audit_run_count;
                    $total_pages_count = $total_pages_count + $pages_count;
                    
                    ?>
                    <div class="locations_row">
                        <div class="locations_td brand brand">
                            <strong><?php echo $brand_name;?></strong><br>(<?php echo $website;?>)
                        </div>
                        <div class="locations_td"><?php echo $number_of_keywords;?></div>
                        <div class="locations_td"><?php echo $count_comp_keywords;?></div>
                        <div class="locations_td"><?php echo $key_opp_count;?></div>
                        <div class="locations_td"><?php echo $pages_count;?></div>
                        <div class="locations_td"><?php echo $audit_run_count;?></div>
                        <div class="locations_td"><?php echo $citation_run_count;?></div>
                    </div>
                <?php } ?>
                <div class="locations_row total">
                    <div class="locations_td brand"><strong>Total</strong></div>
                    <div class="locations_td"><?php echo $total_keywords;?></div>
                    <div class="locations_td"><?php echo $total_comp_keywords;?></div>
                    <div class="locations_td"><?php echo $total_opp_count;?></div>
                    <div class="locations_td"><?php echo $total_pages_count;?></div>
                    <div class="locations_td"><?php echo $total_audit;?></div>
                    <div class="locations_td"><?php echo $total_citation;?></div>
                </div>
            </div>
            
            
            
        </div>
    </div>

</div>
<!--
<script>
jQuery(document).ready(function(){
    jQuery(".billing_list").hide();
    jQuery(".location_list_button").hide();
    jQuery(".location_list_button").click(function(){
        jQuery(".billing_list").hide();
        jQuery(this).hide();
        jQuery(".billing_list_button").show();
        jQuery(".locations_list").show();
    });
    jQuery(".billing_list_button").click(function(){
        jQuery(".locations_list").hide();
        jQuery(this).hide();
        jQuery(".location_list_button").show();
        jQuery(".billing_list").show();
    });
});
</script>
-->
