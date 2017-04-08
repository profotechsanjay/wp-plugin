<?php
global $wpdb;
$location_id = isset($_GET['location_id'])?intval($_GET['location_id']):0;

$location = $wpdb->get_row
(
    $wpdb->prepare
    (
        "SELECT * FROM " . client_location()." WHERE id = %d",$location_id
    )
);
if(empty($location)){    
    ?>
    <div class="update-nag">Invalid Location</div>
    <?php 
    die;
}

include_once 'common.php';

global $current_user;
$UserID = $location->MCCUserId;
$CURENT_ID = $UserID;

$location_web = get_user_meta($UserID,'website',TRUE);
$location_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);

$base_url = site_url();

if(isset($_GET['location_id']) && !empty($_GET['location_id'])){
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
}
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

?>
<div class="panel panelmain">
    <div class="col-lg-12 ">
    <div class="contaninerinner">         
        <h4>Add Sites - <?php echo $location_name . " ( ".$location_web." )"; ?></h4> 
        <div class="bread_crumb">
            <ul>
                <li title="Locations">
                    <a href="<?php echo ST_LOC_PAGE; ?>?parm=locations">Locations</a> >>
                </li>
                <li title="Location Sites">
                    Location Sites
                </li>
            </ul>
        </div>
        <div style="margin-left:10px; float:left; width:100%; ">
            <?php
            if (isset($_POST["primarysite"]) && !empty($_POST["primarysite"])) {
                //update_user_meta($UserID, "Content_Primary_Site", $_POST);
                update_user_meta($UserID, "Content_Primary_Site", $_POST);                                
            }
            if (isset($_POST["competitor_url_save_btn"])) {
                //update_user_meta($UserID, "competitor_url", $_POST['competitor_url']); 
                
                $count_comp_url = count(array_filter($_POST['competitor_url']));
                $current_user_comp_key = $count_active_kw*$count_comp_url;
                $total_comp_key = $all_users_comp_kw + $current_user_comp_key;  // calculate total Comp Keywords of All locations + Current location, and Comp Keywords save only if $total_comp_key less than total limit
                
                if($comp_total_keyword_limit >= $total_comp_key){
                    update_user_meta($UserID, "competitor_url", $_POST['competitor_url']);
                    echo '<div style="text-align:center;font-weight:bold;font-size:17px;color:green;">Successfully Saved Competitor URL. </div><div style="clear:both;height:20px;"></div>'; //To see your report instantly please <a href="'.$URI.'" target="_blank">click here</a>  

                }else{
                    echo '<div class="keyword_alert">Competitor URL is not saved, Because your Limit for <strong>Comp keyword</strong> is over, So please contact to Administrator or Purchase <strong>Comp Keyword Add-Ons</strong> for increase Limit.</div>';
                }
                
            }
            
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['competitor_url']) && !isset($_POST["competitor_url_save_btn"])) {
                
                $count_comp_url = count(array_filter($_POST['competitor_url']));
                $current_user_comp_key = $count_active_kw*$count_comp_url;
                $total_comp_key = $all_users_comp_kw + $current_user_comp_key;  // calculate total Comp Keywords of All locations + Current location, and Comp Keywords save only if $total_comp_key less than total limit
                if(($comp_total_keyword_limit >= $total_comp_key) && ($keyword_opp_total_limit >= $limit_check)){
                    
                    $keys = array();
                    foreach($_POST as $key=>$data){
                        $keys[] = $key;
                    }
                    $url_name = $keys[0];
                    $button_name = $keys[1];

                    $key_opp_for_url = $_POST[$url_name][$button_name];
                    
                    update_user_meta($UserID, "competitor_url", $_POST['competitor_url']);
                    
                    if(!empty($key_opp_for_url)){
                                           
                        echo '<div style="text-align:center;font-weight:bold;font-size:17px;color:green;">Competitor data updated. </div><div style="clear:both;height:20px;"></div>'; //To see your report instantly please <a href="'.$URI.'" target="_blank">click here</a>  

                        $URI = site_url().'/cron/competitor-url-key-insert.php?user_id='.$UserID.'&keyword_opportunity_user='.$UserID.'&comp_url='.$key_opp_for_url;

                        $ch = curl_init($URI);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                        $result_on = curl_exec($ch);

                        header("refresh: 3;");
                    }else{
                        echo '<div class="keyword_alert add_ons">Please add valid Competitor URL.</div>';
                    }
                    
                    
                }elseif($comp_total_keyword_limit < $total_comp_key){
                    echo '<div class="keyword_alert">Competitor URL is not saved and Run, Because your Limit for <strong>Comp keyword</strong> is over, So please contact to Administrator or Purchase <strong>Comp Keyword Add-Ons</strong> for increase Limit.</div>';
                }elseif($keyword_opp_total_limit < $limit_check){
                    echo '<div class="keyword_alert">Competitor URL is not saved and Ru, Because your Limit for <strong>Keyword Opportunity</strong> is over, So please contact to Administrator or Purchase <strong>Keyword Opp Add-Ons</strong> for increase Limit.</div>';
                }
                
            }
            
            //$competitor_url = get_user_meta($UserID, "competitor_url");
            $competitor_url = get_user_meta($UserID, "competitor_url", true);
                        
            //$Content_Primary_Site = get_user_meta($UserID, "Content_Primary_Site");
            $Content_Primary_Site = get_user_meta($UserID, "Content_Primary_Site", true);
            
            if (!empty($Content_Primary_Site)) {

                $primarysiteurl = $Content_Primary_Site['primarysiteurl'];

                $login_url = $Content_Primary_Site['login_url'];

                $login_user = $Content_Primary_Site['login_user'];

                $login_password = $Content_Primary_Site['login_password'];

                $sitetypePrimary = $Content_Primary_Site['sitetypePrimary'];

                $postdrlv = $Content_Primary_Site['postdrlv'];

                $buildlink_primary = $Content_Primary_Site['buildlink_primary'];
            }
            ?>

                    <div style="float:left; width:45%; ">

                        <form name="form1" method="post" action="">

                            <h1 style="margin: 8px 0 8px 0px; font-size: 20px;">Primary Site</h1>

                            <table class="primarysite">

                                <tr>

                                    <td style="float:left; width:140px;"><b>Site Url:</b></td>

                                    <td style="float:left; width:200px;">

                                        <input type="text" name="primarysiteurl" value="<?php echo $primarysiteurl ?>" style="margin-bottom:4px">

                                    </td>

                                </tr>

                                <tr>

                                    <td style="float:left; width:140px;"><b>Login url:</b></td>

                                    <td style="float:left; width:200px;"><input type="text" name="login_url" value="<?php echo $login_url ?>"></td>

                                </tr>

                                <tr>

                                    <td style="float:left; width:140px;"><b>Login User:</b></td>

                                    <td style="float:left; width:200px;"><input type="text" name="login_user" value="<?php echo $login_user ?>"></td>

                                </tr>

                                <tr>

                                    <td style="float:left; width:140px;"><b>Login Password:</b></td>

                                    <td style="float:left; width:200px;"><input type="text" name="login_password" value="<?php echo $login_password ?>"></td>

                                </tr>

            <?php $Sitetype = array('wordpress_blog' => 'Wordpress Blog', 'wordpress.com' => 'Wordpress.com', 'tumbler.com' => 'Tumbler.com', 'blogspot.com' => 'Blogspot.com'); ?>

                                <tr>

                                    <td style="float:left; width:140px;" ><b>Site Type:</b></td>

                                    <td style="float:left; width:200px;"><select name="sitetypePrimary"><?php
            foreach ($Sitetype as $ksite => $hsite) {

                if ($sitetypePrimary == $ksite) {

                    $S = "selected='selected'";
                } else {

                    $S = '';
                }
                ?> 

                                                <option <?php echo $S; ?>value="<?php echo $ksite ?>"><?php echo $hsite ?></option> 

            <?php } ?></select></td>

                                </tr>

            <?php $PostLivedraft = array('Post Live' => 'post_live', 'Post Draft' => 'post_draft', 'Review' => 'review'); ?>

                                <tr>

                                    <td style="float:left; width:140px;"><b>Post User:</b></td>

                                    <td style="float:left; width:200px;"><select name="postdrlv"><?php
            foreach ($PostLivedraft as $k => $h) {

                if ($postdrlv == $h) {

                    $S1 = "selected='selected'";
                } else {

                    $S1 = '';
                }
                ?> 

                                                <option <?php echo $S1; ?>value="<?php echo $h ?>"><?php echo $k ?></option> 

            <?php } ?></select></td>

                                </tr>

            <?php $bulid_link = array('build_link_yes' => 'Yes', 'build_link_no' => 'No'); ?>

                                <tr>

                                    <td style="float:left; width:140px;" ><b>Bulid Link to Posts:</b></td>

                                    <td style="float:left; width:200px;"><select style="width:130px;" name="buildlink_primary">

            <?php
            foreach ($bulid_link as $kbuild => $hbuild) {

                if ($buildlink_primary == $kbuild) {

                    $S12 = "selected='selected'";
                } else {

                    $S12 = '';
                }
                ?> 

                                                <option <?php echo $S12; ?> value="<?php echo $kbuild ?>"><?php echo $hbuild ?></option> 

            <?php } ?></select></td>

                                </tr>

                                <tr>

                                    <td style="float:left; width:140px;"></td>

                                    <td style="float:left; width:200px;"> <input type="submit" name="primarysite" value="Save"></td>

                                </tr>

                            </table>



                        </form>	

                    </div>
                    <!--
                    <div style="float:right; width:52%;">
                        <h1 style="margin: 8px 0 8px 0px;font-size: 20px;">Competitor URL</h1>
                        <form name="competitor_url_save_Frm" class="competitor_url_save_Frm" method="post" action="">
                            <table>
            <?php for ($com_url = 0; $com_url <= 3; $com_url++) { ?>
                                <?php
                                $get_opp_url = 0;
                                if(!empty($competitor_url[$com_url])){                                               
                                    $get_opp_url = $wpdb->get_var("SELECT COUNT(*) FROM `keyword_opportunity` WHERE `user_id` = '".$UserID."' && `competitor_info` like '%".$competitor_url[$com_url]."%'");
                                }
                                ?>
                                    <tr>

                                        <td style="float:left; width:25%;"><b>Competitor URL <?php echo $com_url + 1; ?>:</b></td>

                                        <td style="float:left; width:45%;">
                                            <input style="width:95%;" type="text" name="competitor_url[]" value="<?php if (!empty($competitor_url)) echo $competitor_url[$com_url]; ?>" style="margin-bottom:4px">
                                        </td>
                                        <td style="float:left; width:10%;">                                           
                                            <h1><strong><?php echo $get_opp_url;?></strong></h1>                                           
                                        </td>
                                        <td style="float:left; width:20%;">
                                            <input type="submit" name="<?php echo $com_url;?>" value="Run/Save">
                                        </td>

                                    </tr>

            <?php } ?>
                                <tr>

                                    <td style="float:left; width:30%;"></td>

                                    <td style="float:left; width:60%;"> <input type="submit" name="competitor_url_save_btn" value="Save"></td>

                                </tr>
                            </table>  
                        </form>

                    </div>
                    -->
                    <div style="clear: both;"></div>



            <?php
            if (isset($_POST["buffersite"]) && !empty($_POST["buffersite"])) {

                update_user_meta($UserID, "Content_Buffer_Site", $_POST);
            }

            $buffersiteData = get_user_meta($UserID, "Content_Buffer_Site", true);

            if (empty($buffersiteData)) {
                ?>
                        <input type="hidden" class="count_buffer" value="6">

                        <form name="buffer_step" method="post" action=""> 
                            <div class="buffer_site" style="margin-left:3px; float:left; width:50%;">

                <?php for ($i = 1; $i <= 5; $i++) { ?>

                                    <div style="float:left; border: 1px solid #efefef;  margin: 5px;  padding: 10px; width: 520px;">



                                        <table class="primarysite">

                                            <tr colspan="2">

                                                <td style="float:left; width:160px; font-size:20px; padding-bottom:20px;" ><b>Buffer Site #<?php echo $i; ?></b></td> 



                                            </tr>

                                            <tr>

                                                <td style="float:left; width:140px;"><b>Site Url:</b></td>

                                                <td style="float:left; width:200px;">

                                                    <input type="text" name="buffersiteurl[]" style="margin-bottom:4px;width:311px">

                                                </td>

                                            </tr>

                                            <tr>

                                                <td style="float:left; width:140px;"><b>Login url:</b></td>

                                                <td style="float:left; width:200px;"><input type="text" style="width:311px" name="buffer_login_url[]"></td>

                                            </tr>

                                            <tr>

                                                <td style="float:left; width:140px;"><b>Login User:</b></td>

                                                <td style="float:left; width:200px;"><input type="text" name="buffer_login_user[]"></td>

                                            </tr>

                                            <tr>

                                                <td style="float:left; width:140px;"><b>Login Password:</b></td>

                                                <td style="float:left; width:200px;"><input type="password" name="buffer_login_password[]"></td>

                                            </tr>

                    <?php $Sitetype = array('wordpress_blog' => 'Wordpress Blog', 'wordpress.com' => 'Wordpress.com', 'tumbler.com' => 'Tumbler.com', 'blogspot.com' => 'Blogspot.com', 'hubspot_blog' => 'Hubspot Blog'); ?>

                                            <tr>

                                                <td style="float:left; width:140px;" ><b>Site Type:</b></td>

                                                <td style="float:left; width:200px;"><select name="sitetype[]"><?php foreach ($Sitetype as $ksite => $hsite) { ?> 

                                                            <option value="<?php echo $ksite ?>"><?php echo $hsite ?></option> 

                    <?php } ?></select></td>

                                            </tr>



                    <?php $PostLivedraft = array('Post Live' => 'post_live', 'Post Draft' => 'post_draft', 'Review' => 'review'); ?>

                                            <tr>

                                                <td style="float:left; width:140px;" ><b>Post User:</b></td>

                                                <td style="float:left; width:200px;"><select style="width:130px;" name="post_user[]"><?php foreach ($PostLivedraft as $k => $h) { ?> 

                                                            <option value="<?php echo $h ?>"><?php echo $k ?></option> 

                    <?php } ?></select></td>

                                            </tr>

                    <?php $bulid_link = array('build_link_yes' => 'Yes', 'build_link_no' => 'No'); ?>

                                            <tr>

                                                <td style="float:left; width:140px;" ><b>Bulid Link to Posts:</b></td>

                                                <td style="float:left; width:200px;"><select style="width:130px;" name="buildlink[]">

                    <?php foreach ($bulid_link as $kbuild => $hbuild) { ?> 

                                                            <option value="<?php echo $kbuild ?>"><?php echo $hbuild ?></option> 

                    <?php } ?></select></td>

                                            </tr>



                                        </table>





                                    </div>

                <?php } ?>

                            </div>

                            <div style="float:left; width:700px; margin-left:130px;">

                                <input type="submit" name="buffersite" value="Save">

                                <input type="button" value="Add More" class="Add_more_buffer">

                            </div>

                        </form>

                <?php
            } else {

                if (!empty($buffersiteData)) {

                    $countSite = count($buffersiteData['buffersiteurl']);

                    $buffersiteData1 = $buffersiteData['buffersiteurl'];

                    $buffer_login_url = $buffersiteData['buffer_login_url'];

                    $buffer_login_user = $buffersiteData['buffer_login_user'];

                    $buffer_login_password = $buffersiteData['buffer_login_password'];

                    $sitetype = $buffersiteData['sitetype'];

                    $post_user = $buffersiteData['post_user'];

                    $buildlink = $buffersiteData['buildlink'];
                    ?>

                            <input type="hidden" class="count_buffer" value="<?php echo $countSite + 1; ?>">

                            <form name="buffer_step" method="post" action=""> 

                                <div class="buffer_site" style="float:left; width:700px;">

                    <?php
                    foreach ($buffersiteData1 as $Bufferdat => $val) {

                        $i = $Bufferdat + 1;
                        ?>



                                        <div style="float:left; border: 1px solid #efefef;  margin: 5px;  padding: 10px; width: 520px;">



                                            <table class="primarysite">

                                                <tr colspan="2">

                                                    <td style="float:left; width:160px; font-size:20px; padding-bottom:20px;" ><b>Buffer Site #<?php echo $i; ?></b></td>



                                                </tr>

                                                <tr>

                                                    <td style="float:left; width:140px;"><b>Site Url:</b></td>

                                                    <td style="float:left; width:200px;">

                                                        <input type="text" name="buffersiteurl[]" value="<?php echo $buffersiteData1[$Bufferdat] ?>" style="margin-bottom:4px;width:311px">

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="float:left; width:140px;"><b>Login url:</b></td>

                                                    <td style="float:left; width:200px;"><input type="text" style="width:311px" name="buffer_login_url[]" value="<?php echo $buffer_login_url[$Bufferdat] ?>" ></td> 

                                                </tr>

                                                <tr>

                                                    <td style="float:left; width:140px;"><b>Login User:</b></td>

                                                    <td style="float:left; width:200px;"><input type="text" name="buffer_login_user[]"value="<?php echo $buffer_login_user[$Bufferdat] ?>" ></td>

                                                </tr>

                                                <tr>

                                                    <td style="float:left; width:140px;"><b>Login Password:</b></td>

                                                    <td style="float:left; width:200px;"><input type="text" name="buffer_login_password[]" value="<?php echo $buffer_login_password[$Bufferdat] ?>"></td>

                                                </tr>

                        <?php $Sitetype = array('wordpress_blog' => 'Wordpress Blog', 'wordpress.com' => 'Wordpress.com', 'tumbler.com' => 'Tumbler.com', 'blogspot.com' => 'Blogspot.com', 'hubspot_blog' => 'Hubspot Blog'); ?>



                                                <tr>

                                                    <td style="float:left; width:140px;" ><b>Site Type:</b></td>

                                                    <td style="float:left; width:200px;"><select name="sitetype[]"><?php
                        foreach ($Sitetype as $ksite => $hsite) {



                            if ($sitetype[$Bufferdat] == $ksite) {

                                $Selec = 'selected="selected"';
                            } else {

                                $Selec = '';
                            }
                            ?> 

                                                                <option <?php echo $Selec; ?> value="<?php echo $ksite ?>"><?php echo $hsite ?></option> 

                        <?php } ?></select></td>

                                                </tr>



                        <?php $PostLivedraft = array('Post Live' => 'post_live', 'Post Draft' => 'post_draft', 'Review' => 'review'); ?>

                                                <tr>

                                                    <td style="float:left; width:140px;" ><b>Post User:</b></td>

                                                    <td style="float:left; width:200px;"><select style="width:130px;" name="post_user[]"><?php
                        foreach ($PostLivedraft as $k => $h) {

                            if ($post_user[$Bufferdat] == $h) {

                                $Selec1 = 'selected="selected"';
                            } else {

                                $Selec1 = '';
                            }
                            ?> 

                                                                <option <?php echo $Selec1; ?> value="<?php echo $h ?>"><?php echo $k ?></option> 

                        <?php } ?></select></td>

                                                </tr>

                        <?php $bulid_link = array('build_link_yes' => 'Yes', 'build_link_no' => 'No'); ?>

                                                <tr>

                                                    <td style="float:left; width:140px;" ><b>Bulid Link to Posts:</b></td>

                                                    <td style="float:left; width:200px;"><select style="width:130px;" name="buildlink[]">

                        <?php
                        foreach ($bulid_link as $kbuild => $hbuild) {

                            if ($buildlink[$Bufferdat] == $kbuild) {

                                $Selec122 = 'selected="selected"';
                            } else {

                                $Selec122 = '';
                            }
                            ?> 

                                                                <option <?php echo $Selec122; ?>value="<?php echo $kbuild ?>"><?php echo $hbuild ?></option> 

                        <?php } ?></select></td>

                                                </tr>



                                            </table>





                                        </div>











                        <?php
                        $i++;
                    }
                    ?>



                                </div>

                                <div style="float:left; width:700px; margin-left:130px;">

                                    <input type="submit" name="buffersite" value="Save">

                    <?php if ($countSite < 20) { ?>

                                        <input type="button" value="Add More" class="Add_more_buffer">

                    <?php } ?>

                                </div>

                            </form>



                    <?php
                }
            }
            ?>
                    <script type="text/javascript">
                        jQuery('.Add_more_buffer').on("click", function () {
                            var count_buffer = jQuery('.count_buffer').val();
                            var buffercount = count_buffer;
                            var morefive = parseInt(buffercount) + parseInt(5);
                            jQuery('.count_buffer').val(morefive);
                            if (morefive > 21 || morefive == 21) {
                                jQuery('.Add_more_buffer').hide();
                            }
                            if (morefive <= 25) {
                                for (var k = 6; k <= 10; k++) {
                                    jQuery('.buffer_site').append('<div style="float:left; border: 1px solid #efefef;  margin: 5px;  padding: 10px; width: 520px;"><table class="primarysite"><tr colspan="2"><td style="float:left; font-size:20px; padding-bottom:20px; width:160px;" ><b>Buffer Site #' + buffercount + '</b></td></tr><tr><td style="float:left; width:140px;"><b>Sites Url:</b></td><td style="float:left; width:200px;"><input type="text" name="buffersiteurl[]" style="margin-bottom:4px"></td></tr><tr><td style="float:left; width:140px;"><b>Login url:</b></td><td style="float:left; width:200px;"><input type="text" name="buffer_login_url[]"></td></tr><tr><td style="float:left; width:140px;"><b>Login User:</b></td><td style="float:left; width:200px;"><input type="text" name="buffer_login_user[]"></td></tr><tr><td style="float:left; width:140px;"><b>Login Password:</b></td><td style="float:left; width:200px;"><input type="password" name="buffer_login_password[]"></td></tr><?php $Sitetype = array('wordpress_blog' => 'Wordpress Blog', 'wordpress.com' => 'Wordpress.com', 'tumbler.com' => 'Tumbler.com', 'blogspot.com' => 'Blogspot.com'); ?><tr><td style="float:left; width:140px;" ><b>Site Type:</b></td><td style="float:left; width:200px;"><select name="sitetype[]"><?php foreach ($Sitetype as $ksite => $hsite) { ?><option value="<?php echo $ksite ?>"><?php echo $hsite ?></option><?php } ?></select></td></tr><?php $PostLivedraft = array('Post Live' => 'post_live', 'Post Draft' => 'post_draft'); ?><tr><td style="float:left; width:140px;" ><b>Post User:</b></td>	<td style="float:left; width:200px;"><select style="width:130px;" name="post_user[]"><?php foreach ($PostLivedraft as $k => $h) { ?><option value="<?php echo $h ?>"><?php echo $k ?></option><?php } ?><select></td></tr><?php $bulid_link = array('build_link_yes' => 'Yes', 'build_link_no' => 'No'); ?><tr><td style="float:left; width:140px;" ><b>Bulid Link to Posts:</b></td><td style="float:left; width:200px;"><select style="width:130px;" name="buildlink[]"><?php foreach ($bulid_link as $kbuild => $hbuild) { ?><option value="<?php echo $kbuild ?>"><?php echo $hbuild ?></option><?php } ?></select></td></tr></table></div>');
                                    buffercount++;
                                }
                            }
                        });</script>
                </div>


    </div>
    </div>
</div>