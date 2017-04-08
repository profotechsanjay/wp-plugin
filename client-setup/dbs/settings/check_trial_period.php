<?php
//$dir_path = explode("wp-content",dirname(__FILE__));
//include($dir_path[0]."wp-load.php");

global $wpdb;
$main_webiste = SET_PARENT_URL;

$website_var = parse_url($main_webiste);

$base_url = site_url();
$database_name = $wpdb->dbname;
$main_enfusen_url = $website_var[scheme]."://".$website_var[host];

$check_payment_done = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` LIMIT 1");
$payment_done_count = $wpdb->num_rows;
$check_status = $check_payment_done->status;

//if(($payment_done_count > 0) && ($check_status == 'paid')){
if($payment_done_count > 0){
    $trial = 'not-active';
}else{
    $trial = 'active';
}


if($trial == 'active'){
    $data = array('get_trial_code' => 'get_trial_code', 'database_name' => $database_name, 'trial_status' => $trial);
    //pr($data);
    $data_string = http_build_query($data);

    $url = $main_enfusen_url."/location_packages_for_agency.php";

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
    $trial_package = $credential_object;
    //error_reporting(0);
   
    $locations = $trial_package->locations;
    $keywords = $trial_package->keywords;
    $comp_key = $trial_package->comp_key;
    $key_opp = $trial_package->key_opp;
    $pages = $trial_package->pages;
    $siteaudit = $trial_package->siteaudit;
    $citation = $trial_package->citation;
    $status = $trial_package->status;
   
    $getlimit_fornext = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
    $getlimitcount = $wpdb->num_rows;
    if(!empty($getlimit_fornext)){
        if($status == 'active'){
            foreach($getlimit_fornext as $getlimit_fornexts){
                $fields_name = $getlimit_fornexts->lpf_field;
                if($fields_name == 'keywords'){
                    $check_keywords = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_keywords > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$keywords."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'"); 
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('keywords', '".$keywords."', '0', '0', '0')");
                    }   
                }elseif($fields_name == 'comp_keywords'){
                    $check_comp_keywords = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_comp_keywords > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$comp_key."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'"); 
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('comp_keywords', '".$comp_key."', '0', '0', '0')");
                    }
                }elseif($fields_name == 'keyword_opp'){
                    $check_keyword_opp = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_keyword_opp > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$key_opp."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('keyword_opp', '".$key_opp."', '0', '0', '0')");
                    }
                }elseif($fields_name == 'pages'){
                    $check_pages = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_pages > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$pages."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('pages', '".$pages."', '0', '0', '0')");
                    }
                }elseif($fields_name == 'site_audit'){
                    $check_site_audit = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_site_audit > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$siteaudit."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('site_audit', '".$siteaudit."', '0', '0', '0')");
                    }
                }elseif($fields_name == 'citation_run'){
                    $check_citation_run = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_citation_run > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$citation."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('citation_run', '".$citation."', '0', '0', '0')");
                    }
                }elseif($fields_name == 'location'){
                    $check_location = $wpdb->get_var("SELECT COUNT(*) FROM `wp_location_package_fields` WHERE `lpf_field` = '".$fields_name."'");
                    if($check_location > 0){
                       $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$locations."' WHERE `lpf_id` = '".$getlimit_fornexts->lpf_id."' AND `lpf_field` = '".$getlimit_fornexts->lpf_field."'");
                    }else{
                       $wpdb->query("INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES('location', '".$locations."', '0', '0', '0')");
                    }
                }
            }
        }
        
    }else{
        $limit_insert_query = "INSERT INTO `wp_location_package_fields` (`lpf_field`, `lpf_limit`, `lpf_addons_add`, `lpf_addons_delete`, `lpf_used`) VALUES
                        ('keywords', '".$keywords."', '0', '0', '0'),
                        ('comp_keywords', '".$comp_key."', '0', '0', '0'),
                        ('keyword_opp', '".$key_opp."', '0', '0', '0'),
                        ('pages', '".$pages."', '0', '0', '0'),
                        ('site_audit', '".$siteaudit."', '0', '0', '0'),
                        ('citation_run', '".$citation."', '0', '0', '0'),
                        ('location', '".$locations."', '0', '0', '0')";

        $limit_insert = $wpdb->query($limit_insert_query);
    }
}else{
    $trial_package->status = 'not-active';
}

?>