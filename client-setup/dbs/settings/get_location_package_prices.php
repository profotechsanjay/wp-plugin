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


$get_addons_for_location = get_addons_for_location();

foreach($get_addons_for_location as $get_addons_for_locations){
    //pr($get_addons_for_locations);
    $field_slug = $get_addons_for_locations->addons_slug;
    $field_cost = $get_addons_for_locations->addons_cost;
    $field_value = $get_addons_for_locations->addons_value;
    
    if($field_slug == 'keywords'){
        $locations_package_prices->lp_key_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_key_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'comp_keywords'){
        $locations_package_prices->lp_ckey_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_ckey_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'keyword_opp'){
        $locations_package_prices->lp_keyo_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_keyo_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'pages'){
        $locations_package_prices->lp_page_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_page_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'site_audit'){
        $locations_package_prices->lp_audit_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_audit_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'citation_run'){
        $locations_package_prices->lp_citation_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_citation_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'demo_accounts'){
        $locations_package_prices->lp_demoaccounts_price = $get_addons_for_locations->addons_cost;
        $locations_package_prices->lp_demoaccounts_range = $get_addons_for_locations->addons_value;
    }elseif($field_slug == 'location'){
        $locations_package_prices->lp_location_price = $get_addons_for_locations->addons_cost;
        $location_limit = 1;
        $location_fields = unserialize($get_addons_for_locations->addons_value);
        $location_keywords = $location_fields['location_keywords'];
        $location_ckey = $location_fields['location_ckey'];
        $location_keyopp = $location_fields['location_keyopp'];
        $location_pages = $location_fields['location_pages'];
        $location_audit = $location_fields['location_audit'];
        $location_citation = $location_fields['location_citation'];
        $location_demoaccounts = $location_fields['location_demoaccounts'];
    }
}
$locations_package_prices->lp_duration_unit = 1;
$locations_package_prices->lp_duration_range = 'months';

//pr($locations_package_prices);

?>
