<?php

global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

if (isset($_REQUEST["param"]) && $_REQUEST["param"] != "reset_password") {   
    if($user_id == 0){
        json(0,'Login is required');
    }
}

if (isset($_REQUEST["param"])) {
    if ($_REQUEST["param"] == "white_label_settings") {                                  

        $user_id = $current_user->ID;
        $now = date("Y-m-d H:i:s");            
        $prefix = isset($_POST['prefix'])?trim(htmlspecialchars($_POST['prefix'])):'';
        $is_white_label = 0;
        $white_label_url = ''; $original_url = '';
        if(isset($_POST['urlrewrite'])){
            $is_white_label = 1;
            $white_label_url = isset($_POST['urlwhitelable'])?$_POST['urlwhitelable']:'';
            if(trim($white_label_url) == ''){
                json(0,'Url cannot be empty');
            }
            $original_url = site_url();
        }

        $now = date("Y-m-d H:i:s");
        $res = $wpdb->get_row
        (
            $wpdb->prepare
            (
                    "select * from " . client_company_info(),""                        
            )
        );
        
        if(!empty($res)){
        //Commented starts by rudra 14 feb 2017
		if(isset($_POST['urlwhitelable']) && !empty($_POST['urlwhitelable'])){
	     		$data['agency_client_id']=$_SESSION['agency_id'];
			$data['post_url']=$_POST['urlwhitelable'];
			$ch = curl_init('http://admin.enfusen.com/cron/update_cron_url.php');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			// execute!
			$response = curl_exec($ch);
			// close the connection, release resources used
			curl_close($ch);
	       }
         //Commented ends by rudra 14 feb 2017
            $original_url = trim($res->original_url) != ''?$res->original_url:site_url();
            $id = $res->id;
            $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . client_company_info() . " SET is_white_label = %s, white_label_url = %s, original_url = %s "
                        . "WHERE id = %d", 
                        $is_white_label, $white_label_url, $original_url, $id
                )
            );
        }
        else{

            $rs = $wpdb->query
            (
                $wpdb->prepare
                (
                        "INSERT INTO " . client_company_info() . " (is_white_label, white_label_url, original_url, created_dt) "
                        . "VALUES (%d, %s, %s, '%s')", 
                        $is_white_label, $white_label_url, $original_url, $now
                )
            );
                                   
        }
        
        if($is_white_label == 1){                
                update_option( 'siteurl', $white_label_url );
                update_option( 'home', $white_label_url );
        }
        else{             
                $originalurl = $wpdb->get_var
                (
                    $wpdb->prepare
                    (
                            "select original_url from " . client_company_info(),""                        
                    )
                );
                $tbl_options = $wpdb->prefix."options";
                
                $sql = "UPDATE  " . $tbl_options." SET option_value = %s WHERE option_name = 'siteurl'";                
                $wpdb->query( $wpdb->prepare( $sql, $originalurl ) );                  
                $sql1 ="UPDATE  " . $tbl_options." SET option_value = %s WHERE option_name = 'home'";
                $wpdb->query( $wpdb->prepare( $sql1, $originalurl ) );  
                                
                if (session_status() == PHP_SESSION_NONE)
                    session_start();
                
                $_SESSION['slogin'] = 0;
                unset($_SESSION['slogin']);                
        }
        
        $base_dir = dirname(SET_CONTENT_DIR);
        $root_dir = dirname(dirname($base_dir));

        $ht = 'Options -Indexes '.PHP_EOL;
        $ht .= PHP_EOL.'<IfModule mod_rewrite.c>'.PHP_EOL;
        $ht .= 'RewriteEngine On'.PHP_EOL;
        $ht .= 'RewriteBase / '.PHP_EOL;

        $url = SET_PARENT_URL;
        $db_name = DB_NAME;
        $token = md5($db_name.time().$db_name);
        $wpdb->query
        (
            $wpdb->prepare
            (
                "INSERT INTO super_tokens (token) "
                . "VALUES (%s)", 
                $token
            )
        );
        
        $params = array();
        $params['param'] = 'get_white_label_clients';
        $params['db_name'] = $db_name;
        $params['token'] = $token;
        $params['is_white_label'] = $is_white_label;
        $params['white_label_url'] = $white_label_url;
        
        $clients = json_decode(parent_api_call($url,$params));
        if($clients->sts == 1){
            $clients = $clients->arr;
        }
        else{
            $clients = array();
        }
        if(!empty($clients)){
            foreach($clients as $client){
                $whiteurl = trim(str_replace(array("http://","https://"), array("",""), $client->white_lbl));
                $whiteurl = preg_replace('{/$}', '', $whiteurl);
                $ur = explode(".", $whiteurl);
                if(!empty($ur)){
                    $pre = $client->prefix;
                     $strr = '';
                    foreach($ur as $u){
                        $strr .= $u.'\.';
                    }
                    $strr = substr($strr,0,-2);
                    $ht .= PHP_EOL.'RewriteCond %{HTTP_HOST} ^(www\.)?'.$strr.'$'.PHP_EOL;
                    $ht .= 'RewriteRule !^clients/'.$pre.'/ /clients/'.$pre.'%{REQUEST_URI} [L,NC]'.PHP_EOL;
                }
            }
        }
        $root_dir = $root_dir.'/.htaccess';        

        $ht .= PHP_EOL.'</IfModule>';
        file_put_contents($root_dir, $ht);        
        json(1,'White label setting saved');
    }     
    else if ($_REQUEST["param"] == "add_existing_location") {
        $c_id = get_current_user_id();
        $UserID = $account = isset($_POST['locid'])?intval($_POST['locid']):0;        
        $hasuser = get_user_by('id',$account);
        if(!empty($hasuser)){
            
            $is_created = $wpdb->query
            (
                $wpdb->prepare
                (
                        "INSERT INTO " . client_location() . " (MCCUserId, created_by, status, created_dt) "
                        . "VALUES (%d, %d, 1, '%s')", $account, $c_id, date("Y-m-d H:i:s")
                )
            );
            
            $conn = conn_analytic();
            
            if ($conn != '') {
                $sql = "SELECT count(MCCUserID) as total FROM clients_table WHERE MCCUserID = $UserID";               
                $result = mysqli_query($conn, $sql);                
                $row = $result->fetch_object();                
                $hasadded = $row->total;
                if ($hasadded == 0) {
                    $Name = mysqli_real_escape_string($conn, $_POST['company_name']);
                    $Domain = mysqli_real_escape_string($conn, $_POST['website']);
                    $Email = mysqli_real_escape_string($conn, $_POST['info_email']);

                    $sql = "INSERT INTO clients_table(MCCUserID,Name,Domain,Email,CreatedDate) VALUES($UserID,'$Name','$Domain','$Email',NOW())";
                    mysqli_query($conn, $sql);
                }
                
                $sql = "SHOW TABLES LIKE 'short_analytics_".$UserID."'"; 
                $result = mysqli_query($conn, $sql);                                
                if ($result->num_rows == 0) {
                
                    $ClientID = $UserID;
                    $sql = "CREATE TABLE IF NOT EXISTS `short_analytics_$ClientID` (
                        `short_analytics_id` int(13) NOT NULL AUTO_INCREMENT,
                        `DateOfVisit` varchar(20) NOT NULL,
                        `PageURL` varchar(300) NOT NULL,
                        `Keyword` varchar(100) NOT NULL,
                        `CurrentRank` int(5) NOT NULL,
                        `organic` int(5) NOT NULL,
                        `social` int(5) NOT NULL,
                        `referral` int(5) NOT NULL,
                        `(none)` int(5) NOT NULL,
                        `cpc` int(5) NOT NULL,
                        `email` int(5) NOT NULL,
                        `Total` int(10) NOT NULL,
                        `TimeOnSite` varchar(10) NOT NULL,
                        `BounceRate` varchar(10) NOT NULL,
                        `url_type` varchar(32) NOT NULL,
                        PRIMARY KEY (`short_analytics_id`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
                    mysqli_query($conn, $sql);

                    // create dynamic tables
                    include_once(get_template_directory() . '/analytics/DBUtils.php');                    
                    CreateCustomizedTableForNewClient(AnalyticsDataDBTableName, $ClientID);
                    CreateCustomizedTableForNewClient(AnalyticsCacheDataDBTableName, $ClientID);
                    CreateCustomizedTableForNewClient(ConvTrackingDBTableName, $ClientID);
                    CreateCustomizedTableForNewClient(ConvTrackingCacheDBTableName, $ClientID);
                    CreateCustomizedTableForNewClient(ConvTrackingFilteredDBTableName, $ClientID);
                    CreateCustomizedTableForNewClient(ConvTrackingUrlsDBTableName, $ClientID);
                    CreateCustomizedTableForNewClient(ConvTrackJSCodeFileName, $ClientID);
                }
            }
            
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION['msgaddlocation']);
            
            if ($is_created) {
                json(1,'Location successfully added.');
            } else {
                json(0,'Some problem occurred with mysql query.');
            }              
            
        }
        json(0,'Invalid Request');
    }
    else if ($_REQUEST["param"] == "assign_location") {
        $c_id = get_current_user_id();
        $location_id = isset($_POST['locid'])?intval($_POST['locid']):0;
        $user_id = isset($_POST['uid'])?intval($_POST['uid']):0;
        
        if($location_id == 'add_all_locations'){
            $locations = $wpdb->get_results
            (
                $wpdb->prepare
                (
                    "SELECT * FROM " . client_location() . " WHERE status = 1 ORDER BY created_dt DESC", ""
                )
            );
            $i = 0;
            foreach ($locations as $location) {
                
                $has_added = $wpdb->get_var
                (
                    $wpdb->prepare
                    (
                        "SELECT count(id) as total FROM ". location_mapping()." WHERE location_id = %d AND user_id = %d",
                        $location->id,
                        $user_id
                    )
                );
                if(empty($has_added)){
                    $wpdb->query
                    (
                        $wpdb->prepare
                        (
                            "INSERT INTO " . location_mapping() . " (location_id, user_id, created_dt) "
                            . "VALUES(%d, %d, '%s')",
                            $location->id, $user_id, date("Y-m-d H:i:s")
                        )
                    );
                    $i++;
                }
                
            }
            if($i > 0){
                user_location_all_email($locations,$user_id);
                json(1,'User has been sucessfully added for all locations');
            }
            else{
                json(0,'User has been already added for all locations');
            }
            
        }
        else{
            $has_added = $wpdb->get_var
            (
                $wpdb->prepare
                (
                    "SELECT count(id) as total FROM ". location_mapping()." WHERE location_id = %d AND user_id = %d",
                    $location_id,
                    $user_id
                )
            );
            if($has_added > 0){
                json(0,'User already added in location');
            }
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "INSERT INTO " . location_mapping() . " (location_id, user_id, created_dt) "
                    . "VALUES(%d, %d, '%s')",
                    $location_id, $user_id, date("Y-m-d H:i:s")
                )
            );
            user_location_add_email($location_id,$user_id);
            json(1,'User has been sucessfully added for this location');
        }
       
    }
    else if ($_REQUEST["param"] == "remove_existing_location") {
        $c_id = get_current_user_id();
        $id = isset($_POST['locid'])?intval($_POST['locid']):0;  
        
        $wpdb->query
        (
            $wpdb->prepare
            (
                    "DELETE FROM " . location_mapping() . " WHERE location_id = %d",$id
            )
        );
        
        $is_deleted = $wpdb->query
        (
            $wpdb->prepare
            (
                    "DELETE FROM " . client_location() . " WHERE id = %d",$id
            )
        );
        if ($is_deleted) {
            json(1,'Location successfully unassign.');
        } else {
            json(0,'Some problem occurred with mysql query.');
        }  
        json(0,'Invalid Request');
    }
    else if ($_REQUEST["param"] == "set_analytic_url_session") {
        
        $loc_id = intval($_REQUEST['locid']);        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // need to redirect to admin location page, once return from analytic connect 
        $_SESSION['analytic_loc_id'] = $loc_id;
    }
    else if ($_REQUEST["param"] == "remove_parent_user") {
        
        $uid = intval($_REQUEST['uid']);        
        $tbl = $wpdb->prefix.'usermeta';
        $res = $wpdb->query
        (
            $wpdb->prepare
            (
                "delete from $tbl WHERE user_id = %d AND meta_key = %s",$uid,'parent_analytics_user_id'
            )
        );
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // need to redirect to admin location page, once return from analytic connect 
        $_SESSION['removedparent'] = 1;
        
        if($res)
            json(1,'Parent user removed for google analytic');
        else
            json(0,'Parent user is not removed ');
    }
    else if ($_REQUEST["param"] == "script_save") {
        
        
        $now = date("Y-m-d H:i:s");
        $res = $wpdb->get_row
        (
            $wpdb->prepare
            (
                    "select * from " . client_company_info(),""                        
            )
        );
        
        $codefor = isset($_REQUEST['codefor'])?htmlspecialchars($_REQUEST['codefor']):"updated_dt";
        $val = isset($_REQUEST["$codefor"])?htmlspecialchars($_REQUEST["$codefor"]):"";
        if($codefor == 'updated_dt'){
            $val = $now;
        }
        
        if(!empty($res)){
            
            $original_url = trim($res->original_url) != ''?$res->original_url:site_url();
            $id = $res->id;
            $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . client_company_info() . " SET $codefor = %s "
                        . "WHERE id = %d", 
                        $val, $id
                )
            );
        }
        else{

            $wpdb->query
            (
                $wpdb->prepare
                (
                        "INSERT INTO " . client_company_info() . " ($codefor, created_dt) "
                        . "VALUES (%s, '%s')", 
                        $val, $now
                )
            );
        }
        
        json(1,'Script Saved');
    }
    else if ($_REQUEST["param"] == "location_saved") {
        
        $location_id = isset($_REQUEST['location_id'])?intval($_REQUEST['location_id']):0;
        $location = $wpdb->get_row
        (
            $wpdb->prepare
            (
                    "SELECT * FROM ". client_location()." WHERE id = %d",$location_id
            )
        );
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['msgaddlocation']);   
        
        if(!empty($location)){
            // update record
            $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . client_location() . " SET location_name = %s, address = %s, city = %s, "
                        . "state = %s, country = %s, description = %s WHERE id = %d", 
                        esc_attr($_POST['location_name']), esc_attr($_POST['address']), esc_attr($_POST['city']), esc_attr($_POST['state']),
                        esc_attr($_POST['country']), esc_attr($_POST['description']), $location_id
                )
            );
            json(1,'Location Updated');
        }
        else{
            // insert record
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                        "INSERT INTO " . client_location() . " (location_name, address, city, state, country, description, created_dt) "
                        . "VALUES (%s, %s, %s, %s, %s, %s, '%s')", 
                        esc_attr($_POST['location_name']), esc_attr($_POST['address']), esc_attr($_POST['city']), esc_attr($_POST['state']),
                        esc_attr($_POST['country']), esc_attr($_POST['description']), date("Y-m-d H:i:s")
                )
            );
            json(1,'Location Saved');
        }
        
    }
    else if ($_REQUEST["param"] == "delete_client_location") {
        $location_id = isset($_REQUEST['location_id'])?intval($_REQUEST['location_id']):0;
        $location = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT * FROM ". client_location()." WHERE id = %d",$location_id
            )
        );
        if(empty($location)){
            json(0,'Location ID Invalid');
        }
        
        //deleteusermeta($location->MCCUserId);     
        
        if($location->status == 1){
            // descrement one number used i wp_package table
            $locationlt = $wpdb->get_var("SELECT count(id) as total FROM ".client_location()." WHERE status = 1");            
            $newnum = 0;
            if($locationlt > 0){
                $newnum = $locationlt - 1;
            }
            $wpdb->query("UPDATE wp_location_package_fields SET lpf_used = '$newnum' WHERE lpf_field = 'location'");            
            
        }
        
        $wpdb->query
        (
            $wpdb->prepare
            (
                "DELETE FROM " . location_mapping() . " WHERE location_id = %d", 
                $location_id
            )
        );
        
        $wpdb->query
        (
            $wpdb->prepare
            (
                "DELETE FROM " . client_location() . " WHERE id = %d", 
                $location_id
            )
        );
        // delete location and ranking reports from placesscout also - so we can consume same for new clients
        $user_id = $location->MCCUserId;
        delete_location_and_reports($user_id);
        
        if($location->status == 1){
            updateSubscription();
        }
        
        json(1,'Location Deleted');
    }   
    else if ($_REQUEST["param"] == "checkcode") {
        
        $discountname = $_POST['discountcodename'];        
        echo json_encode(applied_discountcode_data($discountname));
        die;
    }
    else if ($_REQUEST["param"] == "getsession_values") { 
          @session_start();
          $_SESSION['location_varibales'] = $_POST; print_r($_SESSION['location_varibales']); 
          die; 
    }
    else if ($_REQUEST["param"] == "session_change") {
        
        $location_id = isset($_REQUEST['location_id'])?intval($_REQUEST['location_id']):0;
        $user_id = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT MCCUserId FROM ". client_location()." WHERE id = %d",$location_id
            )
        );
        
        if($user_id > 0){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['location'] = $location_id;           
            $_SESSION["Current_user_live"] = $user_id;
            json(1,'Site successfully changed');
        }        
        json(0,'Some error occurred. Please refresh page');
        
    }
    else if ($_REQUEST["param"] == "add_user") {
        $uemail = isset($_POST["memail"])?htmlspecialchars($_POST["memail"]):0;
        $user = get_user_by("email",$uemail);
        if(empty($user)){
            json(0,'This email is not registered with MCC');
        }

        $user_id = $user->data->ID;        

        $location_id = isset($_POST["location_id"])?intval($_POST["location_id"]):0;
        $location = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT count(id) as total FROM ". client_location()." WHERE id = %d",$location_id
            )
        );
        if($location < 1){
            json(0,'Location ID Invalid');
        }        

        $has_added = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT count(id) as total FROM ". location_mapping()." WHERE location_id = %d AND user_id = %d",
                $location_id,
                $user_id
            )
        );
        if($has_added > 0){
            json(0,'User already added in location');
        }
        $wpdb->query
        (
            $wpdb->prepare
            (
                "INSERT INTO " . location_mapping() . " (location_id, user_id, created_dt) "
                . "VALUES(%d, %d, '%s')",
                $location_id, $user_id, date("Y-m-d H:i:s")
            )
        );
        user_location_add_email($location_id,$user_id);
        json(1,'User has been sucessfully added for this location');
    }
    else if ($_REQUEST["param"] == "remove_user") {
              
        $user_id = isset($_POST["u_id"])?intval($_POST["u_id"]):0;            
        $location_id = isset($_POST["location_id"])?intval($_POST["location_id"]):0;
        $id = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT id FROM ". location_mapping()." WHERE location_id = %d AND user_id = %d",
                $location_id,
                $user_id
            )
        );
        if($id <= 0){
            json(0,'Invalid Request');
        }  
        
        $wpdb->query
        (
            $wpdb->prepare
            (
                "DELETE FROM " . location_mapping() . " WHERE id = %d",
                $id
            )
        );
        user_location_remove_email($location_id,$user_id);
        json(1,'User has been sucessfully unassign from location');
        
    }
    else if ($_REQUEST["param"] == "deleteuser") {
              
        $c_id = get_current_user_id();
        $user = new WP_User($c_id);
        $u_role =  $user->roles[0];
        if($u_role == 'administrator' || administrator_permission()){
            $user_id = isset($_POST["u_id"])?intval($_POST["u_id"]):0;    
            if($user_id == 0){
                json(0,'Invalid Request');
            }
            $location_id = isset($_POST["location_id"])?intval($_POST["location_id"]):0;                        
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "DELETE FROM " . location_mapping() . " WHERE user_id = %d",
                    $user_id
                )
            );                       
            
            wp_delete_user($user_id);
            
            json(1,'User has been sucessfully deleted');
        }
        json(0,'You have not permissions to delete user');
        
    }
    else if ($_REQUEST["param"] == "pwd_reset_link") {
        $user_id = isset($_POST["uid"])?intval($_POST["uid"]):0;
        $user = get_user_by('id',$user_id);
        if(empty($user)){
            json(0,'Invalid User');
        }
        $random = mt_rand(99999, 9999999999);
        $code = md5($random.time());
        $usertbl = $wpdb->prefix."users";
        
        $wpdb->query
        (
            $wpdb->prepare
            (
                "UPDATE " . $usertbl . " SET user_activation_key = %s WHERE ID = %d",
                $code, $user_id
            )
        );
        
        $user_email = $user->data->user_email;
        $first_name = trim($user->data->user_nicename) != ''?trim($user->data->user_nicename):trim($user->data->user_login);
        
        $link = site_url() . '/'.ST_PWD_REST.'/?email=' . $user_email . '&code=' . $code;
        $setup_sub =  MCC_NAME." - Password Reset Link";
        
        $innnerbody = 'Hi '.  ucfirst($first_name) .'<br/><br/>'
                . 'Your password reset link is <br/> <a href="'.$link.'">'.$link.'</a><br/>';
        
        $email_body = file_get_contents(site_url() . '/email/EMAIL-template-general.php');
        $body = str_replace('~~EMAIL_BODY~~', $innnerbody, $email_body);                
        $body = html_entity_decode($body);
        
        $email_template_body = email_template_body($body, $user_email, 'password_reset');       
        //$user_email = 'parambir@rudrainnovatives.com';
        @mail($user_email, $setup_sub, $email_template_body, mail_header(), mail_additional_parameters());
        insert_email_historical_report($user_id, 'Password Reset', $setup_sub, $user_email, 'Password Reset', current_id());
        
        json(1,'Password reset link has been sent successfully');
    }    
    else if ($_REQUEST["param"] == "company_info_update") {
        
        $company_name = isset($_POST['company_name'])?esc_attr($_POST['company_name']):'';
        $company_email = isset($_POST['company_email'])?esc_attr($_POST['company_email']):'';
        $street = isset($_POST['street'])?esc_attr($_POST['street']):'';
        $city = isset($_POST['city'])?esc_attr($_POST['city']):'';
        $state = isset($_POST['state'])?esc_attr($_POST['state']):'';
        $country = isset($_POST['country'])?esc_attr($_POST['country']):'';        
        $currency = isset($_POST['currency'])?esc_attr($_POST['currency']):'';
        $language = isset($_POST['language'])?esc_attr($_POST['language']):'';
        $description = isset($_POST['description'])?esc_attr($_POST['description']):'';
        $description = stripcslashes($description);
        
        update_option('blogname',$company_name);
        update_option('blogdescription',$description);
        update_option('admin_email',$company_email);
        
        $arr = array( 'company_name' => $company_name, 'company_email' => $company_email, 'street' => $street, 'city' => $city, 'state' => $state, 
            'country' => $country, 'currency' => $currency, 'language' => $language, 'description' => $description );
        $data = json_encode($arr);
        $now = date("Y-m-d H:i:s");
        
        $res = $wpdb->get_row
        (
            $wpdb->prepare
            (
                    "select id from " . client_company_info(),""                        
            )
        );

        if(!empty($res)){
                       
            $id = $res->id;
            $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . client_company_info() . " SET company_info = %s WHERE id = %d", 
                        $data, $id
                )
            );
        }
        else{

            $wpdb->query
            (
                $wpdb->prepare
                (
                        "INSERT INTO " . client_company_info() . " (company_info, created_dt) "
                        . "VALUES (%d, '%s')", 
                        $data, $now
                )
            );
        }
        
        json(1,'Company Info saved successfully.');
    }
    else if ($_REQUEST["param"] == "reset_password") {
        $email = isset($_POST['__email'])?esc_attr(trim($_POST['__email'])):'';
        $code = isset($_POST['__code'])?esc_attr(trim($_POST['__code'])):'';
        
        $user = get_user_by('email',$email);
        if(empty($user)){
            json(0,'Invalid Request');
        }    
        $user_id = $user->data->ID;
        $cod = $user->data->user_activation_key;
        if($cod != $code){
            json(0,'Invalid Request');
        }
        
        $newpassword = isset($_POST['newpassword'])?trim($_POST['newpassword']):'';
        $confirmnewpassword = isset($_POST['confirmnewpassword'])?trim($_POST['confirmnewpassword']):'';
        if($newpassword != ''){
            if($newpassword == $confirmnewpassword){
                
                //wp_set_password( $newpassword, $user_id );
                $usertbl = $wpdb->prefix."users";                
                $wpdb->query
                (
                    $wpdb->prepare
                    (
                        "UPDATE " . $usertbl . " SET user_pass = %s, user_activation_key = '' WHERE ID = %d",
                        md5($newpassword), $user_id
                    )
                );
                json(1,'Password successfully changed');
            }
            json(0,'Password mismatch with confirm password');
        }
        json(0,'Password must not be empty');
    }
    else if ($_REQUEST["param"] == "verify_code") {
        $location_id = isset($_POST['location_id'])?intval($_POST['location_id']):'';
        $loc = $wpdb->get_row
        (
            $wpdb->prepare
            (
                    "SELECT MCCUserId FROM " . client_location() . " WHERE id = %d",$location_id
            )
        );                                
        if(!empty($loc)){
            
            $locwebsite = get_user_meta($loc->MCCUserId, 'website', TRUE);
            $locwebsite = addhttp($locwebsite);
            
            require_once ABSPATH.'/wp-content/plugins/settings/php-webdriver/vendor/autoload.php';
            try {

                $browser = strtolower(get_browser_name($_SERVER['HTTP_USER_AGENT']));        
                $host = 'http://localhost:4444/wd/hub'; // this is the default
                $capabilities = \Facebook\WebDriver\Remote\DesiredCapabilities::phantomjs();
                $driver = Facebook\WebDriver\Remote\RemoteWebDriver::create($host, $capabilities, 50000, 50000);
                $driver->get($locwebsite);
                $sString = $driver->getPageSource();
                $driver->quit();
                set_time_limit(60000);
                $has_str = 0;
                if( strpos($sString, 'analytics/conv_tracking.php') !== false ){ 
                    $has_str = 1; // has code
                }
                
                $analytic_url = ANALYTICAL_URL;
                $analytic_url = str_replace(array('http://','https://'), array('',''), $analytic_url);
                $analytic_url = trim($analytic_url,'/');                
                
                $str2 = "['setSiteId', '".$loc->MCCUserId."']";
                $str3 = '["setSiteId","'.$loc->MCCUserId.'"]';                
                
                if( strpos($sString, $str2) !== false ){
                    if( strpos($sString, $analytic_url) !== false ){
                        $has_str = 2;
                    }
                }
                else if( strpos($sString, $str3) !== false ){ 
                    if( strpos($sString, $analytic_url) !== false ){
                        $has_str = 2;
                    }
                }
                
                if($has_str == 1){
                    json(0,'Not Verified!! Tracking Code is available on website, but not latest code. Please replace it with new code.');
                }
                else if($has_str == 2){
                    
                    $wpdb->query($wpdb->prepare('UPDATE '.client_location().' SET conv_verified = 1 WHERE id = %d',$location_id));
                    json(1,'Verified!!  Tracking Code is available and is working fine.');
                }
                else{
                    json(0,'Not Verified!!  Tracking Code is not available.');
                }
                

            } catch (Exception $ex) {
                json(0,'Something goes wrong. Please try agian.');
            }            
            
            
        }        
        json(0,'INvalid Location');
    }    
    else if ($_REQUEST["param"] == "user_edit") {
        global $wpdb;
        $type = $_POST['type'];
        
        $inactive = 0;
        $edit_user_id = $_POST['edit_user_id'];
        $wp_capabilities = array($_POST['role_name'] => 1);
        update_user_meta($edit_user_id, 'wp_capabilities', $wp_capabilities);
        update_user_meta($edit_user_id, 'first_name', $_POST['first_name']);
        update_user_meta($edit_user_id, 'last_name', $_POST['last_name']);
        update_user_meta($edit_user_id, 'phonenumber', $_POST['phonenumber']);
        update_user_meta($edit_user_id, 'streetaddress', $_POST['streetaddress']);
        update_user_meta($edit_user_id, 'city', $_POST['city']);
        update_user_meta($edit_user_id, 'state', $_POST['state']);
        update_user_meta($edit_user_id, 'zip', $_POST['zip']);

        if (trim($_POST['website']) != "") {
            update_user_meta($edit_user_id, 'website', $_POST['website']);
        }
        if (trim($_POST['BRAND_NAME']) != "") {
            update_user_meta($edit_user_id, 'BRAND_NAME', $_POST['BRAND_NAME']);
        }

        if ($_POST['role_name'] == 'canceled_user') {
            $inactive = 1;
        }
        
        
        if(isset($_POST['iswriter'])){
            if(trim(strtolower(get_user_meta($user_id, 'tag_type', TRUE))) == 'writer'){
                update_user_meta($user_id,'tag_type','writer');
            }
            else{
                add_user_meta($user_id,'tag_type','writer');
            }

        }
        else{
            delete_user_meta($user_id,'tag_type');
        }
        
        $succ_msg = 'Successfully Updated.';
        
        $pwd = isset($_REQUEST['pwd'])?trim($_REQUEST['pwd']):'';
        $cpwd = isset($_REQUEST['cpwd'])?trim($_REQUEST['cpwd']):'';
        
        if($pwd == $cpwd){
                        
            $res = $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE wp_users SET user_pass = %s WHERE ID = %d", 
                        md5($pwd), $edit_user_id
                )
            );                                    
                        
        }
        
        if ($inactive == 1) {
            $wp_capabilities = array('canceled_user' => 1);
            update_user_meta($edit_user_id, 'wp_capabilities', $wp_capabilities);

            ///*

            delete_user_meta($edit_user_id, 'send_enfusen_report');

            delete_user_meta($edit_user_id, 'send_report_from_name');

            delete_user_meta($edit_user_id, 'send_report_from_email');

            delete_user_meta($edit_user_id, 'send_report_to');

            delete_user_meta($edit_user_id, 'bcc_report_to');

            delete_user_meta($edit_user_id, 'onofftraffic');

            delete_user_meta($edit_user_id, 'onoffgoals');

            delete_user_meta($edit_user_id, 'onoffppc');

            delete_user_meta($edit_user_id, 'onoffadwords');

            delete_user_meta($edit_user_id, 'onoffctm');

            delete_user_meta($edit_user_id, 'onoffcitation');

            delete_user_meta($edit_user_id, 'onoffranking');

            delete_user_meta($edit_user_id, 'onoffrepranking');

            delete_user_meta($edit_user_id, 'onofftasks');

            $btl_campaign = get_user_meta($edit_user_id, "btl_campaign", true);

            if ($btl_campaign != '') {

                define('API_KEY', 'e9ac379e22500737efe4e1e0a700d84e304126d6');

                define('API_SECRET', '5216018119bf2');



                $expires = time() + 1800;

                $sig = base64_encode(hash_hmac('sha1', API_KEY . $expires, API_SECRET, true));

                $sig = rawurlencode($sig);

                $btl_thoken = "api-key=" . API_KEY . "&sig=" . $sig . "&expires=" . $expires . "";



                $client_url = "http://tools.brightlocal.com/seo-tools/api/v2/lsrc/delete?$btl_thoken&campaign-id=$btl_campaign";



                $ch = curl_init($client_url);

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $result_on = curl_exec($ch);

                delete_user_meta($edit_user_id, 'btl_campaign'); //96738 OKC D L
            }

            $yext_id = get_user_meta($edit_user_id, "yext_id", true);

            $yext_id = trim($yext_id);

            if ($yext_id != '') {

                define("YEXT_API", "4eyo0t3Jxg6yl5YhIo56");

                $subscriptions = 'https://api.yext.com/v1/customers/' . $yext_id . '/subscriptions?api_key=' . YEXT_API;

                $ch = curl_init($subscriptions);

                // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $result_on = curl_exec($ch);

                $all_subscriptions = json_decode($result_on);



                foreach ($all_subscriptions->subscriptions as $row_subs) {

                    $subscription_id = $row_subs->id;

                    $cancel_subscription_link = 'https://api.yext.com/v1/customers/' . $yext_id . '/subscriptions/' . $subscription_id . '?api_key=' . YEXT_API;

                    //echo $cancel_subscription_link; exit;

                    $data_string = '{"status": "CANCELED"}';

                    $ch = curl_init($cancel_subscription_link);

                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Length:" . strlen($data_string)));

                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                    curl_setopt($ch, CURLOPT_POST, TRUE);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $Val = curl_exec($ch);



                    $cancel_subscription_value = json_decode($Val);



                    $Subs = array();

                    $Subs["id"] = $cancel_subscription_value->id;

                    $Subs["paidThroughDate"] = $cancel_subscription_value->paidThroughDate;

                    $Subs["customerId"] = $cancel_subscription_value->customerId;

                    $Subs["status"] = $cancel_subscription_value->status;

                    $Subs["offerId"] = $cancel_subscription_value->offerId;

                    update_user_meta($edit_user_id, "Yext_User_Subscription", $Subs);
                }
            }

            $camps = get_user_meta($edit_user_id, 'campaign');

            $Campaigname = $camps[0]["name"];

            $Campaigid = $camps[0]["id"];

            if (!empty($Campaigid)) {

                $urlw = "https://app.linkemperor.com/api/v2/customers/campaigns/" . $Campaigid . ".json?api_key=272403f9a830d41c48f2700cd9f0ccf692b16223";

                $DeleteCam = curl_init();

                curl_setopt($DeleteCam, CURLOPT_URL, $urlw);

                curl_setopt($DeleteCam, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

                curl_setopt($DeleteCam, CURLOPT_CUSTOMREQUEST, "DELETE");

                curl_setopt($DeleteCam, CURLOPT_RETURNTRANSFER, TRUE);

                curl_setopt($DeleteCam, CURLOPT_SSL_VERIFYPEER, FALSE);

                $result = curl_exec($DeleteCam);

                curl_close($DeleteCam);



                delete_user_meta($edit_user_id, 'campaign');

                update_user_meta($edit_user_id, 'send_enfusen_report', 'noreport');
            }
            $BRand_name = get_user_meta($edit_user_id, "BRAND_NAME");

            $emailtemplate = 'Hi,<br/> ' . $BRand_name[0] . ' account has been inactivated. <br/>Client ID: #' . $edit_user_id . '<p><br>Thanks,<br/>The Marketing Control Center Team at Enfusen</p>';




            $email_arr = array();
            $email_arr[] = 'roger@enfusen.com';
            //$email_arr[] = 'tino@enfusen.com';
            foreach ($email_arr as $row_email) {
                if (email_subscription_setting($row_email, 'account_canceled') == 'Yes') {
                    $email_template_body = email_template_body($emailtemplate, $row_email, 'account_canceled');
                    @mail($row_email, $BRand_name[0] . ' Account has been Inactivated', $email_template_body, mail_header(), mail_additional_parameters());
                    insert_email_historical_report($edit_user_id, 'Account Inactivated', $BRand_name[0] . ' Account has been Inactivated', $row_email, $BRand_name[0] . ' Account has been Inactivated from user control', current_id());
                }
            }


            //@mail('roger@enfusen.com,tino@enfusen.com', $BRand_name[0] . ' Account has been Cancelled', $email_body, mail_header());
            // */
        }
        echo $succ_msg;
        die;
    }else if ($_REQUEST["param"] == "smtp_configuration") {

        /* Checking values already exists or not */
        $smtp = get_option("user_smtp");
        $smtp_username = get_option("user_smtp_username");
        $smtp_pwd = get_option("user_smtp_password");
        $smtp_port = get_option("user_smtp_port");
        $smtp_email_from = get_option("user_smtp_email_from");
        $smtp_email_from_name = get_option("user_smtp_from_name");
        $find_smtp_status = get_option("smtp_conf_status");

        /* Request Data */
        $req_smtp = $_REQUEST['smtp'];
        $req_smtp_username = $_REQUEST['txtUsername'];
        $req_smtp_password = $_REQUEST['txtPassword'];
        $req_smtp_email_from = $_REQUEST['txtEmailFrom'];
        $req_smtp_port = $_REQUEST['txtPortnumber'];
        $req_smtp_from_name = $_REQUEST['txtFromName'];
        $chk_smtp_status = $_REQUEST['chk_smtp'];

        if (!empty($find_smtp_status)) {
            update_option("smtp_conf_status", $chk_smtp_status);
        } else {
            add_option("smtp_conf_status", $chk_smtp_status);
        }

        if (!empty($smtp)) {
            update_option("user_smtp", $req_smtp);
        } else {
            add_option("user_smtp", $req_smtp);
        }
        if (!empty($smtp_username)) {
            update_option("user_smtp_username", $req_smtp_username);
        } else {
            add_option("user_smtp_username", $req_smtp_username);
        }
        if (!empty($smtp_pwd)) {
            update_option("user_smtp_password", $req_smtp_password);
        } else {
            add_option("user_smtp_password", $req_smtp_password);
        }
        if (!empty($smtp_port)) {
            update_option("user_smtp_port", $req_smtp_port);
        } else {
            add_option("user_smtp_port", $req_smtp_port);
        }
        if (!empty($smtp_email_from)) {
            update_option("user_smtp_email_from", $req_smtp_email_from);
        } else {
            add_option("user_smtp_email_from", $req_smtp_email_from);
        }
        if (!empty($smtp_email_from_name)) {
            update_option("user_smtp_from_name", $req_smtp_from_name);
        } else {
            add_option("user_smtp_from_name", $req_smtp_from_name);
        }

        json(1, "SMTP Configuration Changed Successfully");
    } else if ($_REQUEST["param"] == "test_connection_smtp") {

        $mailto = $_REQUEST['mdl_email'];
        $html = "<h4>Connection Status</h4><p>Connection Passed Successfully.</p>";
        $subject = 'SMTP Connection testing';
        $curl = curl_init();

        $curl_post_data = array(
            "uhost" => get_option("user_smtp"),
            "uport" => get_option("user_smtp_port"),
            "username" => get_option("user_smtp_username"),
            "user_pwd" => get_option("user_smtp_password"),
            "email_from" => get_option("user_smtp_email_from"),
            "email_to" => $mailto,
            "html_data" => $html,
            "subject" => $subject,
            "from_name" => get_option("user_smtp_from_name")
        );

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://admin.enfusen.com/smtp-connection/test/connection.php',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $curl_post_data
        ));
        $result_smtp_json = curl_exec($curl);

        $result_smtp = json_decode($result_smtp);

        $data = file($result_smtp);

        $response = $data[count($data) - 1];

        if (preg_match("/Email Sent/", $result_smtp_json)) {
            json(1, "Connected Successfully");
        } else {
            json(0, "Please Check your SMTP configuration");
        }
    } else if ($_REQUEST["param"] == "change_smtp_status") {

        $smtp_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : "disable";
        $find_smtp_status = get_option("smtp_conf_status");
        if (!empty($find_smtp_status)) {
            update_option("smtp_conf_status", $smtp_status);
        } else {
            add_option("smtp_conf_status", $smtp_status);
        }
    }
    
}

function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function get_browser_name($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    
    return 'Other';
}

function get_data( $url )
{
    $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(

        CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
        CURLOPT_POST           =>false,        //set to GET
        CURLOPT_USERAGENT      => $user_agent, //set user agent
        CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
        CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch  = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}

function deleteusermeta($user_id){
        
    global $wpdb;
    $meta_table = $wpdb->prefix."usermeta";
    $wpdb->query
    (
        $wpdb->prepare
        (
            "DELETE FROM $meta_table WHERE user_id = %d", $user_id
        )
    );
    
    wp_delete_user($user_id);
    
    // Analytic table delete
    $conn = conn_analytic();    
    if($conn != ''){
        $sql = "DELETE FROM clients_table WHERE MCCUserID = $user_id";
        mysqli_query($conn, $sql);
        
        $sql = "DROP TABLE short_analytics_$user_id";
        mysqli_query($conn, $sql);
        
        // delete dynamic created tables
        $ClientID = $user_id;
        include_once dirname(dirname(SET_COUNT_PLUGIN_DIR)) . '/themes/twentytwelve/analytics/DBUtils.php';            
        DeleteTableForClient(AnalyticsDataDBTableName, $ClientID);
        DeleteTableForClient(AnalyticsCacheDataDBTableName, $ClientID);
        DeleteTableForClient(ConvTrackingDBTableName, $ClientID);
        DeleteTableForClient(ConvTrackingCacheDBTableName, $ClientID);            
        DeleteTableForClient(ConvTrackingFilteredDBTableName, $ClientID);
        DeleteTableForClient(ConvTrackingUrlsDBTableName, $ClientID);
        DeleteTableForClient(ConvTrackJSCodeFileName, $ClientID);
        
        
    }
       
}

function user_location_all_email($locations,$user_id){
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    
    $site_name = get_option( 'blogname' );  
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
        
        $uuser = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
            )
        );
        if(!empty($uuser)){
            
            $site_url = site_url();            
            $email = $uuser->user_email;            
            $template = '<div>Hi {{username}},</div>
                        <div></div>
                        <div>
                        <div>Administrator gave you access for all locations at '.$site_url.'.</div>
                        <div>Login your account to know more.</div>
                        <div></div>
                        Thanks,
                        {{site_name}}

                        </div>';            
            $subj = 'All Locations Assigned at '.$site_url;             
                  
            $msg = $template; 
            $msg = str_replace(array('{{username}}','{{location_name}}','{{site_name}}'),
                    array($uuser->display_name,$location_name,$site_name), $msg);
                 
            /****  Code commented ***/
            //custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    }
    
}

function user_location_add_email($location_id,$user_id){
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $location = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT * FROM ". client_location() . " WHERE id = %d",
            $location_id
        )
    );
    
    $location_name = get_user_meta($location->MCCUserId,'website',true);
    $site_name = get_option( 'blogname' );  
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
            )
        );
        if(!empty($uuser)){
            $email = $uuser->user_email;
            
            $template = '<div>Hi {{username}},</div>
                        <div></div>
                        <div>
                        <div>You have added in new location {{location_name}}.</div>
                        <div>Login your account to know more.</div>
                        <div></div>
                        Thanks,
                        {{site_name}}

                        </div>';            
            $subj = 'New Location assigned';             
                  
            $msg = $template; 
            $msg = str_replace(array('{{username}}','{{location_name}}','{{site_name}}'),
                    array($uuser->display_name,$location_name,$site_name), $msg);
                 
            /****  Code commented ***/
            //custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    }
}

function user_location_remove_email($location_id,$user_id){
     global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $location = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT * FROM ". client_location() . " WHERE id = %d",
            $location_id
        )
    );
    
    $location_name = get_user_meta($location->MCCUserId,'website',true);
    
    $site_name = get_option( 'blogname' );  
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
                $wpdb->prepare
                (
                        "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
                )
        );
        if(!empty($uuser)){
            $email = $uuser->user_email;
            
            $template = '<div>Hi {{username}},</div>
                        <div></div>
                        <div>
                        <div>You have removed from location {{location_name}}.</div>
                        <div>Login your account to know more.</div>
                        <div></div>
                        Thanks,
                        {{site_name}}

                        </div>';            
            $subj = 'Remove From Assigned Location';             
                  
            $msg = $template; 
            $msg = str_replace(array('{{username}}','{{location_name}}','{{site_name}}'),
                    array($uuser->display_name,$location_name,$site_name), $msg);
            
            /****  Code commented ***/
            //custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    }
}

function json($sts,$msg,$arr = array()){
    $ar = array('sts'=>$sts,'msg'=>$msg,'arr'=>$arr);
    print_r(json_encode($ar));
    die;
}
function custom_mail_header($fromcntmail = 'enfusen.com') {
        $additional_parameters = '-f notifications@enfusen.com';
        return "Reply-To: $fromcntmail\r\n"
                . "Return-Path: MCC <notifications@" . $fromcntmail . ">\r\n"
                . "From: Enfusen Notifications <notifications@" . $fromcntmail . ">\r\n"
                . "Return-Receipt-To: notifications@" . $fromcntmail . "\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-type: text/html\r\n"
                . "X-Priority: 3\r\n"
                . "X-Mailer: PHP" . phpversion() . "\r\n";                
    }

function custom_mail($user_email,$setup_sub,$body,$email_type,$reason){        
    $email_template_body = email_template_body($body, $user_email, $email_type);
    @mail($user_email, $setup_sub, $email_template_body, custom_mail_header(), mail_additional_parameters());
    insert_email_historical_report(user_id(), $email_type, $setup_sub, $user_email, $reason, current_id());    
}


function parent_api_call($url,$params = array()){
    
    $params['action'] = 'rudra_api';
    
    foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
          $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec ($ch);
    curl_close ($ch);
    return $output;
}

function conn_analytic(){
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

function updateSubscription(){
    $subsc_action = "delete";
    require(SET_COUNT_PLUGIN_DIR.'/views/updateSubscription.php');  //Update Recurring Payment & $subsc_action use in this file
    //echo $resultsnew;
    /*
    if($resultsnew == 'success'){
        global $wpdb;
        include_once ABSPATH . "wp-content/plugins/settings/get_location_package_prices.php";
        $get_package_fields = $wpdb->get_results("SELECT * FROM `wp_location_package_fields`");
        foreach($get_package_fields as $get_package_field){
            $new_limit = $limit_increase = 0;
            $field_id = $get_package_field->lpf_id;
            $field_name = $get_package_field->lpf_field;
            $old_limit = $get_package_field->lpf_limit;

            if($field_name == 'keywords'){
                $limit_increase = $locations_package_prices->lp_key_range;
            }elseif($field_name == 'comp_keywords'){
                $limit_increase = $locations_package_prices->lp_ckey_range;
            }elseif($field_name == 'keyword_opp'){
                $limit_increase = $locations_package_prices->lp_keyo_range;
            }elseif($field_name == 'pages'){
                $limit_increase = $locations_package_prices->lp_page_range;
            }elseif($field_name == 'site_audit'){
                $limit_increase = $locations_package_prices->lp_audit_range;
            }elseif($field_name == 'citation_run'){
                $limit_increase = $locations_package_prices->lp_citation_range;
            }

            $new_limit = $old_limit - $limit_increase;

            $wpdb->query("UPDATE `wp_location_package_fields` SET `lpf_limit` = '".$new_limit."' WHERE `lpf_id` = '".$field_id."'");
        }

    }
    */
}
?>
