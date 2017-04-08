<?php

include_once 'common.php';
include_once ABSPATH . '/wp-content/themes/twentytwelve/analytics/my_functions.php';
global $wpdb;

$base_url = site_url();
$locations = $wpdb->get_results
(
    $wpdb->prepare
    (
            "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC",""
    )
);

?>

<div class="contaninerinner trackdiv">     
    <h4>Global Conversion URLs </h4>
    <div class="panel panel-primary">
        <div class="panel-heading">Global Conversion URLs</div>
        <div class="panel-body">
            <form action="#" class="form-horizontal" method="post">

                <div class="form-group">
                    <label class="col-md-3 control-label">Select Location (Account)</label>
                    <div class="col-md-6">
                        <select required class="form-control chosen" name="gconversionurl" id="gconversionurl">
                            <option value="">Select Location (Account)</option>
                            <?php
                            foreach ($locations as $location) {
                                $id_loc = intval($_REQUEST['location_id']);
                                $sel = '';
                                if($id_loc == $location->id){
                                    $sel = 'selected="selected"';
                                }
                                $brand = get_user_meta($location->MCCUserId, 'BRAND_NAME', TRUE);
                                if (empty($brand)) {
                                    $brand = get_user_meta($location->MCCUserId, 'company_name', TRUE);
                                }
                                ?>
                                <option <?php echo $sel; ?> value="<?php echo $location->id; ?>"><?php echo $brand; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                <div class="row"><hr/></div>
                <div class="trackingcode">
                    <?php if(isset($_REQUEST['location_id']) && intval($_REQUEST['location_id']) > 0){
                        ?>                       
                            <?php

                            $idloc = intval($_REQUEST['location_id']);
                            $loc = $wpdb->get_row
                            (
                                $wpdb->prepare
                                (
                                        "SELECT MCCUserId FROM " . client_location() . " WHERE id = %d",$idloc
                                )
                           );                                
                           if(!empty($loc)){
                                $locbrand = get_user_meta($loc->MCCUserId, 'BRAND_NAME', TRUE);
                                $locwebsite = get_user_meta($loc->MCCUserId, 'website', TRUE);
                                $current_id = $loc->MCCUserId;                                
                                $gc_setting = get_user_meta($current_id, 'global-conversion-setting', true); //need both place
                                
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['landing_url'])) {
                                    $check_duplicate_url = array();
                                    $insert_land_url['MCCUserId'] = $current_id;
                                    $post_landing_url = array_unique($_POST['landing_url']);
                                    //$post_landing_url = $_POST['landing_url'];                                    
                                    mysql_query("DELETE FROM `global_landing_urls` WHERE `MCCUserId` = $current_id");
                                                                       
                                    foreach ($post_landing_url as $row_url) {
                                        $row_url = trim($row_url);
                                        if ($row_url != "") {
                                            $insert_land_url['landing_url'] = $row_url;
                                            $clear_url = website_format($row_url);
                                            if (!in_array($clear_url, $check_duplicate_url)) {
                                                insert('global_landing_urls', $insert_land_url);
                                                $check_duplicate_url[] = $clear_url;
                                            }
                                        }
                                    }
                                    $success_msg = 'Successfully Saved Conversions Landing Page URLs.';
                                }
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['globalPageURL'])) {

                                    update_user_meta($current_id, 'global-conversion-setting', $_POST['global-conversion-setting']); //need both place

                                    $gc_setting = get_user_meta($current_id, 'global-conversion-setting', true);

                                    $insert_c_url['MCCUserId'] = $current_id;



                                    mysql_query("DELETE FROM `global_conversion_urls` WHERE `MCCUserId` = $current_id");

                                    foreach ($_POST['globalPageURL'] as $row_url) {

                                        if ($row_url != "") {

                                            $insert_c_url['globalPageURL'] = trim($row_url);

                                            insert('global_conversion_urls', $insert_c_url);
                                        }
                                    }

                                    $success_msg = 'Successfully Saved Thank You Page URLs.';
                                }
                                
                                
                                $conversions_step = get_user_meta($current_id, 'conversions_step', true);
                                $ConvTrackPrevURLsA = result_array("SELECT globalPageURL FROM `global_conversion_urls` WHERE `MCCUserId` = $current_id");                                
                                $all_landing_url_for_conv = result_array("SELECT landing_url FROM `global_landing_urls` WHERE `MCCUserId` = $current_id");
                                ?>
                                <h4><?php echo $locbrand; ?> </h4>
                                <div>
                                    
                                    <style>

                                        .left_task{width:18%;float:left;font-weight: bold;margin-top: 7px;}

                                        .right_task{width:50%;float:left;}

                                        .clear_both{clear:both;height:20px;}

                                        .required{color:black;}

                                        label.error{color:red;margin-left:10px;}

                                        .success_c{color:green;font-weight: bold;font-size:17px;}

                                        .fieldset_class {
                                            border: 1px solid #d14836;
                                        }
                                        .legend_class {
                                            margin-left: 50px;
                                            padding: 0 10px;
                                            font-weight: bold;
                                        }

                                    </style>
                                    
                                    <div class="en-right">
                                        <?php if ($success_msg != "") echo '<div class="success_c">' . $success_msg . '</div><div class="clear_both"></div>'; ?>
                                        <?php 
                                         $parent_analytics_user_id = get_user_meta($current_id, 'parent_analytics_user_id', true);
                                        if($parent_analytics_user_id > 0){
                                            $conv_acc_brand_name = brand_name($parent_analytics_user_id);
                                           echo '<div style="text-align:center;font-size:16px;font-weight:bold;margin-top:20px;">Conversions report will be showing from '.$conv_acc_brand_name.' account. So global conversions url does not need to setup. Thanks.</div>'; 
                                        } else {?>
                                        <div style="float:left;width:49%;">                                            
                                            <fieldset class="fieldset_class">
                                                <legend class="legend_class" style="text-align:center;font-weight:bold;">Landing Page URLs</legend>
                                                <div style="padding:20px;">
                                                    <form id="landing_urls_Frm" method="post" action="">

                                                        <div id="landing_url">
                                                            <?php
                                                            if (!empty($all_landing_url_for_conv)) {
                                                                foreach ($all_landing_url_for_conv as $l_url_index => $row_land_conv_url) {
                                                                    ?>
                                                                    <div id="l_url_index_<?php echo $l_url_index; ?>">
                                                                        <input id="l_url_value_<?php echo $l_url_index; ?>" value="<?php echo $row_land_conv_url['landing_url']; ?>" style="width:85%" type="text" name="landing_url[]">
                                                                        <a onclick="removed_l_url_index('<?php echo $l_url_index; ?>')" style="margin-left:20px;cursor: pointer;color:red;">X Remove</a>
                                                                        <div class="clear_both"></div> 
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } for ($more_land_trac = count($all_landing_url_for_conv); $more_land_trac < 5; $more_land_trac++) { ?>

                                                                <div><input style="width:85%;" type="text" name="landing_url[]" value=""></div>

                                                                <div class="clear_both"></div> 

                                                            <?php } ?>

                                                        </div>
                                                        <div style="clear: both;"></div> 
                                                        <a onclick="add_more_landing_url()" style="cursor:pointer;">Add more</a>

                                                        <div class="clear_both"></div>
                                                        <input style="background: #FB6800;color:white;font-weight: bold;" type="submit" value="Save Landing Page URLs">
                                                    </form>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div style="float:right;width:49%;">
                                            <fieldset class="fieldset_class">
                                                <legend class="legend_class" style="text-align:center;font-weight:bold;">Thank You Page URLs</legend>
                                                <div style="padding:20px;">


                                                    <form id="global_conversion_urls_Frm" method="post" action="" enctype="multipart/form-data">

                                                        <div id="global_ct_url">
                                               

                                                            <?php
                                                            foreach ($ConvTrackPrevURLsA as $g_url_index => $row_con_track_url) {
                                                                ?>

                                                                <div id="g_url_index_<?php echo $g_url_index; ?>">
                                                                    <input id="g_url_value_<?php echo $g_url_index; ?>" value="<?php echo $row_con_track_url['globalPageURL']; ?>" style="width:85%" type="text" name="globalPageURL[]">
                                                                    <?php if (trim($row_con_track_url['globalPageURL']) != '') { ?>
                                                                        <a onclick="removed_g_url_index('<?php echo $g_url_index; ?>')" style="margin-left:20px;cursor: pointer;color:red;">X Remove</a>
                                                                    <?php } ?>
                                                                    <div class="clear_both"></div> 
                                                                </div>



                                                                <?php
                                                            }
                                                            ?>

                                                            <?php for ($more_con_trac = count($ConvTrackPrevURLsA); $more_con_trac < 5; $more_con_trac++) { ?>

                                                                <div><input style="width:85%;" type="text" name="globalPageURL[]" value=""></div>

                                                                <div class="clear_both"></div> 

                                                            <?php } ?>

                                                        </div>  
                                                        <div style="clear: both;"></div> 
                                                        <a onclick="add_more_ct_url()" style="cursor:pointer;">Add more</a>

                                                        <div class="clear_both"></div> 

                                                        <div>

                                                            <input style="background: #FB6800;color:white;font-weight: bold;" type="submit" value="Save Thanku Page URLs">

                                                        </div>
                                                        

                                                    </form>    

                                                </div>
                                            </fieldset>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    
                                    
                                </div>
                                <div class="clearfix"></div>
                                
                                
                                <script>

                                    function add_more_ct_url() {

                                        var html = '<div><input style="width:85%;" type="text" name="globalPageURL[]" value=""></div><div class="clear_both"></div>';

                                        jQuery('#global_ct_url').append(html);

                                    }
                                    function add_more_landing_url() {

                                        var html = '<div><input style="width:85%;" type="text" name="landing_url[]" value=""></div><div class="clear_both"></div>';

                                        jQuery('#landing_url').append(html);

                                    }

                                    function removed_g_url_index(g_url_index) {
                                        //alert(g_url_index);
                                        jQuery('#g_url_index_' + g_url_index).hide();
                                        jQuery('#g_url_value_' + g_url_index).val(null);
                                    }
                                    function removed_l_url_index(l_url_index) {
                                        //alert(g_url_index);
                                        jQuery('#l_url_index_' + l_url_index).hide();
                                        jQuery('#l_url_value_' + l_url_index).val(null);
                                    }

                                </script>
                                
                                                                
                                <?php
                           }
                            else {
                                ?>
                                <div class="centerlocmsg">Invalid No Location</div>
                                <?php
                            }

                            ?>                           
                        <?php                        
                        
                    } else {
                        ?>
                        <div class="centerlocmsg">No Location Selected</div>
                        <?php
                    } ?>
                </div>
                    
            </form>


        </div>
    </div>


</div>
