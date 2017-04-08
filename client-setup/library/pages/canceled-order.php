<?php
$order_id = $_GET['order_id'];

$content = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'content_order WHERE order_id = ' . $order_id . ' && user_id = ' . $UserID);
$order_canceled_permission = 0;
if (check_enfusen_worker(1)) {
    $order_canceled_permission = 1;
} else if (administrator_permission()) {
    $order_canceled_permission = 1;
}
if (empty($content) || $order_canceled_permission == 0) {
    wp_redirect(site_url() . '/order-content/?type=delivery');
}

$success_msg = '';




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['canceled_order_btn'])) {

        $canceled_reason = $_POST['canceled_reason'];

        $mail_subject = 'Order ID #' . $order_id . ' has Canceled';
        $email_body = file_get_contents(site_url() . '/email/order_canceled.php');
        $body = html_entity_decode($email_body);
        $body = str_replace('~~WRITER_NAME~~', display_name($content->writer_id), $body);
        $body = str_replace('~~LAST_ID~~', $order_id, $body);
        $body = str_replace('~~BRAND_NAME~~', brand_name($UserID), $body);
        $body = str_replace('~~KEYWORD~~', $content->keys, $body);
        $body = str_replace('~~SITES~~', $content->sites, $body);
        $body = str_replace('~~CANCELED_REASON~~', $canceled_reason, $body);
        $body = str_replace('~~ORDER_DETAILS_LINK~~', site_url() . '/content-admin?type=content&order_id=' . $order_id, $body);

        if (email_subscription_setting(user_email($content->writer_id), 'canceled_order') == 'Yes') {
            $email_template_body = email_template_body($body, user_email($content->writer_id), 'canceled_order');
            @mail(user_email($content->writer_id), $mail_subject, $email_template_body, mail_header(), mail_additional_parameters());
            insert_email_historical_report(user_id(), 'Canceled Order', $mail_subject, user_email($content->writer_id), $canceled_reason, current_id());
        }
        $status_date = date('Y-m-d H:i:s', time());

        $ins_notification['order_id'] = $order_id;
        $ins_notification['client_id'] = $UserID;
        $ins_notification['sender_user_id'] = current_id();
        $ins_notification['type'] = 'canceled_order';
        $ins_notification['created_date'] = $status_date;
        $ins_notification['receiver_user_id'] = $content->writer_id;
        $ins_notification['subject'] = $mail_subject;
        $ins_notification['message'] = spec_rep($body);
        if (notification_setting($content->writer_id, 'canceled_order') == 'On') {
            $wpdb->insert('wp_notification', $ins_notification);
        }
        $content->status = 'Canceled';
        $wpdb->query("UPDATE `wp_content_order` SET `status` = 'Canceled',`status_date` = '$status_date' WHERE `order_id` =$order_id;");

        $success_msg = '<div style="color:green;text-align:center;font-weight: bold;">You have successfully canceled this order!</div><div class="clear_both"></div>';
    }
}
?>
<a href="<?php echo site_url(); ?>/order-content?type=delivery">Back to Delivered Content</a>
<div class="clear_both"></div>
<?php
if ($success_msg != '') {
    echo $success_msg;
}
?>

<h2 style="color:blue;">Canceled Order Info:</h2>
<div class="clear_both"></div>
<form id="canceled_order_Frm" action="" method="post">
    <div style="float:left;width:55%;">
        <div class="left_task">Order ID:</div>   
        <div class="right_task">#<?php echo $content->order_id; ?></div>   
        <div class="clear_both"></div>
        <div class="left_task">Writer:</div>   
        <div class="right_task"><?php echo full_name($content->writer_id); ?></div>   
        <div class="clear_both"></div>
        <div class="left_task">Client:</div>   
        <div class="right_task"><?php echo brand_name($content->user_id); ?></div>  
        <div class="clear_both"></div>
        <div class="left_task">Keyword:</div>   
        <div class="right_task"><?php echo $content->keys; ?></div>   
        <div class="clear_both"></div>

        <?php if ($success_msg == '' && $content->status != 'Canceled') { ?>
            <div class="left_task">Canceled Reason:</div>   
            <div class="right_task">
                <textarea style="width:600px;" name="canceled_reason" class="required"></textarea>
            </div>   
            <div class="clear_both"></div>
            <div class="left_task">&nbsp;</div>   
            <div class="right_task">
                <input type="submit" name="canceled_order_btn" value="Canceled Order" class="new_btn_class">
            </div> 
        <?php } ?>
    </div>
    <div style="float:left;width:45%;">
        <div class="left_task">Order Date:</div>   
        <div class="right_task"><?php echo date("d M Y", strtotime($content->order_date)); ?></div>  
        <div class="clear_both"></div>
        <div class="left_task">Order Status:</div>   
        <div class="right_task"><?php echo $content->status; ?></div>  
        <div class="clear_both"></div>
        <div class="left_task">Post To:</div>   
        <div class="right_task"><?php echo $content->post_to; ?></div>  
        <div class="clear_both"></div>
        <div class="left_task">Site:</div>   
        <div class="right_task"><?php echo $content->sites; ?></div>  
        <div class="clear_both"></div>
    </div>
</form>
<script>
    jQuery('#canceled_order_Frm').validate();
</script>


