<?php

global $wpdb;
$db_name = isset($_REQUEST['db_name']) ? htmlspecialchars(trim($_REQUEST['db_name'])) : '';
$param = isset($_REQUEST['param']) ? htmlspecialchars(trim($_REQUEST['param'])) : '';
$token = isset($_REQUEST['token']) ? htmlspecialchars(trim($_REQUEST['token'])) : '';
$reqtype = isset($_REQUEST['reqtype']) ? htmlspecialchars(trim($_REQUEST['reqtype'])) : '';
$siteurl = isset($_REQUEST['siteurl']) ? trim($_REQUEST['siteurl']) : '';

if ($reqtype == 'module') {
      
    
    if ($param == 'get_query_result') {
        $query = isset($_REQUEST['query']) ? trim($_REQUEST['query']) : '';
        if ($query == '') {
            json(1, 'Empty Query');
        }
        $query = stripcslashes($query);
        $rtype = isset($_REQUEST['rtype']) ? trim($_REQUEST['rtype']) : '';
        if ($rtype == 'row')
            $result = $wpdb->get_row($query);
        else if ($rtype == 'var')
            $result = $wpdb->get_var($query);
        else
            $result = $wpdb->get_results($query);

        json(1, 'Query result', $result);
    }
    else if ($param == 'get_orders') { 
        $writer_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';    
        $type = isset($_REQUEST['typord']) ? trim($_REQUEST['typord']) : '';
        $agency_id = isset($_REQUEST['agency_id']) ? trim($_REQUEST['agency_id']) : '';
        $result = getOrderTemplate('pages/order-list.php',$writer_id,$type,$siteurl,$agency_id);        
    }
    else if ($param == 'order_n_feedback') { 
        
        $query1 = isset($_REQUEST['query1']) ? trim($_REQUEST['query1']) : '';
        $query1 = stripcslashes($query1);
        $result1 = $wpdb->get_row($query1);
        $feed_uid = 0;
        if(!empty($result1)){
            $feed_uid = $result1->user_id;
        }        
        $query2 = isset($_REQUEST['query2']) ? trim($_REQUEST['query2']) : '';
        $query2 = stripcslashes($query2);
        $query2 = str_replace('{{feedback_uid}}', $feed_uid, $query2);
        $result2 = $wpdb->get_row($query2);
        $result = array('order_info' => $result1, 'existing_feedback' => $result2);
        
    }
    else if ($param == 'save_feedback') { 
        
        $feedback_insert = isset($_POST['feedback_insert']) ? ($_POST['feedback_insert']) : '';
        parse_str($feedback_insert, $feedback_insert);
        $result = $wpdb->insert('wp_feedback', $feedback_insert);        
        $result = '<div class="success_c">Successfully given your feedback.</div><div class="clear_both"></div>';
        
    }
    else if ($param == 'content_detail') { 
        $writer_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';    
        $type = isset($_REQUEST['typord']) ? trim($_REQUEST['typord']) : '';
        $agency_id = isset($_REQUEST['agency_id']) ? trim($_REQUEST['agency_id']) : '';
        $result = getPageTemplate('pages/content.php',$_REQUEST);         
    }
    else if ($param == 'save_content') {             
        $result = savecotent($_REQUEST);
    }    
    
    json(1, 'Result', $result);
    
}

function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

function getPageTemplate($file,$post_data) {
    global $wpdb;
    ob_start(); // start output buffer        
    $file = SET_COUNT_PLUGIN_DIR.'/library/'.$file;
        
    include $file;
        
    $template = ob_get_contents(); // get contents of buffer    
    ob_end_clean();
    return $template;

}

function getOrderTemplate($file,$writer_id,$type,$siteurl,$agency_id) {
    global $wpdb;
    ob_start(); // start output buffer        
    $file = SET_COUNT_PLUGIN_DIR.'/library/'.$file;
        
    include $file;
        
    $template = ob_get_contents(); // get contents of buffer    
    ob_end_clean();
    return $template;

}

function savecotent($post_data){
        
    global $wpdb;
    $data['content'] = str_replace(array('\"', "\'"), array('"', "'"), $post_data['content']);
    //$data['optimized'] = $post_data['optimized'];
    $data['live_date'] = $post_data['live_date'];
    $data['post_url'] = $post_data['post_url'];
    $data['status'] = $post_data['status'];
    $data['link_stets'] = serialize(json_decode(stripcslashes($post_data['link_stets'])));
    
    $data['status_date'] = date('Y-m-d H:i:s');

    $data['opt_keyword_in_title'] = isset($post_data['opt_keyword_in_title']) ? 1 : 0;
    $data['opt_header_tags'] = isset($post_data['opt_header_tags']) ? 1 : 0;
    $data['opt_keyword_in_body'] = isset($post_data['opt_keyword_in_body']) ? 1 : 0;
    $data['opt_landing_ink'] = isset($post_data['opt_landing_ink']) ? 1 : 0;
    $data['opt_home_link'] = isset($post_data['opt_home_link']) ? 1 : 0;
    $data['opt_spellcheck'] = isset($post_data['opt_spellcheck']) ? 1 : 0;
    
    $order_id = isset($post_data['order_id']) ? trim($post_data['order_id']) : 0;
    $filedata = $post_data['img_data'];
    
    if (isset($post_data['file']) && $post_data['file'] != '') {
        $info_temp = pathinfo($post_data['file']);
        $ext = 'png';
        $productName = $order_id . '.' . $ext;

        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
            $file_name_basename_temp = $info_temp['basename'];
            $newname = ABSPATH."/wp-content/uploads/content_image/" . $productName;
            $image['image_name'] = $productName;
            $filedata = base64_decode($filedata);
            file_put_contents($newname, $filedata);
            $data['image_name'] = $productName;
        }
        
    }
    
    $wpdb->update('wp_content_order', $data, array('order_id' => $order_id));
    //*
    $sql = "SELECT * FROM wp_content_order WHERE order_id =" . $order_id;
    $order = $wpdb->get_row($sql); //Need it both two space
    
    $Wsubject = 'Content has submitted of order #' . $order_id;
    $success_msg = 'Your content has been saved successfully!';
    if ($post_data['status'] == 'Delivered') {
        if ($order->request_changes != "") {
            $Wsubject = 'Content has updated after your request changes of order #' . $order_id;
        }

        //pr($order);
        //*

        $email_body = file_get_contents(site_url() . '/email/content_submit.php');
        $body = html_entity_decode($email_body);
        $body = str_replace('~~TODAYS_DATE~~', date("d M Y", time()), $body);
        $body = str_replace('~~LAST_ID~~', $order_id, $body);
        $body = str_replace('~~USER_ID~~', $order->worker_id, $body);
        $body = str_replace('~~OPTIMIZED~~', $post_data['optimized'], $body);
        if ($post_data['live_date'] != "") {
            $body = str_replace('~~LIVE_DATE~~', date("d M Y", strtotime($post_data['live_date'])), $body);
        } else {
            $body = str_replace('~~LIVE_DATE~~', '', $body);
        }
        $body = str_replace('~~POST_URL~~', $post_data['post_url'], $body);
        //$body = str_replace('~~CONTENT~~', $post_data['content'], $body);
        $body = str_replace('~~BRAND_NAME~~', brand_name($order->worker_id), $body);
        $body = str_replace('~~ORDER_DETAILS_LINK~~', site_url() . '/order-content/?type=view-content&order_id=' . $order_id, $body);
        $role_worker_list = role_worker_list($order->worker_id, $CSM_role_id);
        
        $ins_notification['order_id'] = $order_id;
        $ins_notification['client_id'] = $order->worker_id;
        $ins_notification['sender_user_id'] = $order->writer_id;
        $ins_notification['type'] = 'content_submit';
        $ins_notification['created_date'] = date('Y-m-d H:i:s', time());
        $ins_notification['receiver_user_id'] = $order->user_id; //Changed for agency users, to access notification fo location
        //$ins_notification['receiver_role_id'] = $order->user_id;
        $ins_notification['subject'] = $Wsubject;
        $ins_notification['message'] = spec_rep(str_replace('\"', '"', $body));

        $sql = "SELECT notification_id FROM `wp_notification` WHERE `type` = 'content_submit' AND `order_id` = $order_id";
        $check_order_content_submit = $wpdb->get_row($sql);

        if (notification_setting($role_worker_list[0], 'content_submit') == 'On') {
            if (empty($check_order_content_submit)) {
                $wpdb->insert('wp_notification', $ins_notification);
            } else {
                $ins_notification['read'] = 0;
                $ins_notification['status'] = 'inbox';
                $wpdb->update('wp_notification', $ins_notification, array('notification_id' => $check_order_content_submit->notification_id));
            }
        }
}
$success_msg = 'Your content has been submitted, Thank You!';
  return $success_msg;  
}

?>