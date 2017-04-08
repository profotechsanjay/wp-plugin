<?php
$order_id = $_GET['order_id'];

$content = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'content_order WHERE order_id = ' . $order_id . ' && user_id = ' . $UserID);

if (empty($content)) { //Need twice before updating and after updating
    wp_redirect(site_url() . '/order-content/?type=delivery');
}

$_SESSION['resources_list_page'] = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$success_msg = '';

$all_resources = $wpdb->get_results("SELECT * FROM wp_resources WHERE user_id = $UserID");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['save_resources_btn'])) {
        $wpdb->delete('wp_resources_order', array('order_id' => $order_id));
        $ins_resources_order['order_id'] = $order_id;
        $ins_resources_order['created_date'] = date('Y-m-d H:i:s', time());
        foreach ($_POST['resources_id'] as $save_resources_id) {
            $ins_resources_order['resources_id'] = $save_resources_id;
            $wpdb->insert('wp_resources_order', $ins_resources_order);
        }
        $success_msg = '<div style="color:green;text-align:center;font-weight: bold;">Successfully Resources are Saved!</div><div class="clear_both"></div>';
    } elseif (isset($_POST['update_content'])) {
        $approved_order_data = $_POST['updt'];
        $approved_order_data['content'] = orginal_html($approved_order_data['content']);
        $wpdb->update('wp_content_order', $approved_order_data, array('order_id' => $order_id));
        $success_msg = '<div style="color:green;text-align:center;font-weight: bold;">The content is successfully updated!</div><div class="clear_both"></div>';
    }
}

$saved_resources_id_arr = array();
$saved_resources = $wpdb->get_results("SELECT * FROM wp_resources_order WHERE order_id = $order_id");

if (!empty($saved_resources)) {
    foreach ($saved_resources as $db_id) {
        $saved_resources_id_arr[] = $db_id->resources_id;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approved_order_id'])) {
        $approval_user_id = $_POST['approval_user_id'];
        $live_date = $_POST['live_date'];

        $mail_header = mail_header();
        $mail_subject = 'Order ID #' . $order_id . ' has approved';
        $email_body = file_get_contents(site_url() . '/email/order_approved.php');
        $body = html_entity_decode($email_body);
        $body = str_replace('~~TODAYS_DATE~~', date("d M Y", time()), $body);
        $body = str_replace('~~LAST_ID~~', $order_id, $body);
        $body = str_replace('~~USER_ID~~', $UserID, $body);
        $body = str_replace('~~WHO_IS_APPROVING~~', full_name($approval_user_id), $body);
        $body = str_replace('~~LIVE_DATE~~', date('d M Y', strtotime($live_date)), $body);
        $body = str_replace('~~ORDER_DETAILS_LINK~~', site_url() . '/contentadmin/content.php?order_id=' . $order_id, $body);

        $Content_Writter_role_id = 6;
        $send_content_order_to = user_email($content->writer_id); //get_user_meta($UserID, 'send_content_order_to', true);

        if ($send_content_order_to != "") {
            if (email_subscription_setting($send_content_order_to, 'approved_order') == 'Yes') {
                $email_template_body = email_template_body($body, $send_content_order_to, 'approved_order');
                @mail($send_content_order_to, $mail_subject, $email_template_body, mail_header(), mail_additional_parameters());
                insert_email_historical_report($UserID, 'Approved Order', $mail_subject, $send_content_order_to, 'Notification email after Approved Order', current_id());
            }
        }

        $role_worker_list = role_worker_list($UserID, $Content_Writter_role_id);

        $ins_notification['order_id'] = $order_id;
        $ins_notification['client_id'] = $UserID;
        $ins_notification['sender_user_id'] = current_id();
        $ins_notification['type'] = 'approved_order';
        $ins_notification['created_date'] = date('Y-m-d H:i:s', time());
        $ins_notification['receiver_role_id'] = $Content_Writter_role_id;
        $ins_notification['subject'] = $mail_subject;
        $ins_notification['message'] = spec_rep($body);

        $sql = "SELECT * FROM `wp_notification` WHERE `type` = 'approved_order' AND `order_id` = $order_id";

        $check_order_content_submit = $wpdb->get_row($sql);
        if (notification_setting($role_worker_list[0], 'approved_order') == 'On') {
            if (empty($check_order_content_submit)) {
                $wpdb->insert('wp_notification', $ins_notification);
            } else {
                $wpdb->update('wp_notification', $ins_notification, array('notification_id' => $check_order_content_submit->notification_id));
            }
        }

        if (!empty($_POST['notify_email'])) {
            $mail_body = 'Hi,<br/><p>Notify email for Order ID #' . $order_id . '<br/><br/>Thanks<br/>The Marketing Control Center Team at Enfusen';
            foreach ($_POST['notify_email'] as $notify_email) {
                if ($send_content_order_to != $notify_email) {
                    if (email_subscription_setting($notify_email, 'approved_order') == 'Yes') {
                        $email_template_body = email_template_body($mail_body, $notify_email, 'approved_order');
                        @mail($notify_email, 'Order ID #' . $order_id . ' has approved', $email_template_body, mail_header(), mail_additional_parameters());
                        insert_email_historical_report($UserID, 'Approved Order', 'Order ID #' . $order_id . ' has approved', $notify_email, 'Notify email after approved an order', current_id());
                    }
                }
            }
        }

        // Start Connection with DL, SS and SL
        $order_info = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'content_order WHERE order_id = ' . $order_id);
///*
        $connection_url = $order_info->post_url;
        if (trim($connection_url) != "") {
            if ($order_info->ss == 'Yes') {
                $check_url = $wpdb->query("SELECT * FROM `wp_social_adr` where url = '$connection_url' and user_id = '$UserID' LIMIT 1");
                if (empty($check_url)) {
                    $wpdb->query('insert into wp_social_adr(url,status,user_id)values("' . $connection_url . '","0","' . $UserID . '")');
                }
            }

            if ($order_info->sl == 'Yes') {
                $check_url = $wpdb->query("SELECT * FROM `wp_seo_nitro` where post_url = '$connection_url' and user_id = '$UserID' LIMIT 1");
                if (empty($check_url)) {
                    $wpdb->query("INSERT INTO `wp_seo_nitro` (`id`, `user_id`, `post_date`, `post_url`, `src`) VALUES (NULL, '$UserID', '" . date('Y-m-d') . "', '$connection_url', 'SEO_NITRO');");
                }
            }

            // Linkemperor set up
            if ($order_info->dl == 'Yes' && $_SERVER['HTTP_HOST'] != 'localhost') {
                $campagian_arr = get_user_meta($UserID, "campaign", true);
                $camp_id = $campagian_arr['id'];

                if ($camp_id > 0) {
                    $key = $order_info->keys;
                    $post_data['target'] = array('target' => array('campaign_id' => $camp_id, 'url_input' => $connection_url, 'keyword_input' => $key));
                    $data_string = json_encode($post_data['target']);
                    $Link_emp_API = '272403f9a830d41c48f2700cd9f0ccf692b16223';
                    $URI = 'https://app.linkemperor.com/api/v2/customers/targets.json?api_key=' . $Link_emp_API . '';

                    //$Return_Value = curl_INTI_POSTS_url($URI, $Data_String);
                    $ch = curl_init($URI);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json",
                        "Content-Length:" . strlen($data_string)));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $Return_Value = curl_exec($ch);
                    $decode = json_decode($Return_Value);
                    $tid = $decode->target_keywords[0]->target_id;

                    $wpdb->query("INSERT INTO `wp_posted_content_le_setup` (`le_setup_id`, `user_id`,`url`, `target_id`, `date`) VALUES (NULL, '$UserID' ,'$connection_url', '$tid', '$time');");
                    $wpdb->query("INSERT INTO `wp_citation_le_post` (`id`, `title`, `url`, `user_id`, `leurl`, `target_id`, `keyword_post`, `ss`) VALUES (NULL, 'Title', '$connection_url', '$UserID', '1', '$tid', '1', '');");
                }
            }
        }

        //*/// End Connection with DL, SS and SL

        $approved_order_data['status'] = 'Approved';
        $approved_order_data['approval_user_id'] = $approval_user_id;
        $approved_order_data['live_date'] = $live_date;
        $approved_order_data['status_date'] = date('Y-m-d H:i:s');
        $wpdb->update('wp_content_order', $approved_order_data, array('order_id' => $order_id));
        $wpdb->query("update `wp_task_user` set status = 'Completed' WHERE order_id = $order_id");
        $success_msg = '<div style="color:green;text-align:center;font-weight: bold;">You have successfully approved your content!</div><div class="clear_both"></div>';
    }



    if (isset($_POST['request_changes'])) {

        $request_changes_data['request_changes'] = $_POST['request_changes'];
        $request_changes_data['status'] = 'Request Changes';
        $request_changes_data['status_date'] = date('Y-m-d H:i:s');
        $wpdb->update('wp_content_order', $request_changes_data, array('order_id' => $order_id));


        $success_msg = '<div style="color:green;text-align:center;font-weight: bold;">Successfully sent your request changes info!</div><div class="clear_both"></div>';


        $mail_header = mail_header();
        $mail_subject = 'Request Changes for Order ID #' . $order_id;
        $email_body = file_get_contents(site_url() . '/email/order_request_change.php');
        $body = html_entity_decode($email_body);
        $body = str_replace('~~TODAYS_DATE~~', date("d M Y", time()), $body);
        $body = str_replace('~~LAST_ID~~', $order_id, $body);
        $body = str_replace('~~USER_ID~~', $UserID, $body);
        $body = str_replace('~~ORDER_DETAILS_LINK~~', site_url() . '/contentadmin/content.php?order_id=' . $order_id, $body);

        $Content_Writter_role_id = 6;

        $send_content_order_to = user_email($content->writer_id);
        if ($send_content_order_to != "") {
             if (email_subscription_setting($send_content_order_to, 'request_changes') == 'Yes') {
                $email_template_body = email_template_body($body, $send_content_order_to, 'request_changes');
                @mail($send_content_order_to, $mail_subject, $email_template_body, mail_header(), mail_additional_parameters());
                insert_email_historical_report($UserID, 'Request Changes', $mail_subject, $send_content_order_to, 'Request changes of a order', current_id());
            }
            //@mail($send_content_order_to, $mail_subject, $body, $mail_header);
        }


        $role_worker_list = role_worker_list($UserID, $Content_Writter_role_id);
        /* Mail off
          if (!empty($role_worker_list)) {
          foreach ($role_worker_list as $row_worker_id) {
          @mail(user_email($row_worker_id), $mail_subject, $body, $mail_header);
          }
          }
         */

        $ins_notification['order_id'] = $order_id;
        $ins_notification['client_id'] = $UserID;
        $ins_notification['sender_user_id'] = current_id();
        $ins_notification['type'] = 'request_changes';
        $ins_notification['receiver_role_id'] = $Content_Writter_role_id;
        $ins_notification['created_date'] = date('Y-m-d H:i:s', time());
        $ins_notification['subject'] = $mail_subject;
        $ins_notification['message'] = spec_rep($body);


        $sql = "SELECT * FROM `wp_notification` WHERE `type` = 'request_changes' AND `order_id` = $order_id";

        $check_order_content_submit = $wpdb->get_row($sql);
        if (notification_setting($role_worker_list[0], 'request_changes') == 'On') {
            if (empty($check_order_content_submit)) {
                $wpdb->insert('wp_notification', $ins_notification);
            } else {
                $wpdb->update('wp_notification', $ins_notification, array('notification_id' => $check_order_content_submit->notification_id));
            }
        }
    }
}

$content = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'content_order WHERE order_id = ' . $order_id . ' && user_id = ' . $UserID);
if (empty($content)) {
    wp_redirect(site_url() . '/order-content/?type=delivery');
}

//print_r($content);
?>

<div style="width:100%;float:left;">

    <div style="padding:0px 15px;">
        <?php if (isset($_GET['order_user_id'])) {
            ?>
            <a style="cursor: pointer;font-weight: bold;" href="<?php echo $_SESSION['open_order_link']; ?>">Back to Open Order</a>
        <?php } else {
            ?>
            <a style="cursor: pointer;font-weight: bold;" href="<?php echo site_url(); ?>/order-content?type=delivery">Back to Delivered Content</a>
        <?php } ?>
        <div class="clear_both"></div>
        <form action="<?php echo site_url() . '/order-content/?type=view-content&post-to=' . $post_to . '&order_id=' . $content->order_id; ?>" method="post" id="updtCont">
            <?php
            echo $success_msg;
            $cmnStyle = ($type == 'edit-content') ? 'line-height:29px;' : '';
            ?>
            <div style="float:left;"><b>Sites:</b> <a style="text-decoration: none;" target="_blank" href="<?php
                $pr = (strpos($content->sites, 'https://') === false) ? 'http://' : 'https://';
                echo $pr . str_replace($pr, '', $content->sites);
                ?>"><?php echo $content->sites; ?></a></div>
            <div style="float:left;margin-left:10px;"><b>Keyword:</b>
                <?php
                $keywords = $content->keys;
                echo $keywords;
                ?>
            </div>
            <div class="clear_both"></div>

            <div style="float:left">
                <b style="float:left">Keyword Synonyms:</b>
                <ul style="float:left;list-style:disc inside; padding-left:10px">
                    <?php
                    $kw = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE user_id={$content->user_id} AND meta_key LIKE 'LE_Repu_Keyword_%' AND `meta_value` LIKE '{$content->keys}'");
                    //pr($kw,'========$kw======');

                    if (!empty($kw)) {
                        $ind = str_replace("LE_Repu_Keyword_", "", $kw->meta_key) - 1;
                        $keywordDat = get_user_meta($content->user_id, "Content_keyword_Site", true);
                        if (!empty($keywordDat['Synonyms_keyword'][$ind])) {
                            $Synonyms_keyword = $keywordDat['Synonyms_keyword'][$ind];
                            //pr($Synonyms_keyword,'========$Synonyms_keyword AND Index=['.$ind.']=====');
                            if (!empty($Synonyms_keyword))
                                echo '<li>' . implode('</li><li>', $Synonyms_keyword) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="clear_both"></div>

            <?php
            //pr($user_sites,'======$user_sites====');
            if ($content->post_to == 'Primary Site') {
                $logLink = $Content_Primary_Site['login_url'];
                $logUser = $Content_Primary_Site['login_user'];
                $logPass = $Content_Primary_Site['login_password'];
                if (isset($_GET['debug']))
                    pr($Content_Primary_Site, '======$Content_Primary_Site======');
            }else {
                $indx = array_search($content->sites, $buffersites['buffersiteurl']);
                $logLink = $buffersites['buffer_login_url'][$indx];
                $logUser = $buffersites['buffer_login_user'][$indx];
                $logPass = $buffersites['buffer_login_password'][$indx];
                if (isset($_GET['debug']))
                    pr($buffersites, '======$buffersites==>Index[' . $indx . ']====');
            }
            ?>
            <div style="float:left"><b>Login URL:</b> <a target="_blank" href="<?php
                $pr = (strpos($logLink, 'https://') === false) ? 'http://' : 'https://';
                echo $pr . str_replace($pr, '', $logLink);
                ?>"><?php echo $logLink; ?></a></div>
            <div class="clear_both"></div>

            <div style="float:left"><b>Login User:</b> <?php echo $logUser; //full_name($content->user_id);       ?></div>
            <div style="float:left;margin-left:20px"><b>Writer:</b> <?php echo full_name($content->writer_id); ?></div>
            <div class="clear_both"></div>

            <div style="float:left"><b>Login Password:</b> <?php echo $logPass; ?></div>
            <div class="clear_both"></div>

            <div style="float:left;<?php echo $cmnStyle; ?>"><b>Order Number:</b> <?php echo $content->order_id; ?></div>
            <div style="float:left;margin-left:20px;<?php echo $cmnStyle; ?>"><b>Optimized:</b> <?php echo $content->optimized; ?></div>
            <div style="float:left;margin-left:20px;<?php echo $cmnStyle; ?>">
                <b>Go Live Date:</b>
                <?php if ($type == 'edit-content') { ?>
                    <input type="text" name="updt[live_date]" class="datepicker" value="<?php echo $content->live_date; ?>">
                    <?php
                } else {
                    echo $content->live_date;
                }
                ?>
            </div>
            <div class="clear_both"></div>



            <?php //if ($content->blog_title != "") {     ?>
            <div style="float:left;">
                <b style="vertical-align:top;<?php echo $cmnStyle; ?>">Blog Title:</b>
                <?php
                if ($type == 'edit-content') {
                    ?>
                    <span id="blog_title_1" style="display:inline-block;">
                        <?php
                        if ($post_to == 'buffer') {
                            ?>
                            <input type="text" readonly="true"name="updt[blog_title]" value="Buffer post - Not on Site">
                            <?php
                        } else {
                            ?>
                            <?php if ($type == 'edit-content' && !empty($content) && $content->blog_title != '' && $content->blog_title != 'Buffer post - Not on Site'): ?>
                                <input name="updt[blog_title]" placeholder="Add new blog title" value="<?php echo $content->blog_title; ?>" type="text" style="height:18px;width:300px">
                                <div style="clear:both;height:10px;"></div>
                                <a style="cursor:pointer;font-size:12px;" onclick="back_to_select_blog_option_func('1')">Back to select options</a>
                            <?php else: ?>
                                <select name="updt[blog_title]" class="sel" onchange="blog_title_func('1', this.value)" style="width:300px">
                                    <?php echo $all_blog_post; ?>
                                </select>
                            <?php
                            endif;
                        }
                        ?>
                    </span>
                    <?php
                }else {
                    echo '<span style="' . $cmnStyle . '">' . $content->blog_title . '</span>';
                }
                ?>
            </div>
            <div class="clear_both"></div>
            <?php //}    ?>



            <div style="float:left;">
                <b>Direct Link:</b>
                <?php if ($type == 'edit-content') { ?>
                    <select name="updt[dl]">
                        <?php
                        foreach (array('No', 'Yes') as $item) {
                            $slct = (!empty($content) && $content->dl == $item) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $item; ?>"<?php echo $slct; ?>><?php echo $item; ?></option><?php }
                        ?>
                    </select>
                    <?php
                } else {
                    echo $content->dl == '' ? 'N/A' : $content->dl;
                }
                ?>
            </div>
            <div style="float:left;margin-left:20px;"><b>Social Signal:</b>
                <?php if ($type == 'edit-content') { ?>
                    <select name="updt[ss]">
                        <?php
                        foreach (array('No', 'Yes') as $item) {
                            $slct = (!empty($content) && $content->ss == $item) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $item; ?>"<?php echo $slct; ?>><?php echo $item; ?></option><?php }
                        ?>
                    </select>
                    <?php
                } else {
                    echo $content->ss == '' ? 'N/A' : $content->ss;
                }
                ?>
            </div>
            <div style="float:left;margin-left:20px;">
                <b>Syndicated Link:</b>
                <?php if ($type == 'edit-content') { ?>
                    <select name="updt[sl]">
                        <?php
                        foreach (array('No', 'Yes') as $item) {
                            $slct = (!empty($content) && $content->sl == $item) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $item; ?>"<?php echo $slct; ?>><?php echo $item; ?></option><?php }
                        ?>
                    </select>
                    <?php
                } else {
                    echo $content->sl == '' ? 'N/A' : $content->sl;
                }
                ?>
            </div>
            <div class="clear_both"></div>
            <?php //}     ?>
            <div style="float:left;">
                <b>Site Post URL:</b>
                <?php if ($type == 'edit-content') { ?>
                    <input type="text" name="updt[post_url]" value="<?php echo $content->post_url; ?>" style="width:315px">
                    <?php
                } else {
                    echo $content->post_url;
                }
                ?>
            </div>
            <div class="clear_both"></div>
            <b>Content:</b>
            <div class="clear_both"></div>
            <style type="text/css">p{margin-top:25px}</style>
            <div id="content_div" style="text-align:justify; border:1px solid #ccc;<?php if ($type != 'edit-content') echo 'padding:12px'; ?>">
                <?php
                if ($type == 'edit-content') {
                    wp_editor($content->content, 'order_content', array('textarea_name' => 'updt[content]', 'editor_height' => '400', 'textarea_rows' => get_option('default_post_edit_rows', 20)));
                    /* ?>
                      <textarea name="updt[content]" style="width:100%;height:100px;" class="required" placeholder="Enter Order Content....."><?php echo $content->content; ?></textarea>
                      <?php
                     */
                } else {
                    echo html_entity_decode(orginal_html($content->content));
                }
                ?>
            </div>
            <div class="clear_both"></div>

            <?php //if ($content->status != 'Approved'){  ?>
            <div style="float:right;" id="req_app_btn">
                <?php if ($type == 'edit-content') { ?>
                    <input type="submit" name="update_content" value="Update Changes">
                    <?php
                } else {
                    ?>
                    <a href="<?php echo site_url() . '/order-content/?type=edit-content&post-to=' . $post_to . '&order_id=' . $content->order_id; ?>" style="text-decoration:none;margin-right:15px; display:inline-block;"><input type="button" value="Edit Content"></a>
                    <?php if ($content->status != 'Approved') { ?>
                        <input type="button" onclick="jQuery('#req_c_div').show();
                                        jQuery('#req_app_btn').hide();" value="Request Changes">
                        <a style="text-decoration: none;" class="fancybox" href="#approved_div"><input style="margin-left:15px;" type="button" value="Approve Content"></a>
                        <?php
                    }
                }
                ?>
                <div class="clear_both"></div>
            </div>

            <?php //}    ?>
        </form>
        <div id="req_c_div" style="display:none;width:65%;">

            <form id="req_changes_Frm" action="" method="post">
                <textarea name="request_changes" style="width:100%;height:100px;" class="required" placeholder="Write your comment for request changes....."><?php echo $content->request_changes; ?></textarea>
                <div class="clear_both"></div>
                <input type="button" onclick="jQuery('#req_c_div').hide();
                        jQuery('#req_app_btn').show();" value="Cancel">
                <input style="margin-left:20px;"type="submit" value="Submit Request Changes">
            </form> 

        </div>

        <?php if ($content->image_name != "") { ?>

            <div class="clear_both"></div>
            <div class="clear_both"></div>
            <b>Image:</b> <a target="_blank" href="<?php echo site_url(); ?>/contentadmin/images/content_image/<?php echo $content->image_name; ?>"> download</a>
            <div class="clear_both"></div>
            <a class="fancybox" href="<?php echo site_url(); ?>/wp-content/uploads/content_image/<?php echo $content->image_name; ?>">
                <img src="<?php echo site_url(); ?>/wp-content/uploads/content_image/<?php echo $content->image_name; ?>">
            </a>
            <div class="clear_both"></div>
            <?php
        }
        ?>

        <!--<div style="text-align: left;font-weight: bold;color:blue;"><a style="cursor: pointer;" href="#all_resources" class="fancybox">Attache a Resource</a> </div><div class="clear_both"></div>-->
        <br/><input type="button" class="btn_class fancybox" style="color:white;font-weight: bold;" href="#all_resources" value="Attach a Resource">
        <?php
        if (!empty($saved_resources_id_arr)) {

            $all_saved_resources_id = implode(",", $saved_resources_id_arr);
            $all_saved_resources = $wpdb->get_results("SELECT * FROM wp_resources WHERE resources_id IN($all_saved_resources_id)");
            if (!empty($all_saved_resources)) {
                ?>
                <div style="text-align: center;font-weight: bold;font-size: 15px;">All Saved Resources File</div>
                <table style="margin-top:10px; border-radius: 3px 3px 3px 3px; width:100%; float:left; border: 1px solid #cecece;">
                    <tbody>
                        <tr style="background-color:#F3F4F4;">
                            <th style="float:left; width:19%; padding:9px 4px;">Date</th>
                            <th style="float:left; width:39%; padding:9px 4px;">Description</th>
                            <th style="float:left;  width:30%; padding:9px 4px;">File</th>
                        </tr>
                        <?php foreach ($all_saved_resources as $res_index => $row_res) { ?>
                            <tr style="font-size:14px;background-color: <?php echo $res_index % 2 == 0 ? '#fff' : '#eee' ?>; text-align:center;">
                                <td style="float:left; width:19%; padding:9px 4px;"><?php echo date('d M Y', strtotime($row_res->created_date)); ?></td>
                                <td style="float:left; width:39%; padding:9px 4px;"><a href="<?php echo site_url(); ?>/resource-details/?resources_id=<?php echo encode_value($row_res->resources_id); ?>"><?php echo $row_res->res_desc; ?></a></td>
                                <td style="float:left; width:30%; padding:9px 4px;">
                                    <a target="_blank" href="<?php echo site_url(); ?>/wp-content/uploads/Resources/<?php echo $row_res->user_id; ?>/<?php echo $row_res->res_file; ?>"><?php echo $row_res->res_file; ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo '<div class="clear_both"></div>';
            }
        }
        ?>

        <div id="all_resources" style="display: none;width:800px;">

            <?php if (!empty($all_resources)) {
                ?>

                <form name="save_resources_Frm" method="post" action="">
                    <div style="text-align: center;font-weight: bold;font-size: 15px;">All Resources File</div>
                    <table style="margin-top:10px; border-radius: 3px 3px 3px 3px; width:100%; float:left; border: 1px solid #cecece;">
                        <tbody>
                            <tr style="background-color:#F3F4F4;">
                                <th style="float:left; width:4%; padding:9px 4px;">&nbsp;</th>
                                <th style="float:left; width:19%; padding:9px 4px;">Date</th>
                                <th style="float:left; width:35%; padding:9px 4px;">Description</th>
                                <th style="float:left;  width:30%; padding:9px 4px;">File</th>
                            </tr>
                            <?php foreach ($all_resources as $res_index => $row_res) { ?>
                                <tr style="font-size:14px;background-color: <?php echo $res_index % 2 == 0 ? '#fff' : '#eee' ?>; text-align:center;">
                                    <td style="float:left; width:4%; padding:9px 4px;"><input type="checkbox" <?php if (in_array($row_res->resources_id, $saved_resources_id_arr)) echo 'checked'; ?> name="resources_id[]" value="<?php echo $row_res->resources_id; ?>"></td>
                                    <td style="float:left; width:19%; padding:9px 4px;"><?php echo date('d M Y', strtotime($row_res->created_date)); ?></td>
                                    <td style="float:left; width:35%; padding:9px 4px;"><a href="<?php echo site_url(); ?>/resource-details/?resources_id=<?php echo encode_value($row_res->resources_id); ?>"><?php echo $row_res->res_desc; ?></a></td>
                                    <td style="float:left; width:30%; padding:9px 4px;">
                                        <a target="_blank" href="<?php echo site_url(); ?>/wp-content/uploads/Resources/<?php echo $row_res->user_id; ?>/<?php echo $row_res->res_file; ?>"><?php echo $row_res->res_file; ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="clear_both"></div>
                    <input type="submit" name="save_resources_btn" class="btn_class" style="color:white;font-weight: bold;margin-left:35%" value="Save Resources">
                </form> 

                <?php
            } else
                echo '<div style="font-weight:bold;text-align:center;">You have no resource file. You can add it from Profile -> Resources Section.</div>';
            ?>

        </div>
    </div>
</div>


<style type="text/css">
    .msgErr{padding:10px; border:1px solid blue; color:red; background:#f4dfdf; text-align:center; margin:10px 0}
</style>
<div id="approved_div" style="width:500px;display: none;">
    <form id="approved_Frm" action="" method="post"<?php if (trim($content->post_url) == ''): ?> onsubmit="return chkSubmit(this);"<?php endif; ?>>
        <?php
        $all_user_list = all_user_list('all');
        //pr($all_user_list);
        if (trim($content->post_url) == ''):
            $errMsg = 'You can not approve this content keeping the "Site Post URL" empty!';
            ?>
            <div class="msgErr"><?php echo $errMsg; ?></div>
        <?php endif;
        ?>
        <div class="left_posted_class" style="width:35%;">Who is approving?</div>
        <div class="right_posted_class" style="width:63%;">
            <?php
            $current_user = wp_get_current_user();
            $CURENT_ID = $current_user->ID;

            if (role($CURENT_ID) == 'worker') {
                ?>
                <input type="hidden" name="approval_user_id" value="<?php echo $CURENT_ID; ?>"> <span style="margin-left:10px;"><?php echo brand_name($CURENT_ID); ?></span>
                <?php
            } else {
                ?>
                <select name="approval_user_id" class="required" style="width: 150px;">
                    <option value="">Select User</option>
                    <?php
                    foreach ($all_user_list as $row_worker_list) {
                        ?>
                        <option value="<?php echo $row_worker_list->ID; ?>"><?php echo $row_worker_list->display_name; ?></option>
                        <?php
                    }
                    ?>
                </select>
            <?php } ?>
        </div>

        <div class="clear_both"></div>
        <div class="left_posted_class" style="width:35%;">Go Live Date:</div>

        <div class="right_posted_class" style="width:63%;">
            <input type="text" name="live_date" class="datepicker required" value="<?php echo $content->live_date; ?>">
        </div>

        <div class="clear_both"></div>
        <div class="left_posted_class" style="width: 35%;">Who is notified:</div>

        <div class="right_posted_class" style="width: 63%;">
            <input style="cursor:pointer;" type="checkbox" id="selecctall"> <span style="margin-left: 10px;"> <b>Select All</b></span>
            <div style="clear:both;height:7px;"></div>
            <?php
            foreach ($all_user_list as $row_worker_list) {
                ?>
                <input class="checkbox1" name="notify_email[]" value="<?php echo $row_worker_list->user_email; ?>" type="checkbox"> <span style="margin-left:10px;"><?php echo $row_worker_list->display_name; ?></span>
                <div style="clear:both;height:7px;"></div>
                <?php
            }

            if (role($CURENT_ID) == 'worker') {
                $all_worker_list = all_user_list('worker', 0, $UserID);
                foreach ($all_worker_list as $worker_info) {
                    ?>
                    <input class="checkbox1" name="notify_email[]" value="<?php echo $worker_info->user_email; ?>" type="checkbox"> <span style="margin-left:10px;"><?php echo $worker_info->display_name; ?></span>
                    <div style="clear:both;height:7px;"></div>
                    <?php
                }
            }
            ?>
        </div>

        <div class="clear_both"></div>
        <input type="hidden" name="approved_order_id" value="<?php echo $content->order_id; ?>">

        <div class="left_posted_class" style="width:35%;">&nbsp;</div>
        <div class="right_posted_class" style="width:63%;">
            <input type="submit" class="btn_class" value="Approved" style="color:white;">
        </div>

    </form>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script type="text/javascript">
        function chkSubmit(frm) {
            alert(jQuery('.msgErr').text());
            return false;
        }
        jQuery(document).ready(function($) {
            $(".datepicker").datepicker();
            $('#approved_Frm').validate({
                //
            });
            $('#selecctall').click(function(event) {  //on click
                if (this.checked) { // check select status
                    $('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "checkbox1"
                    });
                } else {
                    $('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"
                    });
                }
            });
        });
    </script>
</div>    


<?php if ($type == 'edit-content') { ?>
    <div id="back_to_select_blog_option" style="display: none;">
        <?php if ($type == 'edit-content' && !empty($content) && $content->blog_title != '' && $content->blog_title != 'Buffer post - Not on Site'): ?>
            <select name="updt[blog_title]" class="sel" onchange="blog_title_func('1', this.value)" style="width:300px"><?php echo $all_blog_post; ?></select>
        <?php endif; ?>
    </div>
<?php } ?>
<script type="text/javascript">
<?php /*
  function approved_func(order_id){
  var con = confirm('Are you sure to approved this content?');
  if (con){
  document.forms.approved_Frm.submit();
  }
  }
 */ ?>
    jQuery(document).ready(function($) {
        $('#req_changes_Frm').validate({
            messages: {
                request_changes: 'Please insert your comment'
            }
        });
    });

<?php if ($type == 'edit-content') { ?>
        var oldBlogTitle = "<?php echo ($type == 'edit-content' && !empty($content) && $content->blog_title != '' && $content->blog_title != 'Buffer post - Not on Site') ? $content->blog_title : '' ?>";
        function blog_title_func(number, value) {
            if (value == 'Add New') {
                jQuery('#back_to_select_blog_option').html(jQuery('#blog_title_' + number).html());
                jQuery('#blog_title_' + number).html('<input name="updt[blog_title]" placeholder="Add new blog title" value="' + oldBlogTitle + '" type="text" style="height:18px;width:300px"><div style="clear:both;height:10px;"></div><a style="cursor:pointer;font-size:12px;" onclick="back_to_select_blog_option_func(' + number + ')">Back to select options</a>');
            }
        }
        function back_to_select_blog_option_func(number) {
            oldBlogTitle = jQuery("#blog_title_" + number + ' input').val();
            jQuery("#blog_title_" + number).html(jQuery("#back_to_select_blog_option").html());
        }
<?php } ?>
</script>

