<?php

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/global_config.php';
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;
$content_recommend = 'wp_content_recommend';
$content_recommend_hisory = 'wp_content_recommend_hisory';


$result = $wpdb->get_results
(
    $wpdb->prepare
            (
            "SELECT * FROM wp_content_recommend WHERE result is not null",""
    )
);

foreach ($result as $res){
    
}

