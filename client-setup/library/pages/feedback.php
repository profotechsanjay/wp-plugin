<?php

$succ_msg = '';
$feedback_order_id = $_GET['feedback_order_id'];
$feedback_order_info = $wpdb->get_row("SELECT * FROM wp_content_order WHERE order_id = $feedback_order_id");

$check_existing_feedback = $wpdb->get_row("SELECT * FROM `wp_feedback` WHERE `order_id` = $feedback_order_id && `sender_user_id` = $UserID && `receiver_user_id` = $feedback_order_info->writer_id");
if (!empty($check_existing_feedback)) {
    wp_redirect(site_url() . "/order-content?type=delivery");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback_btn'])) {
    $feedback_insert['order_id'] = $feedback_order_id;
    $feedback_insert['sender_user_id'] = $UserID;
    $feedback_insert['receiver_user_id'] = $feedback_order_info->writer_id;
    $feedback_insert['logged_in_user_id'] = current_id();
    $feedback_insert['rating'] = $_POST['rating'];
    $feedback_insert['comments'] = $_POST['comments'];
    $feedback_insert['created_date'] = date("Y-m-d H:i:s");
    $wpdb->insert('wp_feedback', $feedback_insert);

    $email_body = file_get_contents(site_url() . '/email/feedback_to_writer.php');
    $body = html_entity_decode($email_body);
    $body = str_replace('~~WRITER_NAME~~', full_name($feedback_order_info->writer_id), $body);
    $body = str_replace('~~BRAND_NAME~~', brand_name($feedback_order_info->user_id), $body);
    $body = str_replace('~~ORDER_ID~~', $feedback_order_id, $body);
    $body = str_replace('~~RATTING~~', star_image_loop($_POST['rating']), $body);
    $body = str_replace('~~COMMENTS~~', orginal_html($_POST['comments']), $body);
    $body = str_replace('~~NOTES~~', orginal_html($mail_task_info->account_notes), $body);
    $body = str_replace('~~FEEDBACK_GIVEN_LINK~~', site_url() . '/content-admin/?type=feedback&feedback_order_id=' . $feedback_order_id, $body);


    $ins_notification['client_id'] = $feedback_order_info->user_id;
    $ins_notification['sender_user_id'] = current_id();
    $ins_notification['receiver_user_id'] = $feedback_order_info->writer_id;
    $ins_notification['created_date'] = date('Y-m-d H:i:s', time());
    $ins_notification['type'] = 'feedback_sent_to_writer';
    $ins_notification['subject'] = brand_name($feedback_order_info->user_id) . ' has given a feedback to you. Order ID #' . $feedback_order_id;
    $ins_notification['message'] = $body;
    $wpdb->insert('wp_notification', $ins_notification);

    $succ_msg = '<div class="success_c">Successfully given your feedback.</div><div class="clear_both"></div>';
}
$feed_back_to = 'Writer';
$back_location = site_url() . "/order-content/?type=delivery";
include_once('feedback-common-part.php');
?>
