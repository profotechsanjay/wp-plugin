<?php
/* Update Subscription Amount by subscription id if number of locations more than 5
 * 
 * 
*/

require('authorize_payment/Authorize.Net-XML-master/config.inc.php');
require('authorize_payment/Authorize.Net-XML-master/AuthnetXML.class.php');
//require_once 'authorize_payment/AuthorizeNet.php';

global $wpdb;
$billing_enable = '0';
$billing_enable = BILLING_ENABLE;

$query = "SELECT * FROM `wp_client_location` WHERE `status` != '0'";

$query_results = $wpdb->get_results($query);

$rowcount = $wpdb->num_rows;

$min_locations_allowed = get_user_meta( $user_id = '1', 'number_of_locations' , true );

$location_next_pending_payment = get_user_meta( $user_id = '1', 'location_next_pending_payment' , true );

$per_location_price = get_user_meta( $user_id = '1', 'per_location_price' , true );

$date = date('Y-m-d H:i:s');

$previous_payment_query = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` WHERE `status` = 'paid' ORDER BY `payment_id` DESC LIMIT 1");

$previous_payment_date = $previous_payment_query->startDate;

$previous_payment_SubscriptionId = $previous_payment_query->SubscriptionId;

$total_hours = intval(abs(strtotime($date) - strtotime($previous_payment_date))/(60*60));  // Count hours from last payment cycle

if($subsc_action == "add"){
    
    $add_query = "INSERT INTO `wp_add_locations_status`(`als_created_date`, `als_update_date`, `als_status`) VALUES ('".$date."','".$date."','".$subsc_action."')";

    $insert_query = $wpdb->query($add_query);

    if($insert_query && $rowcount >= $min_locations_allowed && $billing_enable == '1'){

        $next_payment_date = date('Y-m-d', strtotime('+1 month', strtotime($previous_payment_date)));

        $daylen = 60*60*24;

       $paid_for_days = intval((strtotime($next_payment_date)-strtotime($previous_payment_date))/$daylen);

       if($paid_for_days == '31' || $paid_for_days == '30'){
           $additional_payment = $per_location_price;
       } else {
           $per_day_price = $per_location_price/30;
           $additional_payment = $paid_for_days*$per_day_price;
       }

       $new_payment_amount = intval($location_next_pending_payment + $per_location_price + $additional_payment);

       $resultsnew = subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours);
       
    }elseif($billing_enable == '0'){
        if($rowcount >= $min_locations_allowed){
            $new_payment_amount = intval($location_next_pending_payment + $per_location_price);
            update_user_meta($user_id = '1', 'location_next_pending_payment', $new_payment_amount);
            $result = "success";
            $resultsnew = "success";
        }
        
    }
    
} elseif($subsc_action == "delete") {
    $delete_query = "UPDATE `wp_add_locations_status` SET `als_update_date` = '".$date."', `als_status` = '".$subsc_action."' WHERE `als_status` = 'add' ORDER BY `als_id` DESC LIMIT 1";

    $update_query = $wpdb->query($delete_query);
    
    if($update_query && $rowcount >= $min_locations_allowed && $billing_enable == '1'){
        
        $new_payment_amount = intval($location_next_pending_payment - $per_location_price);

        $resultsnew = subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours);
    }elseif($billing_enable == '0'){
        if($rowcount >= $min_locations_allowed){
            $new_payment_amount = intval($location_next_pending_payment - $per_location_price);
            update_user_meta($user_id = '1', 'location_next_pending_payment', $new_payment_amount);
            $result = "success";
            $resultsnew = "success";
        }
        
    }
} elseif($subsc_action == "cron_updatesubscription"){
    
    if($billing_enable == '1'){
        $new_payment_amount = $location_next_pending_payment;
        subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours);
    }elseif($billing_enable == '0'){
        $result = "success";
        $resultsnew = "success";
    }   
    
} elseif($subsc_action == "cron_updatesubscription_extra_data_consume"){    
    
    if($billing_enable == '1'){
        $new_payment_amount = $location_next_pending_payment + $extra_consume_price;
        //subscription_update($previous_payment_SubscriptionId, $new_payment_amount);
    }elseif($billing_enable == '0'){
        $result = "success";
        $resultsnew = "success";
    }
    
} elseif($subsc_action == "add_ons_add"){   
    
    if($billing_enable == '1'){
        $new_payment_amount = $location_next_pending_payment + $additional_payment;
        $resultsnew = subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours);
    }elseif($billing_enable == '0'){
        $result = "success";
        $resultsnew = "success";
    }
    
} elseif($subsc_action == "add_ons_stop"){   
    
    if($billing_enable == '1'){
        $new_payment_amount = $location_next_pending_payment - $additional_payment;
        $resultsnew = subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours);
    }elseif($billing_enable == '0'){
        $result = "success";
        $resultsnew = "success";
    }
    
} elseif($subsc_action == "discount_coupon"){   
    
    if($billing_enable == '1'){
        $new_payment_amount = $location_next_pending_payment + $discount;
        $resultsnew = subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours);
    }elseif($billing_enable == '0'){
        $new_payment_amount = $location_next_pending_payment + $discount;
        update_user_meta($user_id = '1', 'location_next_pending_payment', $new_payment_amount);
        $result = "success";
        $resultsnew = "success";
    }
    
}



function subscription_update($previous_payment_SubscriptionId, $new_payment_amount, $total_hours){
    
    if($total_hours > 48){ 
        $new_payment_amount = round($new_payment_amount,2);
        $xml = new AuthnetXML(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetXML::USE_PRODUCTION_SERVER);
        $xml->ARBUpdateSubscriptionRequest(array(
            //'refId' => 'Sample',
            'subscriptionId' => $previous_payment_SubscriptionId,
            'subscription' => array(
                'amount' => $new_payment_amount,
            ),
        ));
        //echo $xml;
        $resultCode = $xml->messages->resultCode;
        $message_code = $xml->messages->message->code;
        $message_success = ($xml->isSuccessful()) ? 'yes' : 'no';
        $message_error = ($xml->isError()) ? 'yes' : 'no';

        if($resultCode == "Ok" && $message_code == "I00001" && $message_success == "yes" && $message_error == "no"){
            update_user_meta($user_id = '1', 'location_next_pending_payment', $new_payment_amount);
            $result = "success";
            return $result;
        }
    } else {
        update_user_meta($user_id = '1', 'location_next_pending_payment', $new_payment_amount);
        $result = "success";
        return $result;
    }

}
?>
