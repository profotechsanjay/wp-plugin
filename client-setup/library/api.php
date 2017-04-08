<?php

global $wpdb;
$db_name = isset($_POST['db_name']) ? htmlspecialchars(trim($_POST['db_name'])) : '';
$param = isset($_POST['param']) ? htmlspecialchars(trim($_POST['param'])) : '';
$token = isset($_POST['token']) ? htmlspecialchars(trim($_POST['token'])) : '';
$reqtype = isset($_POST['reqtype']) ? htmlspecialchars(trim($_POST['reqtype'])) : '';
$siteurl = isset($_POST['siteurl']) ? trim($_POST['siteurl']) : '';

if ($reqtype == 'module') {
    
    $agency_id = $wpdb->get_var
        (
        $wpdb->prepare
                (
                "SELECT client_id FROM " . setup_table() . " WHERE db_name = %s ", $db_name
        )
    );
    if ($agency_id == 0) {
        json(0, 'Invalid Client');
    }
    $param = trim($param);
    
    if ($param == 'get_query_result') {
        $query = isset($_POST['query']) ? trim($_POST['query']) : '';
        if ($query == '') {
            json(1, 'Empty Query');
        }
        $query = stripcslashes($query);
        $rtype = isset($_POST['rtype']) ? trim($_POST['rtype']) : '';
        if ($rtype == 'row')
            $result = $wpdb->get_row($query);
        else if ($rtype == 'var')
            $result = $wpdb->get_var($query);
        else
            $result = $wpdb->get_results($query);

        json(1, 'Query result', $result);
    }
    else if ($param == 'agency_id') {        
        json(1, 'agency_id',$agency_id);    
    }
    else if ($param == 'order_notify') {        
        
        $user_id = $agency_id;
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : '';
        $selected_writer = isset($_POST['writer_id']) ? intval($_POST['writer_id']) : '';
        $ins_notification = isset($_POST['ins_notification']) ? ($_POST['ins_notification']) : '';
        parse_str($ins_notification, $ins_notification);
        //$ins_notification = explode("{{{exp}}}", $ins_notification);
        $body = isset($_POST['body']) ? esc_attr($_POST['body']) : '';
        $Wsubject = isset($_POST['Wsubject']) ? esc_attr($_POST['Wsubject']) : '';
        $re = '';
        
        if (trim(strtolower(notification_setting($selected_writer, 'order_create'))) == 'on') {
                                  
            $re = $wpdb->insert('wp_notification', $ins_notification);                        
        }
        
        if (email_subscription_setting(user_email($selected_writer), 'order_create') == 'Yes') {
            $email_template_body = email_template_body($body, user_email($selected_writer), 'order_create');
            $email = user_email($selected_writer);
            //$email = 'parambir@rudrainnovatives.com';
            @mail($email, $Wsubject, $email_template_body, mail_header(), mail_additional_parameters());
            insert_email_historical_report($client_id, 'Order Create', $Wsubject, user_email($selected_writer), 'Create a new Order', $user_id);
        }
        if($re == ''){
            $re = $ins_notification;
        }
        json(1, 'res: ',$re ); 
    }
    else if ($param == 'order_cancel') {
        
        $user_id = $agency_id;
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : '';
        $writer_id = isset($_POST['writer_id']) ? intval($_POST['writer_id']) : '';
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : '';        
        $ins_notification = isset($_POST['ins_notification']) ? ($_POST['ins_notification']) : '';
        parse_str($ins_notification, $ins_notification);
        
        $body = isset($_POST['body']) ? esc_attr($_POST['body']) : '';
        $mail_subject = isset($_POST['mail_subject']) ? esc_attr($_POST['mail_subject']) : '';
        $canceled_reason = isset($_POST['canceled_reason']) ? esc_attr($_POST['canceled_reason']) : '';
        
        if (notification_setting($writer_id, 'canceled_order') == 'On') {
                                    
            $wpdb->insert('wp_notification', $ins_notification);
        }
        
        $body = str_replace('~~ORDER_DETAILS_LINK~~', site_url() . '/content-admin?type=content&agency_id='.$agency_id.'&order_id=' . $order_id, $body);
        if (email_subscription_setting(user_email($writer_id), 'canceled_order') == 'Yes') {
            $email_template_body = email_template_body($body, user_email($writer_id), 'canceled_order');
            $email = user_email($writer_id);
            //$email = 'parambir@rudrainnovatives.com';
            @mail($email, $mail_subject, $email_template_body, mail_header(), mail_additional_parameters());
            insert_email_historical_report($user_id, 'Canceled Order', $mail_subject, user_email($writer_id), $canceled_reason, $user_id);
        }
        
        
    }
    else if ($param == 'change_notify') {
        
        $user_id = $agency_id;
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : '';
        $writer_id = isset($_POST['writer_id']) ? intval($_POST['writer_id']) : '';
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : '';        
        $ins_notification = isset($_POST['ins_notification']) ? ($_POST['ins_notification']) : '';
        parse_str($ins_notification, $ins_notification);
        
        $body = isset($_POST['body']) ? esc_attr($_POST['body']) : '';
        $mail_subject = isset($_POST['mail_subject']) ? esc_attr($_POST['mail_subject']) : '';       
                
//        $sql = "SELECT notification_id FROM `wp_notification` WHERE `type` = 'request_changes' AND `order_id` = $order_id";
//        $check_order_content_submit = $wpdb->get_row($sql);
//        
        if (notification_setting($writer_id, 'request_changes') == 'On') {
            $wpdb->insert('wp_notification', $ins_notification);
        }
        
        if (email_subscription_setting(user_email($writer_id), 'request_changes') == 'Yes') {
            $email_template_body = email_template_body($body, user_email($writer_id), 'request_changes');
            $email = user_email($writer_id);
            //$email = 'parambir@rudrainnovatives.com';
            @mail($email, $mail_subject, $email_template_body, mail_header(), mail_additional_parameters());            
            insert_email_historical_report($user_id, 'Request Changes', $mail_subject, user_email($writer_id), 'Request changes of a order', $user_id);
        }
        
        
    }
    else if ($param == 'approve_notify') {
        
        $user_id = $agency_id;
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : '';
        $writer_id = isset($_POST['writer_id']) ? intval($_POST['writer_id']) : '';
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : '';        
        $ins_notification = isset($_POST['ins_notification']) ? ($_POST['ins_notification']) : '';
        parse_str($ins_notification, $ins_notification);
        
        $body = isset($_POST['body']) ? esc_attr($_POST['body']) : '';
        $mail_subject = isset($_POST['mail_subject']) ? esc_attr($_POST['mail_subject']) : '';
                
        if (notification_setting($writer_id, 'approved_order') == 'On') {        
           $wpdb->insert('wp_notification', $ins_notification);
        }
        

       if (email_subscription_setting($writer_id, 'approved_order') == 'Yes') {
                        
            $email_template_body = email_template_body($body, user_email($writer_id), 'approved_order');
            $email = user_email($writer_id);
            //$email = 'parambir@rudrainnovatives.com';
            
            @mail($email, $mail_subject, $email_template_body, mail_header(), mail_additional_parameters());            
            insert_email_historical_report($user_id, 'Approved Order', $mail_subject, user_email($writer_id), 'Notification email after Approved Order', $user_id);
        }
        
    }
    else if ($param == 'full_name') {        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : '';
        $name = '';
        if (role($user_id) == 'subscriber') {

            if (trim(brand_name($user_id)) != "") {

                $name = brand_name($user_id);
            }
        }

        $name = get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true);
        
        json(1, 'name',$name);
        
    }
    else if ($param == 'feedback_submit') {
        
        $writer_id = isset($_REQUEST['writer_id'])?$_REQUEST['writer_id']:0;
        $user_id = $agency_id;
        $location_id = $UserID = isset($_POST['location_id']) ? intval($_POST['location_id']) : '';
        $brand_name = isset($_POST['brand_name']) ? ($_POST['brand_name']) : '';
        
        $feedback_insert['order_id'] = $feedback_order_id = isset($_REQUEST['feedback_order_id'])?$_REQUEST['feedback_order_id']:0;
        $feedback_insert['sender_user_id'] = $agency_id;
        $feedback_insert['receiver_user_id'] = $writer_id;
        $feedback_insert['logged_in_user_id'] = $writer_id;
        $feedback_insert['rating'] = $_REQUEST['rating'];
        $feedback_insert['comments'] = $_REQUEST['comments'];
        $feedback_insert['created_date'] = date("Y-m-d H:i:s");
        $wpdb->insert('wp_feedback', $feedback_insert);

        $email_body = file_get_contents(site_url() . '/email/feedback_to_writer.php');
        $body = html_entity_decode($email_body);
        $body = str_replace('~~WRITER_NAME~~', full_name($writer_id), $body);
        $body = str_replace('~~BRAND_NAME~~', $brand_name, $body);
        $body = str_replace('~~ORDER_ID~~', $feedback_order_id, $body);
        $body = str_replace('~~RATTING~~', star_image_loop($_REQUEST['rating']), $body);
        $body = str_replace('~~COMMENTS~~', orginal_html($_REQUEST['comments']), $body);
        //$body = str_replace('~~NOTES~~', orginal_html($mail_task_info->account_notes), $body);
        $body = str_replace('~~NOTES~~', '', $body);
        $body = str_replace('~~FEEDBACK_GIVEN_LINK~~', site_url() . '/content-admin/?type=feedback&agency_id='.$agency_id.'&feedback_order_id=' . $feedback_order_id, $body);

        
        
        $ins_notification['client_id'] = $user_id;
        $ins_notification['sender_user_id'] = $user_id;
        $ins_notification['receiver_user_id'] = $writer_id;
        $ins_notification['created_date'] = date('Y-m-d H:i:s', time());
        $ins_notification['type'] = 'feedback_sent_to_writer';
        $ins_notification['subject'] = $brand_name . ' has given a feedback to you. Order ID #' . $feedback_order_id;
        $ins_notification['message'] = $body;
        $wpdb->insert('wp_notification', $ins_notification);
        
        $result = '<div class="success_c">Successfully given your feedback.</div><div class="clear_both"></div>';
    }
    else if ($param == 'uemail') {        
        
        global $wpdb;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : '';        
        $result = $wpdb->get_row("SELECT user_email FROM `wp_users` WHERE `ID` = $user_id");
        $email = strtolower(trim($result->user_email));        
        json(1, 'email',$email);
        
    }
    else if ($param == 'get_writers_list_byids') { 
        $listids = isset($_POST['listids']) ? trim($_POST['listids']) : '';
        $writer_id = isset($_POST['writer_id']) ? trim($_POST['writer_id']) : '';        
        $result = getwritesbyid($listids, $writer_id);
        
    }
    else if ($param == 'check_feedback') { 
        
        $query = isset($_REQUEST['query']) ? trim($_REQUEST['query']) : '';
        $query = stripcslashes($query);
        $query = str_replace('{{agency_id}}', $agency_id, $query);        
        $result = $wpdb->get_row($query);
    }
    else if ($param == 'write_search_page') {        
        $result = getContentTemplate('pages/writer-search.php',$agency_id);
    }
    else if ($param == 'user_profile_page') {
        $result = getContentTemplate('pages/profile_page.php',$agency_id);           
    }
    else if ($param == 'order_page') {
        $result = getContentTemplate('pages/order.php',$agency_id);           
    }
    else if ($param == 'write_img_name') {
        $writer_id = isset($_POST['writer_id']) ? trim($_POST['writer_id']) : '';
        get_user_meta($writer_id, 'user_photo', true);
        if (trim($worker_image) == "") {
               $worker_image = 'default.png';
        }
        $img = site_url().'/wp-content/uploads/people/'.$worker_image;        
        $name = full_name($writer_id);
        $result = array('img' => $img, 'name' => $name);        
    }
    json(1, 'Result', $result);
    
} else {
    
    $setup = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT db_name,db_username,db_password FROM " . setup_table() . " WHERE db_name = %s ", $db_name
            )
    );
    if (empty($setup)) {
        json(0, 'Invalid Client');
    }

    $servername = DB_HOST;
    $db_name = $setup->db_name;
    $db_user = $setup->db_username;
    $db_password = base64_decode(base64_decode($setup->db_password));
    $conn = new mysqli($servername, $db_user, $db_password, $db_name);
    if ($conn->connect_error) {
        json(0, 'Error MySqli', $conn->connect_error);
    }

    $sql = "SELECT * FROM super_tokens WHERE token = '" . $token . "'";
    $result = mysqli_query($conn, $sql);

    $row = $result->fetch_array(MYSQLI_NUM);
    if (empty($row)) {
        json(0, 'Invalid Token');
    }

    if ($param == 'get_client_info') {
        json(1, '', $setup);
    } else if ($param == 'get_white_label_clients') {

        $is_white_label = isset($_POST['is_white_label']) ? intval($_POST['is_white_label']) : 0;
        $white_label_url = isset($_POST['white_label_url']) ? htmlspecialchars(trim($_POST['white_label_url'])) : '';
        if ($is_white_label == 0) {
            $white_label_url = '';
        }

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . setup_table() . " SET white_lbl = %s WHERE db_name = %s ", $white_label_url, $db_name
                )
        );

        $setups = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setup_table()." WHERE white_lbl != %s", ''
                )
        );
//        $ar_to_send = array();
//        foreach ($setups as $setp) {
//
//            $db_name = $setp->db_name;
//            if ($db_name == '' || empty($db_name)) {
//                continue;
//            }
//            $db_user = $setp->db_username;
//            $db_password = base64_decode(base64_decode($setp->db_password));
//            $con = new mysqli($servername, $db_user, $db_password, $db_name);
//            if ($con->connect_error) {
//                continue;
//            }
//
//            $sql = "SELECT * FROM wp_client_company_info";
//            $result = mysqli_query($con, $sql);
//            if (isset($result) && !empty($result)) {
//                $row = $result->fetch_object();
//                if (isset($row) && !empty($row)) {
//                    if ($row->is_white_label == 1) {
//                        $setp->white_url = $row->white_label_url;
//                        array_push($ar_to_send, $setp);
//                    }
//                }
//            }
//        }

        json(1, '', $setups);
        $con->close();
    }

    $conn->close();
}

function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

function getContentTemplate($file,$agency_id) {
    global $wpdb;
    ob_start(); // start output buffer    
    $commonfiletop = ST_COUNT_PLUGIN_DIR.'/library/pages/commonvars.php';    
    $file = ST_COUNT_PLUGIN_DIR.'/library/'.$file;
    
    include $commonfiletop;
    include $file;
    
    
    $template = ob_get_contents(); // get contents of buffer    
    ob_end_clean();
    return $template;

}

function getwritesbyid($listids, $writer_id){
    
    $all_writer_list = explode(",", $listids);
    
    if(empty($all_writer_list)){
        return '';
    }
    else{
        
        $list = '';
        foreach ($all_writer_list as $row_uid) {
            if ($writer_id == $row_uid){
                $list .='<option selected = "selected" value="'.$row_uid.'">'.full_name($row_uid).'</option>';
            }
            else{
                $list .='<option value="'.$row_uid.'">'.full_name($row_uid).'</option>';
            }
            
        }
        return $list;
    }
}