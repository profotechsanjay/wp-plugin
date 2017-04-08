<?php
global $wpdb;
$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;

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
$msg = '';
global $current_user;
$UserID = $location->MCCUserId;
$CURENT_ID = $UserID;

$location_web = get_user_meta($UserID,'website',TRUE);
$location_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);

$base_url = site_url();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['website'])) {

    if (isset($_POST['adwords-pull'])) {
        $meta = get_user_meta($UserID, "adwords-pull");

        if (empty($meta))
            add_user_meta($UserID, "adwords-pull", $_POST['adwords-pull']);
        else
            update_user_meta($UserID, "adwords-pull", $_POST['adwords-pull']);
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
    }
    unset($_SESSION['msgaddlocation']);
    update_user_meta($UserID, 'client_name', $_POST['client_name']);    
    if ($_POST['company_name'] != '') {
        update_user_meta($UserID, 'business', $_POST['company_name']);
        update_user_meta($UserID, 'company_name', $_POST['company_name']);
        update_user_meta($UserID, 'BRAND_NAME', $_POST['company_name']);        
    }
    update_user_meta($UserID, 'industry', $_POST['industry']);
    update_user_meta($UserID, 'geo_location', $_POST['geo_location']);
    update_user_meta($UserID, 'streetaddress', $_POST['streetaddress']);
    update_user_meta($UserID, 'city', $_POST['city']);
    update_user_meta($UserID, 'state', $_POST['state']);
    update_user_meta($UserID, 'zip', $_POST['zip']);
    update_user_meta($UserID, 'info_email', $_POST['info_email']);
    //update_user_meta($UserID, 'assessment_interested', $_POST['assessment_interested']);
    update_user_meta($UserID, 'info_phone', $_POST['info_phone']);
    update_user_meta($UserID, 'google_account', $encryp_google_account);
    update_user_meta($UserID, 'others_account', $encryp_others_account);

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
    
}

$website = $client_name = $company_name = $industry = $geo_location = $streetaddress = $email = '';
$website = get_user_meta($UserID, "website", true);
$client_name = get_user_meta($UserID, "client_name", true);
$company_name = get_user_meta($UserID, "BRAND_NAME", true);
if ($company_name == '') {
    $company_name = get_user_meta($UserID, "company_name", true);
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
if ($info_email_value == '') {
    $info_email_value = '<input required="true" email="true" type="text" name="info_email[]">';
}
$info_phone_value = '';
if (!empty($info_phone)) {
    foreach ($info_phone as $em) {
        foreach ($em as $a_phone) {
            if (trim($a_phone) != "") {
                $info_phone_value .= '<input class="phone_validate" type="text" name="info_phone[]" value="' . $a_phone . '"><div style="clear:both;height:5px;"></div>';
            }
        }
    }
}
if ($info_phone_value == '') {
    $info_phone_value = '<input type="text" class="phone_validate" name="info_phone[]">';
}

/************* Get Packages from Main enfusen website from API (START) **************/
$packages = '';
$main_webiste = SET_PARENT_URL;
$website_var = parse_url($main_webiste);
$main_web_url = $website_var[scheme]."://".$website_var[host];
$table_name = "wp_billing_info";
$data = array('package_table' => $table_name);

//print_r($data);

$data_string = http_build_query($data);
$url = $main_web_url."/agency_billing_packages_api.php";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



$billing_packages = curl_exec($ch);
$result_stripslash = stripslashes($billing_packages);
$get_packages = json_decode($result_stripslash);

$packages = '<select name="billing-packages">';
foreach($get_packages as $get_package){
    $packages .= '<option value="'.$get_package->billing_info_id.'">'.$get_package->package_name.'</option>';
}
$packages .= '</select>';
/************* Get Packages from Main enfusen website from API (STOP) **************/

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
    #more_phone, #others_acc, #more_email {
        margin-top: 20px;
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
}

$content1 = '<form name="form_url" id="form_url" method="post" action="">';

$content1 .= '
			<table class="keywords" style="width: 100%;">				
                                
                <tr>
					<td><label>Location Website *</label></td>
					<td><input required="true" type="text" ' . $readonly . ' name="website" value="' . $website . '"></td>
				</tr>
               
                <tr>
					<td><label>Brand Name *</label></td>
					<td><input type="hidden" name="hidden_user_id" value="' . $UserID . '">'
        . '                             <input required="true" type="text" name="company_name" value="' . $company_name . '"></td>
				</tr>
                <tr>
					<td><label>Location Head</label></td>
					<td><input type="text" name="client_name" value="' . $client_name . '"></td>
				</tr>
                <tr>
					<td><label>Industry</label></td>
					<td><input type="text" name="industry" value="' . $industry . '"></td>
				</tr>
                <tr>
					<td><label>GEO Location</label></td>
					<td><input type="text" name="geo_location" value="' . $geo_location . '"></td>
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
					<td><input required="true" type="text" name="state" value="' . $state . '"></td>
				</tr>
				
				                <tr>
					<td><label>Location Zip *</label></td>
					<td><input required="true" type="text" name="zip" value="' . $zip . '"></td>
				</tr>
								
                                <tr>
					<td><label>Email *</label></td>
					<td>
                                           <div id="more_email">
                                            ' . $info_email_value . '
                                           </div>
                                            <br/><a href="javascript:void(0);" onclick="add_more_email()"> Add more email</a>
                                        </td>
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
				
                                <tr>
					<td><label>Phone</label></td>
					<td>
                                           <div id="more_phone">
                                            ' . $info_phone_value . '
                                           </div>
                                            <br/><a href="javascript:void(0);" onclick="add_more_phone()"> Add more phone</a>
                                        </td>
				</tr>
                                
                                <tr>
					<td><label>Select Package</label></td>
					<td>
                                           <div id="billing_package">
                                           ' . $packages . '
                                           </div>
                                        </td>
				</tr>
                                
                                <tr>
					<td><label>Others Account</label></td>
					<td><div id="others_acc">' . $others_account_value . '</div>
                                        <br/><a href="javascript:void(0);" onclick="add_more_others_acc()"> Add more others account</a>    
                                        </td>
                                </tr>
                                
				
				<input type="hidden" class="userIDget" value="' . $UserID . '">
                                  <tr>
					<td><label></label></td>
					<td><button type="submit" class="btn" >Submit</button></td>
				</tr>   
                                ';
?>

                <script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/mask.js"></script>    
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $(".phone_validate").mask("(999) 999-9999");
                    });
                    function add_more_email() {
                        jQuery('#more_email').append('<div style="clear:both;height:5px;"></div><input type="text" name="info_email[]">');
                    }
                    function add_more_phone()
                    {
                        jQuery('#more_phone').append('<div style="clear:both;height:5px;"></div><input class="phone_validate" type="text" name="info_phone[]">');
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
