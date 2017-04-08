<?php
$from_date = date('Y-m-d', time() - 31 * 24 * 3600);
$to_date = date('Y-m-d', time() - 2 * 24 * 3600);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['analytics_date_frm_btn'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));    
}
$rank_type = 'google';
if (isset($_GET['rank_type'])) {
    $rank_type = $_GET['rank_type'];
}

include_once( get_template_directory() . '/analytics/BrightLocalUtils.php');
include_once(get_template_directory() . '/analytics/my_functions.php');
include_once(get_template_directory() . '/common/report-function.php');
            
$strstyle = '<style>
/*    .dataTables_length,.dataTables_filter,.dataTables_info,.dataTables_paginate{display: none;}*/
    .left_align{text-align:left;}
    .table td, .table th{font-size:12px!important;}
    table td{text-align:center;}
    table td span.s-icn{width:12px; margin-left:4px; display:inline-block}
    table td i{margin-left:5px; display:inline-block; font-style:normal; text-align:left; font-weight:bold;}
    .red_arrow{background-image: url('.get_template_directory_uri().'/images/icons/v2/red-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;}
    .green_arrow{background-image: url('.get_template_directory_uri().'/images/icons/v2/green-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;}
    .blue_arrow{background-image: url('.get_template_directory_uri().'/images/icons/v2/blue-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;}
    table.dataTable.no-footer{border-bottom:none!important;}
    .key_table {overflow: auto; overflow-y: hidden; } .key_table .dataTables_wrapper{border: 0; margin: 0; 
  padding: 1em; 
  white-space: nowrap; }
</style>';

$schedule_report_page = 'keyword_grouping';
include_once(get_template_directory() . '/analytics/my_functions.php');
include_once(get_template_directory() . '/common/schedule-report.php');
include_once(SET_COUNT_PLUGIN_DIR . '/custom_functions.php');
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';


$db_report_name = ST_KEYWORD_REPORT.'_'.$rank_type;
if (isset($_POST['btn_download-report'])) {
            
    ob_end_clean();
    
    if ($_POST['dwnld_type'] == 'pdf') {
        
        $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                .keyword_width{width:20%;} .text-center{S;} 
                .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                td.text-center { text-align: center; padding: 10px 5px; } .arroicn {display: inline-block; width: 15px; }
                </style>';
        
        $str .= $stle;        
        $str .= $strstyle;
        $str .= rd_pdf_header();        
        $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' '. ucfirst($rank_type) . ' Keyword Report</h3><br/>';        
        
        $ht = 70;                     
        
        foreach ($locations as $location) {
            
            $ht = $ht + 70;
            $location_id = $location->id;
            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
            $client_website = $website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
            $download_from_date = date('Y-m-d', time() - 30 * 24 * 3600);
            $download_to_date = date("Y-m-d");
            $targeting = get_user_meta($user_id, 'adwords-pull', true);
            
            $target_url = target_url($UserID);
            $page_name = 'keywords-report';            
            $table_c = 'respTbl';
                        
            $all_active_target_url = all_active_target_url($UserID, $remove_http = 1);
            $keywords_order = rd_keywords_order($UserID);

            $keywords_report = keywords_report($UserID, $from_date, $to_date, $synonyms = 1);            
            $primary_row = array();
            $str .= '<h4> Location :  '.$brand.' ( '. $client_website .' ) </h4>';
            $str .='<table border="1" cellspacing="0" class="c2" style="border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 1600px;">';
            
            $table_header = '<tr> <th>Keyword</th>';
            $table_header .= '<th style="width: 250px;">' . ucfirst($rank_type) . ' Ranking URL</th>';
            $table_header .= '<th style="width: 150px">' . ucfirst($rank_type) . ' Rank</th>';
            if ($targeting == 'local') {
                $table_header .= '<th style="width: 150px">' . ucfirst($rank_type) . ' Local Rank</th>';
            }
            if ($rank_type == 'google') {
                $table_header .= '<th style="width: 150px">Google Mobile Rank</th>';
                $table_header .= '<th style="width: 150px">SEOv</th>';
                $table_header .= '<th>Organic<br>Visits</th>
                                    <th>Total<br>Conv</th>
                                    <th>Conv Rate</th>
                                    <th>Avg<br> Monthly<br> Searches</th>
                                    <th>Competition</th>
                                    <th>Suggested Bid</th>
                                    <th>Bucket</th>
                                    <th>Days in Bucket</th>
                                    <th>% of Time</th>';
            }
            $table_header .= '</tr>';
            
            $str.=  $table_header;
            $str .= '<tbody>';
            
            
            foreach ($keywords_order as $single_key => $row_key) { 
                $str .= '<tr>';
                    
                $target_keyword = $row_key['target_keyword'];
                $target_keyword_text = '';
                $table_c = 'respTbl';
                if ($target_keyword == 'Yes') {
                    $table_c = 'respTbl_target';
                    $target_keyword_text = '<span style="background:blue; margin-right: 5px; color: #fff; padding: 2px 5px; border-radius: 10px;" class="target_key badge">T</span>';
                }                                


                $single_key = strtolower(str_replace("'", "", $single_key));
                $CurrentRank = $keywords_report[$single_key]['CurrentRank'];
                $Previous_rank = $keywords_report[$single_key]['Previous_rank'];
                $RankingURL = $keywords_report[$single_key]['RankingURL'];
                $key_html = '<span style="background:#22B04B; margin-right: 5px; color: #fff; padding: 2px 5px; border-radius: 10px;" class="badge">P</span>';
                //$key_html = '';
                $rank_change = $keywords_report[$single_key]['rank_change'];
                $RankingData = $keywords_report[$single_key]['RankingData'];
                $prev_RankingData = $keywords_report[$single_key]['prev_RankingData'];

                if ($rank_type == 'google') {
                    //google Local data
                    $places_name = 'google-places';
                    $mobile_name = 'google-mobile';
                    //google Mobile data
                    $get_ranking_data = get_ranking_data('google-mobile', $RankingData, $prev_RankingData);
                    $mobile_CurrentRank = $get_ranking_data['CurrentRank'];
                    $mobile_prev_CurrentRank = $get_ranking_data['prev_CurrentRank'];
                    $mobile_rank_change = $get_ranking_data['rank_change'];
                    $mobile_arrow_class = $get_ranking_data['arrow_class'];
                } else {

                    $get_ranking_data = get_ranking_data($rank_type, $RankingData, $prev_RankingData);
                    $CurrentRank = $get_ranking_data['CurrentRank'];
                    $RankingURL = $get_ranking_data['RankingURL'];
                    /*
                      if (current_id() == 671 && user_id() == 1349 && $single_key == 'corporate events medina') {
                      pr($RankingData);
                      pr($prev_RankingData);
                      pr($get_ranking_data);
                      echo 'test'.$RankingURL;
                      }
                     */
                    $Previous_rank = $get_ranking_data['prev_CurrentRank'];
                    $rank_change = $get_ranking_data['rank_change'];
                    //$places_arrow_class = $get_ranking_data['arrow_class'];
                    $places_name = $rank_type . '-local';
                }
                if ($targeting == 'local') {
                    $get_ranking_data = get_ranking_data($places_name, $RankingData, $prev_RankingData);
                    $places_CurrentRank = $get_ranking_data['CurrentRank'];
                    $places_prev_CurrentRank = $get_ranking_data['prev_CurrentRank'];
                    $places_rank_change = $get_ranking_data['rank_change'];
                    $places_arrow_class = $get_ranking_data['arrow_class'];
                }

                $arrow_class = '';
                if ($rank_change > 0) {
                    $arrow_class = 'green_arrow';
                    $went_up++;
                } else if ($rank_change < 0) {
                    $arrow_class = 'red_arrow';
                    $went_down++;
                } else {
                    $arrow_class = 'blue_arrow';
                    $stay_same++;
                }
                $SEOv_change = str_replace("$", "", $keywords_report[$single_key]['SEOv_change']);
                if ($SEOv_change > 0) {
                    $seov_arrow_class = 'green_arrow';
                } else if ($SEOv_change < 0) {
                    $seov_arrow_class = 'red_arrow';
                } else {
                    $seov_arrow_class = 'blue_arrow';
                }

                // Historical rank data count start
                $get_current_rank = $CurrentRank;
                if ($CurrentRank == '50+') {
                    $get_current_rank = 0;
                }
                if ($get_current_rank > 0) {
                    $current_data['total_rank_number'] += 1;
                    $current_data['total_keywords_rank'] += $get_current_rank;
                } else {
                    $current_data['total_keywords_rank'] += 50;
                    $current_data['pos_no_rank'] ++;
                }
                if ($get_current_rank == 1) {
                    $current_data['first_place'] += 1;
                }
                if ($get_current_rank == 2) {
                    $current_data['second_place'] += 1;
                }
                if ($get_current_rank == 3) {
                    $current_data['third_place'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                    $current_data['top_3'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                    $current_data['top_10'] += 1;
                }

                if ($get_current_rank >= 4 && $get_current_rank <= 10) {
                    $current_data['pos_4_10'] += 1;
                }
                if ($get_current_rank >= 11 && $get_current_rank <= 20) {
                    $current_data['pos_11_20'] += 1;
                }
                if ($get_current_rank >= 21 && $get_current_rank <= 50) {
                    $current_data['pos_21_50'] += 1;
                }
                //Previous data 

                $get_current_rank = $Previous_rank;
                if ($Previous_rank == '50+') {
                    $get_current_rank = 0;
                }
                if ($get_current_rank > 0) {
                    $prev_data['total_rank_number'] += 1;
                    $prev_data['total_keywords_rank'] += $get_current_rank;
                } else {
                    $prev_data['total_keywords_rank'] += 50;
                    $prev_data['pos_no_rank'] ++;
                }
                if ($get_current_rank == 1) {
                    $prev_data['first_place'] += 1;
                }
                if ($get_current_rank == 2) {
                    $prev_data['second_place'] += 1;
                }
                if ($get_current_rank == 3) {
                    $prev_data['third_place'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                    $prev_data['top_3'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                    $prev_data['top_10'] += 1;
                }

                if ($get_current_rank >= 4 && $get_current_rank <= 10) {
                    $prev_data['pos_4_10'] += 1;
                }
                if ($get_current_rank >= 11 && $get_current_rank <= 20) {
                    $prev_data['pos_11_20'] += 1;
                }
                if ($get_current_rank >= 21 && $get_current_rank <= 50) {
                    $prev_data['pos_21_50'] += 1;
                }

                $current_bucket = '';
                $bucket_color_style = 'color: #fff;';

                if ($CurrentRank == '0' || $CurrentRank == '50+') {
                    $current_bucket = '50+';
                    $con = " and `CurrentRank`= 0 ";
                    $bucket_color_style .= 'background-color: #ed6b75';
                } else if ($CurrentRank > 10 && $CurrentRank <= 50) {
                    $current_bucket = '11-50';
                    $con = " and `CurrentRank` >= 11 and `CurrentRank` <= 50 ";
                    $bucket_color_style .= 'background-color: #659be0';
                } else if ($CurrentRank >= 4 && $CurrentRank <= 10) {
                    $current_bucket = '4-10';
                    $con = " and `CurrentRank` >= 4 and `CurrentRank` <= 10 ";
                    $bucket_color_style .= 'background-color: #337ab7;';
                } else if ($CurrentRank >= 1 && $CurrentRank <= 3) {
                    $current_bucket = 'Top 3';
                    $con = " and `CurrentRank` >= 1 and `CurrentRank` <= 3 ";
                    $bucket_color_style .= 'background-color: #36c6d3';
                }

                $sql = 'SELECT CurrentRank, DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $single_key . '" order by `DateOfRank` desc LIMIT 15';
                $date_enter = result_array($sql);
                $bucket_change = 0;
                foreach ($date_enter as $row_enter) {
                    $existing_bucket = bucket_name($row_enter['CurrentRank']);
                    if ($current_bucket != $existing_bucket) {
                        $date_in_bucket = $row_enter['DateOfRank'];
                        $bucket_change = 1;
                        break;
                    }
                }

                $last_time_date = $date_enter[count($date_enter) - 1]['DateOfRank'];
                if ($bucket_change == 0) {
                    $date_in_bucket = $last_time_date;
                }

                $sql = 'SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $single_key . '" order by `DateOfRank` ASC LIMIT 1';
                $date_enter = row_array($sql);
                if (!empty($date_enter)) {
                    $date_enter_val = $date_enter['DateOfRank'];
                } else {
                    $date_enter_val = $seo_data_arr[$row_key]['DateOfRank'];
                }
                $date_enter_val = date("d M Y", strtotime($date_enter_val));

                $date_in_bucket = date("d M Y", strtotime($date_in_bucket));


                $now = time(); // or your date as well
                $your_date = strtotime($date_in_bucket);
                $datediff = $now - $your_date;
                $date_in_bucket_days = floor($datediff / (60 * 60 * 24));
                $bg_color_style = '';
                if ($date_in_bucket_days >= 90) {
                    $date_in_bucket_days = 90;
                }
                if ($current_bucket == '11-50' || $current_bucket == '50+') {
                    if ($date_in_bucket_days >= 90) {
                        $bg_color_style = 'color:red!important;font-weight:bold;';
                    }
                }
                if ($current_bucket == 'Top 3' && $date_in_bucket_days >= 90) {
                    $bg_color_style = 'color:green!important;font-weight:bold;';
                }
                //echo $date_in_bucket_days.'sss<br>'; exit;

                $your_date = strtotime($last_time_date);
                //$your_date = strtotime($date_enter_val);
                $datediff = $now - $your_date;
                $total_days = floor($datediff / (60 * 60 * 24));

                $bucket_val = $current_bucket; //$CurrentRank_text != $none_value ? $current_bucket : $none_value;
                $days_in_bucket_val = $date_in_bucket_days; //$CurrentRank_text != $none_value ? $date_in_bucket_days : $none_value;
                //$percentage_of_time = $CurrentRank_text != $none_value ? sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100) . '%' : $none_value;
                //if ($CurrentRank_text != $none_value) {
                $percentage_of_time = sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100);
                if ($percentage_of_time <= 100) {
                    $percentage_of_time = $percentage_of_time . '%';
                } else {
                    $percentage_of_time = '100.00%';
                }

                //$all_active_target_url
                $reduce_ranking_url = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $RankingURL);

                $target_url_badge = '';
                if (in_array(fully_trim($RankingURL), $all_active_target_url)) {
                    $target_url_badge = '<span style="background:green;margin-right:7px;" class="badge">T</span>';
                }


                $p_keyword = '<td>' . $target_keyword_text . $key_html . '<a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $single_key) . '">' . $single_key . '</a></td>';
                $p_RankingURL = '<td><a href="' . site_url() . '/url-profile/?url=' . $RankingURL . '">' . $target_url_badge . $reduce_ranking_url . '</a></td>';
                $p_CurrentRank = '<td><i>' . $CurrentRank . '</i>
                        <div class="arroicn ' . $arrow_class . ' s-icn">
                            &nbsp;
                        </div> ' . $rank_change . '
                        <div><small>
                        <small>
                            Previous <span style="color:red">' . $Previous_rank . '</span>
                        </small></div>
                    </td>';


                if ($targeting == 'local') {
                    $p_local = '<td><i>' . $places_CurrentRank . '</i>
                        <div class="arroicn ' . $places_arrow_class . ' s-icn">
                            &nbsp;
                        </div> ' . $places_rank_change . '
                         <div><small>
                            Previous <span style="color:red">' . $places_prev_CurrentRank . '</span>
                        </small></div>
                    </td>';
                }



                if ($rank_type == 'google') {
                    $p_google_mobile = '<td><i>' . $mobile_CurrentRank . '</i>
                        <div class="arroicn ' . $mobile_arrow_class . ' s-icn">
                            &nbsp;
                        </div> ' . $mobile_rank_change . '
                        <div><small>
                        <small>
                            Previous <span style="color:red">' . $mobile_prev_CurrentRank . '</span>
                        </small></div>
                    </td>';


                    $p_Current_cal_SEOv = '<td style="width:9%!important;"><i>' . $keywords_report[$single_key]['Current_cal_SEOv'] . '</i>
                        <div class="arroicn ' . $seov_arrow_class . ' s-icn"> &nbsp;</div>                        
                        <div><small>
                            Previous 
                            <span style="color:red">' . $keywords_report[$single_key]['Previous_cal_SEOv'] . '</span>
                        </small></div>
                    </td>';

                    $p_Organic_visit = '<td>' . $keywords_report[$single_key]['Organic_visit'] . '</td>';
                    $p_total_conversion = '<td>' . $keywords_report[$single_key]['total_conversion'] . '</td>';
                    $p_conversion_rate = '<td>' . $keywords_report[$single_key]['conversion_rate'] . '</td>';
                    $p_GoogleSearchVolume = '<td>' . $keywords_report[$single_key]['GoogleSearchVolume'] . '</td>';
                    $p_Difficulty = '<td>' . $keywords_report[$single_key]['Difficulty'] . '</td>';
                    $p_CPC = '<td>' . $keywords_report[$single_key]['CPC'] . '</td>';
                    $p_bucket_val = '<td style="' . $bucket_color_style . '">' . $bucket_val . '</td>';
                    $p_days_in_bucket_val = '<td style="' . $bg_color_style . '">' . $days_in_bucket_val . '</td>';

                    $p_percentage_of_time = '<td>' . $percentage_of_time . '</td>';
                }


                $str .= $p_keyword;
                $str .= $p_RankingURL;
                $str .= $p_CurrentRank;
                if ($targeting == 'local') {
                    $str .= $p_local;
                }
                if ($rank_type == 'google') {
                    $str .= $p_google_mobile;

                    $str .= $p_Current_cal_SEOv;
                    $str .= $p_Organic_visit;
                    $str .= $p_total_conversion;
                    $str .= $p_conversion_rate;
                    $str .= $p_GoogleSearchVolume;
                    $str .= $p_Difficulty;
                    $str .= $p_CPC;
                    $str .= $p_bucket_val;
                    $str .= $p_days_in_bucket_val;
                    $str .= $p_percentage_of_time;
                }
                $str .= '</tr>';                    
            }
            
            $str .= '</tbody>';
            $str .=  '</table>';            
            
        }
                                
        //echo $str; die;
        
        $str .='<div style="clear:both;height:20px;"></div>';                       
        require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($str);  
        $widpdf = 1300;
        $customPaper = array(0,0,$widpdf,$ht);
        $dompdf->set_paper($customPaper);        
        $dompdf->render();
        $user_id = $UserID;
        
        include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
        $pdf = $dompdf->output();        
        $dompdf->stream($rank_type."_keyword_report.pdf", array("Attachment" => true));
        exit;        
        
    }
    else if ($_POST['dwnld_type'] == 'csv') {                                                              
        
        ob_end_clean();
        $FilePath =  ucfirst($rank_type) . "Keyword Report.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename=' . $FilePath);    
        ob_clean();
        $fp = fopen('php://output', "w");                
        
        
        foreach ($locations as $location) {
                        
            $location_id = $location->id;
            $user_id = $UserID = $location->MCCUserId;
            $client_website = $website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
            $download_from_date = date('Y-m-d', time() - 30 * 24 * 3600);
            $download_to_date = date("Y-m-d");
            $targeting = get_user_meta($user_id, 'adwords-pull', true);

            $analytics_user_id = analytics_user_id($UserID);
            $target_url = target_url($UserID);
            $page_name = 'keywords-report';            
            $table_c = 'respTbl';
                        
            $all_active_target_url = all_active_target_url($UserID, $remove_http = 1);
            $keywords_order = rd_keywords_order($UserID);

            $keywords_report = keywords_report($UserID, $from_date, $to_date, $synonyms = 1);            
            $primary_row = array();
                        
            if ($rank_type == 'google') {
                
                if ($targeting == 'local') {
                
                    $header_key_report = array('Account Name - '.$brand, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
                    $header_lower = array('Url - '.$website, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
                    $table_header = array('','Keyword',ucfirst($rank_type). ' Ranking URL',ucfirst($rank_type) . ' Rank',ucfirst($rank_type) . ' Local Rank','Google Mobile Rank',
                        'SEOv','Organic Visits','Total Conv','Conv Rate','Avg Monthly Searches','Competition',
                        'Suggested Bid','Bucket','Days in Bucket','% of Time');
                    $header_empty = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '','');            
                }
                else{
                    
                    $header_key_report = array('Account Name - '.$brand, '', '', '',  '', '', '', '', '', '', '', '', '', '');
                    $header_lower = array('Url - '.$website, '', '', '', '', '', '', '', '', '', '', '', '', '');
                    $table_header = array('','Keyword',ucfirst($rank_type). ' Ranking URL', ucfirst($rank_type) . ' Local Rank','Google Mobile Rank',
                        'SEOv','Organic Visits','Total Conv','Conv Rate','Avg Monthly Searches','Competition',
                        'Suggested Bid','Bucket','Days in Bucket','% of Time');
                    $header_empty = array('', '', '', '', '', '', '', '', '', '', '', '', '', '','');            
                }
                
            }                                             
            else{
                
                if ($targeting == 'local') {
                    $header_key_report = array('Account Name - '.$brand, '', '', '','');
                    $header_lower = array('Url - '.$website, '', '', '', '');
                    $table_header = array('','Keyword',ucfirst($rank_type) . ' Ranking URL',ucfirst($rank_type) . ' Rank', ucfirst($rank_type) . ' Local Rank');
                    $header_empty = array('', '', '', '', '');
                }
                else{
                    $header_key_report = array('Account Name - '.$brand, '', '');
                    $header_lower = array('Url - '.$website, '', '', '');
                    $table_header = array('','Keyword',ucfirst($rank_type) . ' Ranking URL',ucfirst($rank_type) . ' Rank');
                    $header_empty = array('', '', '', '');
                }
                
            }
            
            fputcsv($fp, $header_key_report);
            fputcsv($fp, $header_lower);
            fputcsv($fp, $header_empty);
            fputcsv($fp, $header_empty);
            fputcsv($fp, $table_header);            
            fputcsv($fp, $header_empty);
            
            foreach ($keywords_order as $single_key => $row_key) { 
                                     
                $target_keyword = $row_key['target_keyword'];
                $target_keyword_text = '';
                $table_c = 'respTbl';
                if ($target_keyword == 'Yes') {
                    $table_c = 'respTbl_target';
                    $target_keyword_text = '<span style="background:blue; margin-right: 5px; color: #fff; padding: 2px 5px; border-radius: 10px;" class="target_key badge">T</span>';
                }
                
                $single_key = strtolower(str_replace("'", "", $single_key));
                $CurrentRank = $keywords_report[$single_key]['CurrentRank'];
                $Previous_rank = $keywords_report[$single_key]['Previous_rank'];
                $RankingURL = $keywords_report[$single_key]['RankingURL'];
                $key_html = '<span style="background:#22B04B; margin-right: 5px; color: #fff; padding: 2px 5px; border-radius: 10px;" class="badge">P</span>';
                //$key_html = '';
                $rank_change = $keywords_report[$single_key]['rank_change'];
                $RankingData = $keywords_report[$single_key]['RankingData'];
                $prev_RankingData = $keywords_report[$single_key]['prev_RankingData'];

                if ($rank_type == 'google') {
                    //google Local data
                    $places_name = 'google-places';
                    $mobile_name = 'google-mobile';
                    //google Mobile data
                    $get_ranking_data = get_ranking_data('google-mobile', $RankingData, $prev_RankingData);
                    $mobile_CurrentRank = $get_ranking_data['CurrentRank'];
                    $mobile_prev_CurrentRank = $get_ranking_data['prev_CurrentRank'];
                    $mobile_rank_change = $get_ranking_data['rank_change'];
                    $mobile_arrow_class = $get_ranking_data['arrow_class'];
                } else {

                    $get_ranking_data = get_ranking_data($rank_type, $RankingData, $prev_RankingData);
                    $CurrentRank = $get_ranking_data['CurrentRank'];
                    $RankingURL = $get_ranking_data['RankingURL'];
                    /*
                      if (current_id() == 671 && user_id() == 1349 && $single_key == 'corporate events medina') {
                      pr($RankingData);
                      pr($prev_RankingData);
                      pr($get_ranking_data);
                      echo 'test'.$RankingURL;
                      }
                     */
                    $Previous_rank = $get_ranking_data['prev_CurrentRank'];
                    $rank_change = $get_ranking_data['rank_change'];
                    //$places_arrow_class = $get_ranking_data['arrow_class'];
                    $places_name = $rank_type . '-local';
                }
                if ($targeting == 'local') {
                    $get_ranking_data = get_ranking_data($places_name, $RankingData, $prev_RankingData);
                    $places_CurrentRank = $get_ranking_data['CurrentRank'];
                    $places_prev_CurrentRank = $get_ranking_data['prev_CurrentRank'];
                    $places_rank_change = $get_ranking_data['rank_change'];
                    $places_arrow_class = $get_ranking_data['arrow_class'];
                }

                $arrow_class = '';
                if ($rank_change > 0) {
                    $arrow_class = 'green_arrow';
                    $went_up++;
                } else if ($rank_change < 0) {
                    $arrow_class = 'red_arrow';
                    $went_down++;
                } else {
                    $arrow_class = 'blue_arrow';
                    $stay_same++;
                }
                $SEOv_change = str_replace("$", "", $keywords_report[$single_key]['SEOv_change']);
                if ($SEOv_change > 0) {
                    $seov_arrow_class = 'green_arrow';
                } else if ($SEOv_change < 0) {
                    $seov_arrow_class = 'red_arrow';
                } else {
                    $seov_arrow_class = 'blue_arrow';
                }

                // Historical rank data count start
                $get_current_rank = $CurrentRank;
                if ($CurrentRank == '50+') {
                    $get_current_rank = 0;
                }
                if ($get_current_rank > 0) {
                    $current_data['total_rank_number'] += 1;
                    $current_data['total_keywords_rank'] += $get_current_rank;
                } else {
                    $current_data['total_keywords_rank'] += 50;
                    $current_data['pos_no_rank'] ++;
                }
                if ($get_current_rank == 1) {
                    $current_data['first_place'] += 1;
                }
                if ($get_current_rank == 2) {
                    $current_data['second_place'] += 1;
                }
                if ($get_current_rank == 3) {
                    $current_data['third_place'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                    $current_data['top_3'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                    $current_data['top_10'] += 1;
                }

                if ($get_current_rank >= 4 && $get_current_rank <= 10) {
                    $current_data['pos_4_10'] += 1;
                }
                if ($get_current_rank >= 11 && $get_current_rank <= 20) {
                    $current_data['pos_11_20'] += 1;
                }
                if ($get_current_rank >= 21 && $get_current_rank <= 50) {
                    $current_data['pos_21_50'] += 1;
                }
                //Previous data 

                $get_current_rank = $Previous_rank;
                if ($Previous_rank == '50+') {
                    $get_current_rank = 0;
                }
                if ($get_current_rank > 0) {
                    $prev_data['total_rank_number'] += 1;
                    $prev_data['total_keywords_rank'] += $get_current_rank;
                } else {
                    $prev_data['total_keywords_rank'] += 50;
                    $prev_data['pos_no_rank'] ++;
                }
                if ($get_current_rank == 1) {
                    $prev_data['first_place'] += 1;
                }
                if ($get_current_rank == 2) {
                    $prev_data['second_place'] += 1;
                }
                if ($get_current_rank == 3) {
                    $prev_data['third_place'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                    $prev_data['top_3'] += 1;
                }
                if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                    $prev_data['top_10'] += 1;
                }

                if ($get_current_rank >= 4 && $get_current_rank <= 10) {
                    $prev_data['pos_4_10'] += 1;
                }
                if ($get_current_rank >= 11 && $get_current_rank <= 20) {
                    $prev_data['pos_11_20'] += 1;
                }
                if ($get_current_rank >= 21 && $get_current_rank <= 50) {
                    $prev_data['pos_21_50'] += 1;
                }

                $current_bucket = '';
                $bucket_color_style = 'color: #fff;';


                if ($CurrentRank == '0' || $CurrentRank == '50+') {
                    $current_bucket = '50+';
                    $con = " and `CurrentRank`= 0 ";
                    $bucket_color_style .= 'background-color: #ed6b75';
                } else if ($CurrentRank > 10 && $CurrentRank <= 50) {
                    $current_bucket = '11-50';
                    $con = " and `CurrentRank` >= 11 and `CurrentRank` <= 50 ";
                    $bucket_color_style .= 'background-color: #659be0';
                } else if ($CurrentRank >= 4 && $CurrentRank <= 10) {
                    $current_bucket = '4-10';
                    $con = " and `CurrentRank` >= 4 and `CurrentRank` <= 10 ";
                    $bucket_color_style .= 'background-color: #337ab7;';
                } else if ($CurrentRank >= 1 && $CurrentRank <= 3) {
                    $current_bucket = 'Top 3';
                    $con = " and `CurrentRank` >= 1 and `CurrentRank` <= 3 ";
                    $bucket_color_style .= 'background-color: #36c6d3';
                }

                $sql = 'SELECT CurrentRank, DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $single_key . '" order by `DateOfRank` desc LIMIT 15';
                $date_enter = result_array($sql);
                $bucket_change = 0;
                foreach ($date_enter as $row_enter) {
                    $existing_bucket = bucket_name($row_enter['CurrentRank']);
                    if ($current_bucket != $existing_bucket) {
                        $date_in_bucket = $row_enter['DateOfRank'];
                        $bucket_change = 1;
                        break;
                    }
                }

                $last_time_date = $date_enter[count($date_enter) - 1]['DateOfRank'];
                if ($bucket_change == 0) {
                    $date_in_bucket = $last_time_date;
                }

                $sql = 'SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $single_key . '" order by `DateOfRank` ASC LIMIT 1';
                $date_enter = row_array($sql);
                if (!empty($date_enter)) {
                    $date_enter_val = $date_enter['DateOfRank'];
                } else {
                    $date_enter_val = $seo_data_arr[$row_key]['DateOfRank'];
                }
                $date_enter_val = date("d M Y", strtotime($date_enter_val));

                $date_in_bucket = date("d M Y", strtotime($date_in_bucket));


                $now = time(); // or your date as well
                $your_date = strtotime($date_in_bucket);
                $datediff = $now - $your_date;
                $date_in_bucket_days = floor($datediff / (60 * 60 * 24));
                $bg_color_style = '';
                if ($date_in_bucket_days >= 90) {
                    $date_in_bucket_days = 90;
                }
                if ($current_bucket == '11-50' || $current_bucket == '50+') {
                    if ($date_in_bucket_days >= 90) {
                        $bg_color_style = 'color:red!important;font-weight:bold;';
                    }
                }
                if ($current_bucket == 'Top 3' && $date_in_bucket_days >= 90) {
                    $bg_color_style = 'color:green!important;font-weight:bold;';
                }
                //echo $date_in_bucket_days.'sss<br>'; exit;

                $your_date = strtotime($last_time_date);
                //$your_date = strtotime($date_enter_val);
                $datediff = $now - $your_date;
                $total_days = floor($datediff / (60 * 60 * 24));

                $bucket_val = $current_bucket; //$CurrentRank_text != $none_value ? $current_bucket : $none_value;
                $days_in_bucket_val = $date_in_bucket_days; //$CurrentRank_text != $none_value ? $date_in_bucket_days : $none_value;
                //$percentage_of_time = $CurrentRank_text != $none_value ? sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100) . '%' : $none_value;
                //if ($CurrentRank_text != $none_value) {
                $percentage_of_time = sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100);
                if ($percentage_of_time <= 100) {
                    $percentage_of_time = $percentage_of_time . '%';
                } else {
                    $percentage_of_time = '100.00%';
                }

                //$all_active_target_url
                $reduce_ranking_url = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $RankingURL);

                $target_url_badge = '';
                if (in_array(fully_trim($RankingURL), $all_active_target_url)) {
                    $target_url_badge = '<span style="background:green;margin-right:7px;" class="badge">T</span>';
                }


                $p_keyword = $single_key;
                $p_RankingURL = '';
                if($RankingURL != '')
                    $p_RankingURL =  site_url() . '/url-profile/?url=' . $RankingURL;
                
                $p_CurrentRank = $CurrentRank;


                if ($targeting == 'local') {
                    $p_local = $places_CurrentRank;
                }

                if ($rank_type == 'google') {
                    $p_google_mobile = $mobile_CurrentRank;


                    $p_Current_cal_SEOv = $keywords_report[$single_key]['Current_cal_SEOv'];

                    $p_Organic_visit = '<td>' . $keywords_report[$single_key]['Organic_visit'] . '</td>';
                    $p_total_conversion = '<td>' . $keywords_report[$single_key]['total_conversion'] . '</td>';
                    $p_conversion_rate = '<td>' . $keywords_report[$single_key]['conversion_rate'] . '</td>';
                    $p_GoogleSearchVolume = '<td>' . $keywords_report[$single_key]['GoogleSearchVolume'] . '</td>';
                    $p_Difficulty = '<td>' . $keywords_report[$single_key]['Difficulty'] . '</td>';
                    $p_CPC = '<td>' . $keywords_report[$single_key]['CPC'] . '</td>';
                    $p_bucket_val = '<td style="' . $bucket_color_style . '">' . $bucket_val . '</td>';
                    $p_days_in_bucket_val = '<td style="' . $bg_color_style . '">' . $days_in_bucket_val . '</td>';

                    $p_percentage_of_time = '<td>' . $percentage_of_time . '</td>';
                }
                
                $ar_vals = array();
                
                $p_keyword = strip_tags($p_keyword);
                $p_RankingURL = strip_tags($p_RankingURL);
                $p_CurrentRank = strip_tags($p_CurrentRank);
                
                $ar_vals[] = '';
                $ar_vals[] = strip_tags($p_keyword);
                $ar_vals[] = strip_tags($p_RankingURL);
                $ar_vals[] = strip_tags($p_CurrentRank);
                
                if ($targeting == 'local') {                                        
                    $p_local = strip_tags($p_local);   
                    $ar_vals[] = $p_local;                                        
                }
                
                if ($rank_type == 'google') {                                                            
                    $ar_vals[] = strip_tags($p_google_mobile);
                    $ar_vals[] = strip_tags($p_Current_cal_SEOv);
                    $ar_vals[] = strip_tags($p_Organic_visit);
                    $ar_vals[] = strip_tags($p_total_conversion);
                    $ar_vals[] = strip_tags($p_conversion_rate);
                    $ar_vals[] = strip_tags($p_GoogleSearchVolume);
                    $ar_vals[] = strip_tags($p_Difficulty);
                    $ar_vals[] = strip_tags($p_CPC);
                    $ar_vals[] = strip_tags($p_bucket_val);
                    $ar_vals[] = strip_tags($p_days_in_bucket_val);
                    $ar_vals[] = strip_tags($p_percentage_of_time);                                        
                    
                }                
                fputcsv($fp, $ar_vals);    
                //pr($ar_vals);
            }
            fputcsv($fp, $header_empty);
            fputcsv($fp, $header_empty);
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
?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/css/theme.blue.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.widgets.min.js"></script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-timepicker-addon.js"></script>
    
<?php
echo $strstyle; 
$start = 0;
$limit = 10;
?>

<div class="margin_top_minus20 overflowhidn">
    <div class="col-md-6">  
        <div class="btn-group btn-group">
            <a href="<?php echo site_url()."/".ST_LOC_PAGE; ?>?parm=reports&report-type=keyword" class="btn btn-success <?php if ($rank_type == 'google') echo 'active'; ?>"> Google Rank </a>
            <a href="<?php echo site_url()."/".ST_LOC_PAGE; ?>?parm=reports&report-type=keyword&rank_type=yahoo" class="btn btn-success <?php if ($rank_type == 'yahoo') echo 'active'; ?>"> Yahoo Rank </a>
            <a href="<?php echo site_url()."/".ST_LOC_PAGE; ?>?parm=reports&report-type=keyword&rank_type=bing" class="btn btn-success <?php if ($rank_type == 'bing') echo 'active'; ?>"> Bing Rank </a>
        </div>
    </div>

    <div class="col-md-6">
        <form id="analytics_Frm" action="" method="post" class="form-inline pull-right">               
            <label for="from" class="control-label"><b>From</b></label>
            <input type="text" name="from_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($from_date)); ?>">
            <label for="to"><b>To</b></label>
            <input type="text" name="to_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($to_date)); ?>">
            <input type="submit" class="btn btn-success"  style="background:none;" name="analytics_date_frm_btn" value="Submit">

        </form>
    </div>
</div>

<?php
$arrids = array();
foreach ($locations as $location) {

    $location_id = $location->id;
    $user_id = $UserID = $location->MCCUserId;
    $client_website = $website = get_user_meta($UserID, 'website', TRUE);
    $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
    $download_from_date = date('Y-m-d', time() - 30 * 24 * 3600);
    $download_to_date = date("Y-m-d");
    $targeting = get_user_meta($user_id, 'adwords-pull', true);
    
    $analytics_user_id = analytics_user_id($UserID);
    $target_url = target_url($UserID);
    $page_name = 'keywords-report';    
    $table_c = 'respTbl';
    
    
    $table_header = '<tr>' .
        '<th style="width:25%!important;" class="left_align">Keyword</th>';
    $table_header .= '<th style="width:25%!important;">' . ucfirst($rank_type) . ' Ranking URL</th>';
    $table_header .= '<th style="width:9%!important;">' . ucfirst($rank_type) . ' Rank</th>';
    if ($targeting == 'local') {
        $table_header .= '<th style="width:9%!important;">' . ucfirst($rank_type) . ' Local Rank</th>';
    }
    if ($rank_type == 'google') {
        $table_header .= '<th style="width:9%!important;">Google Mobile Rank</th>';
        $table_header .= '<th style="width:9%!important;">SEOv</th>';
        $table_header .= '<th>Organic<br>Visits</th>
                            <th>Total<br>Conv</th>
                            <th>Conv Rate</th>
                            <th>Avg<br> Monthly<br> Searches</th>
                            <th>Competition</th>
                            <th>Suggested Bid</th>
                            <th>Bucket</th>
                            <th>Days in Bucket</th>
                            <th>% of Time</th>';
    }
    $table_header .= '</tr>';
    $all_active_target_url = all_active_target_url($UserID, $remove_http = 1);
    $keywords_order = rd_keywords_order($UserID);
    
    $keywords_report = keywords_report($UserID, $from_date, $to_date, $synonyms = 1);
    //pr($keywords_order);
    $primary_row = array();
    $iddiv = "tbl_".$location->id;
    $arrids[] = $iddiv;
    ?>
    <div class="reportdiv">        
        <h5>Keyword Report - <?php echo $brand. ' ( '.$website.' )'; ?>
            <div class="pull-right locreport"><a href="?parm=execution&function=location_full_report&location_id=<?php echo $location_id; ?>" target="_blank" class="btn btn-primary ">Location Full Report</a></div>
        </h5>
        
        <div class="commnerpoert keyword_report<?php echo $UserID; ?>">
            
            <div class="primary_keywords key_table" id="<?php echo $iddiv; ?>">
                <div style="clear:both;height:10px;"></div>
                <table class="<?php echo $table_c; ?> table table-striped table-bordered table-hover" cellspacing="0" >
                    <thead>
                        <?php echo $table_header; ?>
                    </thead>
                    <tbody>
                        <?php foreach ($keywords_order as $single_key => $row_key) { ?>  
                            <tr>
                                <?php
                                
                                $target_keyword = $row_key['target_keyword'];
                                $target_keyword_text = '';
                                $table_c = 'respTbl';
                                if ($target_keyword == 'Yes') {
                                    $table_c = 'respTbl_target';
                                    $target_keyword_text = '<span style="background:blue;margin-right:2px;" class="target_key badge">T</span>';
                                }                                
                               
                                
                                $single_key = strtolower(str_replace("'", "", $single_key));
                                $CurrentRank = $keywords_report[$single_key]['CurrentRank'];
                                $Previous_rank = $keywords_report[$single_key]['Previous_rank'];
                                $RankingURL = $keywords_report[$single_key]['RankingURL'];
                                $key_html = '<span style="background:#22B04B; margin-right:6px;" class="badge">P</span>';
                                
                                $rank_change = $keywords_report[$single_key]['rank_change'];
                                $RankingData = $keywords_report[$single_key]['RankingData'];
                                $prev_RankingData = $keywords_report[$single_key]['prev_RankingData'];
                                
                                if ($rank_type == 'google') {
                                    //google Local data
                                    $places_name = 'google-places';
                                    $mobile_name = 'google-mobile';
                                    //google Mobile data
                                    $get_ranking_data = get_ranking_data('google-mobile', $RankingData, $prev_RankingData);
                                    $mobile_CurrentRank = $get_ranking_data['CurrentRank'];
                                    $mobile_prev_CurrentRank = $get_ranking_data['prev_CurrentRank'];
                                    $mobile_rank_change = $get_ranking_data['rank_change'];
                                    $mobile_arrow_class = $get_ranking_data['arrow_class'];
                                } else {

                                    $get_ranking_data = get_ranking_data($rank_type, $RankingData, $prev_RankingData);
                                    $CurrentRank = $get_ranking_data['CurrentRank'];
                                    $RankingURL = $get_ranking_data['RankingURL'];
                                    /*
                                      if (current_id() == 671 && user_id() == 1349 && $single_key == 'corporate events medina') {
                                      pr($RankingData);
                                      pr($prev_RankingData);
                                      pr($get_ranking_data);
                                      echo 'test'.$RankingURL;
                                      }
                                     */
                                    $Previous_rank = $get_ranking_data['prev_CurrentRank'];
                                    $rank_change = $get_ranking_data['rank_change'];
                                    //$places_arrow_class = $get_ranking_data['arrow_class'];
                                    $places_name = $rank_type . '-local';
                                }
                                if ($targeting == 'local') {
                                    $get_ranking_data = get_ranking_data($places_name, $RankingData, $prev_RankingData);
                                    $places_CurrentRank = $get_ranking_data['CurrentRank'];
                                    $places_prev_CurrentRank = $get_ranking_data['prev_CurrentRank'];
                                    $places_rank_change = $get_ranking_data['rank_change'];
                                    $places_arrow_class = $get_ranking_data['arrow_class'];
                                }

                                $arrow_class = '';
                                if ($rank_change > 0) {
                                    $arrow_class = 'green_arrow';
                                    $went_up++;
                                } else if ($rank_change < 0) {
                                    $arrow_class = 'red_arrow';
                                    $went_down++;
                                } else {
                                    $arrow_class = 'blue_arrow';
                                    $stay_same++;
                                }
                                $SEOv_change = str_replace("$", "", $keywords_report[$single_key]['SEOv_change']);
                                if ($SEOv_change > 0) {
                                    $seov_arrow_class = 'green_arrow';
                                } else if ($SEOv_change < 0) {
                                    $seov_arrow_class = 'red_arrow';
                                } else {
                                    $seov_arrow_class = 'blue_arrow';
                                }

                                // Historical rank data count start
                                $get_current_rank = $CurrentRank;
                                if ($CurrentRank == '50+') {
                                    $get_current_rank = 0;
                                }
                                if ($get_current_rank > 0) {
                                    $current_data['total_rank_number'] += 1;
                                    $current_data['total_keywords_rank'] += $get_current_rank;
                                } else {
                                    $current_data['total_keywords_rank'] += 50;
                                    $current_data['pos_no_rank'] ++;
                                }
                                if ($get_current_rank == 1) {
                                    $current_data['first_place'] += 1;
                                }
                                if ($get_current_rank == 2) {
                                    $current_data['second_place'] += 1;
                                }
                                if ($get_current_rank == 3) {
                                    $current_data['third_place'] += 1;
                                }
                                if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                                    $current_data['top_3'] += 1;
                                }
                                if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                                    $current_data['top_10'] += 1;
                                }

                                if ($get_current_rank >= 4 && $get_current_rank <= 10) {
                                    $current_data['pos_4_10'] += 1;
                                }
                                if ($get_current_rank >= 11 && $get_current_rank <= 20) {
                                    $current_data['pos_11_20'] += 1;
                                }
                                if ($get_current_rank >= 21 && $get_current_rank <= 50) {
                                    $current_data['pos_21_50'] += 1;
                                }
                                //Previous data 

                                $get_current_rank = $Previous_rank;
                                if ($Previous_rank == '50+') {
                                    $get_current_rank = 0;
                                }
                                if ($get_current_rank > 0) {
                                    $prev_data['total_rank_number'] += 1;
                                    $prev_data['total_keywords_rank'] += $get_current_rank;
                                } else {
                                    $prev_data['total_keywords_rank'] += 50;
                                    $prev_data['pos_no_rank'] ++;
                                }
                                if ($get_current_rank == 1) {
                                    $prev_data['first_place'] += 1;
                                }
                                if ($get_current_rank == 2) {
                                    $prev_data['second_place'] += 1;
                                }
                                if ($get_current_rank == 3) {
                                    $prev_data['third_place'] += 1;
                                }
                                if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                                    $prev_data['top_3'] += 1;
                                }
                                if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                                    $prev_data['top_10'] += 1;
                                }

                                if ($get_current_rank >= 4 && $get_current_rank <= 10) {
                                    $prev_data['pos_4_10'] += 1;
                                }
                                if ($get_current_rank >= 11 && $get_current_rank <= 20) {
                                    $prev_data['pos_11_20'] += 1;
                                }
                                if ($get_current_rank >= 21 && $get_current_rank <= 50) {
                                    $prev_data['pos_21_50'] += 1;
                                }

                                $current_bucket = '';
                                $bucket_color_style = 'color: #fff;';


                                if ($CurrentRank == '0' || $CurrentRank == '50+') {
                                    $current_bucket = '50+';
                                    $con = " and `CurrentRank`= 0 ";
                                    $bucket_color_style .= 'background-color: #ed6b75';
                                } else if ($CurrentRank > 10 && $CurrentRank <= 50) {
                                    $current_bucket = '11-50';
                                    $con = " and `CurrentRank` >= 11 and `CurrentRank` <= 50 ";
                                    $bucket_color_style .= 'background-color: #659be0';
                                } else if ($CurrentRank >= 4 && $CurrentRank <= 10) {
                                    $current_bucket = '4-10';
                                    $con = " and `CurrentRank` >= 4 and `CurrentRank` <= 10 ";
                                    $bucket_color_style .= 'background-color: #337ab7;';
                                } else if ($CurrentRank >= 1 && $CurrentRank <= 3) {
                                    $current_bucket = 'Top 3';
                                    $con = " and `CurrentRank` >= 1 and `CurrentRank` <= 3 ";
                                    $bucket_color_style .= 'background-color: #36c6d3';
                                }

                                $sql = 'SELECT CurrentRank, DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $single_key . '" order by `DateOfRank` desc LIMIT 15';
                                $date_enter = result_array($sql);
                                $bucket_change = 0;
                                foreach ($date_enter as $row_enter) {
                                    $existing_bucket = bucket_name($row_enter['CurrentRank']);
                                    if ($current_bucket != $existing_bucket) {
                                        $date_in_bucket = $row_enter['DateOfRank'];
                                        $bucket_change = 1;
                                        break;
                                    }
                                }

                                $last_time_date = $date_enter[count($date_enter) - 1]['DateOfRank'];
                                if ($bucket_change == 0) {
                                    $date_in_bucket = $last_time_date;
                                }

                                $sql = 'SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $single_key . '" order by `DateOfRank` ASC LIMIT 1';
                                $date_enter = row_array($sql);
                                if (!empty($date_enter)) {
                                    $date_enter_val = $date_enter['DateOfRank'];
                                } else {
                                    $date_enter_val = $seo_data_arr[$row_key]['DateOfRank'];
                                }
                                $date_enter_val = date("d M Y", strtotime($date_enter_val));

                                $date_in_bucket = date("d M Y", strtotime($date_in_bucket));


                                $now = time(); // or your date as well
                                $your_date = strtotime($date_in_bucket);
                                $datediff = $now - $your_date;
                                $date_in_bucket_days = floor($datediff / (60 * 60 * 24));
                                $bg_color_style = '';
                                if ($date_in_bucket_days >= 90) {
                                    $date_in_bucket_days = 90;
                                }
                                if ($current_bucket == '11-50' || $current_bucket == '50+') {
                                    if ($date_in_bucket_days >= 90) {
                                        $bg_color_style = 'color:red!important;font-weight:bold;';
                                    }
                                }
                                if ($current_bucket == 'Top 3' && $date_in_bucket_days >= 90) {
                                    $bg_color_style = 'color:green!important;font-weight:bold;';
                                }
                                //echo $date_in_bucket_days.'sss<br>'; exit;

                                $your_date = strtotime($last_time_date);
                                //$your_date = strtotime($date_enter_val);
                                $datediff = $now - $your_date;
                                $total_days = floor($datediff / (60 * 60 * 24));

                                $bucket_val = $current_bucket; //$CurrentRank_text != $none_value ? $current_bucket : $none_value;
                                $days_in_bucket_val = $date_in_bucket_days; //$CurrentRank_text != $none_value ? $date_in_bucket_days : $none_value;
                                //$percentage_of_time = $CurrentRank_text != $none_value ? sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100) . '%' : $none_value;
                                //if ($CurrentRank_text != $none_value) {
                                $percentage_of_time = sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100);
                                if ($percentage_of_time <= 100) {
                                    $percentage_of_time = $percentage_of_time . '%';
                                } else {
                                    $percentage_of_time = '100.00%';
                                }
                                //} else {
                                //    $percentage_of_time = $none_value;
                                // }
                                //Rank Bucket End 
                                //$all_active_target_url
                                $reduce_ranking_url = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $RankingURL);
                                
                                $target_url_badge = '';
                                if (in_array(fully_trim($RankingURL), $all_active_target_url)) {
                                    $target_url_badge = '<span style="background:green;margin-right:7px;" class="badge">T</span>';
                                }


                                $p_keyword = '<td class="left_align">' . $target_keyword_text . $key_html . '<a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $single_key) . '">' . $single_key . '</a></td>';
                                $p_RankingURL = '<td class="left_align"><a href="' . site_url() . '/url-profile/?url=' . $RankingURL . '">' . $target_url_badge . $reduce_ranking_url . '</a></td>';
                                $p_CurrentRank = '<td><i>' . $CurrentRank . '</i>
                                        <span class="' . $arrow_class . ' s-icn">
                                            &nbsp;
                                        </span> ' . $rank_change . '
                                        <br>
                                        <small>
                                            Previous <span style="color:red">' . $Previous_rank . '</span>
                                        </small>
                                    </td>';


                                if ($targeting == 'local') {
                                    $p_local = '<td><i>' . $places_CurrentRank . '</i>
                                        <span class="' . $places_arrow_class . ' s-icn">
                                            &nbsp;
                                        </span> ' . $places_rank_change . '
                                        <br>
                                        <small>
                                            Previous <span style="color:red">' . $places_prev_CurrentRank . '</span>
                                        </small>
                                    </td>';
                                }



                                if ($rank_type == 'google') {
                                    $p_google_mobile = '<td><i>' . $mobile_CurrentRank . '</i>
                                        <span class="' . $mobile_arrow_class . ' s-icn">
                                            &nbsp;
                                        </span> ' . $mobile_rank_change . '
                                        <br>
                                        <small>
                                            Previous <span style="color:red">' . $mobile_prev_CurrentRank . '</span>
                                        </small>
                                    </td>';


                                    $p_Current_cal_SEOv = '<td style="width:9%!important;"><i>' . $keywords_report[$single_key]['Current_cal_SEOv'] . '</i>
                                        <span class="' . $seov_arrow_class . ' s-icn"> &nbsp;</span>
                                        <br>
                                        <small>
                                            Previous 
                                            <span style="color:red">' . $keywords_report[$single_key]['Previous_cal_SEOv'] . '</span>
                                        </small>
                                    </td>';

                                    $p_Organic_visit = '<td>' . $keywords_report[$single_key]['Organic_visit'] . '</td>';
                                    $p_total_conversion = '<td>' . $keywords_report[$single_key]['total_conversion'] . '</td>';
                                    $p_conversion_rate = '<td>' . $keywords_report[$single_key]['conversion_rate'] . '</td>';
                                    $p_GoogleSearchVolume = '<td>' . $keywords_report[$single_key]['GoogleSearchVolume'] . '</td>';
                                    $p_Difficulty = '<td>' . $keywords_report[$single_key]['Difficulty'] . '</td>';
                                    $p_CPC = '<td>' . $keywords_report[$single_key]['CPC'] . '</td>';
                                    $p_bucket_val = '<td style="' . $bucket_color_style . '">' . $bucket_val . '</td>';
                                    $p_days_in_bucket_val = '<td style="' . $bg_color_style . '">' . $days_in_bucket_val . '</td>';

                                    $p_percentage_of_time = '<td>' . $percentage_of_time . '</td>';
                                }
                                

                                echo $p_keyword;
                                echo $p_RankingURL;
                                echo $p_CurrentRank;
                                if ($targeting == 'local') {
                                    echo $p_local;
                                }
                                if ($rank_type == 'google') {
                                    echo $p_google_mobile;

                                    echo $p_Current_cal_SEOv;
                                    echo $p_Organic_visit;
                                    echo $p_total_conversion;
                                    echo $p_conversion_rate;
                                    echo $p_GoogleSearchVolume;
                                    echo $p_Difficulty;
                                    echo $p_CPC;
                                    echo $p_bucket_val;
                                    echo $p_days_in_bucket_val;
                                    echo $p_percentage_of_time;
                                }
                               ?>
                            </tr>
                                <?php
                        } 
                        
                        ?>
                                
                    </tbody>
                </table>
            </div>

        </div> 
    </div>    


    <?php
    
    
    
    
}
$arrids = implode(",",$arrids);
?>
<script>
       
jQuery(document).ready(function() {
    jQuery('.respTbl').dataTable({
        "order": [[0, "asc"]],
        "iDisplayLength": 10

    });
    
    var ids = "<?php echo $arrids; ?>";
    var ids = ids.split(",");
    for(flg = 0; flg < ids.length; flg++){       
        DoubleScroll(document.getElementById(ids[flg]));
    }
    
});
 
</script>
<?php include_once 'download_report.php'; ?>