<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( 'settings/views/authorize_payment/css/location_payment.css');?>">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<?php
global $wpdb;
$admin_id = '1';  // Admin ID
//pr($locations_package_prices->lp_location_price);
?>

<?php
$resultsctr = array();
$resultsctr[0][id] = "AUS";
$resultsctr[0][name] = "Australia";
$resultsctr[1][id] = "CAN";
$resultsctr[1][name] = "Canada";
$resultsctr[2][id] = "DEU";
$resultsctr[2][name] = "Germany";
$resultsctr[3][id] = "HKG";
$resultsctr[3][name] = "Hong Kong";
$resultsctr[4][id] = "IRL";
$resultsctr[4][name] = "Ireland";
$resultsctr[5][id] = "MAC";
$resultsctr[5][name] = "Macau";
$resultsctr[7][id] = "NLD";
$resultsctr[7][name] = "Netherlands";
$resultsctr[8][id] = "NZL";
$resultsctr[8][name] = "New Zealand";
$resultsctr[9][id] = "SGP";
$resultsctr[9][name] = "Singapore";
$resultsctr[10][id] = "ZAF";
$resultsctr[10][name] = "South Africa";
$resultsctr[11][id] = "PHL";
$resultsctr[11][name] = "Philippines";
$resultsctr[12][id] = "TWN";
$resultsctr[12][name] = "Taiwan";
$resultsctr[13][id] = "GBR";
$resultsctr[13][name] = "United Kingdom";
$resultsctr[14][id] = "USA";
$resultsctr[14][name] = "United States";


    
$country = get_user_meta($admin_id, 'country', true);

              
// get cuntry select options
foreach($resultsctr as $countryitem) {
$selectcheck = "";    
if($country == $countryitem["id"]) { $selectcheck = "selected";}    
$countryoptions .= '<option value="' . $countryitem["id"] . '" ' . $selectcheck . '>'.$countryitem["name"].'</option>';

}
?>

<?php
global $wpdb;
wp_enqueue_style('billing_form_style.css', SET_COUNT_PLUGIN_URL .'/assets/css/billing_form_style.css','', SET_VERSION); 

$currentdatetime = date("Y-m-d h:i:s");

if (count($wpdb->get_var('SHOW TABLES LIKE "wp_package_used"')) == 0) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   
    $sql = 'CREATE TABLE IF NOT EXISTS `wp_package_used` (
        `wpu_id` int(11) PRIMARY KEY AUTO_INCREMENT,
        `wpu_data` longtext NOT NULL,
        `wpu_date_create` datetime NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
    dbDelta($sql);
}

//pr($locations_package_prices);



if(isset($_SESSION["packagetype"]) && isset($_SESSION["packageid"])){
    $packagetype = $_SESSION["packagetype"];
    $packageid = $_SESSION["packageid"];
    include_once ABSPATH . "wp-content/plugins/settings/get_paying_packages.php";
    //pr($getpackage);
    if($packagetype == 'discount_coupon'){
        
        $discount_coupon = applied_discountcode_data($packageid);

        $getpackage->dc_id = $discount_coupon['codedata']->dc_id;
        $getpackage->dc_name = $discount_coupon['codedata']->dc_name;
        $getpackage->dc_cost = $discount_coupon['codedata']->dc_cost;
        $getpackage->dc_location = $discount_coupon['codedata']->dc_location;
        $getpackage->dc_keywords = $discount_coupon['codedata']->dc_keywords;
        $getpackage->dc_comp_key = $discount_coupon['codedata']->dc_comp_key;
        $getpackage->dc_key_opp = $discount_coupon['codedata']->dc_key_opp;
        $getpackage->dc_pages = $discount_coupon['codedata']->dc_pages;
        $getpackage->dc_siteaudit = $discount_coupon['codedata']->dc_siteaudit;
        $getpackage->dc_citation = $discount_coupon['codedata']->dc_citation;
        $getpackage->dc_location_price = $locations_package_prices->lp_location_price;
        $getpackage->dc_duration_unit = $locations_package_prices->lp_duration_unit;
        $getpackage->dc_duration_range = $locations_package_prices->lp_duration_range;
    
        $_SESSION["packageprice"] = $discount_coupon['codedata']->dc_cost;
    }else{
        $getpackage->dc_location_price = $locations_package_prices->lp_location_price;
        $getpackage->dc_duration_unit = $locations_package_prices->lp_duration_unit;
        $getpackage->dc_duration_range = $locations_package_prices->lp_duration_range;
    }
    
    
    $getpackage->package_type = $packagetype;
    //pr($getpackage);
    $serialize_package = serialize($getpackage);
}

//pr($getpackage);
$package_type = $getpackage->package_type;
if($package_type == 'discount_coupon'){
    $discount_name = $package_type = $getpackage->dc_name;
    $readonly = 'readonly';
}else{
    $discount_name = '';
    $readonly = '';
}


//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php

$lp_price = $getpackage->dc_cost;
$lp_location_price = $getpackage->dc_location_price;
$lp_locations = $getpackage->dc_location;
$lp_duration_unit = $getpackage->dc_duration_unit;
$lp_duration_range = $getpackage->dc_duration_range;

$key_field_limit = $getpackage->dc_keywords;
$ckey_field_limit = $getpackage->dc_comp_key;
$keyo_field_limit = $getpackage->dc_key_opp;
$pages_field_limit = $getpackage->dc_pages;
$audit_field_limit = $getpackage->dc_siteaudit;
$citation_field_limit = $getpackage->dc_citation;
/*
$lp_trial = $getpackage->lp_trial;
$lp_triallastdate = $getpackage->lp_triallastdate;
$lp_payment_startfrom = date('Y-m-d', strtotime($lp_triallastdate . ' +1 day'));
$current_paymentdate = date("Y-m-d");
*/
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
    $lp_payment_startfrom = date("Y-m-d");
    $amount = $lp_price;
}else{
    $initial_payment = get_user_meta($admin_id, 'location_initial_payment', true);
    $location_next_pending_payment = get_user_meta($admin_id, 'location_next_pending_payment', true);
    $amount = $location_next_pending_payment;
}

/********************************/

$noof_locations = '';

$intervalLength = $lp_duration_unit;  //  1
$intervalUnit = $lp_duration_range;   // months
//$orderInvoiceNumber = mt_rand(10000000,99999999);
$orderInvoiceNumber = date("YmdHis");
$status = 'paid';
?>

<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['location_payment_submit'])) {
    
    if(($_SESSION["packagetype"] == 'discount_coupon') && isset($_SESSION["packageprice"]) && ($_SESSION["packageprice"] == 0)){
       
        $resultCode = "Ok";
        $message_code = "I00001";
        $message_success = "yes";
        $message_error = "no";
        
        $subscriptionId = 0;
        $creditCardCardNumber = 'XXXX-XXXX-XXXX-XXXX';
        $intervalLength = 0;
        $intervalUnit = 0;
        $amount = 0;
        $noof_locations = '';
        $status = 'paid';
        $customerProfileId = '';
        $customerPaymentProfileId = '';
        
    }else{
        
        //echo $_POST['creditCardExpirationYear'].'-'.$_POST['creditCardExpirationMonth'];
        require('authorize_payment/Authorize.Net-XML-master/config.inc.php');
        require('authorize_payment/Authorize.Net-XML-master/AuthnetXML.class.php'); 

        // Code Starts By Rudra(Vaild Credit Check)
        require('authorize_payment/Authorize.Net-XML-master/AuthnetAIM.class.php');    
      
   
        try{
            $cardNumber = $_POST['creditCardCardNumber'];
            $_POST['creditCardExpirationMonth'] = str_pad($_POST['creditCardExpirationMonth'], 2, '0', STR_PAD_LEFT);
            $expirationDate = $_POST['creditCardExpirationMonth'].'-'.$_POST['creditCardExpirationYear'];
            $cardCode = $_POST['creditCardCardCode'];

            $payment = new AuthnetAIM(AUTHNET_LOGIN, AUTHNET_TRANSKEY, 0);
            $payment->setTransaction($cardNumber, $expirationDate, '0.01',$cardCode);
            $payment->process();

            if($payment->isDeclined()){
                // Get reason for the decline from the bank. This always says,
                // "This credit card has been declined". Not very useful.
                $reasoncode=$payment->getResponseCode();
                $reasonsubcode=$payment->getResponseSubcode();
                $reason = $payment->getResponseText();
                // Politely tell the customer their card was declined
                // and to try a different form of payment
            }elseif ($payment->isError()){
                // Get the error number so we can reference the Authnet
                // documentation and get an error description
                $error_number  = $payment->getResponseSubcode();
                $error_message = $payment->getResponseText();
                // Or just echo the message out ourselves
                echo $payment->getResponseMessage();
                // Report the error to someone who can investigate it
                // and hopefully fix it

                // Notify the user of the error and request they contact
                // us for further assistance
            }elseif ($payment->isApproved()){
                // Get the approval code
                $approval_code  = $payment->getAuthCode();

                // Get the results of AVS
                $avs_result     = $payment->getAVSResponse();

                // Get the Authorize.Net transaction ID
                $transaction_id = $payment->getTransactionID();

                // Do stuff with this. Most likely store it in a database.
            }
	}
	catch (AuthnetAIMException $e)
	{
	    echo 'There was an error processing the transaction. Here is the error message: ';
	    echo $e->__toString();
	}
	echo $payment->account_number;
        pr($payment);

	die('--------');
       //Code ends By Rudra
        $xml = new AuthnetXML(AUTHNET_LOGIN, AUTHNET_TRANSKEY, 0);

        $xmlcode = array(
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
        );
       
        $xml->ARBCreateSubscriptionRequest($xmlcode);
        
        $resultCode = $xml->messages->resultCode;
        $message_code = $xml->messages->message->code;
        $message_text = $xml->messages->message->text;
        $message_success = ($xml->isSuccessful()) ? 'yes' : 'no';
        $message_error = ($xml->isError()) ? 'yes' : 'no';
        $subscriptionId = $xml->subscriptionId;

        $customerProfileId = $xml->profile->customerProfileId;
        $customerPaymentProfileId = $xml->profile->customerPaymentProfileId;
    }
    
    
    if($resultCode == "Ok" && $message_code == "I00001" && $message_success == "yes" && $message_error == "no"){
    
        global $wpdb;
        
        if(isset($_SESSION["packageprice"]) && ($_SESSION["packageprice"] == 0)){
            echo "<div class='payment_response_message'>";
            echo "Subscription Successfully Saved.<br>";
            echo 'Wait for refresh page.';
            echo "</div>";
        }else{
            echo "<div class='payment_response_message'>";
            echo "Subscription id is <strong>".$subscriptionId."</strong><br>";
            echo $text_message = $message_text.'. Wait for refresh page.';
            echo "</div>";
            $subscription_id = $subscriptionId;

            $creditCardCardNumber = "XXXX-XXXX-XXXX-" . substr($_POST['creditCardCardNumber'],-4,4);
        }
        
        $query = "INSERT INTO `wp_pay_for_locations`"
                . "(`SubscriptionId`, `orderInvoiceNumber`, `customerEmail`, `creditCardCardNumber`, `intervalLength`, `intervalUnit`, `amount`, `noof_locations`, `startDate`, `modifyDate`, `status`, `customerProfileId`, `customerPaymentProfileId`) VALUES "
                . "('".$subscription_id."','".$orderInvoiceNumber."','".$_POST['customerEmail']."','".$creditCardCardNumber."','".$intervalLength."','".$intervalUnit."','".$amount."','".$noof_locations."','".date("Y-m-d h:i:s")."','".date("Y-m-d h:i:s")."','".$status."','".$customerProfileId."','".$customerPaymentProfileId."')";
        
        
        $wpdb->query($query);

        $location_row = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields`");
    
        if($location_row == 0){
            $limit_insert_query = "INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES
                            ('keywords', '".$key_field_limit."', '0', '0', '0'),
                            ('comp_keywords', '".$ckey_field_limit."', '0', '0', '0'),
                            ('keyword_opp', '".$keyo_field_limit."', '0', '0', '0'),
                            ('pages', '".$pages_field_limit."', '0', '0', '0'),
                            ('site_audit', '".$audit_field_limit."', '0', '0', '0'),
                            ('citation_run', '".$citation_field_limit."', '0', '0', '0'),
                            ('location', '".$lp_locations."', '0', '0', '0')";

            $limit_insert = $wpdb->query($limit_insert_query);
        }else{
            $getlimit_fornext = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
            $getlimitcount = $wpdb->num_rows;
            if($getlimitcount > 0){
                foreach($getlimit_fornext as $getlimit_fornexts){
                    $fields_name = $getlimit_fornexts->lpf_field;
                    if($fields_name == 'keywords'){
                        $check_keywords = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_keywords > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$key_field_limit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'"); 
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('keywords', '".$key_field_limit."', '0', '0', '0')");
                        }   
                    }elseif($fields_name == 'comp_keywords'){
                        $check_comp_keywords = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_comp_keywords > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$ckey_field_limit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'"); 
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('comp_keywords', '".$ckey_field_limit."', '0', '0', '0')");
                        }
                    }elseif($fields_name == 'keyword_opp'){
                        $check_keyword_opp = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_keyword_opp > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$keyo_field_limit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('keyword_opp', '".$keyo_field_limit."', '0', '0', '0')");
                        }
                    }elseif($fields_name == 'pages'){
                        $check_pages = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_pages > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$pages_field_limit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('pages', '".$pages_field_limit."', '0', '0', '0')");
                        }
                    }elseif($fields_name == 'site_audit'){
                        $check_site_audit = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_site_audit > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$audit_field_limit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('site_audit', '".$audit_field_limit."', '0', '0', '0')");
                        }
                    }elseif($fields_name == 'citation_run'){
                        $check_citation_run = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_citation_run > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$citation_field_limit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('citation_run', '".$citation_field_limit."', '0', '0', '0')");
                        }
                    }elseif($fields_name == 'location'){
                        $check_location = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                        if($check_location > 0){
                           $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$lp_locations."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                        }else{
                           $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('location', '".$lp_locations."', '0', '0', '0')");
                        }
                    }
                }
            }
            
        }
        
        $check_next_payment = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'location_next_pending_payment'");
        if(empty($check_next_payment)){
            $user_id = 1;
            add_user_meta( $user_id, 'location_next_pending_payment', $amount);
        }else{
            update_user_meta( $user_id, 'location_next_pending_payment', $amount );
        }
        
        $wpdb->query("INSERT INTO `wp_package_used` (`wpu_data`, `wpu_date_create`) VALUES ('".$serialize_package."', '".$currentdatetime."')");
        
        
        if($getpackage->package_type == 'discount_coupon'){
            //pr($getpackage);
            //pr($getpackage->lp_id);
            //pr($getpackage->lp_name);
            $assignthis_discount_from_agency = assignthis_discount_from_agency($getpackage->dc_id, $getpackage->dc_name);
            //pr($assignthis_discount_from_agency);
            if(!empty($assignthis_discount_from_agency)){
                $getold_dc = $wpdb->get_results("SELECT * FROM `wp_billingdiscount`");
                if(!empty($getold_dc)){
                    foreach($getold_dc as $getold_discounts){
                        $wpdb->query("UPDATE `wp_billingdiscount` SET `bd_status` = 'expire' WHERE `bd_id` = '".$getold_discounts->bd_id."'");
                    }
                }
                $wpdb->query("INSERT INTO `wp_billingdiscount`(`bd_dcid`, `bd_dcname`, `bd_dcprice`, `bd_price`, `bd_assign`, `bd_status`) VALUES ('".$assignthis_discount_from_agency."', '".$getpackage->dc_name."', '', '".$getpackage->dc_cost."', '".date("Y-m-d h:i:s")."', 'expire')");
            }else{
                $wpdb->query("UPDATE `wp_billingdiscount` SET `bd_status` = 'expire' WHERE `bd_dcname` = '".$getpackage->dc_name."'");
            }
        }else{
            $assignthis_discount_from_agency = assignthis_discount_from_agency($getpackage->dc_id, $getpackage->dc_name);
        }
        
        
        unset($_SESSION['packagetype']);
        unset($_SESSION['packageid']);
        unset($_SESSION['packageprice']);
        
        $billing_cancelon = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'");
        if(!empty($billing_cancelon)){
            $stop_billing_query = "DELETE FROM `wp_usermeta` WHERE `user_id` = '1' AND `meta_key` = 'billing_cancel_date'";
            $wpdb->query($stop_billing_query);
        }

        header('refresh: 2; url='.$current_page); //Redirect after Payment Successfully
    } else {
        echo "<div class='payment_response_message'>";
        echo $text_message = $message_text;
        echo "</div>";
    }
    
    
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

<?php
$current_year = date("Y");
$last_year = $current_year + 20;
$current_month = date("m");
?>

<form class="bill_form" method="post" action="">	
    <div class="col-md-7">
        <div class="bill_sec pay_div">
            <h5>Payment Information</h5>
            <?php
            if(($_SESSION["packagetype"] == 'discount_coupon') && isset($_SESSION["packageprice"]) && ($_SESSION["packageprice"] == 0)){
                //echo "Yes, Zero balance Discount Code.";
            }else{ ?>
                <ul>
                    <!--<li class="col-xs-12 col-sm-12 col-md-12"> <label>Card Holder Name</label> <input type="text"/> </li>-->
                    <li class="col-xs-12 col-sm-12 col-md-12"> <label>Credit Card Number</label> <input required type="text" name="creditCardCardNumber" value="" type="text"/> <img src="<?php echo SET_COUNT_PLUGIN_URL;?>/assets/images/pay_card.png"/> </li>
                    <li class="col-xs-12 col-sm-7 col-md-7"> <label>Expiration Date</label> 
                        <div class="col-xs-6 col-sm-6 col-md-6 month_input">
                        <label>Month</label> 
                        <select required type="text" name="creditCardExpirationMonth" value=""> 
                            <?php
                            for($i=1;$i<=12;$i++){
                                if($current_month == $i){
                                    $selected = 'selected';
                                }else{
                                    $selected = '';
                                }
                                echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select> 
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 month_input">
                         <label>year</label>     
                        <select required type="text" name="creditCardExpirationYear" value=""> 
                            <?php
                            for($i=$current_year;$i<=$last_year;$i++){
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                        </div>
                    </li>
                    <li class="col-xs-12 col-sm-5 col-md-5"> <label class="hid_txt">1</label> <label>Security Code</label> <input required type="text" name="creditCardCardCode" value="" type="text" placeholder="CVV"/> <a data-toggle="modal" data-target="#myModal" href="#">what's this?</a></li>
                    <div class="clear"></div>
                </ul>
            <?php } ?>
            
            <div class="clear"></div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="bill_sec plan_div">
            <h5>Plan Information</h5>
            <div class="hd_text">
                <span>Plan</span>
                <h3><?php echo $getpackage->dc_name;?></h3>
                <p href="" class="change_package">Change</p>
            </div>
            <ul>
                <li><?php echo $lp_locations;?> Locations (each aditional $<?php echo $lp_location_price;?>pm)</li>
                <li>Unlimited User</li>
                <li><?php echo $key_field_limit;?> Keywords</li>
                <li><?php echo $ckey_field_limit;?> Competitor Keywords</li>
                <li><?php echo $audit_field_limit;?> Site Audits Per Month</li>
                <li><?php echo $citation_field_limit;?> Citation Reports Per Month</li>
                <li> 
                    <?php 
                        if($readonly == 'readonly'){ ?>
                            <label>Applied Coupon</label> <input style="background: #eff3f8;" id="assign_discount_code" name="assign_discount_code" <?php echo $readonly;?> value="<?php echo $discount_name;?>" type="text"/><p id="update_availability" class="check_availability">Reset</p>
                        <?php }else{ ?>
                            <label>I have a coupon</label> <input id="assign_discount_code" name="assign_discount_code" <?php echo $readonly;?> value="<?php echo $discount_name;?>" type="text"/><p id="check_availability" class="check_availability">Update</p>
                        <?php }
                    ?>
                     
                </li>
                <div class="clear"></div>
            </ul>
            <div class="clear"></div>
            <div class="total_div">
                <span>Total Plan Changes</span>
                <strong>$<?php echo $lp_price;?> per month</strong>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="col-md-12 bill_sec detail_div">
            <h5>Billing Information</h5>
            <ul>
                <div class="row">
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>First Name</label> <input required type="text" name="billToFirstName" value="<?php echo $f_name;?>" type="text"/> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>Last Name</label> <input required type="text" name="billToLastName" value="<?php echo $l_name;?>" type="text"/> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>E-mail Address</label> <input required type="email" name="customerEmail" value="<?php echo $user_email;?>" type="email"/> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>Company Name</label> <input required type="text" name="billToCompany" value="<?php echo $brand_name;?>" type="text"/> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>Phone Number</label> <input required type="text" name="customerPhoneNumber" value="<?php echo $phone;?>" type="text"/> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>Address</label> <input required type="text" name="billToAddress" value="<?php echo $address;?>" type="text"/> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>Country</label> <select required type="text" class="billToCountry" name="billToCountry"> 
                            <option value="">Select Country</option>
                            <?php echo $countryoptions;?>
                        </select>  
                    </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>State</label> <select id="state-list" name="billToState"> <option value="<?php echo $state;?>">select your state</option> </select> </li>
                    <li class="col-xs-12 col-sm-4 col-md-4"> <label>Zip</label> <input required type="text" name="billToZip" value="<?php echo $zip;?>" type="text"/> </li>
                </div>
                <div class="clear"></div>
            </ul>

            <div class="clear"></div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="btn_div">
            <!--<a href="#">Submit</a>-->
            <?php 
            if(($_SESSION["packagetype"] == 'discount_coupon') && isset($_SESSION["packageprice"]) && ($_SESSION["packageprice"] == 0)){ ?>
                <input type="submit" id="location_payment_submit" name="location_payment_submit" value="Save">
            <?php }else{ ?>
                <input type="submit" id="location_payment_submit" name="location_payment_submit" value="Pay Now">
            <?php } ?>
            
            <!--<a href="#">Reset</a>-->
            <button type="reset" class="reset_button" value="Reset">Reset</button>
        </div>
    </div>

</form>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">VISA/MASTERCARD</h4>
      </div>
      <div class="modal-body" style="text-align: center;">
        <p>A 3-digit number in reverse italics on the <strong>back</strong> of your credit card.</p>
        <img src="<?php echo site_url();?>/wp-content/plugins/settings/assets/images/cvv-visa.gif">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<form style="display: none;" name="discount_coupon_form" id="discount_coupon_form" action="" method="post">
    <input name="discount_coupon" value="discount_coupon" type="hidden">
    <input name="discount_dcid" value="" id="discount_price" type="hidden">
    <!--<input name="discount_price" value="" id="discount_price" type="hidden">-->
    <input type="submit" id="discount_coupon_active" name="discount_coupon_active">
</form>
<form style="display: none;" name="reset_coupon_form" id="discount_coupon_form" action="" method="post">
    <input type="submit" id="discount_coupon_reset" name="discount_coupon_reset">
</form>

<form style="display: none;" method="post" action="" class="distroy_session" style="float:right;">
    <input type="submit" id="distroy_session" name="distroy_session" value="Back">
</form>
<?php $state = get_user_meta($admin_id, 'state', true);?>
<script>
jQuery(document).ready(function(){
    jQuery(".change_package").click(function(){
        jQuery('#distroy_session').trigger('click');
    });
    
    jQuery("#update_availability").click(function(){
        //alert("hello"); return false;
        jQuery('#discount_coupon_reset').trigger('click');
        //location.reload();
    });
});
</script>

<script>
jQuery(document).ready(function(){
    jQuery('select.billToCountry').on('change', function() {
      //alert( this.value );
      
      var valcountry = this.value;
        
	jQuery.ajax({
	type: "POST",
	url: "../../../../get_state.php",
	//data:'country_id='+val,
        //data:'state_id='+valstate,
        data: {country_id: valcountry},
        //data:'state_id='+valstate,
	success: function(data){
            //alert(data);
		jQuery("#state-list").html(data);
	}
	});
    })
    
    var selected_country = jQuery('select.billToCountry').val();
    if(selected_country != ''){
        var state = "<?php echo get_user_meta($admin_id, 'state', true);?>";
        //alert(state);
        jQuery.ajax({
	type: "POST",
	url: "../../../../get_state.php",
	//data:'country_id='+val,
        //data:'state_id='+valstate,
        data: {country_id: selected_country, state_id: state},
        //data:'state_id='+valstate,
	success: function(data){
            //alert(data);
		jQuery("#state-list").html(data);
	}
	});
    }
    
});
</script>
<script>
jQuery(document).ready(function(){ 
    jQuery("#check_availability").click(function(){
        var getdc_name = jQuery('#assign_discount_code').val();

        var url = ajaxurl+"?param=checkcode&action=settings_lib";
        
        //alert(coderesponse);
        if(getdc_name != ''){
            
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: {discountcodename : getdc_name},
                success: function(data) {
                    //alert(data);
                    var resp = jQuery.parseJSON(data);
                    var codedatas = resp.codedata;
                    var coderesponses = resp.coderesponse;
                    var codereturns = resp.codereturn;
                    //alert(codereturns);
                    if(codereturns=='run'){
                        //alert(getdc_name);
                        jQuery('#discount_price').val(getdc_name);
                        jQuery('#discount_coupon_active').trigger('click');
                    }else{
                        alert(coderesponses);
                        return false;
                    }
                },
                error: function(e) {
                    var coderesponses = 'Discount Code not to be Added.';
                    var codereturns = 'notrun';
                    alert(coderesponses);
                    return false;
                }
              });
        }else{
            alert('Insert Valid Discount Code.');
            return false;
        }
    });
    


//alert(coderesponse);
//alert(codereturn);
//alert(codedata);
});
</script>








