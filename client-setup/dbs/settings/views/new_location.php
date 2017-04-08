<?php


ini_set('display_errors', 'Off');
global $wpdb;
$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;
//pr($_POST); die;
global $billing_enable;

$location = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . client_location() . " WHERE id = %d", $location_id
        )
);
if (empty($location)) {
    ?>
    <div class="update-nag">Invalid Location</div>
    <?php
    die;
}

include_once 'common.php';

//$trial_nooflocations = $locations_package_prices->lp_locations;
//if(empty($trial_nooflocations)){
//    $trial_nooflocations = 5;
//}


$check_condition_forlimit = trial_check_for_agency_limit_message();
//pr($check_condition_forlimit);
if(!empty($check_condition_forlimit)){
    if($check_condition_forlimit == 'subscription'){
        $link = site_url()."/location-settings/?parm=billing_info";
        $limit_message = 'You can not add more locations because limit to add location under current Package is over. So <a href="'.$link.'"><strong>Click here</strong></a> to Purchase Location Add-Ons for add more location. Or Contact with Administrator.';
    }elseif($check_condition_forlimit == 'trial'){
        $link = site_url()."/location-settings/?parm=billing_payment";
        $limit_message = 'Sorry, No more locations add in Trial Account. So <a href="'.$link.'">Click here</a> to start Subscription and increase Limit.';
    }
}

$trial_nooflocations = $wpdb->get_var("SELECT lpf_limit FROM wp_location_package_fields WHERE lpf_field = 'location'");
global $current_user;
$UserID = $location->MCCUserId;
$CURENT_ID = $UserID;

$location_web = get_user_meta($UserID,'website',TRUE);
$location_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);

$base_url = site_url();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['website'])) {

//sets the user role to "enterprise" (from subscriber) it must be that role (or accelerator) for the crons to run properly. The new demo setup form will have options to select the user type.
wp_update_user( array( 'ID' => $UserID, 'role' => 'enterprise' ) );
// user_ROLE is also needed for some scripts, role need to be same as line above
update_user_meta($UserID, "User_ROLE", "enterprise");

 
    if (isset($_POST['adwords-pull'])) {
        $meta = get_user_meta($UserID, "adwords-pull");

        if (empty($meta)){
            add_user_meta($UserID, "adwords-pull", $_POST['adwords-pull']);
            
            $location_row = $wpdb->get_row("SELECT * FROM `wp_location_package_fields` WHERE `lpf_field` = 'location'");
            //pr($location_row->lpf_used);
            $lpf_used = $location_row->lpf_used;
            $new_lpf_used = $lpf_used + 1;
            
            $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_used` = '".$new_lpf_used."' WHERE `lpf_field` = 'location'");
            
        } else {
            update_user_meta($UserID, "adwords-pull", $_POST['adwords-pull']);
        }
        
    }

    //important
    foreach ($_POST['google_account'] as $index_e_g_acc => $g_acc) {
        $encryp_google_account[$index_e_g_acc]['google_a_name'] = $g_acc['google_a_name'];
        $encryp_google_account[$index_e_g_acc]['google_a_password'] = encode_value($g_acc['google_a_password']);
    }
    foreach ($_POST['bing_account'] as $index_e_g_acc => $g_acc) {
        $encryp_bing_account[$index_e_g_acc]['bing_url'] = $g_acc['bing_url'];
        $encryp_bing_account[$index_e_g_acc]['bing_username'] = encode_value($g_acc['bing_username']);
        $encryp_bing_account[$index_e_g_acc]['bing_password'] = encode_value($g_acc['bing_password']);
    }
    foreach ($_POST['others_account'] as $index_e_g_acc => $g_acc) {
        $encryp_others_account[$index_e_g_acc]['others_name'] = $g_acc['others_name'];
        $encryp_others_account[$index_e_g_acc]['others_url'] = $g_acc['others_url'];
        $encryp_others_account[$index_e_g_acc]['others_username'] = encode_value($g_acc['others_username']);
        $encryp_others_account[$index_e_g_acc]['others_password'] = encode_value($g_acc['others_password']);
    }
    //pr($encryp_others_account); exit;

    update_user_meta($UserID, 'website', $_POST['website']);
    if ($_POST['website'] != '') {
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . client_location() . " SET status = 1 "
                        . "WHERE id = %d", $location_id
                )
        );
                
        unset($_SESSION['msgaddlocation']);
    }
    update_user_meta($UserID, 'client_name', $_POST['client_name']);    
    if ($_POST['company_name'] != '') {
        update_user_meta($UserID, 'business', $_POST['company_name']);
        update_user_meta($UserID, 'company_name', $_POST['company_name']);
        update_user_meta($UserID, 'BRAND_NAME', $_POST['company_name']); 
        update_user_meta($UserID, 'ct_BusinessName', $_POST['company_name']);  
    }
    update_user_meta($UserID, 'country', $_POST['country']);
    update_user_meta($UserID, 'industry', $_POST['industry']);
    update_user_meta($UserID, 'geo_location', $_POST['geo_location']);
    update_user_meta($UserID, 'streetaddress', $_POST['streetaddress']);
    update_user_meta($UserID, 'city', $_POST['city']);
    update_user_meta($UserID, 'state', $_POST['state']);
    update_user_meta($UserID, 'zip', $_POST['zip']);
    update_user_meta($UserID, 'info_email', $_POST['info_email']);
    //update_user_meta($UserID, 'assessment_interested', $_POST['assessment_interested']);
    update_user_meta($UserID, 'info_phone', $_POST['info_phone']);
    update_user_meta($UserID, 'phonenumber', $_POST['info_phone'][0]);
    update_user_meta($UserID, 'google_account', $encryp_google_account);
    update_user_meta($UserID, 'others_account', $encryp_others_account);
    update_user_meta($UserID, 'ct_Keyword', $_POST['ct_Keyword']);   
    $ct_google_location = "$_POST[city], $_POST[state]";
    update_user_meta($UserID, 'ct_GoogleLocation', $ct_google_location);

    $conn = analytic_conn();
    if ($conn != '') {
        $sql = "SELECT count(MCCUserID) as total FROM clients_table WHERE MCCUserID = $UserID";
        $result = mysqli_query($conn, $sql);
        $row = $result->fetch_object();
        $hasadded = $row->total;
        if ($hasadded == 0) {
            $Name = mysqli_real_escape_string($conn, $_POST['company_name']);
            $Domain = mysqli_real_escape_string($conn, $_POST['website']);
            $Email = mysqli_real_escape_string($conn, $_POST['info_email']);

            $sql = "INSERT INTO clients_table(MCCUserID,Name,Domain,Email,CreatedDate) VALUES($UserID,'$Name','$Domain','$Email',NOW())";
            mysqli_query($conn, $sql);
            $ClientID = $UserID;
            $sql = "CREATE TABLE IF NOT EXISTS `short_analytics_$ClientID` (
                `short_analytics_id` int(13) NOT NULL AUTO_INCREMENT,
                `DateOfVisit` varchar(20) NOT NULL,
                `PageURL` varchar(300) NOT NULL,
                `Keyword` varchar(100) NOT NULL,
                `CurrentRank` int(5) NOT NULL,
                `organic` int(5) NOT NULL,
                `social` int(5) NOT NULL,
                `referral` int(5) NOT NULL,
                `(none)` int(5) NOT NULL,
                `cpc` int(5) NOT NULL,
                `email` int(5) NOT NULL,
                `Total` int(10) NOT NULL,
                `TimeOnSite` varchar(10) NOT NULL,
                `BounceRate` varchar(10) NOT NULL,
                `url_type` varchar(32) NOT NULL,
                PRIMARY KEY (`short_analytics_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
            mysqli_query($conn, $sql);

            // create dynamic tables
            include_once dirname(dirname(SET_COUNT_PLUGIN_DIR)) . '/themes/twentytwelve/analytics/DBUtils.php';
            CreateCustomizedTableForNewClient(AnalyticsDataDBTableName, $ClientID);
            CreateCustomizedTableForNewClient(AnalyticsCacheDataDBTableName, $ClientID);
            CreateCustomizedTableForNewClient(ConvTrackingDBTableName, $ClientID);
            CreateCustomizedTableForNewClient(ConvTrackingCacheDBTableName, $ClientID);
            CreateCustomizedTableForNewClient(ConvTrackingFilteredDBTableName, $ClientID);
            CreateCustomizedTableForNewClient(ConvTrackingUrlsDBTableName, $ClientID);
            CreateCustomizedTableForNewClient(ConvTrackJSCodeFileName, $ClientID);
        }
    }
    
    $msg = "Location information has been successfully saved";
    $msg = '<div class="messdv alert alert-success" style=""> <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    '.$msg.'</div>';

    /*Rudra Changed*/
    @session_start();	
    unset($_SESSION['location_varibales']); /*Destroying SESSION values of trial pd*/
    /*Rudra Changed Ends*/
    
}

$website = $client_name = $company_name = $industry = $geo_location = $streetaddress = $email = '';

/*Rudra Changed*/
@session_start();

$value_email='';
$value_phone='';

if(isset($_SESSION['location_varibales'])){
	
        $dataSession = $_SESSION['location_varibales'];
	
	$website = $dataSession['website'];
	$company_name = $dataSession['company_name'];

	$industry = get_user_meta($UserID, "industry", true);
	$geo_location = get_user_meta($UserID, "geo_location", true);
	$streetaddress = $dataSession['streetaddress'];
	$city = $dataSession['city'];
	$zip = $dataSession['zip'];

	$ct_Keyword = $dataSession['ct_Keyword'];

}else{
	    $website = get_user_meta($UserID, "website", true);
		$client_name = get_user_meta($UserID, "client_name", true);
		$company_name = get_user_meta($UserID, "BRAND_NAME", true);
		if ($company_name == '') {
			$company_name = get_user_meta($UserID, "company_name", true);
		}
		if($company_name != "") { 
			$company_name = get_user_meta($UserID, "ct_BusinessName", true);
		}
		$industry = get_user_meta($UserID, "industry", true);
		$geo_location = get_user_meta($UserID, "geo_location", true);
		$streetaddress = get_user_meta($UserID, "streetaddress", true);
		$city = get_user_meta($UserID, "city", true);
		$state = get_user_meta($UserID, "state", true);
		$zip = get_user_meta($UserID, "zip", true);

		$info_email = get_user_meta($UserID, "info_email");
		$info_phone = get_user_meta($UserID, "info_phone");
		$others_account = get_user_meta($UserID, "others_account");


		$meta = get_user_meta($UserID, "adwords-pull", true);
		$mcity = get_user_meta($UserID, "city", true);
		$mzip = get_user_meta($UserID, "zip", true);
		$country = get_user_meta($UserID, "country", true);
		$ct_Keyword = get_user_meta($UserID, "ct_Keyword", true);
}
/*Rudra Changed Ends*/



$info_email_value = '';
if (!empty($info_email)) {
    foreach ($info_email as $em) {
        foreach ($em as $a_email) {
            if (trim($a_email) != "") {
                $info_email_value .= '<input required="true" email="true" type="text" name="info_email[]" value="' . $a_email . '"><div style="clear:both;height:5px;"></div>';
            }
        }
    }
}
/*Rudra Changed*/
if ($info_email_value == '') {
    $info_email_value = '<input required="true" email="true" type="text" name="info_email[]" '.$value_email.'>';
}
/*Rudra Changed Ends*/
$info_phone_value = '';
if (!empty($info_phone)) {
    foreach ($info_phone as $em) {
        $count=0;
        foreach ($em as $a_phone) {
            if (trim($a_phone) != "") {
                $info_phone_value .= '<input class="phone_validate" type="text" name="info_phone[]" required="true" value="' . $a_phone . '"><div style="clear:both;height:5px;"></div>';
            }
            $count++;
        }
    }
}
/*Rudra Changed*/
if ($info_phone_value == '') {
    $count=0;
    $info_phone_value = '<input type="text"  required="true" class="phone_validate" name="info_phone[]" '.$value_phone.'/>';
}
/*Rudra Changed Ends*/




$others_account_value = '';
if (!empty($others_account)) {
    foreach ($others_account as $em) {
        foreach ($em as $inc_others => $b_acc) {
            //print_r($g_acc);
            $others_account_value .='<div>
                                         Name: <input class="small_field" type="text" name="others_account[' . $inc_others . '][others_name]" value="' . $b_acc['others_name'] . '">
                                         <span style="margin-left: 5px;">URL:</span> <input class="small_field" type="text" name="others_account[' . $inc_others . '][others_url]" value="' . $b_acc['others_url'] . '">
                                         <span style="margin-left: 5px;">User:</span> <input class="small_field" type="text" name="others_account[' . $inc_others . '][others_username]" value="' . decode_value($b_acc['others_username']) . '">
                                         <span style="margin-left: 5px;">Pass:</span> <input class="small_field" type="text" name="others_account[' . $inc_others . '][others_password]" value="' . decode_value($b_acc['others_password']) . '">
                                        </div><div style="clear:both;height:4px;"></div>';
        }
    }
} else {
    $inc_others = 0;
    $others_account_value = '<div>
                                         Name: <input class="small_field" type="text" name="others_account[0][others_name]">
                                         <span style="margin-left: 5px;">URL:</span> <input class="small_field" type="text" name="others_account[0][others_url]">
                                         <span style="margin-left: 5px;">User:</span> <input class="small_field" type="text" name="others_account[0][others_username]">
                                         <span style="margin-left: 5px;">Pass:</span> <input class="small_field" type="text" name="others_account[0][others_password]">
                                        </div><div style="clear:both;height:4px;"></div>';
}
?>
<style>
    .small_field{
        width: 150px;
    }    
     input, select {
        width: 400px;
    }
    .term_cond {
        width:20px;
    }    
    
</style>
<?php if($msg != ''): ?>
    <div class="msg"> <?php echo $msg; ?> </div>
<?php endif; ?>
<div class="panel panelmain">
    <div class="col-lg-12 ">
        <div class="contaninerinner">         
            <h4>Location Information - <?php echo $location_name . " ( ".$location_web." )"; ?></h4> 
            <div class="bread_crumb">
                <ul>
                    <li title="Locations">
                        <a href="<?php echo ST_LOC_PAGE; ?>?parm=locations">Locations</a> >>
                    </li>
                    <li title="Location Information">
                        Location Information
                    </li>
                </ul>
            </div>
            <div class="">
<?php
$readonly = '';
if ($website != '') {
    $readonly = 'readonly';
    $typeurl="";
}else{
$readonly="";
$typeurl="url='true' title='Please enter a complete URL to include the http or https' type='url'";
}
//country list for country dropdown
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
        $resultsctr[15][id] = "EST";
        $resultsctr[15][name] = "Estonia";
        
     
        
// get cuntry select options
foreach($resultsctr as $countryitem) {
$selectcheck = "";    
if($country == $countryitem["id"]) { $selectcheck = "selected";}    
$countryoptions .= '<option value="' . $countryitem["id"] . '" ' . $selectcheck . '>'.$countryitem["name"].'</option>';

}

$count_locations_query = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `status` = '1'");
$count_locations = $wpdb->num_rows;

$current_loc_status_query = $wpdb->get_row("SELECT * FROM `wp_client_location` WHERE `id` = '".$_GET['location_id']."'");
//print_r($current_loc_status_query);
$current_loc_status = $current_loc_status_query->status;

$check_lp_all_limits = check_lp_all_limits();
//pr($check_lp_all_limits);
$location_available = $check_lp_all_limits['location_available'];
//$location_available = 0;
//pr($check_lp_all_limits['location_available']);

$locations_rowcount = $wpdb->get_var("SELECT COUNT(*) FROM `wp_client_location` WHERE `status` = '1'");
if($billing_enable == 1){
    
    if(isset($_GET['location_id'])){
        $check_current_location_status = $wpdb->get_var("SELECT `status` FROM `wp_setup_table` WHERE `id` = '".$_GET['location_id']."'");
        if(($check_current_location_status == 1) || ($location_available > 0)){
            $content1 = '<form name="form_url" id="form_url" method="post" action="">';
            $checked = 'checked';
        }else{
            if($check_condition_forlimit == 'subscription'){
                $content1 = '<form name="form_url" id="form_url" method="post" action="" class="paid_locations" onsubmit="return paid_locations(this);">';
                $checked = '';
            }elseif($check_condition_forlimit == 'trial'){
                $content1 = '<form name="form_url" id="form_url" method="post" action="" onsubmit="return trial_locations(this);">';
            }
        }
    }
    
}elseif($billing_enable == 0){
    $content1 = '<form name="form_url" id="form_url" method="post" action="">';
    $checked = 'checked';
}else{
    $content1 = '<form name="form_url" id="form_url" method="post" action="">';
}


/*Rudra Changed*/
$content1 .= '<input type="hidden" value="'.$_GET['location_id'].'" name="location_id"/>
			<table class="keywords" style="width: 100%;">				
                <tr>
			<td style="width:250px"><label>Account Type</label></td>
			<td>
				<select name="account_type">
					<option value="enterprise">Active Campaign</option>							
					<option value="demo">Demo Account</option>
				</select>			
			</td>
		</tr>              
                <tr>
					<td><label>Location Website *</label></td>

<td><input required="true"  ' .$typeurl.' '.$readonly . ' name="website" value="' . $website . '"></td>

					
				</tr>
               
                <tr>
					<td><label>Business Name *</label></td>
					<td><input type="hidden" name="hidden_user_id" value="' . $UserID . '">'
        . '                             <input required="true" type="text" name="company_name" value="' . $company_name . '"><br><small>This should be your business name used for citations.</small></td>
				</tr>
                <tr style="display:none">
					<td><label>Location Head</label></td>
					<td><input type="text" name="client_name" value="' . $client_name . '"></td>
				</tr>
                <tr style="display:none">
					<td><label>Industry</label></td>
					<td><input type="text" name="industry" value="' . $industry . '"></td>
				</tr>
                <tr style="display:none">
					<td><label>GEO Location</label></td>
					<td><input type="text" name="geo_location" value="' . $geo_location . '"></td>
				</tr>
                                <tr>
                                        <td><label>Location Country *</label></td>
                                        <td> 
                                        <select required="true" name="country" id="country-list" class="demoInputBox" onChange="getState(this.value);">
                                        <option value="">Select Country</option>
                                        '.$countryoptions.'
                                        </select>

                                        </td>
                                </tr>
                                <tr>
					<td><label>Location Address  *</label></td>
					<td><input required="true" type="text" name="streetaddress" value="' . $streetaddress . '"></td>
				</tr>
                                <tr>
					<td><label>Location City  *</label></td>
					<td><input required="true" type="text" name="city" value="' . $city . '"></td>
				</tr>
                                <tr>
					<td><label>Location State *</label></td>
					<td>
                                        <div id="state-list"></div>
                                        </td>
                                            
				</tr>
				
				                <tr>
					<td><label>Location Zip *</label></td>
					<td><input required="true" type="text" name="zip" value="' . $zip . '"></td>
				</tr>
                                <tr>
					<td><label>Phone *</label></td>
					<td>
                                           <div id="more_phone">
                                            ' . $info_phone_value . '
                                           </div>
                                            <div style="display:none;"><br/><a href="javascript:void(0);" onclick="add_more_phone()"> Add more phone</a></div>
                                        </td>
				</tr>
								
                                <tr>
					<td><label>Email *</label></td>
					<td>
                                           <div id="more_email">
                                            ' . $info_email_value . '
                                           </div>
                                           <div style="display:none;"><br/><a href="javascript:void(0);" onclick="add_more_email()"> Add more email</a></div>
                                        </td>
				</tr>
                                
                                <tr>
					<td><label>Primary Service *</label></td>
					<td><input required="true" type="text" name="ct_Keyword" value="' . $ct_Keyword . '"><br><small>Will be the primary service keyword for citations.</small></td>
				</tr>
                                
                                <tr>
					<td><label>Targeting</label></td>
					<td>
						<select name="adwords-pull">
							<option value="" ' . (($meta == "") ? "SELECTED" : "") . ' >Select Target</option>
							<option value="national" ' . (($meta == "national") ? "SELECTED" : "") . ' >National</option>
							<option value="local" ' . (($meta == "local") ? "SELECTED" : "") . ' >Local - ' . $mcity . " " . $mzip . '</option>
						</select>
						
					</td>
				</tr>                               
                                
                                
                                
                                <tr style="display:none;">
					<td><label>Others Account</label></td>
					<td><div id="others_acc">' . $others_account_value . '</div>
                                        <br/><a href="javascript:void(0);" onclick="add_more_others_acc()"> Add more others account</a>    
                                        </td>
                                </tr>
                                <tr>
                                    <td><label>Term & Conditions</label></td>
                                    <td><input type="checkbox" '.$checked.' class="term_cond" name="term_cond" required><!--<br>1) Minimun 10 Locations free under minimum Package. <br>
                                    2) After  locations, charge $100 per location and per month.<br>
                                    3) After 5 locations, every new location charge $100 for next month in advance Plus from today to next payment cycle on daily bases.-->
                                    </td>
                                </tr>
				
				<input type="hidden" class="userIDget" value="' . $UserID . '">
                                <tr>
                                        <td><label></label></td>
                                        <td><button type="submit" class="btn" >Submit</button></td>
                                </tr>
                                     
                                ';
/*Rudra Changed Ends*/

?>

                <script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/mask.js"></script>    
                <script type="text/javascript">                    
                    function add_more_email() {
                        jQuery('#more_email').append('<div style="clear:both;height:5px;"></div><input type="text" name="info_email">');
                    }
                    function add_more_phone()
                    {
                        jQuery('#more_phone').append('<div style="clear:both;height:5px;"></div><input class="phone_validate" type="text" name="info_phone" required="true">');
                    }
                    var google_account_inc = '<?php echo $inc; ?>';
                    function add_more_google_acc()
                    {
                        google_account_inc++;
                        var html = '<div>' +
                                'Email: <input type="text" name="google_account[' + google_account_inc + '][google_a_name]">' +
                                '<span style="margin-left: 13px;">Password:</span> <input type="text" name="google_account[' + google_account_inc + '][google_a_password]">' +
                                '</div><div style="clear:both;height:4px;"></div>';
                        jQuery('#google_acc').append(html);
                    }

                    var bing_account_inc = '<?php echo $inc_bing; ?>';
                    function add_more_bing_acc()
                    {
                        bing_account_inc++;
                        var html = '<div>' +
                                'URL: <input type="text" name="bing_account[' + bing_account_inc + '][bing_url]">' +
                                '<span style="margin-left: 8px;">Username:</span> <input type="text" name="bing_account[' + bing_account_inc + '][bing_username]">' +
                                '<span style="margin-left: 8px;">Password:</span> <input type="text" name="bing_account[' + bing_account_inc + '][bing_password]">' +
                                '</div><div style="clear:both;height:4px;"></div>';
                        jQuery('#bing_acc').append(html);
                    }

                    var others_account_inc = '<?php echo $inc_others; ?>';
                    function add_more_others_acc()
                    {
                        others_account_inc++;
                        var html = '<div>' +
                                'Name: <input class="small_field" type="text" name="others_account[' + others_account_inc + '][others_name]">' +
                                '<span style="margin-left: 8px;">URL:</span> <input class="small_field" type="text" name="others_account[' + others_account_inc + '][others_url]">' +
                                '<span style="margin-left: 8px;">User:</span> <input class="small_field" type="text" name="others_account[' + others_account_inc + '][others_username]">' +
                                '<span style="margin-left: 8px;">Pass:</span> <input class="small_field" type="text" name="others_account[' + others_account_inc + '][others_password]">' +
                                '</div><div style="clear:both;height:4px;"></div>';
                        jQuery('#others_acc').append(html);
                    }
                </script>
<?php
$j = 1;

for ($i = 0; $i < 5; $i++) {
    $GetRepuKeywrds = get_user_meta($UserID, "LE_Repu_Keyword_" . $j . "", true);

    $content1 .= '<tr style="display:none">';

    if ($j == 1)
        $content1 .= '<td><label>Insert up to 5 keywords here</label></td>';
    else
        $content1 .= '<td></td>';

    $content1 .='<td>' . $j . ':&nbsp;&nbsp;<input type="text" name="Repukeywords[]" value="' . $GetRepuKeywrds . '"></td>
							  </tr>';

    $j++;
}

$content1 .='</table></form>';
echo $content1;
?>


                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
    <input type="hidden" id="hidnewlocpage" name="hidnewlocpage" value="1" />
    

<div class="container">
  
  <!-- Modal -->
  <div class="modal fade" id="limitalertmessage" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Location Limit</h4>
        </div>
        <div class="modal-body">
          <p><?php echo $limit_message;?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>


    
<script>
/*Rudra Changed*/
jQuery("button.limitalertmessage").hide();

function paid_locations(form) {  
    if(jQuery("#form_url").valid()){
        if(jQuery('.term_cond').is(':checked')){
            //alert("hello");    
//var adurl = "../wp-content/themes/twentytwelve/inc/setlocsession.php";
         
/*ajax request with add_new_location form*/
             var podata = "param=getsession_values&action=settings_lib&location_id="+jQuery("input[name=location_id]").val()+"&actype="+jQuery("input[name=account_type]").val()+"&website="+jQuery("input[name=website]").val()+"&company_name="+jQuery("input[name=company_name]").val()+"&country="+jQuery("input[name=country]").val()+"&streetaddress="+jQuery("input[name=streetaddress]").val()+"&city="+jQuery("input[name=city]").val()+"&state="+jQuery("input[name=state]").val()+"&zip="+jQuery("input[name=zip]").val()+"&ct_Keyword="+jQuery("input[name=ct_Keyword]").val();

           /*ajax request with add_new_location form*/
            jQuery.post(ajaxurl,podata,function(response){

               console.log(response);

            });  
            //alert("New Location not to be added, because maximum limit to add location is over. Purchase Location Add-Ons for add more location.");
            jQuery('#limitalertmessage').modal('show');
            return false;
        }
    }
    
}

function trial_locations(form) {  

    if(jQuery("#form_url").valid()){
        if(jQuery('.term_cond').is(':checked')){
            //alert("hello");        
            var message = "Sorry, No more locations add in Trial Account";
         //var adurl = "../wp-content/themes/twentytwelve/inc/setlocsession.php";
         var podata = "param=getsession_values&action=settings_lib&location_id="+jQuery("input[name=location_id]").vtal()+"&actype="+jQuery("input[name=account_type]").val()+"&website="+jQuery("input[name=website]").val()+"&company_name="+jQuery("input[name=company_name]").val()+"&country="+jQuery("input[name=country]").val()+"&streetaddress="+jQuery("input[name=streetaddress]").val()+"&city="+jQuery("input[name=city]").val()+"&state="+jQuery("input[name=state]").val()+"&zip="+jQuery("input[name=zip]").val()+"&ct_Keyword="+jQuery("input[name=ct_Keyword]").val();
           /*ajax request with add_new_location form*/
            jQuery.post(ajaxurl,podata,function(response){

               console.log(response);

            }); 
            //alert(message);
            jQuery('#limitalertmessage').modal('show');
            return false;
        }
    }
    
}
/*Rudra Changed Ends*/

function getState(val,valstate) {
        
        valstate = "<?php echo "$state"; ?>";
        
	$.ajax({
	type: "POST",
	url: "../wp-content/themes/twentytwelve/inc/get_state.php",
	//data:'country_id='+val,
        data: {country_id: val, state_id: valstate},
        //data:'state_id='+valstate,
	success: function(data){
		$("#state-list").html(data);
	}
	});
}

function selectCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
var strCTR = <?php echo json_encode($country); ?>;
var strST = <?php echo json_encode($state); ?>;
getState(strCTR,strST);

</script>
