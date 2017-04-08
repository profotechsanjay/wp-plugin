<?php
/* This code Run if delete location, but only if number of locations more than 5, otherwise not.
 * So according to this page, last row with any subscription ID Status Change from 'Paid' to 'Cancelled'.
 * And in Authorize.net ARB Subscription is Cancelled with above (same) subscription ID.
*/

require('authorize_payment/Authorize.Net-XML-master/config.inc.php');
require('authorize_payment/Authorize.Net-XML-master/AuthnetXML.class.php');
//require_once 'authorize_payment/AuthorizeNet.php';


$query = "SELECT * FROM `wp_pay_for_locations` WHERE `noof_locations` = '1' AND `status` = 'paid' ORDER BY `payment_id` DESC LIMIT 1";
$query_results = $wpdb->get_row($query);

$subscriptionId = $query_results->SubscriptionId;

$xml = new AuthnetXML(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetXML::USE_PRODUCTION_SERVER);
$xml->ARBCancelSubscriptionRequest(array(
    //'refId' => 'Sample',
    'subscriptionId' => $subscriptionId
));

$response = $xml->messages->resultCode;
$code = $xml->messages->message->code;
$successful = ($xml->isSuccessful()) ? 'yes' : 'no';
$error = ($xml->isError()) ? 'yes' : 'no';

if($response == "Ok" && $code == "I00001" && $successful == "yes" && $error == "no"){
    $xml->ARBGetSubscriptionStatusRequest(array(
        'subscriptionId' => $subscriptionId
    ));
    $status = $xml->status;
    
    $update_query = "UPDATE `wp_pay_for_locations` SET `modifyDate`='".date("Y-m-d h:i:s")."',`status`='".$status."' WHERE `SubscriptionId` = '".$subscriptionId."' AND `payment_id` = '".$query_results->payment_id."'";
    $update_query_run = $wpdb->query( $wpdb->prepare($update_query));
    
    $prev_noof_locations = get_user_meta( $user_id = 1, 'number_of_locations', true );
    $new_noof_locations = $prev_noof_locations + 1;
    update_user_meta( $user_id = 1, 'number_of_locations', $new_noof_locations );
}
?>

<?php
    //echo $xml;
?>

