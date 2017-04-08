<?php

include_once 'common.php';

//include_once '../custom_functions.php';
global $wpdb;
global $billing_enable;

$base_url = site_url();

?>

<div class="billing_tabs">
    <a href="<?php echo site_url();?>/location-settings/?parm=billing_info" class="location_list_button active">Billing Report</a>
    <!--<a href="<?php echo site_url();?>/location-settings/?parm=add_ons" class="location_list_button">Add-Ons</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=purchased_addons" class="location_list_button">Add-Ons Report</a>-->
    <?php
    $billing_type = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` DESC LIMIT 1");
    $billing_SubscriptionId = $billing_type->SubscriptionId;
    $billing_status = $billing_type->status;
    if(($billing_status == 'paid') && !empty($billing_SubscriptionId)){ ?>
        <a href="<?php echo site_url();?>/location-settings/?parm=payment_history" class="location_list_button">Billing History</a>
        <?php
        $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");
        if(empty($billing_cancelon)){ ?>
            <a href="<?php echo site_url();?>/location-settings/?parm=cancelSubscription" class="location_list_button">Stop Billing</a>
        <?php }else{ ?>
            <a href="<?php echo site_url();?>/location-settings/?parm=cancelSubscription" class="location_list_button">Stopped Billing</a>
        <?php }
    }
    
    ?>
</div>
<div class="contaninerinner">
    <h4>Billing Info</h4>
    
    <div class="panel panel-primary">
        <div class="panel-heading">Billing Info</div>
        <?php
        $get_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `status` = '1'");
        $noof_locations_count = $wpdb->num_rows;

        $total_keywords = $total_comp_keywords = $total_audit = $total_citation = $total_opp_count = $total_pages_count = 0;
        ?>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover" id="billing_info_datatable" >
                <thead>
                    <tr>
                        <th style="width: 4%;">S.No.</th>
                        <th style="width: 30%;">Client</th>
                        <th style="width: 11%;">Keywords</th>
                        <th style="width: 11%;">Comp Keywords</th>
                        <th style="width: 11%;">Keyword Opp</th>
                        <th style="width: 11%;">Pages</th>
                        <th style="width: 11%;">Site Audit Runs</th>
                        <th style="width: 11%;">Citation Runs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach($get_locations as $get_location){
                        
                        $i = $i + 1;
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
                        <tr class="rowmod" data-id="<?php echo $get_location->id; ?>">
                            <td><?php echo $i; ?></td>
                            <td><strong><?php echo $brand_name;?></strong><br>(<?php echo $website;?>)</td>
                            <td><?php echo $number_of_keywords;?></td>
                            <td><?php echo $count_comp_keywords;?></td>
                            <td><?php echo $key_opp_count;?></td>
                            <td><?php echo $pages_count;?></td>
                            <td><?php echo $audit_run_count;?></td>
                            <td><?php echo $citation_run_count;?></td>
                        </tr>
                    <?php
                    }
                    ?>  
                </tbody>
                <thead>
                    <tr>
                        <th></th>
                        <th>Total</th>
                        <th><?php echo $total_keywords;?></th>
                        <th><?php echo $total_comp_keywords;?></th>
                        <th><?php echo $total_opp_count;?></th>
                        <th><?php echo $total_pages_count;?></th>
                        <th><?php echo $total_audit;?></th>
                        <th><?php echo $total_citation;?></th>
                    </tr>
                </thead>
            </table>
            </div>
    </div>

</div>


<?php
    include_once 'add_ons.php';

$billing_check = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` DESC LIMIT 1");
$billing_querystatus = $billing_check->status;
$billing_querySubscriptionId = $billing_check->SubscriptionId;
if(($billing_enable == 1) && !empty($billing_querySubscriptionId) && ($billing_querystatus == 'paid')){
    
    $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");
    if(empty($billing_cancelon)){ 
        //So these two files show only if Billing Subscription not to be stopped by admin
        //include_once 'add_ons.php';
        include_once 'purchased_addons.php';
    }
}
?>

