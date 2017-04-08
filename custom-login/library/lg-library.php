<?php

global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
session_start();

if (isset($_REQUEST["param"])) {
    if ($_REQUEST["param"] == "custom_agency_login") {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!defined('IS_MCC_SETUP')) {
            $_SESSION['is_enfusen'] = 0;
        } else {
            $_SESSION['is_enfusen'] = 1;
        }
        $creds = array();
        $creds['user_login'] = $_POST['Cusername'];
        $creds['user_password'] = $_POST['Cpassword'];
        //$creds['remember'] = $_POST['CRemember'];

        $check_canceled_user = wp_authenticate($_POST['Cusername'], $_POST['Cpassword']);
        if (isset($check_canceled_user->roles[0]) && $check_canceled_user->roles[0] == 'canceled_user') {
            json(0, "ERROR #1", "Invalid Authentication");
        } else {
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                json(0, "ERROR #2", "Invalid User");
            } else {
                if (!get_option("user_email_status")) {
                    add_option('user_email_status', 1);
                    add_option('profile_campaign', 1);
                    add_option('user_profile_status', 1);
                }

                $_SESSION['customuser'] = 1;
                $uid = $user->data->ID;
                $user_id = $uid;
                $user = get_user_by('id', $user_id);
                if ($user) {
                    wp_set_current_user($user_id, $user->user_login);
                    wp_set_auth_cookie($user_id);
                    do_action('wp_login', $user->user_login);
                }
                json(1, "login Successful", site_url() . '/agency-home');
            }
        } die();
    } else if ($_REQUEST["param"] == "agency_comp") {
        $url1 = $_REQUEST['compurl_1'];
        $url2 = $_REQUEST['compurl_2'];
        $url3 = $_REQUEST['compurl_3'];
        $urlArray = array($url1, $url2, $url3);
        foreach ($urlArray as $data) {
            $url_name = $data;
            $key_limit_for_url = 250;
            $URI = site_url() . '/cron/competitor-url-key-insert.php?user_id=' . $current_user->ID . '&keyword_opportunity_user=' . $current_user->ID . '&comp_url=' . $url_name . '&keywordlimit=' . $key_limit_for_url;
            $ch = curl_init($URI);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $result_on = curl_exec($ch);
        }
        //json(1, "Keyword Pulled Successfully",site_url());
    } else if ($_REQUEST["param"] == "agency_profile") {

        $result = wp_update_user(array('ID' => $_REQUEST['userid'], 'user_email' => $_REQUEST['email'], 'user_nicename' => $_REQUEST['name'], 'display_name' => $_REQUEST['name']));


        /* $current_id = user_id();

          update_user_meta($current_id, "website", "");

          update_user_meta($current_id, "phonenumber", $_POST['phone']);

          update_user_meta($current_id, "streetaddress", "test");

          update_user_meta($current_id, "city", "test");

          update_user_meta($current_id, 'country', $_POST['country']);

          update_user_meta($current_id, 'state', "test"); */


        /* $userProfileStatus = get_user_meta($_REQUEST['userid'], "user_profile_status", true);
          if (empty($userProfileStatus)) {
          add_user_meta($_REQUEST['userid'], "user_profile_status", 1);
          } */
        if (!get_option("user_profile_status")) {
            add_option("user_profile_status", 1);
        }

        $userPhone = get_user_meta($_REQUEST['userid'], "user_phone", true);
        if (empty($userPhone)) {
            add_user_meta($_REQUEST['userid'], "user_phone", $_REQUEST['phone']);
        } else {
            update_user_meta($_REQUEST['userid'], "user_phone", $_REQUEST['phone']);
        }

        $userCountry = get_user_meta($_REQUEST['userid'], "user_country", true);
        if (empty($userCountry)) {
            add_user_meta($_REQUEST['userid'], "user_country", $_REQUEST['country']);
        } else {
            update_user_meta($_REQUEST['userid'], "user_country", $_REQUEST['country']);
        }

        $userClients = get_user_meta($_REQUEST['userid'], "user_clients", true);
        if (empty($userClients)) {
            add_user_meta($_REQUEST['userid'], "user_clients", $_REQUEST['clients']);
        } else {
            update_user_meta($_REQUEST['userid'], "user_clients", $_REQUEST['clients']);
        }

        if (!is_wp_error($result)) {
            json(1, "User Updated Successfully");
        } else {
            json(0, "Failed to update User");
        }
    } else if ($_REQUEST["param"] == "change_email") {
        $result = wp_update_user(array('ID' => $_REQUEST['userid'], 'user_email' => $_REQUEST['email']));
        if (!is_wp_error($result)) {
            json(1, "Email Changed Successfully");
        } else {
            json(0, "Failed to update User Email");
        }
    } else if ($_REQUEST["param"] == "resend_mail") {

        if (user_send_mail($_REQUEST['uemail'], $_REQUEST['uid'])) {
            json(1, "Mail Sent Successfully");
        } else {
            json(0, "Mail Sent Failed");
        }
    } else if ($_REQUEST["param"] == "create_campaign") {

        /* Post Variables */
        $AdminID = $_REQUEST['userid'];
        $useremail = $_REQUEST['useremail'];
        $campaignType = $_REQUEST['ctype'];
        $country = $_REQUEST['country'];
        $geo_location = $_REQUEST['geo_location'];
        $website = $_REQUEST['curl'];
        $brandName = $_REQUEST['cname'];
        $clientName = $_REQUEST['uname'];
        $phoneNo = $_REQUEST['uphone'];
        $st = $_REQUEST['state'];

        if (filter_var($website, FILTER_VALIDATE_URL) === false) {
            json(0, "Please enter a valid url");
        }

        //$proto = $_REQUEST['proto'];
        //$website = str_replace(array("https://", "http://"), "", $website);
        //$website = $website;
        // setcookie('uname', 'gdg', time()+60*30);
        // json(1, "Meenu has stopped",'');

        if (!empty($brandName) && !empty($website) && !empty($geo_location)) {

            if ($_SESSION['is_campaign_active'] == 0) {
                // if (!is_valid_url_lg($website)) {
                //     json(0, "Please Enter Valid URL");
                // }


                $rand = time();
                $username = "site_location_" . $rand;
                $email = $username . "@test.com";
                $password = md5(time() . $username);
                $mcc_userId = wp_create_user($username, $password, $email);
                $location_id = 0;
                if ($mcc_userId > 0) {
                    $is_created = $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO wp_client_location (MCCUserId, created_by, created_dt) "
                                    . "VALUES (%d, %d, '%s')", $mcc_userId, $AdminID, date("Y-m-d H:i:s")
                            )
                    );
                    if ($is_created) {
                        $location_id = $wpdb->insert_id;
                        $_SESSION['is_campaign_active'] = 1;
                        $_SESSION['keywords_pulled'] = 0;
                    }
                }
                $location = $wpdb->get_row
                        (
                        $wpdb->prepare
                                (
                                "SELECT * FROM wp_client_location WHERE id = %d", $location_id
                        )
                );
                if (!empty($location)) {

                    $UserID = $location->MCCUserId;
                    $_SESSION['MccUserId'] = $UserID;

                    $_SESSION['general'] = array("brand" => $brandName, "website" => $website, "country" => $country, "geolocation" => $geo_location, "campaignType" => $campaignType, "mcc_userid" => $UserID, "state" => $st, "city" => $_REQUEST['city'], "street" => $_REQUEST['street'], "zipcode" => $_REQUEST['zipcode']);

                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE wp_client_location SET status = 1 "
                                    . "WHERE id = %d", $location_id
                            )
                    );

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_state_by_id.php?id=' . $_REQUEST['state']
                    ));
                    $result_states = curl_exec($curl);

                    $result_states_name = json_decode($result_states);

                    $stateAddr = $result_states_name->state_name;


                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_city_by_id.php?id=' . $_REQUEST['city']
                    ));
                    $result_city = curl_exec($curl);

                    $result_city_name = json_decode($result_city);

                    $cityAddr = $result_city_name->city_name;

                    update_user_meta($UserID, 'website', $website);

                    update_user_meta($UserID, 'client_name', $_REQUEST['uname']);

                    update_user_meta($UserID, 'business', $_REQUEST['cname']);
                    update_user_meta($UserID, 'company_name', $_REQUEST['cname']);
                    update_user_meta($UserID, 'BRAND_NAME', $_REQUEST['cname']);
                    update_user_meta($UserID, 'ct_BusinessName', $_REQUEST['cname']);
                    update_user_meta($UserID, 'campaignType', $_REQUEST['ctype']);

                    update_user_meta($UserID, 'country', $_REQUEST['country']);
                    update_user_meta($UserID, 'industry', $_REQUEST['cname']);
                    update_user_meta($UserID, 'geo_location', $_REQUEST['geo_location']);
                    update_user_meta($UserID, 'streetaddress', $_REQUEST['street']);
                    update_user_meta($UserID, 'city', $_REQUEST['geo_location']);
                    update_user_meta($UserID, 'state', $st);
                    update_user_meta($UserID, 'zip', $_REQUEST['zipcode']);

                    update_user_meta($UserID, 'country', $_REQUEST['country']);
                    update_user_meta($UserID, 'info_email', $_REQUEST['useremail']);
                    update_user_meta($UserID, 'info_phone', get_user_meta(1, "user_phone", true));
                    update_user_meta($UserID, 'phonenumber', get_user_meta(1, "user_phone", true));
                    update_user_meta($UserID, 'ct_GoogleLocation', $_REQUEST['geo_location']);

                    /* Running SEMrush keyword pull */

                    pull_locationUrl_keywords($UserID, $website, 250);  /* Working */
                    trigger_siteaudit($UserID, $website);

                    $keywords = $wpdb->get_results("SELECT * FROM keyword_opportunity where user_id = " . $UserID . " and url like '%" . $website . "%'");
                    $_SESSION['keywords'] = $keywords;
                    $_SESSION['key_exist'] = 1;
                    ob_start();
                    setcookie('mccuserid', $_SESSION['MccUserId'], time() + 60 * 30);
                    ob_flush();
                    json(1, $keywords, $UserID);
                } else {

                    json(0, "Failed to Save Location");
                }
            } else {

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_state_by_id.php?id=' . $_REQUEST['state']
                ));
                $result_states = curl_exec($curl);

                $result_states_name = json_decode($result_states);

                $stateAddr = $result_states_name->state_name;


                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_city_by_id.php?id=' . $_REQUEST['city']
                ));
                $result_city = curl_exec($curl);

                $result_city_name = json_decode($result_city);

                $cityAddr = $result_city_name->city_name;

                update_user_meta($_SESSION['MccUserId'], 'website', $website);

                update_user_meta($_SESSION['MccUserId'], 'client_name', $_REQUEST['uname']);

                update_user_meta($_SESSION['MccUserId'], 'business', $_REQUEST['cname']);
                update_user_meta($_SESSION['MccUserId'], 'company_name', $_REQUEST['cname']);
                update_user_meta($_SESSION['MccUserId'], 'BRAND_NAME', $_REQUEST['cname']);
                update_user_meta($_SESSION['MccUserId'], 'ct_BusinessName', $_REQUEST['cname']);
                update_user_meta($_SESSION['MccUserId'], 'campaignType', $_REQUEST['ctype']);

                update_user_meta($_SESSION['MccUserId'], 'country', $_REQUEST['country']);
                update_user_meta($_SESSION['MccUserId'], 'industry', $_REQUEST['cname']);
                update_user_meta($_SESSION['MccUserId'], 'geo_location', $_REQUEST['geo_location']);
                update_user_meta($_SESSION['MccUserId'], 'streetaddress', $_REQUEST['street']);
                update_user_meta($_SESSION['MccUserId'], 'city', $_REQUEST['geo_location']);
                update_user_meta($_SESSION['MccUserId'], 'state', $st);
                update_user_meta($_SESSION['MccUserId'], 'zip', $_REQUEST['zipcode']);

                update_user_meta($_SESSION['MccUserId'], 'country', $_REQUEST['country']);
                update_user_meta($_SESSION['MccUserId'], 'info_email', $_REQUEST['useremail']);
                update_user_meta($UserID, 'info_phone', get_user_meta(1, "user_phone", true));
                update_user_meta($UserID, 'phonenumber', get_user_meta(1, "user_phone", true));
                update_user_meta($_SESSION['MccUserId'], 'ct_GoogleLocation', $_REQUEST['geo_location']);

                $keywords = $wpdb->get_results("SELECT * FROM keyword_opportunity where user_id = " . $_SESSION['MccUserId'] . " and url like '%" . $website . "%'");
                json(1, $keywords, $_SESSION['MccUserId']);
            }
        } else {
            json(0, "All Fields are Required");
        }
    } else if ($_REQUEST["param"] == "test_method") {

        $placesscout_api_info = placesscout_api_info();
        json(1, $placesscout_api_info);
    } else if ($_REQUEST["param"] == "run_citation") {

	$user_id = $_REQUEST['mccuserid'];

        update_user_meta($user_id, 'ct_BusinessName', $_REQUEST['rprt_name']);
        update_user_meta($user_id, 'streetaddress', $_REQUEST['street_addr']);
        update_user_meta($user_id, 'website', $_REQUEST['bus_name']);
        update_user_meta($user_id, 'ct_Keyword', $_REQUEST['pr_prod']);
        update_user_meta($user_id, 'country', $_REQUEST['country_citation']);
        update_user_meta($user_id, 'zip', $_REQUEST['zipcode_addr']);
        update_user_meta($user_id, 'ct_GoogleLocation', $_REQUEST['geolocation_citation']);
        update_user_meta($user_id, 'phonenumber', $_REQUEST['uphoneNo']);
        update_user_meta($user_id, 'BRAND_NAME', $_REQUEST['rprt_name']);
        update_user_meta($user_id, 'city', $_REQUEST['geolocation_citation']);
        update_user_meta($user_id, 'state', $_REQUEST['geolocation_citation']);
        
        $_SESSION['citation'] = 1;
        
        if (trigger_citation($user_id)) {
            json(1, "Citation Started");
        } else {
            json(0, "Failed to Generate Report ID", array(get_user_meta($user_id, 'ct_BusinessName', true), get_user_meta($user_id, 'streetaddress', true), get_user_meta($user_id, 'city', true), get_user_meta($user_id, 'state', true), get_user_meta($user_id, 'zip', true), get_user_meta($user_id, 'website', true), get_user_meta($user_id, 'phonenumber', true), get_user_meta($user_id, 'country', true)));
        }
        
    } else if ($_REQUEST["param"] == "run_competitor") {
        $MCCUserId = $_REQUEST['mccuserid'];

        $urls = array();
        if (!empty($_REQUEST['url1'])) {
            if (is_valid_url_lg($_REQUEST['url1'])) {
                array_push($urls, $_REQUEST['url1']);
            } else {
                json(0, "Invalid URL Found");
            }
        }

        if (!empty($_REQUEST['url2'])) {
            if (is_valid_url_lg($_REQUEST['url2'])) {
                array_push($urls, $_REQUEST['url2']);
            } else {
                json(0, "Invalid URL Found");
            }
        }

        if (!empty($_REQUEST['url3'])) {
            if (is_valid_url_lg($_REQUEST['url3'])) {
                array_push($urls, $_REQUEST['url3']);
            } else {
                json(0, "Invalid URL Found");
            }
        }
        //json(1, "Keyword Pulled Successfully", site_url() . "/dashboard");
        if (count($urls) > 0) {
            foreach ($urls as $url) {
                $limit = 250;
                pull_competitorUrl_keywords($MCCUserId, $url, $limit);
            }
            add_user_meta($MCCUserId, "competitor_url", $urls);
            json(1, "Keyword Pulled Successfully", site_url() . "/dashboard");
        } else {
            json(0, "No URL Found to Pull Keywords");
        }
    } else if ($_REQUEST["param"] == "unset_session") {
        $current_user_id = $_POST['current_userID'];

        $_SESSION['is_campaign_active'] = 0;
        $_SESSION['keywords_pulled'] = '';

        if (isset($_SESSION['general'])) {
            unset($_SESSION['general']);
        }
        if (isset($_SESSION['keywords'])) {
            unset($_SESSION['keywords']);
        }
        if (isset($_SESSION['key_exist'])) {
            unset($_SESSION['key_exist']);
        }

        if (isset($_SESSION['integrationed'])) {
            unset($_SESSION['integrationed']);
        }
        if (isset($_SESSION['integration'])) {
            unset($_SESSION['integration']);
        }

        if (isset($_SESSION['citation'])) {
            unset($_SESSION['citation']);
        }
        /* if (empty(get_user_meta($current_user_id, "profile_campaign", true))) {
          add_user_meta($current_user_id, "profile_campaign", $current_user_id);
          } */
        if (!get_option("profile_campaign")) {
            add_option("profile_campaign", 1);
        }
        //setcookie("mccuserid", "", time() - 60 * 30);

        json(1, "", $_SESSION);
    } else if ($_REQUEST["param"] == "campaign_edit") {

        //json(1,$_REQUEST);

        $UserID = $_REQUEST['mccuser_edit'];

        update_user_meta($UserID, 'website', $_REQUEST['website_edit']);

        update_user_meta($UserID, 'client_name', $_REQUEST['uname']);

        update_user_meta($UserID, 'business', $_REQUEST['cname_edit']);
        update_user_meta($UserID, 'company_name', $_REQUEST['cname_edit']);
        update_user_meta($UserID, 'BRAND_NAME', $_REQUEST['cname_edit']);
        update_user_meta($UserID, 'ct_BusinessName', $_REQUEST['cname_edit']);
        update_user_meta($UserID, 'campaignType', $_REQUEST['ctype']);

        update_user_meta($UserID, 'industry', $_REQUEST['cname_edit']);
        update_user_meta($UserID, 'geo_location', $_REQUEST['geolocation']);
        // update_user_meta($UserID, 'streetaddress', "testing");
        // update_user_meta($UserID, 'city', "testing");
        // update_user_meta($UserID, 'state', "testing");
        // update_user_meta($UserID, 'zip', "testing");

        update_user_meta($UserID, 'country', $_REQUEST['country_location_edit']);
        update_user_meta($UserID, 'info_email', $_REQUEST['useremail']);
        //update_user_meta($UserID, 'info_phone', $_REQUEST['userphone']);
        update_user_meta($UserID, 'phonenumber', $_REQUEST['userphone']);
        update_user_meta($UserID, 'ct_GoogleLocation', $_REQUEST['geolocation']);

        json(1, "Campaign Updated Successfully");
    } else if ($_REQUEST["param"] == "state_list") {

        $c_code = $_REQUEST['country_code'];
        //$c_id = $wpdb->get_var("select country_id from countries where country_code = '" . $c_code . "'");

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_states.php?id=' . $c_code
        ));
        $result_states = curl_exec($curl);

        $result_states = json_decode($result_states);

        $states_array = array();
        foreach ($result_states as $key => $value) {
            $states_array[] = array("id" => $value->id, "title" => $value->title);
        }
        json(1, $states_array);
    } else if ($_REQUEST["param"] == "city_list") {

        $state_code = $_REQUEST['state_code'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_cities.php?id=' . $state_code
        ));
        $result_cities = curl_exec($curl);

        $result_cities = json_decode($result_cities);

        //$cities = $wpdb->get_results("select * from cities where state_id =" . $state_code);
        $cities_array = array();
        foreach ($result_cities as $key => $value) {
            $cities_array[] = array("id" => $value->id, "title" => $value->title);
        }
        json(1, array_map("unserialize", array_unique(array_map("serialize", $cities_array))));
    } else if ($_REQUEST["param"] == "lost_password") {
        $email = $_REQUEST['lostemail'];

        $user_info = get_userdata(1);
        $username = $user_info->user_login;
        $uemail = $user_info->user_email;

        if (!strcmp($email, $uemail)) {
            if (lglostPassword($username, $email)) {
                json(1, "Password reset link sent to mail");
            } else {
                json(0, "Mail Sent Failed");
            }
        } else {
            json(0, "ERROR: There is no user registered with that email address.");
        }
    } else if ($_REQUEST["param"] == "change_password") {
        $password = $_REQUEST['confpwd'];
        if (lgresetpwd(1, $password)) {
            json(1, "Password Changed Successfully", site_url() . "/custom-agency-login");
        } else {
            json(0, "Failed to change password");
        }
    } else if ($_REQUEST["param"] == "campaign_keywords") {

        $campaign_keywords = $_REQUEST['choosekeywords'];

        $MCCUserId = $_REQUEST['mccuserid'];
        $BrandName = get_user_meta($MCCUserId, "BRAND_NAME", true);
        $TargetCountry = get_user_meta($MCCUserId, "country", true);
        $LocalLocation = get_user_meta($MCCUserId, "streetaddress", true);
        $UserID = 1;
        $Status = 1;
        $Is_Running = 0;
        $RunDate = date("Y-m-d H:i:s");
        $RankDate = date("Y-m-d H:i:s");
        $CreatedDate = date("Y-m-d H:i:s");
        $UpdatedDate = date("Y-m-d H:i:s");

        $totalKeywords = count($campaign_keywords);
        $loopCount = 0;
        /* wp_campaign tbl Entry */

        if (isset($_SESSION['keywords_pulled']) && $_SESSION['keywords_pulled'] == 0) {
            $is_campaign_created = $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO wp_campaigns (name, target_country, local_location,location_id,user_id,status,is_running,rundate,rankdate,created_dt,updated_dt) "
                            . "VALUES (%s, %s, %s,%s,%s,%s,%s,%s,%s,%s,%s)", $BrandName, $TargetCountry, $LocalLocation, $MCCUserId, $UserID, $Status, $Is_Running, $RunDate, $RankDate, $CreatedDate, $UpdatedDate
                    )
            );

            if ($wpdb->insert_id > 0) {

                $CampaignId = $wpdb->insert_id;
                foreach ($campaign_keywords as $keyword) {
                    /* wp_keygroup tbl Entry */
                    $is_group_created = $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO wp_keygroup (campaign_id, location_id, google_location,is_target,user_id,status,created_dt,updated_dt) "
                                    . "VALUES (%s, %s, %s,%s,%s,%s,%s,%s)", $CampaignId, $MCCUserId, $LocalLocation, 'No', $UserID, $Status, $CreatedDate, $UpdatedDate
                            )
                    );

                    if ($wpdb->insert_id > 0) {

                        $GroupId = $wpdb->insert_id;
                        /* wp_keywords tbl Entry */
                        $is_keyword_created = $wpdb->query
                                (
                                $wpdb->prepare
                                        (
                                        "INSERT INTO wp_keywords (campaign_id, group_id, location_id,keyword,isprimary,user_id,created_dt,updated_dt) "
                                        . "VALUES (%s, %s, %s,%s,%s,%s,%s,%s)", $CampaignId, $GroupId, $MCCUserId, $keyword, 1, $UserID, $CreatedDate, $UpdatedDate
                                )
                        );
                    }

                    $loopCount++;
                }

                if ($totalKeywords == $loopCount) {
                    $_SESSION['keywords_pulled'] = 1;
                    json(1, "Keywords Pulled to Campaign Tbl Successfully");
                } else {
                    json(0, "Failed to Make Entry in Campaign Tbl", "Total Keywords Pulled : " . $loopCount);
                }
            } else {
                json(0, "Failed to Create Campaign", $_SESSION['keywords_pulled']);
            }
        } else {
            json(1, "Keywords Already Pulled to Campaign");
        }
    }
}

function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

function pull_locationUrl_keywords($locationId, $url, $limit) {

    $URI = site_url() . '/cron/competitor-url-key-insert.php?user_id=' . $locationId . '&keyword_opportunity_user=' . $locationId . '&ownweb=' . $url . '&keywordlimit=' . $limit;
    $ch = curl_init($URI);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result_on = curl_exec($ch);
    curl_close($ch);
}

function pull_competitorUrl_keywords($locationId, $url, $limit) {
    $URI = site_url() . '/cron/competitor-url-key-insert.php?user_id=' . $locationId . '&keyword_opportunity_user=' . $locationId . '&comp_url=' . $url . '&keywordlimit=' . $limit;
    $ch = curl_init($URI);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result_on = curl_exec($ch);
    curl_close($ch);
}

function trigger_siteaudit($locationId, $url) {
    /* Site Audit */
    global $wpdb;
    $userid = $locationId;
    $semrush_api_info = semrush_api_info();
    $key = $semrush_api_info['key'];
    $main_api_url = $semrush_api_info['main_api_url'];

    $curl_url = 'management/v1/projects?key=' . $key;
    $post_data['url'] = fully_trim($url);
    $data_string = json_encode($post_data);

    $create_new_project = pc_post($username = '', $password = '', $main_api_url, $curl_url, $data_string);
    $create_new_project = json_decode($create_new_project);
    $project_id = $create_new_project->project_id;

    //add_user_meta($locationId,"semrush_project_id",$project_id);
    // Enable the site audit tool
    $curl_url = 'management/v1/projects/' . $project_id . '/siteaudit/enable?key=' . $key;
    $enable_site_audit_tool_post_data['domain'] = fully_trim($url);
    $enable_site_audit_tool_post_data['scheduleDay'] = 0;
    $enable_site_audit_tool_post_data['notify'] = false;
    $enable_site_audit_tool_post_data['pageLimit'] = '1500';
    $enable_site_audit_tool_post_data['crawlSubdomains'] = false;

    $data_string = json_encode($enable_site_audit_tool_post_data);
    $enable_site_audit_tool = pc_post($username = '', $password = '', $main_api_url, $curl_url, $data_string);

    $curl_url = 'reports/v1/projects/' . $project_id . '/siteaudit/launch?key=' . $key;
    $data_string = json_encode(array());
    $run_audit = pc_post($username = '', $password = '', $main_api_url, $curl_url, $data_string);
    $run_audit = json_decode($run_audit);
    $snapshot_id = $run_audit->snapshot_id;

    $insert_site_audit['user_id'] = $userid;
    $insert_site_audit['campaign_id'] = $project_id;
    $insert_site_audit['snapshot_id'] = $snapshot_id;
    $insert_site_audit['audit_status'] = 'In Progress';
    $insert_site_audit['last_audit'] = date('Y-m-d H:i:s');

    $wpdb->insert('wp_site_audit', $insert_site_audit);
}

function trigger_citation($locationId) {
    global $wpdb;

    $user_id = $locationId;

    $placesscout_api_info = placesscout_api_info();

    $username = $placesscout_api_info['username'];
    $password = $placesscout_api_info['password'];
    $main_api_url = $placesscout_api_info['main_api_url'];

    $new_citation['Name'] = get_user_meta($user_id, 'BRAND_NAME', true);
    $new_citation['BusinessInfo']['BusinessName'] = get_user_meta($user_id, 'ct_BusinessName', true);
    $new_citation['BusinessInfo']['StreetAddress'] = get_user_meta($user_id, 'streetaddress', true);
    $new_citation['BusinessInfo']['City'] = get_user_meta($user_id, 'city', true);
    $new_citation['BusinessInfo']['State'] = get_user_meta($user_id, 'state', true);
    $new_citation['BusinessInfo']['ZipCode'] = get_user_meta($user_id, 'zip', true);
    $new_citation['BusinessInfo']['WebsiteUrl'] = get_user_meta($user_id, 'website', true);
    $new_citation['BusinessInfo']['PhoneNumber'] = get_user_meta($UserID, 'phonenumber', true);
    $new_citation['BusinessInfo']['Country'] = get_user_meta($user_id, 'country', true);
    $new_citation['KeywordSearchesForCompetitiveAnalysis']['GoogleLocation'] = get_user_meta($user_id, 'ct_GoogleLocation', true);
    $new_citation['TotalCompetitorsToAnalyze'] = 10;
    $new_citation['NumResultsToGatherPerQuery'] = 200;
    $new_citation['GatherCitationStrengthData'] = true;

    $curl_url = 'citationreports';
    $new_citations = pc_post($username, $password, $main_api_url, $curl_url, json_encode($new_citation)); //Create New Citaions
    $new_citations = json_decode($new_citations);

    $ReportId = $new_citations->id;

    if ($ReportId != "") {
        $insert_citation['user_id'] = $user_id;
        $insert_citation['ReportId'] = $ReportId;
        $insert_citation['status'] = 'In Progress';
        $insert_citation['last_run'] = date('Y-m-d H:i:s');

        $check_existing_ReportId = $wpdb->get_row("SELECT * FROM `wp_citation_tracker` WHERE `user_id` = $user_id");

        if (empty($check_existing_ReportId)) {
            $wpdb->insert('wp_citation_tracker', $insert_citation);
            $citation_run['ReportId'] = $ReportId;
            $curl_url = 'citationreports/' . $ReportId . '/runreport';
            $citations_run = pc_post($username, $password, $main_api_url, $curl_url, json_encode($citation_run));
        }

        return true;
    } else {
        return false;
    }
}

function lgresetpwd($userid, $password) {
    $user_data = wp_update_user(array('ID' => $userid, 'user_pass' => $password));
    if (is_wp_error($user_data)) {
        return false;
    } else {
        return true;
    }
}

function lglostPassword($username, $email) {

    $to = empty($email) ? "sanjay@rudrainnovatives.com" : $email;

    $subject = "Reset Password via Enfusen";

    $htmlContent = '
                <table style="background-color:#f4f4f4" width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td align="center" bgcolor="#f4f4f4">
                                <table width="640" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color:transparent;padding:10px 30px" bgcolor="transparent">
                                                <img src="' . site_url() . '/wp-content/themes/twentytwelve/images/logo.png" style="max-height:3rem;max-width:30rem;display:inline;min-height:auto;line-height:100%;outline-style:none;text-decoration:none" border="0">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <span><font color="#888888">
                                    </font></span><span class="HOEnZb"><font color="#888888">
                                    </font></span><span class="HOEnZb"><font color="#888888">
                                    </font></span><table style="border:1px solid #e5e5e5" width="640" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td style="font-family:sans-serif;font-size:16px;line-height:25px;color:#666666;border-collapse:collapse;padding:30px">
                                                Someone requested that the password be reset for the following account:
                                                <br>' . site_url() . '<br>Username : ' . $username . '<br><br>If this was a mistake, just ignore this email and nothing will happen.<br>

To reset your password, visit the following address:

                                               <div style="width: 50%;margin: 0px auto;">
 <a href="' . site_url() . '/custom-agency-login/?em=' . base64_encode(base64_encode($email)) . '&action=' . base64_encode(base64_encode("resetpwd")) . '&redirect_uri=' . site_url() . '/custom-agency-login/"  style="font-size:13px;font-weight:100;font-family:sans-serif;text-transform:uppercase;text-align:center;letter-spacing:1px;text-decoration:none;line-height:62px;display:block;width:300px;min-height:60px;border-top-left-radius:3px;border-top-right-radius:3px;border-bottom-right-radius:3px;border-bottom-left-radius:3px;color:#ffffff;background:#2985cc" target="_blank" >
                                                    Reset Password
                                                </a>
</div>
                                                <br>
                                                Thanks from the Enfusen Optimization Bot								</td></tr></tbody></table><span class="HOEnZb"><font color="#888888">
                                                
                                    </font>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="yj6qo"></div>
                <div class="adL">
                </div>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    $headers .= 'From: Enfusen Notification<notifications@enfusen.com>' . "\r\n";

    if (mail($to, $subject, $htmlContent, $headers)) {
        return true;
    } else {
        return false;
    }
}

function is_valid_url_lg($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
        return true;
    } else {
        return false;
    }
}

function user_send_mail($mailto, $userid) {

    $to = empty($mailto) ? "sanjay@rudrainnovatives.com" : $mailto;

    $subject = "Email Verfication via Enfusen";

    $htmlContent = '
                <table style="background-color:#f4f4f4" width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td align="center" bgcolor="#f4f4f4">
                                <table width="640" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color:transparent;padding:10px 30px" bgcolor="transparent">
                                                <img src="' . site_url() . '/wp-content/themes/twentytwelve/images/logo.png" style="max-height:3rem;max-width:30rem;display:inline;min-height:auto;line-height:100%;outline-style:none;text-decoration:none" border="0">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <span><font color="#888888">
                                    </font></span><span class="HOEnZb"><font color="#888888">
                                    </font></span><span class="HOEnZb"><font color="#888888">
                                    </font></span><table style="border:1px solid #e5e5e5" width="640" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td style="font-family:sans-serif;font-size:16px;line-height:25px;color:#666666;border-collapse:collapse;padding:30px"><h2 style="margin:0 0 16px 0">Welcome to Enfusen Analytics,</h2>
                                                Please confirm your email address to start using the Enfusen Analytics Platform.
                                                <br><br>

                                               <div style="width: 50%;margin: 0px auto;">
 <a href="' . site_url() . '/custom-agency-login/?uid=' . base64_encode(base64_encode($userid)) . '&verified=' . base64_encode(base64_encode("true")) . '&redirect_uri=' . site_url() . '/custom-agency-login/" title="Confirm Email Address" style="font-size:13px;font-weight:100;font-family:sans-serif;text-transform:uppercase;text-align:center;letter-spacing:1px;text-decoration:none;line-height:62px;display:block;width:300px;min-height:60px;border-top-left-radius:3px;border-top-right-radius:3px;border-bottom-right-radius:3px;border-bottom-left-radius:3px;color:#ffffff;background:#2985cc" target="_blank" >
                                                    CONFIRM EMAIL
                                                </a>
</div>
                                                <br>
                                                Thanks from the Enfusen Optimization Bot								</td></tr></tbody></table><span class="HOEnZb"><font color="#888888">
                                                
                                    </font>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="yj6qo"></div>
                <div class="adL">
                </div>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    $headers .= 'From: Enfusen Notification<notifications@enfusen.com>' . "\r\n";

    if (mail($to, $subject, $htmlContent, $headers)) {
        return true;
    } else {
        return false;
    }
}

?>
