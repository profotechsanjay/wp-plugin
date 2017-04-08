<?php
$isDwnld = isset($_POST['dwnld_type']) ? true : false;
$all_conv_landing_page = conversions_report($analytics_user_id, $from_date, $to_date);

$conv_results_by_url = array();
foreach ($all_conv_landing_page as $row_conv_url) {
    $cur_conv_url = fully_trim($row_conv_url['url']);
    $conv_results_by_url[$cur_conv_url] = $row_conv_url['total_conv'];
}

$all_active_keywords = user_limited_keywords($UserID, $start, $limit);

$AssignedValue = 0.21;
$client_website = rtrim(get_user_meta($UserID, 'website', true), '/\\');
$total_organic_arr = array();
$top_performing_keywords = $top_old_keywords = array();
$keyword_total_organic = array();
$keyword_rank_change_image = array();
$ConvCount_RankingURL = array();
$conv_tracking_urls_arr = array();
$improved_ranking = $lost_ranking = $same_rank = 0;
$all_keywords = result_array("SELECT * FROM `seo` WHERE `MCCUserId` = $UserID order by Keyword asc");

//$sql = 'select * from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%"';
//$keys = $wpdb->get_results($sql);

$primary_html = '<span class="badge" style="background:#22B04B; margin-right:6px;">P</span>';
$secondary_html = '<span class="badge" style="background:#FF7F27; margin-right:6px; margin-left:10px;">S</span>';

$all_ranking_url = result_array("SELECT RankingURL FROM `seo` WHERE `MCCUserId` = $UserID and `RankingURL` != '' group by `RankingURL` order by `RankingURL` asc");
$url_con = '';
if (!empty($all_ranking_url)) {
    foreach ($all_ranking_url as $row_r_url) {
        $r_url = $row_r_url['RankingURL'];
        $r_url = str_replace(array('http://', 'https://'), "", $r_url);
        if ($url_con == '') {
            $url_con .= " PageURL = '$r_url'";
        } else {
            $url_con .= " OR PageURL = '$r_url'";
        }
    }
}


$organic_results_by_url = array();
$total_visit_by_url = array();

if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'")) == 1) {
    $sql = "SELECT `PageURL`,sum(`organic`) as total_organic, sum( `Total` ) AS total_visit FROM `short_analytics_$analytics_user_id` WHERE  `DateOfVisit` >= '$from_date' AND `DateOfVisit` <= '$to_date' AND ($url_con) group by `PageURL`";
    $organic_data = result_array($sql);
    //pr($organic_data);
    foreach ($organic_data as $row_url_organic) {
        $original_pageurl = fully_trim($row_url_organic['PageURL']);
        $organic_results_by_url[$original_pageurl] = $row_url_organic['total_organic'];
        $total_visit_by_url[$original_pageurl] = $row_url_organic['total_visit'];
    }
}
//pr($organic_results_by_url);






$month = date('Y-m', strtotime($from_date));
$sql = "SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = $UserID and `DateOfRank` >= '{$month}-01 00:00:00' ORDER BY DateOfRank ASC LIMIT 1";
$DateOfRank_arr = row_array($sql);
if (!empty($DateOfRank_arr)) {
    $DateOfRank_arr = $DateOfRank_arr['DateOfRank'];
} else {
    $DateOfRank_arr = date('Y-m-d', strtotime('last sunday'));
}

$Content_keyword_Site = get_user_meta($UserID, "Content_keyword_Site", true);
//pr($Content_keyword_Site);
$Synonyms_keyword_arr = $Content_keyword_Site['Synonyms_keyword'];
$activation = $Content_keyword_Site['activation'];
$target_keyword = $Content_keyword_Site["target_keyword"];

$formula = row_array("SELECT * FROM seorv_formula");
$rank_values = result_array("SELECT * FROM rank_values");

$google_html = $pimary_g_html = '';
$bing_html = $pimary_b_html = '';
$yahoo_html = $pimary_y_html = '';
$position1 = $position10 = $position20 = $position50 = $noposition = $total_pos = $tot_organic = $total_ov_lp = 0;
$position1_yahoo = $position10_yahoo = $position20_yahoo = $position50_yahoo = $noposition_yahoo = $total_pos_yahoo = $tot_organic_yahoo = $total_ov_lp_yahoo = 0;
$position1_bing = $position10_bing = $position20_bing = $position50_bing = $noposition_bing = $total_pos_bing = $tot_organic_bing = $total_ov_lp_bing = 0;
$ranked_pages = $old_ranked_pages = $page1_ranking = $old_page1_ranking = $sum_of_total = $sum_of_old_total = 0;
$ranked_pages_yahoo = $old_ranked_pages_yahoo = $page1_ranking_yahoo = $old_page1_ranking_yahoo = $sum_of_total_yahoo = $sum_of_old_total_yahoo = 0;
$ranked_pages_bing = $old_ranked_pages_bing = $page1_ranking_bing = $old_page1_ranking_bing = $sum_of_total_bing = $sum_of_old_total_bing = 0;
$no1_rank_count = $no1_old_rank_count = $no2_rank_count = $no2_old_rank_count = $no3_rank_count = $no3_old_rank_count = 0;
$no1_rank_count_yahoo = $no1_old_rank_count_yahoo = $no2_rank_count_yahoo = $no2_old_rank_count_yahoo = $no3_rank_count_yahoo = $no3_old_rank_count_yahoo = 0;
$no1_rank_count_bing = $no1_old_rank_count_bing = $no2_rank_count_bing = $no2_old_rank_count_bing = $no3_rank_count_bing = $no3_old_rank_count_bing = 0;

$google_html .= '<div class="row">
	<div class="col-sm-12">
		<div class="clearfix margin-bottom-5"> </div>';
$bing_html .= '<div class="row">
	<div class="col-sm-12">
		<div class="clearfix margin-bottom-5"> </div>';
$yahoo_html .= '<div class="row">
	<div class="col-sm-12">
		<div class="clearfix margin-bottom-5"> </div>';
//pr($all_active_keywords);
$key_index = -1;

$pi = $pitable = 1;

// Rank Bucket code 
$user_id = $UserID;
$all_seo_table_data = result_array("SELECT * FROM `seo` WHERE `MCCUserId` = $user_id");

foreach ($all_seo_table_data as $row_s_data) {
    $t_key = trim(strtolower($row_s_data['Keyword']));
    $seo_data_arr[$t_key]['Keyword'] = $t_key;
    $seo_data_arr[$t_key]['RankingURL'] = $row_s_data['RankingURL'];
    $seo_data_arr[$t_key]['CurrentRank'] = $row_s_data['CurrentRank'];
    $seo_data_arr[$t_key]['DateOfRank'] = $row_s_data['DateOfRank'];
}
$none_value = 'N/A';
// end
//pr($all_active_keywords);exit;
foreach ($all_active_keywords as $table_index => $row_active_key) {


    //$syn_keywords_index = explode('_', get_meta_key($UserID, $row_active_key, true));
    ///*
    $same_keywords = $wpdb->get_results('SELECT meta_key FROM `wp_usermeta` WHERE `user_id` =' . $UserID . '  AND (`meta_value` LIKE "' . $row_active_key . '" || `meta_value` LIKE "' . $row_active_key . '") ORDER BY `umeta_id` DESC');
    //pr($same_keywords);exit;
    $row_active_key = str_replace("'", "", $row_active_key);
    $Synonyms_keyword = array();
    $target_keyword_value = '';
    foreach ($same_keywords as $row_same_key) {
        $syn_keywords_index = explode('_', $row_same_key->meta_key);
        $syn_keywords_index = $syn_keywords_index[count($syn_keywords_index) - 1];
        //echo $syn_keywords_index.'<br/>';
        if ($activation[$syn_keywords_index - 1] != 'inactive') {
            $Synonyms_keyword = $Synonyms_keyword_arr[$syn_keywords_index - 1];
            $Synonyms_keyword = array_filter($Synonyms_keyword);
            $target_keyword_value = $target_keyword[$syn_keywords_index - 1];
        }
    }
    // echo '<div class="clear_both"></div>';
    //$syn_keywords_index = $syn_keywords_index[count($syn_keywords_index) - 1];
    //$Synonyms_keyword = $Synonyms_keyword_arr[$syn_keywords_index - 1];

    $primary_and_synonyms_key = array();
    $primary_and_synonyms_key[] = $row_active_key;
    if (!empty($Synonyms_keyword)) {
        $Synonyms_keyword = array_map('trim', $Synonyms_keyword);
        $primary_and_synonyms_key = array_merge($primary_and_synonyms_key, $Synonyms_keyword);
    }
    //pr($primary_and_synonyms_key);
    //pr($primary_and_synonyms_key);exit;

    $resptarget = '';
    if ($target_keyword_value == 'Yes') {
        $resptarget = '_target';
    }

    $google_html .= '<section class="sectionC">
            <table class="tabl1 table table-striped table-bordered table-hover secondary-table narrow-table respTbl' . $resptarget . '" >
                <thead>
                    <tr>
                        <th style="width:25%!important;">Keyword</th>
                        <th style="width:25%!important;">Google Ranking URL</th>
                        <th style="width:9%!important;">Google Rank</th>';
    $bing_html .= '<section class="sectionC">
            <table class="tabl1 table table-striped table-bordered table-hover secondary-table narrow-table respTbl' . $resptarget . '" >
                <thead>
                    <tr>
                        <th style="width:25%!important;">Keyword</th>
                        <th style="width:25%!important;">Bing Ranking URL</th>
                        <th style="width:9%!important;">Bing Rank</th>';
    $yahoo_html .= '<section class="sectionC">
            <table class="tabl1 table table-striped table-bordered table-hover secondary-table narrow-table respTbl' . $resptarget . '" >
                <thead>
                    <tr>
                        <th style="width:25%!important;">Keyword</th>
                        <th style="width:25%!important;">Yahoo Ranking URL</th>
                        <th style="width:9%!important;">Yahoo Rank</th>';
    if ($targeting == 'local') {
        $google_html .= '<th>Google Local Rank</th>';
        $bing_html .= '<th>Bing Local Rank URL</th>
                       <th style="width:9%!important;">Bing Local Rank</th>';
        $yahoo_html .= '<th>Yahoo Local Rank URL</th>
                       <th style="width:9%!important;">Yahoo Local Rank</th>';
    }
    $google_html .= '<th>SEOv</th>
                        <th>Organic<br>Visits</th>
                        <th>Total<br>Conv</th>
                        <th>Conv Rate</th>
                        <th>Avg<br> Monthly<br> Searches</th>
                        <th>Competition</th>
                        <th>Suggested Bid</th>
                        <th>Bucket</th>
                        <th>Days in Bucket</th>
                        <th>% of Time</th>
                    </tr>
                </thead>
                <tbody>';
    $bing_html .= '</tr>
                </thead>
                <tbody>';
    $bing_html .= '</tr>
                </thead>
                <tbody>';

    if ($pi == 1) {
        $pitable++;
        $pimary_g_html .= '<section class="sectionC">
						<table class="tabl1 table table-striped table-bordered table-hover narrow-table primary-table respTbl' . $resptarget . '">
							<thead>
								<tr>
									<th style="width:25%!important;">Keyword</th>
									<th style="width:25%!important;">Google Ranking URL</th>
									<th style="width:9%!important;">Google Rank</th>';
        $pimary_b_html .= '<section class="sectionC">
						<table class="tabl1 table table-striped table-bordered table-hover narrow-table primary-table  respTbl' . $resptarget . '">
							<thead>
								<tr>
									<th style="width:25%!important;">Keyword</th>
									<th style="width:25%!important;">Bing Ranking URL</th>
									<th style="width:9%!important;">Bing Rank</th>';
        $pimary_y_html .= '<section class="sectionC">
						<table class="tabl1 table table-striped table-bordered table-hover narrow-table primary-table  respTbl' . $resptarget . '">
							<thead>
								<tr>
									<th style="width:25%!important;">Keyword</th>
									<th style="width:25%!important;">Yahoo Ranking URL</th>
									<th style="width:9%!important;">Yahoo Rank</th>';
        if ($targeting == 'local') {
            $pimary_g_html .= '<th>Google Local Rank</th>';
            $pimary_b_html .= '<th style="width:9%!important;">Bing Local Rank</th>
                               <th>Bing Local Rank URL</th>';
            $pimary_b_html .= '<th style="width:9%!important;">Yahoo Local Rank</th>
                               <th>Yahoo Local Rank URL</th>';
        }
        $pimary_g_html .= '<th>SEOv</th>
									<th>Organic<br>Visits</th>
									<th>Total<br>Conv</th>
									<th>Conv Rate</th>
									<th>Avg<br> Monthly<br> Searches</th>
									<th>Competition</th>
									<th>Suggested Bid</th>
                                                                        <th>Bucket</th>
                                                                        <th>Days in Bucket</th>
                                                                        <th>% of Time</th>
								</tr>
							</thead>
							<tbody>';
        $pimary_b_html .= '</tr>
							</thead>
							<tbody>';
        $pimary_y_html .= '</tr>
							</thead>
							<tbody>';
    }

//pr($all_keywords);
//pr($all_active_keywords);

    $table_sort_order = '0';
    foreach ($primary_and_synonyms_key as $index_ps => $row_ps_key) {
        $row_key_data = row_array('SELECT * FROM `seo` WHERE `MCCUserId` = ' . $UserID . ' and Keyword = "' . $row_ps_key . '" order by Keyword asc');
        $URL = fully_trim($row_key_data['RankingURL']);
        $Keyword = $row_key_data['Keyword'];
        $RankingURL = $row_key_data['RankingURL'];
        $CurrentRank = $row_key_data['CurrentRank'];

        $RankingData = explode(',}/$^&', $row_key_data['RankingData']);

        //get bing ranking data
        $search_location_b = 'bing';

        $key_index_bing = array_search($search_location_b, $RankingData);

        $ranking_url_index_b = $key_index_bing + 1;
        $rank_index_bing = $key_index_bing + 2;
        $RankingURL_bing = $RankingData[$ranking_url_index_b];
        $CurrentRank_bing = $RankingData[$rank_index_bing];

        //yahoo ranking data
        $search_location_y = 'yahoo';

        $key_index_yahoo = array_search($search_location_y, $RankingData);

        $ranking_url_index_y = $key_index_yahoo + 1;
        $rank_index_yahoo = $key_index_yahoo + 2;
        $RankingURL_yahoo = $RankingData[$ranking_url_index_y];
        $CurrentRank_yahoo = $RankingData[$rank_index_yahoo];

        //$info_yahoo = row_array($qy);

        if ($CurrentRank == 0) {
            $CurrentRank = 50;
        }
        if ($CurrentRank_bing == 0) {
            $CurrentRank_bing = 50;
        }
        if ($CurrentRank_yahoo == 0) {
            $CurrentRank_yahoo = 50;
        }


        $top_performing_keywords[$Keyword] = $CurrentRank;
        // previous month code start
        $sql = 'SELECT * FROM `seo_history` WHERE `MCCUserId` = ' . $UserID . ' and `Keyword` = "' . $Keyword . '" and `DateOfRank` >= "' . $month . '-01 00:00:00" ORDER BY DateOfRank ASC  LIMIT 1';
        //echo $sql;exit;
        // end
        //$sql = "SELECT * FROM `seo_history` WHERE `MCCUserId` = $UserID and `Keyword` = '$Keyword' order by DateOfRank desc,CurrentRank desc LIMIT 1";

        $get_dup_keyword = row_array($sql);
        $rank_change = $oldRank = $SEOv_change = $cal_SEOv = 0;

        $params = array(
            "formula" => $formula,
            "rank_values" => $rank_values,
            "cpc" => MoneyFromMicros($get_dup_keyword['CPC']),
            "difficulty" => $get_dup_keyword['Difficulty'],
            "volume" => $get_dup_keyword['GoogleSearchVolume'],
            "rank" => $get_dup_keyword['CurrentRank']
        );

        $tmp = calcSEOrv($params);

        //old rank google
        if (!empty($get_dup_keyword)) {
            $oldRank = $get_dup_keyword['CurrentRank'];
            if ($oldRank == 0) {
                $oldRank = 50;
            }
            $rank_change = $oldRank - $CurrentRank;
            if ($get_dup_keyword['SEOV'] > 0) {
                $cal_SEOv = $get_dup_keyword['SEOV'];
            } else {
                $CPC = MoneyFromMicros($get_dup_keyword['CPC']);
                $RankV = CalcRankV($get_dup_keyword['CurrentRank']);
                $cal_SEOv = floatval($get_dup_keyword['GoogleSearchVolume']) * floatval($get_dup_keyword['Difficulty']) * floatval($CPC) * floatval($AssignedValue) * floatval($RankV);
                mysql_query("UPDATE `seo_history` SET `SEOV` = '$cal_SEOv' WHERE `Id` = {$get_dup_keyword['Id']};");
            }

            $top_old_keywords[$Keyword] = $oldRank;
        } else {
            if ($oldRank == 0) {
                $oldRank = 50;
            }
            $rank_change = $oldRank - $CurrentRank;
            //$SEOv_change = ????

            $top_old_keywords[$Keyword] = $oldRank;
        }
        //end old rank google
        //old rank bing
        if (!empty($get_dup_keyword)) {
            $RankingDataOld = explode(',}/$^&', $get_dup_keyword['RankingData']);

            //get bing old ranking data
            $search_location_old_b = 'bing';

            $key_index_old_bing = array_search($search_location_old_b, $RankingDataOld);

            $ranking_url_index_old_b = $key_index_old_bing + 1;
            $rank_index_old_bing = $key_index_old_bing + 2;
            $ranking_url_old_bing = $RankingDataOld[$ranking_url_index_old_b];
            $oldRank_bing = $RankingDataOld[$rank_index_old_bing];

            //$oldRank_bing = $RankingDataOld[11];            
            if ($oldRank_bing == 0) {
                $oldRank_bing = 50;
            }
            $rank_change_bing = $oldRank_bing - $CurrentRank_bing;

            $top_old_keywords_bing[$Keyword] = $oldRank_bing;
        } else {
            if ($oldRank_bing == 0) {
                $oldRank_bing = 50;
            }
            $rank_change_bing = $oldRank_bing - $CurrentRank_bing;
            //$SEOv_change = ????

            $top_old_keywords_bing[$Keyword] = $oldRank_bing;
        }
        //end old rank bing
        //old rank yahoo
        if (!empty($get_dup_keyword)) {
            $RankingDataOld = explode(',}/$^&', $get_dup_keyword['RankingData']);
            //get bing old ranking data
            $search_location_old_y = 'yahoo';

            $key_index_old_yahoo = array_search($search_location_old_y, $RankingDataOld);

            $ranking_url_index_old_y = $key_index_old_yahoo + 1;
            $rank_index_old_yahoo = $key_index_old_yahoo + 2;
            $ranking_url_old_yahoo = $RankingDataOld[$ranking_url_index_old_y];
            $oldRank_yahoo = $RankingDataOld[$rank_index_old_yahoo];
            //$oldRank_yahoo = $RankingDataOld[8];            
            if ($oldRank_yahoo == 0) {
                $oldRank_yahoo = 50;
            }
            $rank_change_yahoo = $oldRank_yahoo - $CurrentRank_yahoo;

            $top_old_keywords_yahoo[$Keyword] = $oldRank_yahoo;
        } else {
            if ($oldRank_yahoo == 0) {
                $oldRank_yahoo = 50;
            }
            $rank_change_yahoo = $oldRank_yahoo - $CurrentRank_yahoo;
            //$SEOv_change = ????

            $top_old_keywords_bing[$Keyword] = $oldRank_yahoo;
        }
        //end old rank yahoo

        $Current_CPC = MoneyFromMicros($row_key_data['CPC']);
        $Current_RankV = CalcRankV($CurrentRank);

        if ($row_key_data['SEOV'] > 0) {
            $Current_cal_SEOv = $row_key_data['SEOV'];
        } else {
            $Current_CPC = MoneyFromMicros($row_key_data['CPC']);
            $Current_RankV = CalcRankV($CurrentRank);
            $Current_cal_SEOv = floatval($row_key_data['GoogleSearchVolume']) * floatval($row_key_data['Difficulty']) * floatval($Current_CPC) * floatval($AssignedValue) * floatval($Current_RankV);
            mysql_query("UPDATE `seo` SET `SEOV` = '$Current_cal_SEOv' WHERE `Id` = {$row_key_data['Id']};");
        }
        $SEOv_change = round($Current_cal_SEOv - $cal_SEOv, 2);


        //  ob_start();

        $primary = "";

        $key_index++;
        $row_class = '';
        if ($index_ps == 0) {
            $key_cal = $primary_html;
            $key_type = 'primary';
            $row_class = 'primary-row';
        } else {
            $key_cal = $secondary_html;
            $key_type = 'secondary';
            $row_class = 'secondary-row';
        }
        $target_key_html = '';
        if ($target_keyword_value == "Yes" && $index_ps == 0) {
            $target_key_html = '<span class="target_key badge" style="background:blue;">T</span>';
        }

        $google_html .= '<tr class="' . $row_class . '">
                            <td class="cl-1" style="width:25%!important;">' . $target_key_html . $primary . '
                                <a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $row_ps_key) . '">' . $key_cal . $row_ps_key . '</a></td>';
        $bing_html .= '<tr class="' . $row_class . '">
                            <td class="cl-1" style="width:25%!important;">' . $target_key_html . $primary . '
                                <a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $row_ps_key) . '">' . $key_cal . $row_ps_key . '</a></td>';
        $yahoo_html .= '<tr class="' . $row_class . '">
                            <td class="cl-1" style="width:25%!important;">' . $target_key_html . $primary . '
                                <a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $row_ps_key) . '">' . $key_cal . $row_ps_key . '</a></td>';
        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = $row_ps_key;
        $google_html .= ' <td class="cl-2" style="width:25%!important;">';
        $bing_html .= ' <td class="cl-2" style="width:25%!important;">';
        $yahoo_html .= ' <td class="cl-2" style="width:25%!important;">';

        //type of url google
        $url_type_style = '';
        $RankingURL = explode("?", $RankingURL);
        $RankingURL = $RankingURL[0];
        $tt = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $RankingURL);

        $isLp = false;
        if (in_array($URL, $target_url)) {
            $isLp = true;
            $url_type_style = "<span class='badge' style='background:green;margin-right:7px;'>T</span>";
        }
        // end type of url google
        //type of url bing
        $url_type_style_bing = '';
        $RankingURL_bing = explode("?", $RankingURL_bing);
        $RankingURL_bing = $RankingURL_bing[0];
        $tt_bing = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $RankingURL_bing);

        $isLp_bing = false;
        $URL_bing = fully_trim($RankingURL_bing);
        if (in_array($URL_bing, $target_url)) {
            $isLp_bing = true;
            $url_type_style_bing = "<span class='badge' style='background:green;margin-right:7px;'>T</span>";
        }
        // end type of url bing
        //type of url yahoo
        $url_type_style_yahoo = '';
        $RankingURL_yahoo = explode("?", $RankingURL_yahoo);
        $RankingURL_yahoo = $RankingURL_yahoo[0];
        $tt_yahoo = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $RankingURL_yahoo);

        $isLp_yahoo = false;
        $URL_yahoo = fully_trim($RankingURL_yahoo);
        if (in_array($URL_yahoo, $target_url)) {
            $isLp_yahoo = true;
            $url_type_style_yahoo = "<span class='badge' style='background:green;margin-right:7px;'>T</span>";
        }

        // end type of url yahoo

        $google_html .= $url_type_style . '
                                <a title="' . $RankingURL . '" href="' . site_url() . '/url-profile/?url=' . $RankingURL . '">' . $tt . '</a>
                            </td>';
        $bing_html .= $url_type_style_bing . '
                                <a title="' . $RankingURL_bing . '" href="' . site_url() . '/url-profile/?url=' . $RankingURL_bing . '">' . $tt_bing . '</a>
                            </td>';
        $yahoo_html .= $url_type_style_yahoo . '
                                <a title="' . $RankingURL_yahoo . '" href="' . site_url() . '/url-profile/?url=' . $RankingURL_yahoo . '">' . $tt_yahoo . '</a>
                            </td>';

        if ($index_ps == 0) {
            $pimary_g_html .= '<tr class="' . $row_class . '">
									<td class="cl-1" style="width:25%!important;">' . $target_key_html . $primary . '
									<a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $row_ps_key) . '">' . $key_cal . $row_ps_key . '</a></td>
									<td class="cl-2" style="width:25%!important;">' . $url_type_style . '
										<a title="' . $RankingURL . '" href="' . site_url() . '/url-profile/?url=' . $RankingURL . '">' . $tt . '</a>
									</td>';
            $pimary_b_html .= '<tr class="' . $row_class . '">
									<td class="cl-1" style="width:25%!important;">' . $target_key_html . $primary . '
									<a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $row_ps_key) . '">' . $key_cal . $row_ps_key . '</a></td>
									<td class="cl-2" style="width:25%!important;">' . $url_type_style_bing . '
										<a title="' . $RankingURL_bing . '" href="' . site_url() . '/url-profile/?url=' . $RankingURL_bing . '">' . $tt_bing . '</a>
									</td>';
            $pimary_y_html .= '<tr class="' . $row_class . '">
									<td class="cl-1" style="width:25%!important;">' . $target_key_html . $primary . '
									<a href="' . site_url() . '/keyword-profile/?keyword=' . str_replace(" ", "-", $row_ps_key) . '">' . $key_cal . $row_ps_key . '</a></td>
									<td class="cl-2" style="width:25%!important;">' . $url_type_style_yahoo . '
										<a title="' . $RankingURL_yahoo . '" href="' . site_url() . '/url-profile/?url=' . $RankingURL_yahoo . '">' . $tt_yahoo . '</a>
									</td>';
        }

        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $url_type_style . $RankingURL;
        $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = $RankingURL;

        $google_html .= '<td class="cl-3">';
        $bing_html .= '<td class="cl-3">';
        $yahoo_html .= '<td class="cl-3">';

        //pr($get_dup_keyword);
        //rank change google
        if ($rank_change == '0' || $rank_change == '50') {
            $same_rank++;
            $arrow_image = 'blue';
            $keyword_rank_change_image[$Keyword] = $arrow_image;
        } elseif ($rank_change > 0) {
            $improved_ranking++;
            $arrow_image = 'green';
            $keyword_rank_change_image[$Keyword] = $arrow_image;
        } elseif ($rank_change < 0) {
            $lost_ranking++;
            $arrow_image = 'red';
            $keyword_rank_change_image[$Keyword] = $arrow_image;
        }
        //end rank change google
        //rank change bing
        if ($rank_change_bing == '0' || $rank_change_bing == '50') {
            $same_rank_bing++;
            $arrow_image_bing = 'blue';
            $keyword_rank_change_image_bing[$Keyword] = $arrow_image_bing;
        } elseif ($rank_change_bing > 0) {
            $improved_ranking_bing++;
            $arrow_image_bing = 'green';
            $keyword_rank_change_image_bing[$Keyword] = $arrow_image_bing;
        } elseif ($rank_change_bing < 0) {
            $lost_ranking_bing++;
            $arrow_image_bing = 'red';
            $keyword_rank_change_image_bing[$Keyword] = $arrow_image_bing;
        }
        //end rank change bing
        //rank change yahoo
        if ($rank_change_yahoo == '0' || $rank_change_yahoo == '50') {
            $same_rank_yahoo++;
            $arrow_image_yahoo = 'blue';
            $keyword_rank_change_image_yahoo[$Keyword] = $arrow_image_yahoo;
        } elseif ($rank_change_yahoo > 0) {
            $improved_ranking_yahoo++;
            $arrow_image_yahoo = 'green';
            $keyword_rank_change_image_yahoo[$Keyword] = $arrow_image_yahoo;
        } elseif ($rank_change_yahoo < 0) {
            $lost_ranking_yahoo++;
            $arrow_image_yahoo = 'red';
            $keyword_rank_change_image_yahoo[$Keyword] = $arrow_image_yahoo;
        }
        //end rank change yahoo
        //$view_rank = $CurrentRank == 0 ? '50+' : $CurrentRank;
        if ($CurrentRank == 0 || $CurrentRank == 50) {
            $view_rank = '50+';
        } else {
            $view_rank = $CurrentRank;
        }

        //$view_rank_bing = $CurrentRank == 0 ? '50+' : $CurrentRank;
        if ($CurrentRank_bing == 0 || $CurrentRank_bing == 50) {
            $view_rank_bing = '50+';
        } else {
            $view_rank_bing = $CurrentRank_bing;
        }

        //$view_rank_yahoo = $CurrentRank == 0 ? '50+' : $CurrentRank;
        if ($CurrentRank_yahoo == 0 || $CurrentRank_yahoo == 50) {
            $view_rank_yahoo = '50+';
        } else {
            $view_rank_yahoo = $CurrentRank_yahoo;
        }

        // google rank charts calc start
        if ($CurrentRank >= 1 && $CurrentRank <= 3) {
            $position1++;
        } else if ($CurrentRank >= 4 && $CurrentRank <= 10) {
            $position10++;
        } else if ($CurrentRank >= 11 && $CurrentRank <= 20) {
            $position20++;
        } else if ($CurrentRank >= 21 && $CurrentRank < 50) {
            $position50++;
        } else {
            $noposition++;
        }
        $total_pos++;

        if ($CurrentRank >= 1 && $CurrentRank < 50) {
            $ranked_pages++;
            $sum_of_total += $CurrentRank;
        } else {
            $sum_of_total += 50;
        }
        if ($oldRank >= 1 && $oldRank < 50) {
            $old_ranked_pages++;
            $sum_of_old_total += $oldRank;
        } else {
            $sum_of_old_total += 50;
        }

        if ($CurrentRank >= 1 && $CurrentRank <= 10) {
            $page1_ranking++;
        }
        if ($oldRank >= 1 && $oldRank <= 10) {
            $old_page1_ranking++;
        }

        if ($CurrentRank == 1) {
            $no1_rank_count++;
        }
        if ($oldRank == 1) {
            $no1_old_rank_count++;
        }
        if ($CurrentRank == 2) {
            $no2_rank_count++;
        }
        if ($oldRank == 2) {
            $no2_old_rank_count++;
        }
        if ($CurrentRank == 3) {
            $no3_rank_count++;
        }
        if ($oldRank == 3) {
            $no3_old_rank_count++;
        }
        // google rank charts calc end
        // bing rank charts calc start
        if ($CurrentRank_bing >= 1 && $CurrentRank_bing <= 3) {
            $position1_bing++;
        } else if ($CurrentRank_bing >= 4 && $CurrentRank_bing <= 10) {
            $position10_bing++;
        } else if ($CurrentRank_bing >= 11 && $CurrentRank_bing <= 20) {
            $position20_bing++;
        } else if ($CurrentRank_bing >= 21 && $CurrentRank_bing < 50) {
            $position50_bing++;
        } else {
            $noposition_bing++;
        }
        $total_pos_bing++;

        if ($CurrentRank_bing >= 1 && $CurrentRank_bing < 50) {
            $ranked_pages_bing++;
            $sum_of_total_bing += $CurrentRank_bing;
        } else {
            $sum_of_total_bing += 50;
        }
        if ($oldRank_bing >= 1 && $oldRank_bing < 50) {
            $old_ranked_pages_bing++;
            $sum_of_old_total_bing += $oldRank_bing;
        } else {
            $sum_of_old_total_bing += 50;
        }

        if ($CurrentRank_bing >= 1 && $CurrentRank_bing <= 10) {
            $page1_ranking_bing++;
        }
        if ($oldRank_bing >= 1 && $oldRank_bing <= 10) {
            $old_page1_ranking_bing++;
        }

        if ($CurrentRank_bing == 1) {
            $no1_rank_count_bing++;
        }
        if ($oldRank_bing == 1) {
            $no1_old_rank_count_bing++;
        }
        if ($CurrentRank_bing == 2) {
            $no2_rank_count_bing++;
        }
        if ($oldRank_bing == 2) {
            $no2_old_rank_count_bing++;
        }
        if ($CurrentRank_bing == 3) {
            $no3_rank_count_bing++;
        }
        if ($oldRank_bing == 3) {
            $no3_old_rank_count_bing++;
        }
        // bing rank charts calc end
        // yahoo rank charts calc start
        if ($CurrentRank_yahoo >= 1 && $CurrentRank_yahoo <= 3) {
            $position1_yahoo++;
        } else if ($CurrentRank_yahoo >= 4 && $CurrentRank_yahoo <= 10) {
            $position10_yahoo++;
        } else if ($CurrentRank_yahoo >= 11 && $CurrentRank_yahoo <= 20) {
            $position20_yahoo++;
        } else if ($CurrentRank_yahoo >= 21 && $CurrentRank_yahoo < 50) {
            $position50_yahoo++;
        } else {
            $noposition_yahoo++;
        }
        $total_pos_yahoo++;

        if ($CurrentRank_yahoo >= 1 && $CurrentRank_yahoo < 50) {
            $ranked_pages_yahoo++;
            $sum_of_total_yahoo += $CurrentRank_yahoo;
        } else {
            $sum_of_total_yahoo += 50;
        }
        if ($oldRank_yahoo >= 1 && $oldRank_yahoo < 50) {
            $old_ranked_pages_yahoo++;
            $sum_of_old_total_yahoo += $oldRank_yahoo;
        } else {
            $sum_of_old_total_yahoo += 50;
        }

        if ($CurrentRank_yahoo >= 1 && $CurrentRank_yahoo <= 10) {
            $page1_ranking_yahoo++;
        }
        if ($oldRank_yahoo >= 1 && $oldRank_yahoo <= 10) {
            $old_page1_ranking_yahoo++;
        }

        if ($CurrentRank_yahoo == 1) {
            $no1_rank_count_yahoo++;
        }
        if ($oldRank_yahoo == 1) {
            $no1_old_rank_count_yahoo++;
        }
        if ($CurrentRank_yahoo == 2) {
            $no2_rank_count_yahoo++;
        }
        if ($oldRank_yahoo == 2) {
            $no2_old_rank_count_yahoo++;
        }
        if ($CurrentRank_yahoo == 3) {
            $no3_rank_count_yahoo++;
        }
        if ($oldRank_yahoo == 3) {
            $no3_old_rank_count_yahoo++;
        }
        // yahoo rank charts calc end

        $google_html .= '<i>' . $view_rank . '</i>
                               
                                <span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
                                    &nbsp;
                                </span> ';
        $bing_html .= '<i>' . $view_rank_bing . '</i>
                               
                                <span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_bing . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
                                    &nbsp;
                                </span> ';
        $yahoo_html .= '<i>' . $view_rank_yahoo . '</i>
                               
                                <span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_yahoo . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
                                    &nbsp;
                                </span> ';
        $changeInRank = ($rank_change < 50) ? ($rank_change > 0 ? '+' : '') . $rank_change : '0';
        $changeInRank_bing = ($rank_change_bing < 50) ? ($rank_change_bing > 0 ? '+' : '') . $rank_change_bing : '0';
        $changeInRank_yahoo = ($rank_change_yahoo < 50) ? ($rank_change_yahoo > 0 ? '+' : '') . $rank_change_yahoo : '0';

        $google_html .= $changeInRank . '
                                <br />
                                <small>
                                    Previous ';
        $bing_html .= $changeInRank_bing . '
                                <br />
                                <small>
                                    Previous ';
        $yahoo_html .= $changeInRank_yahoo . '
                                <br />
                                <small>
                                    Previous ';
        $oldRank_str = ($oldRank == 50 ? '50+' : $oldRank);
        $oldRank_bing_str = ($oldRank_bing == 50 ? '50+' : $oldRank_bing);
        $oldRank_yahoo_str = ($oldRank_yahoo == 50 ? '50+' : $oldRank_yahoo);
        $google_html .= '<span style="color:red">' . $oldRank_str . '</span>
                                </small>
                            </td>';
        $bing_html .= '<span style="color:red">' . $oldRank_bing_str . '</span>
                                </small>
                            </td>';
        $yahoo_html .= '<span style="color:red">' . $oldRank_yahoo_str . '</span>
                                </small>
                            </td>';

        if ($index_ps == 0) {
            $pimary_g_html .= '<td class="cl-3"><i>' . $view_rank . '</i>
                               
									<span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
										&nbsp;
									</span>' . $changeInRank . '
									<br />
									<small>
										Previous <span style="color:red">' . $oldRank_str . '</span>
									</small>
								</td>';
            $pimary_b_html .= '<td class="cl-3"><i>' . $view_rank . '</i>
                               
									<span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_bing . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
										&nbsp;
									</span>' . $changeInRank_bing . '
									<br />
									<small>
										Previous <span style="color:red">' . $oldRank_bing_str . '</span>
									</small>
								</td>';
            $pimary_y_html .= '<td class="cl-3"><i>' . $view_rank . '</i>
                               
									<span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_yahoo . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
										&nbsp;
									</span>' . $changeInRank_yahoo . '
									<br />
									<small>
										Previous <span style="color:red">' . $oldRank_yahoo_str . '</span>
									</small>
								</td>';
        }


        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = $view_rank;
        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = $changeInRank;
        //start local targeting
        if ($targeting == 'local') {
            $RankingData = explode(',}/$^&', $row_key_data['RankingData']);
            $pr_RankingData = explode(',}/$^&', $get_dup_keyword['RankingData']);

            //print_r($RankingData);
            //google places rank data
            $search_location_gp = 'google-places';

            $key_index_gp = array_search($search_location_gp, $RankingData);

            $ranking_url_index_gp = $key_index_gp + 1;
            $rank_index_gp = $key_index_gp + 2;
            $ranking_url_gp = $RankingData[$ranking_url_index_gp];
            //$google_place_rank = $RankingData[$rank_index_gp];


            $google_place_rank = $RankingData[5];
            $bing_place_rank = $RankingData[20];
            $yahoo_place_rank = $RankingData[14];

            $pr_google_place_rank = $RankingData[5];
            $pr_bing_place_rank = $RankingData[20];
            $pr_yahoo_place_rank = $RankingData[14];

            $bing_place_rank_url = $RankingData[19];
            $bing_place_url = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $bing_place_rank_url);

            $yahoo_place_rank_url = $RankingData[13];
            $yahoo_place_url = str_replace(array("http://" . $client_website, "https://" . $client_website, "http://www." . $client_website, "https://www." . $client_website, $client_website), "", $yahoo_place_rank_url);


            if ($google_place_rank == 0) {
                $google_place_rank = 50;
            }
            if ($bing_place_rank == 0) {
                $bing_place_rank = 50;
            }
            if ($yahoo_place_rank == 0) {
                $yahoo_place_rank = 50;
            }
            if ($pr_google_place_rank == 0) {
                $pr_google_place_rank = 50;
            }
            if ($pr_bing_place_rank == 0) {
                $pr_bing_place_rank = 50;
            }
            if ($pr_yahoo_place_rank == 0) {
                $pr_yahoo_place_rank = 50;
            }
            $gp_rank_change = $pr_google_place_rank - $google_place_rank;
            if ($gp_rank_change == '0' || $gp_rank_change == '50') {

                $arrow_image = 'blue';
            } elseif ($gp_rank_change > 0) {

                $arrow_image = 'green';
            } elseif ($gp_rank_change < 0) {

                $arrow_image = 'red';
            }

            $bp_rank_change = $pr_bing_place_rank - $bing_place_rank;
            if ($bp_rank_change == '0' || $bp_rank_change == '50') {

                $arrow_image_bing = 'blue';
            } elseif ($bp_rank_change > 0) {

                $arrow_image_bing = 'green';
            } elseif ($bp_rank_change < 0) {

                $arrow_image_bing = 'red';
            }

            $yp_rank_change = $pr_yahoo_place_rank - $yahoo_place_rank;
            if ($yp_rank_change == '0' || $yp_rank_change == '50') {

                $arrow_image_yahoo = 'blue';
            } elseif ($yp_rank_change > 0) {

                $arrow_image_yahoo = 'green';
            } elseif ($yp_rank_change < 0) {

                $arrow_image_yahoo = 'red';
            }

            $google_html .= '<td class="cl-3">
                                    <i>';
            $bing_html .= '<td class="cl-3"><a title="' . $bing_place_rank_url . '" href="' . site_url() . '/url-profile/?url=' . $bing_place_rank_url . '">' . $bing_place_url . '</a></td><td class="cl-3">
                                    <i>';
            $yahoo_html .= '<td class="cl-3"><a title="' . $yahoo_place_rank_url . '" href="' . site_url() . '/url-profile/?url=' . $yahoo_place_rank_url . '">' . $yahoo_place_url . '</a></td><td class="cl-3">
                                    <i>';
            $gstr = ($google_place_rank == 50 ? '50+' : $google_place_rank);
            $bstr = ($bing_place_rank == 50 ? '50+' : $bing_place_rank);
            $ystr = ($yahoo_place_rank == 50 ? '50+' : $yahoo_place_rank);

            $google_html .= $gstr . '</i>
                                    <span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
                                        &nbsp;
                                    </span>';

            $bing_html .= $bstr . '</i>
                                    <span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_bing . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
                                        &nbsp;
                                    </span>';
            $yahoo_html .= $ystr . '</i>
                                    <span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_yahoo . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
                                        &nbsp;
                                    </span>';

            $grchstr = ($gp_rank_change < 50) ? ($gp_rank_change > 0 ? '+' : '') . $gp_rank_change : '0';
            $google_html .= $grchstr . '<br />
                                    <small>
                                        Previous 
                                        <span style="color:red">';
            $brchstr = ($bp_rank_change < 50) ? ($bp_rank_change > 0 ? '+' : '') . $bp_rank_change : '0';
            $bing_html .= $brchstr . '<br />
                                    <small>
                                        Previous
                                        <span style="color:red">';
            $yrchstr = ($yp_rank_change < 50) ? ($yp_rank_change > 0 ? '+' : '') . $yp_rank_change : '0';
            $yahoo_html .= $yrchstr . '<br />
                                    <small>
                                        Previous
                                        <span style="color:red">';
            $prstr = $pr_google_place_rank == 50 ? '50+' : $pr_google_place_rank;
            $google_html .= $prstr . '</span>
                                    </small>
                                </td>';
            $bprstr = $pr_bing_place_rank == 50 ? '50+' : $pr_bing_place_rank;
            $bing_html .= $bprstr . '</span>
                                    </small>
                                </td>';
            $yprstr = $pr_yahoo_place_rank == 50 ? '50+' : $pr_yahoo_place_rank;
            $yahoo_html .= $yprstr . '</span>
                                    </small>
                                </td>';
            $bing_html .= '</tr>';
            //end row bing
            $yahoo_html .= '</tr>';
            //end row yahoo

            if ($index_ps == 0) {
                $pimary_g_html .='<td class="cl-3">
										<i>' . $gstr . '</i>
										<span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
											&nbsp;
										</span>' . $grchstr . '<br />
										<small>
											Previous 
											<span style="color:red">' . $prstr . '</span>
										</small>
									</td>';
                $pimary_b_html .='<td class="cl-3"><a title="' . $bing_place_rank_url . '" href="' . site_url() . '/url-profile/?url=' . $bing_place_rank_url . '">' . $bing_place_url . '</a></td><td class="cl-3">
										<i>' . $bstr . '</i>
										<span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_bing . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
											&nbsp;
										</span>' . $brchstr . '<br />
										<small>
											Previous 
											<span style="color:red">' . $bprstr . '</span>
										</small>
									</td>';
                $pimary_y_html .='<td class="cl-3"><a title="' . $yahoo_place_rank_url . '" href="' . site_url() . '/url-profile/?url=' . $yahoo_place_rank_url . '">' . $yahoo_place_url . '</a></td><td class="cl-3">
										<i>' . $ystr . '</i>
										<span class="s-icn" style="background-image: url(' . site_url() . '/wp-content/themes/twentytwelve/images/icons/v2/' . $arrow_image_yahoo . '-arrow.png);background-size: 12px 13px;background-repeat: no-repeat;">
											&nbsp;
										</span>' . $yrchstr . '<br />
										<small>
											Previous 
											<span style="color:red">' . $yprstr . '</span>
										</small>
									</td>';
                $pimary_b_html .= '</tr>';
                //end row bing
                $pimary_y_html .= '</tr>';
                //end row yahoo
            }
        }
        //end local targeting
        //start seov not needed for Bing and Yahoo
        $google_html .= '<td class="cl-4">';
        if ($SEOv_change == '0' && $cal_SEOv == '0') {
            $arrow_image = 'blue';
        } elseif ($SEOv_change > 0) {
            $arrow_image = 'green';
        } elseif ($SEOv_change < 0) {
            $arrow_image = 'red';
        }

        $google_html .= '<i>' . FormatMoney($Current_cal_SEOv) . '</i>
                                <span class="s-icn"><img alt="' . $Current_cal_SEOv . '" src="' . get_template_directory_uri() . '/images/icons/v2/' . $arrow_image . '-arrow.png" width="12" height="13" /></span>
                                <br />
                                <small>
                                    Previous 
                                    <span style="color:red">' . FormatMoney($cal_SEOv) . '</span>
                                </small>
                            </td>';


        if ($index_ps == 0) {
            $pimary_g_html .='<td class="cl-4"><i>' . FormatMoney($Current_cal_SEOv) . '</i>
									<span class="s-icn"><img alt="' . $Current_cal_SEOv . '" src="' . get_template_directory_uri() . '/images/icons/v2/' . $arrow_image . '-arrow.png" width="12" height="13" /></span>
									<br />
									<small>
										Previous 
										<span style="color:red">' . FormatMoney($cal_SEOv) . '</span>
									</small>
								</td>';
        }

        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = FormatMoney($Current_cal_SEOv);
        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = FormatMoney($SEOv_change);

        $google_html .= '<td class="cl-5">';


        $total_organic = $organic_results_by_url[$URL];

        $total_visit = $total_visit_by_url[$URL];

        $total_visit = $total_visit > 0 ? $total_visit : 0;
        $total_organic = $total_organic > 0 ? $total_organic : 0;
        $keyword_total_organic[$Keyword] = $total_organic;
        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = $total_organic;

        $tot_organic += $total_organic;
        if ($isLp == true) { //landing page
            $total_ov_lp += $total_organic;
            $target_url_organic_visits[$RankingURL] = $total_organic;
        }

        $google_html .= $total_organic . '</td>
                            <td class="cl-6">';

        if ($index_ps == 0) {
            $pimary_g_html .= '<td class="cl-5">' . $total_organic . '</td>';
        }

        $ConvCountForClient = 0;
        if (isset($conv_results_by_url[$URL])) {
            $ConvCountForClient = $conv_results_by_url[$URL];
        }
        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = $ConvCountForClient > 0 ? $ConvCountForClient : 0;

        $convstr = $ConvCountForClient > 0 ? $ConvCountForClient : 0;

        $google_html .= $convstr . '    
                            </td>
                            <td class="cl-7">';

        if ($index_ps == 0) {
            $pimary_g_html .= '<td class="cl-6">' . $convstr . '</td>';
        }

        $_SESSION['pdf_report'][$row_active_key][$key_type][$row_ps_key][] = $_SESSION['csv_report'][$row_active_key][$key_type][$row_ps_key][] = PerSentFormat(($ConvCountForClient / $total_visit) * 10000);

        $confrstr = PerSentFormat(($ConvCountForClient / $total_visit) * 10000);

        $google_html .= $confrstr . '</td>
                            <td class="cl-8">';
        $searchstr = $row_key_data['GoogleSearchVolume'] > 0 ? $row_key_data['GoogleSearchVolume'] : 0;
        $google_html .= $searchstr . '</td>
                            <td class="cl-9">' . PerSentFormat2($row_key_data['Difficulty'] * 10000) . '</td>
                            <td class="cl-10">';

        $CPC = MoneyFromMicros($row_key_data['CPC']);
        $google_html .= FormatMoney($CPC) . '</td>';

        //Rank Bucket Start 
        $row_key = strtolower(str_replace("'", "", $row_ps_key));
        if (isset($seo_data_arr[$row_key]['CurrentRank'])) {
            $CurrentRank = $CurrentRank_text = $seo_data_arr[$row_key]['CurrentRank'];
            if ($CurrentRank == 0) {
                $CurrentRank_text = '50+';
            }
        } else {
            $CurrentRank_text = $none_value;
        }

        $current_bucket = '';
        $bucket_color_style = 'color: #fff;';
        if ($CurrentRank == '0') {
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

        $sql = 'SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $row_key . '" order by `DateOfRank` ASC LIMIT 1';

        $date_enter = row_array($sql);
        if (!empty($date_enter)) {
            $date_enter_val = $date_enter['DateOfRank'];
        } else {
            $date_enter_val = $seo_data_arr[$row_key]['DateOfRank'];
        }
        $date_enter_val = date("d M Y", strtotime($date_enter_val));

        $sql = 'SELECT DateOfRank FROM `seo_history` WHERE `MCCUserId` = ' . $user_id . ' and `Keyword` = "' . $row_key . '" ' . $con . 'order by `DateOfRank` ASC LIMIT 1';
        //echo $sql . '<br/>';
        $rank_history = row_array($sql);
        if (!empty($rank_history)) {
            $date_in_bucket = $rank_history['DateOfRank'];
        } else {
            $date_in_bucket = $seo_data_arr[$row_key]['DateOfRank'];
        }
        $date_in_bucket = date("d M Y", strtotime($date_in_bucket));


        $now = time(); // or your date as well
        $your_date = strtotime($date_in_bucket);
        $datediff = $now - $your_date;
        $date_in_bucket_days = floor($datediff / (60 * 60 * 24));
        $bg_color_style = '';
        if ($current_bucket == '11-50' || $current_bucket == '50+') {
            if ($date_in_bucket_days >= 90) {
                $bg_color_style = 'color:red!important;font-weight:bold;';
            }
        }

        $your_date = strtotime($date_enter_val);
        $datediff = $now - $your_date;
        $total_days = floor($datediff / (60 * 60 * 24));

        $bucket_val = $CurrentRank_text != $none_value ? $current_bucket : $none_value;
        $days_in_bucket_val = $CurrentRank_text != $none_value ? $date_in_bucket_days : $none_value;
        $percentage_of_time = $CurrentRank_text != $none_value ? sprintf("%.2f", ($date_in_bucket_days / $total_days) * 100) . '%' : $none_value;
        //Rank Bucket End 






        $google_html .='<td class="cl-11" style="' . $bucket_color_style . '">' . $bucket_val . '</td>';
        $google_html .='<td class="cl-12" style="' . $bg_color_style . '">' . $days_in_bucket_val . '</td>';
        $google_html .='<td class="cl-13">' . $percentage_of_time . '</td>';
        $google_html .='</tr>';

        if ($index_ps == 0) {
            $pimary_g_html .= '<td class="cl-7">' . $confrstr . '</td>
								<td class="cl-8">' . $searchstr . '</td>
								<td class="cl-9">' . PerSentFormat2($row_key_data['Difficulty'] * 10000) . '</td>
								<td class="cl-10">' . FormatMoney($CPC) . '</td>
								<td class="cl-11" style="' . $bucket_color_style . '">' . $bucket_val . '</td>
								<td class="cl-12" style="' . $bg_color_style . '">' . $days_in_bucket_val . '</td>
								<td class="cl-13">' . $percentage_of_time . '</td>
							</tr>';
        }
    }

    $google_html .= '</tbody>
            </table>
        </section>';
    $bing_html .= '</tbody>
            </table>
        </section>';
    $yahoo_html .= '</tbody>
            </table>
        </section>';
    $pi++;
} //endforeach;

if ($pitable > 1) {
    $pimary_g_html .= '</tbody>
		</table>
		</section>';
    $pimary_b_html .= '</tbody>
		</table>
		</section>';
    $pimary_y_html .= '</tbody>
		</table>
		</section>';
}
$google_html .= ' </div>
</div>';
$bing_html .= ' </div>
</div>';
$yahoo_html .= ' </div>
</div>';


$_SESSION['improved_ranking'] = $improved_ranking;
$_SESSION['lost_ranking'] = $lost_ranking;
$_SESSION['same_rank'] = $same_rank;
?>
<script>
    jQuery(document).ready(function () {
        jQuery('.respTbl').tablesorter({
            // sortInitialOrder: 'desc',
            sortList: [[0, 0]], // etc. [[4, 1]]
        });
    });
    jQuery(document).ready(function () {
        jQuery('.respTbl_target').tablesorter({
            //sortInitialOrder: 'asc',
            sortList: [[0, 1]], // etc. [[4, 1]]
        });
    });

</script>

