<div class="accoSet">
    <h2 class="fulllist">Content Order Settings</h2>
</div>
<div class="item-setting" style="padding: 20px 20px;line-height: 23px;">
    <?php
    
    if (isset($_POST['updatecontentsettings'])) {
        //pr($_POST);exit;
        update_user_meta($UserID,'selected_writer',$_POST['selected_writer']);
        if(!$_POST['selected_writer'] > 0){
        if (is_wp_error($UserID)) {
            $error_string = $UserID->get_error_message();
            echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
        }
        
//On success
        elseif (!is_wp_error($UserID)) {
            echo "<div style='color:green;font-weight:bold;'>Successfully Udpated!</div><br>";
            update_user_meta($UserID, 'send_content_order_to', $_REQUEST['send_content_order_to']);
            update_user_meta($UserID, 'send_content_order_from_email', $_REQUEST['send_content_order_from_email']);
        } else {
            echo "not working";
        }
        }  
        echo '<div class="success_c">You have successfully selected a writer!</div>';
    }
    $userdata = get_userdata($UserID);
    $send_content_order_to = get_user_meta($UserID, 'send_content_order_to', 'true');
    $send_content_order_from_email = get_user_meta($UserID, 'send_content_order_from_email', 'true');
    //echo "<h1>" . __('Content Order Settings', 'ga-dash') . "</h1><br/>";
    
    $all_writers = $wpdb->get_results("SELECT * FROM `wp_usermeta` where `meta_key` = 'TAG_CLIENT_ID_$UserID' and `meta_value` = 3");
    $selected_writer = get_user_meta($UserID,'selected_writer',true);
    ?>



    <form name="ga_adduser_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
        <p><label><b>Select Writer:</b></label><br />
        <select name="selected_writer">
            <option value="">Select Writer</option>
            <?php
               if(!empty($all_writers)){
                   foreach($all_writers as $single_writer){
                       ?>
            <option <?php if($selected_writer == $single_writer->user_id) echo 'selected';?> value="<?php echo $single_writer->user_id; ?>"><?php echo full_name($single_writer->user_id); ?></option>
                       <?php
                   }
               }
            ?>
            
        </select>
            <br /><br />

            <?php if(!$selected_writer > 0){?>
        <p><label><?php echo "<b>" . __("Send Content Orders To:", 'ga-dash') . " </b>"; ?></label><br /><input type="text" name="send_content_order_to" value="<?php
            if (isset($_REQUEST['updateuser'])) {
                echo $_REQUEST[send_content_order_to];
            } else {
                echo "$send_content_order_to";
            }
            ?>" size="40"><br /><span class="description">Separate multiple E-Mail addresses by comma. <br />If left blank no content order will be sent. <br />Add any E-Mail address for which you wish to receive a copy of the order.</span></p>  
        <br /><br />
        <p><label><?php echo "<b>" . __("Content Order Reply/Questions (E-Mail):", 'ga-dash') . " </b>"; ?></label><br /><input type="text" name="send_content_order_from_email" value="<?php
            if (isset($_REQUEST['updateuser'])) {
                echo $_REQUEST[send_content_order_from_email];
            } else {
                echo "$send_content_order_from_email";
            }
            ?>" size="40"><br /><span class="description">Please provide an email address where writers can <br />
                contact you if they have questions regarding the order. <br />
                This will also be the Reply E-Mail Address.</span><br /><br /></p>  

            <?php } ?>


        <p> <input type="submit" name="updatecontentsettings" class="btn_class" style="color:white;" value="<?php _e('Update', 'ga-dash') ?>" /></p>

    </form>  












</div>