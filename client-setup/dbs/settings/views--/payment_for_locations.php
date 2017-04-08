<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( 'settings/views/authorize_payment/css/location_payment.css');?>">

<?php
global $wpdb;
$admin_id = '1';  // Admin ID
//pr($locations_package_prices);

/**************************/

$getlimit_fornext = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
foreach($getlimit_fornext as $getlimit_fornexts){
    $fields_name = $getlimit_fornexts->lpf_field;
    if($fields_name != 'location'){
        $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_used` = '0' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
    }
}

/************************/



//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php

$lp_price = $locations_package_prices->lp_price;
$lp_location_price = $locations_package_prices->lp_location_price;
$lp_locations = $locations_package_prices->lp_locations;
$lp_duration_unit = $locations_package_prices->lp_duration_unit;
$lp_duration_range = $locations_package_prices->lp_duration_range;

$key_field_limit = $lp_locations*$locations_package_prices->lp_key_range;
$ckey_field_limit = $lp_locations*$locations_package_prices->lp_ckey_range;
$keyo_field_limit = $lp_locations*$locations_package_prices->lp_keyo_range;
$pages_field_limit = $lp_locations*$locations_package_prices->lp_page_range;
$audit_field_limit = $lp_locations*$locations_package_prices->lp_audit_range;
$citation_field_limit = $lp_locations*$locations_package_prices->lp_citation_range;
$lp_trial = $locations_package_prices->lp_trial;
$lp_triallastdate = $locations_package_prices->lp_triallastdate;
$lp_payment_startfrom = date('Y-m-d', strtotime($lp_triallastdate . ' +1 day'));
$current_paymentdate = date("Y-m-d");


//pr($locations_package_prices); die;
$current_page = site_url().'/'.ST_LOC_PAGE."?parm=billing_info";
$url = site_url();

$agency_break = explode('_', $wpdb->dbname);

//$agency_name = $agency_break[1];
$agency_name = $wpdb->dbname;  //

/********************************/



$count_payments = $wpdb->get_var("SELECT COUNT(*) FROM `wp_pay_for_locations`");
if($count_payments == 0){
    if(isset($lp_trial) && !empty($lp_trial)){
        $lp_payment_startfrom = date('Y-m-d', strtotime($lp_triallastdate . ' +1 day'));
    }else{
        $lp_payment_startfrom = date("Y-m-d");
    }
    $amount = $lp_price;
}else{
    $initial_payment = get_user_meta($admin_id, 'location_initial_payment', true);
    $location_next_pending_payment = get_user_meta($admin_id, 'location_next_pending_payment', true);
    $amount = $location_next_pending_payment;
}

/********************************/

$noof_locations = $locations_count;

$intervalLength = $lp_duration_unit;  //  1
$intervalUnit = $lp_duration_range;   // months
//$orderInvoiceNumber = mt_rand(10000000,99999999);
$orderInvoiceNumber = date("YmdHis");
$status = 'paid';
?>

<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['location_payment_submit'])) {
    
    /***************************************************************/
    //echo $_POST['creditCardExpirationYear'].'-'.$_POST['creditCardExpirationMonth'];
    require('authorize_payment/Authorize.Net-XML-master/config.inc.php');
    require('authorize_payment/Authorize.Net-XML-master/AuthnetXML.class.php');
    
    $xml = new AuthnetXML(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetXML::USE_PRODUCTION_SERVER);
    $xml->ARBCreateSubscriptionRequest(array(
        'subscription' => array(
            'name' => $agency_name,
            'paymentSchedule' => array(
                'interval' => array(
                    'length' => $intervalLength,
                    'unit' => $intervalUnit
                ),
                'startDate' => $current_paymentdate,
                'totalOccurrences' => '100',   //number of Cycles
                'trialOccurrences' => '0'
            ),
            'amount' => $amount,
            'trialAmount' => '0.00',
            'payment' => array(
                'creditCard' => array(
                    'cardNumber' => $_POST['creditCardCardNumber'],
                    'expirationDate' => $_POST['creditCardExpirationYear'].'-'.$_POST['creditCardExpirationMonth'],
                    'cardCode' => $_POST['creditCardCardCode']
                )
            ),
            'order' => array(
                'invoiceNumber' => $orderInvoiceNumber,
                'description' => 'Monthly payment for locations by '.$agency_name
            ),
            'customer' => array(
                'id' => $agency_name,
                'email' => $_POST['customerEmail'],
                'phoneNumber' => $_POST['customerPhoneNumber']
            ),
            'billTo' => array(
                'firstName' => $_POST['billToFirstName'],
                'lastName' => $_POST['billToLastName'],
                'company' => $_POST['billToCompany'],
                'address' => $_POST['billToAddress'],
                'city' => $_POST['billToCity'],
                'state' => $_POST['billToState'],
                'zip' => $_POST['billToZip'],
                'country' => $_POST['billToCountry']
            )
        )
    ));
    //pr($xml); die;
    $resultCode = $xml->messages->resultCode;
    $message_code = $xml->messages->message->code;
    $message_text = $xml->messages->message->text;
    $message_success = ($xml->isSuccessful()) ? 'yes' : 'no';
    $message_error = ($xml->isError()) ? 'yes' : 'no';
    $subscriptionId = $xml->subscriptionId;
    
    $customerProfileId = $xml->profile->customerProfileId;
    $customerPaymentProfileId = $xml->profile->customerPaymentProfileId;
    
    if($resultCode == "Ok" && $message_code == "I00001" && $message_success == "yes" && $message_error == "no"){
        echo "<div class='payment_response_message'>";
        echo "Subscription id is <strong>".$subscriptionId."</strong><br>";
        echo $text_message = $message_text.'. Wait for refresh page.';
        echo "</div>";
        $subscription_id = $subscriptionId;
        
        $creditCardCardNumber = "XXXX-XXXX-XXXX-" . substr($_POST['creditCardCardNumber'],-4,4);
        
        $query = "INSERT INTO `wp_pay_for_locations`"
                . "(`SubscriptionId`, `orderInvoiceNumber`, `customerEmail`, `creditCardCardNumber`, `intervalLength`, `intervalUnit`, `amount`, `noof_locations`, `startDate`, `modifyDate`, `status`, `customerProfileId`, `customerPaymentProfileId`) VALUES "
                . "('".$subscription_id."','".$orderInvoiceNumber."','".$_POST['customerEmail']."','".$creditCardCardNumber."','".$intervalLength."','".$intervalUnit."','".$amount."','".$noof_locations."','".date("Y-m-d h:i:s")."','".date("Y-m-d h:i:s")."','".$status."','".$customerProfileId."','".$customerPaymentProfileId."')";
        $wpdb->query($query);
        
        $location_row = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields`");
    
        if($location_row == 0){
            /*
            $limit_insert_query = "INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES
                            ('keywords', '".$key_field_limit."', '0', '0', '0'),
                            ('comp_keywords', '".$ckey_field_limit."', '0', '0', '0'),
                            ('keyword_opp', '".$keyo_field_limit."', '0', '0', '0'),
                            ('pages', '".$pages_field_limit."', '0', '0', '0'),
                            ('site_audit', '".$audit_field_limit."', '0', '0', '0'),
                            ('citation_run', '".$citation_field_limit."', '0', '0', '0'),
                            ('location', '".$lp_locations."', '0', '0', '0')";

            $limit_insert = $wpdb->query($limit_insert_query);
            */
        }else{
            $getlimit_fornext = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
            foreach($getlimit_fornext as $getlimit_fornexts){
                $fields_name = $getlimit_fornexts->lpf_field;
                if($fields_name != 'location'){
                    $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_used` = '0' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                }
            }
        }
        
        $location_next_pending_payment = get_user_meta('1', 'location_next_pending_payment', true);
        if ($location_next_pending_payment == '') {    
            add_user_meta( '1', 'location_next_pending_payment', $amount);
        }

        header('refresh: 2; url='.$current_page); //Redirect after Payment Successfully
    } else {
        echo "<div class='payment_response_message'>";
        echo $text_message = $message_text;
        echo "</div>";
    }
    /*************************************************************/
    
    update_user_meta($admin_id, 'first_name', $_POST['billToFirstName']);
    update_user_meta($admin_id, 'last_name', $_POST['billToLastName']);
    update_user_meta($admin_id, 'BRAND_NAME', $_POST['billToCompany']);
    update_user_meta($admin_id, 'phonenumber', $_POST['customerPhoneNumber']);
    update_user_meta($admin_id, 'streetaddress', $_POST['billToAddress']);
    update_user_meta($admin_id, 'city', $_POST['billToCity']);
    update_user_meta($admin_id, 'state', $_POST['billToState']);
    update_user_meta($admin_id, 'zip', $_POST['billToZip']);
    update_user_meta($admin_id, 'country', $_POST['billToCountry']);
    
}
?>
<?php

$user_info = get_userdata($admin_id); // 1 is admin id
$user_email = $user_info->user_email;
$f_name = get_user_meta($admin_id, 'first_name', true);
$l_name = get_user_meta($admin_id, 'last_name', true);
$brand_name = get_user_meta($admin_id, 'BRAND_NAME', true);
$phone = get_user_meta($admin_id, 'phonenumber', true);
$address = get_user_meta($admin_id, 'streetaddress', true);
$city = get_user_meta($admin_id, 'city', true);
$state = get_user_meta($admin_id, 'state', true);
$zip = get_user_meta($admin_id, 'zip', true);
$country = get_user_meta($admin_id, 'country', true);

?>
<div class="open_package_form">
    <input type="button" value="Buy Now Package For Locations" name="buy_location_package" class="buy_location_package">
</div>
<form method="post" action="" class="form_location_package">
    <div class="location_payment left_section">
        <input required type="text" name="billToFirstName" value="<?php echo $f_name;?>" placeholder="First Name">
        <input required type="text" name="billToLastName" value="<?php echo $l_name;?>" placeholder="Last Name">
        <input required type="email" name="customerEmail" value="<?php echo $user_email;?>" placeholder="Email Id">
        <input required type="text" name="billToCompany" value="<?php echo $brand_name;?>" placeholder="Company Name">
        <input required type="text" name="customerPhoneNumber" value="<?php echo $phone;?>" placeholder="Phone Number">
        <input required type="text" name="billToAddress" value="<?php echo $address;?>" placeholder="Address #">
        <input required type="text" name="billToCity" value="<?php echo $city;?>" placeholder="City">
        <input required type="text" name="billToState" value="<?php echo $state;?>" placeholder="State">
        <input required type="text" name="billToZip" value="<?php echo $zip;?>" placeholder="Zip">
        <input required type="text" name="billToCountry" value="<?php echo $country;?>" placeholder="Country">
    </div>
    <div class="location_payment right_section">
        <input required type="text" name="creditCardCardNumber" value="" placeholder="Card Number">
        <input required type="text" name="creditCardExpirationMonth" value="" placeholder="Expiration Month">
        <input required type="text" name="creditCardExpirationYear" value="" placeholder="Expiration Year">
        <input required type="text" name="creditCardCardCode" value="" placeholder="CSV">
        <span><strong>Note :- </strong>Pay initial Payment $<?php echo $lp_price;?> before add locations. <br>
        1) Only upto <?php echo $lp_locations;?> Locations add under this package. <br>
        2) After that Pay $<?php echo $lp_location_price;?>/Location. <br>
        </span>
        <input type="submit" name="location_payment_submit" value="Pay Now">
    </div>
</form>

<script>
jQuery(document).ready(function(){
    $(".form_location_package").hide();
    jQuery(".buy_location_package").click(function(){       
        jQuery(".form_location_package").toggle("slow");
    });
});
</script>
