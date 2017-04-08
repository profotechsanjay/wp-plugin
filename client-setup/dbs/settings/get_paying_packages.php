<?php
$dir_path = explode("wp-content",dirname(__FILE__));
//print_r($dir_path[0]);

include($dir_path[0]."wp-load.php");
global $wpdb;

$main_webiste = SET_PARENT_URL;

$website_var = parse_url($main_webiste);

$base_url = site_url();
$database_name = $wpdb->dbname;
$main_enfusen_url = $website_var[scheme]."://".$website_var[host];

//$packageid = 2;
//$packagetype = 'agency_coupon';

$data = array('agency_package_prices' => 'agency_package_prices', 'packageid' => $packageid, 'packagetype' => $packagetype);
//pr($data);
$data_string = http_build_query($data);

//$url = $main_enfusen_url."/location_packages_for_agency.php";
$url = $main_enfusen_url."/paying_agency_packages.php";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$result = curl_exec($ch);
//pr($result);
curl_close($ch);

$credential_object = json_decode($result);
//pr($credential_object);
//pr($credential_object);
$getpackage = $credential_object;
error_reporting(0);
//pr($getpackage); die;
?>
