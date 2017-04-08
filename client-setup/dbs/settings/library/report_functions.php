<?php

/* Functions */
error_reporting(0);
function rd_pdf_header() { 
    global $wpdb;    
    $website = rd_website_format(site_url());
    $logo = site_url()."/wp-content/themes/twentytwelve/images/new/logo-2.png";
    $padflogo = ABSPATH."wp-content/plugins/settings/uploads/pdf_logo.jpg";                                
    if(file_exists($padflogo) == true){
        $logo = site_url().'/wp-content/plugins/settings/uploads/pdf_logo.jpg?'.time();        
    }    
    return '<table border="0" width="100%"><tr><td align="left" ><img style="width:300px;height:27px;"  src="' . $logo . '"></td> <td style="font-size:25px;" align="right" width="20%">' . $website . '</td></tr></table><br/>';
}
function rd_website_format($website) {

    $website = rtrim($website, '/\\');

    $website = str_replace(array('http://', 'https://'), "", $website);

    return $website;
}

function countlocation_keywords($UserID){
    global $wpdb;    
            
    $meta_table = $wpdb->prefix."usermeta";
    $res = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT count(user_id) as total FROM $meta_table where user_id = %d AND meta_key like '%s' AND meta_value != '' ", $UserID, 'LE_Repu_Keyword_%'
        )
    ); 
    
    if($res->total == 0) return "N/A";
        
    return $res->total;
}


function rd_historical_executive_report_data($user_id, $all_active_target_url, $date_time, $current_data = 0, $compare_change_data = array()) {

    include_once get_template_directory() . '/analytics/my_functions.php';
    $match_ranking_url = array();
    if ($current_data == 0) {
        $to_date = date('Y-m-d', strtotime($date_time) + 8 * 24 * 3600);
        $from_date = $date_time;
        $sql = "SELECT CurrentRank, RankingURL FROM `seo_history` WHERE `MCCUserId` = $user_id and `DateOfRank` >= '$from_date 00:00:00' and `DateOfRank` <= '$to_date 23:59:59' group by `Keyword` order by `DateOfRank` asc";
    } else {
        $sql = "SELECT CurrentRank, RankingURL FROM `seo` WHERE `MCCUserId` = $user_id group by `Keyword` order by `DateOfRank` asc";
    }

    $date_result = result_array($sql);
    if (empty($date_result)) {
        $collect_first_date = row_array("SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = $user_id order by `DateOfRank` asc LIMIT 1");
        $collect_first_date = date("Y-m-d", strtotime($collect_first_date['DateOfRank']));
        $collect_last_date = date('Y-m-d', strtotime($collect_first_date) + 30 * 24 * 3600);
        $sql = "SELECT CurrentRank, RankingURL FROM `seo_history` WHERE `MCCUserId` = $user_id and `DateOfRank` >= '$collect_first_date 00:00:00' and `DateOfRank` <= '$collect_last_date 23:59:59' group by `Keyword` order by `DateOfRank` asc";
        $date_result = result_array($sql);
    }

    $total_rank = $first_place_rank = $top_3_rank = $top_10_rank = $total_keywords_rank = 0;
    $count_date_result = count($date_result);
    foreach ($date_result as $row_result) {
        
        $get_current_rank = $row_result['CurrentRank'];
        $RankingURL = $row_result['RankingURL'];

        $RankingURL = str_replace(array('http://', 'https://', 'www.'), "", $RankingURL);
        $RankingURL = rtrim($RankingURL, '/\\');
        if (in_array($RankingURL, $all_active_target_url)) {
            $match_ranking_url[] = $RankingURL;
        }
        if ($get_current_rank > 0) {
            $total_rank += 1;
            $total_keywords_rank += $get_current_rank;
        } else {
            $total_keywords_rank += 50;
            $pos_no_rank++;
        }
        if ($get_current_rank == 1) {
            $first_place_rank += 1;
        }
        if ($get_current_rank >= 1 && $get_current_rank <= 3) {
            $top_3_rank += 1;
        }
        if ($get_current_rank >= 1 && $get_current_rank <= 10) {
            $top_10_rank += 1;
        }

        if ($get_current_rank >= 4 && $get_current_rank <= 10) {
            $pos_4_10_rank += 1;
        }
        if ($get_current_rank >= 11 && $get_current_rank <= 20) {
            $pos_11_20_rank += 1;
        }
        if ($get_current_rank >= 21 && $get_current_rank <= 50) {
            $pos_21_50_rank += 1;
        }
    }
    
    $rank_result['total_keywords'] = $count_date_result;
    $rank_result['total_rank'] = sprintf("%.2f", ($total_rank / $count_date_result) * 100);
    $rank_result['total_rank_number'] = $total_rank;
    $rank_result['first_place'] = $first_place_rank;
    $rank_result['top_3'] = $top_3_rank;
    $rank_result['top_10'] = $top_10_rank;

    $rank_result['pos_no_rank'] = $pos_no_rank;
    $rank_result['pos_4_10'] = $pos_4_10_rank;
    $rank_result['pos_11_20'] = $pos_11_20_rank;
    $rank_result['pos_21_50'] = $pos_21_50_rank;

    $rank_result['avg_rank'] = sprintf("%.2f", $total_keywords_rank / $count_date_result);
    if ($rank_result['avg_rank'] < 0) {
        $rank_result['avg_rank'] = 50;
    }

    $match_ranking_url = array_unique($match_ranking_url);
    $count_match_ranking_url = count($match_ranking_url);
    $rank_result['rank_vs_target'] = sprintf("%.2f", ($count_match_ranking_url / count($all_active_target_url)) * 100);

    if (!empty($compare_change_data)) {
        $total_keywords_change = $compare_change_data['total_keywords'] - $rank_result['total_keywords'];
        $total_rank_change = $compare_change_data['total_rank'] - $rank_result['total_rank'];
        $total_rank_change = sprintf("%.2f", $total_rank_change);
        $avg_rank_change = $rank_result['avg_rank'] - $compare_change_data['avg_rank'];
        $avg_rank_change = sprintf("%.2f", $avg_rank_change);
        $rank_vs_target_change = $compare_change_data['rank_vs_target'] - $rank_result['rank_vs_target'];
        $total_rank_number_change = $compare_change_data['total_rank_number'] - $rank_result['total_rank_number'];
        $rank_vs_target_change = sprintf("%.2f", $rank_vs_target_change);
        $first_place_change = $compare_change_data['first_place'] - $rank_result['first_place'];
        $top_3_change = $compare_change_data['top_3'] - $rank_result['top_3'];
        $top_10_change = $compare_change_data['top_10'] - $rank_result['top_10'];

        $rank_result['total_keywords_change'] = color_set($total_keywords_change);
        $rank_result['total_rank_number_change'] = color_set($total_rank_number_change);
        $rank_result['total_rank_change'] = color_set($total_rank_change, 1);
        $rank_result['avg_rank_change'] = color_set($avg_rank_change);
        $rank_result['rank_vs_target_change'] = color_set($rank_vs_target_change, 1);
        $rank_result['first_place_change'] = color_set($first_place_change);
        $rank_result['top_3_change'] = color_set($top_3_change);
        $rank_result['top_10_change'] = color_set($top_10_change);
    }
    return $rank_result;
}

function rank_change_func($rank_change) {
    $arrow_class = '';
    if ($rank_change > 0) {
        $arrow_class = 'green_arrow';
    } else if ($rank_change < 0) {
        $arrow_class = 'red_arrow';
    } else {
        $arrow_class = 'blue_arrow';
    }
    return $arrow_class;
}

function get_ranking_data($search_type, $RankingData, $prev_RankingData) {

    $key_index = array_search($search_type, $RankingData);
    $ranking_url_index = $key_index + 1;
    $rank_index = $key_index + 2;
    $google_places_CurrentRank = $google_places_CurrentRank_text = $RankingData[$rank_index];
    $rank_result['RankingURL'] = $RankingData[$ranking_url_index];

    $key_index = array_search($search_type, $prev_RankingData);
    $rank_index = $key_index + 2;
    $google_places_prev_CurrentRank = $google_places_prev_CurrentRank_text = $prev_RankingData[$rank_index];

    if ($google_places_CurrentRank == 0 || $google_places_CurrentRank == 50) {
        $google_places_CurrentRank_text = '50+';
        $google_places_CurrentRank = 50;
    }
    if ($google_places_prev_CurrentRank == 0 || $google_places_prev_CurrentRank == 50) {
        $google_places_prev_CurrentRank_text = '50+';
        $google_places_prev_CurrentRank = 50;
    }
    $google_places_rank_change = $google_places_prev_CurrentRank - $google_places_CurrentRank;
    $rank_result['CurrentRank'] = $google_places_CurrentRank_text;
    $rank_result['prev_CurrentRank'] = $google_places_prev_CurrentRank_text;
    $rank_result['rank_change'] = $google_places_rank_change;
    $rank_result['arrow_class'] = rank_change_func($google_places_rank_change);

    return $rank_result;
}

function rd_site_audit_info($user_id){
    global $wpdb;
    $site_audit = $wpdb->prefix.'site_audit';
    $sql = "SELECT all_info, last_audit FROM $site_audit WHERE `user_id` = $user_id && `audit_status` = 'Completed' order by `id` desc LIMIT 1";
    $site_audit_info = $wpdb->get_row($sql);
    return $site_audit_info;
}

function rd_citation_tracker($user_id){
    global $wpdb;
    $site_audit = $wpdb->prefix.'citation_tracker';
    $sql = "SELECT citations_data, last_run FROM `wp_citation_tracker` WHERE `user_id` = $analytics_user_id order by `last_run` desc LIMIT 1";
    $result_info = $wpdb->get_row($sql);
    return $result_info;
}

?>