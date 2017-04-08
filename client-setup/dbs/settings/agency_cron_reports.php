<?php

/** Cron Job - Executive Summary Report for Agency Locations * */
$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';


include_once ABSPATH . '/global_config.php';
include_once ABSPATH . "//wp-content/themes/twentytwelve/common/report-function.php";
include_once "settings.php";
include_once "library/report_functions.php";

include_once get_template_directory() . '/analytics/my_functions.php';
include_once SET_COUNT_PLUGIN_DIR . '/custom_functions.php';
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';
                
$key = isset($_GET['key']) ? htmlspecialchars(trim($_GET['key'])) : '';

if ($key != ST_CRON_KEY) {
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
// executve report, Traffic Report, Rank Vs Target Report, Conversion Report

$rep_names = array(ST_ADMIN_EXE_REPORT, ST_TRAFFIC_REPORT, ST_RANK_REPORT, ST_CONVERSION_REPORT);
ini_set('max_execution_time', 90000);
ini_set("error_log", "php-error.log");
error_reporting(0);
$interval_time = 30; // minute

foreach ($rep_names as $rep_name) {

    $db_report_name = $rep_name;
    $before_days = date('Y-m-d H:i:s', time() - 1 * 24 * 3600); //It is ok
    $current_time = date('H:i', time());
    $increase_time = date('H:i:s', time() + $interval_time * 60);
    $weekday = 'Monday';
    $con = 'invalid';

    if (date('l') == $weekday) {
        //$con = ' and `sch_id` = 1';
        $con = " and sch_frequency = 'Weekly'";
    } else if (date('d') == '01') {
        $con = " and sch_frequency = 'Monthly'";
    }
    if (date('l') == $weekday && date('d') == '01') {
        $con = '';
    }

    if ($con != 'invalid') {
        //$con = '';
        $sql = "SELECT * FROM `" . $wpdb->prefix . "mcc_sch_settings` WHERE sch_type "
                . "= '$db_report_name' and sch_status = 1 $con order by `sch_uId` asc";

        $single_report = $wpdb->get_row($sql);

        require_once(ABSPATH . WPINC . '/class-phpmailer.php');
        $today = date("m/d/Y");
        $fromdate = $from_date = date('Y-m-d', time() - 31 * 24 * 3600);
        $todate = $to_date = date('Y-m-d', time() - 2 * 24 * 3600);
        $call_page = $db_report_name;

        if (!empty($single_report)) {
            $sch_id = $single_report->sch_id;
            $sch_type = $single_report->sch_type;
            $sch_reportVolume = $single_report->sch_reportVolume;
            if ($sch_reportVolume == 'Last 30 Days') {
                $reportVolume_date = date('Y-m-d', time() - 30 * 24 * 3600);
            } else if ($sch_reportVolume == 'Last 90 Days') {
                $reportVolume_date = date('Y-m-d', time() - 90 * 24 * 3600);
            } else if ($sch_reportVolume == 'Last 7 Days') {
                $reportVolume_date = date('Y-m-d', time() - 7 * 24 * 3600);
            }

            $str = rd_pdf_header();
            $report_type = $single_report->report_type;

            $locations = $wpdb->get_results
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . client_location() . " WHERE status = 1 ORDER BY created_dt DESC", ""
                    )
            );

            ob_end_clean();

            if($rep_name == ST_ADMIN_EXE_REPORT){

                $header_key_report = array('Account Name', 'URL', 'Keywords', 'Visibility Score', 'Citation Score', 'Site Audit Score', '', '', 'Rank %', '', '', '', 'Avg Rank', '', '', '', 'Rank/Target', '', '', '', '1st Place', '', '', '', 'Top 3', '', '', '', 'Top 10', '');
                $header_lower = array('', '', '', '', '', '', '', '90 Day', '180 Day', '1 Year', '', '90 Day', '180 Day', '1 Year', '', '90 Day', '180 Day', '1 Year', '', '90 Day', '180 Day', '1 Year', '', '90 Day', '180 Day', '1 Year', '', '90 Day', '180 Day', '1 Year');
                $header_lower1 = array('Active Campaigns', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
                $header_empty = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');            

                if ($report_type == 'pdf') {

                    $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                    .keyword_width{width:20%;}
                    .ranking_width{width:33%;}
                    </style>';

                    $str .= rd_pdf_header();
                    $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Executive Summary Report</h3><br/>';
                    $str .='<table cellspacing="0" class="c2" style="margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 1400px; border: 1px solid #cecece;">';
                    $str .='<tr style="background-color:#EBEBEB;">';

                    $arnotinclude = array('URL', 'Keywords', 'Visibility Score', 'Citation Score', 'Site Audit Score');

                    foreach ($header_key_report as $row_head) {
                        if ($row_head != '') {

                            if ($row_head == 'Account Name') {
                                $str .='<th class="padding_full">' . $row_head;
                                $str .='<div style="margin-top:10px; font-size: 12px; ">(Active Campaigns)</div>';
                            } else if (!in_array($row_head, $arnotinclude)) {
                                $str .='<th class="padding_full" style="width: 200px">' . $row_head;
                                $newstr = '<table style="margin-top:10px; font-size:13px; width:100%;" ><tr style="background-color:#EBEBEB;">'
                                        . '<th>90 Day</th><th>180 Day</th><th>1 Year</th></tr></table>';
                                $str .='<div>' . $newstr . '</div>';
                            } else {
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
//                        $totalkeywords = countlocation_keywords($UserID);
//                        if ($totalkeywords == 'N/A') {
//                            $totalkeywords = 'NA';
//                        }
                        $days90data = 20;
                        $all_active_target_url = all_active_target_url($user_id, $remove_http = 1);

                        $today = date("Y-m-d");
                        $current_days_data = array();
                        $current_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $today, 1);
                        $totalkeywords = $current_days_data['total_keywords'];

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
                        $sql = "SELECT citations_data, last_run FROM `wp_citation_tracker` WHERE `user_id` = $user_id order by `citation_tracker_id` desc LIMIT 1";
                        $result_info = $wpdb->get_row($sql);
                        $citation_score = '0.0%';
                        $verified_citaions = $needs_attention = $citation_index = 0;
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

                        $rankcent = $current_days_data['total_rank'] > 0 ? $current_days_data['total_rank'] . " %" : 'NA';

                        $rankcent90a = $last_90_days_data['total_rank'] > 0 ? $last_90_days_data['total_rank'] . " %" : 'NA';
                        $rankcent180a = $last_180_days_data['total_rank'] > 0 ? $last_180_days_data['total_rank'] . " %" : 'NA';
                        $rankcent1yeara = $last_1year_data['total_rank'] > 0 ? $last_1year_data['total_rank'] . " %" : 'NA';

                        $rankcent90 = $last_90_days_data['total_rank_change'];
                        $rankcent180 = $last_180_days_data['total_rank_change'];
                        $rankcent1year = $last_1year_data['total_rank_change'];

                        $avgrankcent = $current_days_data['avg_rank'] > 0 ? $current_days_data['avg_rank'] : 'NA';

                        $avgrankcent90a = $last_90_days_data['avg_rank'] > 0 ? $last_90_days_data['avg_rank'] : 'NA';
                        $avgrankcent180a = $last_180_days_data['avg_rank'] > 0 ? $last_180_days_data['avg_rank'] : 'NA';
                        $avgrankcent1yeara = $last_1year_data['avg_rank'] > 0 ? $last_1year_data['avg_rank'] : 'NA';

                        $avgrankcent90 = $last_90_days_data['avg_rank_change'];
                        $avgrankcent180 = $last_180_days_data['avg_rank_change'];
                        $avgrankcent1year = $last_1year_data['avg_rank_change'];

                        $ranktarcent = $current_days_data['rank_vs_target'] > 0 ? $current_days_data['rank_vs_target'] . " %" : 'NA';

                        $ranktarcent90a = $last_90_days_data['rank_vs_target'] > 0 ? $last_90_days_data['rank_vs_target'] . " %" : 'NA';
                        $ranktarcent180a = $last_180_days_data['rank_vs_target'] > 0 ? $last_180_days_data['rank_vs_target'] . " %" : 'NA';
                        $ranktarcent1yeara = $last_1year_data['rank_vs_target'] > 0 ? $last_1year_data['rank_vs_target'] . " %" : 'NA';

                        $ranktarcent90 = $last_90_days_data['rank_vs_target_change'];
                        $ranktarcent180 = $last_180_days_data['rank_vs_target_change'];
                        $ranktarcent1year = $last_1year_data['rank_vs_target_change'];

                        $istplacecent = $current_days_data['first_place'] > 0 ? $current_days_data['first_place'] . " " : '0';

                        $istplace90a = $last_90_days_data['first_place'] > 0 ? $last_90_days_data['first_place'] . " " : '0';
                        $istplace180a = $last_180_days_data['first_place'] > 0 ? $last_180_days_data['first_place'] . " " : '0';
                        $istplace1yeara = $last_1year_data['first_place'] > 0 ? $last_1year_data['first_place'] . " " : '0';

                        $istplace90 = $last_90_days_data['first_place_change'];
                        $istplace180 = $last_180_days_data['first_place_change'];
                        $istplace1year = $last_1year_data['first_place_change'];

                        $top3cent = $current_days_data['top_3'] > 0 ? $current_days_data['top_3'] . " " : '0';

                        $top390a = $last_90_days_data['top_3'] > 0 ? $last_90_days_data['top_3'] . " " : '0';
                        $top3180a = $last_180_days_data['top_3'] > 0 ? $last_180_days_data['top_3'] . " " : '0';
                        $top31yeara = $last_1year_data['top_3'] > 0 ? $last_1year_data['top_3'] . " " : '0';

                        $top390 = $last_90_days_data['top_3_change'];
                        $top3180 = $last_180_days_data['top_3_change'];
                        $top31year = $last_1year_data['top_3_change'];

                        $top_10cent = $current_days_data['top_10'] > 0 ? $current_days_data['top_10'] . " " : '0';

                        $top_1090a = $last_90_days_data['top_10'] > 0 ? $last_90_days_data['top_10'] . " " : '0';
                        $top_10180a = $last_180_days_data['top_10'] > 0 ? $last_180_days_data['top_10'] . " " : '0';
                        $top_101yeara = $last_1year_data['top_10'] > 0 ? $last_1year_data['top_10'] . " " : '0';

                        $top_1090 = $last_90_days_data['top_10_change'];
                        $top_10180 = $last_180_days_data['top_10_change'];
                        $top_101year = $last_1year_data['top_10_change'];

                        $visibilityscore = $visibility_score . '<br/>'
                                . '<span style="font-size: 11px;">Last Run: ' . $visibility_date . '</span>';

                        $citationscore = $citation_score . '<br/>'
                                . '<span style="font-size: 11px;">Last Run: ' . $citation_last_run . '</span>';

                        $siteauditscore = $site_audit_score . '<br/>'
                                . '<span style="font-size: 11px;">Last Run: ' . $last_site_audit_run . '</span>';

                        $inner_csvArr = array($client_name, $website, $totalkeywords, $visibilityscore, $citationscore, $siteauditscore, '', '', $rankcent, '', '', '', $avgrankcent, '', '', '', $ranktarcent, '', '', '',
                            $istplacecent, '', '', '', $top3cent, '', '', '', $top_10cent, '');

                        $aralliners = array();

                        $arrank = array($rankcent90, $rankcent180, $rankcent1year, $rankcent90a, $rankcent180a, $rankcent1yeara);
                        array_push($aralliners, $arrank);
                        $aravgrank = array($avgrankcent90, $avgrankcent180, $avgrankcent1year, $avgrankcent90a, $avgrankcent180a, $avgrankcent1yeara);
                        array_push($aralliners, $aravgrank);
                        $arranktarcent = array($ranktarcent90, $ranktarcent180, $ranktarcent1year, $ranktarcent90a, $ranktarcent180a, $ranktarcent1yeara);
                        array_push($aralliners, $arranktarcent);
                        $aristplace = array($istplace90, $istplace180, $istplace1year, $istplace90a, $istplace180a, $istplace1yeara);
                        array_push($aralliners, $aristplace);
                        $artop3 = array($top390, $top3180, $top31year, $top390a, $top3180a, $top31yeara);
                        array_push($aralliners, $artop3);
                        $artop10 = array($top_1090, $top_10180, $top_101year, $top_1090a, $top_10180a, $top_101yeara);
                        array_push($aralliners, $artop10);


                        $str .='<tr>';
                        $k = 0;
                        $j = 0;
                        foreach ($inner_csvArr as $row_head) {
                            $k++;
                            if ($row_head != '') {
                                $str .='<td class="padding_full">' . $row_head;

                                if ($k > 6) {

                                    $newstr = '<table style="margin-top:10px; font-size:13px; width:100%; text-align: center;" ><tr>'
                                            . '<td><div>' . $aralliners[$j][3] . '</div><div>(Change)</div><div>' . $aralliners[$j][0] . '</div></td>'
                                            . '<td><div>' . $aralliners[$j][4] . '</div><div>(Change)</div><div>' . $aralliners[$j][1] . '</div></td>'
                                            . '<td><div>' . $aralliners[$j][5] . '</div><div>(Change)</div><div>' . $aralliners[$j][2] . '</div></td></tr></table>';


                                    $str .='<div>' . $newstr . '</div>';
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

                    $report_name = ST_ADMIN_EXE_REPORT . "_" . $sch_type . "_" . date('Y-m-d') . '.' . $report_type;

                    $dompdf = new DOMPDF();
                    $dompdf->load_html($str);
                    $widpdf = 1500;
                    $customPaper = array(0, 0, $widpdf, $ht);
                    $dompdf->set_paper($customPaper);
                    $dompdf->render();
                    $user_id = $UserID;
                    include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
                    $pdf = $dompdf->output();

                    if (!is_dir(ABSPATH . '/pdf/schedule-report')) {
                        @mkdir(ABSPATH . '/pdf/schedule-report', 0777);
                    }
                    $h_reportLink = '/pdf/schedule-report/' . $report_name;
                    $filepath = ABSPATH . $h_reportLink;
                    file_put_contents($filepath, $pdf);
                    $all_sent_email = array();
                    $report_full_name = ST_REPORT_FULL_NAME;
                    $all_email = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "mcc_sch_emails` WHERE `em_status` = 1 and `em_sch_id` = $sch_id");
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
                                $email->Subject = $single_report->sch_frequency . ' Executive Summary Report For - ' . $single_report->sch_reportVolume;
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
                                $wpdb->query("UPDATE `" . $wpdb->prefix . "mcc_sch_settings` SET `sch_lastUpdated` = '$sch_lastUpdated' WHERE `sch_id` = $sch_id;");
                            }
                        }
                    }
                    exit;
                } else if ($report_type == 'csv') {

                    $FilePath = "executive_summary_report.csv";
                    $report_name = ST_ADMIN_EXE_REPORT . "_" . $sch_type . "_" . date('Y-m-d') . '.' . $report_type;

                    if (!is_dir(ABSPATH . '/csv/schedule-report')) {
                        @mkdir(ABSPATH . '/csv/schedule-report', 0777);
                    }
                    $h_reportLink = '/csv/schedule-report/' . $report_name;
                    $filepath = ABSPATH . $h_reportLink;

                    ob_clean();

                    $fp = fopen($filepath, "w");
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
//                        $totalkeywords = countlocation_keywords($UserID);
//                        if ($totalkeywords == 'N/A') {
//                            $totalkeywords = 'NA';
//                        }
                        $days90data = 20;
                        $all_active_target_url = all_active_target_url($user_id, $remove_http = 1);

                        $today = date("Y-m-d");
                        $current_days_data = array();
                        $current_days_data = rd_historical_executive_report_data($user_id, $all_active_target_url, $today, 1);
                        $totalkeywords = $current_days_data['total_keywords'];
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
                        $sql = "SELECT citations_data, last_run FROM `wp_citation_tracker` WHERE `user_id` = $user_id order by `citation_tracker_id` desc LIMIT 1";
                        $result_info = $wpdb->get_row($sql);
                        $citation_score = '0.0%';
                        $verified_citaions = $needs_attention = $citation_index = 0;
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

                        $rankcent = $current_days_data['total_rank'] > 0 ? $current_days_data['total_rank'] . " %" : 'NA';

                        $rankcent90a = $last_90_days_data['total_rank'] > 0 ? $last_90_days_data['total_rank'] . " %" : 'NA';
                        $rankcent180a = $last_180_days_data['total_rank'] > 0 ? $last_180_days_data['total_rank'] . " %" : 'NA';
                        $rankcent1yeara = $last_1year_data['total_rank'] > 0 ? $last_1year_data['total_rank'] . " %" : 'NA';

                        $rankcent90 = strip_tags($last_90_days_data['total_rank_change']) . ' (change) ';
                        $rankcent180 = strip_tags($last_180_days_data['total_rank_change']) . ' (change) ';
                        $rankcent1year = strip_tags($last_1year_data['total_rank_change']) . ' (change) ';

                        $avgrankcent = $current_days_data['avg_rank'] > 0 ? $current_days_data['avg_rank'] : 'NA';

                        $avgrankcent90a = $last_90_days_data['avg_rank'] > 0 ? $last_90_days_data['avg_rank'] : 'NA';
                        $avgrankcent180a = $last_180_days_data['avg_rank'] > 0 ? $last_180_days_data['avg_rank'] : 'NA';
                        $avgrankcent1yeara = $last_1year_data['avg_rank'] > 0 ? $last_1year_data['avg_rank'] : 'NA';

                        $avgrankcent90 = strip_tags($last_90_days_data['avg_rank_change']) . ' (change) ';
                        $avgrankcent180 = strip_tags($last_180_days_data['avg_rank_change']) . ' (change) ';
                        $avgrankcent1year = strip_tags($last_1year_data['avg_rank_change']) . ' (change) ';

                        $ranktarcent = $current_days_data['rank_vs_target'] > 0 ? $current_days_data['rank_vs_target'] . " %" : 'NA';


                        $ranktarcent90a = $last_90_days_data['rank_vs_target'] > 0 ? $last_90_days_data['rank_vs_target'] . " %" : 'NA';
                        $ranktarcent180a = $last_180_days_data['rank_vs_target'] > 0 ? $last_180_days_data['rank_vs_target'] . " %" : 'NA';
                        $ranktarcent1yeara = $last_1year_data['rank_vs_target'] > 0 ? $last_1year_data['rank_vs_target'] . " %" : 'NA';

                        $ranktarcent90 = strip_tags($last_90_days_data['rank_vs_target_change']) . ' (change) ';
                        $ranktarcent180 = strip_tags($last_180_days_data['rank_vs_target_change']) . ' (change) ';
                        $ranktarcent1year = strip_tags($last_1year_data['rank_vs_target_change']) . ' (change) ';

                        $istplacecent = $current_days_data['first_place'] > 0 ? $current_days_data['first_place'] . " " : '0';


                        $istplace90a = $last_90_days_data['first_place'] > 0 ? $last_90_days_data['first_place'] . " " : '0';
                        $istplace180a = $last_180_days_data['first_place'] > 0 ? $last_180_days_data['first_place'] . " " : '0';
                        $istplace1yeara = $last_1year_data['first_place'] > 0 ? $last_1year_data['first_place'] . " " : '0';

                        $istplace90 = strip_tags($last_90_days_data['first_place_change']) . ' (change) ';
                        $istplace180 = strip_tags($last_180_days_data['first_place_change']) . ' (change) ';
                        $istplace1year = strip_tags($last_1year_data['first_place_change']) . ' (change) ';


                        $top3cent = $current_days_data['top_3'] > 0 ? $current_days_data['top_3'] . " " : '0';


                        $top390a = $last_90_days_data['top_3'] > 0 ? $last_90_days_data['top_3'] . " " : '0';
                        $top3180a = $last_180_days_data['top_3'] > 0 ? $last_180_days_data['top_3'] . " " : '0';
                        $top31yeara = $last_1year_data['top_3'] > 0 ? $last_1year_data['top_3'] . " " : '0';

                        $top390 = strip_tags($last_90_days_data['top_3_change']) . ' (change) ';
                        $top3180 = strip_tags($last_180_days_data['top_3_change']) . ' (change) ';
                        $top31year = strip_tags($last_1year_data['top_3_change']) . ' (change) ';



                        $top_10cent = $current_days_data['top_10'] > 0 ? $current_days_data['top_10'] . " " : '0';

                        $top_1090a = $last_90_days_data['top_10'] > 0 ? $last_90_days_data['top_10'] . " " : '0';
                        $top_10180a = $last_180_days_data['top_10'] > 0 ? $last_180_days_data['top_10'] . " " : '0';
                        $top_101yeara = $last_1year_data['top_10'] > 0 ? $last_1year_data['top_10'] . " " : '0';

                        $top_1090 = strip_tags($last_90_days_data['top_10_change']) . ' (change) ';
                        $top_10180 = strip_tags($last_180_days_data['top_10_change']) . ' (change) ';
                        $top_101year = strip_tags($last_1year_data['top_10_change']) . ' (change) ';

                        $visibilityscore = 'Last Run: ' . $visibility_date;

                        $citationscore = 'Last Run: ' . $citation_last_run;

                        $siteauditscore = 'Last Run: ' . $last_site_audit_run;

                        $inner_csvArr = array($client_name, $website, $totalkeywords, $visibility_score, $citation_score, $site_audit_score, '', '', $rankcent, '', '', '', $avgrankcent, '', '', '', $ranktarcent, '', '', '',
                            $istplacecent, '', '', '', $top3cent, '', '', '', $top_10cent, '');
                        fputcsv($fp, $inner_csvArr);

                        $inner_lower_csvArr = array('', '', '', $visibilityscore, $citationscore, $siteauditscore, '', $rankcent90a, $rankcent180a, $rankcent1yeara, '', $avgrankcent90a, $avgrankcent180a, $avgrankcent1yeara, '',
                            $ranktarcent90a, $ranktarcent180a, $ranktarcent1yeara, '', $istplace90a, $istplace180a, $istplace1yeara,
                            '', $top390a, $top3180a, $top31yeara, '', $top_1090a, $top_10180a, $top_101yeara);

                        $inner_lower_change_csvArr = array('', '', '', '', '', '', '', $rankcent90, $rankcent180, $rankcent1year, '', $avgrankcent90, $avgrankcent180, $avgrankcent1year, '',
                            $ranktarcent90, $ranktarcent180, $ranktarcent1year, '', $istplace90, $istplace180, $istplace1year,
                            '', $top390, $top3180, $top31year, '', $top_1090, $top_10180, $top_101year);

                        fputcsv($fp, $inner_lower_csvArr);
                        fputcsv($fp, $inner_lower_change_csvArr);
                        fputcsv($fp, $header_empty);
                    }

                    ob_flush();
                    fclose($fp);

                    $all_sent_email = array();
                    $report_full_name = ST_REPORT_FULL_NAME;
                    $all_email = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "mcc_sch_emails` WHERE `em_status` = 1 and `em_sch_id` = $sch_id");
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
                                $email->Subject = $single_report->sch_frequency . ' Executive Summary Report For - ' . $single_report->sch_reportVolume;
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
                                $wpdb->query("UPDATE `" . $wpdb->prefix . "mcc_sch_settings` SET `sch_lastUpdated` = '$sch_lastUpdated' WHERE `sch_id` = $sch_id;");
                            }
                        }
                    }
                }

            }
            else if($rep_name == ST_TRAFFIC_REPORT){                                                
                
                if ($report_type == 'pdf'){
                    
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
        
                    //echo $str; die;
                    require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($str); 
                    $widpdf = 1250;
                    $customPaper = array(0,0,$widpdf,$ht);
                    $dompdf->set_paper($customPaper);        
                    $dompdf->render();
                    $user_id = $UserID;
                    
                    include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
                    $report_name = $db_report_name."_".date('Y-m-d'). '.' . $report_type;                    
                    $pdf = $dompdf->output();  
                    if(!is_dir(ABSPATH.'/pdf/schedule-report')){
                        @mkdir(ABSPATH.'/pdf/schedule-report',0777);
                    }
                    $h_reportLink = '/pdf/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;
                    file_put_contents($filepath, $pdf);
                    $all_sent_email = array();
                    $report_full_name = 'Traffic Report';
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
                else if ($report_type == 'csv'){
                    
                    $FilePath =  "traffic_report";
                    $report_name = $FilePath."_".date('Y-m-d'). '.' . $report_type;
                    if(!is_dir(ABSPATH.'/csv/schedule-report')){
                        @mkdir(ABSPATH.'/csv/schedule-report',0777);
                    }
                    $h_reportLink = '/csv/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;                  
                    ob_clean();
                    $fp = fopen($filepath, "w");

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

                            $sql = "SELECT PageURL,`Keyword`,`CurrentRank`,sum(organic) as organic_val,sum(social) as social_val,sum(`referral`) as referral_val, sum(`(none)`) as direct_val, sum(`cpc`) as cpc_val, sum(`Total`) as Total_val, sum(`TimeOnSite`) as TOS_val, sum(`BounceRate`) as bounce_rate_val FROM `short_analytics_$analytics_user_id` WHERE `DateOfVisit` >= '$fromdate' and `DateOfVisit` <= '$todate' and PageURL != '' group by PageURL order by `DateOfVisit` desc LIMIT ".ST_MAX_TRAFFIC_REC_TO_SHOW;
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
                    
                    $all_sent_email = array();
                    $report_full_name = 'Traffic Report';
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
            else if($rep_name == ST_RANK_REPORT){
                if ($report_type == 'pdf'){
                    
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
                    $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' '. ucfirst($rank_type) . ' Target Vs Ranking Report</h3><br/>';  
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
                    $report_name = $db_report_name."_".date('Y-m-d'). '.' . $report_type;                    
                    $pdf = $dompdf->output();  
                    if(!is_dir(ABSPATH.'/pdf/schedule-report')){
                        @mkdir(ABSPATH.'/pdf/schedule-report',0777);
                    }
                    $h_reportLink = '/pdf/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;
                    file_put_contents($filepath, $pdf);
                    $all_sent_email = array();
                    $report_full_name = 'Target Vs Rank Report';
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
                else if ($report_type == 'csv'){
                    
                    $FilePath =  "target_vs_rank_report";
                    $report_name = $FilePath."_".date('Y-m-d'). '.' . $report_type;
                    if(!is_dir(ABSPATH.'/csv/schedule-report')){
                        @mkdir(ABSPATH.'/csv/schedule-report',0777);
                    }
                    $h_reportLink = '/csv/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;                  
                    ob_clean();
                    $fp = fopen($filepath, "w");
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
                                        
                    $all_sent_email = array();
                    $report_full_name = 'Target Vs Rank Report';
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
            else if($rep_name == ST_CONVERSION_REPORT){
                if ($report_type == 'pdf'){
                    $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                            .keyword_width{width:20%;} .text-center{S;} 
                            .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                            .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                            td.text-center { text-align: center; padding: 10px 5px; } .tbl{text-align: center; border: 1px solid #ddd;
                            padding: 10px; width: 300px;  display: inline-block; margin-right: 15px;} .col-md-4 .col-md-12{padding: 10px;}
                                        .location {padding: 15px; width: 100%; margin-bottom: 20px; } td, th {
                            padding: 6px;
                        } .text-right {
                            text-align: center;
                            margin-bottom: 13px;
                        } </style>';

                    $str .= rd_pdf_header();        
                    $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Conversion Report</h3><br/>';                      
                    $str .= '<div class="content">';

                    foreach ($locations as $location) {
                            
                            $location_id = $location->id;
                            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
                            $client_website = $website = get_user_meta($UserID, 'website', TRUE);
                            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);        

                            $str .= '<fieldset class="location">
                            <div><h4>Location : '.$brand.' ( '.$website.' )</h4></div>';

                            $str .= '
                            <div class="reportdiv">';

                                if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'")) == 0) {

                                    $str .= '<div class="alert alert-danger">Table Not Found </div>';                        
                                }
                                else{

                                    $all_conv_landing_page = conversions_report($analytics_user_id, $fromdate, $todate);
                                    $conversions_source_report = conversions_source_report($analytics_user_id, $fromdate, $todate, $all_conv_landing_page);                                    

                                    $str .= '
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table border="1" cellspacing="0" class="c2" style="position: relative; top: 15px; border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Source</th>
                                                        <th style="text-align: center;">Visits</th>
                                                        <th style="text-align: center;">Conversions</th>
                                                        <th style="text-align: center;">Conversions Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:20px!important;">';

                                                    $colors = array("#55BF3B", "#DF5353", "#7798BF", "#ff0066", "#8d4654", "#f45b5b", "#8085e9", "#7798BF", "#aaeeee", "#aaeeee", "#eeaaee");
                                                    $i = 0;
                                                    foreach ($conversions_source_report as $row_source_report) {
                                                        $cols = isset($colors[$i]) ? 'color:' . $colors[$i] . '; background:' . $colors[$i] : '';
                                                        $str .='
                                                        <tr>   
                                                            <td><i class="fa fa-bar-chart fa-1x" style="'.$cols.'"></i> '.$row_source_report['name'].'</td>
                                                            <td style="text-align: center;">'.$row_source_report['all_visit'].'</td>
                                                            <td style="text-align: center;">';

                                                                $str .= $row_source_report['all_conv'];
                                                                $total_conversions += $row_source_report['all_conv'];

                                                        $str .= '</td><td style="text-align: center;">'.$row_source_report['conv_rate'].'</td></tr>';                                            
                                                        $i++;
                                                    }
                                                    $str .='
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br /><br />      

                                    <table border="1" cellspacing="0" class="c2" style="position: relative; top: 15px; border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Landing Page</th>
                                                <th style="text-align: center;">Visits</th>                                    
                                                <th style="text-align: center;">Conversions</th>
                                                <th style="text-align: center;">Conversions Rate </th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size:20px!important;">';

                                            foreach ($all_conv_landing_page as $row_con) {     

                                                $str .= '<tr>   
                                                    <td>                                            
                                                        <a href="'.site_url().'/url-profile/?url='.$row_con['url'].'">'.$row_con['url'].'</a>  
                                                    </td>
                                                    <td style="text-align: center;">
                                                        '.$row_con['total_visits'].'</td>
                                                    <td style="text-align: center;">
                                                        '.$row_con['total_conv'].'</td>
                                                    <td style="text-align: center;">
                                                        '.sprintf("%.2f", ($row_con['total_conv'] * 100) / $row_con['total_visits'], 2) . '%'.'</td>
                                                </tr>';
                                            }
                                        $str .= '</tbody>
                                    </table>';

                                }

                            $str .= '</div>';
                            $str .='<div style="clear:both;height:20px;"></div></fieldset>';
                        }

                    $str .= '</div>';                    
                    require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($str);
                    $dompdf->set_paper('A4','landscape');                    
                    $dompdf->render();
                    
                    include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
                    $report_name = $db_report_name."_".date('Y-m-d'). '.' . $report_type;                    
                    $pdf = $dompdf->output();  
                    if(!is_dir(ABSPATH.'/pdf/schedule-report')){
                        @mkdir(ABSPATH.'/pdf/schedule-report',0777);
                    }
                    $h_reportLink = '/pdf/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;
                    file_put_contents($filepath, $pdf);
                    $all_sent_email = array();
                    $report_full_name = 'Conversion Report';
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
                else if ($report_type == 'csv'){
                    
                    $FilePath =  "conersion_report";
                    $report_name = $FilePath."_".date('Y-m-d'). '.' . $report_type;
                    if(!is_dir(ABSPATH.'/csv/schedule-report')){
                        @mkdir(ABSPATH.'/csv/schedule-report',0777);
                    }
                    $h_reportLink = '/csv/schedule-report/' . $report_name;
                    $filepath = ABSPATH.$h_reportLink;                  
                    ob_clean();
                    $fp = fopen($filepath, "w");
                    
                    $headertop = array('','','Source','Visits','Conversions','Conversions Rate');
                    $headerbott = array('','','Landing page','Visits','Conversions','Conversions Rate');        
                    $headerempty = array('','','','','','');

                    foreach ($locations as $location) {

                        $location_id = $location->id;
                        $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
                        $client_website = $website = get_user_meta($UserID, 'website', TRUE);
                        $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);        

                        $headerlocname = array('Location',$brand.' ('.$website.')','','','','');
                        fputcsv($fp, $headerlocname);

                        if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'")) == 0) {

                            $headernotbl = array('','','Table does not exist','','','');
                            fputcsv($fp, $headernotbl);                
                        }
                        else{

                            $all_conv_landing_page = conversions_report($analytics_user_id, $fromdate, $todate);
                            $conversions_source_report = conversions_source_report($analytics_user_id, $fromdate, $todate, $all_conv_landing_page);                                    

                            fputcsv($fp, $headerempty);
                            fputcsv($fp, $headertop);
                            fputcsv($fp, $headerempty);
                            foreach ($conversions_source_report as $row_source_report) {                                        
                                $arrtoptbl = array('','',$row_source_report['name'],$row_source_report['all_visit'],$row_source_report['all_conv'],$row_source_report['conv_rate']);                    
                                fputcsv($fp, $arrtoptbl);
                            }
                            fputcsv($fp, $headerempty);
                            fputcsv($fp, $headerempty);               
                            fputcsv($fp, $headerbott);
                            fputcsv($fp, $headerempty);
                            foreach ($all_conv_landing_page as $row_con) {                         
                                $percent = sprintf("%.2f", ($row_con['total_conv'] * 100) / $row_con['total_visits'], 2).'%';
                                $arrbottbl = array('','',$row_con['url'],$row_con['total_visits'],$row_con['total_conv'],$percent);
                                fputcsv($fp, $arrbottbl);
                            }
                            if(empty($all_conv_landing_page)){
                                $arrbottbl = array('','','No Landing page Found','','','');
                                fputcsv($fp, $arrbottbl);
                            }                    
                        }

                        fputcsv($fp, $headerempty);
                        fputcsv($fp, $headerempty);
                        fputcsv($fp, $headerempty);
                        fputcsv($fp, $headerempty);
                        fputcsv($fp, $headerempty);            
                    }                    
                    
                    ob_flush(); 
                    fclose($fp);
                    
                    $all_sent_email = array();
                    $report_full_name = 'Conversion Report';
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
}