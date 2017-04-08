<?php

$styl = '<style>
    table.tabl-sort thead th.tablesorter-headerAsc,
    table.tabl-sort thead th.tablesorter-headerDesc,
    table.tabl-sort thead th.tablesorter-headerUnSorted{

        padding-right:15px !important;
        vertical-align:middle;
        background-position:right center;
        background-repeat:no-repeat;
        cursor:pointer;

    }
    table.tabl-sort thead th.tablesorter-headerUnSorted{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/bg.gif);
    }
    table.tabl-sort thead th.tablesorter-headerAsc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/desc.gif);
    }
    table.tabl-sort thead th.tablesorter-headerDesc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/asc.gif);
    }
     .urlcls{
        font-size: 13px !important;
    }
</style>';

include get_template_directory(). '/common/report-function.php';
include_once(get_template_directory() . '/analytics/my_functions.php');
include_once(get_template_directory() . '/common/schedule-report.php');
include_once(SET_COUNT_PLUGIN_DIR . '/custom_functions.php');
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';

$db_report_name = ST_RANK_REPORT;
if (isset($_POST['btn_download-report'])) {
    
    ob_end_clean();
    if ($_POST['dwnld_type'] == 'pdf') {
        
        $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                .keyword_width{width:20%;} .text-center{S;} 
                .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                td.text-center { text-align: center; padding: 10px 5px; } .tbl{text-align: center; border: 1px solid #ddd;
                padding: 10px; width: 300px;  display: inline-block; margin-right: 15px;} .col-md-4 .col-md-12{padding: 10px;}
                            .location {border-bottom: 5px solid #ddd; padding: 15px; width: 100%; margin-bottom: 20px; } td, th {
                padding: 6px;
            } .text-right {
                text-align: center;
                margin-bottom: 13px;
            } </style>';
        
        $str .= rd_pdf_header();        
        $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Target Vs Ranking Report</h3><br/>';  
        $ht = 70;                     
        $str .= '<div class="content">';
        
        foreach ($locations as $location) {
            $ht = $ht + 70;
            $location_id = $location->id;
            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;            
            $website = $client_website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
            
            $Content_keyword_Site = get_user_meta($user_id, "Content_keyword_Site", true);
            $Synonyms_keyword_arr = $Content_keyword_Site['Synonyms_keyword'];
            $activation = $Content_keyword_Site['activation'];
            $landing_page_count = 0;
            $landing_page_url_arr = array();
            $RankingURL_arr = array();
            $rank_arr = array();
            $all_url = array();
            $keyword_landing_arr = array();
            $match_ranking_url = array();
            
            if (!empty($Content_keyword_Site)) {
                foreach ($Content_keyword_Site['landing_page'] as $landing_index => $landing_page) {
                    if ($Content_keyword_Site['activation'][$landing_index] != 'inactive') {
                        $landing_page_url = $landing_page[0];
                        if (trim($landing_page_url) != '' && !in_array($landing_page_url, $landing_page_arr)) {
                            $landing_page_count++;

                            $landing_page_url_arr[] = rtrim(str_replace(array('http://', 'https://', 'www.'), "", $landing_page_url), '/\\');
                            $cal_index = $landing_index + 1;
                            $keyword_landing_arr[strtolower($Content_keyword_Site['LE_Repu_Keyword_' . $cal_index])] = $landing_page_url;
                        }
                    }
                }
            }
            $total_target_url = count($keyword_landing_arr);
            $total_rank_count = 0;
            $total_keywords_count = 0;
            
            $all_active_keywords = rd_all_active_keywords($user_id);
            
            $str .= '<fieldset class="location">
                <div><h4>Location : '.$brand.' ( '.$website.' )</h4></div>';
            
            $str .= '
            <table border="1" cellspacing="0" class="c2" style="position: relative; top: 15px; border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">
                <thead>
                    <tr>
                        <th width="26%">Keyword</th>
                        <th width="32%">Target URL</th>
                        <th width="32%">Ranking URL</th>
                        <th width="5%">Rank</th>
                        <th width="5%">Match</th>
                    </tr>
                </thead>
                <tbody>';

                if (!empty($all_active_keywords)) {
                    foreach ($all_active_keywords as $row_key) {
                        $sql = 'SELECT Keyword, RankingURL, CurrentRank FROM `seo` '
                                . 'WHERE `MCCUserId` = ' . $user_id . ' and Keyword LIKE "' . $row_key . '"';
                        $result = row_array($sql);
                        $vl1 = $result['Keyword'] != '' ? $result['Keyword'] : $row_key;
                        $landing_page_url = $keyword_landing_arr[$row_key];
                        $RankingURL = $result['RankingURL'];
                        $str .= 
                        '<tr>
                            <td><span style="background:#22B04B; margin-right:6px;" class="badge">P</span>'.$vl1.'</td>
                            <td>'.$landing_page_url.'</td>
                            <td>'.$RankingURL.'</td>
                            <td class="text-center">';                                            
                                if ($result['CurrentRank'] > 0) {
                                    $str .= $result['CurrentRank'];
                                } else {
                                    $str .= '50+';
                                }

                            $str .= '</td><td class="text-center">';

                            $landing_page_url = str_replace(array('http://', 'https://', 'www.'), "", $keyword_landing_arr[$row_key]);
                            $RankingURL = str_replace(array('http://', 'https://', 'www.'), "", $RankingURL);
                            if ($RankingURL != "" && $RankingURL == $landing_page_url) {
                                $str .= '<span style="color:green">Yes</span>';
                            } else {
                                $str .= '<span style="color:red">No</span>';
                            }
                            $str .='</td></tr>';                                    
                    }
                } else {

                    $str .= '<tr><td colspan="5">No Data Found</td></tr>';

                }


            $str .= '</tbody>
            </table>';

            $str .='<div style="clear:both;height:20px;"></div></fieldset>';            
        }
        
        $str .= '</div>';
                
        require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
        $dompdf = new DOMPDF();            
        $dompdf->load_html($str);         
        $widpdf = 1000;
        $customPaper = array(0,0,$widpdf,$ht);
        $dompdf->set_paper($customPaper);            
        $dompdf->render();            
        $user_id = $UserID;
        
        include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
        $pdf = $dompdf->output();            
        $dompdf->stream("rank_vs_target_report.pdf", array("Attachment" => true));
        exit;
        
    }
    else if ($_POST['dwnld_type'] == 'csv') {
        
        $FilePath = "target_vs_ranking_report.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename=' . $FilePath);    
        ob_clean();
        $fp = fopen('php://output', "w");
        
        $headertop = array('','','Keyword','Target url','Ranking Url','Rank','Match');
        $headerempty = array('','','','','','','');
        
        foreach ($locations as $location) {
            $ht = $ht + 70;
            $location_id = $location->id;
            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;            
            $website = $client_website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
            
            $Content_keyword_Site = get_user_meta($user_id, "Content_keyword_Site", true);
            $Synonyms_keyword_arr = $Content_keyword_Site['Synonyms_keyword'];
            $activation = $Content_keyword_Site['activation'];
            $landing_page_count = 0;
            $landing_page_url_arr = array();
            $RankingURL_arr = array();
            $rank_arr = array();
            $all_url = array();
            $keyword_landing_arr = array();
            $match_ranking_url = array();
            
            if (!empty($Content_keyword_Site)) {
                foreach ($Content_keyword_Site['landing_page'] as $landing_index => $landing_page) {
                    if ($Content_keyword_Site['activation'][$landing_index] != 'inactive') {
                        $landing_page_url = $landing_page[0];
                        if (trim($landing_page_url) != '' && !in_array($landing_page_url, $landing_page_arr)) {
                            $landing_page_count++;

                            $landing_page_url_arr[] = rtrim(str_replace(array('http://', 'https://', 'www.'), "", $landing_page_url), '/\\');
                            $cal_index = $landing_index + 1;
                            $keyword_landing_arr[strtolower($Content_keyword_Site['LE_Repu_Keyword_' . $cal_index])] = $landing_page_url;
                        }
                    }
                }
            }
            $total_target_url = count($keyword_landing_arr);
            $total_rank_count = 0;
            $total_keywords_count = 0;
            
            $headerlocname = array('Location',$brand.' ('.$website.')','','','','','');
            fputcsv($fp, $headerlocname);
            fputcsv($fp, $headerempty);            
            
            $all_active_keywords = rd_all_active_keywords($user_id);
                       

                if (!empty($all_active_keywords)) {
                    
                    fputcsv($fp, $headertop);
                    
                    foreach ($all_active_keywords as $row_key) {
                        $sql = 'SELECT Keyword, RankingURL, CurrentRank FROM `seo` '
                                . 'WHERE `MCCUserId` = ' . $user_id . ' and Keyword LIKE "' . $row_key . '"';
                        $result = row_array($sql);
                        $vl1 = $result['Keyword'] != '' ? $result['Keyword'] : $row_key;
                        $landing_page_url = $keyword_landing_arr[$row_key];
                        $RankingURL = $result['RankingURL'];
                        $curank = '50+';
                        if ($result['CurrentRank'] > 0) {
                            $curank = $result['CurrentRank'];
                        }                        
                        $landing_page_url = str_replace(array('http://', 'https://', 'www.'), "", $keyword_landing_arr[$row_key]);
                        $RankingURL = str_replace(array('http://', 'https://', 'www.'), "", $RankingURL);
                        if ($RankingURL != "" && $RankingURL == $landing_page_url) {
                            $yesno = 'Yes';
                        } else {
                            $yesno = 'No';
                        }                        

                        $valuesar = array('','',$vl1,$landing_page_url,$RankingURL,$curank,$yesno);
                        fputcsv($fp, $valuesar);
                            
                    }
                } else {
                    
                    $headeremptyval = array('','','No Data found','','','','');
                    fputcsv($fp, $headeremptyval);

                }
                
                fputcsv($fp, $headerempty);
                fputcsv($fp, $headerempty);
                fputcsv($fp, $headerempty);
                fputcsv($fp, $headerempty);
                fputcsv($fp, $headerempty);           
        }
        
        ob_flush(); 
        fclose($fp);
        exit;
    }    
}
else if (isset($_POST['btn_schedule-executive-report'])) {
    //pr($_POST); die;    
    $shEmails = array_filter($_POST['sh-email'], function($a) { //IF PHP >=3.5 ELSE create_function('$a','!empty($a["to"]) && empty($a["id"]);')
        //if 'id'-key is set, then do not remove/filter this item from the array. The 'id'-key items will be handled differently to delete or edit;
        return !empty($a["to"]) || !empty($a["id"]);
    });
    if (empty($shEmails)) {
        $popupErrMsg = 'Please type your email';        
        
    } else {
        
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $sql = "SELECT sch_id FROM {$wpdb->prefix}mcc_sch_settings WHERE sch_type='$db_report_name'";
        $settingId = $wpdb->get_var($sql);        
        $sch_status = empty($_POST['sh-send-report-via-email']) ? 0 : 1;
        $schData = array(
            'sch_frequency' => $_POST['sh-how-often'],
            'sch_reportVolume' => $_POST['sh-volume'],
            'report_type' => $_POST['report_type'],           
            'sch_status' => $sch_status,		            
            'sch_type' => $db_report_name,
            'sch_uId' => $user_id,
        );
        
        if (empty($settingId)) {
            $queryStatus = $wpdb->insert(
            	"{$wpdb->prefix}mcc_sch_settings",
				$schData,
				array('%s', '%s', '%s', '%d', '%s', '%d')
            );

            if ($queryStatus === false) {
                $popupErrMsg = 'Invalid Query! Please try again.';

            } else {
                $settingId = $wpdb->insert_id;
                $popupSucsMsg = 'Email scheduler is successfully added';
                unset($_POST);

            }
        } else {
                                    
            $tbl = $wpdb->prefix."mcc_sch_settings";           
            $queryStatus = $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . $tbl . " SET sch_frequency = %s, report_type = %s, sch_reportVolume = %s, sch_status = %s "
                        . "WHERE sch_id = %d", 
                        $_POST['sh-how-often'], $_POST['report_type'], $_POST['sh-volume'], $sch_status, $settingId
                )
            );
                            
            if ($queryStatus === false) {
                $popupErrMsg = 'Invalid Query! Please try again';

            } else {
                $popupSucsMsg = 'Email scheduler is successfully updated';
                unset($_POST);

            }
        }
        
         //Finally process emails:
        if (!empty($settingId)) {
            $queryStatus2 = array();
            foreach ($shEmails as $em) {
                if (empty($em['id'])) {
                    $queryStatus2[] = $wpdb->insert(
                        "{$wpdb->prefix}mcc_sch_emails",
						array(
							'em_sch_id' => $settingId,
							'em_emailTo' => $em['to'],
							'em_status' => $em['st']
                        ),
						array('%d', '%s', '%d')
                    );

                } else {
                    if (empty($em['to'])) {
                        $queryStatus2[] = $wpdb->delete(
                        	"{$wpdb->prefix}mcc_sch_emails",
							array('em_id' => $em['id']),
							array('%d')
                        );

                    } else {
                        $queryStatus2[] = $wpdb->update(
                            "{$wpdb->prefix}mcc_sch_emails",
							array(
								//'em_sch_id'		=> $settingId,
								'em_emailTo' => $em['to'],
								'em_status' => $em['st']
                            ),
							array('em_id' => $em['id']),
							array('%s', '%d'),
							array('%d')
                        );
						
                    }
                }
            }			
        }
        
        
    }
}

function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

$from_date = date('Y-m-d', time() - 31 * 24 * 3600);
$to_date = date('Y-m-d', time() - 2 * 24 * 3600);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['analytics_date_frm_btn'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));
    if (isset($_POST['min_organic_visitors'])) {
        $min_organic_visitors = $_POST['min_organic_visitors'];
    }
}
echo $styl;
?>

<div class="dateformdiv hidden">
    <form id="analytics_Frm" action="" method="post" class="form-inline">        
        <label for="from" class="control-label"><b>From</b></label>
            <input type="text" name="from_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($from_date)); ?>">
            <label for="to"><b>To</b></label>
            <input type="text" name="to_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($to_date)); ?>">
        <input type="submit" class="btn btn-success"  style="background:none;" name="analytics_date_frm_btn" value="Submit">

    </form>
</div>

<?php    

foreach ($locations as $location) {

    $location_id = $location->id;    
    $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
    $client_website = $website = get_user_meta($UserID, 'website', TRUE);
       
    $user_website = preg_replace('#^https?://#', '', $client_website);
    $user_website = trim($user_website,"/");
    $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);    
    $Content_keyword_Site = get_user_meta($user_id, "Content_keyword_Site", true);
    $Synonyms_keyword_arr = $Content_keyword_Site['Synonyms_keyword'];
    $activation = $Content_keyword_Site['activation'];
    $landing_page_count = 0;
    $landing_page_url_arr = array();
    $RankingURL_arr = array();
    $rank_arr = array();
    $all_url = array();
    $keyword_landing_arr = array();
    $match_ranking_url = array();
    
    if (!empty($Content_keyword_Site)) {
        foreach ($Content_keyword_Site['landing_page'] as $landing_index => $landing_page) {
            if ($Content_keyword_Site['activation'][$landing_index] != 'inactive') {
                $landing_page_url = $landing_page[0];
                if (trim($landing_page_url) != '' && !in_array($landing_page_url, $landing_page_arr)) {
                    $landing_page_count++;

                    $landing_page_url_arr[] = rtrim(str_replace(array('http://', 'https://', 'www.'), "", $landing_page_url), '/\\');
                    $cal_index = $landing_index + 1;
                    $keyword_landing_arr[strtolower($Content_keyword_Site['LE_Repu_Keyword_' . $cal_index])] = $landing_page_url;
                }
            }
        }
    }
    $total_target_url = count($keyword_landing_arr);
    $total_rank_count = 0;
    $total_keywords_count = 0;
    
    ?>
    <div class="reportdiv">        
        <h5>Target Vs Ranking Report - <?php echo $brand. ' ( '.$website.' )'; ?>
            <div class="pull-right locreport"><a href="?parm=execution&function=rank_target_report&location_id=<?php echo $location_id; ?>" target="_blank" class="btn btn-primary ">Location Full Report</a></div>
        </h5>        
               
        <div class="page-container">
                <?php
                        $all_active_keywords = rd_all_active_keywords($user_id);                        
                        ?>

                        <table class="tabl1 table table-striped table-bordered table-hover primary-tbl respTbl" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="26%">Keyword</th>
                                    <th width="32%">Target URL</th>
                                    <th width="32%">Ranking URL</th>
                                    <th width="5%">Rank</th>
                                    <th width="5%">Match</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($all_active_keywords)) {
                                    foreach ($all_active_keywords as $row_key) {
                                        $sql = 'SELECT Keyword, RankingURL, CurrentRank FROM `seo` '
                                                . 'WHERE `MCCUserId` = ' . $user_id . ' and Keyword LIKE "' . $row_key . '"';
                                        $result = row_array($sql);
                                        ?>
                                        <tr>
                                            <td><span style="background:#22B04B; margin-right:6px;" class="badge">P</span><?php echo $result['Keyword'] != '' ? $result['Keyword'] : $row_key; ?></td>
                                            <td class="urlcls">
                                                    <?php 
                                                        $landing_page_url = $keyword_landing_arr[$row_key];
                                                        $landing_pageshow = explode($user_website,$landing_page_url);
                                                        $landing_pageshow = isset($landing_pageshow[1])?$landing_pageshow[1]:$landing_pageshow[0];
                                                    ?>
                                                    <a target="_blank" href='<?php echo addhttp($landing_page_url); ?>'><?php echo $landing_pageshow; ?></a>
                                                </td>
                                                <td class="urlcls">
                                                    <?php 
                                                        $RankingURL = $result['RankingURL'];                                                                       
                                                        $RankingURLshow = explode($user_website,$RankingURL);
                                                        $RankingURLshow = isset($RankingURLshow[1])?$RankingURLshow[1]:$RankingURLshow[0];
                                                    ?>
                                                    <a target="_blank" href='<?php echo addhttp($RankingURL); ?>'><?php echo $RankingURLshow; ?></a>

                                                </td>
                                            <td class="text-center">
                                                <?php
                                                if ($result['CurrentRank'] > 0) {
                                                    echo $result['CurrentRank'];
                                                } else {
                                                    echo '50+';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $landing_page_url = str_replace(array('http://', 'https://', 'www.'), "", $keyword_landing_arr[$row_key]);
                                                $RankingURL = str_replace(array('http://', 'https://', 'www.'), "", $RankingURL);
                                                if ($RankingURL != "" && $RankingURL == $landing_page_url) {
                                                    echo '<span style="color:green">Yes</span>';
                                                } else {
                                                    echo '<span style="color:red">No</span>';
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
    
</div>
        
    </div>
 

    <?php    
}

?>
<script type="text/javascript">
    
jQuery(document).ready(function() {
    jQuery('.respTbl').dataTable({
        "order": [[0, "asc"]],
        "iDisplayLength": 10

    });            
});
 
</script>
<?php include_once 'download_report.php'; ?>