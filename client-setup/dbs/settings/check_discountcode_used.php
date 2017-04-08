<?php
//$dir_path = explode("wp-content",dirname(__FILE__));
//include($dir_path[0]."wp-load.php");

global $wpdb;
$main_webiste = SET_PARENT_URL;

$website_var = parse_url($main_webiste);

$base_url = site_url();
$database_name = $wpdb->dbname;
$main_enfusen_url = $website_var[scheme]."://".$website_var[host];

$check_payment_done = $wpdb->get_row("SELECT * FROM `wp_package_used` ORDER BY `wpu_id` DESC LIMIT 1");

if(!empty($check_payment_done)){
    $discount_code_used_id = $check_payment_done->wpu_id;
    $discount_code_used_data = unserialize($check_payment_done->wpu_data);
    //pr($check_payment_done);

    //pr($discount_code_used_data);
    $discountcode_name = $discount_code_used_data->dc_name;
    $discountcode_id = $discount_code_used_data->dc_id;
    
    $data = array('get_used_discount_code_data' => 'get_used_discount_code_data', 'discountcode_id' => $discountcode_id);
    //pr($data);
    $data_string = http_build_query($data);

    $url = $main_enfusen_url."/location_packages_for_agency.php";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    $result_discountcode = curl_exec($ch);
    //pr($result);
    curl_close($ch);
    //pr($result_discountcode);
    $discountcode_object = json_decode($result_discountcode);
    $discountcode_object->dc_location_price = $discount_code_used_data->dc_location_price;
    $discountcode_object->dc_duration_unit = $discount_code_used_data->dc_duration_unit;
    $discountcode_object->dc_duration_range = $discount_code_used_data->dc_duration_range;
    $discountcode_object->package_type = $discount_code_used_data->package_type;
    
    //pr($discountcode_object);
    
    $new_dc_location = $discountcode_object->dc_location;
    $new_dc_keywords = $discountcode_object->dc_keywords;
    $new_dc_comp_key = $discountcode_object->dc_comp_key;
    $new_dc_key_opp = $discountcode_object->dc_key_opp;
    $new_dc_pages = $discountcode_object->dc_pages;
    $new_dc_siteaudit = $discountcode_object->dc_siteaudit;
    $new_dc_citation = $discountcode_object->dc_citation;
    $new_dc_demoaccounts = $discountcode_object->dc_demoaccounts;
    //pr($discountcode_object);
    $wpu_newdata = serialize($discountcode_object);
    
    //$wpdb->query("UPDATE `wp_package_used` SET `wpu_data`='".$wpu_newdata."' WHERE `wpu_id`='".$discount_code_used_id."'");
    
    $get_all_fields = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
    if(!empty($get_all_fields)){
        foreach($get_all_fields as $get_all_field){
            $field_name = $get_all_field->lpf_field;
            $field_id = $get_all_field->lpf_id;
            
            if($field_name == 'keywords'){
                $new_limit = $new_dc_keywords;
                if(empty($new_dc_keywords)){
                    $new_limit = 500;
                }
            }elseif($field_name == 'comp_keywords'){
                $new_limit = $new_dc_comp_key;
                if(empty($new_dc_comp_key)){
                    $new_limit = 1000;
                }
            }elseif($field_name == 'keyword_opp'){
                $new_limit = $new_dc_key_opp;
                if(empty($new_dc_key_opp)){
                    $new_limit = 1250;
                }
            }elseif($field_name == 'pages'){
                $new_limit = $new_dc_pages;
                if(empty($new_dc_pages)){
                    $new_limit = 5000;
                }
            }elseif($field_name == 'site_audit'){
                $new_limit = $new_dc_siteaudit;
                if(empty($new_dc_siteaudit)){
                    $new_limit = 5;
                }
            }elseif($field_name == 'citation_run'){
                $new_limit = $new_dc_citation;
                if(empty($new_dc_citation)){
                    $new_limit = 5;
                }
            }elseif($field_name == 'location'){
                $new_limit = $new_dc_location;
                if(empty($new_dc_location)){
                    $new_limit = 5;
                }
            }elseif($field_name == 'demo_accounts'){
                $new_limit = $new_dc_demoaccounts;
                if(empty($new_dc_demoaccounts)){
                    $new_limit = 2;
                }
            }
            
            $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$new_limit."' WHERE `lpf_id` = '".$field_id."' AND `lpf_field` = '".$field_name."'");
            
        }
    }
    
}

if(!empty($check_payment_done)){
    $data_location = array('get_location_addons_data' => 'get_location_addons_data');
    //pr($data);
    $data_location_string = http_build_query($data_location);

    $mainurl = $main_enfusen_url."/location_packages_for_agency.php";

    $mainch = curl_init($mainurl);
    curl_setopt($mainch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($mainch, CURLOPT_POSTFIELDS, $data_location_string);
    curl_setopt($mainch, CURLOPT_RETURNTRANSFER, true);


    $location_addons = curl_exec($mainch);
    
    curl_close($mainch);
    $location_addons_object = unserialize(json_decode($location_addons)->addons_value);
    //pr($location_addons_object);
    $addon_location_location = 1;
    $addon_location_keywords = $location_addons_object['location_keywords'];
    $addon_location_ckey = $location_addons_object['location_ckey'];
    $addon_location_keyopp = $location_addons_object['location_keyopp'];
    $addon_location_pages = $location_addons_object['location_pages'];
    $addon_location_audit = $location_addons_object['location_audit'];
    $addon_location_citation = $location_addons_object['location_citation'];
    $addon_location_demoaccounts = $location_addons_object['location_demoaccounts'];
    
    $get_agency_fields = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
    if(!empty($get_agency_fields)){
        foreach($get_agency_fields as $get_agency_field){
            $fields_name = $get_agency_field->lpf_field;
            $fields_id = $get_agency_field->lpf_id;
            
            if($fields_name == 'keywords'){
                $addon_limit = $addon_location_keywords;
            }elseif($fields_name == 'comp_keywords'){
                $addon_limit = $addon_location_ckey;
            }elseif($fields_name == 'keyword_opp'){
                $addon_limit = $addon_location_keyopp;
            }elseif($fields_name == 'pages'){
                $addon_limit = $addon_location_pages;
            }elseif($fields_name == 'site_audit'){
                $addon_limit = $addon_location_audit;
            }elseif($fields_name == 'citation_run'){
                $addon_limit = $addon_location_citation;
            }elseif($fields_name == 'location'){
                $addon_limit = $addon_location_location;
            }elseif($fields_name == 'demo_accounts'){
                $addon_limit = $addon_location_demoaccounts;
            }
            
            $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_location_addon_field` = '".$addon_limit."' WHERE `lpf_id` = '".$fields_id."' AND `lpf_field` = '".$fields_name."'");
            
        }
    }
}

?>