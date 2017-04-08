<?php
$competitor_url = array_filter($competitor_url);
$count_competitor_url = count($competitor_url);

$Content_keyword_Site = get_user_meta($UserID, "Content_keyword_Site", true);
$Synonyms_keyword_arr = $Content_keyword_Site['Synonyms_keyword'];
$activation = $Content_keyword_Site['activation'];
$target_keyword = $Content_keyword_Site["target_keyword"];

$all_active_keywords = all_active_keywords($UserID);
$primary_html = '<span style="background:#22B04B; margin-right:6px;" class="badge">P</span>';
$secondary_html = '<span style="background:#FF7F27; margin-right:6px;margin-left: 10px;" class="badge">S</span>';

$x_axis[] = $client_website;
$comp_data_arr[$client_website] = array(
    'total_rank' => 0,
    'total_avg_pos' => 0,
    'first_palce' => 0,
    'top3' => 0,
    'page1' => 0,
    'old_total_keywords_rank' => 0,
    'old_total_rank' => 0,
    'old_total_avg_pos' => 0,
    'old_first_palce' => 0,
    'old_top3' => 0,
    'old_page1' => 0,
);
for ($com_url = 0; $com_url < $count_competitor_url; $com_url++) {
    $x_axis[$com_url + 1] = $competitor_url[$com_url];
    $comp_data_arr[$competitor_url[$com_url]] = array(
        'total_rank' => 0,
        'total_avg_pos' => 0,
        'first_palce' => 0,
        'top3' => 0,
        'page1' => 0,
        'old_total_keywords_rank' => 0,
        'old_total_rank' => 0,
        'old_total_avg_pos' => 0,
        'old_first_palce' => 0,
        'old_top3' => 0,
        'old_page1' => 0,
    );
}

$total_keywords = 0;

foreach ($all_active_keywords as $table_index => $row_active_key) {
    $same_keywords = $wpdb->get_results("SELECT meta_key FROM `wp_usermeta` WHERE `user_id` = $UserID  AND (`meta_value` LIKE '$row_active_key' || `meta_value` LIKE '$row_active_key ') ORDER BY `umeta_id` DESC");
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
    $primary_and_synonyms_key = array();
    $primary_and_synonyms_key[] = $row_active_key;

    if (!empty($Synonyms_keyword)) {
        $primary_and_synonyms_key = array_merge($primary_and_synonyms_key, $Synonyms_keyword);
    }

    foreach ($primary_and_synonyms_key as $index_ps => $row_ps_key) {
        $total_keywords++;
        $key_index++;
        $valkey = mysql_real_escape_string($row_ps_key);
        $sql = "SELECT CurrentRank FROM `seo` WHERE `MCCUserId` = $UserID and Keyword = '$valkey' order by Keyword asc";
        $row_key_data = row_array($sql);
        $CurrentRank = $get_current_rank = $row_key_data['CurrentRank'];

        if ($index_ps == 0) {
            $key_cal = $primary_html;
            $key_type = 'primary';
        } else {
            $key_cal = $secondary_html;
            $key_type = 'secondary';
        }

        //----------
        if ($get_current_rank > 0) {
            $comp_data_arr[$client_website]['total_rank'] += 1;
            $comp_data_arr[$client_website]['total_avg_pos'] += $get_current_rank;
        } else {
            $comp_data_arr[$client_website]['total_avg_pos'] += 50;
        }
        if ($get_current_rank == 1) {
            $comp_data_arr[$client_website]['first_palce'] += 1;
        }
        if ($get_current_rank >= 1 && $get_current_rank <= 3) {
            $comp_data_arr[$client_website]['top3'] += 1;
        }
        if ($get_current_rank >= 1 && $get_current_rank <= 10) {
            $comp_data_arr[$client_website]['page1'] += 1;
        }

        $old_result = row_array("SELECT CurrentRank FROM `seo_history` WHERE `MCCUserId` = $UserID and Keyword = '$valkey' order by DateOfRank DESC LIMIT 1");
        $get_current_rank = $old_result['CurrentRank'];

        if ($get_current_rank > 0) {
            $comp_data_arr[$client_website]['old_total_rank'] += 1;
            $comp_data_arr[$client_website]['old_total_avg_pos'] += $get_current_rank;
        } else {
            $comp_data_arr[$client_website]['old_total_avg_pos'] += 50;
        }
        if ($get_current_rank == 1) {
            $comp_data_arr[$client_website]['old_first_palce'] += 1;
        }
        if ($get_current_rank >= 1 && $get_current_rank <= 3) {
            $comp_data_arr[$client_website]['old_top3'] += 1;
        }
        if ($get_current_rank >= 1 && $get_current_rank <= 10) {
            $comp_data_arr[$client_website]['old_page1'] += 1;
        }

        foreach ($competitor_url as $rank_competitor_url) {

            $sql = 'SELECT rank FROM `competitor_report` WHERE  `user_id` = ' . $UserID . ' and `keyword` = "' . $valkey . '" and `url` = "' . $rank_competitor_url . '" LIMIT 1';
            $result = row_array($sql);
            //--------------

            $get_current_rank = $result['rank'];
            if ($get_current_rank > 0) {
                $comp_data_arr[$rank_competitor_url]['total_rank'] += 1;
                $comp_data_arr[$rank_competitor_url]['total_avg_pos'] += $get_current_rank;
            } else {
                $comp_data_arr[$rank_competitor_url]['total_avg_pos'] += 50;
            }
            if ($get_current_rank == 1) {
                $comp_data_arr[$rank_competitor_url]['first_palce'] += 1;
            }
            if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                $comp_data_arr[$rank_competitor_url]['top3'] += 1;
            }
            if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                $comp_data_arr[$rank_competitor_url]['page1'] += 1;
            }
            $old_sql = 'SELECT rank FROM `competitor_report_history` WHERE `user_id` = ' . $UserID . ' and `keyword` = "' . $valkey . '" and `url` = "' . $rank_competitor_url . '" ORDER BY rank_date DESC LIMIT 1';
            $old_result = row_array($old_sql);

            $get_current_rank = $old_result['rank'];
            if ($get_current_rank > 0) {
                $comp_data_arr[$rank_competitor_url]['old_total_rank'] += 1;
                $comp_data_arr[$rank_competitor_url]['old_total_avg_pos'] += $get_current_rank;
            } else {
                $comp_data_arr[$rank_competitor_url]['old_total_avg_pos'] += 50;
            }
            if ($get_current_rank == 1) {
                $comp_data_arr[$rank_competitor_url]['old_first_palce'] += 1;
            }
            if ($get_current_rank >= 1 && $get_current_rank <= 3) {
                $comp_data_arr[$rank_competitor_url]['old_top3'] += 1;
            }
            if ($get_current_rank >= 1 && $get_current_rank <= 10) {
                $comp_data_arr[$rank_competitor_url]['old_page1'] += 1;
            }
        }
    }
}

$label_arr = array(
    'blue' => 'bg-blue',
    'green' => 'bg-green-jungle',
    'red' => 'bg-red-thunderbird',
);

$gh_competitor = array();
?>