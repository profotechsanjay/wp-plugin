<?php

include_once 'common.php';
include_once ABSPATH. '/wp-content/themes/twentytwelve/analytics/CommonUtils.php';
global $wpdb;

$billing_enable = '0';
$billing_enable = BILLING_ENABLE;

$base_url = site_url();
$locations = $wpdb->get_results
(
    $wpdb->prepare
    (
            "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC",""
    )
);



?>
<style>
    .btnparent{
        background-color: #337ab7 !important; border-color: #2e6da4 !important; color: #fff !important;
        background-image: none !important;
    } 
    .btnparent:hover{color: #fff !important;}
    .gatrack select{
        height: 33px;
    }
</style>
<div class="contaninerinner trackdiv">     
    <h4>Competitor Url</h4>
    <div class="panel panel-primary">
        <div class="panel-heading">Competitor Url </div>
        <div class="panel-body">
         

                <div class="form-group">
                    <label class="col-md-3 control-label">Select Location (Account)</label>
                    <div class="col-md-6">
                        <select required class="form-control chosen" name="compurlconnect" id="compurlconnect">
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
                
                <div class="gatrack">
                    <?php if(isset($_REQUEST['location_id']) && intval($_REQUEST['location_id']) > 0){
                        $location_id = isset($_GET['location_id'])?intval($_GET['location_id']):0;
                
                        $location = $wpdb->get_row
                        (
                            $wpdb->prepare
                            (
                                "SELECT * FROM " . client_location()." WHERE id = %d",$location_id
                            )
                        );

                        $UserID = $location->MCCUserId;
                        $base_url = site_url();
                        
                        $get_all_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `MCCUserId` != '".$UserID."'");  // Neglect only current location keywords
                        $all_users_comp_kw = 0;
                        foreach($get_all_locations as $get_all_location){
                            $user_id = $get_all_location->MCCUserId;

                            /********** Count Comp Keywords (START) ***********/
                            $comp_keywords = billing_info($user_id);
                            $count_comp_kw = $comp_keywords['count_comp_keywords'];
                            $all_users_comp_kw = $all_users_comp_kw + $count_comp_kw;
                            /********** Count Comp Keywords (END) ***********/
                        }

                        $active_keywords = rd_all_active_keywords($UserID);
                        $count_active_kw = count($active_keywords);
                        
                        $location_package_limit = location_package_limit();
                        $package_ckw_limit = $location_package_limit['comp_keywords_limit'];
                        $package_ckw_addons = $location_package_limit['comp_keywords_addons'];
                        $package_kwo_limit = $location_package_limit['keyword_opp_limit'];
                        $package_kwo_addons = $location_package_limit['keyword_opp_addons'];
                        $package_kwo_used = $location_package_limit['keyword_opp_used'];

                        $comp_keywords_per_addons = $locations_package_prices->lp_ckey_range;  // 200 Comp keywords allow per addons
                        $keywords_opp_per_addons = $locations_package_prices->lp_keyo_range;  // 200 Comp keywords allow per addons

                        $limit_check = $package_kwo_used + $keywords_opp_per_addons;  // Limit check before submit approximately how many total keywords opportunity get if submit

                        $comp_total_keyword_limit = $package_ckw_limit + ($package_ckw_addons*$comp_keywords_per_addons);
                        $keyword_opp_total_limit = $package_kwo_limit + ($package_kwo_addons*$keywords_opp_per_addons);
                        
                        $check_lp_all_limits = check_lp_all_limits();
                        $max_keyword_get_limit = $check_lp_all_limits['keyword_opp_available'];
            
                        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['competitor_url'])) {
                            //pr($_POST['competitor_url']);
                            $count_comp_url = count(array_filter($_POST['competitor_url']));
                            $current_user_comp_key = $count_active_kw*$count_comp_url;
                            $total_comp_key = $all_users_comp_kw + $current_user_comp_key;  // calculate total Comp Keywords of All locations + Current location, and Comp Keywords save only if $total_comp_key less than total limit
                            if((($comp_total_keyword_limit >= $total_comp_key) && ($keyword_opp_total_limit >= $limit_check) && ($billing_enable == '1')) || ($billing_enable == '0')){
                               
                                
                                if(!isset($_POST['remove_0']) && !isset($_POST['remove_1']) && !isset($_POST['remove_2']) && !isset($_POST['remove_3'])){
                                    $keys = array();
                                    foreach($_POST as $key=>$data){
                                        $keys[] = $key;
                                    }
                                    //pr($keys);
                                    $url_name = $keys[0];
                                    
                                    $keyword_limit = $keys[1];
                                    
                                    $button_name = $keys[2];
                                    
                                    
                                    $key_opp_for_url = $_POST[$url_name][$button_name];
                                    
                                    $key_limit_for_url = $_POST[$keyword_limit][$button_name];
                                    
                                    if(empty($key_limit_for_url)){
                                        $key_limit_for_url = 250;
                                    }
                                    
                                    /*********************/
                                    $competitor_url = get_user_meta($UserID, "competitor_url", true);
                                    
                                    if(empty($competitor_url)){
                                                                
                                        $competitor_url = array("0" => "","1" => "","2" => "","3" => "");
                                    }
                                    
                                    //pr($competitor_url); die;
                                    $new_competitor_url = array();
                                    
                                    $n = 0;
                                    foreach($competitor_url as $competitor_urls){
                                        if($n == $button_name){
                                            $new_competitor_url[] = $key_opp_for_url;
                                        }else{
                                            $new_competitor_url[] = $competitor_urls;
                                        }
                                        $n = $n + 1;
                                    }
                                    
                                    
                                    if((($billing_enable == '1') && ($max_keyword_get_limit >= $key_limit_for_url)) || ($billing_enable == '0')){
                                        //pr($new_competitor_url);
                                        update_user_meta($UserID, "competitor_url", $new_competitor_url);
                                        /********************/

                                        //update_user_meta($UserID, "competitor_url", $_POST['competitor_url']);

                                        if(!empty($key_opp_for_url) && !empty($key_limit_for_url) && $key_limit_for_url > 0){

                                            echo '<div style="text-align:center;font-weight:bold;font-size:17px;color:green;">Competitor data updated. </div><div style="clear:both;height:20px;"></div>'; //To see your report instantly please <a href="'.$URI.'" target="_blank">click here</a>  

                                            $URI = site_url().'/cron/competitor-url-key-insert.php?user_id='.$UserID.'&keyword_opportunity_user='.$UserID.'&comp_url='.$key_opp_for_url.'&keywordlimit='.$key_limit_for_url;
                                            $ch = curl_init($URI);
                                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                            $result_on = curl_exec($ch);

                                            $checklimit = "comp_keywords";
                                            $agency_package_notification = agency_package_notification($checklimit);  //function use to send Location package Notification

                                            $checklimit = "keyword_opp";
                                            $agency_package_notification = agency_package_notification($checklimit);  //function use to send Location package Notification

                                            header("refresh: 3;");
                                        }else{
                                            echo '<div class="keyword_alert add_ons">Please add valid Competitor URL Or Keyword Limit..</div>';
                                        }
                                    }else{
                                        echo '<div class="keyword_alert add_ons">Sorry Report not generate, because your Keyword Limit to get keywords from competitor URL is less than inserted number of keyword value. Purchase Keyword Opportunity Add-Ons for increase Limit.</div>';
                                    }
                                    
                                }else{
                                    $remove_opp_for_url = array();
                                    if(isset($_POST['remove_0'])){
                                        $removed_key = '0';                                       
                                    }elseif(isset($_POST['remove_1'])){
                                        $removed_key = '1';                                       
                                    }elseif(isset($_POST['remove_2'])){
                                        $removed_key = '2';                                       
                                    }elseif(isset($_POST['remove_3'])){
                                        $removed_key = '3';                                       
                                    }
                                    $remove_opp_for_url[] = $_POST['competitor_url'][$removed_key];
                                    //pr($remove_opp_for_url);
                                    $competitor_url = get_user_meta($UserID, "competitor_url", true);
                                    //pr($competitor_url);
                                    $new_competitor_url = array();
                                    
                                    $n = 0;
                                    foreach($competitor_url as $competitor_urls){
                                        if($n == $removed_key){
                                            $new_competitor_url[] = '';
                                        }else{
                                            $new_competitor_url[] = $competitor_urls;
                                        }
                                        $n = $n + 1;
                                    }
                                    //pr($new_competitor_url);
                                    update_user_meta($UserID, "competitor_url", $new_competitor_url);
                                    echo '<div class="keyword_alert">Competitor URL is Successfully removed.</div>';
                                }

                            }elseif($comp_total_keyword_limit < $total_comp_key){
                                echo '<div class="keyword_alert">Competitor URL is not saved and Run, Because your Limit for <strong>Comp keyword</strong> is over, So please contact to Administrator or Purchase <strong>Comp Keyword Add-Ons</strong> for increase Limit.</div>';
                            }elseif($keyword_opp_total_limit < $limit_check){
                                echo '<div class="keyword_alert">Competitor URL is not saved and Ru, Because your Limit for <strong>Keyword Opportunity</strong> is over, So please contact to Administrator or Purchase <strong>Keyword Opp Add-Ons</strong> for increase Limit.</div>';
                            }

                        }
                        
                        $competitor_url = get_user_meta($UserID, "competitor_url", true);
                        
                        ?>
                        <div style="float:right; width:100%;">
                            <?php
                            if($billing_enable == '1'){ ?>
                                <div style="margin: 35px 0 28px;">Maximum Limit to get Keywords from Competitor URL = <strong><?php echo $max_keyword_get_limit;?></strong></div>
                            <?php }
                            ?>
                            
                            <form name="competitor_url_save_Frm" class="competitor_url_save_Frm" method="post" action="">
                                <?php $total_get_keyword_opp = 0;?>
                                <table>
                                    <tr>
                                        <td style="float:left; width:20%;"></td>

                                        <td style="float:left; width:38%;">

                                        </td>
                                        <td style="float:left; width:12%;">
                                            
                                            <h1><strong>Total Keywords</strong></h1>
                                        </td>
                                        <td style="float:left; width:10%;text-align: center;">                                           
                                            <h1><strong>Keyword Limit</strong></h1>
                                        </td>
                                        <td style="float:left; width:20%;">
                                        </td>

                                    </tr>
                                <?php for ($com_url = 0; $com_url <= 3; $com_url++) { ?>
                                    <?php
                                    $get_opp_url = 0;
                                    $last_run_date = "Not Run";
                                    if(!empty($competitor_url[$com_url])){ 
                                        $last_run_date = $wpdb->get_var("SELECT `update_date` FROM `keyword_opportunity` WHERE `user_id` = '".$UserID."' && `competitor_info` like '%".$competitor_url[$com_url]."%' ORDER BY `update_date` DESC LIMIT 1");
                                        if($last_run_date == ''){
                                            $last_run_date = "Not Run";
                                        }
                                        $get_opp_url = $wpdb->get_var("SELECT COUNT(*) FROM `keyword_opportunity` WHERE `user_id` = '".$UserID."' && `competitor_info` like '%".$competitor_url[$com_url]."%'");
                                    }
                                    ?>
                                        <tr>

                                            <td style="float:left; width:20%;"><b>Competitor URL <?php echo $com_url + 1; ?>:</b><br>Last Run : <?php echo $last_run_date;?></td>

                                            <td style="float:left; width:38%;">
                                                <input style="width:100%;" type="text" name="competitor_url[]" value="<?php if (!empty($competitor_url)) echo $competitor_url[$com_url]; ?>" style="margin-bottom:4px">
                                            </td>
                                            <td style="float:left; width:12%;text-align:center;">
                                                <h1><strong><?php echo $get_opp_url;?></strong></h1>                                           
                                                
                                            </td>
                                            <td style="float:left; width:10%;text-align: center;">                                           
                                                <input style="width:100%;" type="text" name="keywordlimit[]" value="250" style="margin-bottom:4px">
                                            </td>
                                            <td style="float:left; width:20%;">
                                                <input type="submit" style="padding: 8px 12px;" name="<?php echo $com_url;?>" value="Run/Update">
                                                <input type="submit" style="padding: 8px 12px;" name="remove_<?php echo $com_url;?>" value="Remove" style="margin-left:13px">
                                            </td>

                                        </tr>

                                    <?php 
                                    $total_get_keyword_opp = $total_get_keyword_opp + $get_opp_url;
                                    } ?>
                                        <tr>
                                            <td style="float:left; width:20%;"></td>

                                            <td style="float:left; width:38%;">
                                                
                                            </td>
                                            <td style="float:left; width:12%;text-align:center;">
                                                <h1><strong><?php echo $total_get_keyword_opp;?></strong></h1>
                                            </td>
                                            <td style="float:left; width:10%;text-align: center;">                                           
                                                
                                            </td>
                                            <td style="float:left; width:20%;">
                                            </td>

                                        </tr>   
                                </table>  
                            </form>

                        </div>
                        <div style="clear: both;"></div>
                    <?php } else {
                        ?>
                        <div class="centerlocmsg">No Location Selected</div>
                        <?php
                    }
                    ?>
                </div>
                
                
             

        </div>
    </div>


</div>
<script>
    jQuery(document).ready(function() {
        jQuery(window).keydown(function(event){
          if(event.keyCode == 13){
            event.preventDefault();
            return false;
          }
        });
    });
</script>