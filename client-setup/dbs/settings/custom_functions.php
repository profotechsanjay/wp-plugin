<?php

/* * * Functions by Rudra Innovatives Software ** */
error_reporting(0);
function getusermetabyid($id) {
    global $wpdb;
    $meta_table = $wpdb->prefix . "usermeta";
    $res = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT meta_value FROM $meta_table WHERE umeta_id = %d", $id
            )
    );
    if (empty($res)) {
        return '';
    }
    return $res->meta_value;
}

function getusermeta($user_id, $loc_id, $key) {
    
    global $wpdb;
    $meta_table = $wpdb->prefix . "usermeta";
    $txt = "user_id = $user_id AND ";
    if($user_id == 0){
       $txt = ""; 
    }
    
    $res = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT meta_value FROM $meta_table WHERE $txt location_id = %d AND meta_key = %s", $loc_id, $key
        )
    );
    
    if (empty($res)) {
        return '';
    }
    $data = $res->meta_value;
    if(is_serialized($data)){
        $data = unserialize($data);
    }    
    return $data;
}

function addusermeta($user_id, $loc_id, $key, $value) {

    global $wpdb;
    $meta_table = $wpdb->prefix . "usermeta";
    if (is_array($value) == TRUE || is_object($value)) {
        $value = serialize($value);
    }
    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "INSERT INTO $meta_table (user_id, meta_key, meta_value, location_id) VALUES(%d, %s, %s, %d)", $user_id, $key, $value, $loc_id
            )
    );
}

function updateusermetabyid($id, $value) {
    global $wpdb;
    $meta_table = $wpdb->prefix . "usermeta";

    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "UPDATE $meta_table meta_value = %s WHERE umeta_id = %d", $value, $id
            )
    );
}

function updateusermeta($user_id, $loc_id, $key, $value) {
    global $wpdb;

    $meta_table = $wpdb->prefix . "usermeta";
    if (is_array($value) == TRUE || is_object($value)) {
        $value = serialize($value);
    }

    $vl = getusermeta($user_id, $loc_id, $key);

    if (empty($vl)) {
        addusermeta($user_id, $loc_id, $key, $value);
    } else {

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE $meta_table SET meta_value = %s WHERE user_id = %d AND meta_key = %s AND location_id = %d", $value, $user_id, $key, $loc_id
                )
        );
    }
}

function allactivekeywords($user_id, $location_id, $synonyms = 0) {

    $active_key = array();
    $all_keyword_list = getusermeta($user_id, $location_id, "Content_keyword_Site");
    $Synonyms_keyword_arr = $all_keyword_list['Synonyms_keyword'];
    $all_Synonyms_keyword_arr = array();

    for ($i = 1; $i <= $all_keyword_list['keyword_count']; $i++) {

        if (trim($all_keyword_list['LE_Repu_Keyword_' . $i]) != "") {

            if (isset($all_keyword_list['activation'][$i - 1])) {

                if ($all_keyword_list['activation'][$i - 1] != 'inactive') {

                    $active_key[] = trim($all_keyword_list['LE_Repu_Keyword_' . $i]);
                    if ($synonyms == 1) {
                        $Synonyms_keyword = $Synonyms_keyword_arr[$i - 1];
                        if (!empty($Synonyms_keyword)) {
                            $Synonyms_keyword = array_map('trim', $Synonyms_keyword);
                            $all_Synonyms_keyword_arr = array_merge($all_Synonyms_keyword_arr, $Synonyms_keyword);
                        }
                    }
                }
            } else {
                $active_key[] = trim($all_keyword_list['LE_Repu_Keyword_' . $i]);
                if ($synonyms == 1) {
                    $Synonyms_keyword = $Synonyms_keyword_arr[$i - 1];
                    if (!empty($Synonyms_keyword)) {
                        $Synonyms_keyword = array_map('trim', $Synonyms_keyword);
                        $all_Synonyms_keyword_arr = array_merge($all_Synonyms_keyword_arr, $Synonyms_keyword);
                    }
                }
            }
        }
    }
    if (!empty($all_Synonyms_keyword_arr)) {
        $active_key = array_merge($active_key, $all_Synonyms_keyword_arr);
    }
    $active_key = array_map('strtolower', $active_key);
    $active_key = array_filter($active_key);
    $active_key = array_unique($active_key);
    natcasesort($active_key);
    return $active_key;
}

function get_admins() {
    //Grab wp DB
    global $wpdb;
    //Get all users in the DB
    $wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY ID");

    //Blank array
    $admins = array();
    //Loop through all users
    foreach ($wp_user_search as $userid) {
        //Current user ID we are looping through
        $curID = $userid->ID;
        //Grab the user info of current ID
        $curuser = get_userdata($curID);
        //Current user level
        $user_level = $curuser->user_level;
        //Only look for admins
        if ($user_level >= 8) {
            //Push user ID into array
            array_push($admins, $userid);
        }
    }
    return $admins;
}

function analytic_conn(){
    $servername = database_host;
    $db_name = database_name;
    $db_user = database_user;
    $db_password = database_password;
    $conn = new mysqli($servername, $db_user, $db_password, $db_name);
    $iserror = 0;
    if ($conn->connect_error) {
        return '';
    }
    return $conn;
}

function rd_all_active_keywords($user_id) {
    global $wpdb;
    $active_key = array();   
    $all_keyword_list = $wpdb->get_results("SELECT keyword FROM `wp_keywords` where group_id IN(SELECT id FROM wp_keygroup WHERE location_id = $user_id AND status = 1 )");
    $active_keyws = array();
    if(!empty($all_keyword_list)){
        foreach($all_keyword_list as $activekey){
            $active_key[] = $activekey->keyword;
        }
    }    
    return $active_key;
}

function rd_keywords_order($user_id) {
        global $wpdb;
        $all_active_keywords = rd_all_active_keywords($user_id);
        $Content_keyword_Site = get_user_meta($user_id, "Content_keyword_Site", true);
        $Synonyms_keyword_arr = $Content_keyword_Site['Synonyms_keyword'];
        $activation = $Content_keyword_Site['activation'];
        $target_keyword = $Content_keyword_Site["target_keyword"];
        $key_arr = array();
        foreach ($all_active_keywords as $table_index => $row_active_key) {
            $same_keywords = $wpdb->get_results('SELECT meta_key FROM `wp_usermeta` WHERE `user_id` =' . $user_id . '  AND `meta_key` like "%LE_Repu_Keyword_%" AND (`meta_value` LIKE "' . $row_active_key . '" || `meta_value` LIKE "' . $row_active_key . '") ORDER BY `umeta_id` DESC');
            //pr($same_keywords);exit;
            $row_active_key = str_replace("'", "", $row_active_key);
            $Synonyms_keyword = array();
            $target_keyword_value = '';
            foreach ($same_keywords as $row_same_key) {
                $syn_keywords_index = explode('_', $row_same_key->meta_key);
                $syn_keywords_index = $syn_keywords_index[count($syn_keywords_index) - 1];
                if ($activation[$syn_keywords_index - 1] != 'inactive') {
                    $Synonyms_keyword = $Synonyms_keyword_arr[$syn_keywords_index - 1];
                    $Synonyms_keyword = array_filter($Synonyms_keyword);
                    $target_keyword_value = $target_keyword[$syn_keywords_index - 1];
                }
            }
            $key_arr[$row_active_key]['key'][] = $row_active_key;
            $key_arr[$row_active_key]['target_keyword'] = $target_keyword_value;
            $primary_and_synonyms_key = array();
            $primary_and_synonyms_key[] = $row_active_key;
            if (!empty($Synonyms_keyword)) {
                $Synonyms_keyword = array_map('trim', $Synonyms_keyword);
                $primary_and_synonyms_key = array_merge($primary_and_synonyms_key, $Synonyms_keyword);
                $key_arr[$row_active_key]['key'] = $primary_and_synonyms_key;                
            }
        }
        return $key_arr;
    }

function billing_info($MCCUserId) {
    
    global $wpdb;
       
    $billing_info = array();
    /********** Check Current locations payment Cycle START *************/

    $location_bill = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` DESC LIMIT 1");   
    
    // new code added 1-Feb-2017 - rudra       
    $bill_status = $location_bill->status;
    $billing_info['bill_startDate'] = $bill_startDate = $location_bill->startDate;
    $billing_info['final'] = $final = date("Y-m-d H:i:s", strtotime("+1 month", strtotime($bill_startDate)));

    $active_keywords = rd_all_active_keywords($MCCUserId);
    //pr($active_keywords);
    $billing_info['number_of_keywords'] = $number_of_keywords = count($active_keywords);

    /************* Count Keywords END **************/

    /************** Count Comp Keywords START ****************/

    $Content_keyword_Site = get_user_meta( $MCCUserId, 'competitor_url' , true );
    if($Content_keyword_Site == ''){
        $Content_keyword_Site = array();
    }
    //print_r($Content_keyword_Site);
    $billing_info['c_key'] = $c_key = count(array_filter($Content_keyword_Site, create_function('$a','return preg_match("#\S#", $a);')));
    $count_comp_keywords = $number_of_keywords*$c_key;
    $billing_info['count_comp_keywords'] = $count_comp_keywords = $count_comp_keywords ? $count_comp_keywords : 0;

    /************** Count Comp Keywords END ****************/

    /************** Count Keyword Opp START ****************/
    //echo "SELECT COUNT(*) FROM `keyword_opportunity` WHERE `user_id` = '".$MCCUserId."'"; 
    //echo "<br>";
    $key_opp = $wpdb->get_results("SELECT * FROM `keyword_opportunity` WHERE `user_id` = '".$MCCUserId."'");
    $key_opp_count = $wpdb->num_rows;

    //$billing_info['key_opp_count'] = $key_opp_count = $key_opp_count ? $key_opp_count : 0;

    // keyword oppo calculation
    $total_keyword_oppo = get_user_meta($MCCUserId,'total_keyword_oppo',true);
    if($total_keyword_oppo == ''){
        $total_keyword_oppo = $key_opp_count = $key_opp_count ? $key_opp_count : 0;
        update_user_meta($MCCUserId,'total_keyword_oppo',$total_keyword_oppo);
    }
    $billing_info['key_opp_count'] = $total_keyword_oppo;


    /************** Count Keyword Opp END ****************/


    $citation_run = $wpdb->get_results("SELECT * FROM `wp_citation_tracker` WHERE `user_id` = '".$MCCUserId."'");

    $citation_run_count = $wpdb->num_rows;
    //$billing_info['citation_run_count'] = $citation_run_count = $citation_run_count ? $citation_run_count : 0;

    // citation calculation
    $total_citation_tracker = get_user_meta($MCCUserId,'total_citation_tracker',true);
    if($total_citation_tracker == ''){
        $total_citation_tracker = $citation_run_count = $citation_run_count ? $citation_run_count : 0;
        update_user_meta($MCCUserId,'total_citation_tracker',$total_citation_tracker);
    }
    $billing_info['citation_run_count'] = $total_citation_tracker;


    /************** Count Citation Runs END ****************/

    /************** Count Site Audit Runs START ****************/
    $audit_run = $wpdb->get_results("SELECT * FROM `wp_site_audit` WHERE `user_id` = '".$MCCUserId."'");
    $snapshot_id = 0;
    foreach($audit_run as $audit_runs){
        $snapshot_id = $audit_runs->snapshot_id;
    }
    $billing_info['snapshot_id'] = $snapshot_id = $snapshot_id ? $snapshot_id : 0;

    $audit_run_count = $wpdb->num_rows;
    //$billing_info['audit_run_count'] = $audit_run_count = $audit_run_count ? $audit_run_count : 0;


    // site audit calculation
    $total_site_audit = get_user_meta($MCCUserId,'total_site_audit',true);
    if($total_site_audit == ''){
        $total_site_audit = $audit_run_count = $audit_run_count ? $audit_run_count : 0;
        update_user_meta($MCCUserId,'total_site_audit',$total_site_audit);
    }
    $billing_info['audit_run_count'] = $total_site_audit;


    // pages calculation 
    $pages = get_user_meta($MCCUserId,'total_scanned_pages',true);
    if($pages == ''){
        $pages = $wpdb->get_var("SELECT count(id) FROM cre_urls WHERE user_id = $MCCUserId"); 
        update_user_meta($MCCUserId,'total_scanned_pages',$pages);
    }
    $billing_info['pages_count'] = $pages;
        
    
    // new code added 1-Feb-2017 - rudra    
    
   
    

    /************** Count PAGES END ****************/    
   return $billing_info;
}

function location_package_prices(){
    global $wpdb;
    include_once ABSPATH . "wp-content/plugins/settings/get_location_package_prices.php";
    error_reporting(0);
    $fetch_prices = $locations_package_prices;
    return $fetch_prices;
}



function extra_consume_locations(){
    global $wpdb;
    $extra_consume_locations = array();
    
    $get_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `status` = '1'");
    $extra_consume_locations['noof_locations_count'] = $noof_locations_count = $wpdb->num_rows;

    $total_keywords = $total_comp_keywords = $total_audit = $total_citation = $total_opp_count = $total_pages_count = 0;

    foreach($get_locations as $get_location){

        $extra_consume_locations['MCCUserId'] = $MCCUserId = $get_location->MCCUserId;
        $brand_name = get_user_meta( $MCCUserId, 'BRAND_NAME' , true );
        $website = get_user_meta( $MCCUserId, 'website' , true );

        $billing_info = billing_info($MCCUserId);

        $number_of_keywords = $billing_info['number_of_keywords'];
        $count_comp_keywords = $billing_info['count_comp_keywords'];
        $key_opp_count = $billing_info['key_opp_count'];
        $pages_count = $billing_info['pages_count'];
        $audit_run_count = $billing_info['audit_run_count'];
        $citation_run_count = $billing_info['citation_run_count'];

        $extra_consume_locations['total_keywords'] = $total_keywords = $total_keywords + $number_of_keywords;
        $extra_consume_locations['total_comp_keywords'] = $total_comp_keywords = $total_comp_keywords + $count_comp_keywords;
        $extra_consume_locations['total_opp_count'] = $total_opp_count = $total_opp_count + $key_opp_count;
        $extra_consume_locations['total_citation'] = $total_citation = $total_citation + $citation_run_count;
        $extra_consume_locations['total_audit'] = $total_audit = $total_audit + $audit_run_count;
        $extra_consume_locations['total_pages_count'] = $total_pages_count = $total_pages_count + $pages_count;

        $extra_consume_locations['cron_run_time'] = $cron_run_time = date("Y-m-d 23:00:00", strtotime("-1 day", strtotime($billing_info['final'])));

    }
    
    return $extra_consume_locations;
    
}

function location_package_limit(){
    global $wpdb;
    $get_limits = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
    $package_limit = array();
    foreach($get_limits as $get_limit){
        $package_limit[$get_limit->lpf_field.'_limit'] = $get_limit->lpf_limit;
        $package_limit[$get_limit->lpf_field.'_addons'] = $get_limit->lpf_addons_add;
        $package_limit[$get_limit->lpf_field.'_used'] = $get_limit->lpf_used;
        $package_limit[$get_limit->lpf_field.'_delete'] = $get_limit->lpf_addons_delete;
        $package_limit[$get_limit->lpf_field.'_from_location'] = $get_limit->lpf_location_addon_field;
    }
    
    return $package_limit;
}

function agency_package_notification($checklimit){
    global $billing_enable;

    if(!empty($billing_enable) && $billing_enable == '1'){
        global $wpdb;
        include ABSPATH . "wp-content/plugins/settings/get_location_package_prices.php";
        $admin_userid = '1';
        //$to = "sunil.sharma@rudrainnovatives.com";
        $user_info = get_userdata($admin_userid);
        $to = $user_info->data->user_email;
        $lp_locations = $locations_package_prices->lp_locations;

        global $limit_keywords_notification;
        global $limit_ckeywords_notification;
        global $limit_keywordso_notification;
        global $limit_pages_notification;
        global $limit_site_audit_notification;
        global $limit_citation_run_notification;

        $location_package_limit = location_package_limit();
        $check_lp_all_limits = check_lp_all_limits();

        $available_keyword_limit = $check_lp_all_limits['keywords_available'];
        $available_comp_keywords_limit = $check_lp_all_limits['comp_keywords_available'];
        $available_keyword_opp_limit = $check_lp_all_limits['keyword_opp_available'];
        $available_pages_limit = $check_lp_all_limits['pages_available'];
        $available_site_audit_limit = $check_lp_all_limits['site_audit_available'];
        $available_citation_run_limit = $check_lp_all_limits['citation_run_available'];

        $message = spec_rep("<strong>Dear,</strong> <br><br>");

        if($checklimit == 'location' && ($lp_locations == $count_locations)){

            $count_locations = $wpdb->get_var("SELECT COUNT(*) FROM `wp_client_location` WHERE `status` = '1'") + 1;
            $type = 'location_limit_over';
            $subject = 'Free Locations Limit is Over';
            $message .= spec_rep("Your Limit to add minimum free locations is complete.<br> "
                    . "After this every location is paid. And charge is detect from your account automatically. <br><br>");

            $send_notification = 'send_notification';

        }elseif($checklimit == 'keywords' && ($available_keyword_limit <= $limit_keywords_notification)){

            $type = 'keywords_limit_over';
            $subject = 'Keywords Limit is Complete';
            $message .= spec_rep("Your Limit to add keywords is reached the maximum limit of keywords.<br> "
                    . "Purchase 'Keywords' Add-Ons and increase the Keyword limit. <br><br>");

            $send_notification = 'send_notification';

        }elseif($checklimit == 'comp_keywords' && ($available_comp_keywords_limit <= $limit_ckeywords_notification)){

            $type = 'comp_keywords_limit_over';
            $subject = 'Comp Keywords Limit is Complete';
            $message .= spec_rep("Your Limit to add Competitor URL is reached the maximum limit 'Comp keywords'.<br> "
                    . "Purchase 'Comp Keywords' Add-Ons and increase the limit. <br><br>");

            $send_notification = 'send_notification';

        }elseif($checklimit == 'keyword_opp' && ($available_keyword_opp_limit <= $limit_keywordso_notification)){

            $type = 'keyword_opp_limit_over';
            $subject = 'Keywords Opportunity Limit is Complete';
            $message .= spec_rep("Your Limit to get Keywords from Competitor URL is reached the maximum limit.<br> "
                    . "So purchase 'Keywords Opportunity' Add-Ons and increase the limit. <br><br>");

            $send_notification = 'send_notification';

        }elseif($checklimit == 'pages' && ($available_pages_limit <= $limit_pages_notification)){

            $type = 'pages_limit_over';
            $subject = 'Pages Limit is Over';
            $message .= spec_rep("Your Limit to get Pages from CRE is reached the maximum limit.<br> "
                    . "So purchase 'pages' Add-Ons and increase the limit. <br><br>");

            $send_notification = 'send_notification';

        }elseif($checklimit == 'site_audit' && ($available_site_audit_limit <= $limit_site_audit_notification)){

            $type = 'site_audit_limit_over';
            $subject = 'Site Audit Run Limit is Complete';
            $message .= spec_rep("Your Limit to Run Site Audit is complete.<br> "
                    . "So Purchase 'Site Audit' Add-Ons and increase Site Audit limit . <br><br>");

            $send_notification = 'send_notification';

        }elseif($checklimit == 'citation_run' && ($available_citation_run_limit <= $limit_citation_run_notification)){

            $type = 'citation_run_limit_over';
            $subject = 'Citation Run Limit is Complete';
            $message .= spec_rep("Your Limit to Run Citation is complete.<br> "
                    . "So Purchase 'Citation Run' Add-Ons and increase Run Citation limit . <br><br>");

            $send_notification = 'send_notification';

        }else{
            $send_notification = '';
        }

        $message .= spec_rep("Best Regards<br>Enfusen");

        if(!empty($send_notification) && $send_notification =='send_notification'){
            $status_date = date('Y-m-d H:i:s', time());
            $ins_notification['order_id'] = '0';
            $ins_notification['client_id'] = $admin_userid;
            $ins_notification['sender_user_id'] = $admin_userid;
            $ins_notification['type'] = $type;
            $ins_notification['created_date'] = $status_date;
            $ins_notification['receiver_user_id'] = $admin_userid;
            $ins_notification['subject'] = $subject;
            $ins_notification['message'] = $message;
            $wpdb->insert('wp_notification', $ins_notification);

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: <'.$to.'>' . "\r\n";

            mail($to,$subject,$message,$headers);
        }

        return $check_lp_all_limits;
    }
    
    
}

function check_lp_all_limits(){
    global $wpdb;
    
    /*********** Check per Add-Ons Limit START ****************/
    include ABSPATH . "wp-content/plugins/settings/get_location_package_prices.php";    
    $key_field_limit = $locations_package_prices->lp_key_range;
    $ckey_field_limit = $locations_package_prices->lp_ckey_range;
    $keyo_field_limit = $locations_package_prices->lp_keyo_range;
    $pages_field_limit = $locations_package_prices->lp_page_range;
    $audit_field_limit = $locations_package_prices->lp_audit_range;
    $citation_field_limit = $locations_package_prices->lp_citation_range;
    $demoaccounts_field_limit = $locations_package_prices->lp_demoaccounts_range;
    $location_field_limit = 1;
    /*********** Check per Add-Ons Limit END ****************/
    
    $check_lp_all_limits = array();     // lp means Location Package
    $location_package_limit = location_package_limit();
    
    $get_all_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `status` = '1' ");
    
    $all_users_kw = $all_users_comp_kw = 0;
    foreach($get_all_locations as $get_all_location){
        //pr($get_all_location);
        $user_id = $get_all_location->MCCUserId;
        /********** Count Keywords (START) ***********/
        $active_keywords = rd_all_active_keywords($user_id);
        //pr($active_keywords);
        $count_active_kw = count($active_keywords);
        $all_users_kw = $all_users_kw + $count_active_kw;
        /********** Count Keywords (END) ***********/

        /********** Count Comp Keywords (START) ***********/
        $comp_keywords = billing_info($user_id);
        $count_comp_kw = $comp_keywords['count_comp_keywords'];
        $all_users_comp_kw = $all_users_comp_kw + $count_comp_kw;
        /********** Count Comp Keywords (END) ***********/
    }
    
    

    $check_lp_all_limits['keywords_package_limit'] = $location_package_limit['keywords_limit'];
    $check_lp_all_limits['keywords_total_limit'] = $total_keyword_limit = $location_package_limit['keywords_limit'] + ($location_package_limit['keywords_addons']*$key_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['keywords_from_location']);
    $check_lp_all_limits['keywords_used'] = $used_keyword_limit = $all_users_kw;
    $check_lp_all_limits['keywords_available'] = $available_keyword_limit = $total_keyword_limit - $used_keyword_limit;

    $check_lp_all_limits['comp_keywords_package_limit'] = $location_package_limit['comp_keywords_limit'];
    $check_lp_all_limits['comp_keywords_total_limit'] = $total_comp_keywords_limit = $location_package_limit['comp_keywords_limit'] + ($location_package_limit['comp_keywords_addons']*$ckey_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['comp_keywords_from_location']);
    $check_lp_all_limits['comp_keywords_used'] = $used_comp_keywords_limit = $all_users_comp_kw;
    $check_lp_all_limits['comp_keywords_available'] = $available_comp_keywords_limit = $total_comp_keywords_limit - $used_comp_keywords_limit;

    $check_lp_all_limits['keyword_opp_package_limit'] = $location_package_limit['keyword_opp_limit'];
    $check_lp_all_limits['keyword_opp_total_limit'] = $total_keyword_opp_limit = $location_package_limit['keyword_opp_limit'] + ($location_package_limit['keyword_opp_addons']*$keyo_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['keyword_opp_from_location']);
    $check_lp_all_limits['keyword_opp_used'] = $used_keyword_opp_limit = $location_package_limit['keyword_opp_used'];
    $check_lp_all_limits['keyword_opp_available'] = $available_keyword_opp_limit = $total_keyword_opp_limit - $used_keyword_opp_limit;

    $check_lp_all_limits['pages_package_limit'] = $location_package_limit['pages_limit'];
    $check_lp_all_limits['pages_total_limit'] = $total_pages_limit = $location_package_limit['pages_limit'] + ($location_package_limit['pages_addons']*$pages_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['pages_from_location']);
    $check_lp_all_limits['pages_used'] = $used_pages_limit = $location_package_limit['pages_used'];
    $check_lp_all_limits['pages_available'] = $available_pages_limit = $total_pages_limit - $used_pages_limit;

    $check_lp_all_limits['site_audit_package_limit'] = $location_package_limit['site_audit_limit'];
    $check_lp_all_limits['site_audit_total_limit'] = $total_site_audit_limit = $location_package_limit['site_audit_limit'] + ($location_package_limit['site_audit_addons']*$audit_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['site_audit_from_location']);
    $check_lp_all_limits['site_audit_used'] = $used_site_audit_limit = $location_package_limit['site_audit_used'];
    $check_lp_all_limits['site_audit_available'] = $available_site_audit_limit = $total_site_audit_limit - $used_site_audit_limit;

    $check_lp_all_limits['citation_run_package_limit'] = $location_package_limit['citation_run_limit'];
    $check_lp_all_limits['citation_run_total_limit'] = $total_citation_run_limit = $location_package_limit['citation_run_limit'] + ($location_package_limit['citation_run_addons']*$citation_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['citation_run_from_location']);
    $check_lp_all_limits['citation_run_used'] = $used_citation_run_limit = $location_package_limit['citation_run_used'];
    $check_lp_all_limits['citation_run_available'] = $available_citation_run_limit = $total_citation_run_limit - $used_citation_run_limit;
    
    $check_lp_all_limits['demoaccounts_package_limit'] = $location_package_limit['demo_accounts_limit'];
    $check_lp_all_limits['demoaccounts_total_limit'] = $total_demo_accounts_limit = $location_package_limit['demo_accounts_limit'] + ($location_package_limit['demo_accounts_addons']*$demoaccounts_field_limit) + ($location_package_limit['location_addons']*$location_package_limit['demo_accounts_from_location']);
    $check_lp_all_limits['demoaccounts_used'] = $used_demo_accounts_limit = $location_package_limit['demo_accounts_used'];
    $check_lp_all_limits['demoaccounts_available'] = $available_demo_accounts_limit = $total_demo_accounts_limit - $used_demo_accounts_limit;
    
    $check_lp_all_limits['location_package_limit'] = $location_package_limit['location_limit'];
    $check_lp_all_limits['location_total_limit'] = $total_location_limit = $location_package_limit['location_limit'] + ($location_package_limit['location_addons']*$location_package_limit['location_from_location']);
    $check_lp_all_limits['location_used'] = $used_location_limit = $location_package_limit['location_used'];
    $check_lp_all_limits['location_available'] = $available_location_limit = $total_location_limit - $used_location_limit;
   
    /*
    $limit_check = $total_location_limit - $location_package_limit['location_delete'];
    if($limit_check > $used_location_limit){
        $check_lp_all_limits['location_available'] = $available_location_limit = $limit_check - $used_location_limit;
    }else{
        $check_lp_all_limits['location_available'] = $available_location_limit = 0;
    }
    */  
    
    return $check_lp_all_limits;
    //return $location_package_limit;
    

}

function check_trial(){
    global $wpdb;
    $trial_array = array();
    $check_trial = $wpdb->get_row("SELECT * FROM `wp_agency_trialperiod`");
    if(!empty($check_trial)){
        $trial_array['trial_nooflocations'] = $check_trial->trial_nooflocations;

        $trial_array['trial_for_days'] = $trial_for_days = $check_trial->days;

        $trial_array['trial_for_hours'] = $trial_for_hours = $trial_for_days*24;

        $trial_array['billing_enable'] = $check_trial->billing_enable;
        //echo $check_trial->date;
        $trial_array['trial_start_date'] = $trial_start_date = strtotime($check_trial->date);

        //echo date("Y-m-d h:i:s");
        $currentdate = strtotime(date("Y-m-d h:i:s"));

        $pending_trial_hour = intval(abs($currentdate - $trial_start_date)/(60*60));

        if($trial_for_hours > $pending_trial_hour){
            $trial_array['trial_period'] = 'enable';
        }else{
            $trial_array['trial_period'] = 'desable';
        }
    }
    return $trial_array;
}

function getall_discountcodes(){
    
    $main_webiste = SET_PARENT_URL;

    $website_var = parse_url($main_webiste);

    $main_enfusen_url = $website_var[scheme]."://".$website_var[host];
    
    $data_variables = array('payment_time_discountcode_check' => 'payment_time_discountcode_check');
    //pr($data_variables);
    $datavariable_string = http_build_query($data_variables);

    $url = $main_enfusen_url."/location_packages_for_agency.php";

    $ch_datavariable = curl_init($url);
    curl_setopt($ch_datavariable, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_datavariable, CURLOPT_POSTFIELDS, $datavariable_string);
    curl_setopt($ch_datavariable, CURLOPT_RETURNTRANSFER, true);


    $result = curl_exec($ch_datavariable);
    //pr($result);
    curl_close($ch_datavariable);

    $discountcode_object = json_decode($result);

    error_reporting(0);
    //pr($discountcode_object);
    
    return $discountcode_object;
}

function applied_discountcode_data($dcname){
    
    $main_webiste = SET_PARENT_URL;

    $website_var = parse_url($main_webiste);

    $main_enfusen_url = $website_var[scheme]."://".$website_var[host];
    
    $data_variables = array('applied_discountcode_data' => 'applied_discountcode_data', 'applied_discountcode_name' => $dcname);
    //pr($data_variables);
    $datavariable_string = http_build_query($data_variables);

    $url = $main_enfusen_url."/location_packages_for_agency.php";

    $ch_datavariable = curl_init($url);
    curl_setopt($ch_datavariable, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_datavariable, CURLOPT_POSTFIELDS, $datavariable_string);
    curl_setopt($ch_datavariable, CURLOPT_RETURNTRANSFER, true);


    $result = curl_exec($ch_datavariable);
    //pr($result);
    curl_close($ch_datavariable);

    $discountcode_object = json_decode($result);

    error_reporting(0);
    //pr($discountcode_object);
    
    if(empty($discountcode_object)){
        $response = "Discount Code Not Exist. Please Insert Valid Discount Code.";
        $return = 'notrun';
    }else{
        $code_status = $discountcode_object->dc_status;
        $code_nodc = $discountcode_object->dc_nodc;
        $code_time_used = $discountcode_object->dc_timesused;
        $code_available = $code_nodc - $code_time_used;
        $code_trial = $discountcode_object->dc_trial_user;
        if(($code_status == 'active') && ($code_available > 0) && empty($code_trial)){
            $response = "Discount Code ok.";
            $return = 'run';
        }elseif(!empty($code_trial) && ($code_trial == 'trial_user')){
            $response = "Sorry, This is Trial code. And not to be used for Billing payment. Please try again with valid Discount Code.";
            $return = 'notrun';
        }else{
            $response = "Discount Code Expire.";
            $return = 'notrun';
        }
    }
    
    $discountcode_object_array = array();
    
    $discountcode_object_array['codedata'] = $discountcode_object;
    $discountcode_object_array['coderesponse'] = $response;
    $discountcode_object_array['codereturn'] = $return;
    
    return $discountcode_object_array;
}

function assignthis_discount_from_agency($getpackage_id, $getpackage_name){
    $db_name = database_name_wp;
    $main_webiste = SET_PARENT_URL;

    $website_var = parse_url($main_webiste);

    $main_enfusen_url = $website_var[scheme]."://".$website_var[host];
    
    $data_variables = array('assignthis_discount_from_agency' => 'assignthis_discount_from_agency', 'database_name_wp' => $db_name, 'discountid' => $getpackage_id, 'discountname' => $getpackage_name);
    //pr($data_variables);
    $datavariable_string = http_build_query($data_variables);

    $url = $main_enfusen_url."/location_packages_for_agency.php";

    $ch_datavariable = curl_init($url);
    curl_setopt($ch_datavariable, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_datavariable, CURLOPT_POSTFIELDS, $datavariable_string);
    curl_setopt($ch_datavariable, CURLOPT_RETURNTRANSFER, true);


    $result = curl_exec($ch_datavariable);
    //pr($result);
    curl_close($ch_datavariable);

    $discountcode_object = json_decode($result);

    error_reporting(0);
    //pr($discountcode_object);
    return $discountcode_object;
}

function get_addons_for_location(){
    $db_name = database_name_wp;
    $main_webiste = SET_PARENT_URL;

    $website_var = parse_url($main_webiste);

    $main_enfusen_url = $website_var[scheme]."://".$website_var[host];
    
    $data_variables = array('get_addons_for_location' => 'get_addons_for_location');
    //pr($data_variables);
    $datavariable_string = http_build_query($data_variables);

    $url = $main_enfusen_url."/location_packages_for_agency.php";

    $ch_datavariable = curl_init($url);
    curl_setopt($ch_datavariable, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_datavariable, CURLOPT_POSTFIELDS, $datavariable_string);
    curl_setopt($ch_datavariable, CURLOPT_RETURNTRANSFER, true);


    $result = curl_exec($ch_datavariable);
    //pr($result);
    curl_close($ch_datavariable);

    $addons_for_location = json_decode($result);

    error_reporting(0);
    //pr($discountcode_object);
    return $addons_for_location;
}

function trial_check_for_agency_limit_message(){
    global $wpdb;
    $c_id = get_current_user_id();
    $user = new WP_User($c_id);
    $u_role = $user->roles[0];
    $count_paid = $wpdb->get_var("SELECT COUNT(*) FROM `wp_pay_for_locations`");
    if(BILLING_ENABLE == 1){
        if(($u_role == 'administrator' || administrator_permission())){
            if(empty($count_paid)){
                include ABSPATH . "wp-content/plugins/settings/check_trial_period.php";        
                $trial_status = $trial_package->status;
                $trial_activetill = date("Y-m-d", strtotime($trial_package->expire_date));

                //$trial_activetill = '2017-01-09'; //temp code
                if(isset($trial_status) && !empty($trial_status) && ($trial_status == 'active')){

                    $currentdate = date("Y-m-d");
                    if($trial_activetill >= $currentdate){
                        $date1 = $currentdate;
                        //echo "<br>";
                        $date2 = $trial_activetill;

                        //Convert them to timestamps.
                        $date1Timestamp = strtotime($date1);
                        $date2Timestamp = strtotime($date2);

                        //Calculate the difference.
                        $difference = $date2Timestamp - $date1Timestamp;

                        $days_left = $difference/86400;
                        if($days_left > 0 || $date1 == $date2){
                            $alert_for = 'trial';
                        }else{
                            $alert_for = 'subscription';
                        }
                    } else {
                        $alert_for = 'subscription';
                    }
                }else{
                    $alert_for = 'subscription';
                }
            }else{
                $alert_for = 'subscription';
            }
            
        }
        return $alert_for;
    }else{
        return '';
    }
}

function used_discountcode_inagency(){
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

        $discountcode_object = json_decode($result_discountcode);
        $additional_location_cost = $discountcode_object->dc_location_cost;
    }else{
        $additional_location_cost = 100;
    }
    if(empty($additional_location_cost)){
        $additional_location_cost = 100;
    }
    return $additional_location_cost;
}
?>
