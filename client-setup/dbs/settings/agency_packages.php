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



$data_pack = array('location_packages' => 'location_packages');
//pr($data);
$data_pack_string = http_build_query($data_pack);

$url_pack = $main_enfusen_url."/professional_location_packages.php";

$chs = curl_init($url_pack);
curl_setopt($chs, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($chs, CURLOPT_POSTFIELDS, $data_pack_string);
curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);


$pack_result = curl_exec($chs);
//pr($result);
curl_close($chs);

$credential_objects = json_decode($pack_result);
//pr($credential_object);
//pr($credential_object);
$agency_package_price = $credential_objects;
error_reporting(0);
//pr($agency_package_price); die;
?>
