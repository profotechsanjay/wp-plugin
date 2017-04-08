<?php

include_once 'common.php';

//include_once '../custom_functions.php';
global $wpdb;

$base_url = site_url();

?>
<div class="billing_tabs">
    <a href="<?php echo site_url();?>/location-settings/?parm=billing_info" class="location_list_button">Billing Report</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=payment_history" class="location_list_button active">Billing History</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=add_ons" class="location_list_button">Add-Ons</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=purchased_addons" class="location_list_button">Add-Ons Report</a>
</div>
<div class="contaninerinner">     
    <h4>Payment History</h4>
    
    <div class="panel panel-primary">
      
        <div class="panel-body new"> 
            <div class="billing_list">
                <div class="billing_row head">
                    <div class="billing_td"><strong>Subscription Id</strong></div>
                    <div class="billing_td"><strong>Transection ID</strong></div>
                    <div class="billing_td"><strong>Amount</strong></div>
                    <div class="billing_td"><strong>Paid Date</strong></div>
                    <div class="billing_td"><strong>Status</strong></div>
                </div>
                <?php
                $paid_billing = $wpdb->get_results( "SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` DESC", OBJECT );
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
                if(empty($paid_billing)){
                    echo "No Billing payment Found";
                }
                
                ?>
            </div>
        </div>
    </div>

</div>

