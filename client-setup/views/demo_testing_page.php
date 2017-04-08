<?php
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
include dirname(__file__).'/../../../themes/twentytwelve/analytics/BrightLocalUtils_test.php';
 /*$ReportID='256683';
 $report_run_PostFields['campaign-id'] = $ReportID;
 $update_campaign = GetBTLInfoUsingCURL('lsrc/get', $report_run_PostFields);
 pr($update_campaign['location_id'];*/

$location_id='512907';
$location_URL='api/v1/clients-and-locations/locations/'.$location_id;
$location_data=btl_result_by_get($location_URL);
pr($location_data);
echo '--';
//$location_client_id=$location_data->location->client-id);
//$client_URL='api/v1/clients-and-locations/clients/'.$location_client_id;
//$client_data=btl_result_by_get($client_URL);
//pr($client_data);


die;
//die('------------');
/*global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
// Code starts by Rudra 24-jan-2017
include dirname(__file__).'/../../../themes/twentytwelve/analytics/BrightLocalUtils_test.php';
$setup_id='';
$setup = $wpdb->get_row
(
        $wpdb->prepare
        (
                "SELECT * FROM ". setup_table() . " WHERE id = %d",
                $setup_id
        )
);
if(empty($setup)){
    json(0,'Invalid Setup ID');
}
$report_run_PostFields=array();
$dir = $setup->dir;
$analytic_dir = $setup->analytic_dir;
$db_host = DB_HOST;
$db_name = $setup->db_name;
$db_user = $setup->db_username;
$analytic_db = $setup->analytic_db;
$grader_db = $setup->grader_db;
prevent_medstar($db_name);
// Code starts by Rudra 24-jan-2017
$setup_clients= $wpdb->get_results("SELECT * FROM $analytic_db.clients_table");
if(isset($setup_clients) && !empty($setup_clients)){
foreach ($setup_clients as $key => $setup_client) {
  //$location_id=$setup_client->BTLChildActId;
//  $location_id='256683';
  //$URL='api/v1/clients-and-locations/locations/'.$location_id;
    $ReportID='680687';
    $report_run_PostFields['campaign-id'] = $ReportID;


$update_campaign = GetBTLInfoUsingCURL('lsrc/get', $report_run_PostFields);
pr($update_campaign);die;
  }
}
die('----------------');*/
?>
