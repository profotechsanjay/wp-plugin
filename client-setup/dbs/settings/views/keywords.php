<?php
global $wpdb;

$user_id = isset($_GET['user_id'])?intval($_GET['user_id']):0;
if($user_id > 0){
    $_SESSION["Current_user_live"] = $user_id;
    wp_redirect(site_url()."/keyword-campaigns");
    exit;
}

$location_id = isset($_GET['location_id'])?intval($_GET['location_id']):0;

global $billing_enable;

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
$current_id = $UserID;
$location_web = get_user_meta($UserID,'website',TRUE);
$location_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);
$success_msg = '';

$get_all_locations = $wpdb->get_results("SELECT * FROM `wp_client_location` WHERE `id` != '".$location_id."'");  // Neglect only current location keywords
$all_users_kw = $all_users_comp_kw = 0;
foreach($get_all_locations as $get_all_location){
    
    $user_id = $get_all_location->MCCUserId;
    /********** Count Keywords (START) ***********/
    $active_keywords = rd_all_active_keywords($user_id);
    $count_active_kw = count($active_keywords);
    $all_users_kw = $all_users_kw + $count_active_kw;
    /********** Count Keywords (END) ***********/
    
    /********** Count Comp Keywords (START) ***********/
    $comp_keywords = billing_info($user_id);
    $count_comp_kw = $comp_keywords['count_comp_keywords'];
    $all_users_comp_kw = $all_users_comp_kw + $count_comp_kw;
    /********** Count Comp Keywords (END) ***********/
}
/*
$var = rd_all_active_keywords('1567');
print_r(count($var));
*/

$location_package_limit = location_package_limit();

$package_kw_limit = $location_package_limit['keywords_limit'];
$package_kw_addons = $location_package_limit['keywords_addons'];
$package_ckw_limit = $location_package_limit['comp_keywords_limit'];
$package_ckw_addons = $location_package_limit['comp_keywords_addons'];

$keywords_per_addons = $locations_package_prices->lp_key_range;  // 100 keywords allow per addons
$comp_keywords_per_addons = $locations_package_prices->lp_ckey_range;  // 200 Comp keywords allow per addons

$total_keyword_limit = $package_kw_limit + ($package_kw_addons*$keywords_per_addons);
$comp_total_keyword_limit = $package_ckw_limit + ($package_ckw_addons*$comp_keywords_per_addons);
$errormsg = '';

$ReportID = get_user_meta($UserID, "btl_campaign", true);

if(isset($_POST['reportrun']) && (intval($_POST['reportrun']) >= 0)){
    //$ReportID = intval($_POST['reportrun']); // if 0 mean run first time, otherwise re-run   
    //pr($ReportID); die;
    if ($ReportID > 0) {        
        
        include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
        $all_active_keywords = all_active_keywords($UserID, 1);
        $all_active_keywords = array_unique($all_active_keywords);
        if (count($all_active_keywords) > 100) {
            //mail('shyamku07@gmail.com', 'More than 100 Keywords', $UserID, mail_header());
        }
        $search_terms = "<pre>" . implode("\n", $all_active_keywords) . "</pre>";
        $PostFields['campaign-id'] = $ReportID;

        $PostFields['search-terms'] = $search_terms;
        if ($_SERVER['HTTP_HOST'] != 'localhost' && ($_SERVER['HTTP_HOST'] != '127.0.0.1')) {

            //=======================================
            update_user_meta($UserID, 'rank_check_options', $_POST['rank_checkoptions']);

            $activatation_status = $Content_keyword_Site2['activation'];
            $active_key_landing_page = array();
            foreach ($activatation_status as $curr_key_index => $row_status) {
                if ($row_status != 'inactive') {
                    if ($Content_keyword_Site2['landing_page'][$curr_key_index][0] != "") {
                        $active_key_landing_page[] = $Content_keyword_Site2['landing_page'][$curr_key_index][0];
                    }
                }
            }
            $active_key_landing_page = array_unique($active_key_landing_page);
            //pr($active_key_landing_page);
            //=======================================

            $website = get_user_meta($UserID, 'website', true);
            //$ranking_urls = get_user_meta($UserID, 'ranking_urls', true);
            if ($_POST['rank_checkoptions'] == 'check_only_landing_pages') {               
                $all_website = implode('","', $active_key_landing_page);
                $all_website = '["' . $all_website . '"]';
            } else {
                $all_website = '["' . $website . '"]';
            }
            $PostFields['website-addresses'] = $all_website;
            $adwords_pull = get_user_meta($UserID, 'adwords-pull', true);
            $PostFields['search-engines'] = 'google,google-mobile,yahoo,bing';
            if ($adwords_pull == "local") {
                $PostFields['search-engines'] = 'google,google-mobile,google-local,yahoo,yahoo-local,bing,bing-local';
            }
            
            $update_campaign = GetBTLInfoUsingCURL('lsrc/update', $PostFields);
            if(isset($update_campaign['errors']) && $update_campaign['errors'] != ''){
                $success_msg = '';
                $errormsg = 'Some error occurred. Please try after sometime.';
            }
            else{
                $report_run_PostFields['campaign-id'] = $ReportID;
                $update_campaign = GetBTLInfoUsingCURL('lsrc/run', $report_run_PostFields);            
                $success_msg = 'Keywords campaign rerun sucessfully.';
            }
        }        
    }
    else{
        
        $URI = site_url().'/cron/keyword-match-with-seo-table.php?user_id='.$UserID;
        $ch = curl_init($URI);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result_on = curl_exec($ch);        
        $success_msg = 'Keywords campaign sucessfully created.';
    }
    
}

if (isset($_POST["submit_keywrds"])) {

    //$duplicates = is_duplicate_array($_POST['landing_page'], $_POST['activation']);
    $duplicates = '';
    if(!empty($duplicates)){        
        $errormsg .= 'Duplicate Landing Pages Found. Please add similar keywords in one group of landing page. <br/>';
        foreach($duplicates as $duplicate){
            $errormsg .= "<div class='duplicateurls'> => ".$duplicate[0]." </div>";
        }        
    }
    else {
        /*********** Count total Number of keywords of all other locations and current added keywords (START) *************/
        $count_keywords = 0;
        if($_POST[keyword_count] > 0){
            for($i=1; $i<=$_POST[keyword_count]; $i++){
                $key = 'LE_Repu_Keyword_'.$i;
                //echo $_POST[activation][$i];
                if(!empty($_POST[$key]) && $_POST[activation][$i-1] == 'active'){
                    $count = 1;
                }else{
                    $count = 0;
                }
                $count_keywords = $count_keywords + $count;
            }
        }

        $count_all_locations_kw = $all_users_kw + $count_keywords;
        /*********** Count total Number of keywords of all other locations and current added keywords (END) *************/

        $current_user_comp_keywords = billing_info($UserID);
        $current_user_ckw_url = $comp_keywords[c_key];
        $current_user_ckw_limit = $current_user_ckw_url*$count_keywords;

        $count_all_locations_ckw = $all_users_comp_kw + $current_user_ckw_limit;

        if((($total_keyword_limit >= $count_all_locations_kw) && ($comp_total_keyword_limit >= $count_all_locations_ckw) && ($billing_enable == '1')) || ($billing_enable == '0')){

            include_once ABSPATH . '/wp-content/themes/twentytwelve/analytics/my_functions.php';

            if ($UserID != 1180 && $UserID != 1317) {  //doctors account don't need to reverse seo
                foreach ($_POST['landing_page'] as $lp) {
                    $lp = $lp[0];

                    if ($lp != "") {
                        $q = "SELECT * FROM primary_keywords WHERE url like '%$lp%'";
                        $info = row_array($q);

                        if (isset($info['url_type'])) { //update
                            $type = $info['url_type'];

                            if (!preg_match("/P/", $type)) {
                                if ($type == "")
                                    $type = "P";
                                else {
                                    $types = explode(",", $type);
                                    $types[] = "P";

                                    $type = implode(",", $types);
                                }

                                $q = "update primary_keywords set url_type = '{$type}' where url LIKE '%{$lp}%'";
                                mysql_query($q) or die(mysql_error());
                            }
                        } else { //insert
                            $q = "insert into primary_keywords (keyword, url, client_id, url_type) values (
                                                '', 
                                                '{$lp}', 
                                                '{$UserID}', 
                                                'P'
                                        )";

                            mysql_query($q) or die(mysql_error());
                        }
                    }
                }
            }
            // Keywords update history
            $keywords_update_history = $wpdb->get_results("SELECT * FROM `wp_keywords_update_history` WHERE `user_id` = $UserID");
            if (!empty($keywords_update_history)) {
                foreach ($keywords_update_history as $row_his) {
                    $sort_key_history[$row_his->keyword_key]['update_user_id'] = $row_his->update_user_id;
                    $sort_key_history[$row_his->keyword_key]['update_date'] = $row_his->update_date;
                    $sort_key_history[$row_his->keyword_key]['status'] = $row_his->status;
                }
            }

            for ($n = 1; $n <= $_POST['keyword_count']; $n++) {
                if ($sort_key_history['LE_Repu_Keyword_' . $n]['status'] != $_POST['activation'][$n - 1]) {
                    $insert_keywords_update_history['user_id'] = $UserID;
                    $insert_keywords_update_history['keyword_key'] = 'LE_Repu_Keyword_' . $n;
                    $insert_keywords_update_history['update_user_id'] = $current_id;            
                    $insert_keywords_update_history['status'] = $_POST['activation'][$n - 1];
                    $wpdb->insert('wp_keywords_update_history', $insert_keywords_update_history);
                }
            }
            // Keywords update history end

            if (isset($_POST['keyword_count']))

                for ($n = 1; $n <= $_POST['keyword_count']; $n++) {
                    $keyword = $_POST['LE_Repu_Keyword_' . $n];

                    if ($keyword == "")
                        continue;

                    $q = "SELECT * FROM all_keywords where keyword LIKE '{$keyword}'";
                    $result = mysql_query($q) or die(mysql_error());

                    $rs = mysql_fetch_assoc($result);

                    if (!isset($rs['id'])) {
                        $q = "INSERT INTO all_keywords (keyword) values ('{$keyword}')";
                        mysql_query($q) or die(mysql_error());
                    }
                }

            for ($n = 1; $n <= $_POST['keyword_count']; $n++) {



                $LE_Repu_Keyword_name = 'LE_Repu_Keyword_' . $n;



                $LE_Repu_Keyword_val = $_POST[$LE_Repu_Keyword_name];



                //if (trim($LE_Repu_Keyword_val) != "") {

                update_user_meta($UserID, $LE_Repu_Keyword_name, trim($LE_Repu_Keyword_val));

                //}
            }

            update_user_meta($UserID, "Content_keyword_Site", $_POST);

            // Automatic update with BTL

            $success_msg = 'Successfully Updated.';

            // Competitor URL update
            //$URI = site_url() . '/cron/competitor-url-key-insert.php?user_id=' . $UserID;
            $URI = site_url() . '/cron/competitor-url-key-insert.php?user_id=' . $UserID;
            $ch = curl_init($URI);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $result_on = curl_exec($ch);
            // end

            $checklimit = "keywords";
            $agency_package_notification = agency_package_notification($checklimit);

        }elseif(($total_keyword_limit < $count_all_locations_kw) && ($billing_enable == '1')){
            echo '<div class="alert alert-danger keyword_alert">Keyword is not saved, Because your Limit to add keyword is over, So please contact to Administrator or Purchase <strong>Keyword Add-Ons</strong> for increase Limit.</div>';
        }elseif(($comp_total_keyword_limit < $count_all_locations_ckw) && ($billing_enable == '1')){
            echo '<div class="alert alert-danger keyword_alert">Keyword is not saved, Because your Limit for <strong>Comp keyword</strong> is over, So please contact to Administrator or Purchase <strong>Comp Keyword Add-Ons</strong> for increase Limit.</div>';
        }

    }
}

//need both section
$sort_key_history = array();
$keywords_update_history = $wpdb->get_results("SELECT * FROM `wp_keywords_update_history` WHERE `user_id` = $UserID");
if (!empty($keywords_update_history)) {
    foreach ($keywords_update_history as $row_his) {
        $sort_key_history[$row_his->keyword_key]['update_user_id'] = $row_his->update_user_id;
        $sort_key_history[$row_his->keyword_key]['update_date'] = $row_his->update_date;
    }
}

$keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
//pr($keywordDat);exit;
$sort_by = 'primary-key-asc';
if (isset($_GET['sort-by'])) {
    $sort_by = $_GET['sort-by'];
}

?>
<style>    
.rerunkeyreport{
    padding: 4px 10px !important;
    color: #fff !important;
    font-weight: bold !important;
    font-size: 12px !important;
}
.rerunkeyreport:hover{
    color: #fff !important;
}
.alert.alert-success{
    margin-bottom: 0px;
}
.label_run{
    float: left;
    width: 120px;
    font-weight: 600;
margin-left:60px;
margin-bottom: 5px;
}
.detail_run{
width:330px;
padding-left:5px;
margin-bottom: 5px;
}
.rank_setting{
float:right;
font-weight:600;
}

</style>
<form name="reportform" id="reportform" method="post">
        <input type="hidden" id="reportrun" name="reportrun" />
        <input type="hidden" id="rank_checkoptions" name="rank_checkoptions" />
</form>

<div class="panel panelmain">
    <div class="col-lg-12 ">
        <div class="contaninerinner">         
            <h4 id="keyword_section">Keywords Section  - <?php echo $location_name . " ( ".$location_web." )"; ?></h4> 
            <div class="bread_crumb">
                <ul>
                    <li title="Locations">
                        <a href="<?php echo ST_LOC_PAGE; ?>?parm=locations">Locations</a> >>
                    </li>
                    <li title="Site Keywords">
                        Site Keywords
                    </li>
                </ul>
            </div>
            <div id="content" role="main">

                <div class="en-left">

                    <?php include_once (get_template_directory() . '/master-admin/left-menu.php'); ?>

                </div>

                <div class="en-right">

                    <?php
                    if ($success_msg != '') {
                        echo '<div class="alert alert-success"><strong>' . $success_msg . '</strong></div><div class="clear_both"></div>';
                    }
                    else if($errormsg != ''){
                        echo '<div style="color:red;margin-left:20px;font-weight: bold;font-size:17px;">'.$errormsg.' </div><div class="clear_both"></div>';
                    }
                    $rank_check_options = get_user_meta($UserID, 'rank_check_options',TRUE);
                    ?>

                    <form name="keyword" method="post" action="">   
                        <div style="float:right;width:500px;">
                            <div id="not_message" style="color:red;display: none;">
                                This choice(Check Only Landing Pages) is designed for e-commerce sellers using big box stores like Amazon.  ONLY use this feature for shared sites.
                                <div class="clear_both"></div>
                            </div>
                            <a class="settings" style="position:relative;float:right;margin-left: 10px;cursor: pointer;" onclick="jQuery('#rank_setting').toggle('slow');"></a>    
                            <div style="margin-left: 10px; margin-top: -2px; display: none;float:right;" id="rank_setting">

                                <input <?php if ($rank_check_options == "" || $rank_check_options == "check_full_site") echo 'checked'; ?> type="radio" name="rank_check_options" value="check_full_site" onclick="rank_check_options_func(this.value)"><span style="margin-left:10px;">Check Full Site</span>
                                <input <?php if ($rank_check_options == "check_only_landing_pages") echo 'checked'; ?> type="radio" name="rank_check_options" value="check_only_landing_pages" onclick="rank_check_options_func(this.value)"><span style="margin-left:10px;">Check Only Landing Pages</span>
                            </div>
                          <?php 
//Code starts By rudra 18-jan-2017

$client_website = get_user_meta($user_id, 'website', true);
include_once(get_template_directory() . '/analytics/my_functions.php');
include_once(get_template_directory() . '/common/report-function.php');
$sql = "SELECT * FROM `seo` WHERE `MCCUserId` = $UserID order by `DateOfRank` desc LIMIT 1";
$last_rank = row_array($sql);
$last_rank_date = '';
if (!empty($last_rank)) {
    $last_rank_date = date("d M Y", strtotime($last_rank['DateOfRank']));
}
?>
<div style="float:left;">
<div class="label_run">Last Run: 
</div>
<div class="detail_run">
<?php if(!empty($last_rank_date)){echo $last_rank_date;}else{ echo "N/A";}?>
</div>
        <div class="label_run">Next Scheduled: </div>
	<div class="detail_run"><?php 
        if(strtolower(date("l")) == 'sunday'){
            echo date('d M Y');
        }
        else{
            echo date('d M Y', strtotime("next Sunday"));
        }
        ?>
	</div>
</div>   
<div class="rank_setting">
Rank Setting:</div>

<?php //Code ends by rudra 18-jan-2017 ?>
                            <script>
                                function rank_check_options_func(value) {

                                    if (value == 'check_only_landing_pages') {
                                        jQuery('#not_message').show();
                                    } else {
                                        jQuery('#not_message').hide();
                                    }
                                }
                            </script>
                        </div>
                        <div class="clear_both"></div>
                        <div style="margin-top: 5px; float:left;width:100%;">
                            <div class="margin-bottom-10">
                                <div style="float:left;width:30%">                                
                                    <div style="">
                                        <b>Sort By:</b>
                                        <select name="sort_by" onchange="sort_func(this.value)">
                                            <option <?php if ($sort_by == 'primary-key-asc') echo 'selected'; ?> value="primary-key-asc">Primary Keyword A - Z</option>
                                            <option <?php if ($sort_by == 'primary-key-desc') echo 'selected'; ?> value="primary-key-desc">Primary Keyword Z - A</option>
                                            <option <?php if ($sort_by == 'date-desc') echo 'selected'; ?> value="date-desc">Updated Date (Newest to Oldest)</option>
                                            <option <?php if ($sort_by == 'date-asc') echo 'selected'; ?> value="date-asc">Updated Date (Oldest to Newest)</option>
                                        </select>
                                    </div>                                
                                </div>

                                <div style="float:right;width:50%;">

                                    <div class="rankreport"style="width: unset;float:right;">                            
                                            <?php if ($ReportID > 0) { ?>
                                                 <a href='javascript:;' data-run="<?php echo $ReportID; ?>" class="rerunkeywords btn btn-primary rerunkeyreport">Rerun Keywords</a>
                                            <?php } else {
                                                ?>
                                                 <a href='javascript:;' data-run="0" class="rerunkeywords btn btn-primary rerunkeyreport">Run Keywords</a>
                                                 <?php
                                            } ?>

                                         <a href="<?php echo site_url(); ?>/wp-content/plugins/settings/views/csv_keyword_list.php?csv_type=keyword_list&mccuserid=<?php echo $UserID; ?>" ><input  type="button"  value="Download Keyword"></a>
                                     </div>

                                    <select onchange="keywords_show_func(this.value)" style="float:right;height:29px;margin-right:20px;">
                                        <option value="all">All Keywords</option>
                                        <option selected value="active">Active Keywords</option>
                                        <option value="inactive">Inactive Keywords</option>
                                    </select>
                                    <div style="float:right;font-weight: bold;margin-right:10px;margin-top: 5px;">Keyword Type:</div>
                                </div>
                                <div class="clearfix"></div>
                            </div>  

                            <?php
                                   
                            if (!empty($keywordDat)) {

                                $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
                                $primarylander = $keywordDat["primarylander"];
                                $secondarylander = $keywordDat["secondarylander"];
                                $Additionalsnotes = $keywordDat["Additionalsnotes"];

                                $activation = $keywordDat["activation"];
                                $target_keyword = $keywordDat["target_keyword"];
                                $delete = $keywordDat["delete"];

                                $landingpage = $keywordDat["landing_page"];
                                $livedate = $keywordDat['live_date'];
                            } else {
                                $keywordDat['keyword_count'] = 0;
                            }
                           
                            global $wpdb;
                            
                            if ($sort_by == 'date-desc' || $sort_by == 'date-asc') {
                                if ($sort_by == 'date-asc') {
                                    $order_by_con = 'asc';
                                } else {
                                    $order_by_con = 'desc';
                                }
                                $sql = "SELECT * FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id` WHERE uh.`user_id` = $location_id AND uh.`user_id` = $UserID && `meta_value` != '' ORDER BY `uh`.`update_date` $order_by_con";
                                $KeyWordQuery = $wpdb->get_results($sql);

                                if (empty($KeyWordQuery)) {
                                    $sort_by = 'primary-key-asc';
                                }

                                $sql = "SELECT * FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id` WHERE uh.`user_id` = $location_id AND uh.`user_id` = $UserID && `meta_value` = '' ORDER BY `uh`.`update_date`  $order_by_con";
                                $null_key_index = $wpdb->get_results($sql);
                            }
                            if ($sort_by == 'primary-key-asc' || $sort_by == 'primary-key-desc') {
                                if ($sort_by == 'primary-key-asc') {
                                    $order_by_con = 'asc';
                                } else {
                                    $order_by_con = 'desc';
                                }
                                $sql = 'select * from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" && `meta_value` != "" order by `meta_value` ' . $order_by_con;
                                $KeyWordQuery = $wpdb->get_results($sql);                                
                                $sql = 'SELECT * FROM wp_usermeta WHERE user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
                                $null_key_index = $wpdb->get_results($sql);
                            }                            
                            //pr($sql);
                            $KeyWordQuery = array_merge($KeyWordQuery, $null_key_index);                                                        
                            
                            ?>
                            
                            <div class="keywords_site">
                                <?php
                                $ks = 1;                                
                                foreach ($KeyWordQuery as $row_key) {
                                   
                                    $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
                                    $j = $ks - 1;
                                    $keywords = trim(get_user_meta($UserID, "LE_Repu_Keyword_" . $ks . "",TRUE));
                                     
                                    //echo $ks.'--'.$keywords.'<br/>';
                                    //if ($keywords != "") {
                                    //echo $delete[$j];

                                    $class_name = 'active_key';
                                    if ($activation[$j] == 'inactive') {
                                        $class_name = 'inactive_key';
                                    }
                                    if ($delete[$j] == 1) {
                                        $class_name = 'delete_key';
                                    }
                                    
                                    ?>
                                
                                    <div id="div_key_<?php echo $ks ?>" class="all_key <?php echo $class_name; ?>" style="float:left; width:100%; padding:15px; border:1px solid #d14836; margin-bottom:20px;display: <?php echo $delete[$j] == 1 ? 'none' : 'block' ?>">
                                        <div class="div_sec">
                                            <div class="left_key_c">Primary Keywords <?php //echo $ks                    ?></div>
                                            <div class="right_key_c">
                                                <input type="text" class="key_width" <?php if (trim($keywords) != "") echo 'readonly="true"'; ?> name="LE_Repu_Keyword_<?php echo $ks; ?>" value="<?php echo $keywords; ?>" />                                                
                                            </div>
                                            <!--div style="float:right;">
                                                    <div class="rankreport" style="width:100px;">
                                                        <a>
                                                            <input id="delete_btn_key_<?php echo $ks ?>" type="button" onclick="delete_key_func('<?php echo $ks ?>')" value="Hide">
                                                        </a>
                                                    </div>
                                                </div-->
                                            <!--<input id="delete_val_key_<?php echo $ks ?>" type="hidden" name="delete[]" value="<?php if (isset($delete[$j])) echo $delete[$j]; ?>">-->
                                            <div class="clear_both"></div>
                                            <div class="left_key_c">Landing Page</div>
                                            <div class="right_key_c">
                                                <input type="text" class="key_width"  name="landing_page[<?php echo $j ?>][]" value="<?php echo $landingpage[$j][0]; ?>">
                                            </div>
                                            <div class="clear_both"></div>
                                            <div class="left_key_c">Live Date</div>
                                            <div class="right_key_c">
                                                <input class="key_width"  type="text" name="live_date[<?php echo $j ?>][]" value="<?php echo $livedate[$j][0]; ?>">
                                            </div>
                                            <div class="clear_both"></div>
                                            <div class="left_key_c">Home Page</div>
                                            <div class="right_key_c">
                                                <input class="key_width"  type="text" name="primarylander[<?php echo $j; ?>]" value="<?php echo $primarylander[$j]; ?>">
                                            </div>
                                            <div class="clear_both"></div>
                                            <div class="left_key_c">Resource Page</div>
                                            <div class="right_key_c">
                                                <input class="key_width"  type="text" name="secondarylander[<?php echo $j; ?>]" value="<?php echo $secondarylander[$j]; ?>">
                                            </div>
                                            <div class="clear_both"></div>
                                        </div>
                                        <div class="div_sec">
                                            <div class="left_key_c">Synonyms or related keywords</div>
                                            <div class="right_key_c">
                                                <?php for ($h = 0; $h < 5; $h++) { ?>
                                                    <input class="key_width" <?php if (trim($Synonyms_keyword[$j][$h]) != "") echo 'readonly="true"'; ?> type="text" name="Synonyms_keyword[<?php echo $j ?>][]" value="<?php echo $Synonyms_keyword[$j][$h]; ?>">
                                                    <div class="clear_both"></div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="div_sec">
                                            <div class="left_key_c">Target Keyword for SEO Campaign</div>
                                            <div class="right_key_c">
                                                <select name="target_keyword[<?php echo $j; ?>]" class="key_width">
                                                    <!--<option value="">Select</option>-->
                                                    <option <?php if ($target_keyword[$j] == 'Yes') echo 'selected'; ?> value="Yes">Yes</option>
                                                    <option <?php if ($target_keyword[$j] == 'No' || $target_keyword[$j] == '') echo 'selected'; ?> value="No">No</option>
                                                </select>  
                                            </div>
                                            <div class="clear_both"></div>
                                            <div class="left_key_c">Additional Notes to writers</div>
                                            <div class="right_key_c">
                                                <textarea name="Additionalsnotes[<?php echo $j; ?>]" rows="5" style="width:90%;"><?php echo $Additionalsnotes[$j]; ?></textarea>
                                            </div>
                                            <div class="clear_both"></div>
                                            <div class="left_key_c">Activation</div>
                                            <div class="right_key_c">
                                                <select class="all_activation" id="activation_key_<?php echo $ks ?>" name="activation[<?php echo $j; ?>]" style="width:200px;">
                                                    <option value="active">Active</option>
                                                    <option <?php if ($activation[$j] == 'inactive') echo 'selected'; ?> value="inactive">Inactive</option>
                                                </select> 
                                            </div>
                                            <div class="clear_both"></div>

                                            <?php
                                            if ($keywords != "") {
                                                if (!empty($sort_key_history["LE_Repu_Keyword_" . $ks])) {
                                                    ?>
                                                    <div class="left_key_c">Last Change By:</div>
                                                    <div class="right_key_c"><?php echo full_name($sort_key_history["LE_Repu_Keyword_" . $ks]['update_user_id']); ?></div>
                                                    <div class="clear_both"></div>
                                                    <div class="left_key_c">Change Date:</div>
                                                    <div class="right_key_c"><?php echo date("d M Y h:i a", strtotime($sort_key_history["LE_Repu_Keyword_" . $ks]['update_date'])); ?></div>
                                                <?php } else { ?>
                                                    <div class="left_key_c">Last Change By:</div>
                                                    <div class="right_key_c">Admin</div>
                                                    <div class="clear_both"></div>
                                                    <div class="left_key_c">Change Date:</div>
                                                    <div class="right_key_c"><?php echo date("d M Y h:i a", time() - 15 * 24 * 3600); ?></div>

                                                <?php } ?>
                                                <div class="clear_both"></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php
                                    //}
                                    // $ks++;
                                    //}
                                    /*
                                      else {
                                      echo '<style>
                                      .button_hidden
                                      {
                                      display:none;
                                      }
                                      </style>';
                                      echo "<h2>No keywords Found . Please enter the keyword in profile section</h2>";
                                      break;
                                      }
                                     */
                                }

///*
                                if (isset($_GET['import_medstar'])) {
                                    $path = dirname(__FILE__) . "/csv_MHVI_Test_Account.csv";
                                    $fp = fopen($path, 'r');
                                    if ($fp) {
                                        //echo 33333; exit;
                                        while (($csv = fgetcsv($fp)) !== FALSE) {
                                            $lines[] = $csv;
                                        }
                                        fclose($fp);
                                    }


                                    /*

                                      $lines = file($path);
                                     * 
                                     */
                                    //pr($lines);
                                    foreach ($lines as $index => $line) {
                                        if ($index < 1)
                                            continue;

                                        $elements = $line; //explode(",", $line);
                                        //echo '<div style="clear:both;height:20px;"></div>';
                                        //pr($line);
                                        //echo '<div style="clear:both;height:20px;"></div>';
                                        $js = $ks - 1;
                                        $landing_page = explode('#', $elements[1]);
                                        $landing_page = $landing_page[0];
                                        ?>

                                        <div id="div_key_<?php echo $ks ?>" class="<?php echo $class_name; ?>" style="float:left; width:100%; padding:15px; border:1px solid #d14836; margin-bottom:20px;">
                                            <div class="div_sec">
                                                <div class="left_key_c">Primary Keywords <?php //echo $ks                    ?></div>
                                                <div class="right_key_c">
                                                    <input class="key_width" type="text" value="<?php echo $elements[0]; ?>" name="LE_Repu_Keyword_<?php echo $ks; ?>">
                                                </div>
                                                <div class="clear_both"></div>
                                                <div class="left_key_c">Landing Page</div>
                                                <div class="right_key_c">
                                                    <input type="text" class="key_width" name="landing_page[<?php echo $js; ?>][]"  value="<?php echo $landing_page; ?>">
                                                </div>
                                                <div class="clear_both"></div>
                                                <div class="left_key_c">Live Date</div>
                                                <div class="right_key_c">
                                                    <input class="key_width" type="text" name="live_date[<?php echo $js; ?>][]">
                                                </div>
                                                <div class="clear_both"></div>
                                                <div class="left_key_c">Home Page</div>
                                                <div class="right_key_c">
                                                    <input class="key_width" type="text" value="http://www.medstarheartinstitute.org/" name="primarylander[<?php echo $js; ?>]">
                                                </div>
                                                <div class="clear_both"></div>
                                                <div class="left_key_c">Resource Page</div>
                                                <div class="right_key_c">
                                                    <input class="key_width" type="text" value="" name="secondarylander[<?php echo $js; ?>]">
                                                </div>
                                                <div class="clear_both"></div>
                                            </div>
                                            <div class="div_sec">
                                                <div class="left_key_c">Synonyms or related keywords</div>
                                                <div class="right_key_c">
                                                    <?php for ($h = 0; $h < 5; $h++) { ?>
                                                        <input class="key_width" type="text" value="<?php echo $elements[$h + 2]; ?>" name="Synonyms_keyword[<?php echo $js; ?>][]">
                                                        <div class="clear_both"></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="div_sec">
                                                <div class="left_key_c">Target Keyword for SEO Campaign</div>
                                                <div class="right_key_c">
                                                    <select name="target_keyword[<?php echo $js; ?>]" class="key_width">
                                                        <!--<option value="">Select</option>-->
                                                        <option value="Yes">Yes</option>
                                                        <option selected value="No">No</option>
                                                    </select>  
                                                </div>
                                                <div class="clear_both"></div>
                                                <div class="left_key_c">Additional Notes to writers</div>
                                                <div class="right_key_c">
                                                    <textarea name="Additionalsnotes[<?php echo $js; ?>]" rows="5" style="width:90%;"></textarea>
                                                </div>
                                                <div class="clear_both"></div>
                                                <div class="left_key_c">Activation</div>
                                                <div class="right_key_c">
                                                    <select name="activation[<?php echo $js; ?>]" style="width:200px;">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select> 
                                                </div>
                                                <div class="clear_both"></div>
                                            </div>
                                        </div>









                                        <?php
                                        $ks++;

                                        //if($index > 10)
                                        //break;
                                        $keywordDat['keyword_count'] ++;
                                    }
                                }
//*/
                                ?>


                            </div>

                            <div class="button_hidden" style="float:left; width:500px; margin-left:155px;">
                                <input type="submit" name="submit_keywrds" value="Save" class="new_btn_class" />
                                <input type="button" class="add_more_keyword new_btn_class" name="add_more_keyword" value="Add More">
                                <?php if ($UserID == 1180) { ?>
                                                                                                <!--<input type="button" onclick="make_all_inactive()" class="new_btn_class" value="Make all Inactive">-->
                                <?php } ?>
                            </div>

                            <input type="hidden" class="keyword_count" name="keyword_count" value="<?php echo $keywordDat['keyword_count']; //$ks      ?>">






                        </div>
                    </form>
                    <script>

                        jQuery('.add_more_keyword').on('click', function () {
                            var count_keyword = jQuery('.keyword_count').val();

                            var keyword_count = count_keyword;
                            var morefivekeyword = parseInt(keyword_count) + parseInt(5);
                            var prevvalue = parseInt(morefivekeyword) - parseInt(4);

                            jQuery('.keyword_count').val(morefivekeyword);

                            for (var m = prevvalue; m <= morefivekeyword; m++) {
                                var keyword_index = keyword_count;
                                keyword_count++;
                                //jQuery('.keywords_site').append('<div style="float:left; width:550px; padding:5px; border:1px solid #efefef; margin-bottom:10px;"><div style="float:left; width:500px; margin:5px;"><div style="float:left; width:150px;"><b>Keywords:</b></div><input type="text" value="" name="LE_Repu_Keyword_' + keyword_count + '"></div><div style="float:left; width:500px; margin:5px; "><div style="float:left; width:150px;"><b>Synonyms or related keywords</b></div><div style="width:350px; padding:0px 148px 6px; "><input type="text" value="" name="Synonyms_keyword[' + keyword_index + '][]"></div><div style="width:350px; padding:0px 148px 6px; "><input type="text" value="" name="Synonyms_keyword[' + keyword_index + '][]"></div><div style="width:350px; padding:0px 148px 6px; "><input type="text" value="" name="Synonyms_keyword[' + keyword_index + '][]"></div><div style="width:350px; padding:0px 148px 6px; "><input type="text" value="" name="Synonyms_keyword[' + keyword_index + '][]"></div><div style="width:350px; padding:0px 148px 6px; "><input type="text" value="" name="Synonyms_keyword[' + keyword_index + '][]"></div><div style="float:left; width:500px;"><div style="float:left; width:150px;"><b>Landing Page</b></div><div style="float:left; width:350px;"><input type="text" name="landing_page[' + keyword_index + '][]" style="width:270px;" value=""></div></div><div style="clear:both;height:10px;"></div><div style="float:left; width:500px;"><div style="float:left; width:150px;"><b>Live Date</b></div><div style="float:left; width:350px;"><input type="text" name="live_date[' + keyword_index + '][]" style="width:270px;" value=""></div></div></div><div style="float:left; width:500px; margin:5px; "><div style="float:left; width:150px;"><b>Home Page</b></div><div style="float:left; width:350px;"><input type="text" value="" style="width:270px;" name="primarylander[]"></div></div><div style="float:left; width:500px; margin:5px; "><div style="float:left; width:150px;"><b>Resource Page</b></div><div style="float:left; width:350px;"><input type="text" value="" style="width:270px;" name="secondarylander[]"></div></div><div style="float:left; width:500px; margin:5px; "><div style="float:left; width:150px;"><b>Additionals Notes to writers</b></div><div style="float:left; width:350px;"><textarea cols="45" rows="5" name="Additionalsnotes[]"></textarea></div></div></div>'); //' + keyword_count + '
                                var html = '<div style="float:left; width:100%; padding:15px; border:1px solid #d14836; margin-bottom:20px;">' +
                                        '<div class="div_sec">' +
                                        '<div class="left_key_c">Primary Keywords</div>' +
                                        '<div class="right_key_c">' +
                                        '<input class="key_width" type="text" name="LE_Repu_Keyword_' + keyword_count + '">' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '<div class="left_key_c">Landing Page</div>' +
                                        '<div class="right_key_c">' +
                                        '<input type="text" class="key_width"  name="landing_page[' + keyword_index + '][]">' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '<div class="left_key_c">Live Date</div>' +
                                        '<div class="right_key_c">' +
                                        '<input class="key_width"  type="text" name="live_date[' + keyword_index + '][]">' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '<div class="left_key_c">Home Page</div>' +
                                        '<div class="right_key_c">' +
                                        '<input class="key_width"  type="text" name="primarylander[' + keyword_index + ']">' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '<div class="left_key_c">Resource Page</div>' +
                                        '<div class="right_key_c">' +
                                        '<input class="key_width"  type="text" name="secondarylander[' + keyword_index + ']">' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '</div>' +
                                        '<div class="div_sec">' +
                                        '<div class="left_key_c">Synonyms or related keywords</div>' +
                                        '<div class="right_key_c">' +
<?php for ($h = 0; $h < 5; $h++) { ?>
                                    '<input class="key_width" type="text" name="Synonyms_keyword[' + keyword_index + '][]">' +
                                            '<div class="clear_both"></div>' +
<?php } ?>
                                '</div>' +
                                        '</div>' +
                                        '<div class="div_sec">' +
                                        '<div class="left_key_c">Target Keyword for SEO Campaign</div>' +
                                        '<div class="right_key_c">' +
                                        '<select name="target_keyword[' + keyword_index + ']" class="key_width">' +
                                        '<option value="Yes">Yes</option>' +
                                        '<option selected value="No">No</option>' +
                                        '</select>' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '<div class="left_key_c">Additional Notes to writers</div>' +
                                        '<div class="right_key_c">' +
                                        '<textarea name="Additionalsnotes[' + keyword_index + ']" rows="5" style="width:90%;"></textarea>' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '<div class="left_key_c">Activation</div>' +
                                        '<div class="right_key_c">' +
                                        '<select name="activation[' + keyword_index + ']" style="width:200px;">' +
                                        '<option value="active">Active</option>' +
                                        '<option value="inactive">Inactive</option>' +
                                        '</select>' +
                                        '</div>' +
                                        '<div class="clear_both"></div>' +
                                        '</div>' +
                                        '</div>';
                                jQuery('.keywords_site').append(html);
                            }
                        });

                        function keywords_show_func(type) {
                            jQuery('.all_key').hide();
                            jQuery('.' + type + '_key').show();
                            jQuery('.delete_key').hide();
                            jQuery('#keyword_section').html('Keywords Section [' + type + ' keywords]');
                        }

                        jQuery('.all_key').hide();
                        jQuery('.active_key').show();

                        ///*
                        function delete_key_func(key_id) {
                            jQuery('#div_key_' + key_id).hide('slow');
                            jQuery('#delete_val_key_' + key_id).val('1');
                            jQuery('#activation_key_' + key_id).val('inactive');
                        }

                        function make_all_inactive() {
                            jQuery('.all_activation').val('active');
                        }

                        function sort_func(value) {
                            document.location.href = '<?php echo ST_LOC_PAGE; ?>?parm=keywords&location_id=<?php echo $location_id; ?>&sort-by=' + value;
                        }
                        
                        jQuery(document).on("click",".rerunkeywords",function(){
                            var hasrun = jQuery(this).attr("data-run");
                            var ms = "Are you sure to run keywords?";
                            if(hasrun == 1){
                                ms = "Are you sure to rerun keywords?";
                            }
                            var conf = confirm(ms);
                            if(conf){
                                jQuery("#rank_checkoptions").val(jQuery("input[name=rank_check_options]:checked").val());
                                jQuery("#reportrun").val(hasrun);
                                jQuery("#reportform").submit();
                            }
                         }); 
                        
                        //*/
                    </script>

                    <?php
                    if ($keywordDat['keyword_count'] == 0 && !isset($_GET['import_medstar'])) {
                        ?>

                        <script>jQuery('.add_more_keyword').click();</script>

                        <?php
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
</div>
