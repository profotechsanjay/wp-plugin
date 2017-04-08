<?php

$succ_msg = '';
$feedback_order_id = $_GET['feedback_order_id'];
$feedback_order_info = $wpdb->get_row("SELECT * FROM wp_content_order WHERE order_id = $feedback_order_id");

$check_existing_feedback = $wpdb->get_row("SELECT * FROM `wp_feedback` WHERE `order_id` = $feedback_order_id && `sender_user_id` = $writer_id && `receiver_user_id` = $feedback_order_info->user_id");
if(!empty($check_existing_feedback)){
    wp_redirect(site_url()."/content-admin");exit;  
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback_btn'])) {
    $feedback_insert['order_id'] = $feedback_order_id;
    $feedback_insert['sender_user_id'] = current_id();
    $feedback_insert['receiver_user_id'] = $feedback_order_info->user_id;
    $feedback_insert['logged_in_user_id'] = current_id();
    $feedback_insert['rating'] = $_POST['rating'];
    $feedback_insert['comments'] = $_POST['comments'];
    $feedback_insert['created_date'] = date("Y-m-d H:i:s");
    $wpdb->insert('wp_feedback', $feedback_insert);
    $succ_msg = '<div class="success_c">Successfully given your feedback.</div><div class="clear_both"></div>';
}
$feed_back_to = 'Client';
$back_location = site_url()."/content-admin";
?>
