<?php

$con = "status = '" . str_replace("_", " ", $type) . "'";



if($type == "all-order"){



  $con = " order_id > 0";  



  $type = '';


}

//$sql = "SELECT wp_content_order.*, wp_users.user_nicename as 'writer' 
//        FROM wp_content_order LEFT JOIN wp_users on wp_users.ID = wp_content_order.writer_id
//        WHERE $con and user_id in(SELECT MCCUserId FROM " . client_location() . ") and writer_id = $writer_id
//        ORDER BY order_id DESC";

$sql = "SELECT wp_content_order.*, wp_users.user_nicename as 'writer' 
        FROM wp_content_order LEFT JOIN wp_users on wp_users.ID = wp_content_order.writer_id
        WHERE $con and writer_id = $writer_id
        ORDER BY order_id DESC";

$all_order = $wpdb->get_results($sql);

?>

<table style="font-size:80% !important;" id="example" class="tabl1 table table-striped table-bordered table-hover" cellspacing="0" >
    <thead style="background-color: #888;color:white;">
        <tr>
            <th style="text-align: center;">Order ID</th>
            <th style="text-align: center;">Client</th>
            <?php /*
            <th class="sites_c">Site</th>
            <th>Keyword</th>
            <th>Status</th>*/ ?>
            <th style="text-align: center;">Due Date</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: center;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if (!empty($all_order)) {
            foreach ($all_order as $order) {
                ?>
                <tr>
                    <td style="text-align: center;">#<?php echo $order->order_id; ?></td>
                    <td style="text-align: center;"><?php echo brand_name($order->user_id); ?></td>
                    <?php /*
 					<td class="sites_c"><?php echo $order->sites; ?></td>
                    <td><?php echo $order->keys; ?></td>
                    <td><?php echo $order->status; ?></td>
					*/ ?>
                    <td style="text-align: center;"><?php echo  date("d M Y", strtotime($order->order_date) + 7*24*3600); ?></td>
                    <td style="text-align: center;"><?php echo  $order->status; ?></td>
                    <td style="text-align: center;">
                        <?php
                        if ($order->status == 'Approved' || $order->status == 'Canceled') {
                            $text = 'View Content';
                        } else if ($order->status == 'Ordered') {
                            $text = 'Add Content';
                        } else {
                            $text = 'Update Content';
                        }
                        $order_link = $siteurl.'/content-admin?type=content&agency_id='.$agency_id.'&order_id='.$order->order_id; 
                        $feedback_link = $siteurl.'/content-admin?type=feedback&agency_id='.$agency_id.'&feedback_order_id='.$order->order_id;
//                        $check_order_task = $wpdb->get_row("SELECT task_list_id FROM `wp_task_user` WHERE `order_id` =$order->order_id");
//                        if(!empty($check_order_task)){
//                          $micro_task_check = $wpdb->get_row("SELECT * FROM `wp_task_list` WHERE `parent_task_list_id` = $check_order_task->task_list_id ORDER BY `task_list_id` DESC LIMIT 1");
//                          if(!empty($micro_task_check)){
//                             //$order_link = $feedback_link = site_url().'/task-details/?type=to-do-list&task_list_id='.$check_order_task->task_list_id.'&client_user_id='.$order->user_id;  
//                          }
//                        }
                        ?> 
                        <a style="text-decoration: none;" href="<?php echo $order_link; ?>"><?php echo $text; ?></a>
                        <?php
                        if ($order->status == 'Approved') {
                            $client_given_feedback = $wpdb->get_row("SELECT feedback_id FROM `wp_feedback` WHERE `order_id` = $order->order_id && `sender_user_id` = $order->user_id");
                            if (!empty($client_given_feedback)) {
                                $already_given_feedback = $wpdb->get_row("SELECT feedback_id FROM `wp_feedback` WHERE `order_id` = $order->order_id && `sender_user_id` = $writer_id");
                                if (empty($already_given_feedback)) {
                                    
                                    ?>
                        | <a style="text-decoration: none;" href="<?php echo $feedback_link; ?>">Give Feedback</a>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
