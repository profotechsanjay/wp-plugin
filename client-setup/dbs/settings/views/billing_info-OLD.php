<?php

include_once 'common.php';

//include_once '../custom_functions.php';
global $wpdb;

$base_url = site_url();

?>
<div class="billing_tabs">
    <input type="button" class="location_list_button" value="Billing Report">
    <input type="button" class="billing_list_button" value="Payment History">
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
                    
                    $number_of_keywords = $billing_info['number_of_keywords'];
                    $count_comp_keywords = $billing_info['count_comp_keywords'];
                    $key_opp_count = $billing_info['key_opp_count'];
                    $pages_count = $billing_info['pages_count'];
                    $audit_run_count = $billing_info['audit_run_count'];
                    $citation_run_count = $billing_info['citation_run_count'];
                    
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
            
            <div class="billing_list">
                <div class="billing_row head">
                    <div class="billing_td"><strong>Subscription Id</strong></div>
                    <div class="billing_td"><strong>Transection ID</strong></div>
                    <div class="billing_td"><strong>Amount</strong></div>
                    <div class="billing_td"><strong>Paid Date</strong></div>
                    <div class="billing_td"><strong>Status</strong></div>
                </div>
                <?php
                $paid_billing = $wpdb->get_results( "SELECT * FROM `wp_pay_for_locations`", OBJECT );
                foreach($paid_billing as $paid_billings){
                    //print_r($paid_billings);
                    $SubscriptionId = $paid_billings->SubscriptionId;
                    
                    $amount = $paid_billings->amount;
                    
                    $trans_id = $paid_billings->trans_id;
                    $trans_id = $trans_id ? $trans_id : 'wait to fetch...';
                    
                    $startDate = $paid_billings->startDate;
                    
                    $status = $paid_billings->status;
                    ?>
                    <div class="billing_row">
                        <div class="billing_td"><strong><?php echo $SubscriptionId;?></strong></div>
                        <div class="billing_td"><?php echo $trans_id;?></div>
                        <div class="billing_td">$<?php echo $amount;?></div>
                        <div class="billing_td"><?php echo $startDate;?></div>
                        <div class="billing_td"><?php echo $status;?></div>
                    </div>
                <?php
                }
                
                ?>
            </div>
        </div>
    </div>

</div>

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
