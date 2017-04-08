<?php
/** Cron Job - Reports for Agency Locations - Google, Yahoo, Bing Report **/

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';


include_once ABSPATH . '/global_config.php';
include_once ABSPATH."/wp-content/themes/twentytwelve/common/report-function.php";
include_once ABSPATH . '/wp-content/themes/twentytwelve/analytics/my_functions.php';
include_once "settings.php";
include_once "library/report_functions.php";

$key = isset($_GET['key'])?htmlspecialchars(trim($_GET['key'])):'';

if($key != ST_CRON_KEY){
    //exit("Invalid Key");
}

global $wpdb;

if(!function_exists('email_template_body_new')){
    function email_template_body_new($body, $user_email, $email_type) {

        $Tmplt_General = file_get_contents(site_url() . "/cron/csv-email-reports/templates/TMPL-general.php");    
        $body = str_replace('~~EMAIL_BODY~~', html_entity_decode($body), $Tmplt_General);
        $body = str_replace('~~LINK_UNSUBSCRIBE~~', site_url() . "/unsubscribe-email/?email=$user_email&email_type=$email_type&code=" . md5($user_email), $body);
        $body = str_replace('~~LINK_EMAIL_PREFERENCES~~', site_url() . "/email-subscription/?email=" . $user_email . "&code=" . md5($user_email), $body);
        return $body;
    }
}

$stle= '<style>

    .sectionC table thead th.tablesorter-headerAsc,
    .sectionC table thead th.tablesorter-headerDesc,
    .sectionC table thead th.tablesorter-headerUnSorted{

        padding-right:15px !important;
        vertical-align:middle;
        background-position:right center;
        background-repeat:no-repeat;
        cursor:pointer;

    }

    .white-table thead th{ color:#FFFFFF; }

    .sectionC table thead th.tablesorter-headerUnSorted{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/bg.gif);
    }
    .sectionC table thead th.tablesorter-headerAsc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/desc.gif);
    }
    .sectionC table thead th.tablesorter-headerDesc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/asc.gif);
    }

    .cus-btn
    {
        width:106px !important;
    }
    td i.fa-bar-chart{
        margin-right: 5px;
    } 
    
</style>';

$rep_names = '"'.ST_KEYWORD_REPORT.'_google",'. '"'.ST_KEYWORD_REPORT.'_yahoo",'.'"'.ST_KEYWORD_REPORT.'_bing"';
$rank_types = array("google","yahoo","bing");


ini_set('max_execution_time', 90000);
//ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
error_reporting(0);
$interval_time = 30; // minute

$before_days = date('Y-m-d H:i:s', time() - 1 * 24 * 3600); //It is ok
$current_time = date('H:i', time());
$increase_time = date('H:i:s', time() + $interval_time * 60);
$weekday = 'Monday';
$con = 'invalid';

if (date('l') == $weekday) {
    //$con = ' and `sch_id` = 1';
    $con = " and sch_frequency = 'Weekly'";
}
else if (date('d') == '01') {
    $con = " and sch_frequency = 'Monthly'";
}
if (date('l') == $weekday && date('d') == '01') {
    $con = '';
}

require_once(ABSPATH . WPINC . '/class-phpmailer.php');
if($con != 'invalid'){
        
    $sql = "SELECT * FROM `".$wpdb->prefix."mcc_sch_settings` WHERE sch_type "
        . "IN($rep_names) and sch_status = 1 $con ORDER BY FIELD(sch_type, $rep_names) asc";
            
    $sreport = $wpdb->get_results($sql);
    $no = 0;
    foreach($sreport as $single_report){
               
        $rank_type = $rank_types[$no];
        $no++;
        $today = date("m/d/Y");
        $from_date = date('Y-m-d', time() - 31 * 24 * 3600);
        $to_date = date('Y-m-d', time() - 2 * 24 * 3600);
        $call_page = $db_report_name = $single_report->sch_type;
                
        if (!empty($single_report)) {
            $sch_id = $single_report->sch_id;
            $sch_type = $single_report->sch_type;
            $sch_reportVolume = $single_report->sch_reportVolume;
            if($sch_reportVolume == 'Last 30 Days'){
                $reportVolume_date = date('Y-m-d', time() - 30 * 24 * 3600);
            } 
            else if($sch_reportVolume == 'Last 90 Days'){
                $reportVolume_date = date('Y-m-d', time() - 90 * 24 * 3600);
            }
            else if($sch_reportVolume == 'Last 7 Days'){
                $reportVolume_date = date('Y-m-d', time() - 7 * 24 * 3600);
            }

            $report_type = $single_report->report_type;

            $locations = $wpdb->get_results
            (
                $wpdb->prepare
                (
                    "SELECT * FROM " . client_location() . " WHERE status = 1 ORDER BY created_dt DESC", ""
                )
            );
            
            ob_end_clean();
            
            if ($report_type == 'pdf') {
                
                    $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                    .keyword_width{width:20%;} .text-center{S;} 
                    .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                    .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                    td.text-center { text-align: center; padding: 10px 5px; }
                    </style>';
                    
                    $str .= $stle;        
                    $str .= $strstyle;
                    $str .= rd_pdf_header();        
                    $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' '. ucfirst($rank_type) . ' Keyword Report</h3><br/>';        

                    $ht = 70;                     
                    
                    foreach ($locations as $location) {
                        $ht = $ht + 70;
                        
                        $location_id = $location->id;
                        $user_id = $UserID = $location->MCCUserId;
                        $client_website = $website = get_user_meta($UserID, 'website', TRUE);
                        $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);
                        $download_from_date = date('Y-m-d', time() - 30 * 24 * 3600);
                        $download_to_date = date("Y-m-d");
                        $targeting = get_user_meta($user_id, 'adwords-pull', true);

                        $analytics_user_id = $UserID;
                        $target_url = target_url($UserID);                        
                        $page_name = 'keywords-report';            
                        $table_c = 'respTbl';

                        $all_active_target_url = all_active_target_url($UserID, $remove_http = 1);                        
                        $keywords_order = rd_keywords_order($UserID);

                        $keywords_report = keywords_report($UserID, $from_date, $to_date, $synonyms = 1);            
                        $primary_row = array();
                        $str .= '<h4> Location :  '.$brand.' ( '. $client_website .' ) </h4>';
                        $str .='<table border="1" cellspacing="0" class="c2" style="border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 1500px;">';

                        $table_header = '<tr> <th>Keyword</th>';
                        $table_header .= '<th style="width: 250px;">' . ucfirst($rank_type) . ' Ranking URL</th>';
                        $table_header .= '<th>' . ucfirst($rank_type) . ' Rank</th>';
                        if ($targeting == 'local') {
                            $table_header .= '<th>' . ucfirst($rank_type) . ' Local Rank</th>';
                        }
                        if ($rank_type == 'google') {
                            $table_header .= '<th>Google Mobile Rank</th>';
                            $table_header .= '<th>SEOv</th>';
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
                    
                    $str .='<div style="clear:both;height:20px;"></div>';  
                    
                    require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($str);   
                    $widpdf = 1500;
                    $customPaper = array(0,0,$widpdf,$ht);
                    $dompdf->set_paper($customPaper);        
                    $dompdf->render();
                    $user_id = $UserID;
                    include(ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php');
                    
                    $report_name = $db_report_name."_".date('Y-m-d'). '.' . $report_type;
                    $pdf = $dompdf->output();  
                    if(!is_dir(ABSPATH.'/pdf/schedule-report')){
                        @mkdir(ABSPATH.'/pdf/schedule-report',0777);
                    }
                    
                    $h_reportLink = '/pdf/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;
                    file_put_contents($filepath, $pdf);
                    $all_sent_email = array();
                    $report_full_name = $db_report_name;
                    $all_email = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mcc_sch_emails` WHERE `em_status` = 1 and `em_sch_id` = $sch_id");
                    foreach ($all_email as $row_email) {
                        $email = new PHPMailer();
                        $em_emailTo = trim($row_email->em_emailTo);
                        if (email_subscription_setting($em_emailTo, $sch_type) == 'Yes') {
                            if ($em_emailTo != '') {

                                $email->AddAddress($em_emailTo);
                                $all_sent_email[] = $em_emailTo;
                                $display_name = '';
                                $display_name = get_name_by_email($em_emailTo);
                                if ($display_name != '') {
                                    $display_name = ' ' . $display_name;
                                }

                                //$email->AddBCC('parambir@rudrainnovatives.com');
                                $email->From = MCC_SITE_NAME;
                                $email->FromName = MCC_SITE_NAME;
                                $email->Subject = ucwords(str_replace("_", " ", $db_report_name)).' - '.$single_report->sch_reportVolume;
                                $Tmplt_Body = file_get_contents(site_url() . "/cron/csv-email-reports/templates/BODY-all-report.php");
                                $email->Body = email_template_body_new($Tmplt_Body, $em_emailTo, $sch_type);

                                $BRAND_NAME = site_url();

                                $email->Body = str_replace('~~EMAIL_HOLDER_NAME~~', $display_name, $email->Body);
                                $email->Body = str_replace('~~BRAND_NAME~~', $BRAND_NAME, $email->Body);
                                $email->Body = str_replace('~~CLIENT~~', MCC_NAME, $email->Body);
                                $email->Body = str_replace('~~REPORT_FULL_NAME~~', $report_full_name, $email->Body);
                                $email->Body = str_replace('~~WEBSITE~~', 'All Locations', $email->Body);

                                $email->Body = str_replace('~~LINK_UNSUBSCRIBE~~', site_url() . "/unsubscribe-email/?email={$em_emailTo}&email_type={$single_report->sch_type}&code=" . md5($em_emailTo), $email->Body);

                                $email->Body = str_replace('~~LINK_EMAIL_PREFERENCES~~', site_url() . "/email-subscription/?email=" . $em_emailTo . "&code=" . md5($em_emailTo), $email->Body);
                                $email->Body = str_replace('~~FREQUENCY~~', strtolower($single_report->sch_frequency), $email->Body);
                                $email->Body = str_replace('~~TYPE_NAME~~', strtolower(str_replace("ly", "", $single_report->sch_frequency)), $email->Body);

                                $email->msgHTML($email->Body);
                                $email->AddAttachment($filepath, $report_name);
                                $email->Send();

                                $report_full_name2 = $report_full_name;
                                $report_full_name2 = $single_report->sch_frequency . ' ' . $report_full_name2;
                                insert_email_historical_report($user_id, $report_full_name2, $email->Subject, $em_emailTo, 'Automated Schedule Report', '');

                                $wpdb->insert(
                                        "{$wpdb->prefix}mcc_sch_history", array(
                                    'h_sch_id' => $sch_id,
                                    'h_emailTo' => implode(",", $all_sent_email),
                                    'h_reportLink' => $h_reportLink
                                        ), array('%d', '%s', '%s')
                                );

                                $sch_lastUpdated = date("Y-m-d H:i:s");
                                $wpdb->query("UPDATE `".$wpdb->prefix."mcc_sch_settings` SET `sch_lastUpdated` = '$sch_lastUpdated' WHERE `sch_id` = $sch_id;");
                            }
                        }
                    }
                    
            }
            else{
                
                $FilePath =  $rank_type. "_keyword_report.csv";
                $report_name = $FilePath."_".date('Y-m-d'). '.' . $report_type;
                if(!is_dir(ABSPATH.'/csv/schedule-report')){
                    @mkdir(ABSPATH.'/csv/schedule-report',0777);
                }
                $h_reportLink = '/csv/schedule-report/' . $report_name;
                $filepath = ABSPATH.$h_reportLink;
                
                ob_clean();
                $fp = fopen($filepath, "w");
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
                    }
                    fputcsv($fp, $header_empty);
                    fputcsv($fp, $header_empty);
                    fputcsv($fp, $header_empty);

                }
                
                ob_flush(); 
                fclose($fp);
                
                $all_sent_email = array();
                $report_full_name = ST_COMPETIROR_REPORT_NAME;
                $all_email = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."mcc_sch_emails` WHERE `em_status` = 1 and `em_sch_id` = $sch_id");
                foreach ($all_email as $row_email) {
                    $email = new PHPMailer();
                    $em_emailTo = trim($row_email->em_emailTo);
                    if (email_subscription_setting($em_emailTo, $sch_type) == 'Yes') {
                        if ($em_emailTo != '') {

                            $email->AddAddress($em_emailTo);
                            $all_sent_email[] = $em_emailTo;
                            $display_name = '';
                            $display_name = get_name_by_email($em_emailTo);
                            if ($display_name != '') {
                                $display_name = ' ' . $display_name;
                            }
                            
                            
                            $email->From = MCC_SITE_NAME;
                            $email->FromName = MCC_SITE_NAME;
                            $email->Subject = ucwords(str_replace("_", " ", $db_report_name)).' - '.$single_report->sch_reportVolume;
                            $Tmplt_Body = file_get_contents(site_url() . "/cron/csv-email-reports/templates/BODY-all-report.php");
                            $email->Body = email_template_body_new($Tmplt_Body, $em_emailTo, $sch_type);

                            $BRAND_NAME = site_url();

                            $email->Body = str_replace('~~EMAIL_HOLDER_NAME~~', $display_name, $email->Body);
                            $email->Body = str_replace('~~BRAND_NAME~~', $BRAND_NAME, $email->Body);
                            $email->Body = str_replace('~~CLIENT~~', MCC_NAME, $email->Body);
                            $email->Body = str_replace('~~REPORT_FULL_NAME~~', $report_full_name, $email->Body);
                            $email->Body = str_replace('~~WEBSITE~~', 'All Locations', $email->Body);

                            $email->Body = str_replace('~~LINK_UNSUBSCRIBE~~', site_url() . "/unsubscribe-email/?email={$em_emailTo}&email_type={$single_report->sch_type}&code=" . md5($em_emailTo), $email->Body);

                            $email->Body = str_replace('~~LINK_EMAIL_PREFERENCES~~', site_url() . "/email-subscription/?email=" . $em_emailTo . "&code=" . md5($em_emailTo), $email->Body);
                            $email->Body = str_replace('~~FREQUENCY~~', strtolower($single_report->sch_frequency), $email->Body);
                            $email->Body = str_replace('~~TYPE_NAME~~', strtolower(str_replace("ly", "", $single_report->sch_frequency)), $email->Body);

                            $email->msgHTML($email->Body);
                            $email->AddAttachment($filepath, $report_name);
                            $email->Send();

                            $report_full_name2 = $report_full_name;
                            $report_full_name2 = $single_report->sch_frequency . ' ' . $report_full_name2;
                            insert_email_historical_report($user_id, $report_full_name2, $email->Subject, $em_emailTo, 'Automated Schedule Report', '');

                            $wpdb->insert(
                                    "{$wpdb->prefix}mcc_sch_history", array(
                                'h_sch_id' => $sch_id,
                                'h_emailTo' => implode(",", $all_sent_email),
                                'h_reportLink' => $h_reportLink
                                    ), array('%d', '%s', '%s')
                            );

                            $sch_lastUpdated = date("Y-m-d H:i:s");
                            $wpdb->query("UPDATE `".$wpdb->prefix."mcc_sch_settings` SET `sch_lastUpdated` = '$sch_lastUpdated' WHERE `sch_id` = $sch_id;");
                        }
                    }
                }
                
                
            }
                        
        }
                
    }
    
            
}


