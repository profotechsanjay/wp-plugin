<?php

include_once get_template_directory() . '/common/report-function.php';
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';

$db_report_name = ST_ADMIN_EXE_REPORT;

//$res = user_limited_keywords(1180, $start, $limit);
//pr($res);
//
//function user_limited_keywords($user_id, $start, $limit) {
//    $active_key = array();
//    $all_keyword_list = get_user_meta($user_id, "Content_keyword_Site", true);
//    if (!empty($all_keyword_list)) {
//        for ($i = 1; $i <= $all_keyword_list['keyword_count']; $i++) {
//            if (trim($all_keyword_list['LE_Repu_Keyword_' . $i]) != "") {
//                if ($all_keyword_list['activation'][$i - 1] != 'inactive') {
//                    $active_key[] = trim($all_keyword_list['LE_Repu_Keyword_' . $i]);
//                }
//            }
//        }
//    }
//    return $active_key;
//}

if (isset($_POST['btn_download-report'])) {
        
    ob_end_clean();
    $header_key_report = array('Account Name', 'URL', 'Keywords','','', 'Rank %','','','', 'Avg Rank','','','', 'Rank/Target','','','', '1st Place','','','', 'Top 3','','','', 'Top 10','');
    $header_lower = array('', '', '','','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year');
    $header_lower1 = array('Active Campaigns', '', '','','', '','','','', '','','','', '','','','', '','','','', '','','','', '','');
    $header_empty = array('', '', '','','', '','','','', '','','','', '','','','', '','','','', '','','','', '','');
    if ($_POST['dwnld_type'] == 'pdf') {
                
        $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                .keyword_width{width:20%;}
                .ranking_width{width:33%;}
                </style>';
        $str .= rd_pdf_header();
        $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Executive Summary Report</h3><br/>';
        $str .='<table cellspacing="0" class="c2" style="margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 1400px; border: 1px solid #cecece;">';
        $str .='<tr style="background-color:#EBEBEB;">';
        
        $arnotinclude = array('URL','Keywords');
        
        foreach ($header_key_report as $row_head) {
                if($row_head != ''){
                                       
                    if($row_head == 'Account Name'){
                        $str .='<th class="padding_full">' . $row_head;
                        $str .='<div style="margin-top:10px; font-size: 12px; ">(Active Campaigns)</div>';
                    }
                    else if(!in_array($row_head, $arnotinclude)){
                        $str .='<th class="padding_full" style="width: 200px">' . $row_head;
                        $newstr = '<table style="margin-top:10px; font-size:13px; width:100%;" ><tr style="background-color:#EBEBEB;">'
                                . '<th>90 Day</th><th>180 Day</th><th>1 Year</th></tr></table>';
                        $str .='<div>'.$newstr.'</div>';
                    }
                    else{
                        $str .='<th class="padding_full">' . $row_head;
                        $str .='<div style="margin-top:10px; ">&nbsp;</div>';
                    }
                    $str .='</th>';
                }
        }
        $str .='</tr>';
        
        $ht = 70;
        foreach ($locations as $location) {
            $ht = $ht + 70;
            $location_id = $location->id;
            $user_id = $UserID = $location->MCCUserId;        
            $website = get_user_meta($UserID, 'website', TRUE);    
            $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);    
            $totalkeywords =  countlocation_keywords($UserID);
            if($totalkeywords == 'N/A'){
                $totalkeywords = 'NA';
            }
            $days90data = 20;
            $all_active_target_url = all_active_target_url($user_id, $remove_http = 1);

            $today = date("Y-m-d");
            $current_days_data = array();
            $current_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $today, 1);            
            
            // site audit
            $site_audit_info = rd_site_audit_info($user_id);
            $site_audit_score = '0.0%';
            $last_site_audit_run = 'Yet not run';
            if (!empty($site_audit_info)) {
                $site_audit_result = unserialize($site_audit_info->all_info);
                $site_audit_score = $site_audit_result->current_snapshot->quality->value . '%';
                $last_site_audit_run = date('D, M d, Y', $site_audit_result->last_audit / 1000);
            }

            // Citation Tracker

            $citation_score = '0.0%';
            $citation_last_run = 'Yet not run';
            if (!empty($result_info)) {
                $citations_data = json_decode($result_info->citations_data);
                $all_citations = $citations_data->citations;
                foreach ($all_citations as $row_c) {
                    if ($row_c->isCitationVerified == 1) {
                        $verified_citaions += 1;
                    }
                }
                $total_citaion = count($all_citations);
                $citation_score = sprintf("%.2f", ($verified_citaions / $total_citaion) * 100) . '%';
                $citation_last_run = date('D, M d, Y', strtotime($result_info->last_run));
            }

            // Visibility Score

            $visibility_score = $visibility_date = '--';
            if ($current_days_data['total_keywords'] > 0) {
                $visibility_score = sprintf("%.2f", ($current_days_data['top_10'] / $current_days_data['total_keywords']) * 100) . '%';
                $visibility_date = date('D, M d, Y', strtotime('last Sunday'));
            }
            
            $last_90_days_date = date("Y-m-d", strtotime("-3 months", time()));
            $last_90_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_90_days_date, 0, $current_days_data);

            $last_180_days_date = date("Y-m-d", strtotime("-6 months", time()));
            $last_180_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_180_days_date, 0, $current_days_data);

            $last_1year_date = date("Y-m-d", strtotime("-12 months", time()));
            $last_1year_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_1year_date, 0, $current_days_data);
            
            $rankcent = $current_days_data['total_rank'] > 0 ? $current_days_data['total_rank']." %":'NA';                        
            
            $rankcent90a = $last_90_days_data['total_rank'] > 0 ? $last_90_days_data['total_rank']." %":'NA';
            $rankcent180a = $last_180_days_data['total_rank'] > 0 ? $last_180_days_data['total_rank']." %":'NA';
            $rankcent1yeara = $last_1year_data['total_rank'] > 0 ? $last_1year_data['total_rank']." %":'NA';
            
            $rankcent90 = $last_90_days_data['total_rank_change'];
            $rankcent180 = $last_180_days_data['total_rank_change'];
            $rankcent1year = $last_1year_data['total_rank_change'];                        
                        
            $avgrankcent = $current_days_data['avg_rank'] > 0 ? $current_days_data['avg_rank']:'NA';
            
            $avgrankcent90a = $last_90_days_data['avg_rank'] > 0 ? $last_90_days_data['avg_rank']:'NA';
            $avgrankcent180a = $last_180_days_data['avg_rank'] > 0 ? $last_180_days_data['avg_rank']:'NA';
            $avgrankcent1yeara = $last_1year_data['avg_rank'] > 0 ? $last_1year_data['avg_rank']:'NA'; 
            
            $avgrankcent90 = $last_90_days_data['avg_rank_change'];
            $avgrankcent180 = $last_180_days_data['avg_rank_change'];
            $avgrankcent1year = $last_1year_data['avg_rank_change'];
            
            $ranktarcent = $current_days_data['rank_vs_target'] > 0 ? $current_days_data['rank_vs_target']." %":'NA';
            
            $ranktarcent90a = $last_90_days_data['rank_vs_target'] > 0 ? $last_90_days_data['rank_vs_target']." %":'NA';
            $ranktarcent180a = $last_180_days_data['rank_vs_target'] > 0 ? $last_180_days_data['rank_vs_target']." %":'NA';
            $ranktarcent1yeara = $last_1year_data['rank_vs_target'] > 0 ? $last_1year_data['rank_vs_target']." %":'NA';
            
            $ranktarcent90 = $last_90_days_data['rank_vs_target_change'];
            $ranktarcent180 = $last_180_days_data['rank_vs_target_change'];
            $ranktarcent1year = $last_1year_data['rank_vs_target_change'];
            
            $istplacecent = $current_days_data['first_place'] > 0 ? $current_days_data['first_place']." ":'0';
            
            $istplace90a = $last_90_days_data['first_place'] > 0 ? $last_90_days_data['first_place']." ":'0';
            $istplace180a = $last_180_days_data['first_place'] > 0 ? $last_180_days_data['first_place']." ":'0';
            $istplace1yeara = $last_1year_data['first_place'] > 0 ? $last_1year_data['first_place']." ":'0';
            
            $istplace90 = $last_90_days_data['first_place_change'];
            $istplace180 = $last_180_days_data['first_place_change'];
            $istplace1year = $last_1year_data['first_place_change'];
            
            $top3cent = $current_days_data['top_3'] > 0 ? $current_days_data['top_3']." ":'0';
            
            $top390a = $last_90_days_data['top_3'] > 0 ? $last_90_days_data['top_3']." ":'0';
            $top3180a = $last_180_days_data['top_3'] > 0 ? $last_180_days_data['top_3']." ":'0';
            $top31yeara = $last_1year_data['top_3'] > 0 ? $last_1year_data['top_3']." ":'0';
            
            $top390 = $last_90_days_data['top_3_change'];
            $top3180 = $last_180_days_data['top_3_change'];
            $top31year = $last_1year_data['top_3_change'];
            
            $top_10cent = $current_days_data['top_10'] > 0 ? $current_days_data['top_10']." ":'0';
            
            $top_1090a = $last_90_days_data['top_10'] > 0 ? $last_90_days_data['top_10']." ":'0';
            $top_10180a = $last_180_days_data['top_10'] > 0 ? $last_180_days_data['top_10']." ":'0';
            $top_101yeara = $last_1year_data['top_10'] > 0 ? $last_1year_data['top_10']." ":'0';
            
            $top_1090 = $last_90_days_data['top_10_change'];
            $top_10180 = $last_180_days_data['top_10_change'];
            $top_101year = $last_1year_data['top_10_change'];
                        
            $inner_csvArr = array($client_name,$website,$totalkeywords,'','', $rankcent,'','','', $avgrankcent,'','','', $ranktarcent,'','','', 
                $istplacecent,'','','', $top3cent,'','','', $top_10cent,'');
           
            $aralliners = array();
            
            $arrank = array($rankcent90,$rankcent180,$rankcent1year,$rankcent90a,$rankcent180a,$rankcent1yeara);
            array_push($aralliners, $arrank);
            $aravgrank = array($avgrankcent90,$avgrankcent180,$avgrankcent1year,$avgrankcent90a,$avgrankcent180a,$avgrankcent1yeara);
            array_push($aralliners, $aravgrank);
            $arranktarcent = array($ranktarcent90,$ranktarcent180,$ranktarcent1year,$ranktarcent90a,$ranktarcent180a,$ranktarcent1yeara);
            array_push($aralliners, $arranktarcent);
            $aristplace = array($istplace90,$istplace180,$istplace1year,$istplace90a,$istplace180a,$istplace1yeara);
            array_push($aralliners, $aristplace);
            $artop3 = array($top390,$top3180,$top31year,$top390a,$top3180a,$top31yeara);
            array_push($aralliners, $artop3);
            $artop10 = array($top_1090,$top_10180,$top_101year,$top_1090a,$top_10180a,$top_101yeara);
            array_push($aralliners, $artop10);            
            
            $str .='<tr>';            
            $k = 0;
            $j = 0;
            foreach ($inner_csvArr as $row_head) {
                    $k++;
                    if($row_head != ''){
                        $str .='<td class="padding_full">' . $row_head;     
                        
                        if($k > 3){
                                                        
                            $newstr = '<table style="margin-top:10px; font-size:13px; width:100%; text-align: center;" ><tr>'
                                    . '<td><div>'.$aralliners[$j][3].'</div><div>(Change)</div><div>'.$aralliners[$j][0].'</div></td>'
                                    . '<td><div>'.$aralliners[$j][4].'</div><div>(Change)</div><div>'.$aralliners[$j][1].'</div></td>'
                                    . '<td><div>'.$aralliners[$j][5].'</div><div>(Change)</div><div>'.$aralliners[$j][2].'</div></td></tr></table>';
                            
                                                        
                            $str .='<div>'.$newstr.'</div>';
                            $j++;
                        }
                        
                        $str .='</td>';
                    }
            }
            $str .='</tr>';
                                    
        }
                
        $str .='</table>';
                        
                
        $str .='<div style="clear:both;height:20px;"></div>';                
        require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($str);        
        $customPaper = array(0,0,1350,$ht);
        $dompdf->set_paper($customPaper);        
        $dompdf->render();
        $user_id = $UserID;
        
        //include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';        
        $pdf = $dompdf->output();        
        $dompdf->stream("executive_summary_report.pdf", array("Attachment" => true));
        exit;        
        
    }
    else if ($_POST['dwnld_type'] == 'csv') {
        $FilePath =  "executive_summary_report.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename=' . $FilePath);    
        ob_clean();
        $fp = fopen('php://output', "w");        
        fputcsv($fp, $header_key_report);
        fputcsv($fp, $header_lower);
        fputcsv($fp, $header_lower1);
        fputcsv($fp, $header_empty);
        fputcsv($fp, $header_empty);
                
        foreach ($locations as $location) {
            $location_id = $location->id;
            $user_id = $UserID = $location->MCCUserId;        
            $website = get_user_meta($UserID, 'website', TRUE);    
            $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);    
            $totalkeywords =  countlocation_keywords($UserID);
            if($totalkeywords == 'N/A'){
                $totalkeywords = 'NA';
            }
            $days90data = 20;
            $all_active_target_url = all_active_target_url($user_id, $remove_http = 1);

            $today = date("Y-m-d");
            $current_days_data = array();
            $current_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $today, 1);
            
            // site audit
            $site_audit_info = rd_site_audit_info($user_id);
            $site_audit_score = '0.0%';
            $last_site_audit_run = 'Yet not run';
            if (!empty($site_audit_info)) {
                $site_audit_result = unserialize($site_audit_info->all_info);
                $site_audit_score = $site_audit_result->current_snapshot->quality->value . '%';
                $last_site_audit_run = date('D, M d, Y', $site_audit_result->last_audit / 1000);
            }

            // Citation Tracker

            $citation_score = '0.0%';
            $citation_last_run = 'Yet not run';
            if (!empty($result_info)) {
                $citations_data = json_decode($result_info->citations_data);
                $all_citations = $citations_data->citations;
                foreach ($all_citations as $row_c) {
                    if ($row_c->isCitationVerified == 1) {
                        $verified_citaions += 1;
                    }
                }
                $total_citaion = count($all_citations);
                $citation_score = sprintf("%.2f", ($verified_citaions / $total_citaion) * 100) . '%';
                $citation_last_run = date('D, M d, Y', strtotime($result_info->last_run));
            }

            // Visibility Score

            $visibility_score = $visibility_date = '--';
            if ($current_days_data['total_keywords'] > 0) {
                $visibility_score = sprintf("%.2f", ($current_days_data['top_10'] / $current_days_data['total_keywords']) * 100) . '%';
                $visibility_date = date('D, M d, Y', strtotime('last Sunday'));
            }
            
            $last_90_days_date = date("Y-m-d", strtotime("-3 months", time()));
            $last_90_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_90_days_date, 0, $current_days_data);

            $last_180_days_date = date("Y-m-d", strtotime("-6 months", time()));
            $last_180_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_180_days_date, 0, $current_days_data);

            $last_1year_date = date("Y-m-d", strtotime("-12 months", time()));
            $last_1year_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_1year_date, 0, $current_days_data);
            
            $rankcent = $current_days_data['total_rank'] > 0 ? $current_days_data['total_rank']." %":'NA';
                        
            $rankcent90a = $last_90_days_data['total_rank'] > 0 ? $last_90_days_data['total_rank']." %":'NA';
            $rankcent180a = $last_180_days_data['total_rank'] > 0 ? $last_180_days_data['total_rank']." %":'NA';
            $rankcent1yeara = $last_1year_data['total_rank'] > 0 ? $last_1year_data['total_rank']." %":'NA';
            
            $rankcent90 = strip_tags($last_90_days_data['total_rank_change']).' (change) ';
            $rankcent180 = strip_tags($last_180_days_data['total_rank_change']).' (change) ';
            $rankcent1year = strip_tags($last_1year_data['total_rank_change']).' (change) ';                        
                        
            $avgrankcent = $current_days_data['avg_rank'] > 0 ? $current_days_data['avg_rank']:'NA';
            
            $avgrankcent90a = $last_90_days_data['avg_rank'] > 0 ? $last_90_days_data['avg_rank']:'NA';
            $avgrankcent180a = $last_180_days_data['avg_rank'] > 0 ? $last_180_days_data['avg_rank']:'NA';
            $avgrankcent1yeara = $last_1year_data['avg_rank'] > 0 ? $last_1year_data['avg_rank']:'NA'; 
            
            $avgrankcent90 = strip_tags($last_90_days_data['avg_rank_change']).' (change) ';
            $avgrankcent180 = strip_tags($last_180_days_data['avg_rank_change']).' (change) ';
            $avgrankcent1year = strip_tags($last_1year_data['avg_rank_change']).' (change) ';
            
            $ranktarcent = $current_days_data['rank_vs_target'] > 0 ? $current_days_data['rank_vs_target']." %":'NA';
            
            
            $ranktarcent90a = $last_90_days_data['rank_vs_target'] > 0 ? $last_90_days_data['rank_vs_target']." %":'NA';
            $ranktarcent180a = $last_180_days_data['rank_vs_target'] > 0 ? $last_180_days_data['rank_vs_target']." %":'NA';
            $ranktarcent1yeara = $last_1year_data['rank_vs_target'] > 0 ? $last_1year_data['rank_vs_target']." %":'NA';
            
            $ranktarcent90 = strip_tags($last_90_days_data['rank_vs_target_change']).' (change) ';
            $ranktarcent180 = strip_tags($last_180_days_data['rank_vs_target_change']).' (change) ';
            $ranktarcent1year = strip_tags($last_1year_data['rank_vs_target_change']).' (change) ';
            
            $istplacecent = $current_days_data['first_place'] > 0 ? $current_days_data['first_place']." ":'0';
            
            
            $istplace90a = $last_90_days_data['first_place'] > 0 ? $last_90_days_data['first_place']." ":'0';
            $istplace180a = $last_180_days_data['first_place'] > 0 ? $last_180_days_data['first_place']." ":'0';
            $istplace1yeara = $last_1year_data['first_place'] > 0 ? $last_1year_data['first_place']." ":'0';
            
            $istplace90 = strip_tags($last_90_days_data['first_place_change']).' (change) ';
            $istplace180 = strip_tags($last_180_days_data['first_place_change']).' (change) ';
            $istplace1year = strip_tags($last_1year_data['first_place_change']).' (change) ';
                                                            
            
            $top3cent = $current_days_data['top_3'] > 0 ? $current_days_data['top_3']." ":'0';
            
            
            $top390a = $last_90_days_data['top_3'] > 0 ? $last_90_days_data['top_3']." ":'0';
            $top3180a = $last_180_days_data['top_3'] > 0 ? $last_180_days_data['top_3']." ":'0';
            $top31yeara = $last_1year_data['top_3'] > 0 ? $last_1year_data['top_3']." ":'0';
            
            $top390 = strip_tags($last_90_days_data['top_3_change']).' (change) ';
            $top3180 = strip_tags($last_180_days_data['top_3_change']).' (change) ';
            $top31year = strip_tags($last_1year_data['top_3_change']).' (change) ';
            
            
            
            $top_10cent = $current_days_data['top_10'] > 0 ? $current_days_data['top_10']." ":'0';            
            
            $top_1090a = $last_90_days_data['top_10'] > 0 ? $last_90_days_data['top_10']." ":'0';
            $top_10180a = $last_180_days_data['top_10'] > 0 ? $last_180_days_data['top_10']." ":'0';
            $top_101yeara = $last_1year_data['top_10'] > 0 ? $last_1year_data['top_10']." ":'0';
            
            $top_1090 = strip_tags($last_90_days_data['top_10_change']).' (change) ';
            $top_10180 = strip_tags($last_180_days_data['top_10_change']).' (change) ';
            $top_101year = strip_tags($last_1year_data['top_10_change']).' (change) ';
                        
            $inner_csvArr = array($client_name,$website,$totalkeywords,'','', $rankcent,'','','', $avgrankcent,'','','', $ranktarcent,'','','', 
                $istplacecent,'','','', $top3cent,'','','', $top_10cent,'');
            fputcsv($fp, $inner_csvArr);
        
            $inner_lower_csvArr = array('', '', '','',$rankcent90a,$rankcent180a,$rankcent1yeara, '',$avgrankcent90a,$avgrankcent180a,$avgrankcent1yeara, '',
                $ranktarcent90,$ranktarcent180,$ranktarcent1year, '',$istplace90,$istplace180,$istplace1year,
                '',$top390a,$top3180a,$top31yeara, '',$top_1090a,$top_10180a,$top_101yeara);
            
            $inner_lower_change_csvArr = array('', '', '','',$rankcent90,$rankcent180,$rankcent1year, '',$avgrankcent90,$avgrankcent180,$avgrankcent1year, '',
                $ranktarcent90,$ranktarcent180,$ranktarcent1year, '',$istplace90,$istplace180,$istplace1year,
                '',$top390,$top3180,$top31year, '',$top_1090,$top_10180,$top_101year);
            
            fputcsv($fp, $inner_lower_csvArr);
            fputcsv($fp, $inner_lower_change_csvArr);
            fputcsv($fp, $header_empty);                                    
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


$i = 0;
foreach ($locations as $location) {
        
    $location_id = $location->id;
    $user_id = $UserID = $location->MCCUserId;        
    $website = get_user_meta($UserID, 'website', TRUE);    
    $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);    
    $totalkeywords =  countlocation_keywords($UserID);
    $days90data = 20;
    $today = date("Y-m-d"); 
    $all_active_target_url = all_active_target_url($user_id, $remove_http = 1);
    $current_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $today, 1);
    
    // site audit
    $site_audit_info = rd_site_audit_info($user_id);
    $site_audit_score = '0.0%';
    $last_site_audit_run = 'Yet not run';
    if (!empty($site_audit_info)) {
        $site_audit_result = unserialize($site_audit_info->all_info);
        $site_audit_score = $site_audit_result->current_snapshot->quality->value . '%';
        $last_site_audit_run = date('D, M d, Y', $site_audit_result->last_audit / 1000);
    }
    
    // Citation Tracker
    
    $citation_score = '0.0%';
    $citation_last_run = 'Yet not run';
    if (!empty($result_info)) {
        $citations_data = json_decode($result_info->citations_data);
        $all_citations = $citations_data->citations;
        foreach ($all_citations as $row_c) {
            if ($row_c->isCitationVerified == 1) {
                $verified_citaions += 1;
            }
        }
        $total_citaion = count($all_citations);
        $citation_score = sprintf("%.2f", ($verified_citaions / $total_citaion) * 100) . '%';
        $citation_last_run = date('D, M d, Y', strtotime($result_info->last_run));
    }
    
    // Visibility Score
                   
    $visibility_score = $visibility_date = '--';
    if ($current_days_data['total_keywords'] > 0) {
        $visibility_score = sprintf("%.2f", ($current_days_data['top_10'] / $current_days_data['total_keywords']) * 100) . '%';
        $visibility_date = date('D, M d, Y', strtotime('last Sunday'));
    }
    
    
    $last_90_days_date = date("Y-m-d", strtotime("-3 months", time()));
    $last_90_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_90_days_date, 0, $current_days_data);
    
    $last_180_days_date = date("Y-m-d", strtotime("-6 months", time()));
    $last_180_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_180_days_date, 0, $current_days_data);
    
    $last_1year_date = date("Y-m-d", strtotime("-12 months", time()));
    $last_1year_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $last_1year_date, 0, $current_days_data);
            
    //pr($last_1year_data);
    ?>
    

    <div class="reportdiv <?php if($i == 0) echo 'margin_top_minus20'; ?>">
        <h5>
            <div>Account Name - <?php echo $client_name; ?> </div>
            <div>URL: <?php echo $website; ?>  </div>
        
        <div class="pull-right totalkeyworddiv"> 
            <div class="headreportdiv">Total Keywords</div>
            <div><?php echo $totalkeywords; ?></div>
        </div>
        </h5>
        <div class="clearfix"></div>
        <div class="portlet light traffic_report<?php echo $UserID; ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="boxreport">
                        <div class="headreportdiv">Visibility Score</div>
                        <div class="scorediv"><?php echo $visibility_score; ?> <a target="_blank" href="?parm=execution&function=visibility_report&location_id=<?php echo $location_id; ?>">Detail</a> </div>
                        <div class="scoredate">Last Run : <?php echo $visibility_date; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="boxreport">
                        <div class="headreportdiv">Citation Score</div>
                        <div class="scorediv"><?php echo $citation_score; ?> <a target="_blank" href="?parm=execution&function=citation_report&location_id=<?php echo $location_id; ?>">Detail</a></div>
                        <div class="scoredate">Last Run : <?php echo $citation_last_run; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="boxreport">
                        <div class="headreportdiv">Site Audit Score</div>
                        <div class="scorediv"><?php echo $site_audit_score; ?> <a target="_blank" href="?parm=execution&function=siteaudit_report&location_id=<?php echo $location_id; ?>">Detail</a></div>
                        <div class="scoredate">Last Run : <?php echo $last_site_audit_run; ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                                                                              
                <div class="col-md-4">   
                    <div class="boxreport">
                        <div class="headreportdiv">Rank %</div>
                        <div class="row headermaindiv">
                            <div class="col-md-4"></div>
                            <div class="col-lg-4 mainboxranks">
                                <?php echo isset($current_days_data['total_rank']) && $current_days_data['total_rank'] > 0 ? $current_days_data['total_rank']." %":'N/A'; ?>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 boxreportinner">
                                <div class="headreportdiv">90 Day</div>
                                <div><?php echo isset($last_90_days_data['total_rank']) &&  $last_90_days_data['total_rank'] > 0 ? $last_90_days_data['total_rank']." %":'N/A'; ?></div>
                                <div class="small">(Change)</div>
                                <div><?php echo $last_90_days_data['total_rank_change']; ?></div>
                            </div>
                            <div class="col-md-4 boxreportinner">
                                <div class="headreportdiv">180 Day</div>
                                <div><?php echo isset($last_180_days_data['total_rank']) &&  $last_180_days_data['total_rank'] > 0 ? $last_180_days_data['total_rank']." %":'N/A'; ?></div>
                                <div class="small">(Change)</div>
                                <div><?php echo $last_180_days_data['total_rank_change']; ?></div>
                            </div>
                            <div class="col-md-4 boxreportinner">
                                <div class="headreportdiv">1 year</div>
                                <div><?php echo isset($last_1year_data['total_rank']) &&  $last_1year_data['total_rank'] > 0 ? $last_1year_data['total_rank']." %":'N/A'; ?></div>
                                <div class="small">(Change)</div>
                                <div><?php echo $last_1year_data['total_rank_change']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"> 
                    <div class="boxreport">
                    <div class="headreportdiv">Avg Rank</div>
                    <div class="row headermaindiv">
                        <div class="col-md-4"></div>
                        <div class="col-lg-4 mainboxranks">
                            <?php echo isset($current_days_data['avg_rank']) &&  $current_days_data['avg_rank'] > 0 ? $current_days_data['avg_rank']:'N/A'; ?>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">90 Day</div>
                            <div><?php echo isset($last_90_days_data['avg_rank']) &&  $last_90_days_data['avg_rank'] > 0 ? $last_90_days_data['avg_rank']:'N/A'; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_90_days_data['avg_rank_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">180 Day</div>
                            <div><?php echo isset($last_180_days_data['avg_rank']) &&  $last_180_days_data['avg_rank'] > 0 ? $last_180_days_data['avg_rank']:'N/A'; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_180_days_data['avg_rank_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">1 year</div>
                            <div><?php echo isset($last_1year_data['avg_rank']) &&  $last_1year_data['avg_rank'] > 0 ? $last_1year_data['avg_rank']:'N/A'; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_1year_data['avg_rank_change']; ?></div>
                        </div>
                    </div> 
                    </div>
                </div>
                <div class="col-md-4"> 
                    <div class="boxreport">
                    <div class="headreportdiv">Rank/Target</div>
                    <div class="row headermaindiv">
                        <div class="col-md-4"></div>
                        <div class="col-lg-4 mainboxranks">
                            <?php echo isset($current_days_data['rank_vs_target']) &&  $current_days_data['rank_vs_target'] > 0 ? $current_days_data['rank_vs_target']." %":'N/A'; ?>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">90 Day</div>
                            <div><?php echo isset($last_90_days_data['rank_vs_target']) &&  $last_90_days_data['rank_vs_target'] > 0 ? $last_90_days_data['rank_vs_target']." %":'N/A'; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_90_days_data['rank_vs_target_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">180 Day</div>
                            <div><?php echo isset($last_180_days_data['rank_vs_target']) &&  $last_180_days_data['rank_vs_target'] > 0 ? $last_180_days_data['rank_vs_target']." %":'N/A'; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_180_days_data['rank_vs_target_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">1 year</div>
                            <div><?php echo isset($last_1year_data['rank_vs_target']) &&  $last_1year_data['rank_vs_target'] > 0 ? $last_1year_data['rank_vs_target']." %":'N/A'; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_1year_data['rank_vs_target_change']; ?></div>
                        </div>
                    </div> 
                    </div>
                </div>
                
            </div>
            <div class="row">
                                                                              
                <div class="col-md-4"> 
                    <div class="boxreport">
                    <div class="headreportdiv">1st Place</div>
                    <div class="row headermaindiv">
                        <div class="col-md-4"></div>
                        <div class="col-lg-4 mainboxranks">
                            <?php echo $current_days_data['first_place']; ?>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">90 Day</div>
                            <div><?php echo $last_90_days_data['first_place']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_90_days_data['first_place_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">180 Day</div>
                            <div><?php echo $last_180_days_data['first_place']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_180_days_data['first_place_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">1 year</div>
                            <div><?php echo $last_1year_data['first_place']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_1year_data['first_place_change']; ?></div>
                        </div>
                    </div> 
                    </div>
                </div>
                <div class="col-md-4"> 
                    <div class="boxreport">
                    <div class="headreportdiv">Top 3</div>
                    <div class="row headermaindiv">
                        <div class="col-md-4"></div>
                        <div class="col-lg-4 mainboxranks">
                            <?php echo $current_days_data['top_3']; ?>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">90 Day</div>
                            <div><?php echo $last_90_days_data['top_3']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_90_days_data['top_3_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">180 Day</div>
                            <div><?php echo $last_180_days_data['top_3']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_180_days_data['top_3_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">1 year</div>
                            <div><?php echo $last_1year_data['top_3']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_1year_data['top_3_change']; ?></div>
                        </div>
                    </div> 
                    </div>
                </div>
                <div class="col-md-4"> 
                    <div class="boxreport">
                    <div class="headreportdiv">Top 10</div>
                    <div class="row headermaindiv">
                        <div class="col-md-4"></div>
                        <div class="col-lg-4 mainboxranks">
                            <?php echo $current_days_data['top_10']; ?>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">90 Day</div>
                            <div><?php echo $last_90_days_data['top_10']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_90_days_data['top_10_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">180 Day</div>
                            <div><?php echo $last_180_days_data['top_10']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_180_days_data['top_10_change']; ?></div>
                        </div>
                        <div class="col-md-4 boxreportinner">
                            <div class="headreportdiv">1 year</div>
                            <div><?php echo $last_1year_data['top_10']; ?></div>
                            <div class="small">(Change)</div>
                            <div><?php echo $last_1year_data['top_10_change']; ?></div>
                        </div>
                    </div> 
                    </div>
                </div>
                
            </div>            
        </div>
        
    </div>

    

    <?php
    $i++;
}
include_once 'download_report.php';
?>