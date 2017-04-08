<?php

                    if (isset($_POST['edit_form_submit'])) {

                        update_user_meta($UserID, "Posted Content", $_POST);
                        //all dl work has here le_posted_content_url_add.php we have added LE campaign 

                        foreach ($_POST['ss'] as $new_post_index => $new_post) {
                            $social_url = $_POST['url'][$new_post_index];
                            //echo $social_url.'-------'.$new_post_index;
                            if ($new_post == 'yes') {
                                $check_url = $wpdb->query("SELECT * FROM `wp_social_adr` where url = '$social_url' and user_id = '$UserID' LIMIT 1");
                                if (empty($check_url)) {
                                    $insert1 = $wpdb->query('insert into wp_social_adr(url,status,user_id)values("' . $social_url . '","0","' . $UserID . '")');
                                }
                            } else {
                                $wpdb->query("DELETE FROM `wp_social_adr` WHERE url = '$social_url' and user_id = '$UserID'");
                            }
                        }

                        // for seonitro setup
                        foreach ($_POST['il'] as $sl_index => $sl_post) {
                            $seonitro_url = $_POST['url'][$sl_index];
                            //echo $seonitro_url; exit;
                            if ($sl_post == 'yes') {
                                $check_url = $wpdb->query("SELECT * FROM `wp_seo_nitro` where post_url = '$seonitro_url' and user_id = '$UserID' LIMIT 1");
                                if (empty($check_url)) {
                                    $wpdb->query("INSERT INTO `wp_seo_nitro` (`id`, `user_id`, `post_date`, `post_url`, `src`) VALUES (NULL, '$UserID', '" . date('Y-m-d') . "', '$seonitro_url', 'SEO_NITRO');");
                                }
                            } else {
                                $wpdb->query("DELETE FROM `wp_seo_nitro` WHERE post_url = '$seonitro_url' and user_id = '$UserID'");
                            }
                        }
                    }
                    $posted_list = get_user_meta($UserID, "Posted Content", true);
                    //pr($posted_list);
                    $count_posted_content = count($posted_list['url']);
                    ?>
                    <div class="accoSet">
                        <h2 class="fulllist">All Posted Content</h2>
                    </div>
                    <div class="item-postedContList">
                        <?php
                        $check_campagian_arr = get_user_meta($UserID, "campaign", true);
                        $check_camp_id = $check_campagian_arr['id'];
                        if (!empty($posted_list)) {
                            ?>
                            <table style="margin-top:10px; border-radius: 3px 3px 3px 3px; width:100%; float:left; border: 1px solid #cecece;">
                                <tbody>
                                    <tr style="background-color:#F3F4F4;">
                                        <th style="float:left; width:30%; padding:9px 4px;">URL</th>
                                        <th style="float:left; width:27%; padding:9px 4px;">Key1</th>
                                        <th style="float:left; width:10%; padding:9px 4px;">DL</th>
                                        <th style="float:left;  width:8%; padding:9px 4px;">SS</th>
                                        <th style="float:left;  width:8%; padding:9px 4px;">SL</th>
                                        <th style="float:left;  width:10%; padding:9px 4px;">Action</th>
                                    </tr>
                                    <?php for ($inc_p = 0; $inc_p < $count_posted_content; $inc_p++) { ?>
                                        <tr style="font-size:14px;background-color: <?php echo $inc_p % 2 == 0 ? '#fff' : '#eee' ?>; text-align:center;">
                                            <td style="float:left; width:30%; padding:9px 4px;"><?php echo $posted_list['url'][$inc_p] ?></td>								
                                            <td style="float:left; width:27%; padding:9px 4px;"><?php if ($posted_list['keyword1'][$inc_p] != '') echo get_user_meta($UserID, $posted_list['keyword1'][$inc_p], true); ?></a></td>
                                            <td style="float:left; width:10%; padding:9px 4px;">
                                                <?php
                                                if ($posted_list['dl'][$inc_p] == 'yes') {
                                                    if ($check_camp_id > 0) {
                                                        if (posted_content_le_setup_check($UserID, $posted_list['url'][$inc_p]) > 0) {
                                                            $dl_le = 'Yes';
                                                        } else
                                                            $dl_le = 'Unconfirmed';
                                                    }
                                                    else {
                                                        $dl_le = 'No LE campaign';
                                                    }
                                                } else {
                                                    $dl_le = $posted_list['dl'][$inc_p];
                                                }

                                                echo $dl_le;
                                                ?>
                                            </td>
                                            <td style="float:left; width:8%; padding:9px 4px;"><?php echo $posted_list['ss'][$inc_p] ?></a></td>
                                            <td style="float:left; width:8%; padding:9px 4px;"><?php echo $posted_list['il'][$inc_p] ?></a></td>
                                            <td style="float:left; width:10%; padding:9px 4px;">
                                                <a style="text-decoration:none;" href="#view_<?php echo $inc_p; ?>" class="fancybox">View</a> |
                                                <a style="text-decoration:none;" href="#order_content_edit_div" onclick="edit_order_content(<?php echo $inc_p; ?>)" class="fancybox">Edit</a>
                                            </td>
                                        </tr>
                                    <div id="view_<?php echo $inc_p; ?>" style="display:none;width:400px;">
                                        <div class="left_posted_class">URL:</div><div class="right_posted_class"><?php echo $posted_list['url'][$inc_p] ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">Keyword 1:</div><div class="right_posted_class"><?php if ($posted_list['keyword1'][$inc_p] != '') echo get_user_meta($UserID, $posted_list['keyword1'][$inc_p], true); ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">Keyword 2:</div><div class="right_posted_class"><?php if ($posted_list['keyword2'][$inc_p] != '') echo get_user_meta($UserID, $posted_list['keyword2'][$inc_p], true); ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">Keyword 3:</div><div class="right_posted_class"><?php if ($posted_list['keyword3'][$inc_p] != '') echo get_user_meta($UserID, $posted_list['keyword3'][$inc_p], true); ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">Keyword 4:</div><div class="right_posted_class"><?php if ($posted_list['keyword4'][$inc_p] != '') echo get_user_meta($UserID, $posted_list['keyword4'][$inc_p], true); ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">Keyword 5:</div><div class="right_posted_class"><?php if ($posted_list['keyword5'][$inc_p] != '') echo get_user_meta($UserID, $posted_list['keyword5'][$inc_p], true); ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">DL:</div><div class="right_posted_class"><?php echo $dl_le; ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">SS:</div><div class="right_posted_class"><?php echo $posted_list['ss'][$inc_p] ?></div><div class="clear_both"></div>
                                        <div class="left_posted_class">SL:</div><div class="right_posted_class"><?php echo $posted_list['il'][$inc_p] ?></div><div class="clear_both"></div>
                                    </div>

                                <?php }
                                ?>
                                </tbody>
                            </table>  
                            <?php
                            include_once('order-content-edit.php');
                        } else {
                            echo '<br/><center><h3>You have no previous posted content.</h3></center>';
                        }
                        ?>  
                    </div>              