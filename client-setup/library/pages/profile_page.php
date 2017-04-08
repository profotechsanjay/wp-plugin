<?php $current_id = isset($_POST['profile_id']) ? intval($_POST['profile_id']) : '';  ?>
<style>
    .pages a{float:left;margin-bottom: 8px;cursor: pointer;color:#1394BE; padding:6px 9px; border:1px solid #1394BE; margin-right:5px;text-decoration: none;}
    .pages a.act{cursor: pointer;background:#333; border:1px solid #000; color:white;text-decoration: none;}
    .clear_10px{clear:both;height:10px;}
    .all_section{display: block;}
</style>
<div id="primary" class="site-content">

    <div id="content" role="main">

        <div class="en-left">

            <?php include_once (get_template_directory() . '/master-admin/left-menu.php'); ?>

        </div>

        <div class="en-right" style="min-height:700px;">
            
            <div class="clear_both"></div>
            <div class="profile all_section" style="display: block;">
                <div style="float:left;width:18%;">
                    <?php
                    $profile_img = get_user_meta($current_id, 'user_photo', true);
                    if ($profile_img == "") {
                        $profile_img = 'default.png';
                    }
                    ?>
                    <img width="180" height="80" src="<?php echo site_url() ?>/wp-content/uploads/people/<?php echo $profile_img; ?>" alt="">

                </div> 
                <div style="float:left;width:75%;">
                    <div style="font-size: 20px;font-weight: bold;"><?php echo get_user_meta($current_id, 'first_name', true) . ' ' . get_user_meta($current_id, 'last_name', true); ?></div>
                    <div class="clear_10px"></div>
                    <?php
                    $sel_expertise = get_user_meta($current_id, 'expertise', true);
                    $sel_expertise = explode(",", $sel_expertise);
                    if (!empty($sel_expertise)) {
                        foreach ($sel_expertise as $row_s_exp) {
                            $exp_name_arr[] = $wpdb->get_row("SELECT * FROM `wp_expertise` where `expertise_id` = $row_s_exp")->exp_name;
                        }
                        echo implode(", ", $exp_name_arr);
                    }
                    ?>  
                    <div class="clear_10px"></div>
                    <?php
                    echo get_user_meta($current_id, 'streetaddress', true);
                    ?>
                    <div class="clear_10px"></div>
                    <?php
                    echo get_user_meta($current_id, 'city', true) . ', ' . get_user_meta($current_id, 'state', true) . ', ' . get_user_meta($current_id, 'zip', true) . ', ' . get_user_meta($current_id, 'country', true);
                    ?>
                    <div class="clear_10px"></div>
                    <?php
                    echo get_user_meta($current_id, 'phonenumber', true);
                    ?>

                </div>
                <div class="clear_both"></div>
                <h2>Overview</h2>
                <div class="clear_10px"></div>
                <?php
                echo get_user_meta($current_id, 'away_message', true);
                ?>
            </div>
            <?php if (get_user_meta($current_id, 'tag_type', true) == 'writer') { ?>
            <div class="clear_both"></div>
            
            <h1>Overall Work History</h1>
             <div class="clear_both"></div>
            <div class="overall_history all_section">
                <?php $complete_order = $wpdb->get_row("SELECT count(*) as c_order FROM `wp_content_order` WHERE `writer_id` = $current_id and status = 'Approved' LIMIT 1")->c_order; ?>
                Completed order: <?php echo $complete_order; ?>
                <div class="clear_10px"></div>
                <?php
                $avg_ratting = $wpdb->get_row("SELECT avg(`rating`) as avg_rating FROM `wp_feedback` WHERE `receiver_user_id` = $current_id")->avg_rating;
                if (!empty($avg_ratting)) {
                    echo star_image_loop(round($avg_ratting)) . ' (' . $avg_ratting . ')';
                } else {
                    echo star_image_loop(5, 'star-hash');
                }
                ?>
            </div>
              <div class="clear_both"></div>
              <h1>Availability</h1>
               <div class="clear_both"></div>
            <div class="availability all_section">
                <?php
                echo get_user_meta($current_id, 'weekly_capacity', true);
                ?>
            </div>
               <div class="clear_both"></div>
               <h1>Feedback</h1>
            <div class="clear_both"></div>
            <div class="feedback all_section">
                <script src="<?php echo site_url(); ?>/data-table/jquery.dataTables.min.js"></script>

                <link rel="stylesheet" href="<?php echo site_url(); ?>/data-table/jquery.dataTables.css"/> 

                <style>
                    thead th{text-transform: none !important;font-size: 13px;}
                </style>
                <?php
                $all_feedback = $wpdb->get_results("SELECT * FROM `wp_feedback` WHERE `receiver_user_id` = $current_id");
                if(empty($all_feedback)){
                    echo 'Yet no feedback has found.';
                }else {
                ?>
                <table style="font-size:92%!important;width:100%!important;" id="example" class="display" cellspacing="0" >
                    <thead style="background-color: #888;color:white;">
                        <tr>
                            <th style="text-align: center;width:11%;">Order ID</th>
                            <th>Client</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_feedback)) {
                            foreach ($all_feedback as $row_feedback) {
                                ?>
                                <tr>
                                    <td style="text-align: center;width:11%;">#<?php echo $row_feedback->order_id; ?></td>
                                    <td><?php echo brand_name($row_feedback->sender_user_id); ?></td>
                                    <td><?php echo star_image_loop($row_feedback->rating); ?></td>
                                    <td><?php echo $row_feedback->comments; ?></td>
                                    <td><?php echo date("d M Y", strtotime($row_feedback->created_date)); ?></td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
             
            <?php } ?>   
               <div class="clear_both"></div>
               <div class="clear_both"></div><div class="clear_both"></div>
        </div>
    </div>
</div>
<script>
    /*
                    function tab_view_func(tab) {
                        jQuery('.all_section').hide();
                        jQuery('.' + tab).show();
                        jQuery(".pagination_act").removeClass("act");
                        jQuery("#" + tab).addClass("act");
                    }
*/
                    jQuery(document).ready(function() {
                        jQuery('#example').dataTable({
                            "order": [[4, "desc"]],
                            "iDisplayLength": 25
                        });
                    });
</script>
