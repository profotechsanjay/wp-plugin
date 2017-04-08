<?php

global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
if($_REQUEST['param'] != 'processed_queue_update' && $_REQUEST['param'] != 'global_queue' && $_REQUEST['param'] != 'singlepageanalysis' 
    && $_REQUEST['param'] != 'keywordscanning' && $_REQUEST['param'] != 'pageanalysis' && $_REQUEST['param'] != 'fetchcredata'
    && $_REQUEST['param'] != 'cretriggerstop' && $_REQUEST['param'] != 'recommendation_report' && $_REQUEST['param'] != 'content_recommend'){    
    if ($user_id == 0) {
        json(0, 'Login is required');
    }
}

if (isset($_REQUEST["param"])) {
    
    
    /* Content Recommendation Engine */
    if ($_REQUEST["param"] == "pageanalysis") {
        $user_id = isset($_REQUEST['user_id'])?intval($_REQUEST['user_id']):0;
        if($user_id <= 0){
            die;
        }
        $url = isset($_REQUEST['url'])?$_REQUEST['url']:'';
        if(empty($url) || $url == ''){
            die;
        }
        require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
        $browser = &new SimpleBrowser();
                
        $data = array(                
            'url' => $url,
            'keyword' => '',
            'total_issues' => 0
        );    
        $data = arrtoobj($data);   
        //@mail("parambir.rudra@gmail.com","Analysis started for $url", "Analysis has been started for $url at ".date('Y-m-d H:i:s') );
        $analysis = page_analysis($data,$browser);  
        //@mail("parambir.rudra@gmail.com","Analysis end for $url", json_encode($analysis));                
        
        $result = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT crawl_result, result FROM wp_content_recommend WHERE user_id = %d", $user_id
            )
        );
        $dataset = json_decode($result->result);
        foreach($dataset as $key => $dat){
            if(trim($dat->url) == trim($url)){
                $dat->analysis = $analysis;                
            }           
        }
        
        $final_result = json_encode($dataset);
        
        $result->result = $final_result;
        
        $wpdb->query
        (
            $wpdb->prepare
            (
            "UPDATE wp_content_recommend SET result = %s WHERE user_id = %d", 
             $final_result, $user_id
            )
        );
        
        // checking last result        
        
        $result = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT crawl_result, result FROM wp_content_recommend WHERE user_id = %d", $user_id
            )
        );
        
        $rsrec = json_decode($result->result);;
        $crawlrs = json_decode($result->crawl_result);
        $totalurls = count($crawlrs->urls);
        
        $total_title_issues = 0; $total_meta_issues = 0; $total_content_issues = 0;
        $total_heading_issues = 0; $total_link_issues = 0; $total_image_issues = 0;
        
        $completed_urls = 0;
        foreach($rsrec as $key => $rsre){
            if(isset($rsre->analysis) && $rsre->analysis != ''){
                $completed_urls++;
                $analysis = $rsre->analysis;
                $total_title_issues = $total_title_issues + $analysis->issues_count->title_issues;
                $total_meta_issues = $total_meta_issues + $analysis->issues_count->meta_issues;
                $total_content_issues = $total_content_issues + $analysis->issues_count->content_issues;
                $total_heading_issues = $total_heading_issues + $analysis->issues_count->heading_issues;
                $total_link_issues = $total_link_issues + $analysis->issues_count->link_issues;
                $total_image_issues = $total_image_issues + $analysis->issues_count->image_issues;                
            }                        
        }
        
        if($completed_urls == $totalurls){
           
            $rsrec = (array) $rsrec;
            $arnew = array(
                'total_title_issues' => $total_title_issues,
                'total_meta_issues' => $total_meta_issues,
                'total_content_issues' => $total_content_issues,
                'total_heading_issues' => $total_heading_issues,
                'total_link_issues' => $total_link_issues,
                'total_image_issues' => $total_image_issues
            );
            
            $rsrec = array_merge($rsrec, $arnew);            
            $final_result = json_encode($rsrec);
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                "UPDATE wp_content_recommend SET trigger_report = 0, result = %s WHERE user_id = %d", 
                 $final_result, $user_id
                )
            );
            // send mail to user
            //@mail("parambir.rudra@gmail.com","Analysis completed for all urls", "Analysis done for all pages");
        }                
        die;
    }
    else if ($_REQUEST["param"] == "remcremsg") { 
       $uid = user_id();
       $vl = get_user_meta($uid,'crever',TRUE); 
       if($vl == ''){
           add_user_meta($uid,'crever',CREVER);
       }
       else{
           update_user_meta($uid,'crever',CREVER);
       }       
       die;
    }
    else if ($_REQUEST["param"] == "fetchcredata") { 
               
        $curver = $wpdb->get_var("SELECT id FROM cre_algovals ORDER BY id DESC LIMIT 1");        
        $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;    
        if($id > 0){
            $credt = $wpdb->get_row("SELECT id, credata, created_dt FROM cre_algovals WHERE id = ".$id);
        }
        else{
            $credt = $wpdb->get_row("SELECT id, credata, created_dt FROM cre_algovals ORDER BY id DESC LIMIT 1");
        }            
        json(1, $curver, $credt);
    }
    else if ($_REQUEST["param"] == "processed_queue_update") {
        $db = isset($_REQUEST['db'])?trim($_REQUEST['db']):"";
        if($db == DB_NAME){
            $isok = 1;
        }
        else{
            $is_client = $wpdb->get_var
            (
                $wpdb->prepare
                (
                    "SELECT id FROM wp_setup_table WHERE db_name = %s", $db
                )
            );
            if($is_client > 0){
                $isok = 1;
            }
        }
        if($isok == 0){
            json(0, 'Invalid Request');
        }
        
        $queue_id = isset($_POST['queue_id'])?intval($_POST['queue_id']):"";               
        $queueurl = isset($_POST['queueurl'])?trim($_POST['queueurl']):"";
        $pageindex = isset($_POST['pageindex'])?intval($_POST['pageindex']):"";
        
        $queueprocessed = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT processed FROM cre_queue WHERE id = %d", $queue_id
            )
        );
                     
        if($queueprocessed != ''){
            $queueprocessed = trim($queueprocessed);
            $queueprocessed = explode(",", $queueprocessed);
            $newstr = '';
            foreach($queueprocessed as $queueproc){
                if(intval($queueproc) != $pageindex){
                    $newstr .= $queueproc.',';
                }
            }
            if($newstr != ''){
                $newstr = substr($newstr, 0, -1);
            }
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "UPDATE cre_queue SET processed = %s WHERE id = %d", $newstr, $queue_id
                )
            );            
        }                
        
        die;
    }
    else if ($_REQUEST["param"] == "crekeywordcurrentgroup") {
       
        $keyword = isset($_POST['keyword'])?trim($_POST['keyword']):'';
        $add_keyword_url = isset($_POST['pageurl'])?trim($_POST['pageurl']):'';
        $only_keyword_url= trim(trim(str_replace(array('http://','https://','www.'), array('','',''), $add_keyword_url),"/"));
        if($add_keyword_url == ''){
            json(0, 'Page URL is missing');
        }
        if($keyword == ''){
            json(0, 'Keyword should not empty');
        }
        $user_id = $UserID = user_id();
        $add_keyword = strtolower(trim($keyword));
        $keywordDat = get_user_meta($UserID, "Content_keyword_Site", true);
        
        if (!empty($keywordDat)) {
            $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
            $primarylander = $keywordDat["primarylander"];
            $secondarylander = $keywordDat["secondarylander"];
            $Additionalsnotes = $keywordDat["Additionalsnotes"];

            $activation = $keywordDat["activation"];
            $target_keyword = $keywordDat["target_keyword"];
            $delete = $keywordDat["delete"];

            $landingpage = $keywordDat["landing_page"];
            $idx = -1;
            $statuss = $keywordDat['activation'];
            foreach($landingpage as $key => $landig){
                $landingurl = $landig[0];
                $landingurl = trim(trim(str_replace(array('http://','https://','www.'), array('','',''), $landingurl),"/"));
                
                if($landingurl == $only_keyword_url){
                    if($statuss["$key"] == 'active'){
                        $idx = $key;
                    }
                }
            }
                        
            $livedate = $keywordDat['live_date'];        
            $existingkeyword = array();
            $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` != '' ORDER BY `uh`.`update_date`";
            $KeyWordQuery = $wpdb->get_results($sql);

            if (empty($KeyWordQuery)) {

                $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" && `meta_value` != "" order by `meta_value` ';
                $KeyWordQuery = $wpdb->get_results($sql);

                $sql = 'SELECT meta_key, meta_value FROM wp_usermeta WHERE user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
                $null_key_index = $wpdb->get_results($sql);

            }
            else{
                $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` = '' ORDER BY `uh`.`update_date` ";
                $null_key_index = $wpdb->get_results($sql);
            }

            $KeyWordQuery = array_merge($KeyWordQuery, $null_key_index);
            foreach ($KeyWordQuery as $KeyWordQ => $KeyWordData) {
                $ks = str_replace("LE_Repu_Keyword_", "", $KeyWordData->meta_key);
                $j = $ks - 1;
                $sykey = trim(strtolower($KeyWordData->meta_value)); 
                $sts = isset($KeyWordData->status)?$KeyWordData->status:'';
                if($sykey == $add_keyword){

                    $existingkeyword = array('keyword' => $sykey, 'type' => 'primary', 'status' => $sts);
                    break;
                }
                for ($h = 0; $h < 5; $h++) { 
                    $sykey = trim(strtolower($Synonyms_keyword[$j][$h]));   
                    if($sykey == $add_keyword){
                        $existingkeyword = array('keyword' => $sykey, 'type' => 'synonym', 'status' => $sts);
                        break;
                    }
                }
            }

            if (!empty($existingkeyword)) {
                json(-1, '',$existingkeyword);
            }
            
            if($idx >= 0){
                $keyword_count = $keywordDat['keyword_count'];
                $Content_keyword_Site = $keywordDat;
                $vals = $Content_keyword_Site['Synonyms_keyword'][$idx]; $ik = 0;
                foreach($vals as $ky => $val){
                   if(trim($val) == ''){
                       $vals["$ky"] = $keyword;
                       $ik++;
                       break;
                   }                    
                }
                
                if($ik == 0){
                    json(-2, $keyword, $vals);                    
                }
                
                $Content_keyword_Site['Synonyms_keyword'][$idx] = $vals;
                update_user_meta($user_id, 'Content_keyword_Site', $Content_keyword_Site);
                
//                $ReportID = get_user_meta($user_id, "btl_campaign", true);
//                if ($ReportID > 0) {
//                    include(get_template_directory().'/analytics/BrightLocalUtils.php');
//                    $all_active_keywords = all_active_keywords($user_id, 1);
//                    $all_active_keywords = array_unique($all_active_keywords);
//                    $search_terms = "<pre>" . implode("\n", $all_active_keywords) . "</pre>";
//
//                    $PostFields['campaign-id'] = $ReportID;
//                    $PostFields['search-terms'] = $search_terms;
//                    if ($_SERVER['HTTP_HOST'] != 'localhost' && ($_SERVER['HTTP_HOST'] != '127.0.0.1')) {
//                        $update_campaign = GetBTLInfoUsingCURL('lsrc/update', $PostFields);
//
//                        $report_run_PostFields['campaign-id'] = $ReportID;
//
//                        $update_campaign = GetBTLInfoUsingCURL('lsrc/run', $report_run_PostFields);
//                    }
//                } 
                
                json(1, 'Keyword successfully added as synonym keyword'); 
                
            }
            
        }
        
        json(0, 'Keyword Not Added');
    }
    
    else if ($_REQUEST["param"] == "crekeywordcurrentgroupmove") {
       
        
        $idxtorem = isset($_POST['idxtorem'])?trim($_POST['idxtorem']):'';
        $keyword = isset($_POST['keyword'])?trim($_POST['keyword']):'';
        $add_keyword_url = isset($_POST['pageurl'])?trim($_POST['pageurl']):'';
        $only_keyword_url= trim(trim(str_replace(array('http://','https://','www.'), array('','',''), $add_keyword_url),"/"));
        if($add_keyword_url == ''){
            json(0, 'Page URL is missing');
        }
        if($keyword == ''){
            json(0, 'Keyword should not empty');
        }
        $user_id = $UserID = user_id();
        $add_keyword = strtolower(trim($keyword));
        $keywordDat = get_user_meta($UserID, "Content_keyword_Site", true);
        
        if (!empty($keywordDat)) {
            $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
            $primarylander = $keywordDat["primarylander"];
            $secondarylander = $keywordDat["secondarylander"];
            $Additionalsnotes = $keywordDat["Additionalsnotes"];

            $activation = $keywordDat["activation"];
            $target_keyword = $keywordDat["target_keyword"];
            $delete = $keywordDat["delete"];

            $landingpage = $keywordDat["landing_page"];
            $idx = -1;
            $statuss = $keywordDat['activation'];
            foreach($landingpage as $key => $landig){
                $landingurl = $landig[0];
                $landingurl = trim(trim(str_replace(array('http://','https://','www.'), array('','',''), $landingurl),"/"));
                
                if($landingurl == $only_keyword_url){
                    if($statuss["$key"] == 'active'){
                        $idx = $key;
                    }
                }
            }
                        
            $livedate = $keywordDat['live_date'];        
            $existingkeyword = array();
            $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` != '' ORDER BY `uh`.`update_date`";
            $KeyWordQuery = $wpdb->get_results($sql);

            if (empty($KeyWordQuery)) {

                $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" && `meta_value` != "" order by `meta_value` ';
                $KeyWordQuery = $wpdb->get_results($sql);

                $sql = 'SELECT meta_key, meta_value FROM wp_usermeta WHERE user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
                $null_key_index = $wpdb->get_results($sql);

            }
            else{
                $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` = '' ORDER BY `uh`.`update_date` ";
                $null_key_index = $wpdb->get_results($sql);
            }

            $KeyWordQuery = array_merge($KeyWordQuery, $null_key_index);
            foreach ($KeyWordQuery as $KeyWordQ => $KeyWordData) {
                $ks = str_replace("LE_Repu_Keyword_", "", $KeyWordData->meta_key);
                $j = $ks - 1;
                $sykey = trim(strtolower($KeyWordData->meta_value)); 
                $sts = isset($KeyWordData->status)?$KeyWordData->status:'';
                if($sykey == $add_keyword){

                    $existingkeyword = array('keyword' => $sykey, 'type' => 'primary', 'status' => $sts);
                    break;
                }
                for ($h = 0; $h < 5; $h++) { 
                    $sykey = trim(strtolower($Synonyms_keyword[$j][$h]));   
                    if($sykey == $add_keyword){
                        $existingkeyword = array('keyword' => $sykey, 'type' => 'synonym', 'status' => $sts);
                        break;
                    }
                }
            }

            if (!empty($existingkeyword)) {
                json(-1, '',$existingkeyword);
            }
            
            if($idx >= 0){
                $keyword_count = $keywordDat['keyword_count'];
                $Content_keyword_Site = $keywordDat;
                $vals = $Content_keyword_Site['Synonyms_keyword'][$idx]; $ik = 0;                
                $vals["$idxtorem"] = $keyword;
                
                $Content_keyword_Site['Synonyms_keyword'][$idx] = $vals;
                update_user_meta($user_id, 'Content_keyword_Site', $Content_keyword_Site);
                
//                $ReportID = get_user_meta($user_id, "btl_campaign", true);
//                if ($ReportID > 0) {
//                    include(get_template_directory().'/analytics/BrightLocalUtils.php');
//                    $all_active_keywords = all_active_keywords($user_id, 1);
//                    $all_active_keywords = array_unique($all_active_keywords);
//                    $search_terms = "<pre>" . implode("\n", $all_active_keywords) . "</pre>";
//
//                    $PostFields['campaign-id'] = $ReportID;
//                    $PostFields['search-terms'] = $search_terms;
//                    if ($_SERVER['HTTP_HOST'] != 'localhost' && ($_SERVER['HTTP_HOST'] != '127.0.0.1')) {
//                        $update_campaign = GetBTLInfoUsingCURL('lsrc/update', $PostFields);
//
//                        $report_run_PostFields['campaign-id'] = $ReportID;
//
//                        $update_campaign = GetBTLInfoUsingCURL('lsrc/run', $report_run_PostFields);
//                    }
//                } 
                json(1, 'Keyword replaced and successfully added as synonym keyword');                
            }
            
        }
        
        json(0, 'Keyword Not Added');
    }
    
    else if ($_REQUEST["param"] == "crekeywordadd") {
        $keyword = isset($_POST['keyword'])?trim($_POST['keyword']):'';
        $add_keyword_url = isset($_POST['pageurl'])?trim($_POST['pageurl']):'';
        if($add_keyword_url == ''){
            json(0, 'Page URL is missing');
        }
        if($keyword == ''){
            json(0, 'Keyword should not empty');
        }
        $user_id = $UserID = user_id();
        $add_keyword = strtolower(trim($keyword));
        $keywordDat = get_user_meta($UserID, "Content_keyword_Site", true);
        if (!empty($keywordDat)) {
            $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
            $primarylander = $keywordDat["primarylander"];
            $secondarylander = $keywordDat["secondarylander"];
            $Additionalsnotes = $keywordDat["Additionalsnotes"];

            $activation = $keywordDat["activation"];
            $target_keyword = $keywordDat["target_keyword"];
            $delete = $keywordDat["delete"];

            $landingpage = $keywordDat["landing_page"];
            $livedate = $keywordDat['live_date'];
        } else {
            $keywordDat['keyword_count'] = 0;
        }

        $existingkeyword = array();
        $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` != '' ORDER BY `uh`.`update_date`";
        $KeyWordQuery = $wpdb->get_results($sql);

        if (empty($KeyWordQuery)) {

            $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" && `meta_value` != "" order by `meta_value` ';
            $KeyWordQuery = $wpdb->get_results($sql);

            $sql = 'SELECT meta_key, meta_value FROM wp_usermeta WHERE user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
            $null_key_index = $wpdb->get_results($sql);

        }
        else{
            $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` = '' ORDER BY `uh`.`update_date` ";
            $null_key_index = $wpdb->get_results($sql);
        }
        
        $KeyWordQuery = array_merge($KeyWordQuery, $null_key_index);
        foreach ($KeyWordQuery as $KeyWordQ => $KeyWordData) {
            $ks = str_replace("LE_Repu_Keyword_", "", $KeyWordData->meta_key);
            $j = $ks - 1;
            $sykey = trim(strtolower($KeyWordData->meta_value)); 
            $sts = isset($KeyWordData->status)?$KeyWordData->status:'';
            if($sykey == $add_keyword){
                
                $existingkeyword = array('keyword' => $sykey, 'type' => 'primary', 'status' => $sts);
                break;
            }
            for ($h = 0; $h < 5; $h++) { 
                $sykey = trim(strtolower($Synonyms_keyword[$j][$h]));   
                if($sykey == $add_keyword){
                    $existingkeyword = array('keyword' => $sykey, 'type' => 'synonym', 'status' => $sts);
                    break;
                }
            }
        }
                
        if (!empty($existingkeyword)) {
            json(-1, '',$existingkeyword);
        }        
        
        $Content_keyword_Site = $keywordDat;
        $keyword_count = $keywordDat['keyword_count'];
        $keyword_count_for_array_data = $keyword_count;
        $keyword_count += 1;
        $Content_keyword_Site['keyword_count'] = $keyword_count;
        $Content_keyword_Site['LE_Repu_Keyword_' . $keyword_count] = $add_keyword;
        $Content_keyword_Site['activation'][$keyword_count_for_array_data] = 'active';
        $Content_keyword_Site['Additionalsnotes'][$keyword_count_for_array_data] = '';
        $Content_keyword_Site['target_keyword'][$keyword_count_for_array_data] = 'No';
        $Content_keyword_Site['Synonyms_keyword'][$keyword_count_for_array_data] = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '');
        $Content_keyword_Site['secondarylander'][$keyword_count_for_array_data] = '';
        $Content_keyword_Site['primarylander'][$keyword_count_for_array_data] = appendhttp(get_user_meta($user_id, 'website', true));
        $Content_keyword_Site['live_date'][$keyword_count_for_array_data] = array(0 => '');
        $Content_keyword_Site['landing_page'][$keyword_count_for_array_data] = array(0 => $add_keyword_url);
       
        update_user_meta($user_id, 'LE_Repu_Keyword_' . $keyword_count, $add_keyword);
        $insert_keywords_update_history['user_id'] = $user_id;
        $insert_keywords_update_history['keyword_key'] = 'LE_Repu_Keyword_' . $keyword_count;
        $insert_keywords_update_history['update_user_id'] = current_id();
        $insert_keywords_update_history['status'] = 'active';
        $insert_keywords_update_history['update_date'] = date('Y-m-d H:i:s');
        $wpdb->insert('wp_keywords_update_history', $insert_keywords_update_history);

        update_user_meta($user_id, 'Content_keyword_Site', $Content_keyword_Site);
//        $ReportID = get_user_meta($user_id, "btl_campaign", true);
//        if ($ReportID > 0) {
//            include(get_template_directory().'/analytics/BrightLocalUtils.php');
//            $all_active_keywords = all_active_keywords($user_id, 1);
//            $all_active_keywords = array_unique($all_active_keywords);
//            $search_terms = "<pre>" . implode("\n", $all_active_keywords) . "</pre>";
//            
//            $PostFields['campaign-id'] = $ReportID;
//            $PostFields['search-terms'] = $search_terms;
//            if ($_SERVER['HTTP_HOST'] != 'localhost' && ($_SERVER['HTTP_HOST'] != '127.0.0.1')) {
//                $update_campaign = GetBTLInfoUsingCURL('lsrc/update', $PostFields);
//
//                $report_run_PostFields['campaign-id'] = $ReportID;
//
//                $update_campaign = GetBTLInfoUsingCURL('lsrc/run', $report_run_PostFields);
//            }
//        } 
                
        json(1, 'Keyword successfully added');
        
    }    
    else if ($_REQUEST["param"] == "global_queue") {
        $isok = 0;
        $db = isset($_REQUEST['db'])?trim($_REQUEST['db']):"";
        if($db == DB_NAME){
            $isok = 1;
        }
        else{
            $is_client = $wpdb->get_var
            (
                $wpdb->prepare
                (
                    "SELECT id FROM wp_setup_table WHERE db_name = %s", $db
                )
            );
            if($is_client > 0){
                $isok = 1;
            }
        }
        if($isok == 0){
            json(0, 'Invalid Request');
        }
        
        $agency_url = isset($_POST['agency_url'])?trim($_POST['agency_url']):"";               
        $urls = isset($_POST['urls'])?trim($_POST['urls']):"";   
        $user_id = isset($_POST['user_id'])?intval($_POST['user_id']):"";                 
        $wpdb->query
        (
            $wpdb->prepare
            (
               "INSERT INTO cre_queue (agency_url, urls, db, user_id, created_dt) VALUES(%s, %s, %s, %d, NOW())", 
                $agency_url, $urls, $db, $user_id
            )
        );
        //@mail("parambir.rudra@gmail.com","CRE Queue for $agency_url", "CRE Queue updated for $agency_url");
        json(1, 'Queue updated');
        
    }
    else if ($_REQUEST["param"] == "cretriggerstop") {
        $key = isset($_REQUEST['key'])?trim($_REQUEST['key']):"";        
        if($key == md5(DB_NAME)){   
            $user_id = isset($_POST['user_id'])?trim($_POST['user_id']):"";
            $result = $wpdb->get_row
            (
                $wpdb->prepare
                (
                    "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
                )
            );
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 1 WHERE user_id = %d", 
                     $user_id
                )
            );        
            
            // Start - code to delete extra urls, that are not scanned even after full scanning
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "DELETE FROM cre_urls WHERE is_running = 1 AND user_id = %d", $user_id            
                )
            );
            
            // END - code to delete extra urls, that are not scanned even after full scanning
            
            notifyuserrecom($user_id,$result);
        }
        die;
    }
    else if ($_REQUEST["param"] == "crehistory") {
        $user_id = user_id();
        $crehistory = $wpdb->get_results
        (
            $wpdb->prepare
            (
                "SELECT * FROM cre_history WHERE user_id = %d", $user_id
            )
        );
        
        ?>
            <div>                
                <table class="table table-condensed tblhist">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Total Urls</th>
                            <th>Total Issues </th>
                            <th>Issues Detail</th>
                            <th>Avg Score</th>
                            <th>Run Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($crehistory as $crehis){
                            ?>
                            <tr>
                                <td><?php echo $crehis->type == 'targetpage'?'Target Pages':'All Pages'; ?></td>
                                <td><?php echo $crehis->totalurls; ?></td>
                                <td><?php echo $crehis->totalissues; ?></td>
                                <td><?php 
                                    $issuesdet = json_decode($crehis->issues_detail);
                                    foreach($issuesdet as $key => $issue){
                                        echo "<div> ".  ucfirst($key)." issues : $issue</div>";
                                    }
                                ?></td>
                                <td><?php echo $crehis->avg_score; ?></td>
                                <td><?php echo $crehis->rundate; ?></td>
                            </tr>
                            <?php
                        }                    
                        ?>
                    </tbody>
                </table>
            </div>    
        <?php
                
        die;
    }
    else if ($_REQUEST["param"] == "singlepageanalysis") {
            
        //@mail('parambir@rudrainnovatives.com','Single page analysis call','Single page call from page');
        sleep(3);
        $key = isset($_REQUEST['key'])?trim($_REQUEST['key']):"";        
        if($key == md5(DB_NAME)){              
            $url = isset($_POST['url'])?trim($_POST['url']):"";            
            $pageindex = isset($_POST['pageindex'])?intval($_POST['pageindex']):0;
            $user_id = isset($_POST['user_id'])?trim($_POST['user_id']):"";
            $queue_id = isset($_POST['queue_id'])?intval($_POST['queue_id']):0;             
            newsinglepagecall($pageindex, $url, $user_id, $queue_id);
        }
        die;
    }    
    else if ($_REQUEST["param"] == "content_recommend") {  
        //@mail('parambir.rudra@gmail.com','CRE - '.site_url().' at '.date('Y-m-d'),'Campaign has been started on '.date('Y-m-d').' for agency '.site_url());
        sleep(3);
        $user_id = isset($_REQUEST['user_id'])?intval($_REQUEST['user_id']):0;
        if($user_id <= 0){
            die;
        }
        $baseurl = get_user_meta($user_id,'website',true); //isset($_REQUEST['url'])?trim($_REQUEST['url']):'';
        if($baseurl == ''){
            json(0, 'Empty Website');
        }
        $history_entry = isset($_REQUEST['history_entry'])?intval($_REQUEST['history_entry']):0;
        if($history_entry == 1){
            // add data in history
            enter_history_data($user_id);
        }        
            
        $user_trigger = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT user_trigger FROM wp_content_recommend WHERE user_id = %d", $user_id
            )
        );
        if(empty($user_trigger) || $user_trigger == 0){
            $user_trigger = 1;
        }
        
        $type = isset($_REQUEST['typerequest'])?$_REQUEST['typerequest']:''; 
        
        if($type == 'allpage'){
            $res = crawl_page($user_id, $baseurl);
        }
        else{            
            $res = target_pages($user_id);
        }                  
        //@mail("parambir.rudra@gmail.com","Crawl Result", json_encode($res));
        // billing code  
        $bilingenable = 0;
        if(defined("BILLING_ENABLE") && BILLING_ENABLE == 1){
            $bilingenable = 1;
        }
        if($bilingenable == 1){       
            $lmt = check_lp_all_limits();
            $limit = $lmt['pages_available'];
        }
        
        //$limit = 10; // temp
        //pr($lmt); die;
        // billing code        
                
        //temp code         
//        $re = '{"robots":0,"yoast":0,"sitemap":0,"urls":[{"0":"https:\/\/www.enfusen.com\/","1":"inbound marketing funnel","2":"how to build an email marketing campaign","3":"Do Not Add Keyword Here","4":"digital marketing","5":"creating a customer avatar"},{"0":"https:\/\/www.enfusen.com\/solutions\/"},{"0":"https:\/\/www.enfusen.com\/about\/"},{"0":"https:\/\/www.enfusen.com\/team\/"},{"0":"https:\/\/www.enfusen.com\/case-studies\/"},{"0":"https:\/\/www.enfusen.com\/resources\/"},{"0":"https:\/\/www.enfusen.com\/blog\/"},{"0":"https:\/\/www.enfusen.com\/videos\/"},{"0":"https:\/\/www.enfusen.com\/contact\/"},{"0":"https:\/\/www.enfusen.com\/enfusen-marketing-cloud-agency"},{"0":"https:\/\/www.enfusen.com\/enfusen-marketing-cloud-agency"},{"0":"http:\/\/www.enfusen.com\/max451-client-testimonial\/"},{"0":"http:\/\/www.enfusen.com\/max451-client-testimonial\/"},{"0":"http:\/\/www.enfusen.com\/rawhide-boys-ranch-client-testimonial\/"},{"0":"http:\/\/www.enfusen.com\/rawhide-boys-ranch-client-testimonial\/"},{"0":"http:\/\/www.enfusen.com\/cal-business-solutions-client-showcase\/"},{"0":"http:\/\/www.enfusen.com\/cal-business-solutions-client-showcase\/"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book\/"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book\/"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book\/#commentform"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book\/"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book-ch-1\/"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book-ch-1\/"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book-ch-1\/#commentform"},{"0":"https:\/\/www.enfusen.com\/data-driven-marketing-book-ch-1\/"},{"0":"https:\/\/www.enfusen.com\/intro-data-driven-marketing\/"},{"0":"https:\/\/www.enfusen.com\/intro-data-driven-marketing\/"},{"0":"https:\/\/www.enfusen.com\/intro-data-driven-marketing\/#commentform"},{"0":"https:\/\/www.enfusen.com\/intro-data-driven-marketing\/"},{"0":"https:\/\/www.enfusen.com\/about\/"},{"0":"https:\/\/www.enfusen.com\/resources\/"},{"0":"https:\/\/www.enfusen.com\/enfusen\/careers\/"},{"0":"https:\/\/www.enfusen.com\/training-events\/"},{"0":"https:\/\/www.enfusen.com\/support\/"},{"0":"https:\/\/www.enfusen.com\/enfusen\/faq\/"},{"0":"https:\/\/www.enfusen.com\/videos\/"},{"0":"https:\/\/www.enfusen.com\/enfusen-marketing-cloud-business"},{"0":"https:\/\/www.enfusen.com\/enfusen-marketing-cloud-agency"},{"0":"https:\/\/www.enfusen.com\/contact\/"},{"0":"https:\/\/www.enfusen.com\/privacy-policy\/"},{"0":"https:\/\/www.enfusen.com\/terms-service\/"},{"0":"https:\/\/www.enfusen.com\/sitemap\/"},{"0":"https:\/\/www.enfusen.com\/enfusen-marketing-cloud-agency"},{"0":"https:\/\/www.enfusen.com\/should-you-hire-a-seo-agency","1":"Just in time marketing","2":"digital marketing"},{"0":"https:\/\/www.enfusen.com\/roger-bryan-lands-top-50-online-marketing-influencers-to-watch-in-2016-list","1":"inbound marketing company","2":"healthcare marketing","3":"closed loop reporting"}]}';
//        $res = (array) json_decode($re);                        
//        $urlobj1 = 'https://www.enfusen.com/';
//        $urlobj2 = 'https://www.enfusen.com/blog/';
//        $urlobj3 = 'https://www.enfusen.com/demonstration-blog-post/';        
//        $urlobj4 = 'https://www.enfusen.com/is-predictive-analytics-for-your-company/';
//        
//        $allurls = array($urlobj1,$urlobj2,$urlobj3,$urlobj4);        
//        $res = array(
//            'urls' => $allurls
//        );
                        
        require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
        $browser = &new SimpleBrowser();
                
        $outerar = array(); $urlissues = array(); $ik = 1; $totalurls = count($res['urls']);
        $total_title_issues = 0; $total_meta_issues = 0; $total_content_issues = 0;
        $total_heading_issues = 0; $total_link_issues = 0; $total_image_issues = 0;
                
        $allurls = $res['urls'];  
        //@mail("parambir.rudra@gmail.com","All Urls", json_encode($allurls));
        if(empty($allurls)){
            $x = $wpdb->query
            (
                $wpdb->prepare
                (
                "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 0 WHERE user_id = %d", 
                 $user_id
                )
            );
            $user_info = get_userdata($user_id);
            $useremail = $user_info->data->user_email.',roger@enfusen.com,parambir@rudrainnovatives.com';                        
            $website = get_user_meta($user_id,'website',true);
            $siteurl = site_url();
            $sub = 'CRE failed to crawl result. You need to rerun it again.';            
            $body = 'Hi, <br/>br/> CRE has failed to crawl your website '.$website.' at agency '.$siteurl.'.'
                    . ' You need to rerun it again.';
            @mail($useremail,$sub, $body);            
            json(1, 'Done');
        }
        else {
                    
            foreach($allurls as $url){
                $aurl = arrtoobj($url);            
                $urlstrtosend = isset($aurl->{0})?$aurl->{0}:'';    
                if($urlstrtosend == ''){
                    continue;
                }
                // existing page keywords start
                $total = count((array) $url);
                $extkeywords = array();
                if($total > 1){
                    // if keywords also present in our database for url
                    $i = 0;
                    foreach($url as $key => $u){
                        if($i > 0){
                            $extkeywords[] = $u;
                        }
                        $i++;
                    }
                }

                if(!empty($extkeywords)){
                    $extkeywords = json_encode($extkeywords);
                }
                else{
                    $extkeywords = '';
                }

                // existing page keywords end

                $urlchk = trim(str_replace(array("https://","http://","www."), array("","",""), $urlstrtosend),"/");            
                $crepage = $wpdb->get_row
                (
                    $wpdb->prepare
                    (
                        "SELECT id FROM cre_urls WHERE user_id = %d AND TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.','')) like '%s'", $user_id, $urlchk
                    )
                );                                    

                if(empty($crepage)){
                    $wpdb->query
                    (
                        $wpdb->prepare
                        (
                            "INSERT INTO cre_urls(url, keyword, user_id, is_running, rundate, user_trigger) VALUES(%s, %s, %d, %d, '%s', %d)", 
                            $urlstrtosend, $extkeywords, $user_id, 1, date("Y-m-d H:i:s"), $user_trigger
                        )
                    );                

                    $lastid = $wpdb->insert_id;                
                }
                else{
                    $lastid = $crepage->id;
                    $wpdb->query
                    (
                        $wpdb->prepare
                        (
                            "UPDATE cre_urls SET keyword = %s, is_running = 1, rundate = '%s', user_trigger = %d WHERE id = %d", 
                            $extkeywords, date("Y-m-d H:i:s"), $user_trigger, $lastid
                        )
                    );                
                }

    //            $data = array(                
    //                'url' => $urlstrtosend,
    //                'rundate' => date("Y-m-d H:i:s"),
    //                'is_running' => 1,
    //                'keyword' => ''
    //            );
    //            
    //            $data = arrtoobj($data);                                   
               // array_push($outerar, $data);   

                $urlissues["$lastid"] = $urlstrtosend;            
                $ik++;                        
                if($bilingenable == 1){ 
                    // if billing enable then check limit, no of remaining page
                    if($ik > $limit){
                        break;
                    }                
                }
            }        
            
            if($type == 'allpage'){
                // Delete OLD Urls
                $wpdb->query
                (
                    $wpdb->prepare
                    (
                        "DELETE FROM cre_urls WHERE user_id = %d AND is_running = 0", $user_id
                    )
                ); 
            }
            
            $res['urls'] = $urlissues;        
            $crawl_result = json_encode($res); 
            //@mail("parambir.rudra@gmail.com","Url issues", json_encode($urlissues));
            // billing code            
            if($bilingenable == 1){            

                // rudra to show on billing page
                $total_scanned_pages = get_user_meta($user_id,'total_scanned_pages',true);
                if(empty($total_scanned_pages) || $total_scanned_pages == ''){
                    add_user_meta($user_id,'total_scanned_pages',count($urlissues));
                }
                else{
                    $total_scanned_pages = $total_scanned_pages + count($urlissues);
                    update_user_meta($user_id,'total_scanned_pages',$total_scanned_pages);
                }
                // rudra to show on billing page

                $lasttotal = $wpdb->get_var("SELECT lpf_used FROM wp_location_package_fields WHERE lpf_field = 'pages'");
                $totalused = $lasttotal + count($urlissues);
                $wpdb->query("UPDATE wp_location_package_fields SET lpf_used = $totalused WHERE lpf_field = 'pages'");            
            }
            // billing code

            if(empty($urlissues)){
                $x = $wpdb->query
                (
                    $wpdb->prepare
                    (
                    "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 0 WHERE user_id = %d", 
                     $user_id
                    )
                );            
            }
            else{
                //$final_result = json_encode($outerar);       
                $pass = add_global_queue(site_url(),$urlissues,$user_id);
                if($pass == 1){
                    $x = $wpdb->query
                    (
                        $wpdb->prepare
                        (
                        "UPDATE wp_content_recommend SET crawl_result = %s, auto_trigger = 0 WHERE user_id = %d", 
                         $crawl_result, $user_id
                        )
                    );                
                }
                else{
                    $x = $wpdb->query
                    (
                        $wpdb->prepare
                        (
                        "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 0 WHERE user_id = %d", 
                         $user_id
                        )
                    );                
                }
            }
        
            json(1, 'Done');
        }
    }
    else if ($_REQUEST["param"] == "campaignrunpage") {
        $url = isset($_POST['scanurl'])?trim($_POST['scanurl']):'';
        $pageindex = isset($_POST['pageindex'])?trim($_POST['pageindex']):'';
        $user_id = $UserID = $current_id = user_id();
        
        // biling code        
        $bilingenable = 0;
        if(defined("BILLING_ENABLE") && BILLING_ENABLE == 1){
            $bilingenable = 1;
        }
        
        if($bilingenable == 1){            
            $lmt = check_lp_all_limits();        
            $limit = $lmt['pages_available'];
            if($limit <= 0){                
                json(0, '<strong>Alert ! </strong> Agency monthly page run limit has been reached. You need to purchase add on for extra pages to run.');
            }
        }
        // biling code
        
        if($url != ''){
                                            
            $totalrun = $wpdb->get_var
            (
                $wpdb->prepare
                (
                    "SELECT count(id) as total FROM cre_urls WHERE user_id = %d AND is_running = 1", $user_id
                )
            );
            if($totalrun >= CRE_SINGLE_MAX_RUN){
                json(0, 'You can execute only '.CRE_SINGLE_MAX_RUN.' pages at a time with signle run. Either you need to wait for execution of a page or you can run campaign for all pages or for target pages.');
            }
                
            // check dynamically created favicon   
            $hasfavicon = get_user_meta($user_id, 'webfavicon', TRUE);
            if($hasfavicon == ''){
                $url = appendhttp($url);
                $parseurl = parse_url($url);
                $baseurl = $parseurl['host'];
                $serviceurl = 'http://icons.better-idea.org/allicons.json';
                $returndata = @file_get_contents($serviceurl.'?'.'url='.$baseurl);
                $returndata = json_decode($returndata);
                $faviconurl = 0;
                if(isset($returndata->icons[0]->url) && $returndata->icons[0]->url != ''){
                    $faviconurl = $returndata->icons[0]->url;                 
                }
                add_user_meta( $user_id, 'webfavicon', $faviconurl );
            }            
            // check dynamically created favicon
            
            
            $website = get_user_meta($user_id,'website',true);
            $url1 = trim(str_replace(array("http://","https://","www."), array("","",""), $url),"/");
            $url2 = trim(str_replace(array("http://","https://","www."), array("","",""), $website),"/");
            
            $currid = current_id();
            //if (strpos(strtolower($url1), strtolower($url2)) !== false || $url1 == $url2) {
                                       
                $rcommendcre = $wpdb->get_row
                (
                    $wpdb->prepare
                    (
                        "SELECT id FROM cre_urls WHERE id = %d AND user_id = %d", $pageindex, $user_id
                    )
                );
                $target_page_keywords = target_page_keywords($url, $user_id);
                if(!empty($target_page_keywords)){
                    $target_page_keywords = json_encode($target_page_keywords);
                }
                else{
                    $target_page_keywords = '';
                }
                
                if(empty($rcommendcre)){
                    $wpdb->query
                    (
                        $wpdb->prepare
                        (
                            "INSERT INTO cre_urls(url, keyword, user_id, is_running, rundate, user_trigger) VALUES(%s, %s, %d, %d, '%s', %d)", 
                            $url, $target_page_keywords, $user_id, 1, date("Y-m-d H:i:s"), $currid
                        )
                    );
                    $pageindex = $wpdb->insert_id;
                }
                else{
                    $wpdb->query
                    (
                        $wpdb->prepare
                        (
                            "UPDATE cre_urls SET is_running = 1, keyword = %s, user_trigger = %d, rundate = '%s' WHERE id = %d", 
                            $target_page_keywords, $currid, date("Y-m-d H:i:s"), $pageindex
                        )
                    );                    
                }
                
                $params = array('param'=>'singlepageanalysis','url'=> $url,'pageindex'=> $pageindex,'user_id'=>$user_id,'key'=>md5(DB_NAME));                                
                silent_post($params);                
                
                // billing code
                if($bilingenable == 1){ 
                    
                    // rudra to show on billing page
                    $total_scanned_pages = get_user_meta($user_id,'total_scanned_pages',true);
                    $total_scanned_pages = $total_scanned_pages + 1;
                    update_user_meta($user_id,'total_scanned_pages',$total_scanned_pages);
                    // rudra to show on billing page
                    
                    $lasttotal = $wpdb->get_var("SELECT lpf_used FROM wp_location_package_fields WHERE lpf_field = 'pages'");
                    $totalused = $lasttotal + 1;
                    $wpdb->query("UPDATE wp_location_package_fields SET lpf_used = $totalused WHERE lpf_field = 'pages'");  
                }
                // billing code
                
                json(1, 'CRE successfully started for this page.', $rs);
                
            //}
//            else{
//                json(0, 'We cannot run CRE for this url, because it is not matched with your website : '.$url1.' == '.$url2);
//            }
        }
        json(0, 'Empty URL');
    }    
    else if ($_REQUEST["param"] == "campaignrun") {
        
        $user_id = $UserID = user_id();
        /* temp blockage */
        if($user_id != 250){
            //json(0, 'You are not allowed to run campaign right now. Please contact with administrator for more information.');
        }        
        /* temp blockage */
        
        
        // biling code        
        $bilingenable = 0;
        if(defined("BILLING_ENABLE") && BILLING_ENABLE == 1){
            $bilingenable = 1;
        }
        
        if($bilingenable == 1){            
            $lmt = check_lp_all_limits();        
            $limit = $lmt['pages_available'];
            if($limit <= 0){
                json(0, '<strong>Alert ! </strong> Agency monthly page run limit has been reached. You need to purchase add on for extra pages to run.');
            }
        }
        // biling code
        
        
        $type = isset($_POST['type'])?$_POST['type']:'allpage';
        if($type != 'allpage'){
           if(check_has_target_pages($user_id) == FALSE){
               json(1, "No Active Target Page Found In Database");
           }
        }
        
        
        $currid = current_id();
        
        $recommend = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
            )
        );
        
        $history_entry = 0;        
        if(!empty($recommend)){
            
            if($recommend->trigger_report == 1){
                json(0, "Campaign is already running...");
            }
            
            $x = $wpdb->query
            (
                $wpdb->prepare
                (
                "UPDATE wp_content_recommend SET type = %s, trigger_report = %d, auto_trigger = %d, rundate = '%s', user_trigger = %d WHERE user_id = %d", 
                 $type, 1, 1, date('Y-m-d H:i:s'), $currid, $user_id
                )
            );    
            $history_entry = 1;
        }
        else{
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "INSERT INTO wp_content_recommend (type, user_id, trigger_report, auto_trigger, user_trigger, rundate, created_dt) "
                    . "VALUES(%s, %d, %d, %d, %d, '%s', '%s') ", 
                    $type, $user_id, 1, 1, $currid, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
                )
            );
            
        }        
                
        // check dynamically created favicon            
        $url = get_user_meta($user_id, 'website', TRUE);
        $url = appendhttp($url);
        $parseurl = parse_url($url);
        $baseurl = $parseurl['host'];
        $serviceurl = 'http://icons.better-idea.org/allicons.json';
        $returndata = @file_get_contents($serviceurl.'?'.'url='.$baseurl);
        $returndata = json_decode($returndata);
        $faviconurl = 0;
        if(isset($returndata->icons[0]->url) && $returndata->icons[0]->url != ''){
            $faviconurl = $returndata->icons[0]->url;                 
        }
        
        $hasfavicon = get_user_meta($user_id, 'webfavicon', TRUE);
        if($hasfavicon == ''){
            add_user_meta( $user_id, 'webfavicon', $faviconurl );
        }
        else{
            update_user_meta( $user_id, 'webfavicon', $faviconurl );
        }
        // check dynamically created favicon
        //@mail('parambir.rudra@gmail.com','Run Campaign','Campaign Starting');
        $params = array('param'=>'content_recommend','user_id'=>$user_id, "typerequest"=>$type, 'history_entry'=>$history_entry);
        silent_post($params);
        
        $msg = 'Your campaign for all pages has been added to the queue. This report does take some time to run. You will be notified as soon as your campaign is complete.';
        if($type == 'targetpage'){
            $msg = 'Your campaign for target pages has been added to the queue. This report does take some time to run. You will be notified as soon as your campaign is complete.';
        }
        
        json(1, $msg);
    }
    else if($_REQUEST["param"] == 'checkifurlscoming'){
        
        $user_id = user_id();
        $x = $wpdb->get_var
        (
            $wpdb->prepare
            (
            "SELECT auto_trigger FROM wp_content_recommend WHERE user_id = %d", 
             $user_id
            )
        );
        if($x == 0){
            
            ob_start(); // start output buffer    
            $checkifurlscoming = 1;
            $file = TR_COUNT_PLUGIN_DIR.'/views/cre_dashboard.php';
            include $file;
            
            $template = ob_get_contents(); // get contents of buffer    
            ob_end_clean();
            
            json(1, $template);
        }
        json(0, $x);
        
    }
    else if ($_REQUEST["param"] == "checkurlanalysis") {
        $user_id = $UserID = $current_id = user_id();
        $url = get_user_meta($user_id,'website',true);
        $idx = isset($_POST['idx'])?$_POST['idx']:'';
        $pageurl = isset($_POST['pageurl'])?$_POST['pageurl']:'';
        
        $rempagesscan = isset($_POST['rempagesscan'])?intval($_POST['rempagesscan']):0;
        $runstr = '';
        if($rempagesscan == 1){
            
            $rcommendruning = $wpdb->get_var
            (
                $wpdb->prepare
                (
                    "SELECT trigger_report FROM wp_content_recommend WHERE user_id = %d", $user_id
                )
            );
            
            if($rcommendruning == 1){
                $running = $wpdb->get_var
                (
                    $wpdb->prepare
                    (
                        'SELECT count(id) as total_running FROM cre_urls WHERE user_id = %d AND is_running = 1', $user_id
                    )
                );
                if($running > 0){
                    $total = $wpdb->get_var
                    (
                        $wpdb->prepare
                        (
                            'SELECT count(id) as total FROM cre_urls WHERE user_id = %d', $user_id
                        )
                    );

                    $runstr = $running." Pages Remaining Out Of $total Pages";
                }
            }
        }
        
        $sql = '';
        if($pageurl != ''){
            $idx = intval($idx);
            if($idx > 0){
                $sql = "SELECT id, result, is_running FROM cre_urls WHERE id = $idx AND is_running = 0";
                $resrec = $wpdb->get_results
                (
                    $wpdb->prepare
                    (
                        $sql, ''
                    )
                );  
                
            }
            else{
                // new url not in cre_urls table            
                $pageurl = str_replace(array("http://","https://","www."), array("","",""), $pageurl);

                $resrec = $wpdb->get_results
                (
                    $wpdb->prepare
                    (
                        "SELECT id, result, is_running FROM cre_urls WHERE user_id = %d AND is_running = 0 AND TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.','')) like '%s'", $user_id, $pageurl
                    )
                );  
            }
        }
        else{
            $idx = trim($idx);
            if($idx == ''){                
                json(3, $runstr);
            }
            $idxar = explode(",", $idx);
            $idxar = array_unique($idxar);
            $idx = implode(",", $idxar);
            $sql = "SELECT id, result, is_running FROM cre_urls WHERE id in($idx) AND is_running = 0";
            $resrec = $wpdb->get_results
            (
                $wpdb->prepare
                (
                    $sql, ''
                )
            );              
        }
        $aridex = array();
        if(!empty($resrec)){
             foreach($resrec as $rcommend){
                
                if($rcommend->result != ''){                
                    $rs = json_decode(($rcommend->result));                    
                    $arnew = array(
                        'idx' => $rcommend->id,                           
                    );
                    
                    if(is_object($rs)){
                        $arnew['issues'] = $rs->total_issues;                                          
                        $scorecls = '<span class="redscore">'.$rs->score.'</span>';
                        if( $rs->score >= 51 && $rs->score <= 79 ){
                            $scorecls = '<span class="yellowscore">'.$rs->score.'</span>';
                        }
                        else if($rs->score >= 80){                            
                            $scorecls = '<span class="greenscore">'.$rs->score.'</span>';
                        }
                        
                        $arnew['score'] = $scorecls;
                        $stscode = isset($rs->pagestatus)?$rs->pagestatus:200;
                    }
                    else{
                        $arnew['issues'] = $rs['total_issues'];
                        
                        $scorecls = '<span class="redscore">'.$rs['score'].'</span>';
                        if( $rs['score'] >= 51 && $rs['score'] <= 79 ){
                            $scorecls = '<span class="yellowscore">'.$rs['score'].'</span>';
                        }
                        else if($rs['score'] >= 80){                            
                            $scorecls = '<span class="greenscore">'.$rs['score'].'</span>';
                        }
                        $arnew['score'] = $scorecls;
                        $stscode = isset($rs['pagestatus'])?$rs['pagestatus']:200;
                    }
                    
                    if($stscode == 404){
                        $arnew['error'] = '404 Error';
                    }                                        
                    array_push($aridex, $arnew);                                        
                }            
            }
            
            json(1, $runstr, $aridex);
        }
        json(3, $runstr,$resrec);
    }
    else if ($_REQUEST["param"] == "getcrwaledurls") {
        $user_id = $UserID = $current_id = user_id();
        $url = get_user_meta($UserID,'website',TRUE);
        
        require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
        $browser = &new SimpleBrowser(); 
        $browser->get($url);
        $urls = $browser->getUrls();        
        
        if(empty($urls)){
            json(0, 'Web site is not crawled. Please enter empty URL from keyword profile page.');
        }
        $options = '';
              
        $arurls1 = str_replace(array("http://","https://"), array("",""), $url);
        $arurls1 = trim($arurls1,"/");
        
        foreach($urls as $ur){
            $arurls2 = parse_url(strtolower($ur));    
            
            if(isset($arurls2['host'])){                
                $url2 = str_replace(array("http://","https://"), array("",""), $arurls2['host']);                
                $url2 = trim($url2,"/");                
                if($arurls1 == $url2){
                    $options .= "<option value='$ur'>$ur</option>";
                }
            }            
        }
        json(1, 'Result',$options);
    }
    else if ($_REQUEST["param"] == "saveurlkeyword") {
        $user_id = $UserID = $current_id = user_id();
        $newurl = isset($_REQUEST['newurl'])?trim($_REQUEST['newurl']):'';
        $datdid = isset($_REQUEST['datdid'])?intval($_REQUEST['datdid']):1;
        $j = $datdid - 1;
        
        $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);       
        $keywordDat['landing_page'][$j] = array($newurl);
        update_user_meta($UserID, "Content_keyword_Site",$keywordDat);
        json(1, 'URL Assigned',$keywordDat);
    }
    else if ($_REQUEST["param"] == "triggerreportcontent") {
        
        $user_id = $UserID = $current_id = user_id();
        $loggedid = current_id();
        $x = $wpdb->query
        (
            $wpdb->prepare
                    (
                    "UPDATE wp_content_recommend SET trigger_report = %d, user_trigger = %d, auto_trigger = 0 WHERE user_id = %d", 
                    1, $loggedid, $user_id
            )
        );
        
        if($x){
            
            $key == '23dff44243tetet';
            $params = array('param'=>'recommendation_report','data'=>$data,'key'=>$key,'user_id'=>$user_id);        
            silent_post($params); 
            json(1, 'Report Generation Triggered. Please wait for sometime. You will notify soon through email or you can check after sometime.');
        }
        else{
            json(0, 'Some Error. Please try again');
        }
    }
    else if ($_REQUEST["param"] == "recommendation_report") {
        
        $key = isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '';
        $key = '23dff44243tetet';
        if ($key == '23dff44243tetet') {
            $UserID = $user_id = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : 0;
            $result = $wpdb->get_row
            (
                $wpdb->prepare
                (
                    "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
                )
            );
            
            if(!empty($result) && $result->trigger_report == 1){
            //if(!empty($result)){                
                try{
                    
                    require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
                    $browser = &new SimpleBrowser(); 
                    
                    $data = json_decode($result->scannedurls);
                    $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
                    set_time_limit(0);
                    foreach($data as $dat){
                        $j = $dat->datdid - 1;
                        $url = $keywordDat['landing_page'][$j][0];                                                            
                        $dat->url = $url;                        
                        $analysis = page_analysis($dat,$browser);
                        //@mail('parambir.rudra@gmail.com', 'Analysis End URL: '.$url, 'Analysis End for : '.$url);
                        $dat->analysis = $analysis;
                    }                                        
                                        
                    $final_result = json_encode($data);  
                    //@mail("parambir@rudrainnovatives.com","CRE Report",$final_result);
                    
                    $x = $wpdb->query
                    (
                        $wpdb->prepare
                        (
                        "UPDATE wp_content_recommend SET trigger_report = 0, result = %s WHERE user_id = %d", 
                         $final_result, $user_id
                        )
                    );

                    if($x == 1){
                      
                        notifyuserrecom($UserID,$result);                        
                        json(1, 'Result Fetched For Content');
                    }
                    json(1, 'Result Not Fetched for Content');
                }
                catch (Exception $e){
                    json(1, 'Exception in Result fetching : '.$e->getMessage());
                }
                
            }
            json(0, 'Action Not Triggered');
        }
       json(0, 'Some Error Occuured');   
    }
    
    else if ($_REQUEST["param"] == "checkanalytic") {

        try{
            $UserID = user_id();
            
            $parent_analytics_user_id = get_user_meta($UserID, 'parent_analytics_user_id', true);
            $s = '';
            
            if($parent_analytics_user_id == 0 || empty($parent_analytics_user_id)){
          
                $sql = "SELECT MCCUserID, AnalyticsToken FROM `clients_table` WHERE MCCUserID = $UserID";
                $conn = anconn();
                $result = mysqli_query($conn, $sql);            
                $client = $result->fetch_object();            
                if(empty($client)){
                    json(0, "Client not exist.");
                }
                if($client->AnalyticsToken == ''){
                    json(0, "Google Analytic not connected.",$client);
                }
                
                include_once get_template_directory() . '/analytics/AdWordsUtils.php';
                include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
                include_once get_template_directory() . '/analytics/AnalyticsUtils.php';
                $check_clients_table = GetAllRowsFromTableWithSpecifiedColumns('clients_table', ' MCCUserID,Name,AnalyticsToken ', "MCCUserID = $UserID", " `MCCUserID` ASC ");
                $_REQUEST['ClientID'] = $UserID;                                                                                                        
                $RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/analytics-settings/';
                PrevInitAdwordsUserSettings($user);                                        
                $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);
                $AllClientsFromDB = GetAccessTokensFromTable();
                $CurrentClient = GetCurrentClient($AllClientsFromDB, $UserID);
                LoadAnalyticsAccessTokenFromDB($CurrentClient);
                
                if (empty($GLOBALS["Analytics"]))/////////////////////
                    $GLOBALS["Analytics"] = new Google_Service_Analytics($GLOBALS["Client"]);
                
                try{
                    $accountsItems = $GLOBALS["Analytics"]->management_accounts->listManagementAccounts()->getItems();
                }
                catch(Exception $ex){
                    update_user_meta($UserID,'ga_connected',0);
                    json(0, "Google Analytic not connected.");
                }
            }
        }
        catch(Exception $e){
            $rs = $e;            
            $s = get_class($rs);
        }
        
        if($s == 'Google_Auth_Exception'){
            update_user_meta($UserID,'ga_connected',0);
            json(0, "Google Analytic not connected.");
        }        
        
        $k = update_user_meta($UserID,'ga_connected',1);        
        json(1, 'Connected',$k);
    }
    
    else if ($_REQUEST["param"] == "checkconversioncode") {
        
        $UserID = user_id();
        $locwebsite = get_user_meta($UserID, 'website', TRUE);
        $locwebsite = appendhttp($locwebsite);

        require_once TR_COUNT_PLUGIN_DIR . '/php-webdriver/vendor/autoload.php';
        try {
            
            $browser = strtolower(browsername($_SERVER['HTTP_USER_AGENT']));
            $host = 'http://localhost:4444/wd/hub'; // this is the default
            $capabilities = \Facebook\WebDriver\Remote\DesiredCapabilities::phantomjs();
            $driver = Facebook\WebDriver\Remote\RemoteWebDriver::create($host, $capabilities, 50000, 50000);
            $driver->get($locwebsite);
            $sString = $driver->getPageSource();
            $driver->quit();
            set_time_limit(60000);
            $has_str = 0;
            if (strpos($sString, 'analytics/conv_tracking.php') !== false) {
                $has_str = 1; // has code
            }

            $analytic_url = ANALYTICAL_URL;
            $analytic_url = str_replace(array('http://', 'https://'), array('', ''), $analytic_url);
            $analytic_url = trim($analytic_url, '/');

            $str2 = "['setSiteId', '" . $UserID . "']";
            $str3 = '["setSiteId","' . $UserID . '"]';

            if (strpos($sString, $str2) !== false) {
                if (strpos($sString, $analytic_url) !== false) {
                    $has_str = 2;
                }
            } else if (strpos($sString, $str3) !== false) {
                if (strpos($sString, $analytic_url) !== false) {
                    $has_str = 2;
                }
            }

            if ($has_str == 1) {
                update_user_meta($UserID,'tracking_code',0);
                json(0, 'Not Verified!! Tracking Code is available on website, but not latest code. Please replace it with new code.');
            } else if ($has_str == 2) {

                update_user_meta($UserID,'tracking_code',1);
                json(1, 'Verified!!  Tracking Code is available and is working fine.');
                
            } else {
                update_user_meta($UserID,'tracking_code',0);
                json(0, 'Not Verified!!  Tracking Code is not available.');
            }
        } catch (Exception $ex) {
            json(0, 'Something goes wrong. Please try agian.');
        }
    }
    
    else if ($_REQUEST["param"] == "keywordscanning") {
        
        $key = isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '';
        if ($key == '23423#$cc435354#$cc64fdgfdg5465#$cc675sddfsfdsf5645646@#$ccff') {
                        
            $data = isset($_REQUEST['data']) ? trim($_REQUEST['data']) : '';                       
            $user_id = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : 0;
            if ($user_id > 0) {
                $data = stripcslashes(stripcslashes($data));                
                $data = json_decode($data);
                
                $percent = 0;
                $arr_sess = array();
                $ij = 0;                
                foreach ($data as $dt) {                   
                    $keyword = trim($dt->keyword);                    
                    $url = trim($dt->url);
                    $datdid = trim($dt->datdid);
                    $url = appendhttp($url);
                    if(urlexist($url)){                  
                        $content = file_get_contents($url);
                        $content = strtolower($content); $keyw = strtolower($keyword); 
                        if (strpos($content, $keyw) !== false) {
                            $arrsess = array('available' => 1, 'datdid' => $datdid, 'keyword' => $keyword);
                        } else {
                            $arrsess = array('available' => 0, 'datdid' => $datdid, 'keyword' => $keyword);
                            $ij++;
                        }
                    }
                    else{
                        $arrsess = array('available' => 0, 'datdid' => $datdid, 'keyword' => $keyword);
                        $ij++;
                    }
                    array_push($arr_sess, $arrsess);                    
                }
                
                $x = $wpdb->query
                (
                    $wpdb->prepare
                            (
                            "UPDATE wp_content_recommend SET scannedurls = %s, urlscanning = %d WHERE user_id = %d", 
                            json_encode($arr_sess), 0, $user_id
                    )
                );                                
                
            }
        }

        die;
        
    } else if ($_REQUEST["param"] == "scankeywords") {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }        
        $user_id = user_id();
        $data = isset($_REQUEST['data'])?trim($_REQUEST['data']):'';
        $key = '23423#$cc435354#$cc64fdgfdg5465#$cc675sddfsfdsf5645646@#$ccff';
        
        $result = $wpdb->get_row
        (
            $wpdb->prepare
                    (
                    "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
            )
        );                
        
        if(empty($result)){
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "INSERT INTO wp_content_recommend (user_id, urlscanning, rundate, created_dt) "
                    . "VALUES(%d, %d, NOW(), NOW()) ", $user_id, 1
                )
            );
        }
        else{
            
            
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "INSERT INTO wp_content_recommend_hisory (user_id, scannedurls, urlscanning, result, rundate, created_dt) "
                    . "VALUES(%d, %s, %d, %s, '%s', '%s') ", 
                    $user_id, $result->scannedurls, $result->urlscanning, $result->result, $result->rundate, $result->created_dt
                )
            );
            
            $wpdb->query
            (
                $wpdb->prepare
                        (
                        "UPDATE wp_content_recommend SET scannedurls = '', urlscanning = %d, rundate = '%s' WHERE user_id = %d", 
                        1, date("Y-m-d H:i:s"), $user_id
                )
            );
        }
        $params = array('param'=>'keywordscanning','data'=>$data,'key'=>$key,'user_id'=>$user_id);        
        silent_post($params);                
        json(1, 'Scan processed');        
    }
    else if ($_REQUEST["param"] == "checkprogress") {
        
        $user_id = user_id();
        $data = $wpdb->get_row
        (
            $wpdb->prepare
                    (
                    "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
            )
        );
        
        $arr_sess = array();
        if(!empty($data)){            
            $arr_sess = json_decode($data->scannedurls);            
            json(1, $data->urlscanning,$arr_sess);        
        }
        json(0, '');
    }
    
    /*  ../ends  */
    
    /* get users survey basis mentors - custom */
    if ($_REQUEST["param"] == "get_survey_users") {
        $mentor_id = $_POST['ment_id'];
        $option_data = '';
        $users = $wpdb->get_results("SELECT * FROM wp_mentor_assign where mentor_id = '" . $mentor_id . "'");
        foreach ($users as $user) {
            $uemail = get_userdata($user->user_id);
            $option_data.="<li><input type='checkbox'  value='" . $uemail->ID . "' class='chkSt' name='st[]'> " . $uemail->user_email . "</li>";
        }
        echo $option_data;
        die;
    }
    /*  ../ends  */
    /* get users survey basis course - custom */
    if ($_REQUEST["param"] == "get_survey_users_by_course") {
        $course_id = $_POST['cour_id'];
        $option_data = '';
        $users = $wpdb->get_results("SELECT * FROM wp_enrollment where course_id = '" . $course_id . "'");
        foreach ($users as $user) {
            $uemail = get_userdata($user->user_id);
            $option_data.="<li><input type='checkbox'  value='" . $uemail->ID . "' class='chkSt' name='st[]'> " . $uemail->user_email . "</li>";
        }
        echo $option_data;
        die;
    }
    /*  ../ends  */

    /* save community call */
    if ($_REQUEST["param"] == "save_community_title") {
        $lastId = '';
        $call_title = $_REQUEST['call_title'];
        $course_id = $_REQUEST['course_id'];
        if ($call_title == '') {
            json(0, 'Title is required');
        }
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . community_call() . " (course_id,call_heading) "
                        . "VALUES (%d,%s)", $course_id, $call_title
                )
        );
        //json(1,'Call title Added.',$wpdb->insert_id);
        echo $wpdb->insert_id;
        die;
    }
    /* ../ends */
    /* update community title */
    if ($_REQUEST["param"] == "update_community_title") {
        $call_title = $_REQUEST['call_title'];
        $id = $_REQUEST['id'];
        if ($call_title == '') {
            json(0, 'Title is required');
        }
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . community_call() . " SET call_heading = %s  WHERE id = %d", $call_title, $id
                )
        );
        //json(1,'Call title Added.',$wpdb->insert_id);
        echo $wpdb->insert_id;
        die;
    }
    /* ../ends */

    /* delete community call */
    if ($_REQUEST["param"] == "DeleteCommCall") {

        $call_id = $_REQUEST['call_id'];
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . community_call() . " WHERE id = %d", $call_id
                )
        );

        json(1, 'Call Deleted Successfully');
        die;
    }
    /* ../ends */

    /* delete comm doc file */
    if ($_REQUEST["param"] == "del_comdoc_file") {
        global $wpdb;
        $doc_file = $_REQUEST['doc_file'];
        $call_heading = $_REQUEST['call_heading'];
        $course_id = $_REQUEST['course_id'];
        $call_details = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . community_call() . " WHERE course_id = %d and call_heading='%s'", $course_id, $call_heading
                )
        );

        $getStringValue = $call_details->doc_file_links;
        $replacedString = str_replace($doc_file, "", $getStringValue);

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "Update " . community_call() . " set doc_file_links=%s WHERE course_id = %d and call_heading=%s", $replacedString, $course_id, $call_heading
                )
        );

        json(1, 'Doc Deleted Successfully');

        die;
    }
    /* ../ends */
    /* delete comm url file */
    if ($_REQUEST["param"] == "del_comlink_file") {
        global $wpdb;
        $url_file = $_REQUEST['url_file'];
        $call_heading = $_REQUEST['call_heading'];
        $course_id = $_REQUEST['course_id'];
        $call_details = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . community_call() . " WHERE course_id = %d and call_heading='%s'", $course_id, $call_heading
                )
        );

        $getStringValue = $call_details->comm_hlp_links;
        $replacedString = str_replace($url_file, "", $getStringValue);

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "Update " . community_call() . " set comm_hlp_links=%s WHERE course_id = %d and call_heading=%s", $replacedString, $course_id, $call_heading
                )
        );

        json(1, 'URL Deleted Successfully');

        die;
    }
    /* ../ends */
    /* delete comm note file */
    if ($_REQUEST["param"] == "del_comNotes_file") {
        global $wpdb;
        $note_file = $_REQUEST['note_file'];
        $call_heading = $_REQUEST['call_heading'];
        $course_id = $_REQUEST['course_id'];
        $call_details = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . community_call() . " WHERE course_id = %d and call_heading='%s'", $course_id, $call_heading
                )
        );

        $getStringValue = $call_details->comm_notes;
        $replacedString = str_replace($note_file, "", $getStringValue);

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "Update " . community_call() . " set comm_notes=%s WHERE course_id = %d and call_heading=%s", $replacedString, $course_id, $call_heading
                )
        );

        json(1, 'URL Deleted Successfully');

        echo $note_file;

        die;
    }
    /* ../ends */
    if ($_REQUEST["param"] == "add_course") {

        $user_id = $current_user->ID;
        $now = date("Y-m-d H:i:s");
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
        $description = isset($_POST['description']) ? htmlspecialchars($_REQUEST["description"]) : '';
        $description = stripcslashes($description);

        if ($title == '') {
            json(0, 'Title is required');
        }

        $course = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT id,user_ids FROM " . courses() . " WHERE id = %d", $course_id
                )
        );

        if (!empty($course)) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . courses() . " SET title = %s, description = %s WHERE id = %d", $title, $description, $course_id
                    )
            );

            json(1, 'Course Updated');
        } else {

            $ord = $wpdb->get_var
                    (
                    $wpdb->prepare
                            (
                            "SELECT MAX(ord) FROM " . courses(), ""
                    )
            );

            $ord = $ord + 1;

            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . courses() . " (ord, title, description, created_by, created_dt, updated_by) "
                            . "VALUES (%d, %s, %s, %d, '%s', %d)", $ord, $title, $description, $user_id, $now, $user_id
                    )
            );

            $lastid = $wpdb->insert_id;
            $arr = array("lastid" => $lastid);
            json(1, 'Course Created', $arr);
        }
    } else if ($_REQUEST["param"] == "enroll_user") {

        $uemail = isset($_POST["uemail"]) ? htmlspecialchars($_POST["uemail"]) : 0;
        $user = get_user_by("email", $uemail);
        if (empty($user)) {
            json(0, 'This email is not registered with MCC');
        }
        $course_id = isset($_POST["course_id"]) ? intval($_POST["course_id"]) : 0;
        $is_enrol = isset($_POST["is_enrol"]) ? intval($_POST["is_enrol"]) : 0;
        $user_id = $user->data->ID;
        $is_enrolled = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
                )
        );

        if ($is_enrolled >= 1) {
            json(0, 'User has been already enrolled for this course.');
        }
        if ($is_enrol == 0) {
            json(1, 'User is available for enrollment.');
        }
        $now = date("Y-m-d H:i:s");
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . enrollment() . " (course_id, user_id, created_dt) "
                        . "VALUES (%d, %d, '%s')", $course_id, $user_id, $now
                )
        );
        permission_email_course($course_id, $user_id);
        json(1, 'User has been sucessfully enrolled for this course');
    } else if ($_REQUEST["param"] == "add_mentor") {

        $uemail = isset($_POST["memail"]) ? htmlspecialchars($_POST["memail"]) : 0;
        $user = get_user_by("email", $uemail);
        if (empty($user)) {
            json(0, 'This email is not registered with MCC');
        }

        $user_id = $user->data->ID;
        $userrole = new WP_User($user_id);
        $u_role = $userrole->roles[0];

//            if($u_role != MENTOR_ROLE){
//                json(0,'User Role is not mentor. Please change user role in User section.');
//            }

        $course_id = isset($_POST["course_id"]) ? intval($_POST["course_id"]) : 0;
        $is_enrol = isset($_POST["is_enrol"]) ? intval($_POST["is_enrol"]) : 0;

        $course = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT id,title,mentor_ids FROM " . courses() . " WHERE id = %d", $course_id
                )
        );
        if (empty($course)) {
            json(0, 'Invalid Course.');
        }

        $mentor_ids = array();
        $mentor_ids = explode(",", $course->mentor_ids);
        if (in_array($user_id, $mentor_ids)) {
            json(0, 'Mentor already added to this course');
        }
        if ($is_enrol == 0) {
            json(1, 'Mentor is available.');
        }
        array_push($mentor_ids, $user_id);
        $now = date("Y-m-d H:i:s");
        $mentor_ids = implode(",", $mentor_ids);
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . courses() . " SET mentor_ids = %s WHERE id = %d", $mentor_ids, $course_id
                )
        );
        mentor_add_course_email($course_id, $user_id);
        json(1, 'Mentor has been sucessfully added for this course');
    } else if ($_REQUEST["param"] == "revoke_user") {

        $enrol_id = isset($_POST["enrol_id"]) ? intval($_POST["enrol_id"]) : 0;
        $enrolled = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . enrollment() . " WHERE id = %d", $enrol_id
                )
        );
        if (empty($enrolled)) {
            json(1, 'Invalid Enrollment');
        }

        $course_id = $enrolled->course_id;
        $user_id = $enrolled->user_id;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . enrollment() . " WHERE id = %d", $enrol_id
                )
        );
        permission_revoke_course($course_id, $user_id);
        json(1, 'User permissions has been sucessfully revoked for this course');
    } else if ($_REQUEST["param"] == "remove_mentor") {

        $u_id = isset($_POST["u_id"]) ? intval($_POST["u_id"]) : 0;
        $course_id = isset($_POST["course_id"]) ? intval($_POST["course_id"]) : 0;

        $hasrecord = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT mentor_ids FROM " . courses() . " WHERE FIND_IN_SET($u_id,mentor_ids) AND id = %d", $course_id
                )
        );
        if (empty($hasrecord)) {
            json(1, 'Invalid Request');
        }

        $mentor_ids = explode(",", $hasrecord->mentor_ids);
        $new_str = "";
        foreach ($mentor_ids as $mentor_id) {
            if ($mentor_id != $u_id) {
                $new_str .= $mentor_id . ",";
            }
        }
        $new_str = substr($new_str, 0, -1);

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . courses() . " SET mentor_ids = %s WHERE id = %d", $new_str, $course_id
                )
        );
        remove_from_course($course_id, $u_id);
        json(1, 'Mentor has been sucessfully removed from this course');
    } else if ($_REQUEST["param"] == "delete_course") {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

        deletemediacourse($id);

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . resources() . " WHERE course_id = %d", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . lessons() . " WHERE module_id IN (SELECT id FROM " . modules() . " WHERE course_id = %d)", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . modules() . " WHERE course_id = %d", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . courses() . " WHERE id = %d", $id
                )
        );

        json(1, 'Course Deleted');
    } else if ($_REQUEST["param"] == "add_module") {

        $user_id = $current_user->ID;
        $now = date("Y-m-d H:i:s");
        $module_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
        $link = isset($_POST['link']) ? htmlspecialchars(trim($_POST['link'])) : '';

        $description = isset($_POST['description']) ? htmlspecialchars($_REQUEST["description"]) : '';
        $description = stripcslashes($description);

        if ($title == '' || $course_id == 0) {
            json(0, 'Title and course id are required');
        }

        if ($module_id > 0) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . modules() . " SET title = %s, description = %s, external_link = %s WHERE id = %d", $title, $description, $link, $module_id
                    )
            );
            json(1, 'Module Updated');
        } else {



            $ord = $wpdb->get_var
                    (
                    $wpdb->prepare
                            (
                            "SELECT MAX(ord) FROM " . modules() . " WHERE course_id = %d", $course_id
                    )
            );

            $ord = $ord + 1;
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . modules() . " (ord, course_id, title, description, external_link, created_by, created_dt, updated_by) "
                            . "VALUES (%d, %d, %s, %s, %s, %d, '%s', %d)", $ord, $course_id, $title, $description, $link, $user_id, $now, $user_id
                    )
            );

            json(1, 'Module Created');
        }
    } else if ($_REQUEST["param"] == "delete_module") {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $course_id = isset($_POST["course_id"]) ? intval($_POST["course_id"]) : 0;

        deletemediamodule($id);
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . resources() . " WHERE module_id = %d", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . lessons() . " WHERE module_id = %d", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . modules() . " WHERE id = %d", $id
                )
        );
        updatehours_and_resources($id, $course_id, 0);
        json(1, 'Module Deleted');
    } else if ($_REQUEST["param"] == "add_lesson") {

        $user_id = $current_user->ID;
        $now = date("Y-m-d H:i:s");
        $lesson_id = isset($_POST['lessid']) ? intval($_POST['lessid']) : 0;
        $module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
        //$hours = isset($_POST['hours'])?htmlspecialchars(trim($_POST['hours'])):0;            
        $link = isset($_POST['link']) ? htmlspecialchars(trim($_POST['link'])) : '';
        $description = isset($_POST['description']) ? htmlspecialchars($_REQUEST["description"]) : '';
        $description = stripcslashes($description);

        if ($title == '' || $module_id == 0) {
            json(0, 'Title, Time and module id are required');
        }

        if ($lesson_id > 0) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . lessons() . " SET title = %s, description = %s, external_link = %s WHERE id = %d", $title, $description, $link, $lesson_id
                    )
            );

            json(1, 'Lesson Updated');
        } else {

            $ord = $wpdb->get_var
                    (
                    $wpdb->prepare
                            (
                            "SELECT MAX(ord) FROM " . lessons() . " WHERE module_id = %d", $module_id
                    )
            );

            $ord = $ord + 1;

            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . lessons() . " (ord, module_id, title, description, external_link, created_by, created_dt, updated_by) "
                            . "VALUES (%d, %d, %s,  %s, %s, %d, '%s', %d)", $ord, $module_id, $title, $description, $link, $user_id, $now, $user_id
                    )
            );

            json(1, 'Lesson Created');
        }
    } else if ($_REQUEST["param"] == "delete_lesson") {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        deletemedia($id);
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . resources() . " WHERE lesson_id = %d", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . lessons() . " WHERE id = %d", $id
                )
        );
        updatehours_and_resources($module_id, $course_id, $id);
        json(1, 'Lesson Deleted');
    } else if ($_REQUEST["param"] == "add_resource") {

        $user_id = $current_user->ID;
        $now = date("Y-m-d H:i:s");
        $resource_id = isset($_POST['resid']) ? intval($_POST['resid']) : 0;
        $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
        $module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
        $button_type = isset($_POST['button_type']) ? htmlspecialchars(trim($_POST['button_type'])) : 'mark';


        $hours = isset($_POST['hours']) ? htmlspecialchars(trim($_POST['hours'])) : 0;
        $link = isset($_POST['link']) ? htmlspecialchars(trim($_POST['link'])) : '';

        $description = isset($_POST['description']) ? htmlspecialchars($_REQUEST["description"]) : '';
        $description = stripcslashes($description);

        if ($title == '' || $module_id == 0 || $lesson_id == 0) {
            json(0, 'Title, module id, Lesson Id are required');
        }

        if ($resource_id > 0) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . resources() . " SET title = %s, description = %s, total_hrs = %s, external_link = %s, "
                            . "button_type = %s WHERE id = %d", $title, $description, $hours, $link, $button_type, $resource_id
                    )
            );
            updatehours_and_resources($module_id, $course_id, $lesson_id);
            json(1, 'Exercise Updated');
        } else {

            $ord = $wpdb->get_var
                    (
                    $wpdb->prepare
                            (
                            "SELECT MAX(ord) FROM " . resources() . " WHERE lesson_id = %d", $lesson_id
                    )
            );

            $ord = $ord + 1;

            $result = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . resources() . " (ord, course_id, module_id, lesson_id, title, description, total_hrs, external_link, button_type, created_by, created_dt, updated_by) "
                            . "VALUES (%d, %d, %d, %d, %s,  %s, %s, %s, %s, %d, '%s', %d)", $ord, $course_id, $module_id, $lesson_id, $title, $description, $hours, $link, $button_type, $user_id, $now, $user_id
                    )
            );
            updatehours_and_resources($module_id, $course_id, $lesson_id);
            json(1, 'Exercise Created', $result);
        }
    } else if ($_REQUEST["param"] == "delete_resource") {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . resources() . " WHERE id = %d", $id
                )
        );

        updatehours_and_resources($module_id, $course_id, $lesson_id);
        json(1, 'Exercise Deleted');
    } else if ($_REQUEST["param"] == "save_settings") {
        $arr = $_POST;
        $i = 0;
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : 0;

        if (count($ids) > 0) {
            foreach ($ids as $id) {

                $key = "key_$id";
                $keyname = $_POST["$key"];

                $valkey = "val_$id";
                $value = $_POST["$valkey"];

                $show = 0;
                $showelement = "show_$id";
                if (isset($_POST["$showelement"])) {
                    $show = 1;
                }

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . setting() . " SET keyname = %s, keyvalue = %s, is_show = %d WHERE id = %d", $keyname, $value, $show, $id
                        )
                );
                $i++;
            }

            if ($i > 0)
                json(1, 'Settings Saved');
        }
        json(0, 'Problem In Saving. Please try after again.');
    }


    
  else if ($_REQUEST["param"] == "mark_resource") {

        $user_id = get_current_user_id();


        $userrole = new WP_User($user_id);
        $u_role = $userrole->roles[0];
        $uid = isset($_POST['uidadmincase']) ? intval(($_POST['uidadmincase'])) : 0;
        if ($uid > 0) {
            $user_id = $uid;
        }

        $resource_id = isset($_POST["resource_id"]) ? intval($_POST["resource_id"]) : 0;
        $status = isset($_POST["resource_id"]) ? htmlspecialchars($_POST["status"]) : 'unmarked';
        $sts = 0;
        if ($status == 'unmarked') {
            $sts = 1;
        }

        $resource = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT r.course_id, r.title as resource_title, c.title as course_title
						FROM " . resources() . " r INNER JOIN " . courses() . " c ON r.course_id
						= c.id WHERE r.id = %d", $resource_id
                )
        );




        if (empty($resource)) {
            json(0, 'Invalid Exercise');
        }


        $enroll_id = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT id FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $resource->course_id, $user_id
                )
        );
        if ($enroll_id == '' || empty($enroll_id)) {
            json(0, 'Invalid Enroll_id');
        }

        $now = date("Y-m-d H:i:s");
        // insert
        if ($sts == 1) {
            $res = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . resource_status() . " (enrollment_id, course_id, resource_id, user_id, created_dt) "
                            . "VALUES (%d, %d, %d, %d, '%s')", $enroll_id, $resource->course_id, $resource_id, $user_id, $now
                    )
            );


            $url = site_url() . "/" . PAGE_SLUG . "?exercise_detail=" . $resource_id;

            /* sending mail code */

            $email_data = $wpdb->get_results("select * from wp_mentor_assign where user_id='" . $user_id . "'");
            $to_id = '';
            $from_id = $user_id;
            foreach ($email_data as $results) {
                $to_id = $results->mentor_id;
                if ($to_id > 0) {
                    emailtocoachnotify($to_id, $from_id, "marked", $resource->course_title, $resource->resource_title, "", "", "Marked", $url);
                }
            }


            if ($to_id == 0) {
                $admin_email = get_option('admin_email');
                $get_admin_id = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT ID FROM $wpdb->users WHERE user_email = %s", $admin_email
                        )
                );
                $to_id = $get_admin_id;
                emailtocoachnotify($to_id, $from_id, "marked", $resource->course_title, $resource->resource_title, "", "", "Marked", $url);
            }

            /*             * ************************************* */

            json(1, 'Exercise marked');
        } else {
            // delete
            $res = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . resource_status() . " WHERE user_id = %d AND resource_id = %d", $user_id, $resource_id
                    )
            );
            json(1, 'Exercise unmarked');
        }
    } else if ($_REQUEST["param"] == "listenrolled") {

        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

        $usertbl = $wpdb->prefix . "users";

        $enrolledby = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT e.user_id,e.status,u.display_name,u.user_email FROM " . enrollment() . " e INNER JOIN "
                        . "$usertbl u ON e.user_id = u.ID WHERE course_id = %d", $course_id
                )
        );

        json(1, '', $enrolledby);
    } else if ($_REQUEST["param"] == "listmentorscourses") {

        $ids = isset($_POST['ids']) ? htmlspecialchars($_POST['ids']) : 0;

        $usertbl = $wpdb->prefix . "users";

        $useres = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT ID,display_name,user_email FROM "
                        . "$usertbl WHERE ID IN(%s)", $ids
                )
        );

        json(1, '', $useres);
    } else if ($_REQUEST["param"] == "add_call") {


        $callid = isset($_POST['callid']) ? intval($_POST['callid']) : 0;

        $iscall = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT id,is_accepted FROM " . mentorcall() . " WHERE id = %d", $callid
                )
        );

        $student_user = isset($_POST['student_user']) ? intval($_POST['student_user']) : 0;
        $meetinglink = isset($_POST['meetinglink']) ? htmlspecialchars($_POST['meetinglink']) : '';
        $course_id = isset($_POST['courseid']) ? intval($_POST['courseid']) : 0;
        $datecall = isset($_POST['datecall']) ? htmlspecialchars($_POST['datecall']) : date("Y-m-d H:i:s");
        $datecall = date("Y-m-d H:i:s", strtotime($datecall));

        $mentor_id = isset($_POST['mentorselect']) ? intval($_POST['mentorselect']) : 0;
        $user_id = $current_user->data->ID;
//            
//            $wpdb->query
//                        (
//                        $wpdb->prepare
//                                (
//                                "UPDATE " . mentorcall()  . " SET status = 'cancelled' WHERE course_id = %d",$course_id                                    
//                        )
//                );

        $recur = 0;
        if (isset($_POST['recurcall'])) {
            $recur = 1;
        }
        $msd = "Mentor call re-scheduled again.";
        $is_accepted = 0;

        $guid = md5(mt_rand(9999, 100099999) . time());

        if (empty($iscall)) {
            $is_accepted = $iscall->is_accepted;
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . mentorcall() . " (guid, course_id, user_id, link, mentor, mentor_call, mentor_id, created_by, recur_call) "
                            . "VALUES (%s, %d, %d, %s, %s, '%s', %d, %d, %d)", $guid, $course_id, $student_user, $meetinglink, '', $datecall, $mentor_id, $user_id, $recur
                    )
            );
            $msd = "Mentor call scheduled successfully.";
            $callid = $wpdb->insert_id;
        } else {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . mentorcall() . " SET guid = %s, course_id = %d, user_id = %d, link = %s, mentor_call = '%s', recur_call = %d "
                            . " WHERE id = %d", $guid, $course_id, $student_user, $meetinglink, $datecall, $recur, $callid
                    )
            );
            $updt = 1;
        }

        if (isset($_POST['notifyuser'])) {
            // Send notification to enrolled users if checked                
            if ($mentor_id != $user_id) {
                $current_user = get_user_by("id", $mentor_id);
            }
            notifyenrolleduser($callid, $guid, $current_user, $course_id, $student_user, $meetinglink, $datecall, $updt, $is_accepted);
        }

        json(1, $msd);
    } else if ($_REQUEST["param"] == "cancel_call") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $user_id = $current_user->data->ID;
        $iscall = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT id,status,mentor_id FROM " . mentorcall() . " WHERE id = %d", $id
                )
        );
        if (empty($iscall)) {
            json(0, "Invalid Call");
        }
        if ($iscall->status != 'active') {
            json(0, "Call is not active already");
        }
        $mentor_id = $iscall->mentor_id;
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . mentorcall() . " SET status = 'cancelled' WHERE id = %d", $id
                )
        );

        /* email to user, for cancellation */

        if ($mentor_id != $user_id) {
            $current_user = get_user_by("id", $mentor_id);
        }

        notifyuserforcancell($current_user, $id);

        /* email to user, for cancellation */

        json(1, 'Mentor Call Cancelled');
    } else if ($_REQUEST["param"] == "delete_call") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $status = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT status FROM " . mentorcall() . " WHERE id = %d", $id
                )
        );
        if ($status == 'active') {
            json(0, 'Mentor call is active. Cancel it to delete.');
        }
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . mentorcall() . " WHERE id = %d", $id
                )
        );

        json(1, 'Mentor Call Deleted');
    } else if ($_REQUEST["param"] == "add_projectexcersie") {

        $user_id = $current_user->ID;
        $now = date("Y-m-d H:i:s");
        $exid = isset($_POST['exid']) ? intval($_POST['exid']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;

        $type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type'])) : '';
        if ($type == '') {
            json(0, 'Something going wrong. please refresh page and try again.');
        }

        if ($type != 'module' && $type != 'course') {
            json(0, 'Type must be module or course.');
        }

        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
        $hours = isset($_POST['hours']) ? htmlspecialchars(trim($_POST['hours'])) : '';
        $description = isset($_POST['description']) ? htmlspecialchars($_REQUEST["description"]) : '';
        $description = stripcslashes($description);

        if ($title == '') {
            json(0, 'Title is required');
        }
        $status = 0;

        if (isset($_POST['isenabled'])) {
            $status = 1;
        }


        if ($exid == 0) {
            $rs = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . project_exercise() . " (type, status, module_id, course_id, title, description, total_hrs, created_by, created_dt, updated_by) "
                            . "VALUES (%s, %d, %d, %d, %s, %s, %s, %d, '%s', %d)", $type, $status, $module_id, $course_id, $title, $description, $hours, $user_id, $now, $user_id
                    )
            );
            if (isset($_POST['isenabled'])) {
                update_hours($module_id, $course_id, 0, $hours);
            }
        } else {

            if ($module_id > 0) {
                $projex = $wpdb->get_row(
                        $wpdb->prepare
                                (
                                "SELECT total_hrs,status FROM " . project_exercise() . " WHERE module_id = %d", $module_id
                        )
                );
            } else {

                $projex = $wpdb->get_row(
                        $wpdb->prepare
                                (
                                "SELECT total_hrs,status FROM " . project_exercise() . " WHERE course_id = %d", $course_id
                        )
                );
            }

            $projhrs = $projex->total_hrs;

            $projsts = $projex->status;
            $rs = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . project_exercise() . " SET status = %d, title = %s, description = %s, total_hrs = %s "
                            . "WHERE id = %d", $status, $title, $description, $hours, $exid
                    )
            );

            if (isset($_POST['isenabled'])) {
                if ($projsts == 0)
                    update_hours($module_id, $course_id, 0, $hours);
                else
                    update_hours($module_id, $course_id, $projhrs, $hours);
            }
            else {
                if ($projsts == 1)
                    update_hours($module_id, $course_id, $projhrs, 0);
            }
        }

        json(1, 'Project Exercise Saved');
    }
    else if ($_REQUEST["param"] == "get_exercise") {

        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $type = isset($_REQUEST['type']) ? htmlspecialchars($_REQUEST['type']) : '';
        if ($type == 'course') {
            $detail = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . project_exercise() . " WHERE course_id = %d AND module_id = 0", $id
                    )
            );
        } else {
            $detail = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . project_exercise() . " WHERE module_id = %d", $id
                    )
            );
        }

        $exid = 0;
        if (!empty($detail)) {
            $exid = $detail->id;
        }

        $usertbl = $wpdb->prefix . "users";
        $pojects = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT u.user_email,u.display_name,pe.type,pe.title,pe.description,pe.total_hrs,p.links "
                        . " FROM " . projects() . " p INNER JOIN " . project_exercise() . " pe ON p.exercise_id = pe.id INNER JOIN "
                        . "$usertbl u ON p.user_id = u.ID WHERE p.exercise_id = %d", $exid
                )
        );

        if (!empty($detail)) {
            $desc = html_entity_decode($detail->description);
            $detail->desc = $desc;
        }

        $ar = array('info' => $detail, "projects" => $pojects);

        json(1, 'detail', $ar);
    } else if ($_REQUEST["param"] == "get_submissions") {

        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

        $usertbl = $wpdb->prefix . "users";
        $pojects = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT u.user_email,u.display_name,pe.type,pe.title,pe.description,pe.total_hrs,p.links "
                        . " FROM " . projects() . " p LEFT JOIN " . project_exercise() . " pe ON p.exercise_id = pe.id INNER JOIN "
                        . "$usertbl u ON p.user_id = u.ID WHERE p.resource_id = %d", $id
                )
        );

        $ar = array("projects" => $pojects);

        json(1, 'detail', $ar);
    } else if ($_REQUEST["param"] == "submit_links") {


        //print_r($_FILES);
        //die("hello");
        $user_id = get_current_user_id();

        $userrole = new WP_User($user_id);
        $u_role = $userrole->roles[0];
        if ($_REQUEST['do'] == "noupdate") {

            if ($u_role == 'administrator' || isagencylocation()) {
                $uid = isset($_POST['uidadmincase']) ? intval(($_POST['uidadmincase'])) : 0;
                if ($uid > 0) {
                    $user_id = $uid;
                }
            }
            $now = date("Y-m-d H:i:s");
            $exe_id = isset($_POST['proj']) ? intval(trim($_POST['proj'])) : 0;
            $links = isset($_POST['links']) ? htmlspecialchars(trim($_POST['links'])) : '';
            $dattyp = isset($_POST['dattyp']) ? htmlspecialchars(trim($_POST['dattyp'])) : '';
            $resourceurl = site_url() . "/" . PAGE_SLUG . "?exercise_detail=" . $exe_id;
            //print_r($exe_id);die;

            if ($links == '') {
                //json(0,'Please submit links');
            }
            if ($dattyp == 'exercise') {
                $proj_exe = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(id) as total FROM " . project_exercise() . " WHERE id = %d", $exe_id
                        )
                );
                if ($proj_exe == 0)
                    json(0, 'Invalid Project');

                $wpdb->query(
                        $wpdb->prepare
                                (
                                "DELETE FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", $exe_id, $user_id
                        )
                );


                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . projects() . " (user_id, exercise_id, links, created_by, created_dt, updated_by) "
                                . "VALUES (%d, %d, %s, %d, '%s', %d)", $user_id, $exe_id, $links, $user_id, $now, $user_id
                        )
                );
            }
            else {

                $resource_id = $exe_id;
                $resource = $wpdb->get_row(
                        $wpdb->prepare
                                (
                                "SELECT * FROM " . resources() . " WHERE id = %d", $resource_id
                        )
                );
                if (empty($resource))
                    json(0, 'Invalid Resource');

                $wpdb->query(
                        $wpdb->prepare
                                (
                                "DELETE FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", $resource_id, $user_id
                        )
                );


                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . projects() . " (user_id, resource_id, links, created_by, created_dt, updated_by) "
                                . "VALUES (%d, %d, %s, %d, '%s', %d)", $user_id, $resource_id, $links, $user_id, $now, $user_id
                        )
                );

                $enroll_id = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT id FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $resource->course_id, $user_id
                        )
                );

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . resource_status() . " WHERE course_id = %d AND resource_id = %d AND user_id = %d", $resource->course_id, $resource_id, $user_id
                        )
                );

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . resource_status() . " (enrollment_id, course_id, resource_id, user_id, created_dt) "
                                . "VALUES (%d, %d, %d, %d, '%s')", $enroll_id, $resource->course_id, $resource_id, $user_id, $now
                        )
                );
            }

            $get_prj_details = $wpdb->get_results("select * from " . projects() . " order by id desc limit 1");
            //print_r($get_prj_details);
            $last_id = '';
            foreach ($get_prj_details as $detail) {
                $last_id = $detail->resource_id;
            }

            if ($_REQUEST['fstatus'] == 1) {
                echo $last_id;
                die;
            } else {

                /* getting resource details */
                $resource_id = $exe_id;
                $resource = $wpdb->get_row(
                        $wpdb->prepare
                                (
                                "SELECT r.course_id, r.title as resource_title, c.title as course_title
								FROM " . resources() . " r INNER JOIN " . courses() . " c ON r.course_id
								= c.id WHERE r.id = %d", $resource_id
                        )
                );

                //print_r($resource->course_title."   ".$resource->resource_title);die;

                $email_data = $wpdb->get_results("select * from wp_mentor_assign where user_id='" . $user_id . "'");
                $to_id = '';
                $from_id = $user_id;
                foreach ($email_data as $results) {
                    $to_id = $results->mentor_id;
                    if ($to_id > 0) {
                        emailtocoachnotify($to_id, $from_id, "submitted", $resource->course_title, $resource->resource_title, $links, "", "Project", $resourceurl);
                    }
                }


                if ($to_id == 0) {
                    $admin_email = get_option('admin_email');
                    $get_admin_id = $wpdb->get_var
                            (
                            $wpdb->prepare
                                    (
                                    "SELECT ID FROM $wpdb->users WHERE user_email = %s", $admin_email
                            )
                    );
                    $to_id = $get_admin_id;
                    emailtocoachnotify($to_id, $from_id, "submitted", $resource->course_title, $resource->resource_title, $links, "", "Project", $resourceurl);
                }

                json(1, 'Project Submitted', "");
            }
        } else if ($_REQUEST['do'] == "update") {


            /* getting resource details */
            $resource_id = $_REQUEST['resourceid'];
            $url = site_url() . "/" . PAGE_SLUG . "?exercise_detail=" . $resource_id;

            $resource = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT r.course_id, r.title as resource_title, c.title as course_title
								FROM " . resources() . " r INNER JOIN " . courses() . " c ON r.course_id
								= c.id WHERE r.id = %d", $resource_id
                    )
            );

            //print_r($resource->course_title."   ".$resource->resource_title);die;
            //print_r($_REQUEST['resourceid']);die;

            $links = isset($_REQUEST['links']) ? htmlspecialchars(trim($_REQUEST['links'])) : '';

            /* custom code to take project_id after insert */

            /* Sending Mail to Coach Code - custom */

            $user_id = get_current_user_id();


            /* ../Code ends */

            //print_r($_FILES['responsedoc']);

            $names = '';
            $file_links = '';
            $other_links = '';
            //$baefilepath = '/assets/files/'.$image['name'];
            //$dir = TR_COUNT_PLUGIN_DIR.$baefilepath; 
            $filepath = TR_COUNT_PLUGIN_URL . "/assets/files/";
            $directrypath = TR_COUNT_PLUGIN_DIR . "/assets/files/";
            foreach ($_FILES as $image) {
                move_uploaded_file($image['tmp_name'], $directrypath . $image['name']);
                $names.=$filepath . $image['name'] . " , ";
                $file_links.="<a href='" . $filepath . $image['name'] . "'>" . $image['name'] . "</a> , ";
                $other_links.="<a href='" . $filepath . $image['name'] . "' target='_blank'>" . $image['name'] . "</a><br/>";
            }



            $updated = $wpdb->update(projects(), array("doc_files" => trim($names, ",")), array("resource_id" => $resource_id));

            if ($updated) {

                $email_data = $wpdb->get_results("select * from wp_mentor_assign where user_id='" . $user_id . "'");
                $to_id = '';
                $from_id = $user_id;
                foreach ($email_data as $results) {
                    $to_id = $results->mentor_id;
                    if ($to_id > 0) {
                        emailtocoachnotify($to_id, $from_id, "submitted", $resource->course_title, $resource->resource_title, $links, trim($file_links, ","), "Project", $url);
                    }
                }


                if ($to_id == 0) {
                    $admin_email = get_option('admin_email');
                    $get_admin_id = $wpdb->get_var
                            (
                            $wpdb->prepare
                                    (
                                    "SELECT ID FROM $wpdb->users WHERE user_email = %s", $admin_email
                            )
                    );
                    $to_id = $get_admin_id;
                    emailtocoachnotify($to_id, $from_id, "submitted", $resource->course_title, $resource->resource_title, $links, trim($file_links, ","), "Project", $url);
                }



                //emailtocoachnotify($to_id,$from_id,"[ ".$links." ]","[ ".trim($files_array,",")." ]");	

                json(1, 'Project Submitted', $other_links);
                //json(1,'Project Submitted',get_userdata($to_id));
            } else {
                echo "Failed 1";
                die;
            }
        } else {
            echo "Failed 2";
            die;
        }
    } else if ($_REQUEST["param"] == "get_links") {

        $user_id = get_current_user_id();
        $userrole = new WP_User($user_id);
        $u_role = $userrole->roles[0];
        if ($u_role == 'administrator' || isagencylocation()) {
            $uid = isset($_POST['uidadmincase']) ? intval(($_POST['uidadmincase'])) : 0;
            if ($uid > 0) {
                $user_id = $uid;
            }
        }

        $typ = isset($_POST['typ']) ? htmlspecialchars(trim($_POST['typ'])) : 'exercise';
        if ($typ == 'resource') {
            $resource_id = isset($_POST['resource_id']) ? intval(trim($_POST['resource_id'])) : 0;
            $proj_links = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT links FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", $resource_id, $user_id
                    )
            );
        } else {
            $exe_id = isset($_POST['proj']) ? intval(trim($_POST['proj'])) : 0;
            $proj_links = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT links FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", $exe_id, $user_id
                    )
            );
        }

        if (empty($proj_links))
            json(0, 'Not Submitted');
        else
            json(1, 'Submitted', $proj_links);
    }

    else if ($_REQUEST["param"] == "remove_links") {
        $user_id = get_current_user_id();
        $userrole = new WP_User($user_id);
        $u_role = $userrole->roles[0];
        if ($u_role == 'administrator' || isagencylocation()) {
            $uid = isset($_POST['uidadmincase']) ? intval(($_POST['uidadmincase'])) : 0;
            if ($uid > 0) {
                $user_id = $uid;
            }
        }
        $exe_id = isset($_POST['proj']) ? intval(trim($_POST['proj'])) : 0;
        $typ = isset($_POST['datatyp']) ? htmlspecialchars(trim($_POST['datatyp'])) : 'exercise';
        if ($typ == 'resource') {

            $resource_id = $exe_id;
            $wpdb->query(
                    $wpdb->prepare
                            (
                            "DELETE FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", $resource_id, $user_id
                    )
            );

            /// also set completed


            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . resource_status() . " WHERE user_id = %d AND resource_id = %d", $user_id, $resource_id
                    )
            );


            /// also set completed
        } else {
            $wpdb->query(
                    $wpdb->prepare
                            (
                            "DELETE FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", $exe_id, $user_id
                    )
            );
        }
        json(1, 'Project Links Deleted');
    } else if ($_REQUEST["param"] == "add_video") {
        $typematerial = isset($_REQUEST['typematerial']) ? htmlspecialchars($_REQUEST['typematerial']) : "lesson";
        $code = isset($_POST['embedcode']) ? htmlspecialchars($_POST['embedcode']) : 0;
        $code = stripslashes($code);
        $user_id = $current_user->ID;

        if ($typematerial == "community_call") { /* custom code to make add of community calls */
            $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 0;
            $insert_id = isset($_REQUEST['insert_id']) ? intval($_REQUEST['insert_id']) : 0;
            /* #################################### */
            /* checking video is not */
            $video = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                    )
            );
            if (empty($video)) {
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . community_call() . " (type, source, path, created_by) "
                                . "VALUES ( %s, %s, %s, %d)", 'video', 'embed', $code, $user_id
                        )
                );
                json(1, 'Video Added');
            } else {
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . community_call() . " SET path = %s WHERE  id = '%d'", $code, $insert_id
                        )
                );
                json(1, 'Video Updated');
            }

            /* #################################### */
        } else {
            $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
            $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;


            if ($typematerial == "lesson") {
                $lesson = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as totaL FROM " . lessons() . " WHERE id = %d ", $lesson_id
                        )
                );

                if ($lesson == 0)
                    json(0, 'Invalid Lesson');
            }
            else {

                $resource = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as totaL FROM " . resources() . " WHERE id = %d ", $resource_id
                        )
                );

                if ($resource == 0)
                    json(0, 'Invalid Exercise');
            }
            $video = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . media() . " WHERE lesson_id = %d AND type= 'video' ", $lesson_id
                    )
            );
            // insert else update
            if (empty($video)) {

                if ($typematerial == "lesson") {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . media() . " (lesson_id, type, source, path, created_by) "
                                    . "VALUES (%d, %s, %s, %s, %d)", $lesson_id, 'video', 'embed', $code, $user_id
                            )
                    );
                } else {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . media() . " (resource_id, type, source, path, created_by) "
                                    . "VALUES (%d, %s, %s, %s, %d)", $resource_id, 'video', 'embed', $code, $user_id
                            )
                    );
                }
                json(1, 'Video Added');
            } else {

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . media() . " SET path = %s WHERE id = %d", $code, $video->id
                        )
                );
                json(1, 'Video Updated');
            }
        }
    } else if ($_REQUEST["param"] == "save_doc") {

        if (isset($_FILES) && count($_FILES) > 0) {
            $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
            $typematerial = isset($_REQUEST['typematerial']) ? htmlspecialchars($_REQUEST['typematerial']) : "lesson";
            $now = time();
            $x = 0;
            $ids = array();
            $pos = array();
            $file_links = '';
            if ($typematerial == "community_call") {

                $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 0;
                $insert_id = isset($_REQUEST['insert_id']) ? intval($_REQUEST['insert_id']) : 0;
                /* #################################### */

                $has_docs = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT id FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                        )
                );

                if ($has_docs > 0) {
                    $docs = $wpdb->get_results
                            (
                            $wpdb->prepare
                                    (
                                    "SELECT * FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                            )
                    );
                    $exist_docs = '';
                    foreach ($docs as $doc) {
                        $exist_docs .= $doc->doc_file_links . ",";
                    }

                    /**/
                    $file_links.= $exist_docs;

                    foreach ($_FILES as $image) {
                        move_uploaded_file($image['tmp_name'], TR_COUNT_PLUGIN_DIR . "/assets/files/" . $image['name']);
                        $file_links.=",{" . $image['name'] . "|" . TR_COUNT_PLUGIN_URL . "/assets/files/" . $image['name'] . "}";
                    }

                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE " . community_call() . " SET doc_file_links = %s WHERE id = '%d'", $file_links, $insert_id
                            )
                    );

                    json(1, 'Documents Uploaded', $arfinal);
                } else {
                    /**/


                    foreach ($_FILES as $image) {
                        move_uploaded_file($image['tmp_name'], TR_COUNT_PLUGIN_DIR . "/assets/files/" . $image['name']);
                        $file_links.="{" . $image['name'] . "|" . TR_COUNT_PLUGIN_URL . "/assets/files/" . $image['name'] . "}";
                    }

                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE " . community_call() . " SET doc_file_links = %s WHERE id = '%d'", $file_links, $insert_id
                            )
                    );

                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . community_call() . " (course_id, doc_file_links , created_by,created_dt) "
                                    . "VALUES (%d, %s, %s, %d)", $course_id, $file_links, $user_id, $now
                            )
                    );

                    json(1, 'Documents Uploaded', $arfinal);




                    /**/
                }

                console . log("Failed");

                /* #################################### */
            } else {

                if ($typematerial == 'lesson') {
                    $colname = 'lesson_id';
                    $tbl = lessons();
                } else {
                    $colname = 'resource_id';
                    $tbl = resources();
                }

                $user_id = $current_user->ID;

                $res = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as totaL FROM " . $tbl . " WHERE id = %d ", $id
                        )
                );

                if ($res == 0)
                    json(0, 'Invalid ' . $typematerial);


                for ($flag = 0; $flag < count($_FILES); $flag++) {

                    if ($_FILES["file-" . $flag]["name"] != "") {

                        if ($_FILES["file-" . $flag]["error"] > 0) {
                            continue;
                        } else {

                            $ext = trim(strtolower(pathinfo($_FILES["file-" . $flag]["name"], PATHINFO_EXTENSION)));

                            if ($ext == "php" || $ext == "sql" || $ext == "js") {
                                continue;
                            }

                            $othername = $_FILES["file-" . $flag]["name"];

                            $filenm = str_replace(" ", "_", $_FILES["file-" . $flag]["name"]);
                            // Path to upload file

                            if ($typematerial == 'lesson') {
                                $filename = $now . '_lesson' . $id . '_' . $filenm;
                            } else {
                                $filename = $now . '_resource' . $id . '_' . $filenm;
                            }


                            $baefilepath = '/assets/docs/' . $filename;

                            $dir = TR_COUNT_PLUGIN_DIR . $baefilepath;

                            $result = move_uploaded_file($_FILES["file-" . $flag]["tmp_name"], $dir);

                            $wpdb->query
                                    (
                                    $wpdb->prepare
                                            (
                                            "INSERT INTO " . media() . " ($colname, type, source, path, extra_info, created_by) "
                                            . "VALUES (%d, %s, %s, %s, %s, %d)", $id, 'document', 'upload', $baefilepath, $othername, $user_id
                                    )
                            );
                            $lastid = $wpdb->insert_id;
                            array_push($ids, $lastid);
                            array_push($pos, $flag);
                            $x++;
                        }
                    }
                }
                if ($x > 0) {
                    $arfinal = array('ids' => $ids, 'pos' => $pos);
                    json(1, 'Documents Uploaded', $arfinal);
                }
            }
            json(0, 'Failed To Upload');
        }
    } else if ($_REQUEST["param"] == "save_doc_titles") {

        $ids = isset($_REQUEST['ids']) ? htmlspecialchars($_REQUEST['ids']) : "";
        $ids = array_map('intval', explode(',', $ids));

        $pos = isset($_REQUEST['pos']) ? htmlspecialchars($_REQUEST['pos']) : "";
        $pos = array_map('intval', explode(',', $pos));

        $i = 0;
        if (count($ids) == 0) {
            json(0, 'Failed to upload..');
        }

        foreach ($ids as $id) {

            if (in_array($i, $pos)) {

                $title = $_POST['doctitles'][$i];
                if ($title != '') {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE " . media() . " SET extra_info = %s WHERE id = %d", $title, $id
                            )
                    );
                }
            }
            $i++;
        }
        if ($i > 0) {
            json(1, 'Document(s) uploaded');
        } else {
            json(0, 'Failed to upload');
        }
    } else if ($_REQUEST["param"] == "delete_doc") {

        $doc_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

        $doc = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT path FROM " . media() . " WHERE id = %d AND type = 'document'", $doc_id
                )
        );
        if (empty($doc)) {
            json(0, 'Invalid Document');
        }

        $path = TR_COUNT_PLUGIN_DIR . "/" . $doc->path;
        @unlink($path);
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . media() . " WHERE id = %d", $doc_id
                )
        );
        json(1, 'Document Deleted');
    } else if ($_REQUEST["param"] == "add_note") {

        $typematerial = isset($_REQUEST['typematerial']) ? htmlspecialchars($_REQUEST['typematerial']) : "lesson";
        $now = date('Y-m-d H:i:s');
        $user_id = $current_user->ID;

        if ($typematerial == "community_call") { /* custom code to make add of community calls notes */
            $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 0;
            $insert_id = isset($_REQUEST['insert_id']) ? intval($_REQUEST['insert_id']) : 0;
            $notetxt = isset($_POST['notetxt']) ? $_POST['notetxt'] : '';
            $notetxt = html_entity_decode($notetxt);
            /* #################################### */
            /* checking notes is not */
            $has_note = $wpdb->get_var(
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                    )
            );

            if ($has_note > 0) {
                $Not = $wpdb->get_results
                        (
                        $wpdb->prepare
                                (
                                "SELECT * FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                        )
                );
                $NotesSaved = '';
                foreach ($Not as $n) {
                    $NotesSaved .= $n->comm_notes . "|";
                }
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . community_call() . " SET comm_notes = %s WHERE id = '%d'", $NotesSaved . "{" . $notetxt . "}", $insert_id
                        )
                );
                json(1, 'Notes Updated');
            } else {
                $res = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . community_call() . " (comm_notes, created_by,created_dt) "
                                . "VALUES (%s,%d, %s)", "{" . $notetxt . "}", $user_id, $now
                        )
                );
                json(1, 'Notes Added');
            }

            console . log("Failed");

            /* #################################### */
        } else {

            $resource_id = isset($_POST['resourid']) ? intval($_POST['resourid']) : 0;
            $lesson_id = isset($_POST['lesonid']) ? intval($_POST['lesonid']) : 0;
            $note_id = isset($_POST['noteid']) ? intval($_POST['noteid']) : 0;

            if ($typematerial == 'lesson') {
                $lesson = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as total FROM " . lessons() . " WHERE id = %d ", $lesson_id
                        )
                );

                if ($lesson == 0)
                    json(0, 'Invalid Lesson');
            }
            else {
                $resource = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as total FROM " . resources() . " WHERE id = %d ", $resource_id
                        )
                );

                if ($resource == 0)
                    json(0, 'Invalid Exercise');
            }

            $has_note = $wpdb->get_var(
                    $wpdb->prepare
                            (
                            "SELECT count(*) as total FROM " . lesson_notes() . " WHERE id = %d ", $note_id
                    )
            );

            if ($has_note > 0) {

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . lesson_notes() . " SET note = %s WHERE id = %d", $notetxt, $note_id
                        )
                );
                json(1, 'Notes Updated');
            } else {

                if ($typematerial == 'lesson') {
                    $res = $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . lesson_notes() . " (lesson_id, note, created_by, created_dt, updated_by) "
                                    . "VALUES (%d, %s, %d, '%s', %d)", $lesson_id, $notetxt, $user_id, $now, $user_id
                            )
                    );
                } else {

                    $res = $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . lesson_notes() . " (resource_id, note, created_by, created_dt, updated_by) "
                                    . "VALUES (%d, %s, %d, '%s', %d)", $resource_id, $notetxt, $user_id, $now, $user_id
                            )
                    );
                }
                json(1, 'Notes Added');
            }
        }
    } else if ($_REQUEST["param"] == "delete_note") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $wpdb->query(
                $wpdb->prepare
                        (
                        "DELETE FROM " . lesson_notes() . " WHERE id = %d", $id
                )
        );

        json(1, 'Note Deleted');
    } else if ($_REQUEST["param"] == "delete_hlink") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . media() . " WHERE id = %d", $id
                )
        );

        json(1, 'Link Deleted');
    } else if ($_REQUEST["param"] == "delete_surveyform") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . survey_results() . " WHERE survey_id = %d", $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . survey_forms() . " WHERE id = %d", $id
                )
        );

        json(1, 'Survey Form Deleted');
    } else if ($_REQUEST["param"] == "delete_survey") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . survey_results() . " WHERE id = %d", $id
                )
        );

        json(1, 'Survey Deleted');
    } else if ($_REQUEST["param"] == "save_img") {

        if (isset($_FILES) && count($_FILES) > 0) {

            $typematerial = isset($_REQUEST['typematerial']) ? htmlspecialchars($_REQUEST['typematerial']) : "lesson";

            $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
            $urlimg = isset($_REQUEST['urlimg']) ? htmlspecialchars($_REQUEST['urlimg']) : "";

            if ($typematerial == "lesson") {
                $tbl = lessons();
                $txt = "Lesson";
                $col = "lesson_id";
            } else {
                $tbl = resources();
                $txt = "Resource";
                $col = "resource_id";
            }

            $user_id = $current_user->ID;

            $res = $wpdb->get_var(
                    $wpdb->prepare
                            (
                            "SELECT count(*) as totaL FROM " . $tbl . " WHERE id = %d ", $id
                    )
            );
            if ($res == 0)
                json(0, 'Invalid ' . $txt);

            $now = time();

            $x = 0;
            for ($flag = 0; $flag < count($_FILES); $flag++) {

                if ($_FILES["file-" . $flag]["name"] != "") {

                    if ($_FILES["file-" . $flag]["error"] > 0) {
                        json(0, $_FILES["file-" . $flag]["error"]);
                    } else {

                        $ext = trim(strtolower(pathinfo($_FILES["file-" . $flag]["name"], PATHINFO_EXTENSION)));

                        if ($ext == "php" || $ext == "sql" || $ext == "js") {
                            json(0, 'You are not allowed to add ' . $ext . ' files');
                        }

                        $filenm = str_replace(" ", "_", $_FILES["file-" . $flag]["name"]);
                        $filename = $now . '_' . $typematerial . '_image_' . $id . '_' . $filenm;

                        // Path to upload file

                        $baefilepath = '/assets/docs/' . $filename;

                        $dir = TR_COUNT_PLUGIN_DIR . $baefilepath;

                        $result = move_uploaded_file($_FILES["file-" . $flag]["tmp_name"], $dir);

                        $wpdb->query
                                (
                                $wpdb->prepare
                                        (
                                        "INSERT INTO " . media() . " ($col, type, source, path, extra_info, created_by) "
                                        . "VALUES (%d, %s, %s, %s, %s, %d)", $id, 'image', 'upload', $baefilepath, $urlimg, $user_id
                                )
                        );
                        $x++;
                    }
                }
            }
            if ($x > 0) {
                json(1, 'Video & Image Saved');
            }
        }
        json(0, 'Failed To Upload Image');
    } else if ($_REQUEST["param"] == "save_urlimg") {

        $imageid = isset($_REQUEST['imageid']) ? intval($_REQUEST['imageid']) : 0;
        $urlimg = isset($_REQUEST['urlimg']) ? htmlspecialchars($_REQUEST['urlimg']) : "";

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . media() . " SET extra_info = %s WHERE id = %d", $urlimg, $imageid
                )
        );

        json(1, 'Video & Image Url Saved');
    } else if ($_REQUEST["param"] == "add_hlink") {

        $typematerial = isset($_REQUEST['typematerial']) ? htmlspecialchars($_REQUEST['typematerial']) : "lesson";
        $resource_id = isset($_POST['resid']) ? intval($_POST['resid']) : 0;
        $lesson_id = isset($_POST['lessid']) ? intval($_POST['lessid']) : 0;
        $helpnk_id = isset($_POST['helpnkid']) ? intval($_POST['helpnkid']) : 0;

        if ($typematerial == "community_call") {
            $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 0;
            $insert_id = isset($_REQUEST['insert_id']) ? intval($_REQUEST['insert_id']) : 0;
            $linktitle = isset($_POST['linktitle']) ? htmlspecialchars($_POST['linktitle']) : '';
            $linkurl = isset($_POST['linkurl']) ? htmlspecialchars($_POST['linkurl']) : '';

            /* #################################### */
            /* checking notes is not */
            $has_links = $wpdb->get_var(
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                    )
            );

            if ($has_links > 0) {
                $helplinks = $wpdb->get_results
                        (
                        $wpdb->prepare
                                (
                                "SELECT * FROM " . community_call() . " WHERE  id = '%d'", $insert_id
                        )
                );
                $linksExists = '';
                foreach ($helplinks as $link) {
                    $linksExists .= $link->comm_hlp_links . ",";
                }


                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . community_call() . " SET comm_hlp_links = %s WHERE id = '%d'", $linksExists . "(" . $linktitle . "|" . $linkurl . ")", $insert_id
                        )
                );
                json(1, 'Help links Updated');
            } else {
                $res = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . community_call() . " (comm_hlp_links, created_by,created_dt) "
                                . "VALUES (%s,%s, %s)", "(" . $linktitle . "|" . $linkurl . ")", $user_id, $now
                        )
                );
                json(1, 'Help links Added');
            }

            console . log("Failed");

            /* #################################### */
        } else {

            $user_id = $current_user->ID;
            $now = date('Y-m-d H:i:s');

            if ($typematerial == 'lesson') {
                $lesson = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as total FROM " . lessons() . " WHERE id = %d ", $lesson_id
                        )
                );

                if ($lesson == 0)
                    json(0, 'Invalid Lesson');
            }
            else {
                $resource = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as total FROM " . resources() . " WHERE id = %d ", $resource_id
                        )
                );

                if ($resource == 0)
                    json(0, 'Invalid Exercise');
            }

            $has_lnk = $wpdb->get_var(
                    $wpdb->prepare
                            (
                            "SELECT count(*) as total FROM " . media() . " WHERE id = %d ", $helpnk_id
                    )
            );

            $linktitle = isset($_POST['linktitle']) ? htmlspecialchars($_POST['linktitle']) : '';
            $linkurl = isset($_POST['linkurl']) ? htmlspecialchars($_POST['linkurl']) : '';


            if ($has_lnk > 0) {

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . media() . " SET path = %s, extra_info = %s WHERE id = %d", $linkurl, $linktitle, $helpnk_id
                        )
                );
                json(1, 'Link Updated');
            } else {

                if ($typematerial == 'lesson') {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . media() . " (lesson_id, type, source, path, extra_info, created_by) "
                                    . "VALUES (%d, %s, %s, %s, %s, %d)", $lesson_id, 'link', 'upload', $linkurl, $linktitle, $user_id
                            )
                    );
                } else {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . media() . " (resource_id, type, source, path, extra_info, created_by) "
                                    . "VALUES (%d, %s, %s, %s, %s, %d)", $resource_id, 'link', 'upload', $linkurl, $linktitle, $user_id
                            )
                    );
                }
                json(1, 'Link Added');
            }
        }
    } else if ($_REQUEST["param"] == "get_rows") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $type = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : "resources";
        $tbl = resources();
        $col = 'lesson_id';
        if ($type == 'modules') {
            $tbl = modules();
            $col = 'course_id';
        } else if ($type == 'lessons') {
            $tbl = lessons();
            $col = 'module_id';
        }

        if ($type == 'courses') {
            $rows = $wpdb->get_results(
                    $wpdb->prepare
                            (
                            "SELECT id,title,ord FROM " . courses() . " ORDER BY ord ASC", ""
                    )
            );
        } else {
            $rows = $wpdb->get_results(
                    $wpdb->prepare
                            (
                            "SELECT id,title,ord FROM " . $tbl . " WHERE $col = %d ORDER BY ord ASC", $id
                    )
            );
        }
        if (!empty($rows))
            json(1, 'Rows Found', $rows);
        else
            json(0, 'No Row Found');
    }

    else if ($_REQUEST["param"] == "get_moverows") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        //$type = isset($_POST['type'])?htmlspecialchars($_POST['type']):"lessons";
        $type = "lessons";
        $tbl = resources();
        $col = 'lesson_id';
        if ($type == 'modules') {
            $tbl = modules();
            $col = 'course_id';
        } else if ($type == 'lessons') {
            $tbl = lessons();
            $col = 'module_id';
        }

        $rows = $wpdb->get_results(
                $wpdb->prepare
                        (
                        "SELECT id,title,ord,module_id FROM " . $tbl . " WHERE $col = %d ORDER BY ord ASC", $id
                )
        );

        if (!empty($rows)) {
            $module_id = $rows[0]->module_id;

            $rows_modules = $wpdb->get_results(
                    $wpdb->prepare
                            (
                            "SELECT id,title,ord FROM " . modules() . " WHERE course_id = (SELECT course_id FROM " . modules() . " WHERE id = %d)", $module_id
                    )
            );

            $ars = array("rows" => $rows, "rows_modules" => $rows_modules);
            json(1, 'Rows Found', $ars);
        } else
            json(0, 'No Row Found');
    }
    else if ($_REQUEST["param"] == "save_rows") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $type = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : "resources";
        $rows = isset($_POST['armult']) ? ($_POST['armult']) : "";
        $rows = explode(",", $rows);
        $tbl = resources();
        if ($type == 'modules') {
            $tbl = modules();
        } else if ($type == 'lessons') {
            $tbl = lessons();
        } else if ($type == 'courses') {
            $tbl = courses();
        }
        $i = 1;

        foreach ($rows as $row) {
            $wpdb->query(
                    $wpdb->prepare
                            (
                            "UPDATE " . $tbl . " SET ord = %d WHERE id = %d", $i, $row
                    )
            );
            $i++;
        }

        if ($i > 1)
            json(1, 'Order Updated');
        else
            json(0, 'Order Not Updated');
    }
    else if ($_REQUEST["param"] == "move_rows") {

        $id = isset($_POST['modid']) ? intval($_POST['modid']) : 0;
        $rows = isset($_POST['armult']) ? ($_POST['armult']) : "";
        $rows = explode(",", $rows);
        $i = 1;
        foreach ($rows as $row) {
            $wpdb->query(
                    $wpdb->prepare
                            (
                            "UPDATE " . lessons() . " SET module_id = %d WHERE id = %d", $id, $row
                    )
            );
            $i++;
        }

        if ($i > 1)
            json(1, 'Selected Lesson(s) Moved ');
        else
            json(1, 'Lesson(s) Not Moved ');
    }

    else if ($_REQUEST["param"] == "save_courseimg") {

        $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 0;
        $urlimg = isset($_REQUEST['urlimg']) ? htmlspecialchars($_REQUEST['urlimg']) : "";
        $now = time();
        $total = $wpdb->get_var(
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM " . courses() . " WHERE id = %d", $course_id
                )
        );
        if ($total == 0) {
            json(0, 'Invalid Course', $total);
        }

        if (isset($_FILES) && count($_FILES) > 0) {

            $user_id = $current_user->ID;

            $x = 0;
            for ($flag = 0; $flag < 1; $flag++) {

                if ($_FILES["file-" . $flag]["name"] != "") {

                    if ($_FILES["file-" . $flag]["error"] > 0) {
                        json(0, $_FILES["file-" . $flag]["error"]);
                    } else {

                        $ext = trim(strtolower(pathinfo($_FILES["file-" . $flag]["name"], PATHINFO_EXTENSION)));

                        if ($ext == "php" || $ext == "sql" || $ext == "js") {
                            json(0, 'You are not allowed to add ' . $ext . ' files');
                        }

                        $othername = $_FILES["file-" . $flag]["name"];

                        $filenm = str_replace(" ", "_", $_FILES["file-" . $flag]["name"]);
                        $filename = "course_" . $course_id . '.' . $ext;

                        $baefilepath = '/assets/docs/' . $filename;

                        $dir = TR_COUNT_PLUGIN_DIR . $baefilepath;
                        $fullpath = TR_COUNT_PLUGIN_URL . $baefilepath;

                        $result = move_uploaded_file($_FILES["file-" . $flag]["tmp_name"], $dir);

                        $wpdb->query
                                (
                                $wpdb->prepare
                                        (
                                        "UPDATE " . courses() . " SET imgpath = %s, link = %s WHERE id = %d", $baefilepath, $urlimg, $course_id
                                )
                        );

                        $tag = "<a target='_blank' href='$urlimg'><img src='$fullpath' /></a>";
                        $ar = array("tag" => $tag, "link" => $urlimg, "fullpath" => $fullpath);
                        json(1, 'Image Uploaded', $ar);
                    }
                }
            }
        } else {

            if ($urlimg != '') {
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . courses() . " SET link = %s WHERE id = %d", $urlimg, $course_id
                        )
                );
                $ar = array("link" => $urlimg);
                json(1, 'Link Saved', $ar);
            }
        }
        json(0, 'Failed To Upload');
    } else if ($_REQUEST["param"] == "markattendence") {

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $mentorcall = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . mentorcall() . " WHERE id = %d", $id
                )
        );

        if (empty($mentorcall)) {
            json(0, 'Invalid Record');
        }

        $val = isset($_POST['val']) ? htmlspecialchars($_POST['val']) : "yes";

        $wpdb->query(
                $wpdb->prepare
                        (
                        "UPDATE " . mentorcall() . " SET is_attended = %s WHERE id = %d", $val, $id
                )
        );

        json(1, 'Attendance marked as <strong>' . $val . '</strong>', $eq);
    } else if ($_REQUEST["param"] == "assignmentor") {

        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        $isdel = isset($_POST['isdel']) ? intval($_POST['isdel']) : 0;
        $mentor = isset($_POST['mentor']) ? intval($_POST['mentor']) : 0;
        if ($uid == 0) {
            json(0, 'Invalid User');
        }

        $id = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT id FROM " . mentor_assign() . " "
                        . "WHERE course_id = %d AND user_id = %d", $course_id, $uid
                )
        );
        if ($id > 0) {

            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . mentor_assign() . " SET mentor_id = %d WHERE id = %d", $mentor, $id
                    )
            );
            json(1, 'Mentor successfully changed', $mentor);
        } else {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . mentor_assign() . " (course_id, user_id, mentor_id) "
                            . "VALUES (%d, %d, %d)", $course_id, $uid, $mentor
                    )
            );
            $id = $wpdb->insert_id;
            json(1, 'Mentor successfully assigned', $mentor);
        }
        /* mentor assigned email if send here */
    } else if ($_REQUEST["param"] == "assignmentormultiple") {
        $ar = isset($_POST['ar']) ? htmlspecialchars($_POST['ar']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $mentor = isset($_POST['mentor']) ? intval($_POST['mentor']) : 0;
        if ($mentor == 0) {
            json(0, 'Invalid Mentor');
        }

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . mentor_assign() . " WHERE user_id IN($ar) AND course_id = %d", $course_id
                )
        );

        $arr = explode(",", $ar);
        foreach ($arr as $uid) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . mentor_assign() . " (course_id, user_id, mentor_id) "
                            . "VALUES (%d, %d, %d)", $course_id, $uid, $mentor
                    )
            );
        }
        json(1, 'Mentor successfully assigned to selected users');
    } else if ($_REQUEST["param"] == "get_mentor_users") {
        global $wpdb;
        $mentor = isset($_POST['mentor']) ? intval($_POST['mentor']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $usertbl = $wpdb->prefix . "users";
        $students = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT s.ID,s.display_name "
                        . "FROM " . mentor_assign() . " map INNER JOIN " . $usertbl . " s ON map.user_id = s.ID "
                        . "WHERE map.mentor_id = %d AND map.course_id = %d ORDER BY s.user_registered DESC", $mentor, $course_id
                )
        );

        json(1, 'users', $students);
    } else if ($_REQUEST["param"] == "saveform") {
        global $wpdb;
        $mentor_id = isset($_POST['mentor_id']) ? intval($_POST['mentor_id']) : 0;
        $formtitle = isset($_POST['formtitle']) ? htmlspecialchars($_POST['formtitle']) : "Dummy Form";
        $c_id = get_current_user_id();
        $date = date("Y-m-d H:i:s");
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . survey_forms() . " (mentor_id, title, data, created_by, created_dt) "
                        . "VALUES (%d, %s, %s, %d, '%s')", $mentor_id, $formtitle, '', $c_id, $date
                )
        );

        $form_id = $wpdb->insert_id;
        json(1, 'form saved', $form_id);
    } else if ($_REQUEST["param"] == "updateform") {
        global $wpdb;
        $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
        $form_data = isset($_POST['form_data']) ? stripslashes($_POST['form_data']) : '';
        $formtitle = isset($_POST['formtitle']) ? htmlspecialchars($_POST['formtitle']) : "Dummy Form";

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . survey_forms() . " SET title = %s, data = %s WHERE id = %d", $formtitle, $form_data, $form_id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . survey_results() . " SET data = %s WHERE survey_id = %d AND is_submitted = 0", $form_data, $form_id
                )
        );

        json(1, 'form updated', $form_data);
    } else if ($_REQUEST["param"] == "survey_send") {

        /* custom */
        global $wpdb;
        $form_id = isset($_POST['formid']) ? intval($_POST['formid']) : 0;
        $mentor_id = '';
        /* getting custom values */
        $utype = $_REQUEST['type'];
        $utypeid = $_REQUEST['typeid'];
        /* ../ends */

        if ($utype == "Mentor") {
            $mentor_id = $utypeid;
            $mentor = get_user_by('id', $mentor_id);
        }
        if ($utype == "Course") {
            $course_id = $mentor_id = $utypeid;
            $course = $wpdb->get_results("SELECT * FROM wp_courses where id = '" . $course_id . "'");
            foreach ($course as $detail) {
                $c_title = $detail->title;
                $mentor = $c_title;
            }
        }

        $form = $wpdb->get_row
                (
                /* $wpdb->prepare
                  (
                  "SELECT id,title,mentor_id,data FROM " . survey_forms()." "
                  . "WHERE id = %d",$form_id
                  ) */
                $wpdb->prepare
                        (
                        "SELECT id,title,data FROM " . survey_forms() . " "
                        . "WHERE id = %d", $form_id
                )
        );
        if (empty($form)) {
            json(0, 'Invalid Form ID');
        }

        //$mentor_id = $form->mentor_id;
        $data = $form->data;
        $c_id = get_current_user_id();
        $date = date("Y-m-d H:i:s");

        $users = isset($_POST['users']) ? htmlspecialchars($_POST['users']) : "";
        $users = explode(",", $users);
        $emails = "";

        //$mentor_id = $form->mentor_id;
        //$mentor = get_user_by('id',$mentor_id);
        $guid = md5(mt_rand(9999, 9999999) . time());

        foreach ($users as $user) {
            $userinfo = get_user_by("id", $user);
            if (!empty($userinfo)) {

                /* $wpdb->query
                  (
                  $wpdb->prepare
                  (
                  "INSERT INTO " . survey_results()  . " (guid, survey_id, mentor_id, user_id, data, created_by, created_dt) "
                  . "VALUES (%s, %d, %d, %d, %s, %d, '%s')",
                  $guid, $form_id, $mentor_id, $user, $data, $c_id, $date
                  )
                  );
                  $insert_id = $wpdb->insert_id; */
                $insert_id = 1;
                if ($utype == "Mentor") { /* custom */
                    emailforsurvey($guid, $insert_id, $userinfo->data->user_email, $userinfo->data->display_name, $form, $mentor);
                } else if ($utype == "Course") {
                    emailforsurveyCourse($guid, $insert_id, $userinfo->data->user_email, $userinfo->data->display_name, $form, $mentor);
                }
            }
        }

        json(1, 'Survey successfully sent', $emails);
    } else if ($_REQUEST["param"] == "saveformresult") {

        $survey_id = isset($_POST['survey_id']) ? intval($_POST['survey_id']) : 0;
        $c_id = get_current_user_id();
        $survey = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . survey_results() . " WHERE id = %d", $survey_id
                )
        );

        if ($survey->is_submitted == 1) {
            json(0, 'You have already submitted this survey.');
        }

        if (empty($survey)) {
            json(0, 'Invalid Survey');
        }
        if ($survey->user_id != $c_id) {
            json(0, 'Invalid Survey');
        }

        $data = json_decode($survey->data);
        $data = $data->fields;
        $values = isset($_POST['values']) ? stripslashes(($_POST['values'])) : "";
        $values = json_decode($values);
        $i = 1;
        foreach ($data as $d) {
            $d->id = $i;
            $d->form_id = $form_id;
            $i++;
        }
        $i = 1;
        foreach ($data as $newd) {

            if ($newd->field_type == "text" || $newd->field_type == "paragraph" || $newd->field_type == "number" ||
                    $newd->field_type == "website" || $newd->field_type == "email") {
                if (isset($values->$i) && $values->$i != "undefined") {
                    $val = trim($values->$i);
                    $newd->value = esc_attr($val);
                }
            } else if ($newd->field_type == "radio" || $newd->field_type == "dropdown") {

                $options = $newd->field_options->options;
                foreach ($options as $option) {
                    if (isset($values->$i) && $option->label == $values->$i) {
                        $option->checked = true;
                    } else {
                        $option->checked = false;
                    }
                }
            } else if ($newd->field_type == "checkboxes") {

                $options = $newd->field_options->options;
                $j = 0;
                foreach ($options as $option) {
                    if (isset($values->$i->$j) && $values->$i->$j == 'on') {
                        $option->checked = true;
                    } else {
                        $option->checked = false;
                    }

                    $j++;
                }
            } else if ($newd->field_type == "address") {

                $newd->value->city = isset($values->$i->city) ? $values->$i->city : '';
                $newd->value->country = isset($values->$i->country) ? $values->$i->country : '';
                $newd->value->state = isset($values->$i->state) ? $values->$i->state : '';
                $newd->value->street = isset($values->$i->street) ? $values->$i->street : '';
                $newd->value->zipcode = isset($values->$i->zipcode) ? $values->$i->zipcode : '';
            } else if ($newd->field_type == "price") {
                $newd->value->cents = isset($values->$i->cents) ? $values->$i->cents : '';
                $newd->value->dollars = isset($values->$i->dollars) ? $values->$i->dollars : '';
            } else if ($newd->field_type == "time") {
                $newd->value->am_pm = isset($values->$i->am_pm) ? $values->$i->am_pm : '';
                $newd->value->hours = isset($values->$i->hours) ? $values->$i->hours : '';
                $newd->value->minutes = isset($values->$i->hours) ? $values->$i->minutes : '';
                $newd->value->seconds = isset($values->$i->hours) ? $values->$i->seconds : '';
            } else if ($newd->field_type == "date") {
                $newd->value->day = isset($values->$i->day) ? $values->$i->day : '';
                $newd->value->month = isset($values->$i->month) ? $values->$i->month : '';
                $newd->value->year = isset($values->$i->year) ? $values->$i->year : '';
            }

            $i++;
        }
        $tosave = new stdClass();
        $tosave->fields = $data;

        $tosave = json_encode($tosave);
        $tosave = trim(str_replace('\n', '<br/>', $tosave));

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . survey_results() . " SET data = %s, is_submitted = 1 WHERE id = %d", $tosave, $survey_id
                )
        );
        emailforsurveyresult($survey);
        //print_r($values); die;
        json(1, 'Thanks!!.. Survey Has Been Submitted Successfully.. Please wait for redirection...');
    } else if ($_REQUEST["param"] == "update__template") {

        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;

        $template = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . email_templates() . " WHERE id = %d ", $template_id
                )
        );

        if (empty($template)) {
            json(0, 'Invalid Template ID');
        }

        $sub = isset($_POST['sub']) ? esc_attr($_POST['sub']) : "";
        $content = isset($_POST['content']) ? html_entity_decode(urldecode($_POST['content'])) : "";
        $content = stripcslashes($content);
        if (trim($sub) == '' || trim($content) == '') {
            json(0, 'Both Subject and Email content are required');
        }

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . email_templates() . " SET subject = %s, content = %s WHERE id = %d", $sub, $content, $template_id
                )
        );

        json(1, 'Email template updated successfully.');
    }
}

function emailforsurvey($guid, $id, $email, $name, $form, $mentor) {

    global $wpdb;

    $slug = PAGE_SLUG;
    $btn_url = site_url() . "/$slug?survey=" . $id . "&guid=" . $guid;

    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $template = tt_get_template("survey_send");
    $subj = $template->subject;
    $subj = str_replace("{{mentor_name}}", $mentor->data->display_name, $subj);

    $msg = $template->content;
    $msg = str_replace(array('{{username}}', '{{mentor_name}}', '{{url}}', '{{site_name}}'), array($name, $mentor->data->display_name, $btn_url, $site_name), $msg);

    custom_mail($email, $subj, $msg, EMAIL_TYPE, "");
}

/*
  Email Survey for Course - custom
 */

function emailforsurveyCourse($guid, $id, $email, $name, $form, $mentor) {

    global $wpdb;

    $slug = PAGE_SLUG;
    $btn_url = site_url() . '/' . $slug . '?survey=' . $id . '&guid=' . $guid;
    $btn_url = "http://www.google.com";

    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=utf-8' . '\r\n' .
            'X-Mailer: PHP/' . phpversion();

    $template = tt_get_template("survey_send_course");
    $subj = $template->subject;
    $subj = str_replace("{{course_name}}", $mentor, $subj);
    $msg = $template->content;
    $msg = str_replace(array('{{username}}', '{{course_name}}', '{{url}}', '{{site_name}}'), array($name, $mentor, $btn_url, $site_name), $msg);
    custom_mail($email, $subj, $msg, EMAIL_TYPE, "");
}

/* ../ custom code ends */

/* custom code to send email notification to coach */

function emailtocoachnotify($to_id, $from_id, $status, $course, $exercise, $links, $files, $type, $url) {

    global $wpdb;
    /* coach */
    $coach_details = get_userdata($to_id);
    $coach_name = $coach_details->display_name;
    $coach_email = /* $coach_details->user_email; */"sanjay@rudrainnovatives.com";
    /* student */
    $student_details = get_userdata($from_id);
    $student_name = strtoupper($student_details->display_name); /* student_name */
    $student_email = $student_details->user_email;  /* student email */

    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;
    $headers = 'From: ' . $student_email . "\r\n" .
            'Reply-To: ' . $student_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $template = tt_get_template("project_submission");
    $subj = $template->subject;
    $subj = str_replace("{{mentor_name}}", $coach_name, $subj);

    $msg = $template->content;

    /* sending mail in case of project submission */
    
    if($coach_email==get_option('admin_email') && $type=="Project"){

    //if ($coach_email == "sanjay@rudrainnovatives.com" && $type == "Project") {


        if (empty($files)) {
            $msg = str_replace(array('{{mentor_name}}', '{{student_name}}', '{{student_email}}', '{{status}}', '{{exercise_name}}', '{{course_name}}', '{{work_files}}', '{{site_name}}'), array($coach_name, $student_name, $student_email, $status . " work , below you can find work details" . "<br/>", $exercise, $course, "<br/>Submitted Work <br/> Links are: " . $links . "<br/><br/>For more information , please go to this link<br/>" . $url . "<br/><br/>*Note : You have recieved this mail because user has no mentor assigned yet.", $site_name), $msg);
        } else {
            $msg = str_replace(array('{{mentor_name}}', '{{student_name}}', '{{student_email}}', '{{status}}', '{{exercise_name}}', '{{course_name}}', '{{work_files}}', '{{site_name}}'), array($coach_name, $student_name, $student_email, $status . " work , below you can find work details" . "<br/>", $exercise, $course, "<br/>Submitted Work <br/>Links are : " . $links . "<br/>Files are :" . $files . "<br/><br/>For more information , please go to this link<br/>" . $url . "<br/><br/>*Note : You have recieved this mail because user has no mentor assigned yet.", $site_name), $msg);
        }
    } else if ($type == "Project") {
        if (empty($files)) {
            $msg = str_replace(array('{{mentor_name}}', '{{student_name}}', '{{student_email}}', '{{status}}', '{{exercise_name}}', '{{course_name}}', '{{work_files}}', '{{site_name}}'), array($coach_name, $student_name, $student_email, $status . " work , below you can find work details.<br/>", $exercise . "<br/>", $course, "<br/>Submitted Work <br/> Links are :" . $links . "<br/><br/>For more information , please go to this link<br/>" . $url, $site_name), $msg);
        } else {
            $msg = str_replace(array('{{mentor_name}}', '{{student_name}}', '{{student_email}}', '{{status}}', '{{exercise_name}}', '{{course_name}}', '{{work_files}}', '{{site_name}}'), array($coach_name, $student_name, $student_email, $status . " work , below you can find work details .<br/>", $exercise, $course, "<br/>Submitted Work  <br/>Links are :" . $links . "<br/>Files are : " . $files . "<br/><br/>For more information , please go to this link<br/>" . $url, $site_name), $msg);
        }
    } else if ($coach_email == get_option('admin_email') && $type == "Marked") {

        $msg = str_replace(array('{{mentor_name}}', '{{student_name}}', '{{student_email}}', '{{status}}', '{{exercise_name}}', '{{course_name}}', '{{work_files}}', '{{site_name}}'), array($coach_name, $student_name, $student_email, $status . " work , below you can find work details .<br/>", $exercise, $course, "<br/><br/>For more information , please go to this link<br/>" . $url, $site_name), $msg);
    } else if ($type == "Marked") {

        $msg = str_replace(array('{{mentor_name}}', '{{student_name}}', '{{student_email}}', '{{status}}', '{{exercise_name}}', '{{course_name}}', '{{work_files}}', '{{site_name}}'), array($coach_name, $student_name, $student_email, $status . " work , below you can find work details .<br/>", $exercise, $course, "<br/><br/>For more information , please go to this link<br/>" . $url, $site_name), $msg);
    }


    custom_mail($coach_email, $subj, $msg, EMAIL_TYPE, "");
}

/* ../coach email ends */

function emailforsurveyresult($survey) {

    global $wpdb;
    $id = $survey->id;
    $user_id = $survey->user_id;
    $user = get_user_by('id', $user_id);

    $mentor_id = $survey->mentor_id;
    $mentor = get_user_by('id', $mentor_id);


    $form = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . survey_forms() . " "
                    . "WHERE id = %d", $survey->survey_id
            )
    );

    $slug = PAGE_SLUG;
    $url = site_url() . "/$slug?survey=" . $id . "&guid=" . $survey->guid;
    $btnaccept = "<a href='" . $url . "'>Click Here To Check Your Survey</a> <br/><br/>";

    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    /* for user */

    $template = tt_get_template("survey_result_user");
    $subj = $template->subject;

    $msg = $template->content;
    $msg = str_replace(array('{{username}}', '{{survey_title}}', '{{mentor_name}}', '{{url}}', '{{site_name}}'), array($user->data->display_name, $form->title, $mentor->data->display_nam, $url, $site_name), $msg);

    custom_mail($user->data->user_email, $subj, $msg, EMAIL_TYPE, "");


    /* for mentor */

    $url = site_url() . "/wp-admin/admin.php?page=survey_result&survey_id=" . $id;

    $template = tt_get_template("survey_result_mentor");
    $subj = $template->subject;

    $msg = $template->content;
    $msg = str_replace(array('{{username}}', '{{survey_user}}', '{{survey_title}}', '{{url}}', '{{site_name}}'), array($mentor->data->display_name, $user->data->display_name, $form->title, $url, $site_name), $msg);

    custom_mail($mentor->data->user_email, $subj, $msg, EMAIL_TYPE, "");
}

function updatlesson($lesson_id) {

    global $wpdb;
    $total_lesshrs = $wpdb->get_var
            (
            $wpdb->prepare
                    (
                    "SELECT sum(total_hrs) as totalhrs FROM " . resources() . " WHERE lesson_id = %d", $lesson_id
            )
    );

    $total_lessresource = $wpdb->get_var
            (
            $wpdb->prepare
                    (
                    "SELECT count(id) as totalresource FROM " . resources() . " WHERE lesson_id = %d", $lesson_id
            )
    );
    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "UPDATE " . lessons() . " SET total_hrs = %s, total_resources = %d WHERE id = %d", $total_lesshrs, $total_lessresource, $lesson_id
            )
    );
}

function updatehours_and_resources($module_id, $course_id, $lesson_id) {
    global $wpdb;

    if ($lesson_id > 0) {
        updatlesson($lesson_id);
    } else if ($lesson_id == 0) {

        if ($module_id > 0) {
            $lessons = $wpdb->get_results
                    (
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . lessons() . " WHERE module_id = %d", $module_id
                    )
            );

            foreach ($lessons as $lesson) {
                updatlesson($lesson->id);
            }
        }
    }

    if ($module_id > 0) {

        $total_modulehrs = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT sum(total_hrs) as totalhrs FROM " . lessons() . " WHERE module_id = %d", $module_id
                )
        );

        $hrm = updatehrsfrominner('module_id', $module_id);
        $total_modulehrs = $total_modulehrs + $hrm;

        $total_moduleresource = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT sum(total_resources) as totalresource FROM " . lessons() . " WHERE module_id = %d", $module_id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . modules() . " SET total_hrs = %s, total_resources = %d WHERE id = %d", $total_modulehrs, $total_moduleresource, $module_id
                )
        );
    }

    if ($course_id > 0) {


        $total_coursehrs = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT sum(total_hrs) as totalhrs FROM " . modules() . " WHERE course_id = %d", $course_id
                )
        );

        $hr = updatehrsfrominner('course_id', $course_id);
        $total_coursehrs = $total_coursehrs + $hr;

        $total_courseresource = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT sum(total_resources) as totalresource FROM " . modules() . " WHERE course_id = %d", $course_id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . courses() . " SET total_hrs = %s, total_resources = %d WHERE id = %d", $total_coursehrs, $total_courseresource, $course_id
                )
        );
    }
}

function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

function notifyenrolleduser($callid, $guid, $current_user, $course_id, $student_user, $meetinglink, $date, $updt, $is_accepted) {

    global $wpdb;
    $course = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . courses() . " WHERE id = %s", $course_id
            )
    );

    $usertbl = $wpdb->prefix . "users";
    $enrolledby = get_user_by('id', $student_user);

    $txt = "schedule";
    if ($updt == 1) {
        $txt = "re-schedule";
    }
    $slug = PAGE_SLUG;
    $url = site_url() . "/$slug?accept_call=$callid&guid=$guid";
    $btnaccept = "<a href='" . $url . "'>Click Here To Accept Invitation And Notify Your Mentor</a> <br/><br/>";
    if ($is_accepted == 1) {
        $btnaccept = '';
    }

    $emails = $enrolledby->data->user_email;

    $date = date("D d M Y, h:i a", strtotime($date));
    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $template = tt_get_template("mentor_call");
    $subj = $template->subject;
    $subj = str_replace("{{course_title}}", $course->title, $subj);

    $msg = $template->content;
    $msg = str_replace(array('{{username}}', '{{mentor_name}}', '{{course_title}}', '{{call_date}}', '{{url}}', '{{meeting_link}}', '{{scehulde_or_reschedule}}', '{{site_name}}'), array($uuser->display_name, $current_user->data->display_name, $course->title, $date, $url, $meetinglink, $txt, $site_name), $msg);

    custom_mail($emails, $subj, $msg, EMAIL_TYPE, "");
}

function notifyuserforcancell($current_user, $id) {
    global $wpdb;
    $usertabl = $wpdb->prefix . "users";
    $mentorcal = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT m.*, u.display_name,u.user_email,c.title FROM " . mentorcall() . " m LEFT JOIN " . $usertabl . " u ON m.user_id = u.ID "
                    . "LEFT JOIN " . courses() . " c ON  m.course_id = c.id "
                    . "WHERE m.id = %d ORDER BY m.created_dt DESC", $id
            )
    );

    $date = $mentorcal->mentor_call;

    $emails = $mentorcal->user_email;

    $date = date("D d M Y, h:i a", strtotime($date));
    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


    $template = tt_get_template("mentor_call_cancel");
    $subj = $template->subject;
    $subj = str_replace("{{course_title}}", $course->title, $subj);

    $msg = $template->content;
    $msg = str_replace(array('{{username}}', '{{mentor_name}}', '{{course_title}}', '{{call_date}}', '{{site_name}}'), array($uuser->display_name, $current_user->data->display_name, $course->title, $date, $site_name), $msg);

    custom_mail($emails, $subj, $msg, EMAIL_TYPE, "");
}

function updatehrsfrominner($column, $value) {

    global $wpdb;

    if ($column == 'course_id') {
        $projhrsm = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT total_hrs,status FROM " . project_exercise() . " WHERE course_id = %d AND module_id = 0", $value
                )
        );
    } else {
        $projhrsm = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT total_hrs,status FROM " . project_exercise() . " WHERE module_id = %d", $value
                )
        );
    }

    $ret = 0;
    if (!empty($projhrsm)) {
        if ($projhrsm->status == 1) {
            $ret = $projhrsm->total_hrs;
        } else {
            $ret = -($projhrsm->total_hrs);
        }
    }

    return $ret;
}

function update_hours($module_id, $course_id, $removehrs, $hrsadd) {
    global $wpdb;
    if ($module_id > 0) {


        $total_modulehrs = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT total_hrs FROM " . modules() . " WHERE id = %d", $module_id
                )
        );
        $total_modulehrs = ($total_modulehrs + $hrsadd) - $removehrs;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . modules() . " SET total_hrs = %s WHERE id = %d", $total_modulehrs, $module_id
                )
        );



        $total_coursehrs = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT total_hrs FROM " . courses() . " WHERE id = %d", $course_id
                )
        );

        $total_coursehrs = ($total_coursehrs + $hrsadd) - $removehrs;
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . courses() . " SET total_hrs = %s WHERE id = %d", $total_coursehrs, $course_id
                )
        );
    } else {

        $total_coursehrs = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT total_hrs FROM " . courses() . " WHERE id = %d", $course_id
                )
        );


        $total_coursehrs = ($total_coursehrs + $hrsadd) - $removehrs;

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . courses() . " SET total_hrs = %s WHERE id = %d", $total_coursehrs, $course_id
                )
        );
    }
}

function deletemedia($lesson_id) {

    global $wpdb;
    $lessonmedia = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT path FROM " . media() . " WHERE type IN('document','image') AND (lesson_id = %d OR resource_id IN(SELECT id FROM " . resources() . " WHERE lesson_id = %d))", $lesson_id, $lesson_id
            )
    );

    foreach ($lessonmedia as $media) {
        $path = $media->path;
        $path = TR_COUNT_PLUGIN_DIR . $path;
        @unlink($path);
    }

    $wpdb->query(
            $wpdb->prepare
                    (
                    "DELETE FROM " . media() . " WHERE lesson_id = %d OR resource_id IN(SELECT id FROM " . resources() . " WHERE lesson_id = %d)", $lesson_id, $lesson_id
            )
    );
}

function deletemediacourse($id) {
    global $wpdb;
    $lessons = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT id FROM " . lessons() . " WHERE module_id IN(SELECT id FROM " . modules() . " WHERE course_id = %d) ", $id
            )
    );
    foreach ($lessons as $lesson) {
        deletemedia($lesson->id);
    }
}

function deletemediamodule($id) {
    global $wpdb;
    $lessons = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT id FROM " . lessons() . " WHERE module_id = %d", $id
            )
    );
    foreach ($lessons as $lesson) {
        deletemedia($lesson->id);
    }
}

function permission_email_course($course_id, $user_id) {

    global $wpdb;
    $usertabl = $wpdb->prefix . "users";
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . courses() . " WHERE id = %d", $course_id
            )
    );

    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    /* Email for permissions granted */
    if ($user_id > 0) {

        $uuser = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT display_name,user_email FROM " . $usertabl . " WHERE id = %d", $user_id
                )
        );

        if (!empty($uuser)) {
            $email = $uuser->user_email;
            $url = site_url() . "/" . PAGE_SLUG . "?course=" . $course_id;
            $link = "<a href='" . $url . "'>Click here to view your course</a>";

            $template = tt_get_template("course_permission_granted");
            $subj = $template->subject;
            $subj = str_replace("{{course_title}}", $course->title, $subj);

            $msg = $template->content;
            $msg = str_replace(array('{{username}}', '{{course_title}}', '{{url}}', '{{site_name}}'), array($uuser->display_name, $course->title, $url, $site_name), $msg);

            custom_mail($email, $subj, $msg, EMAIL_TYPE, "");
        }
    }
}

function permission_revoke_course($course_id, $user_id) {
    /*
      global $wpdb;
      $usertabl = $wpdb->prefix."users";
      $date = date("D d M Y, h:i a");
      $course = $wpdb->get_row
      (
      $wpdb->prepare
      (
      "SELECT * FROM ". courses() . " WHERE id = %d",
      $course_id
      )
      );

      $site_name = TR_SITE_NAME;
      $admin_email = get_option( 'admin_email' );
      $headers = 'From: ' . $admin_email . "\r\n" .
      'Reply-To: ' . $admin_email . "\r\n" .
      'MIME-Version: 1.0' . "\r\n" .
      'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();


      if($user_id > 0){

      $uuser = $wpdb->get_row
      (
      $wpdb->prepare
      (
      "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
      )
      );
      if(!empty($uuser)){
      $email = $uuser->user_email;

      $template = tt_get_template("course_permission_revoked");
      $subj = $template->subject;
      $subj = str_replace("{{course_title}}", $course->title, $subj);

      $msg = $template->content;
      $msg = str_replace(array('{{username}}','{{course_title}}','{{site_name}}'),
      array($uuser->display_name,$course->title,$site_name), $msg);

      custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
      }
      }
     */
}

function tt_get_template($template_name) {
    global $wpdb;
    $template = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT subject, content FROM " . email_templates() . " WHERE template = %s", $template_name
            )
    );
    return $template;
}

function mentor_add_course_email($course_id, $user_id) {

    global $wpdb;
    $usertabl = $wpdb->prefix . "users";
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . courses() . " WHERE id = %d", $course_id
            )
    );

    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    /* Email for permissions granted */
    if ($user_id > 0) {

        $uuser = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT display_name,user_email FROM " . $usertabl . " WHERE id = %d", $user_id
                )
        );
        if (!empty($uuser)) {
            $email = $uuser->user_email;

            $template = tt_get_template("mentor_added");
            $subj = $template->subject;
            $subj = str_replace("{{course_title}}", $course->title, $subj);

            $msg = $template->content;
            $msg = str_replace(array('{{username}}', '{{course_title}}', '{{site_name}}'), array($uuser->display_name, $course->title, $site_name), $msg);

            custom_mail($email, $subj, $msg, EMAIL_TYPE, "");
        }
    }
}

function remove_from_course($course_id, $user_id) {

    global $wpdb;
    $usertabl = $wpdb->prefix . "users";
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . courses() . " WHERE id = %d", $course_id
            )
    );

    $site_name = TR_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    /* Email for permissions granted */
    if ($user_id > 0) {

        $uuser = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT display_name,user_email FROM " . $usertabl . " WHERE id = %d", $user_id
                )
        );
        if (!empty($uuser)) {
            $email = $uuser->user_email;

            $template = tt_get_template("mentor_removed");
            $subj = $template->subject;
            $subj = str_replace("{{course_title}}", $course->title, $subj);

            $msg = $template->content;
            $msg = str_replace(array('{{username}}', '{{course_title}}', '{{site_name}}'), array($uuser->display_name, $course->title, $site_name), $msg);

            custom_mail($email, $subj, $msg, EMAIL_TYPE, "");
        }
    }
}

function custom_mail_header($fromcntmail = 'enfusen.com') {
    $additional_parameters = '-f notifications@enfusen.com';
    return "Reply-To: $fromcntmail\r\n"
            . "Return-Path: MCC <notifications@" . $fromcntmail . ">\r\n"
            . "From: Enfusen Notifications <notifications@" . $fromcntmail . ">\r\n"
            . "Return-Receipt-To: notifications@" . $fromcntmail . "\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-type: text/html; charset=utf-8 " . "\r\n"
            . "X-Priority: 3\r\n"
            . "X-Mailer: PHP" . phpversion() . "\r\n";
}

function custom_mail($user_email, $setup_sub, $body, $email_type, $reason) {
    if (tr_unsubscribed($user_email) == FALSE) {
        $email_template_body = email_template_body($body, $user_email, $email_type);

        @mail($user_email, $setup_sub, $email_template_body, custom_mail_header(), mail_additional_parameters());
        insert_email_historical_report(user_id(), $email_type, $setup_sub, $user_email, $reason, current_id());
    }
}

function getacids(){

    $AccountIds = array();
	
	
    $accountsItems = $GLOBALS["Analytics"]->management_accounts->listManagementAccounts()->getItems();

    if (count($accountsItems) > 0) 

    foreach($accountsItems as $curAccountItem)

        $AccountIds[] = array($curAccountItem->getId(), $curAccountItem->name);

    return $AccountIds;

}
?>
