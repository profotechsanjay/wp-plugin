<?php
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();

if (isset($_REQUEST["param"])) {
    if ($_REQUEST["param"] == "run_cre") {
        $user_id = $_REQUEST['mccuserid'];
        $type = isset($_POST['type']) ? $_POST['type'] : 'allpage';
        $currid = current_id();
        $recommend = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
                )
        );
        $history_entry = 0;
        if (empty($recommend)) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO wp_content_recommend (type, user_id, trigger_report, auto_trigger, user_trigger, rundate, created_dt) "
                            . "VALUES(%s, %d, %d, %d, %d, '%s', '%s') ", $type, $user_id, 1, 1, $currid, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
                    )
            );
            $url = $_POST['data_website'];
            $parseurl = parse_url($url);
            $baseurl = $parseurl['host'];
            $serviceurl = 'http://icons.better-idea.org/allicons.json';
            $returndata = @file_get_contents($serviceurl . '?' . 'url=' . $baseurl);
            $returndata = json_decode($returndata);
            $faviconurl = 0;
            if (isset($returndata->icons[0]->url) && $returndata->icons[0]->url != '') {
                $faviconurl = $returndata->icons[0]->url;
            }
            $hasfavicon = get_user_meta($user_id, 'webfavicon', TRUE);
            if ($hasfavicon == '') {
                add_user_meta($user_id, 'webfavicon', $faviconurl);
            } else {
                update_user_meta($user_id, 'webfavicon', $faviconurl);
            }
            $params = array('param' => 'content_recommend', 'user_id' => $user_id, "typerequest" => $type, 'history_entry' => $history_entry);
		$_SESSION['integration']=1;            
		silent_post($params);
             
            json(1, "", '');
        }
        json(0, "", '');
    } else if ($_REQUEST["param"] == "search_campaign") {
        json(1, $_REQUEST);
    } else if ($_REQUEST["param"] == "del_usermeta") {
        $loc_id = isset($_REQUEST['location_id']) ? $_REQUEST['location_id'] : "";
        if (empty($loc_id)) {
            json(0, "Invalid Campaign ID Found");
        } else {
            lg_deleteusermeta($loc_id);
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM wp_client_location WHERE MCCUserId = %d", $loc_id
                    )
            );
            json(1, "Campaign Deleted Successfully");
        }
    } else if ($_REQUEST["param"] == "connect_ga") {
         
         ob_start();
        $file = LG_COUNT_PLUGIN_DIR . '/views/modalfooter/gaconnent.php';
        include $file;
        $template = ob_get_contents(); // get contents of buffer
        ob_end_clean();
          json(1,'', $template);


    }
else if ($_REQUEST["param"] == "disconnect_ga") {
         $CurClientID=$_REQUEST['id'];
         $action=$_REQUEST['DisconnectAnalytics'];
         $user_id=$_SESSION['general']['mcc_userid'];
         include_once get_template_directory() . '/analytics/AdWordsUtils.php';
          include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
          include_once get_template_directory() . '/analytics/AnalyticsUtils.php';
if(function_exists('UpdateRowsInTable')){
             UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsToken', 'AnalyticsChildActId', 'AnalyticsAccountName'), array('', '', ''), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));
 ob_start();
        $file = LG_COUNT_PLUGIN_DIR . '/views/modalfooter/gaconnent.php';
        include $file;
        $template = ob_get_contents(); // get contents of buffer
        ob_end_clean();
          json(1,'', $template);
}
else{
json(0,'function not found', '');
}
      json(1,'', '');

    }
else if ($_REQUEST["param"] == "select_ga") {
     $data=$_REQUEST['data'];
     $loc_id=$_REQUEST['locationid'];
      $new='SelectedAnalyticsChildAccountComboBox'.$loc_id.'=';
      $str= str_replace($new,'',$_REQUEST['data']);
  // $_REQUEST['SelectedAnalyticsChildAccountComboBoxID'.$loc_id]=$str;
    $str2=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
     $str2=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
    $str3=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
      include_once get_template_directory() . '/analytics/AdWordsUtils.php';
      include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
      include_once get_template_directory() . '/analytics/AnalyticsUtils.php';
      $data= implode(SpecSeparatorStr, array($str, -1, -1));
       UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsChildActId'), array($data), 'MCCUserID = ' . $loc_id, array(1));
    ob_start();
        $file = LG_COUNT_PLUGIN_DIR . '/views/modalfooter/gaconnent.php';
        include $file;
        $template = ob_get_contents(); // get contents of buffer
        ob_end_clean();
          json(1,'', $template);



}
else if ($_REQUEST["param"] == "select_ga1") {
     $data=$_REQUEST['data'];
     $loc_id=$_REQUEST['locationid'];
      $new='SelectedAnalyticsChildAccountComboBox'.$loc_id.'=';
      $str= str_replace($new,'',$_REQUEST['data']);
  // $_REQUEST['SelectedAnalyticsChildAccountComboBoxID'.$loc_id]=$str;
    $str2=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
     $str2=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
    $str3=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
      include_once get_template_directory() . '/analytics/AdWordsUtils.php';
      include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
      include_once get_template_directory() . '/analytics/AnalyticsUtils.php';
      $data= implode(SpecSeparatorStr, array($str,$str2, -1));
       UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsChildActId'), array($data), 'MCCUserID = ' . $loc_id, array(1));
      ob_start();
        $file = LG_COUNT_PLUGIN_DIR . '/views/modalfooter/gaconnent.php';
        include $file;
        $template = ob_get_contents(); // get contents of buffer
        ob_end_clean();
          json(1,'', $template);

}
else if ($_REQUEST["param"] == "select_ga2") {
     $data=$_REQUEST['data'];
     $loc_id=$_REQUEST['locationid'];
      $new='SelectedAnalyticsChildAccountComboBox'.$loc_id.'=';
      $str= str_replace($new,'',$_REQUEST['data']);
  // $_REQUEST['SelectedAnalyticsChildAccountComboBoxID'.$loc_id]=$str;
    $str2=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
     $str2=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
    $str3=$_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$loc_id];
      include_once get_template_directory() . '/analytics/AdWordsUtils.php';
      include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
      include_once get_template_directory() . '/analytics/AnalyticsUtils.php';
      $data= implode(SpecSeparatorStr, array($str,$str2,$str3));
       UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsChildActId'), array($data), 'MCCUserID = ' . $loc_id, array(1));
     ob_start();
        $file = LG_COUNT_PLUGIN_DIR . '/views/modalfooter/gaconnent.php';
        include $file;
        $template = ob_get_contents(); // get contents of buffer
        ob_end_clean();
          json(1,'', $template);

}
else if ($_REQUEST["param"] == "connect_gaa") {


  $user_trigger = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                       "SELECT * FROM ".database_name.".clients_table WHERE MCCUserId = %d", $_COOKIE['mccuserid']
                )
        );

json(1,'', $user_trigger);




}
else if ($_REQUEST["param"] == "content_recommend") {
        $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
        if ($user_id <= 0) {
            die;
        }
        $baseurl = get_user_meta($user_id, 'website', true); //isset($_REQUEST['url'])?trim($_REQUEST['url']):'';
        if ($baseurl == '') {
            json(0, 'Empty Website');
        }
        $history_entry = isset($_REQUEST['history_entry']) ? intval($_REQUEST['history_entry']) : 0;
        if ($history_entry == 1) {
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
        if (empty($user_trigger) || $user_trigger == 0) {
            $user_trigger = 1;
        }

        $type = isset($_REQUEST['typerequest']) ? $_REQUEST['typerequest'] : '';

        if ($type == 'allpage') {
            $res = crawl_page($user_id, $baseurl);
        } else {
            $res = target_pages($user_id);
        }
        //@mail("parambir.rudra@gmail.com","Crawl Result", json_encode($res));
        // billing code
        $bilingenable = 0;
        if (defined("BILLING_ENABLE") && BILLING_ENABLE == 1) {
            $bilingenable = 1;
        }
        if ($bilingenable == 1) {
            $lmt = check_lp_all_limits();
            $limit = $lmt['pages_available'];
        }


        require_once TR_COUNT_PLUGIN_DIR . '/simpletest/browser.php';
        $browser = &new SimpleBrowser();

        $outerar = array();
        $urlissues = array();
        $ik = 1;
        $totalurls = count($res['urls']);
        $total_title_issues = 0;
        $total_meta_issues = 0;
        $total_content_issues = 0;
        $total_heading_issues = 0;
        $total_link_issues = 0;
        $total_image_issues = 0;

        $allurls = $res['urls'];
        //@mail("parambir.rudra@gmail.com","All Urls", json_encode($allurls));
        if (empty($allurls)) {
            $x = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 0 WHERE user_id = %d", $user_id
                    )
            );
            $user_info = get_userdata($user_id);
            $useremail = $user_info->data->user_email . ',roger@enfusen.com,parambir@rudrainnovatives.com';
            $website = get_user_meta($user_id, 'website', true);
            $siteurl = site_url();
            $sub = 'CRE failed to crawl result. You need to rerun it again.';
            $body = 'Hi, <br/>br/> CRE has failed to crawl your website ' . $website . ' at agency ' . $siteurl . '.'
                    . ' You need to rerun it again.';
            @mail($useremail, $sub, $body);
            json(1, 'Done');
        } else {

            foreach ($allurls as $url) {
                $aurl = arrtoobj($url);
                $urlstrtosend = isset($aurl->{0}) ? $aurl->{0} : '';
                if ($urlstrtosend == '') {
                    continue;
                }
                // existing page keywords start
                $total = count((array) $url);
                $extkeywords = array();
                if ($total > 1) {
                    // if keywords also present in our database for url
                    $i = 0;
                    foreach ($url as $key => $u) {
                        if ($i > 0) {
                            $extkeywords[] = $u;
                        }
                        $i++;
                    }
                }

                if (!empty($extkeywords)) {
                    $extkeywords = json_encode($extkeywords);
                } else {
                    $extkeywords = '';
                }

                // existing page keywords end

                $urlchk = trim(str_replace(array("https://", "http://", "www."), array("", "", ""), $urlstrtosend), "/");
                $crepage = $wpdb->get_row
                        (
                        $wpdb->prepare
                                (
                                "SELECT id FROM cre_urls WHERE user_id = %d AND TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.','')) like '%s'", $user_id, $urlchk
                        )
                );

                if (empty($crepage)) {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO cre_urls(url, keyword, user_id, is_running, rundate, user_trigger) VALUES(%s, %s, %d, %d, '%s', %d)", $urlstrtosend, $extkeywords, $user_id, 1, date("Y-m-d H:i:s"), $user_trigger
                            )
                    );

                    $lastid = $wpdb->insert_id;
                } else {
                    $lastid = $crepage->id;
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE cre_urls SET keyword = %s, is_running = 1, rundate = '%s', user_trigger = %d WHERE id = %d", $extkeywords, date("Y-m-d H:i:s"), $user_trigger, $lastid
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
                if ($bilingenable == 1) {
                    // if billing enable then check limit, no of remaining page
                    if ($ik > $limit) {
                        break;
                    }
                }
            }

            if ($type == 'allpage') {
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
            if ($bilingenable == 1) {

                // rudra to show on billing page
                $total_scanned_pages = get_user_meta($user_id, 'total_scanned_pages', true);
                if (empty($total_scanned_pages) || $total_scanned_pages == '') {
                    add_user_meta($user_id, 'total_scanned_pages', count($urlissues));
                } else {
                    $total_scanned_pages = $total_scanned_pages + count($urlissues);
                    update_user_meta($user_id, 'total_scanned_pages', $total_scanned_pages);
                }
                // rudra to show on billing page

                $lasttotal = $wpdb->get_var("SELECT lpf_used FROM wp_location_package_fields WHERE lpf_field = 'pages'");
                $totalused = $lasttotal + count($urlissues);
                $wpdb->query("UPDATE wp_location_package_fields SET lpf_used = $totalused WHERE lpf_field = 'pages'");
            }
            // billing code

            if (empty($urlissues)) {
                $x = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 0 WHERE user_id = %d", $user_id
                        )
                );
            } else {
                //$final_result = json_encode($outerar);
                $pass = add_global_queue(site_url(), $urlissues, $user_id);
                if ($pass == 1) {
                    $x = $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE wp_content_recommend SET crawl_result = %s, auto_trigger = 0 WHERE user_id = %d", $crawl_result, $user_id
                            )
                    );
                } else {
                    $x = $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE wp_content_recommend SET trigger_report = 0, auto_trigger = 0 WHERE user_id = %d", $user_id
                            )
                    );
                }
            }

            json(1, 'Done');
        }
    }
}

function lg_deleteusermeta($user_id) {
    global $wpdb;
    $meta_table = $wpdb->prefix . "usermeta";
    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "DELETE FROM $meta_table WHERE user_id = %d", $user_id
            )
    );

    wp_delete_user($user_id);
}

function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

?>
