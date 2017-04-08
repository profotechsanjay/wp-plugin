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
    
    $data = array('arb_credential' => 'getarb_credential');
    
    $data_string = http_build_query($data);
    
    $url = $main_enfusen_url."/agency_fetch_arb_credential.php";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                               
    

    
    $result = curl_exec($ch);
    curl_close($ch);
    $credential_object = json_decode($result);
    /*
    echo "<pre>";
    print_r($credential_object);
    */
    //$authnet_login = $credential_object->gateway_username;   
    //$authnet_transkey = $credential_object->gateway_tran_key;
    
    $authnet_login = isset($credential_object->gateway_username)?$credential_object->gateway_username:"23wV5GDz6k";    
    $authnet_transkey = isset($credential_object->gateway_tran_key)?$credential_object->gateway_tran_key:"226VMjhA8a3B32xG";
    
    /*********************************************/

    define('AUTHNET_LOGIN', $authnet_login);
    define('AUTHNET_TRANSKEY', $authnet_transkey);

    if (!function_exists('curl_init'))
    {
        throw new Exception('CURL PHP extension not installed');
    }

    if (!function_exists('simplexml_load_file'))
    {
        throw new Exception('SimpleXML PHP extension not installed');
    }

?>