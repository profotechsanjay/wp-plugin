<?php 
require_once("../../../wp-load.php");
print_r('Test'); die;
include_once 'custom_functions.php';
include_once ABSPATH . "/global_config.php";

global $wpdb;

$discountname = $_POST['discountcodename'];
print_r($discountname);
//print_r(applied_discountcode_data($discountname));

echo json_encode(applied_discountcode_data($discountname));

?>
