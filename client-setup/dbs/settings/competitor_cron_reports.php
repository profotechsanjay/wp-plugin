<?php
/** Cron Job - Compititor Report for Agency Locations **/

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
$db_report_name = ST_COMPETIROR_REPORT;
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

if($con != 'invalid'){
        
    $sql = "SELECT * FROM `".$wpdb->prefix."mcc_sch_settings` WHERE sch_type "
        . "= '$db_report_name' and sch_status = 1 $con order by `sch_uId` asc";
        
    $single_report = $wpdb->get_row($sql);
    
    require_once(ABSPATH . WPINC . '/class-phpmailer.php');
    $today = date("m/d/Y");
    $from_date = date('Y-m-d', time() - 31 * 24 * 3600);
    $to_date = date('Y-m-d', time() - 2 * 24 * 3600);
    $call_page = $db_report_name;
    
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
        $header_key_report = array('Account Name', 'URL', 'Keywords','','', 'Rank %','','','', 'Avg Rank','','','', 'Rank/Target','','','', '1st Place','','','', 'Top 3','','','', 'Top 10','');
        $header_lower = array('', '', '','','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year', '','90 Day','180 Day','1 Year');
        $header_lower1 = array('Active Campaigns', '', '','','', '','','','', '','','','', '','','','', '','','','', '','','','', '','');
        $header_empty = array('', '', '','','', '','','','', '','','','', '','','','', '','','','', '','','','', '','');
        
        if ($report_type == 'pdf') {
            
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
                    background-image:url(<?php echo site_url(); ?>/wp-content/themes/twentytwelve/images/sort/bg.gif);
                }
                .sectionC table thead th.tablesorter-headerAsc{
                    background-image:url(<?php echo site_url(); ?>/wp-content/themes/twentytwelve/images/sort/desc.gif);
                }
                .sectionC table thead th.tablesorter-headerDesc{
                    background-image:url(<?php echo site_url(); ?>/wp-content/themes/twentytwelve/images/sort/asc.gif);
                }

                .cus-btn
                {
                    width:106px !important;
                }
                td i.fa-bar-chart{
                    margin-right: 5px;
                } 

            </style>';               
            $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                .keyword_width{width:20%;} .text-center{S;} 
                .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                td.text-center { text-align: center; padding: 10px 5px; }
                </style>';
            
            $str .= $stle;
            $str .= rd_pdf_header();
            
            $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Competitor Report</h3><br/>';
            $str .='<table cellspacing="10" class="c2" style="margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">';

            $ht = 70;
            foreach ($locations as $location) {

                $ht = $ht + 70;
                $location_id = $location->id;
                $user_id = $UserID = $location->MCCUserId;    
                $str .='<tr><td style="border: 1px solid #cecece; padding: 15px 10px;">'; 

                $client_website = $website = get_user_meta($UserID, 'website', TRUE);    
                $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE); 
                $str .='<div><h4>'.$client_name.' ( '.$website.' )</h4></div>';
                $str .= '<table style="margin:10px 0; text-align: center; font-size:15px; text-align: center; width: 100%;">';

                $competitor_url = get_user_meta($UserID, "competitor_url", true);                   
                
                if(!empty($competitor_url)){ 
                    
                    include 'views/reports/competitor_common.php';
                    
                    $ii = 0;
                    $colors_array = array("#55BF3B", "#DF5353", "#7798BF", "#ff0066", "#8d4654", "#f45b5b", "#8085e9", "#7798BF", "#aaeeee", "#aaeeee", "#eeaaee");
                    $gh_competitor[] = $client_website;
                    $str .= '<tr><td>';
                    $styletop = isset($colors_array[$ii]) ? 'color:' . $colors_array[$ii] . '; background:' . $colors_array[$ii] : '';
                    $str .= '<table cellspacing="0" border="1" class="table table-bordered white-table" style="border-color: #e0e0e0; width: 100%;">
                        <thead>
                            <tr>
                                <th class=" bg-blue" width="25%">Domains</th>
                                <th colspan="2" class="text-center bg-blue" width="15%">Total Rankings</th>
                                <th colspan="2" class="text-center bg-blue"  width="15%">Avg. Pos.</th>
                                <th colspan="2" class="text-center bg-blue"  width="15%">First Place</th>
                                <th colspan="2" class="text-center bg-blue"  width="15%">Top 3</th>
                                <th colspan="2" class="text-center bg-blue"  width="15%">Page 1</th>
                            </tr>                       

                            <tr>
                                <td class="text-center"><i class="fa fa-bar-chart fa-1x" style="'.$styletop.'"></i>
                                    '.$client_website.'                                
                                </td>
                                <td class="text-center">'.$comp_data_arr[$client_website]['total_rank'].'</td>
                                <td class="text-center" width="10%;">';

                                    $ii++;
                                    $new_total = $comp_data_arr[$client_website]['total_rank'];
                                    $old_total = $comp_data_arr[$client_website]['old_total_rank'];
                                    $rank_ch = $new_total - $old_total;
                                    $sign = "";
                                    if ($rank_ch > 0) {
                                        $ch_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($rank_ch == 0) {
                                        $ch_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $ch_class = $label_arr['red'];
                                        $sign = "-";
                                    }

                            $str .= '<span class="label label-sm label-hf-block '.$ch_class.'">'.$sign . abs($rank_ch).'</span>
                                </td>
                                <td class="text-center">';                                                                
                                    if ($comp_data_arr[$client_website]['total_avg_pos'] == 0) {
                                        $str .= '50+';
                                    } else {
                                        $str .=  number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                                    }

                                $str .= '</td><td class="text-center"  width="10%;">';

                                    $new_avg = number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                                    $old_avg = number_format($comp_data_arr[$client_website]['old_total_avg_pos'] / $total_keywords, 2);
                                    $avg_ch = $new_avg - $old_avg;
                                    if ($avg_ch == 51 || $avg_ch == -51) {
                                        $avg_ch = 0;
                                    }

                                    if ($avg_ch > 0) {
                                        $avg_class = $label_arr['red'];
                                        $sign = "-";
                                    } else if ($avg_ch == 0) {
                                        $avg_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $avg_class = $label_arr['green'];
                                        $sign = "+";
                                    }

                                $str .= '<span class="label label-sm label-hf-block '.$avg_class.'">'.$sign . abs($avg_ch).'</span>
                                </td>
                                <td class="text-center">'.$comp_data_arr[$client_website]['first_palce'].'</td>
                                <td class="text-center"  width="10%;">';

                                    $new_first_place = $comp_data_arr[$client_website]['first_palce'];
                                    $old_first_place = $comp_data_arr[$client_website]['old_first_palce'];

                                    $first_place_ch = $new_first_place - $old_first_place;
                                    if ($first_place_ch > 0) {
                                        $first_place_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($first_place_ch == 0) {
                                        $first_place_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $first_place_class = $label_arr['red'];
                                        $sign = "-";
                                    }

                                $str .= '<span class="label label-sm label-hf-block '.$first_place_class.'">'.$sign . abs($first_place_ch).'</span>
                                </td>
                                <td class="text-center">'.$comp_data_arr[$client_website]['top3'].'</td>
                                <td class="text-center"  width="10%;">';

                                    $new_top3 = $comp_data_arr[$client_website]['top3'];
                                    $old_top3 = $comp_data_arr[$client_website]['old_top3'];

                                    $top3_ch = $new_top3 - $old_top3;
                                    if ($top3_ch > 0) {
                                        $top3_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($top3_ch == 0) {
                                        $top3_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $top3_class = $label_arr['red'];
                                        $sign = "-";
                                    }

                                    $str .= '<span class="label label-sm label-hf-block '.$top3_class.'">'.$sign . abs($top3_ch).'</span>
                                </td>
                                <td class="text-center">'.$comp_data_arr[$client_website]['page1'].'</td>
                                <td class="text-center" width="10%;">';

                                    $new_page1 = $comp_data_arr[$client_website]['page1'];
                                    $old_page1 = $comp_data_arr[$client_website]['old_page1'];

                                    $page1_ch = $new_page1 - $old_page1;
                                    if ($page1_ch > 0) {
                                        $page1_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($page1_ch == 0) {
                                        $page1_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $page1_class = $label_arr['red'];
                                        $sign = "-";
                                    }

                                $str .= '<span class="label label-sm label-hf-block '.$page1_class.'">'.$sign . abs($page1_ch).'</span> 
                                </td>
                            </tr>';
                                
                            for ($com_url = 0; $com_url < $count_competitor_url; $com_url++) { 

                                $style = isset($colors_array[$ii]) ? 'color:' . $colors_array[$ii] . '; background:' . $colors_array[$ii] : '';

                                $str .=  '<tr>
                                    <td class="text-center"><i class="fa fa-bar-chart fa-1x" style="'.$style .'"></i>';

                                        $ii++;
                                        if (!empty($competitor_url)) {
                                            $str .= $competitor_url[$com_url];
                                            $gh_competitor[] = $competitor_url[$com_url];
                                        }

                                    $str .= '</td>
                                    <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['total_rank'].'</td>
                                    <td class="text-center">';

                                        $new_total = $comp_data_arr[$competitor_url[$com_url]]['total_rank'];
                                        $old_total = $comp_data_arr[$competitor_url[$com_url]]['old_total_rank'];
                                        $rank_ch = $new_total - $old_total;
                                        if ($rank_ch > 0) {
                                            $ch_class = $label_arr['green'];
                                            $sign = "+";
                                        } else if ($rank_ch == 0) {
                                            $ch_class = $label_arr['blue'];
                                            $sign = "";
                                        } else {
                                            $ch_class = $label_arr['red'];
                                            $sign = "-";
                                        }

                                    $str .= '<span class="label label-sm label-hf-block '.$ch_class.'">'.$sign . abs($rank_ch).'</span>
                                    </td>
                                    <td class="text-center">';

                                        if ($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] == 0) {
                                            $str .= '50+';
                                        } else {
                                            $str .= number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                                        }

                                    $str .= '</td>
                                    <td class="text-center">';

                                        $new_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                                        $old_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['old_total_avg_pos'] / $total_keywords, 2);
                                        $avg_ch = $new_avg - $old_avg;

                                        if ($avg_ch == 51 || $avg_ch == -51) {
                                            $avg_ch = 0;
                                        }

                                        if ($avg_ch > 0) {
                                            $avg_class = $label_arr['red'];
                                            $sign = "-";
                                        } else if ($avg_ch == 0) {
                                            $avg_class = $label_arr['blue'];
                                            $sign = "";
                                        } else {
                                            $avg_class = $label_arr['green'];
                                            $sign = "+";
                                        }

                                    $str .= '<span class="label label-sm label-hf-block '.$avg_class.'">
                                        '.$sign . abs($avg_ch).'</span>
                                    </td>
                                    <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['first_palce'].'</td>
                                    <td class="text-center">';

                                        $new_first_place = $comp_data_arr[$competitor_url[$com_url]]['first_palce'];
                                        $old_first_place = $comp_data_arr[$competitor_url[$com_url]]['old_first_palce'];

                                        $first_place_ch = $new_first_place - $old_first_place;
                                        if ($first_place_ch > 0) {
                                            $first_place_class = $label_arr['green'];
                                            $sign = "+";
                                        } else if ($first_place_ch == 0) {
                                            $first_place_class = $label_arr['blue'];
                                            $sign = "";
                                        } else {
                                            $first_place_class = $label_arr['red'];
                                            $sign = "-";
                                        }

                                    $str .= '<span class="label label-sm label-hf-block '.$first_place_class.'">'.$sign . abs($first_place_ch).'</span>
                                    </td>
                                    <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['top3'].'</td>
                                    <td class="text-center">';

                                        $new_top3 = $comp_data_arr[$competitor_url[$com_url]]['top3'];
                                        $old_top3 = $comp_data_arr[$competitor_url[$com_url]]['old_top3'];

                                        $top3_ch = $new_top3 - $old_top3;
                                        if ($top3_ch > 0) {
                                            $top3_class = $label_arr['green'];
                                            $sign = "+";
                                        } else if ($top3_ch == 0) {
                                            $top3_class = $label_arr['blue'];
                                            $sign = "";
                                        } else {
                                            $top3_class = $label_arr['red'];
                                            $sign = "-";
                                        }

                                    $str .= '<span class="label label-sm label-hf-block '.$top3_class.'">'.$sign . abs($top3_ch).'</span>
                                    </td>
                                    <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['page1'].'</td>
                                    <td class="text-center">';

                                        $new_page1 = $comp_data_arr[$competitor_url[$com_url]]['page1'];
                                        $old_page1 = $comp_data_arr[$competitor_url[$com_url]]['old_page1'];

                                        $page1_ch = $new_page1 - $old_page1;
                                        if ($page1_ch > 0) {
                                            $page1_class = $label_arr['green'];
                                            $sign = "+";
                                        } else if ($page1_ch == 0) {
                                            $page1_class = $label_arr['blue'];
                                            $sign = "";
                                        } else {
                                            $page1_class = $label_arr['red'];
                                            $sign = "-";
                                        }

                                    $str .= '<span class="label label-sm label-hf-block '.$page1_class.'">
                                        '.$sign . abs($page1_ch).'</span> 
                                    </td>
                                </tr>';
                             } 
                        $str .= '</thead></table>';
                         $str .='</td></tr>';

                }
                else{
                    $str .= '<tr><td style="color: #e73d4a; padding: 10px; background-color: #fbe1e3; border-color: #fbe1e3; ">No competitor added for this location</td></tr>';
                }
                $str .=  '</table>';            
                $str .='</td></tr>';

            }

            $str .='</table>';
            
            $str .='<div style="clear:both;height:20px;"></div>';             
            require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
            
            $report_name = ST_COMPETIROR_REPORT."_".$sch_type."_".date('Y-m-d'). '.' . $report_type;
            
            $dompdf = new DOMPDF();
            $dompdf->load_html($str);        
            $widpdf = 1350;
            $customPaper = array(0,0,$widpdf,$ht);
            $dompdf->set_paper($customPaper);        
            $dompdf->render();
            $user_id = $UserID;
            include(ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php');
            $pdf = $dompdf->output();     
            
            if(!is_dir(ABSPATH.'/pdf/schedule-report')){
                @mkdir(ABSPATH.'/pdf/schedule-report',0777);
            }
            $h_reportLink = '/pdf/schedule-report/' . $report_name;
            $filepath = ABSPATH.$h_reportLink;
            file_put_contents($filepath, $pdf);
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

                        //$email->AddBCC('parambir@rudrainnovatives.com');
                        $email->From = MCC_SITE_NAME;
                        $email->FromName = MCC_SITE_NAME;
                        $email->Subject = $single_report->sch_frequency. ' Competitor Report For - '.$single_report->sch_reportVolume;
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
            exit; 
            
        }
        else if ($report_type == 'csv') {
            
            $header_table = array('', 'DOMAINS', 'TOTAL RANKINGS', 'AVG. POS.', 'FIRST PLACE', 'TOP 3', 'PAGE 1');
            $header_empty = array('', '', '', '', '', '', '');
            
            $FilePath =  "competitor_report.csv";                   
            $report_name = ST_COMPETIROR_REPORT."_".$sch_type."_".date('Y-m-d'). '.' . $report_type;            
            
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
                $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);    
                $totalkeywords =  countlocation_keywords($UserID);
                if($totalkeywords == 'N/A'){
                    $totalkeywords = 'NA';
                }

                $header_key_report = array('Account Name - '.$client_name, '', '', '', '', '', '');
                $header_lower = array('Url - '.$website, '', '', '', '', '', '');

                fputcsv($fp, $header_key_report);
                fputcsv($fp, $header_lower);
                fputcsv($fp, $header_empty);

                $competitor_url = get_user_meta($UserID, "competitor_url", true);

                if(!empty($competitor_url)){

                    fputcsv($fp, $header_table);
                    $table_values = array('', '', '', '', '', '', '');
                    include 'views/reports/competitor_common.php';
                    $ii = 0;                
                    $gh_competitor[] = $client_website;


                    $ii++;
                    $new_total = $comp_data_arr[$client_website]['total_rank'];
                    $old_total = $comp_data_arr[$client_website]['old_total_rank'];
                    $rank_ch = $new_total - $old_total;
                    $sign = "";
                    if ($rank_ch > 0) {
                        $ch_class = $label_arr['green'];
                        $sign = "+";
                    } else if ($rank_ch == 0) {
                        $ch_class = $label_arr['blue'];
                        $sign = "";
                    } else {
                        $ch_class = $label_arr['red'];
                        $sign = "-";
                    }

                    if ($comp_data_arr[$client_website]['total_avg_pos'] == 0) {
                        $vll = '50+';
                    } else {
                        $vll =  number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                    }                                                                                                                   

                    $new_avg = number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                    $old_avg = number_format($comp_data_arr[$client_website]['old_total_avg_pos'] / $total_keywords, 2);
                    $avg_ch = $new_avg - $old_avg;
                    if ($avg_ch == 51 || $avg_ch == -51) {
                        $avg_ch = 0;
                    }

                    if ($avg_ch > 0) {
                        $avg_class = $label_arr['red'];
                        $sign = "-";
                    } else if ($avg_ch == 0) {
                        $avg_class = $label_arr['blue'];
                        $sign = "";
                    } else {
                        $avg_class = $label_arr['green'];
                        $sign = "+";
                    }                                                        

                    $new_first_place = $comp_data_arr[$client_website]['first_palce'];
                    $old_first_place = $comp_data_arr[$client_website]['old_first_palce'];

                    $first_place_ch = $new_first_place - $old_first_place;
                    if ($first_place_ch > 0) {
                        $first_place_class = $label_arr['green'];
                        $sign = "+";
                    } else if ($first_place_ch == 0) {
                        $first_place_class = $label_arr['blue'];
                        $sign = "";
                    } else {
                        $first_place_class = $label_arr['red'];
                        $sign = "-";
                    }

                    $new_top3 = $comp_data_arr[$client_website]['top3'];
                    $old_top3 = $comp_data_arr[$client_website]['old_top3'];

                    $top3_ch = $new_top3 - $old_top3;
                    if ($top3_ch > 0) {
                        $top3_class = $label_arr['green'];
                        $sign = "+";
                    } else if ($top3_ch == 0) {
                        $top3_class = $label_arr['blue'];
                        $sign = "";
                    } else {
                        $top3_class = $label_arr['red'];
                        $sign = "-";
                    }


                    $new_page1 = $comp_data_arr[$client_website]['page1'];
                    $old_page1 = $comp_data_arr[$client_website]['old_page1'];

                    $page1_ch = $new_page1 - $old_page1;
                    if ($page1_ch > 0) {
                        $page1_class = $label_arr['green'];
                        $sign = "+";
                    } else if ($page1_ch == 0) {
                        $page1_class = $label_arr['blue'];
                        $sign = "";
                    } else {
                        $page1_class = $label_arr['red'];
                        $sign = "-";
                    }


                    $table_values = array('', $client_website,$comp_data_arr[$client_website]['total_rank'].' | '.$sign . abs($rank_ch),
                            $vll.' | '.$sign . abs($avg_ch), $comp_data_arr[$client_website]['first_palce'].' | '.$sign . abs($first_place_ch),
                            $comp_data_arr[$client_website]['top3'].' | '.$sign . abs($top3_ch), $comp_data_arr[$client_website]['page1'].' | '.$sign . abs($page1_ch)); 


                    fputcsv($fp, $table_values);

                    for ($com_url = 0; $com_url < $count_competitor_url; $com_url++) {


                            $ii++; $urlcompt = '';
                            if (!empty($competitor_url)) {
                                $urlcompt = $competitor_url[$com_url];
                                $gh_competitor[] = $competitor_url[$com_url];
                            }



                            $new_total = $comp_data_arr[$competitor_url[$com_url]]['total_rank'];
                            $old_total = $comp_data_arr[$competitor_url[$com_url]]['old_total_rank'];
                            $rank_ch = $new_total - $old_total;
                            if ($rank_ch > 0) {
                                $ch_class = $label_arr['green'];
                                $sign = "+";
                            } else if ($rank_ch == 0) {
                                $ch_class = $label_arr['blue'];
                                $sign = "";
                            } else {
                                $ch_class = $label_arr['red'];
                                $sign = "-";
                            }

                            if ($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] == 0) {
                                $vll = '50+';
                            } else {
                                $vll = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                            }

                            $new_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                            $old_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['old_total_avg_pos'] / $total_keywords, 2);
                            $avg_ch = $new_avg - $old_avg;

                            if ($avg_ch == 51 || $avg_ch == -51) {
                                $avg_ch = 0;
                            }

                            if ($avg_ch > 0) {
                                $avg_class = $label_arr['red'];
                                $sign = "-";
                            } else if ($avg_ch == 0) {
                                $avg_class = $label_arr['blue'];
                                $sign = "";
                            } else {
                                $avg_class = $label_arr['green'];
                                $sign = "+";
                            }


                            $new_first_place = $comp_data_arr[$competitor_url[$com_url]]['first_palce'];
                            $old_first_place = $comp_data_arr[$competitor_url[$com_url]]['old_first_palce'];

                            $first_place_ch = $new_first_place - $old_first_place;
                            if ($first_place_ch > 0) {
                                $first_place_class = $label_arr['green'];
                                $sign = "+";
                            } else if ($first_place_ch == 0) {
                                $first_place_class = $label_arr['blue'];
                                $sign = "";
                            } else {
                                $first_place_class = $label_arr['red'];
                                $sign = "-";
                            }

                            $new_top3 = $comp_data_arr[$competitor_url[$com_url]]['top3'];
                            $old_top3 = $comp_data_arr[$competitor_url[$com_url]]['old_top3'];

                            $top3_ch = $new_top3 - $old_top3;
                            if ($top3_ch > 0) {
                                $top3_class = $label_arr['green'];
                                $sign = "+";
                            } else if ($top3_ch == 0) {
                                $top3_class = $label_arr['blue'];
                                $sign = "";
                            } else {
                                $top3_class = $label_arr['red'];
                                $sign = "-";
                            }                    

                            $new_page1 = $comp_data_arr[$competitor_url[$com_url]]['page1'];
                            $old_page1 = $comp_data_arr[$competitor_url[$com_url]]['old_page1'];

                            $page1_ch = $new_page1 - $old_page1;
                            if ($page1_ch > 0) {
                                $page1_class = $label_arr['green'];
                                $sign = "+";
                            } else if ($page1_ch == 0) {
                                $page1_class = $label_arr['blue'];
                                $sign = "";
                            } else {
                                $page1_class = $label_arr['red'];
                                $sign = "-";
                            }


                            $table_values = array('', $urlcompt,$comp_data_arr[$competitor_url[$com_url]]['total_rank'].' | '.$sign . abs($rank_ch),
                                $vll.' | '.$sign . abs($avg_ch), $comp_data_arr[$competitor_url[$com_url]]['first_palce'].' | '.$sign . abs($first_place_ch),
                                $comp_data_arr[$competitor_url[$com_url]]['top3'].' | '.$sign . abs($top3_ch), $comp_data_arr[$competitor_url[$com_url]]['page1'].' | '.$sign . abs($page1_ch)); 

                            fputcsv($fp, $table_values);

                    }               

                }
                else{                

                    $no_competitor_table = array('', 'No Competitor added for this Location', '', '', '', '', '');
                    fputcsv($fp, $no_competitor_table);

                }


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

                        //$email->AddBCC('parambir@rudrainnovatives.com');
                        $email->From = MCC_SITE_NAME;
                        $email->FromName = MCC_SITE_NAME;
                        $email->Subject = $single_report->sch_frequency. ' Executive Summary Report For - '.$single_report->sch_reportVolume;
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
            exit;
            
            
            
            
        }
        
    }
}


