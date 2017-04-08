<?php
global $wpdb;
echo $role_worker_list[0];

function objToArrayData($d){
   
    if (is_object($d))
        $d = get_object_vars($d);

    return is_array($d) ? array_map(__FUNCTION__, $d) : $d;

}

add_filter('tiny_mce_before_init', 'wpse24113_tiny_mce_before_init');


function wpse24113_tiny_mce_before_init($initArray) {
    $initArray['setup'] = <<<JS
[function(ed) {
    ed.onKeyUp.add(function(ed, e) {
		arr = this.getContent().split(" ");		
		jQuery(".word_count").text(arr.length);
		
		ttl = parseInt(jQuery(".word-total").text());
		
		if(arr.length >= ttl)
			enableButtons();
		else
			disableButtons();
		
    });
}][0]
JS;
    return $initArray;
}

$order_id = $post_data['order_id'];

$CSM_role_id = 5; //CSM

$Wsubject = 'Content has submitted of order #' . $order_id;

$sql = "SELECT * FROM wp_content_order WHERE order_id =" . $order_id;
$order = $wpdb->get_row($sql); //Need it both two space
$keywordDat = get_user_meta($order->user_id, "Content_keyword_Site", true);
$activation = $keywordDat['activation'];
$Synonyms_keyword_arr = $keywordDat['Synonyms_keyword'];

//---------


$landingpage = $keywordDat["landing_page"];

$primarylander = $keywordDat["primarylander"];
$secondarylander = $keywordDat["secondarylander"];
$KeyWordQuery = $wpdb->get_row('SELECT meta_key FROM wp_usermeta WHERE user_id = ' . $order->user_id . ' AND meta_value = "' . $order->keys . '"');

$keywrds = explode('_', $KeyWordQuery->meta_key);
$ks = $keywrds[count($keywrds) - 1];

//$ks = $keywrds[3];

$j = $ks - 1;
if (isset($post_data['debug']))
    pr($KeyWordQuery, '========$KeyWordQuery AND Index=[' . $j . ']=====');

//---------
//$kw = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `meta_value` LIKE '$order->keys'");
$kw = $wpdb->get_results('SELECT meta_key FROM `wp_usermeta` WHERE user_id='.$order->user_id.' AND meta_key LIKE "LE_Repu_Keyword_%" AND `meta_value` LIKE "'.$order->keys.'"');


//Only for active keywords 
foreach ($kw as $row_same_key) {
    $syn_keywords_index = explode('_', $row_same_key->meta_key);
    $syn_keywords_index = $syn_keywords_index[count($syn_keywords_index) - 1];
    //echo $syn_keywords_index.'<br/>';
    if ($activation[$syn_keywords_index - 1] != 'inactive') {
        $Synonyms_keyword = $Synonyms_keyword_arr[$syn_keywords_index - 1];
        $Synonyms_keyword = array_filter($Synonyms_keyword);
    }
}

//------------------------
if (empty($Synonyms_keyword)) {
    $Synonyms_keyword = array();
    $ind = str_replace("LE_Repu_Keyword_", "", $kw[0]->meta_key) - 1;
    foreach ($keywordDat['Synonyms_keyword'][$ind] as $row_syn) {
        if (trim($row_syn) != '') {
            $Synonyms_keyword[] = trim($row_syn);
        }
    }
}
//$Synonyms_keyword = $keywordDat['Synonyms_keyword'][$ind];

$buffersiteData = get_user_meta($order->user_id, "Content_Buffer_Site", true);


if (!empty($buffersiteData)) {
    $countSite = count($buffersiteData['buffersiteurl']);
    $buffersiteData1 = $buffersiteData['buffersiteurl'];
    $buffer_login_url = $buffersiteData['buffer_login_url'];
    $buffer_login_user = $buffersiteData['buffer_login_user'];
    $buffer_login_password = $buffersiteData['buffer_login_password'];
    $sitetype = $buffersiteData['sitetype'];
    $post_user = $buffersiteData['post_user'];
    $buildlink = $buffersiteData['buildlink'];

    foreach ($buffersiteData1 as $Bufferdat => $val) {
        $in = $Bufferdat + 1;
        if ($post_user[$Bufferdat] == 'post_draft') {
            $postas = 'Draft Post';
        } elseif ($post_user[$Bufferdat] == 'post_live') {
            $postas = 'Live Post';
        } elseif ($post_user[$Bufferdat] == 'post_review') {
            $postas = 'Please post it into the contentadmin section of MCC.com so we can review it.';
        } else {
            $postas = 'n/a';
        }

        if ($buffersiteData1[$Bufferdat] == $order->sites) {
            $login_url = $buffer_login_url[$Bufferdat];
            $username = $buffer_login_user[$Bufferdat];
            $password = $buffer_login_password[$Bufferdat];
            //$site_type = $sitetype[$Bufferdat];
        }
        $in++;
    }
}
?>
<!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
<script>tinymce.init({selector: 'textarea'});</script>-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script type="text/javascript">
    jQuery(function() {
        jQuery(".datepicker").datepicker();
    });
</script>
<style type="text/css">
    .editrtext{
        min-height: 400px;
    }
    .left_class{float:left;width:27%;font-size: 17px; font-weight: bold;}
    .right_class{float:left;width:68%;}
    .right_div_left_class{float:left;width:60%;font-size: 17px; font-weight: bold;}
    .right_div_right_class{float:left;width:39%;}
    .left_o_class{float:left;width:8%;font-size: 17px; font-weight: bold;margin-left: 1%;}
    .right_o_class{float:left;width:10%;}
    select{width:95%;}
    .orange_btn_c{background: #FB6800!important;color:white!important;font-weight: bold!important;}
    .success_c{color:green;text-align: center;font-weight: bold;font-size:17px;}
    
    /*******************/
    .tooltip{opacity:unset!important;font-size:unset!important;display:unset!important;}

    a.tooltip {

        //background:url(<?php echo get_template_directory_uri(); ?>/images/hints.png) no-repeat;

        position:absolute;

        margin-top:5px;

        width: 30px!important;

    }
    .tooltip span {

        background-color:#3d6199;

        color:white;

    }
    /*******************/
</style>
<div class="accoSet"><h2 class="fulllist">
        Order ID #<?php echo $order->order_id; ?>
        Order Status:<?php echo $order->status ?>
    </h2></div>
<?php
if (isset($success_msg)) {
    echo '<div class="success_c">' . $success_msg . '</div>';
}
?>
<div class="clear_both"></div>
<div style="padding: 0px 20px;">
    <div style="font-size: 17px;float:left;width:68%">
        <div class="left_class">Site:</div>
        <div class="right_class"> <?php echo $order->sites; ?></div>
        <div class="clear_both"></div>
<?php if ($order->blog_title != "") { ?>
            <div class="left_class">Blog Title:</div>
            <div class="right_class"> <?php echo $order->blog_title; ?></div>
            <div class="clear_both"></div>
<?php } ?>
        <div class="left_class">Keyword:</div>
        <div class="right_class"> <?php echo $order->keys; //get_meta_value($order['user_id'], $order['keys']);         ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Keyword Synonyms:</div>
        <div class="right_class"> <?php echo implode(", ", $Synonyms_keyword); ?></div>
        <div class="clear_both"></div>
<?php
if ($order->notes != '') {
    ?>
            <div class="left_class">Notes:</div>
            <div class="right_class"> <?php echo $order->notes; ?></div>
            <div class="clear_both"></div>
    <?php
}
?>
        <div class="left_class">Login URL:</div>
        <div class="right_class"> <?php echo $login_url; ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Username:</div>
        <div class="right_class"> <?php echo $username; ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Password:</div>
        <div class="right_class"> <?php echo $password; ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Post as:</div>
        <div class="right_class"> <?php echo $postas; ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Landing Page:</div>
        <div class="right_class"> <?php echo $landingpage[$j][0]; ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Home Page:</div>
        <div class="right_class"> <?php echo $primarylander[$j]; ?></div>
        <div class="clear_both"></div>
        <div class="left_class">Resource Page:</div>
        <div class="right_class"> <?php echo $secondarylander[$j]; ?></div>
        <div class="clear_both"></div>
<?php
if ($order->status == 'Request Changes') {
    ?>
            <div class="left_class">Request Changes:</div>
            <div class="right_class"> <?php echo $order->request_changes; ?></div>
            <div class="clear_both"></div>
    <?php
}
?>
    </div>
    <div style="font-size: 17px;float:right;width:28%">
        <div class="right_div_left_class">Order ID:</div>
        <div class="right_div_right_class"> <?php echo $order->order_id; ?></div>
        <div class="clear_both"></div>
        <div class="right_div_left_class">Order Status:</div>
        <div class="right_div_right_class"> <?php echo $order->status; ?></div>
        <div class="clear_both"></div>
        <div class="right_div_left_class">Word count:</div>
        <div class="right_div_right_class word-total"> <?php echo $order->wordcount; ?></div>
        <div class="clear_both"></div>
        <div class="right_div_left_class">Image:</div>
        <div class="right_div_right_class"> <?php echo $order->image; ?></div>
        <div class="clear_both"></div>
        <div class="right_div_left_class">Type:</div>
        <div class="right_div_right_class"> <?php echo $order->type; ?></div>
        <div class="clear_both"></div>
    </div>
<?php
if ($order->status != 'Canceled') {
    if ($order->content != "") {
        echo '<div style="clear:both;"></div>';
        echo seo_check($order->keys, $order->content, $order->post_url);
    }
    ?>
    </div>

    <div class="clear_both"></div>
    <h3>Content:</h3>
    <div style="clear:both;height:10px;"></div>
    <?php
    if ($order->status == 'Approved') {
        echo $order->content;
    } else {
        ?>
        <form name="content_Frm" id="content_Frm" action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div id="content_div">
                      
                <div class="editrtext"><div class="contenttext" style="display: none;"><?php echo $order->content; ?></div></div>
                                    
                <textarea name="content" id="content" style="display: none;"><?php echo $order->content; ?></textarea>
                Words: <span class="word_count">0</span>

                <div class="clear_both"></div>
        <?php
        ///*
        $link_stets = unserialize($order->link_stets);
        $link_stets = objToArrayData($link_stets);
        for ($link = 1; $link <= 3; $link++) {
            
            ?>
                    <span style="float:left;font-weight: bold;">Link Stats <?php echo $link; ?>:  </span>
                    <div class="left_o_class" style="width: 14%;">Primary Keyword:</div>
                    <div class="right_o_class">
                        <select name="link_stets[<?php echo $link; ?>][link_primary_keyword]">;
                            <option value="No Links">No Links</option>
                        </select>
                    </div>
                    <div class="left_o_class">Keywords:</div>
                    <div class="right_o_class" style="width: 18%;">
                        <select name="link_stets[<?php echo $link; ?>][link_keywords]">
                            <option value="">Select</option>
                            <option <?php if ($link_stets[$link]['link_keywords'] == $order->keys) echo 'selected'; ?> value="<?php echo $order->keys ?>"><?php echo $order->keys ?></option>
                            <?php
                            if (!empty($Synonyms_keyword)) {
                                foreach ($Synonyms_keyword as $row_syn) {
                                    if ($row_syn != "") {
                                        ?>
                                        <option <?php if ($link_stets[$link]['link_keywords'] == $row_syn) echo 'selected'; ?>  value="<?php echo $row_syn; ?>"><?php echo $row_syn; ?></option>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="left_o_class">Link Type:</div>
                    <div class="right_o_class">
                        <select name="link_stets[<?php echo $link; ?>][link_type]">
                            <option value="">Select</option>
                            <option <?php if ($link_stets[$link]['link_type'] == 'Exact') echo 'selected'; ?>  value="Exact">Exact</option>
                            <option <?php if ($link_stets[$link]['link_type'] == 'Naked') echo 'selected'; ?> value="Naked">Naked</option>
                            <option <?php if ($link_stets[$link]['link_type'] == 'Syn') echo 'selected'; ?> value="Syn">Syn</option>
                            <option <?php if ($link_stets[$link]['link_type'] == 'Brand') echo 'selected'; ?> value="Brand">Brand</option>
                            <option <?php if ($link_stets[$link]['link_type'] == 'Phrase') echo 'selected'; ?> value="Phrase">Phrase</option>
                            <option <?php if ($link_stets[$link]['link_type'] == 'Generic') echo 'selected'; ?> value="Generic">Generic</option>
                        </select>
                    </div>
                    <div class="left_o_class">Link:</div>
                    <div class="right_o_class">
                        <select name="link_stets[<?php echo $link; ?>][link]">
                            <option value="">Select</option>
            <?php /* <option <?php if ($link_stets[$link]['link'] == 'Actual URL') echo 'selected'; ?> value="Actual URL">Actual URL</option> */ ?>
                            <option <?php if ($link_stets[$link]['link'] == 'Homepage') echo 'selected'; ?> value="Homepage">Homepage</option>
                            <option <?php if ($link_stets[$link]['link'] == 'Landing Page') echo 'selected'; ?> value="Landing Page">Landing Page</option>
                            <option <?php if ($link_stets[$link]['link'] == 'Blog Post') echo 'selected'; ?> value="Blog Post">Blog Post</option>
                        </select>
                    </div>
                    <div class="clear_both"></div>
            <?php
        }
        
        
        ?>
                <div class="clear_both"></div>
                <?php
                /*
                  <div class="left_class">Optimized:</div>
                  <div class="right_class" style="width: 25%;">
                  <select  name="optimized">
                  <option <?php if ($order->optimized == '') echo 'selected'; ?> value="">Select</option>
                  <option <?php if ($order->optimized == 'Yes') echo 'selected'; ?> value="Yes">Yes</option>
                  <option <?php if ($order->optimized == 'No') echo 'selected'; ?> value="No">No</option>
                  </select>
                  </div>
                 */
                ?>
                <div class="left_class">Keyword in Title:</div>
                <div class="right_class" style="width: 25%;">
                    <input type="checkbox" class="optimization" name="opt_keyword_in_title" <?= (isset($order->opt_keyword_in_title) && $order->opt_keyword_in_title) ? "checked" : ""; ?> />
                </div>

                <div class="clear_both"></div>

                <div class="left_class">Use of Header Tags:</div>
                <div class="right_class" style="width: 25%;">
                    <input type="checkbox" class="optimization" name="opt_header_tags" <?= (isset($order->opt_header_tags) && $order->opt_header_tags) ? "checked" : ""; ?> />
                </div>

                <div class="clear_both"></div>

                <div class="left_class">Keyword used once in text body:</div>
                <div class="right_class" style="width: 25%;">
                    <input type="checkbox" class="optimization" name="opt_keyword_in_body" <?= (isset($order->opt_keyword_in_body) && $order->opt_keyword_in_body) ? "checked" : ""; ?> />
                </div>

                <div class="clear_both"></div>

                <div class="left_class">Landing Page link included:</div>
                <div class="right_class" style="width: 25%;">
                    <input type="checkbox" class="optimization" name="opt_landing_ink" <?= (isset($order->opt_landing_ink) && $order->opt_landing_ink) ? "checked" : ""; ?> />
                </div>

                <div class="clear_both"></div>

                <div class="left_class">Home page link included:</div>
                <div class="right_class" style="width: 25%;">
                    <input type="checkbox" class="optimization" name="opt_home_link" <?= (isset($order->opt_home_link) && $order->opt_home_link) ? "checked" : ""; ?> />
                </div>

                <div class="clear_both"></div>

                <div class="left_class">Spellcheck:</div>
                <div class="right_class" style="width: 25%;">
                    <input type="checkbox" class="optimization" name="opt_spellcheck" <?= (isset($order->opt_spellcheck) && $order->opt_spellcheck) ? "checked" : ""; ?> />
                </div>

                <div class="clear_both"></div>
                <div class="left_class">Go Live Date:</div>
                <div class="right_class"><input type="text" name="live_date" value="<?php echo $order->live_date; ?>" class="datepicker"></div>
                <div class="clear_both"></div>
                <div class="left_class">Site Post URL:</div>
                <div class="right_class"><input type="text" class="site_post_url" name="post_url" value="<?php echo $order->post_url; ?>" style="width:70%;"></div>
                <div class="clear_both"></div>
                <div class="left_class">Image:</div>
                <div class="right_class">
        <?php if ($order->image_name != '') { ?>
                        <a class="fancybox" target="_blank" href="<?php echo site_url(); ?>/wp-content/uploads/content_image/<?php echo $order->image_name; ?>">
                            <img src="<?php echo site_url(); ?>/wp-content/uploads/content_image/<?php echo $order->image_name; ?>" style="width:100px;height:100px;">
                        </a>
                        <div class="clear_both"></div>
        <?php } ?>
                    <input type="file" name="image_name" style="width:70%;">
                    <div style="clear:both;height:5px;"></div>
                    <span style="margin-left:10px;">[jpg,jpeg,gif,png format are allowed]</span>
                </div>
                <div class="clear_both"></div>

        <?php
        
        $onc = "jQuery('#status').val('Ordered');";
        if ($order->status == 'Request Changes')
            $onc = "jQuery('#status').val('Request Changes');";
        ?>

                <input type="hidden" name="status" id="status" value="">
                <input name='save' class="orange_btn_c" onclick="<?= $onc; ?><?php //jQuery('.site_post_url').removeClass('required');   ?>" type="submit" value="Save Draft">
                <input class="orange_btn_c" onclick="jQuery('#preview_submission').show();
                        jQuery('#content_div').hide();" type="button" value="Preview Submission" disabled>
                <a style="text-decoration:none;" href="javascript:;" class="tooltip"><input style="margin-top: -5px;" name='sbmt' class="orange_btn_c de_btn" onclick="jQuery('#status').val('Delivered');<?php //jQuery('.site_post_url').addClass('required');   ?>" type="submit" value="Submit Content" disabled><span class="mess_info">Submit content button will be activated only after fill up required word count. Please fill up total <?php echo $order->wordcount; ?> words on your content area.</span></a>
                
                <div class="clear_both"></div><div class="clear_both"></div>
            </div>    

            <div id="preview_submission" style="display:none;">
        <?php echo html_entity_decode(orginal_html($order->content)); ?>
                <div class="clear_both"></div>

                <div class="left_class">Go Live Date:</div>
                <div class="right_class"><?php echo $order->live_date; ?></div>
                <div class="clear_both"></div>

                <div class="left_class">Site Post URL:</div>
                <div class="right_class"><?php echo $order->post_url; ?></div>
                <div class="clear_both"></div>

        <?php if ($order->image_name != "") { ?>
                    <div class="left_class">Image:</div>
                    <div class="right_class">
                        <a class="fancybox" href="<?php echo site_url(); ?>/wp-content/uploads/content_image/<?php echo $order->image_name; ?>">
                            <img style="width:100px;height:100px;" src="<?php echo site_url(); ?>/wp-content/uploads/content_image/<?php echo $order->image_name; ?>">
                        </a>
                    </div>
        <?php } ?>
                <div class="clear_both"></div>

                <input class="orange_btn_c" onclick="jQuery('#preview_submission').hide();
                                jQuery('#content_div').show();" type="button" value="Edit Content">
                <a style="text-decoration:none;" href="javascript:;" class="tooltip"><input style="margin-top: -5px;" name='sbmt' class="orange_btn_c de_btn" onclick="jQuery('#status').val('Delivered');<?php //jQuery('.site_post_url').addClass('required');   ?>" type="submit" value="Submit Content" disabled><span class="mess_info">Submit content button will be activated only after fill up required word count. Please fill up total <?php echo $order->wordcount; ?> words on your content area.</span></a>
                <div class="clear_both"></div>

            </div>
        </form>
        
        <?php
    }
}
?>
<div style="clear:both;"></div>

