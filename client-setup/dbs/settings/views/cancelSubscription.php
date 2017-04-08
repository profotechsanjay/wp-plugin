<?php
/* Cancel Subscription
 * 
 * 
*/
include_once 'common.php';
require('authorize_payment/Authorize.Net-XML-master/config.inc.php');
require('authorize_payment/Authorize.Net-XML-master/AuthnetXML.class.php');
//require_once 'authorize_payment/AuthorizeNet.php';

global $wpdb;
global $billing_enable;
//echo "hello"; die;
?>
<div class="billing_tabs">
    <a href="<?php echo site_url();?>/location-settings/?parm=billing_info" class="location_list_button">Billing Report</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=payment_history" class="location_list_button">Billing History</a>
    <!--<a href="<?php echo site_url();?>/location-settings/?parm=add_ons" class="location_list_button">Add-Ons</a>
    <a href="<?php echo site_url();?>/location-settings/?parm=purchased_addons" class="location_list_button">Add-Ons Report</a>-->
    <?php
    $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");
    if(empty($billing_cancelon)){ ?>
        <a href="<?php echo site_url();?>/location-settings/?parm=cancelSubscription" class="location_list_button active">Stop Billing</a>
    <?php }else{ ?>
        <a href="<?php echo site_url();?>/location-settings/?parm=cancelSubscription" class="location_list_button active">Stopped Billing</a>
    <?php } ?>
</div>
<div class="contaninerinner">
    <h4></h4>
    <div class="panel panel-primary">
        <div class="panel-heading">Stop Billing Subscription</div>
        <div class="panel-body">
        <?php
        $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");

        $previous_payment_query = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` WHERE `status` = 'paid' ORDER BY `payment_id` DESC LIMIT 1");

        $previous_payment_date = $previous_payment_query->startDate;

        $stop_datefor_admin = Date("Y-m-d 11:00:00a", strtotime($previous_payment_date." +1 Month -1 Day"));

        $previous_payment_status = $previous_payment_query->status;

        $previous_payment_SubscriptionId = $previous_payment_query->SubscriptionId;

        $total_hours = intval(abs(strtotime($date) - strtotime($previous_payment_date))/(60*60));  // Count hours from last payment cycle


        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stop_billing_subscription'])) {
            $resultsnew = subscription_cancel($previous_payment_SubscriptionId, $stop_datefor_admin);
            if($resultsnew){
                echo '<div class="alert alert-success billing_alrady_stop">Billing Subscription Successfully Stopped. Wait to redirect...</div>';
                $url = site_url()."/location-settings/?parm=billing_info";
                header( "refresh:2;url=".$url );
            }else{
                echo '<div class="alert alert-danger billing_alrady_stop">Billing Subscription not Stopped, try again.</div>';
            }
        }

        if($previous_payment_status == 'paid'){
            if(empty($billing_cancelon)){ ?>
                <form method="post" class="cancel_subscription_form" action="" onsubmit="return confirm('Do you really want to Cancel Billing Subscription?');">
                    <input type="submit" value="Stop Billing Subscription" name="stop_billing_subscription" class="stop_billing_subscription">
                </form>
            <?php }else{ ?>
                <div class="alert alert-info billing_alrady_stop"><strong>Billing Already Canceled.</strong><br> <strong>Subscription ID : </strong><?php echo $previous_payment_SubscriptionId;?><br>You can used Agency featured till Date <strong><?php echo $stop_datefor_admin;?></strong></div>
            <?php }
        }else{ ?>
            <div class="alert alert-info billing_alrady_stop"><strong>Currently no Subscription run for Billing</strong></div>
        <?php }

        function subscription_cancel($previous_payment_SubscriptionId, $stop_datefor_admin){
                global $wpdb;
                //$previous_payment_SubscriptionId = $previous_payment_query->SubscriptionId;
                $xml = new AuthnetXML(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetXML::USE_PRODUCTION_SERVER);
                $xml->ARBCancelSubscriptionRequest(array(
                    //'refId' => 'Sample',
                    'subscriptionId' => $previous_payment_SubscriptionId,

                ));
                //echo $xml;  die;

                $resultCode = $xml->messages->resultCode;
                $message_code = $xml->messages->message->code;
                $message_success = ($xml->isSuccessful()) ? 'yes' : 'no';
                $message_error = ($xml->isError()) ? 'yes' : 'no';

                if($resultCode == "Ok" && $message_code == "I00001" && $message_success == "yes"){
                    $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");
                    if(empty($billing_cancelon)){
                        $stop_billing_query = "INSERT INTO `wp_usermeta`(`user_id`, `meta_key`, `meta_value`) VALUES ('1','billing_cancel_date','".$stop_datefor_admin."')";
                    }else{
                        $stop_billing_query = "UPDATE `wp_usermeta` SET `meta_value` = '".$stop_datefor_admin."' WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'";
                    }
                    $billing_stop = $wpdb->query($stop_billing_query);
                    if($billing_stop){
                        return true;
                    }
                }else{
                    return false;
                }
        }
        ?>
        </div>
    </div>
</div>

