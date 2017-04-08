<style type="text/css">
    tbody tr td{padding: 10px 2px;}
    thead th, tbody td{
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        border-color: #cdcdcd;
        border-image: none;
        border-style: solid;
        border-width: 0px 1px 1px 0px;
        border-bottom: none;
    }
    .dataTables_info{display: block!important;}
    table.dataTable.display tbody tr td{background-color:#fff;text-indent: 5px;}
    table.dataTable.display tbody tr.even td{background-color:#eee !important}
    table.dataTable td span{display:inline-block;padding:7px 10px;border-radius:20px; color:#fff}

    .topPart{width:100%; padding:20px 0 10px; overflow:hidden}
    .addMore{padding:3px 6px; border-radius:6px; font-weight:bold; font-size:12px; color:#fff; background:#fb6800; margin-top:4px; display:inline-block; cursor:pointer}
    .addMore:hover{color:#fff; background-color:#e14e00}
    #dwnldContRprt{float:right}
    .errMsg, .sucsMsg{color:red; border:1px solid cyan; padding:10px; background:#ffe4c4}
    .sucsMsg{color:green; background-color:#d5f15c;}
    #mult_email_div p{padding-bottom:2px}
</style>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/css/theme.blue.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.widgets.min.js"></script>

<?php

include_once get_template_directory() . '/analytics/my_functions.php';
include_once get_template_directory() . '/common/report-function.php';
include_once get_template_directory() . '/common/schedule-report.php';
include_once SET_COUNT_PLUGIN_DIR . '/custom_functions.php';
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';
$db_report_name = ST_TRAFFIC_REPORT;

if (isset($_POST['btn_download-report'])) {
                
    ob_end_clean();    
    $fromdate = $_POST['fromdate'];
    $todate = $_POST['todate'];
    $fromdate = date('Y-m-d', strtotime($fromdate));
    $todate = date('Y-m-d', strtotime($todate));
    
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
        $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' '. ucfirst($rank_type) . ' Traffic Report</h3><br/>';  
        $ht = 70;                     
        $str .= '<div class="content">';
        
        foreach ($locations as $location) {
            
            $ht = $ht + 70;
            $location_id = $location->id;
            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;            
            $website = $client_website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
            $page_name = 'traffic-report';
            $min_organic_visitors = 5; //by default;                     
            
            $has_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'"));
            
            $str .= '<fieldset class="location">
                <div><h4>Location : '.$brand.' ( '.$website.' )</h4></div>';
            
            if ($has_table == 1) {

                $iddiv = "tbl_" . $location->id;
                $arrids[] = $iddiv;

                $sql = "SELECT count(*) as total_row, 
                    sum(`Total`) as total_visit,
                    sum(`BounceRate`) as total_BounceRate,
                    sum(`TimeOnSite`) as total_timeonsite,
                    sum(`email`) as total_email,
                    sum(`organic`) as total_organic,
                    sum(`cpc`) as total_cpc,
                    sum(`referral`) as total_referral,
                    sum(`social`) as total_social,
                    sum(`(none)`) as total_direct
                    FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate'";

                $visit_date_url = row_array($sql);                
        

                $tos = $tos_avg_url[0][TOS_val] / $total_traffic_url[0][Total_val];

                $tos_avg = gmdate("i:s", round($tos, 0));


                $sql7 = "SELECT url FROM primary_keywords WHERE `url_type` = 'P' AND `client_id` = '$CurClientID' order by `url` desc";
                $lps = result_array($sql7);

                $countlps = 0;
                foreach ($lps as $lp) {
                    $countlps++;

                    $lp_url = $lp[url];

                    $lp_url = preg_replace('#^https?://#', '', rtrim($lp_url, '/'));

                    if ($countlps == '1') {
                        $sqlurls .= " and (`PageURL` = '$lp_url' or `PageURL` = '$lp_url/'";
                    } else {
                        $sqlurls .= " or `PageURL` = '$lp_url'  or `PageURL` = '$lp_url/'";
                    }
                }
 
                $sql8 = "SELECT sum(`Total`) as Total_val,sum(`Organic`) as Organic_val,sum(`TimeOnSite`) as TOS_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate' $sqlurls) order by `DateOfVisit` desc";        
                $lp_traffic_url = result_array($sql8);
                $tos_lps = $lp_traffic_url[0][TOS_val] / $lp_traffic_url[0][Total_val];        
                $tos_lps = gmdate("i:s", round($tos_lps, 0));
                
                $totalvisits = !empty($visit_date_url['total_visit'])?$visit_date_url['total_visit']:0;
                $totalorganic = !empty($visit_date_url['total_organic'])?$visit_date_url['total_organic']:0;
                $totaldirect = !empty($visit_date_url['total_direct'])?$visit_date_url['total_direct']:0;
                
                $Total_val = !empty($lp_traffic_url[0]['Total_val'])?$lp_traffic_url[0]['Total_val']:0;
                $Organic_val = !empty($lp_traffic_url[0]['Organic_val'])?$lp_traffic_url[0]['Organic_val']:0;
                
                $str .= '<div class="tbl" > 
                                
                                    <div class="tline">Total Visits</div>
                                    <div class="text-right"><b>'.$totalvisits.'</b></div>
                                
                                
                                    <div class="tline">Organic Visits</div>
                                    <div class="text-right"><b>'.$totalorganic.'</b></div>
                                
                                
                                    <div class="tline">Direct Visits</div>
                                    <div class="text-right"><b>'.$totaldirect.'</b></div>
                                
                                
                            </div>
                                
                               <div class="tbl">
                                    
                                        <div class="tline"># of all LPs</div>
                                        <div class="text-right"><b>'.$countlps.'</b></div>
                                    
                                    
                                        <div class="tline">Total Visits LPs</div>
                                        <div class="text-right"><b>'.$Total_val.'</b></div>
                                    
                                    
                                        <div class="tline">Organic Visits LPs</div>
                                        <div class="text-right"><b>'.$Organic_val.'</b></div>
                                    
                                  
                                </div>                              

                                <div class="tbl">
                                    
                                        <div class="tline">Average Bounce Rate</div>
                                        <div class="text-right"><b>'.PerSentFormat2(($visit_date_url['total_BounceRate'] / $visit_date_url['total_visit']) * 100).'</b></div>
                                    
                                    
                                        <div class="tline">Average TOS</div>
                                        <div class="text-right"><b>'.SecondsToMinSec2($visit_date_url['total_timeonsite'] / $visit_date_url['total_visit']).'</b></div>
                                    
                                    
                                        <div class="tline">Average TOS LPs</div>
                                        <div class="text-right"><b>'.$tos_lps.'</b></div>
                                    
                                    
                                </div>';
                
                $str .='<div style="clear:both;height:15px;"></div>';
                $sql = "SELECT PageURL,`Keyword`,`CurrentRank`,sum(organic) as organic_val,sum(social) as social_val,sum(`referral`) as referral_val, sum(`(none)`) as direct_val, sum(`cpc`) as cpc_val, sum(`Total`) as Total_val, sum(`TimeOnSite`) as TOS_val, sum(`BounceRate`) as bounce_rate_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate' and PageURL != '' group by PageURL order by `DateOfVisit` desc LIMIT ".ST_MAX_TRAFFIC_REC_TO_SHOW;
                $all_traffic_url = result_array($sql);
                         
                $str .= '<table border="1" cellspacing="0" class="c2" style="position: relative; top: 15px; border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 1500px;">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Keyword</th>
                                        <th>Rank</th>
                                        <th>organic</th>
                                        <th>social</th>
                                        <th>referral</th>
                                        <th>(direct)</th>
                                        <th>cpc</th>
                                        <th>Total</th>
                                        <th>TOS</th>
                                        <th>Bounce Rate</th>
                                    </tr>                                
                                </thead>

                                <tbody>';
      
        foreach ($all_traffic_url as $indx => $row_table_data) {
            if ($row_table_data['organic_val'] >= $min_organic_visitors) {

                $q = "SELECT url_type FROM primary_keywords WHERE instr(url, '{$row_table_data['PageURL']}') > 0 or instr('{$row_table_data['PageURL']}', url) > 0";
                $seo = row_array($q);

                $URLType = $seo['url_type'];
                $csvRow = array();
                $rowBg = $indx % 2 == 0 ? 'even' : 'odd';
                
                $str .= '<tr class="'.$rowBg.'">
                                                <td>';
                                            
                                            $remove_domain = explode(".", $row_table_data['PageURL']);
                                            unset($remove_domain[0]);
                                            unset($remove_domain[1]);
                                            $remove_domain = explode("/", implode("", $remove_domain));
                                            unset($remove_domain[0]);
                                            $URL = '/' . implode("/", $remove_domain);

                                            $TD = array();
                                            if ($URLType != "" && $row_table_data['PageURL'] != "" && $URL != "/") {
                                                $types = explode(",", $URLType);

                                                foreach ($types as $type) {
                                                    switch ($type) {
                                                        case "P":
                                                            $str .= "<span class='badge' style='background:green;'>T</span>";
                                                            $TD[] = 'T';
                                                            break;

                                                        case "B":
                                                            $str .= "<span class='badge' style='background:yellow'>B</span>";
                                                            $TD[] = 'B';
                                                            break;

                                                        case "R":
                                                            $str .= "<span class='badge' style='background:orange'>R</span>";
                                                            $TD[] = 'R';
                                                            break;

                                                        case "C":
                                                            $str .= "<span class='badge' style='background:blue'>SP</span>";
                                                            $TD[] = 'SP';
                                                            break;

                                                        case "O":
                                                            $str .= "<span class='badge' style='background:gray'>NT</span>";
                                                            $TD[] = 'NT';
                                                            break;
                                                    }
                                                }
                                            }
                                           
                                                 $str .=   '<a title="'.$row_table_data['PageURL'].'" target="_blank" href="'.site_url().'/url-profile/?url='.$row_table_data['PageURL'].'">';
                                                                                                        
                                                    $str .= $URL;                                                    
                                                    
                                                    $curan_rank = $row_table_data['CurrentRank'] != 0 ? $row_table_data['CurrentRank']:'';
                                                    
                                                  $str .= '
                                                    </a>
                                                </td>
                                                <td>'.$row_table_data['Keyword'].'</td>
                                                <td>'.$curan_rank.'</td>
                                                <td>'.$row_table_data['organic_val'].'</td>
                                                <td> '.$row_table_data['social_val'].'
                                     </td>
                                                <td> '. $row_table_data['referral_val'].'
                                     </td>
                                                <td> '.$row_table_data['direct_val'].'
                                     </td>
                                                <td> '.$row_table_data['cpc_val'].'
                                     </td>
                                                <td> '. $row_table_data['Total_val'].'
                                         </td>
                                                <td> '. SecondsToMinSec2($row_table_data['TOS_val'] / $row_table_data['Total_val']).'
                                         </td>
                                                <td>
                                                     '.PerSentFormat2(($row_table_data['bounce_rate_val'] / $row_table_data['Total_val']) * 100).'   
                                                </td>
                                            </tr>';
                          
                            }
                            
                        }
        
                        $str .= '</tbody>
                        </table>';
                
            }
            else{
                 $str .= '<div class="row">No Traffic Data Found</div>';
            }                        
            $str .='<div style="clear:both;height:20px;"></div></fieldset>';
            
        }
        $str .= '</div>';
        
        $widpdf = 1250;
        require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($str);        
        $customPaper = array(0,0,$widpdf,$ht);
        $dompdf->set_paper($customPaper);        
        $dompdf->render();
        $user_id = $UserID;

        include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
        $pdf = $dompdf->output();        
        $dompdf->stream("traffic_report.pdf", array("Attachment" => true));
        exit;
        
        
    }
    else if ($_POST['dwnld_type'] == 'csv') {  
        
        $FilePath =  ucfirst($rank_type) . "traffic_report.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename=' . $FilePath);    
        ob_clean();
        $fp = fopen('php://output', "w");     
        
        $headertop = array('','','Total Visits','Organic Visits','Direct Visits','# of all LPs','Total Visits LPs','Organic Visits LPs','Average Bounce Rate','Average TOS','Average TOS LPs','','');
        $headertbl = array('','','URL','Keyword','Rank','organic','social','referral','(direct)','cpc','Total','TOS','Bounce Rate');
        $headerempty = array('','','','','','','','','','','','','');
        foreach ($locations as $location) {
            $location_id = $location->id;
            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;            
            $website = $client_website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
            $page_name = 'traffic-report';
            $min_organic_visitors = 5; //by default;                     
            
            $has_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'"));
            $headerlocname = array('Location',$brand.' ('.$website.')','','','','','','','','','','','');
            
            fputcsv($fp, $headerlocname);
            fputcsv($fp, $headerempty);
            
            
            if ($has_table == 1) {
                
                fputcsv($fp, $headertop);
                
                $sql = "SELECT count(*) as total_row, 
                    sum(`Total`) as total_visit,
                    sum(`BounceRate`) as total_BounceRate,
                    sum(`TimeOnSite`) as total_timeonsite,
                    sum(`email`) as total_email,
                    sum(`organic`) as total_organic,
                    sum(`cpc`) as total_cpc,
                    sum(`referral`) as total_referral,
                    sum(`social`) as total_social,
                    sum(`(none)`) as total_direct
                    FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate'";

                $visit_date_url = row_array($sql);                
        

                $tos = $tos_avg_url[0][TOS_val] / $total_traffic_url[0][Total_val];

                $tos_avg = gmdate("i:s", round($tos, 0));


                $sql7 = "SELECT url FROM primary_keywords WHERE `url_type` = 'P' AND `client_id` = '$CurClientID' order by `url` desc";
                $lps = result_array($sql7);

                $countlps = 0;
                foreach ($lps as $lp) {
                    $countlps++;

                    $lp_url = $lp[url];

                    $lp_url = preg_replace('#^https?://#', '', rtrim($lp_url, '/'));

                    if ($countlps == '1') {
                        $sqlurls .= " and (`PageURL` = '$lp_url' or `PageURL` = '$lp_url/'";
                    } else {
                        $sqlurls .= " or `PageURL` = '$lp_url'  or `PageURL` = '$lp_url/'";
                    }
                }
 
                $sql8 = "SELECT sum(`Total`) as Total_val,sum(`Organic`) as Organic_val,sum(`TimeOnSite`) as TOS_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate' $sqlurls) order by `DateOfVisit` desc";        
                $lp_traffic_url = result_array($sql8);
                $tos_lps = $lp_traffic_url[0][TOS_val] / $lp_traffic_url[0][Total_val];        
                $tos_lps = gmdate("i:s", round($tos_lps, 0));
                
                $totalvisits = !empty($visit_date_url['total_visit'])?$visit_date_url['total_visit']:0;
                $totalorganic = !empty($visit_date_url['total_organic'])?$visit_date_url['total_organic']:0;
                $totaldirect = !empty($visit_date_url['total_direct'])?$visit_date_url['total_direct']:0;
                
                $Total_val = !empty($lp_traffic_url[0]['Total_val'])?$lp_traffic_url[0]['Total_val']:0;
                $Organic_val = !empty($lp_traffic_url[0]['Organic_val'])?$lp_traffic_url[0]['Organic_val']:0;
                
                $arr_topvals = array('','',$totalvisits,$totalorganic,$totaldirect,$countlps,$Total_val,
                    $Organic_val,PerSentFormat2(($visit_date_url['total_BounceRate'] / $visit_date_url['total_visit']) * 100),
                    SecondsToMinSec2($visit_date_url['total_timeonsite'] / $visit_date_url['total_visit']),$tos_lps);
                
                fputcsv($fp, $arr_topvals);
                fputcsv($fp, $headerempty);
                            
                fputcsv($fp, $headertbl);
                
                $sql = "SELECT PageURL,`Keyword`,`CurrentRank`,sum(organic) as organic_val,sum(social) as social_val,sum(`referral`) as referral_val, sum(`(none)`) as direct_val, sum(`cpc`) as cpc_val, sum(`Total`) as Total_val, sum(`TimeOnSite`) as TOS_val, sum(`BounceRate`) as bounce_rate_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate' and PageURL != '' group by PageURL order by `DateOfVisit` LIMIT ".ST_MAX_TRAFFIC_REC_TO_SHOW;
                $all_traffic_url = result_array($sql);
                    
                
                foreach ($all_traffic_url as $indx => $row_table_data) {
                    $arrtblvals = array();
                    if ($row_table_data['organic_val'] >= $min_organic_visitors) {

                        $q = "SELECT url_type FROM primary_keywords WHERE instr(url, '{$row_table_data['PageURL']}') > 0 or instr('{$row_table_data['PageURL']}', url) > 0";
                        $seo = row_array($q);

                        $URLType = $seo['url_type'];
                        $csvRow = array();
                                                
                        $URL = $row_table_data['PageURL'];                        
                        $curan_rank = $row_table_data['CurrentRank'] != 0 ? $row_table_data['CurrentRank'] : '';
                        
                        $arrtblvals = array('','',$URL,$row_table_data['Keyword'],$curan_rank,$row_table_data['organic_val'],$row_table_data['social_val'],
                            $row_table_data['referral_val'],$row_table_data['direct_val'],$row_table_data['cpc_val'],$row_table_data['Total_val'],
                            SecondsToMinSec2($row_table_data['TOS_val'] / $row_table_data['Total_val']),PerSentFormat2(($row_table_data['bounce_rate_val'] / $row_table_data['Total_val']) * 100));
                        fputcsv($fp, $arrtblvals);                      
                    }
                }
            }
            else{
                $arrempty = array('','','No Traffic Data Found','','','','','','','','','','');
                fputcsv($fp, $arrempty);
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
			//pr($queryStatus2,'======$queryStatus2====='); die();
        }
        
        
    }
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


?>

    <div class="dateformdiv">
        <form id="analytics_Frm" action="" method="post" class="form-inline pull-right">               
            <label for="from" class="control-label"><b>From</b></label>
            <input type="text" name="from_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($from_date)); ?>">
            <label for="to"><b>To</b></label>
            <input type="text" name="to_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($to_date)); ?>">
            <input type="submit" class="btn btn-success"  style="background:none;" name="analytics_date_frm_btn" value="Submit">

        </form>
    </div>
<?php
$arrids = array();
foreach ($locations as $location) {
    
    $location_id = $location->id;
    $CurClientID = $analytics_user_id = $UserID = $user_id = $location->MCCUserId;
    $website = get_user_meta($UserID, 'website', TRUE);
    $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
    $page_name = 'traffic-report';
    $min_organic_visitors = 5; //by default;
    ?>
    <div class="reportdiv">
        <h5>Traffic Report - <?php echo $brand.' ( '. $website.' )'; ?>
            <div class="pull-right locreport"><a href="?parm=execution&function=location_traffic_report&location_id=<?php echo $location_id; ?>" target="_blank" class="btn btn-primary ">Location Full Report</a></div>
        </h5>
        <div class="commnerpoert traffic_report<?php echo $UserID; ?>">
            <div class="portlet light">
                <?php                                
                $has_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'"));
                if ( $has_table == 1 ) {
                    
                $iddiv = "tbl_".$location->id;
                $arrids[] = $iddiv;
                    
            $sql = "SELECT count(*) as total_row, 
                    sum(`Total`) as total_visit,
                    sum(`BounceRate`) as total_BounceRate,
                    sum(`TimeOnSite`) as total_timeonsite,
                    sum(`email`) as total_email,
                    sum(`organic`) as total_organic,
                    sum(`cpc`) as total_cpc,
                    sum(`referral`) as total_referral,
                    sum(`social`) as total_social,
                    sum(`(none)`) as total_direct
                    FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$from_date' and `DateOfVisit` <= '$to_date'";
        
        $visit_date_url = row_array($sql);                
        

        $tos = $tos_avg_url[0][TOS_val] / $total_traffic_url[0][Total_val];

        $tos_avg = gmdate("i:s", round($tos, 0));


        $sql7 = "SELECT url FROM primary_keywords WHERE `url_type` = 'P' AND `client_id` = '$CurClientID' order by `url` desc";
        $lps = result_array($sql7);
        
        $countlps = 0;
        foreach ($lps as $lp) {
            $countlps++;

            $lp_url = $lp[url];

            $lp_url = preg_replace('#^https?://#', '', rtrim($lp_url, '/'));

            if ($countlps == '1') {
                $sqlurls .= " and (`PageURL` = '$lp_url' or `PageURL` = '$lp_url/'";
            } else {
                $sqlurls .= " or `PageURL` = '$lp_url'  or `PageURL` = '$lp_url/'";
            }
        }
        
        $sql8 = "SELECT sum(`Total`) as Total_val,sum(`Organic`) as Organic_val,sum(`TimeOnSite`) as TOS_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$from_date' and `DateOfVisit` <= '$to_date' $sqlurls) order by `DateOfVisit` desc";        
        $lp_traffic_url = result_array($sql8);
        $tos_lps = $lp_traffic_url[0][TOS_val] / $lp_traffic_url[0][Total_val];        
        $tos_lps = gmdate("i:s", round($tos_lps, 0));
        ?>
                   
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="col-md-12" style="margin-bootom: 10px; border:2px solid #F1F4F7; padding:0;">
                                        <table class="table table-hover table-light">
                                            <tr>
                                                <td>Total Visits</td>
                                                <td class="text-right"><b><?php echo !empty($visit_date_url['total_visit'])?$visit_date_url['total_visit']:0; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Organic Visits</td>
                                                <td class="text-right"><b><?php echo !empty($visit_date_url['total_organic'])?$visit_date_url['total_organic']:0; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Direct Visits</td>
                                                <td class="text-right"><b><?php echo !empty($visit_date_url['total_direct'])?$visit_date_url['total_direct']:0; ?></b></td>
                                            </tr>
                                        </table>

                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="col-md-12" style="margin-bootom: 5%; border:2px solid #F1F4F7; padding:0;">
                                        <table class="table table-hover table-light">
                                            <tr>
                                                <td># of all LPs</td>
                                                <td class="text-right"><b><?php echo $countlps; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Total Visits LPs</td>
                                                <td class="text-right"><b><?php echo !empty($lp_traffic_url[0]['Total_val'])?$lp_traffic_url[0]['Total_val']:0; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Organic Visits LPs</td>
                                                <td class="text-right"><b><?php echo !empty($lp_traffic_url[0]['Organic_val'])?$lp_traffic_url[0]['Organic_val']:0; ?></b></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>                                

                                <div class="col-md-4">
                                    <div class="col-md-12" style="border:2px solid #F1F4F7; padding:0;">
                                        <table class="table table-hover table-light">
                                            <tr>
                                                <td>Average Bounce Rate</td>
                                                <td class="text-right"><b><?php echo PerSentFormat2(($visit_date_url['total_BounceRate'] / $visit_date_url['total_visit']) * 100); ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Average TOS</td>
                                                <td class="text-right"><b><?php echo SecondsToMinSec2($visit_date_url['total_timeonsite'] / $visit_date_url['total_visit']); ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Average TOS LPs</td>
                                                <td class="text-right"><b><?php echo $tos_lps; ?></b></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>   

                            </div>                            
                        </div>
                    </div>    

        

                    <div class="clearfix margin-bottom-15"> </div>

                    <?php
                    
                    $sql = "SELECT PageURL,`Keyword`,`CurrentRank`,sum(organic) as organic_val,sum(social) as social_val,sum(`referral`) as referral_val, sum(`(none)`) as direct_val, sum(`cpc`) as cpc_val, sum(`Total`) as Total_val, sum(`TimeOnSite`) as TOS_val, sum(`BounceRate`) as bounce_rate_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$from_date' and `DateOfVisit` <= '$to_date' and PageURL != '' group by PageURL order by `DateOfVisit` desc LIMIT ".ST_MAX_TRAFFIC_REC_TO_SHOW;
                    $all_traffic_url = result_array($sql);
                    
                    ?>

                    <div class="row">
                        <div class="col-md-12 key_table" id="<?php echo $iddiv; ?>">
                            <table class="tabl2 table table-striped table-bordered table-hover tabl-sort" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:35%">URL</th>
                                        <th>Keyword</th>
                                        <th>Rank</th>
                                        <th>organic</th>
                                        <th>social</th>
                                        <th>referral</th>
                                        <th>(direct)</th>
                                        <th>cpc</th>
                                        <th>Total</th>
                                        <th>TOS</th>
                                        <th>Bounce Rate</th>
                                    </tr>
                                <?php
                                if ($dwnldType == 'csv')
                                    $csvArr[] = array('URL', 'Keyword', 'Rank', 'organic', 'social', 'referral', '(direct)', 'cpc', 'Total', 'TOS', 'Bounce Rate');
                                ?>
                                </thead>

                                <tbody>
        <?php
        foreach ($all_traffic_url as $indx => $row_table_data) {
            if ($row_table_data['organic_val'] >= $min_organic_visitors) {

                $q = "SELECT url_type FROM primary_keywords WHERE instr(url, '{$row_table_data['PageURL']}') > 0 or instr('{$row_table_data['PageURL']}', url) > 0";
                $seo = row_array($q);

                $URLType = $seo['url_type'];
                $csvRow = array();
                $rowBg = $indx % 2 == 0 ? 'even' : 'odd';
                ?>
                                            <tr class="<?php echo $rowBg; ?>">
                                                <td style="width:35%;border-left:0px solid #cdcdcd;">
                                            <?php
                                            $remove_domain = explode(".", $row_table_data['PageURL']);
                                            unset($remove_domain[0]);
                                            unset($remove_domain[1]);
                                            $remove_domain = explode("/", implode("", $remove_domain));
                                            unset($remove_domain[0]);
                                            $URL = '/' . implode("/", $remove_domain);

                                            $TD = array();
                                            if ($URLType != "" && $row_table_data['PageURL'] != "" && $URL != "/") {
                                                $types = explode(",", $URLType);

                                                foreach ($types as $type) {
                                                    switch ($type) {
                                                        case "P":
                                                            echo "<span class='badge' style='background:green;'>T</span>";
                                                            $TD[] = 'T';
                                                            break;

                                                        case "B":
                                                            echo "<span class='badge' style='background:yellow'>B</span>";
                                                            $TD[] = 'B';
                                                            break;

                                                        case "R":
                                                            echo "<span class='badge' style='background:orange'>R</span>";
                                                            $TD[] = 'R';
                                                            break;

                                                        case "C":
                                                            echo "<span class='badge' style='background:blue'>SP</span>";
                                                            $TD[] = 'SP';
                                                            break;

                                                        case "O":
                                                            echo "<span class='badge' style='background:gray'>NT</span>";
                                                            $TD[] = 'NT';
                                                            break;
                                                    }
                                                }
                                            }
                                            ?>
                                                    <a title="<?php echo $row_table_data['PageURL']; ?>" target="_blank" href="<?php echo site_url(); ?>/url-profile/?url=<?php echo $row_table_data['PageURL']; ?>">
                                                    <?php
                                                    //echo $row_table_data[$row_header];
                                                    echo $URL;
                                                    //echo str_replace(array("http://" . $client_website, "https://" . $client_website, $client_website), "", $row_table_data[$row_header]);
                                                    
                                                    ?>
                                                    </a>
                                                </td>
                                                <td><?php echo $TD = $row_table_data['Keyword'];  ?></td>
                                                <td><?php echo $TD = $row_table_data['CurrentRank'] != 0 ? $row_table_data['CurrentRank'] : '';
                                     ?></td>
                                                <td><?php echo $TD = $row_table_data['organic_val'];
                                     ?></td>
                                                <td><?php echo $TD = $row_table_data['social_val'];
                                     ?></td>
                                                <td><?php echo $TD = $row_table_data['referral_val'];
                                     ?></td>
                                                <td><?php echo $TD = $row_table_data['direct_val'];
                                     ?></td>
                                                <td><?php echo $TD = $row_table_data['cpc_val'];
                                     ?></td>
                                                <td><?php echo $TD = $row_table_data['Total_val'];
                                         ?></td>
                                                <td><?php echo $TD = SecondsToMinSec2($row_table_data['TOS_val'] / $row_table_data['Total_val']);
                                         ?></td>
                                                <td>
                                                        <?php
                                                        //echo round($row_table_data['bounce_rate_val'],2).'%';
                                                        echo $TD = PerSentFormat2(($row_table_data['bounce_rate_val'] / $row_table_data['Total_val']) * 100);
                                                        
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

                        <?php } else { ?>

                    <div class="alert alert-danger">No Traffic Data Found Yet.</div>

                        <?php } ?>

            </div>
        </div>
    </div>
                        <?php
                    }
                    ?>


<?php $arrids = implode(",",$arrids); ?>
<script>
jQuery(document).ready(function() {	
               
    jQuery('.tabl2').dataTable({
        "order": [[3, "asc"]],
        "iDisplayLength": 25
    }); 

    var ids = "<?php echo $arrids; ?>";
    var ids = ids.split(",");
    for(flg = 0; flg < ids.length; flg++){       
        DoubleScroll(document.getElementById(ids[flg]));
    }

});

</script>
<?php include_once 'download_report.php'; ?>