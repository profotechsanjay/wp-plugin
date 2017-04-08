<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['expertise_id'])) {
    //pr($_POST);
    $expertise_id = $_POST['expertise_id'];
    $content_pice_completed = $_POST['content_pice_completed'];
    $quality_level = $_POST['quality_level'];
    $writer_name = $_POST['writer_name'];
    $writer_id_arr = array();
    $co_writer_id = array();
    if ($expertise_id > 0) {
        $search_result = $wpdb->get_row("SELECT GROUP_CONCAT( CONCAT(user_id) SEPARATOR ',') as all_id FROM `wp_usermeta` WHERE `meta_key` = 'expertise' and find_in_set($expertise_id,`meta_value`)")->all_id;
        if (!empty($search_result)) {
            $writer_id_arr = array_merge($writer_id_arr, explode(",", $search_result));
        }
    }

    if ($content_pice_completed > 0) {
        $all_writer_by_total_order = $wpdb->get_results("SELECT count(*) as completed_order,writer_id FROM `wp_content_order` WHERE `status` = 'Approved' group by writer_id ");
        //pr($all_writer_by_total_order);
        foreach ($all_writer_by_total_order as $co_writer) {
            if ($content_pice_completed == 1 && $co_writer->completed_order < 25) {
                $co_writer_id[] = $co_writer->writer_id;
            }
            if ($content_pice_completed == 25 && $co_writer->completed_order > 25 && $co_writer->completed_order <= 100) {
                $co_writer_id[] = $co_writer->writer_id;
            }
            if ($content_pice_completed == 100 && $co_writer->completed_order >= 100 && $co_writer->completed_order <= 500) {
                $co_writer_id[] = $co_writer->writer_id;
            }
            if ($content_pice_completed == 500 && $co_writer->completed_order > 500) {
                $co_writer_id[] = $co_writer->writer_id;
            }
        }
        if (!empty($co_writer_id)) {
            $writer_id_arr = array_merge($writer_id_arr, $co_writer_id);
        }
    }
    if ($quality_level > 0) {
        $quality_level_plus_one = $quality_level + 1;
        $all_writers_by_their_feedback = $wpdb->get_results("SELECT avg(rating) as avg_rating,`receiver_user_id` FROM `wp_feedback` f INNER JOIN wp_usermeta u ON u.user_id = f.`receiver_user_id` where u.`meta_key` = 'tag_type' group by `receiver_user_id`  HAVING avg(rating) >= $quality_level && avg(rating)< $quality_level_plus_one ");
        //pr($all_writers_by_their_feedback);
        if (!empty($all_writers_by_their_feedback)) {
            foreach ($all_writers_by_their_feedback as $row_q_l) {
                $ql_writer_id[] = $row_q_l->receiver_user_id;
            }
            $writer_id_arr = array_merge($writer_id_arr, $ql_writer_id);
        }
    }
    if ($writer_name != "") {
        $search_name = explode(" ", $writer_name);
        $search_name = $search_name[0];
        $all_user_related_name = $wpdb->get_results("SELECT * FROM `wp_usermeta` WHERE `meta_key` = 'first_name' && `meta_value` Like '%$search_name%'");
        $all_writer_by_name = array();
        foreach($all_user_related_name as $row_user){
            if(role($row_user->user_id) == 'worker'){
                $all_writer_by_name[] =  $row_user->user_id;
            }
        }
        $writer_id_arr = array_merge($writer_id_arr, $all_writer_by_name);
        /*
        $all_writer_by_name = $wpdb->get_row("SELECT GROUP_CONCAT( CONCAT(receiver_user_id) SEPARATOR ',') as all_id FROM `wp_feedback` f INNER JOIN wp_usermeta u ON u.user_id = f.`receiver_user_id` where u.`meta_key` = 'first_name' && u.meta_value like '%$search_name%' ")->all_id;
        if (!empty($all_writer_by_name)) {
            $writer_id_arr = array_merge($writer_id_arr, explode(",", $all_writer_by_name));
        }
         * 
         */
    }
}
$writer_id_arr = array_unique($writer_id_arr);
$expertise_list = $wpdb->get_results("SELECT * FROM `wp_expertise` where status = 1 order by `exp_name` asc");
?>
<style>
    .profile_c{width:20%;}
    .image_c{height:20px;width:20px;cursor:pointer;}
    .text_c{font-weight: bold;font-size: 17px;}
    .width_c{width:22%;float:left;}
    tbody tr td{vertical-align: top;text-align: center;}
</style>
<div class="accoSet">
    <h2 class="fulllist">Search for Potential Writers for Your Account </h2>
</div>
<div style="padding: 20px;line-height: 23px;">
    <form name="writer_search_Frm" action="" method="post">  
        <div class="width_c">
            <div class="text_c">Expertise</div>
            <div class="clear_both"></div>
            <select style="min-width: 190px;" name="expertise_id">
                <option value="">Select Expertise</option>
                <?php
                if (!empty($expertise_list)) {
                    foreach ($expertise_list as $single_exp) {
                        ?>
                        <option <?php if (isset($expertise_id) && $expertise_id == $single_exp->expertise_id) echo 'selected'; ?> value="<?php echo $single_exp->expertise_id; ?>"><?php echo $single_exp->exp_name; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="width_c">
            <div class="text_c">Content Pieces Completed</div>
            <div class="clear_both"></div>
            <select style="min-width: 190px;" name="content_pice_completed">
                <option value="">Select</option>
                <option <?php if (isset($content_pice_completed) && $content_pice_completed == 1) echo 'selected'; ?> value="1">< 25</option>
                <option <?php if (isset($content_pice_completed) && $content_pice_completed == 25) echo 'selected'; ?> value="25">25-100</option>
                <option <?php if (isset($content_pice_completed) && $content_pice_completed == 100) echo 'selected'; ?> value="100">100-500</option>
                <option <?php if (isset($content_pice_completed) && $content_pice_completed == 500) echo 'selected'; ?> value="500">500+</option>
            </select>
        </div>
        <div class="width_c">
            <div class="text_c">Quality Level</div>
            <div class="clear_both"></div>
            <?php for ($i = 5; $i > 1; $i--) { ?>
                <input name="quality_level" type="radio" <?php if (isset($quality_level) && $quality_level == $i) echo 'checked'; ?> value="<?php echo $i; ?>"><span style="margin-left:15px;"><?php echo star_image_loop($i); ?></span>
                <div style="clear: both;height:5px;"></div>
            <?php } ?>
        </div>
        <div class="width_c">
            <div class="text_c">Or Search by name</div>
            <div class="clear_both"></div>
            <input name="writer_name" type="text" value="<?php if (isset($writer_name)) echo $writer_name; ?>">

        </div>

        <div style="float:right;width:10%;">
            <div class="clear_both"></div><div class="clear_both"></div>
            <input style="background: #FB6800;color:white;font-weight: bold;" type="submit" value="Search">
        </div>
        <div class="clear_both"></div>
    </form>
</div>
<?php if (isset($writer_id_arr)) {
    ?>
    <script src="<?php echo $siteurl; ?>/data-table/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="<?php echo $siteurl; ?>/data-table/jquery.dataTables.css"/> 
    <table style="font-size:92%!important;width:100%!important;" id="example" class="display" cellspacing="0" >
        <thead style="background-color: #888;color:white;">
            <tr>
                <th>Picture</th>
                <th>Name</th>
                <th>Expertise</th>
                <th>Content Completed</th>
                <th style="width:13%">Quality</th>
                <th>Last Active</th>
                <th>Favorite</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($writer_id_arr)) {
                foreach ($writer_id_arr as $single_writer_id) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            $user_photo = 'default.png';
                            $get_user_photo = get_user_meta($single_writer_id, 'user_photo', true);
                            if ($get_user_photo != '') {
                                $user_photo = $get_user_photo;
                            }
                            ?>
                            <img style="width:120px;height:100px;" src="<?php echo $siteurl . '/wp-content/uploads/people/' . $user_photo ?>">
                        </td>
                        <td>
                            <a target="_blank" href="<?php echo $siteurl; ?>/order-content/?type=writer-search&profile_id=<?php echo $single_writer_id; ?>">
                                <?php echo full_name($single_writer_id); ?>
                            </a>
                        </td>

                        <td>
                            <?php
                            $sel_expertise = get_user_meta($single_writer_id, 'expertise', true);
                            $sel_expertise = explode(",", $sel_expertise);
                            $exp_name_arr = array();
                            if (!empty($sel_expertise)) {
                                foreach ($sel_expertise as $row_s_exp) {
                                    $exp_name_arr[] = $wpdb->get_row("SELECT * FROM `wp_expertise` where `expertise_id` = $row_s_exp")->exp_name;
                                }
                                echo implode(", ", $exp_name_arr);
                            }
                            ?>
                        </td>
                        <td><?php echo $wpdb->get_row("SELECT count(*) as c_order FROM `wp_content_order` WHERE `writer_id` = $single_writer_id and status = 'Approved' LIMIT 1")->c_order; ?></td>
                        <td style="width:13%">
                            <?php
                            $avg_ratting = $wpdb->get_row("SELECT avg(`rating`) as avg_rating FROM `wp_feedback` WHERE `receiver_user_id` = $single_writer_id")->avg_rating;
                            if (!empty($avg_ratting)) {
                                echo star_image_loop(round($avg_ratting)).'<br/>('.$avg_ratting.')';
                            } else {
                              echo star_image_loop(5,'star-hash');  
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $last_login_data = $wpdb->get_row("SELECT * FROM `wp_simple_login_log` WHERE `uid` = $single_writer_id order by `id` desc LIMIT 1");
                            if (!empty($last_login_data)) {
                                echo date("d M Y", strtotime($last_login_data->time)); // (h:i A)
                            } else {
                                echo 'never logged in';
                            }
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php
                            $check_exist_writer = get_user_meta($single_writer_id, 'TAG_CLIENT_ID_' . $agency_id, true);
                            if ($check_exist_writer != "") {
                                $image_name = 'heart';
                                $set_writer = 1;
                            } else {
                                $image_name = 'heart-hash';
                                $set_writer = 0;
                            }
                            ?> 
                            <img class="img_<?php echo $single_writer_id; ?> image_c" onclick="writer_add_func('<?php echo $agency_id; ?>', '<?php echo $single_writer_id; ?>', '<?php echo $set_writer; ?>')" src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $image_name; ?>.png">

                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
<?php } ?>
<script>
    function writer_add_func(client_id, writer_id, set_writer) {
        if (set_writer == '0') {
            jQuery(".img_" + writer_id).attr({src: "<?php echo get_template_directory_uri(); ?>/images/heart.png"});
            jQuery(".img_" + writer_id).attr({onclick: "writer_add_func(" + client_id + "," + writer_id + ",1)"});
        } else if (set_writer == '1') {
            jQuery(".img_" + writer_id).attr({src: "<?php echo get_template_directory_uri(); ?>/images/heart-hash.png"});
            jQuery(".img_" + writer_id).attr({onclick: "writer_add_func(" + client_id + "," + writer_id + ",0)"});
        }
        jQuery.ajax({
            type: 'POST',
            crossDomain: true,
            url: '<?php echo site_url(); ?>/ajax-data.php',
            data: {'page': 'writer_search', 'client_id': client_id, 'writer_id': writer_id, 'set_writer': set_writer},
            success: function(html_data)
            {
               //alert(html_data);
            }
        });
    }
    
    jQuery(document).ready(function() {
        jQuery('#example').dataTable({
            "order": [[1, "asc"]],
            "iDisplayLength": 25
        });
    });
</script>
