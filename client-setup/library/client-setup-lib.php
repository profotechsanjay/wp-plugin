<?php

global $wpdb;
global $current_user;
$current_user = wp_get_current_user();

if (isset($_REQUEST["param"])) {
    if ($_REQUEST["param"] == "add_setup") {
        $user_id = $current_user->ID;
        if ($user_id == 0) {
            json(0, 'Login is required');
        }
        $now = date("Y-m-d H:i:s");
        $prefix = isset($_POST['prefix']) ? trim(htmlspecialchars($_POST['prefix'])) : '';
        $prefix = strtolower($prefix);

        $haspre = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM " . setup_table() . " WHERE prefix = %s ", $prefix
                )
        );

        if ($haspre > 1) {
            json(0, 'URL already used ');
        }


        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';

        $hasrec = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM " . setup_table() . " WHERE email = %s ", $email
                )
        );

        if ($hasrec > 1) {
            json(0, 'Email already used. Please use different email.');
        }

        $login = isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '';

        $hasrec = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM " . setup_table() . " WHERE login = %s ", $login
                )
        );

        if ($hasrec > 1) {
            json(0, 'Login ID already exist. Please use different login ID.');
        }

        $password = isset($_POST['password']) ? htmlspecialchars($_REQUEST["password"]) : '';
        $password = base64_encode(base64_encode($password));

        $url = $prefix . '.' . ST_DOMAIN;
        $dir = ST_DIR . '/' . $prefix;
        tool_setup_script($prefix, $dir, $url);
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . setup_table() . " (name, email, login, password, prefix, url, dir, created_by, created_dt, updated_by) "
                        . "VALUES (%s, %s, %s, %s, %s, %s, %s, %d, '%s', %d)", $name, $email, $login, $password, $prefix, $url, $dir, $user_id, $now, $user_id
                )
        );

        $insert_id = $wpdb->insert_id;

        json(1, 'Setup initialized successfully.', $insert_id);
    } else if ($_REQUEST["param"] == "order_counting_change") {

        $wrid = current_id();
        $tbl = $wpdb->prefix . "setup_table";
        $sql = "SELECT id, client_id, name, db_name, email, url, white_lbl FROM $tbl where status = 1 AND client_id IS NOT NULL";
        $list = $wpdb->get_results($sql);

        $arall = array(
            'neworders' => 0,
            'delorders' => 0,
            'apporders' => 0,
            'reqorders' => 0,
            'canorders' => 0,
            'allorders' => 0
        );

        $params = array('type' => 'countorders', 'writer_id' => $wrid);
        foreach ($list as $ls) {

            $params['status'] = 'Ordered';
            $neworders = dbexecute($ls->db_name, $params);
            $arall['neworders'] = $arall['neworders'] + $neworders;

            $params['status'] = 'Delivered';
            $delorders = dbexecute($ls->db_name, $params);
            $arall['delorders'] = $arall['delorders'] + $delorders;

            $params['status'] = 'Approved';
            $apporders = dbexecute($ls->db_name, $params);
            $arall['apporders'] = $arall['apporders'] + $apporders;

            $params['status'] = 'Request_Changes';
            $reqorders = dbexecute($ls->db_name, $params);
            $arall['reqorders'] = $arall['reqorders'] + $reqorders;

            $params['status'] = 'Canceled';
            $canorders = dbexecute($ls->db_name, $params);
            $arall['canorders'] = $arall['canorders'] + $canorders;

            $params['status'] = 'all-order';
            $allorders = dbexecute($ls->db_name, $params);
            $arall['allorders'] = $arall['allorders'] + $allorders;
        }

        json(1, '', $arall);
    } else if ($_REQUEST["param"] == "updateagencyfiles") {

        $mainar = isset($_REQUEST['mainar']) ? $_REQUEST['mainar'] : '';
        if (!empty($mainar)) {
            $mainar = json_decode(stripslashes($mainar));
            if (empty($mainar->agencies)) {
                json(1, 'Select at least one agency');
            }
            $filmoved = 0;
            $agencies = $mainar->agencies;
            $mainagency = basename(ST_SOURCE_SETUP);

            foreach ($agencies as $agency) {

                $agencyfolder = basename($agency);
                $theme = $mainar->themes;
                $plugin = $mainar->plugins;
                $rootfiles = $mainar->rootfiles;

                $files = array_merge($theme, $plugin, $rootfiles);

                foreach ($files as $file) {
                    // special case for menu
                    $filename = basename($file);
                    // special case for menu

                    $desexp = explode('/' . $mainagency . '/', $file);

                    if (count($desexp) < 2) {
                        continue;
                    }
                    $pathnext = $desexp[1];
                    $destination = dirname($agency . '/' . $pathnext);

                    if ($filename == 'top_menu.php') {
                        $str = file_get_contents(ABSPATH . '/wp-content/plugins/client-setup/dbs/top_menu.php');
                        $destfile = $agency . '/wp-content/themes/twentytwelve/common/top-menu.php';
                        file_put_contents($destfile, $str);
                    } else if ($filename == 'header.php') {
                        $str = file_get_contents(ABSPATH . '/wp-content/plugins/client-setup/dbs/header.php');
                        $destfile = $agency . '/wp-content/themes/twentytwelve/header.php';
                        file_put_contents($destfile, $str);
                    } else {
                        //Commented starts by rudra 10-feb-2017
                        $upPath = $destination;
                        $tags = explode('/', $upPath);
                        $mkDir = "";
                        foreach ($tags as $folder) {
                            $mkDir = $mkDir . $folder . "/";
                            //echo '"'.$mkDir.'"<br/>';         
                            if (!is_dir($mkDir)) {
                                @mkdir($mkDir, 0777, true);
                            }
                        }
                        //Commented ends by rudra 10-feb-2017

                        $command = 'cp ' . $file . ' ' . $destination;
                        system($command);
                    }
                    $filmoved = 1;
                }

                $setting_plugin = $mainar->settingplugin;
                if ($setting_plugin == 1) {
                    //move settings plugin
                    $source = ABSPATH . 'wp-content/plugins/client-setup/dbs/settings';
                    $dest = $agency . '/wp-content/plugins/settings';
                    $filters = array(
                        ABSPATH . 'wp-content/plugins/client-setup/dbs/settings/nbproject',
                        ABSPATH . 'wp-content/plugins/client-setup/dbs/settings/php-webdriver',
                        ABSPATH . 'wp-content/plugins/client-setup/dbs/settings/uploads',
                        ABSPATH . 'wp-content/plugins/client-setup/dbs/settings/assets/fonts'
                    );

                    rcopyfilter($source, $dest, $filters);
                    $filmoved = 1;
                }
            }
            if ($filmoved == 0) {
                json(0, 'No file selected');
            } else {
                json(1, 'Update successfully done');
            }
        } else {
            json(0, 'Invalid Request');
        }
    } else if ($_REQUEST["param"] == "setup_creation") {
        $user_id = $current_user->ID;
        if ($user_id == 0) {
            json(0, 'Login is required');
        }
        $setup_id = isset($_POST["setup_id"]) ? intval($_POST["setup_id"]) : 0;
        $setup_update = isset($_POST["setup_update"]) ? intval($_POST["setup_update"]) : 0;

        $setup = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                )
        );

        if (empty($setup)) {
            json(0, 'Invalid Setup ID');
        }

        if ($setup->client_id == '') {
            $userdata = array(
                'user_login' => $setup->name,
                'user_url' => $setup->url,
                'user_pass' => md5($setup->db_name)
            );
            $userid = wp_insert_user($userdata);
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . setup_table() . " SET client_id = %d WHERE id = %d", $userid, $setup_id
                    )
            );
        }

        if ($setup_update == 1 || $setup_update == 2) {
            prevent_medstar($setup->db_name);
        }

        $option_name = 'client_setup_' . $setup_id;

        if (!get_option($option_name)) {
            add_option($option_name, "0");
        } else {
            update_option($option_name, "0");
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['setupcompleted'] = 10;

        tool_setup_creation($setup, $setup_update);

        // Start default trial code - 7 Jan 2016

        $agency_id = $setup_id;
        $checkdefault_trial = $wpdb->get_row("SELECT dc_id, dc_trial_length FROM `wp_discountcode` WHERE `dc_default` = 'default'");
        $discount_codeid = $checkdefault_trial->dc_id;
        $discount_trial_length = $checkdefault_trial->dc_trial_length;
        $getagency_created = date("Y-m-d H:i:s");
        $expire_date = date('Y-m-d h:i:s', strtotime($getagency_created . ' +' . $discount_trial_length . ' days'));
        ;
        $wpdb->query("INSERT INTO `wp_assign_trialcode`(`tc_dcid`, `tc_agencyid`, `tc_status`, `tc_created`, `tc_expire`, `tc_updated`) "
                . "VALUES ('" . $discount_codeid . "','" . $agency_id . "','active','" . $getagency_created . "','" . $expire_date . "','" . date("Y-m-d h:i:s") . "')");

        // End default trial code - 7 Jan 2016                        

        if ($setup_update == 0)
            json(1, 'Setup build successfully.');
        else if ($setup_update == 1)
            json(1, 'Setup re-build successfully.');
        else if ($setup_update == 2)
            json(1, 'Setup update successfully.');
    }
    else if ($_REQUEST["param"] == "delete_announcement") {
        $user_id = $current_user->ID;
        if ($user_id == 0) {
            json(0, 'Login is required');
        }

        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . en_announcements() . " WHERE id = %d", $id
                )
        );

        json(1, 'Announcement Deleted');
    } else if ($_REQUEST["param"] == "check_availablity") {

        $user_id = $current_user->ID;
        if ($user_id == 0) {
            json(0, 'Login is required');
        }
        $prefix = isset($_POST['prefix']) ? trim(htmlspecialchars($_POST['prefix'])) : '';
        if ($prefix == '') {
            json(0, 'Invalid Request');
        }
        $haspre = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM " . setup_table() . " WHERE prefix = %s ", $prefix
                )
        );
        $url = $prefix . '.' . ST_DOMAIN;
        if ($haspre > 0) {
            json(0, 'URL ' . $url . ' is already used ');
        } else {
            json(1, 'URL ' . $url . ' is available');
        }
    } else if ($_REQUEST["param"] == "rebuild_config") {

        $user_id = $current_user->ID;
        if ($user_id == 0) {
            json(0, 'Login is required');
        }
        $setup_id = isset($_POST["setup_id"]) ? intval($_POST["setup_id"]) : 0;
        $setup = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                )
        );
        if (empty($setup)) {
            json(0, 'Invalid Setup ID');
        }

        $pass = base64_decode(base64_decode($setup->db_password));

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "SET PASSWORD FOR " . $setup->db_username . "@" . DB_HOST . " = PASSWORD('" . $pass . "')", ""
                )
        );
        $dest_file = $setup->dir . "/global_config.php";

        $str = file_get_contents(ST_COUNT_PLUGIN_DIR . '/dbs/global_config.php');
        $old = $str;
        $str = str_replace("{{mcc_host}}", DB_HOST, $str);
        $str = str_replace("{{mcc_user}}", $setup->db_username, $str);
        $str = str_replace("{{mcc_pwd}}", base64_decode(base64_decode($setup->db_password)), $str);

        $str = str_replace("{{mcc_db}}", $setup->db_name, $str);
        $str = str_replace("{{analytical_db}}", $setup->analytic_db, $str);
        $str = str_replace("{{grader_db}}", $setup->grader_db, $str);

        /* Main MCC DB string replaces */

        /* OTHER String replaces */

        $str = str_replace("{{admin_name}}", $setup->name, $str);
        $str = str_replace("{{setting_cron_key}}", md5($setup->db_name), $str);
        $str = str_replace("{{analytical_url}}", 'http://' . $setup->analytic_url, $str);
        $sitename = 'http://' . $setup->url;
        if ($setup->white_lbl != '')
            $sitename = $setup->white_lbl;

        $str = str_replace("{{site_name}}", $sitename, $str);
        $str = str_replace("{{blog_name}}", ucfirst($setup->prefix), $str);
        $str = str_replace("{{no_replay_email}}", $setup->email, $str);
        $str = str_replace("{{training_team_name}}", "Training Team at " . $sitename, $str);
        $str = str_replace("{{announcements_url}}", $sitename . "/announcement", $str);
        $str = str_replace("{{parent_url}}", admin_url('admin-ajax.php'), $str);

        /* Other string replaces
          file_put_contents($dest_file, $str);
          $message = PHP_EOL."<?php define('DISALLOW_FILE_EDIT', true); ?>".PHP_EOL;
          file_put_contents($dest_file, $message, FILE_APPEND); */

        json(1, 'Configuration file successfully reset.');
    }
    else if ($_REQUEST["param"] == "add_plugins") {
        $key = isset($_POST['key']) ? $_POST['key'] : '';
        if ($key == ST_KEY) {
            //@mail("parambir.rudra@gmail.com","Added plugins", json_encode($_POST));
            $setup_id = isset($_POST['setup_id']) ? intval($_POST['setup_id']) : '';
            $setup = $wpdb->get_row
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                    )
            );
            //@mail("parambir.rudra@gmail.com","Setup Row", json_encode($setup));
            if (!empty($setup)) {
                $servername = DB_HOST;
                $db_name = $setup->db_name;
                $db_user = $setup->db_username;
                $db_password = base64_decode(base64_decode($setup->db_password));
                $conn = new mysqli($servername, $db_user, $db_password, $db_name);
                if ($conn->connect_error) {
                    wp_mail('parambir@rudrainnovatives.com', 'Error MySqli', $conn->connect_error . ' :  Password is: ' . $db_password);
                    die;
                }
                //@mail("parambir.rudra@gmail.com","Setup Row", json_encode($conn));
                /*                 * ************************** Code to add plugins for client site  ****************************** */
                // setup account setting plugin on client sub domain
                $src = ST_COUNT_PLUGIN_DIR . '/dbs/settings';
                $dest = $setup->dir . '/wp-content/plugins/settings';
                rcopyonly($src, $dest);

                // delete pdf default logo
                $pdflogopath = $setup->dir . "/wp-content/plugins/settings/uploads/pdf_logo.jpg";
                @unlink($pdflogopath);

                // setup account setting plugin on client sub domain

                /* Start activation */
                $tokens = "super_tokens";
                $sql = "SHOW TABLES LIKE '" . $tokens . "'";
                $result = mysqli_query($conn, $sql);
                $row = $result->fetch_array(MYSQLI_NUM);
                if (empty($row)) {
                    $sql = "CREATE TABLE " . $tokens . " ( "
                            . "id int(100) primary key auto_increment,"
                            . "token varchar(500) not null,"
                            . "created_dt TIMESTAMP NOT NULL"
                            . " )";
                    mysqli_query($conn, $sql);
                }

                $token = md5($setup->id . time() . $setup->url);
                $sql = "INSERT INTO " . $tokens . " (token) VALUES('$token')";
                mysqli_query($conn, $sql);
                $plugin = $setup->dir . '/wp-content/plugins/settings/settings.php';
                $url = $setup->url . '/mcc_login.php';
                $params = array('param' => 'activate_plugin', 'token' => $token, 'plugin' => $plugin);
                api_call_child($url, $params);
                /* end activation */

                /*                 * ************************** Code to add plugins for client site  ****************************** */

                /*
                  if(ST_ENABLE_CRONS == 1){

                  include_once ST_COUNT_PLUGIN_DIR.'/library/cron_class.php';
                  $obj = new Crontab();

                  $cronlist = ABSPATH.'/cron_list.php';
                  $data = file_get_contents($cronlist);
                  $data = str_replace("{{base_dir}}", $setup->dir, $data);
                  $data = str_replace("{{analytics_dir}}", $setup->analytic_dir, $data);
                  $data = str_replace("{{analytic_url}}", $setup->analytic_url, $data);
                  $url = 'http://'.$setup->url;
                  $data = str_replace("{{url}}", $url, $data);
                  $data = str_replace("{{key}}", md5($setup->db_name), $data);
                  $jobs = explode("~", $data);
                  foreach($jobs as $job){
                  if($obj->doesJobExist($job) == FALSE){
                  $obj->addJob($job);
                  }
                  }
                  }
                 */

                /* Code to add Crons */
                //Code starts byy rudra dated:14 jan-2017
                $getagency_created = date("Y-m-d H:i:s");
                $select_crons = $wpdb->get_results("SELECT cc.agency_id,cc.cron_link,cc.cron_type,ts.cron_time FROM `client_crons` as cc JOIN `master_crons` as ts  ON cc.cron_type=ts.cron_type WHERE cc.agency_id=" . $setup->id);
                $loop_select_crons = $select_crons;
                $setup_crons = count($select_crons);
                if ($setup_crons == 0) {
                    $currentdate = $getagency_created;

                    $url = PROTOCOL . $setup->url;
                    if (trim($setup->white_lbl) != '') {
                        $url = $setup->white_lbl;
                    }
                    // default Crons from table not from file
                    $datas = $wpdb->get_results($wpdb->prepare("SELECT default_cron,cron_time,cron_type FROM master_crons where status=1", ""));   //master "cron time" table
                    foreach ($datas as $key => $data) {
                        $data->default_cron = str_replace("{{base_dir}}", $setup->dir, $data->default_cron);
                        $data->default_cron = str_replace("{{analytics_dir}}", $setup->analytic_dir, $data->default_cron);
                        $data->default_cron = str_replace("{{analytic_url}}", PROTOCOL . $setup->analytic_url, $data->default_cron);
                        $data->default_cron = str_replace("{{url}}", $url, $data->default_cron);
                        $data->default_cron = str_replace("{{key}}", md5($setup->db_name), $data->default_cron);
                        $datajob[$data->cron_type] = $data;
                    }
                    foreach ($datajob as $key_val => $job) {
                        $insert_crons = $wpdb->query("INSERT INTO `client_crons`(`agency_id`, `cron_link`, `cron_type`,`order_by`, `created`) VALUES ('" . $setup->id . "','" . $job->default_cron . "','" . $job->cron_type . "','" . $setup->id . "','" . $currentdate . "')");
                    }
                }
                //Code ends by rudra dated:14 jan-2017
                /* Code to add Crons */

                $ana_conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $setup->analytic_db);
                if ($ana_conn->connect_error) {
                    wp_mail('parambir@rudrainnovatives.com', 'Error MySqli', $ana_conn->connect_error . ' :  analytic_db is not connecting.');
                    return;
                }

                if (ST_USERS_DELETE == 1) {
                    /*                     * ************************** Code add a row for admin in agency analytics - clients table ****************************** */

                    $Name = mysqli_real_escape_string($conn, $setup->name);
                    $Domain = mysqli_real_escape_string($conn, $setup->url);
                    $Email = mysqli_real_escape_string($conn, $setup->email);

                    // clients added when admin add new location
                    $sql = "INSERT INTO clients_table(MCCUserID,Name,Domain,Email,CreatedDate) VALUES(1,'$Name','$Domain','$Email',NOW())";
                    mysqli_query($ana_conn, $sql);

                    /*                     * ************************** Code add a row for admin in agency analytics - clients table ****************************** */
                } else {

                    /* copy whole table to agency analytics client table */

                    $sql = "INSERT INTO " . $setup->analytic_db . ".clients_table SELECT * FROM " . database_name . ".clients_table ";
                    mysqli_query($ana_conn, $sql);

                    /* copy whole table to agency analytics client table */
                }

                if (ST_ENABLE_SEO_TBLS == 1) {

                    // SEO data added
                    $sql = "INSERT INTO " . $setup->analytic_db . ".seo SELECT * FROM " . database_name . ".seo ";
                    mysqli_query($ana_conn, $sql);

                    $sql = "INSERT INTO " . $setup->analytic_db . ".seo_history SELECT * FROM " . database_name . ".seo_history ";
                    mysqli_query($ana_conn, $sql);


                    // competitor Report data added
                    $sql = "INSERT INTO " . $setup->analytic_db . ".competitor_report SELECT * FROM " . database_name . ".competitor_report ";
                    mysqli_query($ana_conn, $sql);

                    $sql = "INSERT INTO " . $setup->analytic_db . ".competitor_report_history SELECT * FROM " . database_name . ".competitor_report_history ";
                    mysqli_query($ana_conn, $sql);
                }

                $dbhost = DB_HOST;
                $dbuser = DB_USER;
                $dbpass = DB_PASSWORD;
                $file = ST_COUNT_PLUGIN_DIR . "/dbs/index_script.sql";
                $command_str_imp = "mysql -u " . $dbuser . " -p" . $dbpass . " -h " . $dbhost . " " . $setup->analytic_db . " < " . $file;
                system($command_str_imp);


                /* command to create short_analytics table */
                $command = "wget http://" . $setup->url . "/cron/short_analytics.php";
                system($command);
                /* command to create short_analytics table */

                /*                 * *** Code to add analytic setup **** */

                $src = ST_ANALYTIC_DIR;
                $dir = $setup->analytic_dir;
                $client_db = $setup->db_name;
                $user = $setup->db_username;
                $pass = $setup->db_password;
                $analytic_db = $setup->analytic_db;
                $igonre_folders = array(ST_ANALYTIC_DIR . '/app', ST_ANALYTIC_DIR . '/public', ST_ANALYTIC_DIR . '/vendor');

                rcopy($src, $dir, $client_db, $user, $pass, $analytic_db, $igonre_folders, $setup);

                /*                 * *** Code to add analytic setup **** */


                update_option('client_setup_' . $setup_id, "1");

                /*                 * * Delete sql files, that made during setup ** */
                include_once ABSPATH . '/global_config.php';
                @unlink(ST_COUNT_PLUGIN_DIR . "/dbs/" . database_name_wp . '.sql');
                @unlink(ST_COUNT_PLUGIN_DIR . "/dbs/" . database_name . '.sql');
                @unlink(ST_COUNT_PLUGIN_DIR . "/dbs/" . database_name_grader . '.sql');
                $ins_db = 'insert_' . database_name_wp . '.sql';
                @unlink(ST_COUNT_PLUGIN_DIR . "/dbs/" . $ins_db);
                /*                 * * Delete sql files, that made during setup ** */
            }
        }
    } else if ($_REQUEST["param"] == "setup_completed") {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $setup_id = isset($_POST['setup_id']) ? intval($_POST['setup_id']) : 0;
        $option = 'client_setup_' . $setup_id;
        if (get_option($option) == 1) {
            $prog = "100";
            $iscomplete = 1;
            unset($_SESSION['setupcompleted']);
        } else {
            $iscomplete = 0;
            $per = $_SESSION['setupcompleted'];
            if ($per == '' || $per <= 0) {
                $per = 60;
            }

            if ($per >= 95) {
                $tot = $per;
            } else {
                $tot = $per + 5;
            }
            $_SESSION['setupcompleted'] = $tot;
            $prog = $tot;
        }
        json(1, $iscomplete, $prog);
    } else if ($_REQUEST["param"] == "dbconfigclient") {

        $key = isset($_POST['key']) ? $_POST['key'] : '';
        if ($key == ST_KEY) {

            $setup_id = isset($_POST['setup_id']) ? intval($_POST['setup_id']) : '';
            $setup = $wpdb->get_row
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                    )
            );

            if (!empty($setup)) {

                $servername = DB_HOST;
                $db_name = $setup->db_name;
                $db_user = $setup->db_username;
                $db_password = base64_decode(base64_decode($setup->db_password));
                $conn = new mysqli($servername, $db_user, $db_password, $db_name);
                if ($conn->connect_error) {
                    wp_mail('parambir@rudrainnovatives.com', 'Error MySqli', $conn->connect_error . ' :  Password for DB ' . $db_name . ' is: ' . $db_password);
                    die;
                }

                $igonre_posts = isset($_POST['igonre_posts']) ? trim(htmlspecialchars($_POST['igonre_posts'])) : '';
                if ($igonre_posts != '') {
                    $igonre_posts = json_decode(stripcslashes(html_entity_decode($igonre_posts)));
                }
                $igonre_pages = isset($_POST['igonre_pages']) ? trim(htmlspecialchars($_POST['igonre_pages'])) : '';

                if ($igonre_pages != '') {
                    $igonre_pages = json_decode(stripcslashes(html_entity_decode($igonre_pages)));
                }

                if (!empty($igonre_posts)) {
                    // delete all posts
                    if ($igonre_posts[0] == 'all') {
                        $i = 0;
                        foreach ($igonre_posts as $igonre_post) {
                            if ($i == 0) {
                                $sql_all_posts = "delete p,pm from wp_posts p join wp_postmeta pm on pm.post_id = p.id WHERE p.post_type = 'post'";
                                mysqli_query($conn, $sql_all_posts);
                            } else {
                                $sql_all_posts = "delete p,pm from wp_posts p join wp_postmeta pm on pm.post_id = p.id WHERE p.post_type = '$igonre_post'";
                                mysqli_query($conn, $sql_all_posts);
                            }
                            $i++;
                        }
                    } else {
                        // delete selected posts
                        foreach ($igonre_posts as $igonre_post) {
                            $igonre_post = mysqli_real_escape_string($conn, $igonre_post);
                            $sql_post = "delete p,pm from wp_posts p join wp_postmeta pm on pm.post_id = p.id WHERE p.post_name = '$igonre_post'";
                            mysqli_query($conn, $sql_post);
                        }
                    }
                }

                setting_and_email_templates($conn);


                if (!empty($igonre_pages)) {
                    // delete all pages
                    if ($igonre_pages[0] == 'all') {
                        $sql_all_pages = "delete p,pm from wp_posts p join wp_postmeta pm on pm.post_id = p.id WHERE p.post_type = 'page'";
                        mysqli_query($conn, $sql_all_pages);
                    } else {
                        // delete selected pages
                        foreach ($igonre_pages as $igonre_page) {
                            $igonre_page = mysqli_real_escape_string($conn, $igonre_page);
                            $sql_page = "delete p,pm from wp_posts p join wp_postmeta pm on pm.post_id = p.id WHERE p.post_name = '$igonre_page'";
                            mysqli_query($conn, $sql_page);
                        }
                    }
                }

                if (ST_USERS_DELETE == 1) {
                    // iF ST_USERS_DELETE parameter is 1, means all users deleted, except admin, that created during agency setup.
                    // enfusen staff with admin
                    $enfusen_staff = "'admin','enfusenroger@gmail.com','adaily@enfusen.com','rogercbryan@enfusen.com','kstarta@enfusen.com'";

                    $sql = "DELETE FROM wp_users WHERE user_login NOT IN($enfusen_staff)";
                    mysqli_query($conn, $sql);

//                        $sql = "DELETE FROM wp_usermeta WHERE (user_id != 1) OR (user_id = 1 AND meta_key='total_time') "
//                                . "OR (user_id = 1 AND meta_key='logged_in_amount') OR (user_id = 1 AND meta_key='average_time')";

                    $sql = "DELETE FROM wp_usermeta WHERE user_id NOT IN(SELECT ID FROM wp_users WHERE user_login IN($enfusen_staff))";
                    mysqli_query($conn, $sql);
                }

                $login_id = mysqli_real_escape_string($conn, $setup->login);
                $password = mysqli_real_escape_string($conn, base64_decode(base64_decode($setup->password)));

                $password = md5($password);
                $name = mysqli_real_escape_string($conn, $setup->name);
                $email = mysqli_real_escape_string($conn, $setup->email);
                $now = date("Y-m-d H:i:s");


                $nm = explode(" ", $setup->name);
                $first_name = $nm[0];
                $last_name = isset($nm[1]) ? trim($nm[1]) : '';

                $sql = "UPDATE wp_users SET user_login = '$login_id', user_pass = '$password', user_nicename = '$name', "
                        . "display_name = '$name', user_email = '$email', user_url = '', user_registered = '$now' WHERE ID = 1";
                mysqli_query($conn, $sql);

                $sql = "UPDATE wp_usermeta SET meta_value = '$first_name' WHERE user_id = 1 AND meta_key = 'first_name'";
                mysqli_query($conn, $sql);

                $sql = "UPDATE wp_usermeta SET meta_value = '$last_name' WHERE user_id = 1 AND meta_key = 'last_name'";
                mysqli_query($conn, $sql);

                $sql = "UPDATE wp_usermeta SET meta_value = '" . $setup->prefix . "' WHERE user_id = 1 AND meta_key = 'nickname'";
                mysqli_query($conn, $sql);

                // Change Admin Email in wp option table
                $sql = "UPDATE wp_options SET option_value = '" . $email . "' WHERE option_name = 'admin_email'";
                mysqli_query($conn, $sql);

                $sql = "UPDATE wp_options SET option_value = '" . $setup->name . "' WHERE option_name = 'blogname'";
                mysqli_query($conn, $sql);

                $sql = "UPDATE wp_options SET option_value = 'Reports of " . $setup->name . "' WHERE option_name = 'blogdescription'";
                mysqli_query($conn, $sql);

                // delete info of admin user
                $sql = "DELETE FROM wp_usermeta WHERE user_id = 1 AND meta_key IN ('streetaddress', 'phonenumber', 'city', 'state', 'zip', 'country', 'birth_date', 'occupation')";
                mysqli_query($conn, $sql);

                $conn->close();
            }
        }
    } else if ($_REQUEST["param"] == "analytics_db_setup") {

        $key = isset($_POST['key']) ? $_POST['key'] : '';
        if ($key == ST_KEY) {
            $analytics_table = isset($_POST['analytics_table']) ? $_POST['analytics_table'] : '';
            $setup_id = isset($_POST['setup_id']) ? $_POST['setup_id'] : '';
            $setup_update = isset($_POST['setup_update']) ? $_POST['setup_update'] : '';
            $setup = $wpdb->get_row
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                    )
            );
            if (!empty($setup)) {
                $analytic_db = $setup->analytic_db;
                $grader_db = $setup->grader_db;

                $user = $setup->db_username;

                $dbhost = DB_HOST;
                $dbuser = DB_USER;
                $dbpass = DB_PASSWORD;

                if ($setup_update == 1) {
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "DROP DATABASE " . $analytic_db, ""
                            )
                    );
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "DROP DATABASE " . $grader_db, ""
                            )
                    );
                }


                /*                 * *** Analytic DB installtion **** */

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "CREATE DATABASE $analytic_db", ""
                        )
                );

                $backup_file = ST_COUNT_PLUGIN_DIR . '/dbs/' . ST_ANALYTIC_DB . '.sql';
                if (file_exists($backup_file)) {
                    @unlink($backup_file);
                }


                $command_str = "mysqldump --opt -h $dbhost -u $dbuser -p" . $dbpass . " --no-data " . ST_ANALYTIC_DB . " " . $analytics_table . " > " . $backup_file;
                system($command_str);

                $command_str_imp = "mysql -u " . $dbuser . " -p" . $dbpass . " -h " . $dbhost . " " . $analytic_db . " < " . $backup_file;
                system($command_str_imp);

                $userhost = $dbhost;
                // Special condition for RDS mysql server
                if ($_SERVER['SERVER_ADDR'] == '172.31.41.131' || $_SERVER['SERVER_ADDR'] == '172.31.45.58' || $_SERVER['SERVER_ADDR'] == '172.31.32.200' || $_SERVER['SERVER_ADDR'] == '172.31.14.184' || $_SERVER['SERVER_ADDR'] == '172.31.13.53') {
                    $userhost = $_SERVER['SERVER_ADDR'];
                }

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "GRANT ALL PRIVILEGES ON " . $analytic_db . ".* TO '$user'@'$userhost'", ""
                        )
                );

                /*                 * *** Analytic DB installtion **** */

                /*                 * *** Grader DB installtion **** */
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "CREATE DATABASE $grader_db", ""
                        )
                );

                $backup_file = ST_COUNT_PLUGIN_DIR . '/dbs/' . ST_GRADER_DB . '.sql';
                if (file_exists($backup_file)) {
                    @unlink($backup_file);
                }

                $command_str = "mysqldump --opt -h $dbhost -u $dbuser -p" . $dbpass . " --no-data " . ST_GRADER_DB . " > " . $backup_file;
                system($command_str);

                $command_str_imp = "mysql -u " . $dbuser . " -p" . $dbpass . " -h " . $dbhost . " " . $grader_db . " < " . $backup_file;
                system($command_str_imp);

                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "GRANT ALL PRIVILEGES ON " . $grader_db . ".* TO '$user'@'$userhost'", ""
                        )
                );
                /*                 * *** Grader DB installtion **** */
            }
        }
    } else if ($_REQUEST["param"] == "setup_status") {

        $setup_id = isset($_POST['setup_id']) ? intval($_POST['setup_id']) : '';
        $setup = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                )
        );
        if (empty($setup)) {
            json(0, 'Invalid Setup ID');
        }

        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        if ($setup->status == $status) {
            if ($status == 1)
                json(0, 'Status already enabled');
            else
                json(0, 'Status already disabled');
        }

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . setup_table() . " SET status = %d WHERE id = %d", $status, $setup_id
                )
        );
        // Code Starts by Rudra
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . client_crons . " SET status = %d WHERE agency_id = %d", $status, $setup_id
                )
        );
        // Code Ends by Rudra
        $bkp = $setup->dir . "/__bkp_index.php";
        $dir = $setup->dir . "/index.php";

        $htaccess = $setup->dir . "/.htaccess";

        if ($status == 1) {

            // htaccess to enable wp-admin
            $htaccss = '# BEGIN WordPress
                        <IfModule mod_rewrite.c>
                        RewriteEngine On
                        RewriteBase /
                        RewriteRule ^index\.php$ - [L]
                        RewriteCond %{REQUEST_FILENAME} !-f
                        RewriteCond %{REQUEST_FILENAME} !-d
                        RewriteRule . index.php [L]
                        </IfModule>
                        # END WordPress';


            /* Frontend */
            unlink($dir);
            $str = file_get_contents($bkp);
            file_put_contents($dir, $str);
            /* Frontend */

            /* htaccess write */
            file_put_contents($htaccess, $htaccss);

            // email for client- setup is enabled
            setup_status(1, $setup);
            json(1, 'Setup enabled successfully');
        } else {

            // htaccess to disable wp-admin
            $site_url = PROTOCOL . $setup->url;
            $htaccss = '# BEGIN WordPress
                        <IfModule mod_rewrite.c>
                        RewriteEngine On
                        RewriteBase /
                        RewriteRule ^index\.php$ - [L]
                        RewriteCond %{REQUEST_FILENAME} !-f
                        RewriteCond %{REQUEST_FILENAME} !-d
                        RewriteRule . index.php [L]
                        </IfModule>
                        Redirect 301  /wp-admin  ' . $site_url . '
                        # END WordPress';

            $str = "This setup is currently disabled. Please contact to administrator.";

            /* Frontend */
            $strbkp = file_get_contents($dir);
            file_put_contents($bkp, $strbkp);
            file_put_contents($dir, $str);
            /* Frontend */

            /* htaccess write */
            file_put_contents($htaccess, $htaccss);

            // email for client- setup is disabled
            setup_status(0, $setup);
            json(1, 'Setup disabled successfully');
        }

        $conn->close();
    } else if ($_REQUEST["param"] == "callfunction") {
        $status = $_POST['data_status'];
        $setup_id = $_POST['data_id'];
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE client_crons SET status = %d WHERE agency_id = %d", $status, $setup_id
                )
        );
        if ($status == 0) {
            json(1, 'Cron Job Disabled Successfully.');
        } else {
            json(1, 'Cron Job Enabled Successfully.');
        }
    } else if ($_REQUEST["param"] == "setup_delete") {

        $setup_id = isset($_POST['setup_id']) ? intval($_POST['setup_id']) : '';
        $setup = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
                )
        );
        if (empty($setup)) {
            json(0, 'Invalid Setup ID');
        }

        $dir = $setup->dir;
        $analytic_dir = $setup->analytic_dir;
        $db_host = DB_HOST;
        $db_name = $setup->db_name;
        $db_user = $setup->db_username;
        $analytic_db = $setup->analytic_db;
        $grader_db = $setup->grader_db;
        prevent_medstar($db_name);

        $userhost = $dbhost;
        // Special condition for RDS mysql server
        if ($_SERVER['SERVER_ADDR'] == '172.31.41.131' || $_SERVER['SERVER_ADDR'] == '172.31.45.58' || $_SERVER['SERVER_ADDR'] == '172.31.32.200' || $_SERVER['SERVER_ADDR'] == '172.31.14.184' || $_SERVER['SERVER_ADDR'] == '172.31.13.53') {
            $userhost = $_SERVER['SERVER_ADDR'];
        }


        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DROP database " . $analytic_db, ""
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DROP database " . $grader_db, ""
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DROP database " . $db_name, ""
                )
        );

        rrmdir($dir);
        rrmdir($analytic_dir);
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM " . setup_table() . " WHERE id = %d", $setup_id
                )
        );


        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM mysql.db WHERE User = " . $db_user, ""
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM mysql.user WHERE User = " . $db_user, ""
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "FLUSH PRIVILEGES", ""
                )
        );

        include_once ST_COUNT_PLUGIN_DIR . '/library/cron_class.php';
        $obj = new Crontab();

        $cronlist = ABSPATH . '/cron_list.php';
        $data = file_get_contents($cronlist);
        $data = str_replace("{{base_dir}}", $setup->dir, $data);
        $data = str_replace("{{analytics_dir}}", $setup->analytic_dir, $data);
        $data = str_replace("{{analytic_url}}", $setup->analytic_url, $data);
        $url = 'http://' . $setup->url;
        $data = str_replace("{{url}}", $url, $data);
        $jobs = explode("~", $data);
        foreach ($jobs as $job) {
            $job = trim($job);
            if ($obj->doesJobExist($job) == TRUE) {
                $obj->removeJob($job);
            }
        }
        //Code starts by Rudra 14-jan-2017
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DELETE FROM client_crons WHERE agency_id = " . $setup_id, ""
                )
        );
        //Code ends by Rudra
        json(1, 'Setup deleted successfully', $res);
    } else if ($_REQUEST["param"] == "htaccess_recreate") {

        $clients = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setup_table() . " WHERE white_lbl != ''", ""
                )
        );
        $root_dir = ST_DIR;
        $ht = 'Options -Indexes ' . PHP_EOL;
        $ht .= PHP_EOL . '<IfModule mod_rewrite.c>' . PHP_EOL;
        $ht .= 'RewriteEngine On' . PHP_EOL;
        $ht .= 'RewriteBase / ' . PHP_EOL;

        foreach ($clients as $client) {
            $whiteurl = trim(str_replace(array("http://", "https://"), array("", ""), $client->white_lbl));
            $whiteurl = preg_replace('{/$}', '', $whiteurl);
            $ur = explode(".", $whiteurl);
            if (!empty($ur)) {
                $pre = $client->prefix;
                $strr = '';
                foreach ($ur as $u) {
                    $strr .= $u . '\.';
                }
                $strr = substr($strr, 0, -2);
                $ht .= PHP_EOL . 'RewriteCond %{HTTP_HOST} ^(www\.)?' . $strr . '$' . PHP_EOL;
                $ht .= 'RewriteRule !^clients/' . $pre . '/ /clients/' . $pre . '%{REQUEST_URI} [L,NC]' . PHP_EOL;
            }
        }
        $root_dir = $root_dir . '/.htaccess';
        $ht .= PHP_EOL . '</IfModule>';
        file_put_contents($root_dir, $ht);
        json(1, 'Htaccess file re-created', $root_dir);
    }
}

function api_call_child($url, $params = array()) {

    foreach ($params as $key => &$val) {
        if (is_array($val))
            $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object))
                    rrmdir($dir . "/" . $object);
                else
                    unlink($dir . "/" . $object);
            }
        }
        rmdir($dir);
    }
}

function tool_setup_script($prefix, $dir, $url) {

    global $wpdb;
    if (!file_exists($dir)) {
        @mkdir($dir, 0777, true);
    } else {
        json(0, 'Url already exist.');
    }

    $content = "Welcome to new sub domain. <br/><b>$url</b> is running here.";
    $fp = fopen($dir . "/index.php", "wb");
    fwrite($fp, $content);
    fclose($fp);
}

/* $setup_update = 0 (new), 1 (re-build), 2 (update) */

function tool_setup_creation($setup, $setup_update) {

    $id = $setup->id;
    $prefix = $setup->prefix;
    $dir = $setup->dir;
    $url = $setup->url;

    global $wpdb;
    $dbname = DB_NAME;
    $dbhost = DB_HOST;
    $dbuser = DB_USER;
    $dbpass = DB_PASSWORD;

    $analytic_folder = $prefix . "analytics";
    $anlytics_dir = ST_DIR . "/" . $analytic_folder;
    $anlytics_url = $analytic_folder . "." . ST_DOMAIN;

//    $tables = $wpdb->get_results
//    (
//        $wpdb->prepare
//        (
//                "SHOW TABLES FROM ".DB_NAME,""
//        )
//    );

    $backup_file = ST_COUNT_PLUGIN_DIR . '/dbs/' . $dbname . '.sql';
    $insert_statements = ST_COUNT_PLUGIN_DIR . '/dbs/insert_' . $dbname . '.sql';
    if (file_exists($backup_file)) {
        @unlink($backup_file);
    }
    if (file_exists($backup_file)) {
        @unlink($insert_statements);
    }

    /* Filters - Remove functionality, that is not useable for client */
    $igonre_tables = array("wp_setup_table", "wp_client_company_info", "wp_setup_requests");
    $igonre_folders = array(ST_BASEDIR . "/wp-content/plugins/client-setup", ST_BASEDIR . "/wp-content/plugins/custom-setup-request",
        ST_BASEDIR . "/wp-content/plugins/settings", ST_BASEDIR . "/.git", ST_BASEDIR . "/wp-content/plugins/app-admin-requests",
        ST_BASEDIR . "/contentadmin", ST_BASEDIR . "/pdf", ST_BASEDIR . "/pma", ST_BASEDIR . "/nbproject",
        ST_BASEDIR . "/csv", ST_BASEDIR . "/client-tool", ST_BASEDIR . "/audit-reports", ST_BASEDIR . "/csv",
        ST_BASEDIR . "/wp-content/plugins/training-tool/assets/docs",
        ST_BASEDIR . "/wp-content/plugins/training-tool/assets/docs");


    $igonre_pages = array('mashed-rss-feed', 'buyer-persona', 'task-manager', 'task-overview', 'site-audit-url-report', 'site-audit-report',
        'predictive-report', 'content-report', 'order-content', 'assessment', 'client-control-center', 'recommendation-settings',
        'to-do-list', 'billing', 'email-subscription', 'not-assigned-task', 'task-manager-pdf', 'sales-report-new', 'custom-agency-setup');

    $igonre_pages = json_encode($igonre_pages);
    $igonre_posts = array('all', 'leadpages_post', 'kbe_knowledgebase', 'credibility', 'wpi_object'); // all for all posts
    $igonre_posts = json_encode($igonre_posts);

    /** Select Table from enfusen table * */
    //$table_data_req = 'categories dap_aff_earnings wp_country wp_country_state dma_regions grader_users wp_all_email_historical_report wp_all_email_report wp_all_email_subscription wp_ap_appointments wp_ap_events wp_ap_service_category wp_ap_services wp_appointgen_schedules wp_appointgen_services wp_appointgen_timeslot wp_appointgen_ustsappointments wp_appointgen_ustsappointments_paymentmethods wp_appointgen_venues wp_appointy_calendar wp_aryo_activity_log wp_assessment_report_info wp_audit_fields wp_blast_url wp_booking wp_bookingdates wp_calp_event_category_colors wp_calp_event_feeds wp_calp_event_instances wp_calp_events wp_citation_le_post wp_citation_list wp_citation_tracker wp_commentmeta wp_comments wp_content_order wp_countries wp_courses wp_dc_mv_calendars wp_dc_mv_configuration wp_dc_mv_events wp_dc_mv_views wp_email_history wp_email_templates wp_en_announcements wp_enrollment wp_eo_events wp_eo_venuemeta wp_expertise wp_extrawatch wp_extrawatch_blocked wp_extrawatch_cache wp_extrawatch_cc2c wp_extrawatch_config wp_extrawatch_dm_counter wp_extrawatch_dm_extension wp_extrawatch_dm_paths wp_extrawatch_dm_referrer wp_extrawatch_flow wp_extrawatch_goals wp_extrawatch_heatmap wp_extrawatch_history wp_extrawatch_info wp_extrawatch_internal wp_extrawatch_ip2c_cache wp_extrawatch_keyphrase wp_extrawatch_sql_scripts wp_extrawatch_uri wp_extrawatch_uri2keyphrase wp_extrawatch_uri2keyphrase_pos wp_extrawatch_uri2title wp_extrawatch_uri_history wp_extrawatch_uri_post wp_extrawatch_user_log wp_extrawatch_visit2goal wp_feedback wp_gen_ustsbooking wp_gen_ustsbooking_paymentmethods wp_iframe_unique wp_invoice_email_track wp_invoice_visit_track wp_job_title wp_keywords_update_history wp_kpi_tracker wp_kpi_tracker_group wp_layerslider wp_lbakut_user_stats wp_lesson_notes wp_lessons wp_links wp_mcc_sch_emails wp_mcc_sch_history wp_mcc_sch_settings wp_media wp_mentor_assign wp_mentorcall wp_menu_control wp_message_setup wp_mlw_qm_audit_trail wp_mlw_questions wp_mlw_quizzes wp_mlw_results wp_mlw_results_old wp_modules wp_notification wp_old_content wp_options wp_posted_content_le_setup wp_postmeta wp_posts wp_project_exercise wp_projects wp_ranking_report wp_rankreport_data wp_recurring_task wp_resource_list wp_resource_status wp_resources wp_resources_comments wp_resources_order wp_sales_notes wp_seo_nitro wp_seo_nitro_removed wp_setting wp_simple_history_contexts wp_simple_login_log wp_site_audit wp_site_audit_error_page_list_old_no_need wp_site_audit_page_notes wp_site_audit_tracker wp_site_audit_tracker_group wp_social_adr wp_social_adr_twitter wp_stms_access_log wp_stms_aff_clicks wp_stms_aff_commissions wp_stms_attachments wp_stms_broadcasts wp_stms_carts wp_stms_coupons wp_stms_discounts wp_stms_drip_access wp_stms_email_queue wp_stms_error_log wp_stms_ipn_log wp_stms_license_log wp_stms_licenses wp_stms_members wp_stms_product_access wp_stms_products wp_stms_protects wp_stms_replies wp_stms_third_items wp_stms_tickets wp_stms_transactions wp_survey_forms wp_survey_results wp_tag_list wp_task_comment wp_task_list wp_task_table wp_task_user wp_term_relationships wp_term_taxonomy wp_terms wp_training_video_control wp_user_grader wp_user_group wp_user_notes wp_usermeta wp_users wp_usts_currency_list wp_worker_role wp_wpdevart_calendars wp_wpdevart_dates wp_wpdevart_extras wp_wpdevart_forms wp_wpdevart_reservations wp_wpdevart_themes wp_wpi_object_log';
    $ignoretbls = array("wp_client_company_info", "wp_setup_table", "wp_location_mapping", "wp_client_location",
        "wp_citation_setup_instruction", "wp_site_audit_instruction", "wp_location_package_fields", "wp_add_locations_status",
        "wp_location_extraDataConsume", "wp_pay_for_locations", "wp_billingdiscount", "wp_addons_purchase", "wp_custom", "wp_setup_requests");

    $table_data_req = '--ignore-table=' . $dbname . '.wp_client_company_info --ignore-table=' . $dbname . '.wp_setup_table';
    $table_data_req = '';
    foreach ($ignoretbls as $ignoretbll) {
        $table_data_req .= ' --ignore-table=' . $dbname . '.' . $ignoretbll;
    }

    /** Select Table from enfusen table * */
    /* Filters - Remove functionality, that is not useable for client */

    $analytics_table = 'access_tokens all_conversions_data all_keywords api-hubspot api_contact_fields api_data api_fields api_field_score '
            . 'api_group api_hubspot_contacts api_hubspot_contact_fields api_hubspot_contact_list api_hubspot_lists api_hubspot_list_fields '
            . 'api_hubspot_list_score api_hubspot_tables api_hubspot_user_tables api_lists api_list_lists api_mapping api_report audit_csv '
            . 'buffer_content clients clients_table client_domain_reports competitor_report competitor_report_history contact_pipe_values '
            . 'ea_url_list global_conversion_urls global_landing_urls keyword_alerts onsite_content predictive_records primary_keywords rank_values '
            . 'recommendations recommendation_email_types recommendation_notes recommendation_settings recommend_content recommend_email '
            . 'reports_list reverse_seo seo seorv_formula seo_history seo_rank_change temporary_keywords_list_for_btl_rank unit_ordering '
            . 'update_report users';

    //  // Structure only
    //  $table_data_req = substr($table_data_req,0,-1);

    $command_str = "mysqldump -f  --opt -h $dbhost -u $dbuser -p" . $dbpass . " --no-data " . $dbname . " " . $table_data_req . " > " . $backup_file;
    system($command_str);
    // Records for only selected tables

    $insert_reordstbl = 'wp_users wp_usermeta wp_options wp_posts wp_postmeta wp_countries wp_country wp_country_state categories wp_expertise wp_job_title wp_usa_state_list wp_usts_currency_list wp_all_email_report';
    $command_data = "mysqldump -f --opt -h $dbhost -u $dbuser -p" . $dbpass . " --no-create-info " . $dbname . " $insert_reordstbl > $insert_statements" . ";";
    system($command_data);

    /* replace DB site url */
    $site_url = site_url();
    $site_url = str_replace(array('http://', 'https://'), array('', ''), $site_url);
    $str = file_get_contents($insert_statements);
    $str = str_replace("$site_url", "$url", $str);
    file_put_contents($insert_statements, $str);
    /* replcae DB site url */

    $pref = str_replace("-", "", $prefix);
    $dbprefix = $pref;
    if (strlen($dbprefix) > 12) {
        $dbprefix = substr($dbprefix, 0, 12);
    }

    $client_db = 'mcc_' . $dbprefix;
    $analytic_db = 'analytic_' . $dbprefix;
    $grader_db = 'grader_' . $dbprefix;
    $user = $client_db;

    $userhost = $dbhost;
    // Special condition for RDS mysql server
    if ($_SERVER['SERVER_ADDR'] == '172.31.41.131' || $_SERVER['SERVER_ADDR'] == '172.31.45.58' || $_SERVER['SERVER_ADDR'] == '172.31.32.200' || $_SERVER['SERVER_ADDR'] == '172.31.14.184' || $_SERVER['SERVER_ADDR'] == '172.31.13.53') {
        $userhost = $_SERVER['SERVER_ADDR'];
    }

    if ($setup_update == 0) {

        $pass = rand(999999, 9999999999);
        $password = base64_encode(base64_encode($pass));

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . setup_table() . " SET db_name = %s, analytic_db = %s, grader_db = %s, db_username = %s,"
                        . " db_password = %s, analytic_url = %s, analytic_dir = %s WHERE id = %d", $client_db, $analytic_db, $grader_db, $user, $password, $anlytics_url, $anlytics_dir, $id
                )
        );

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "CREATE USER " . $user . "@" . $userhost . " IDENTIFIED BY '" . $pass . "'", ""
                )
        );
    } else {

        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . setup_table() . " SET white_lbl = '', db_name = %s, analytic_db = %s, grader_db = %s, "
                        . "analytic_url = %s, analytic_dir = %s WHERE id = %d", $client_db, $analytic_db, $grader_db, $anlytics_url, $anlytics_dir, $id
                )
        );

        $password = $setup->db_password;
        $pass = base64_decode(base64_decode($password));
    }

    // re-build setup
    if ($setup_update == 1) {
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "DROP DATABASE $client_db", ""
                )
        );
    }

    if ($setup_update == 0 || $setup_update == 1) {
        $params = array('param' => 'analytics_db_setup', 'analytics_table' => $analytics_table, 'setup_id' => $id,
            'setup_update' => $setup_update);
        asyn_post($params);
    }

    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "CREATE DATABASE $client_db", ""
            )
    );

    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "GRANT ALL PRIVILEGES ON " . $client_db . ".* TO '$user'@'$userhost'", ""
            )
    );

    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "FLUSH PRIVILEGES;", ""
            )
    );

//    if($per == FALSE){
//        json(0,'Database user has not enough permissions to manipulate scripts. Please allow root permissions.');
//    }
    // structre only
    $command_str_imp = "mysql -u " . $dbuser . " -p" . $dbpass . " -h " . $dbhost . " " . $client_db . " < " . $backup_file;
    system($command_str_imp);

    // Records for only selected tables
    $command_data_imp = "mysql -u " . $dbuser . " -p" . $dbpass . " -h " . $dbhost . " " . $client_db . " < " . $insert_statements;
    system($command_data_imp);

    // build or re-build
    if ($setup_update == 0 || $setup_update == 1) {
        /* change db of client */
        $params = array('param' => 'dbconfigclient', 'setup_id' => $setup->id, 'igonre_pages' => $igonre_pages,
            'igonre_posts' => $igonre_posts);
        asyn_post($params);
        /* change db of client */
    }
    if ($setup_update == 0) {
        setup_created($setup);
    }
    $_SESSION['setupcompleted'] = 30;
    $src = ST_SOURCE_SETUP;
    $setup = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup->id
            )
    );
    rcopy($src, $dir, $client_db, $user, $pass, $analytic_db, $igonre_folders, $setup);
    $_SESSION['setupcompleted'] = 60;
    @mail("parambir.rudra@gmail.com", "Call Add Plugins", 'setup_update : ' . $setup_update);
    if ($setup_update == 0 || $setup_update == 1) {
        $params = array('param' => 'add_plugins', 'setup_id' => $setup->id);
        asyn_post($params);
    }
}

function rcopy($src, $dest, $client_db, $user, $pass, $analytic_db, $filters, $setup) {

    $dbname = DB_NAME;
    $dbhost = DB_HOST;
    $dbuser = DB_USER;
    $dbpass = DB_PASSWORD;

    // If source is not a directory stop processing
    if (!is_dir($src))
        return false;

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            $file_name = $f->getFilename();
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($ext != 'zip' && $ext != 'tar' && $ext != 'rar' && $ext != 'xz' && $ext != 'tar.xz') {
                $dest_file = "$dest/" . $file_name;
                copy($f->getRealPath(), $dest_file);
                if ($file_name == 'global_config.php' || $file_name == 'analytics_config.php') {

                    /* Main MCC DB string replaces */

                    $str = file_get_contents(ST_COUNT_PLUGIN_DIR . '/dbs/global_config.php');
                    $str = str_replace("{{mcc_url}}", 'http://' . $setup->url, $str);
                    $str = str_replace("{{analytical_url}}", 'http://' . $setup->analytic_url, $str);
                    $str = str_replace("{{mcc_host}}", DB_HOST, $str);
                    $str = str_replace("{{mcc_user}}", $user, $str);
                    $str = str_replace("{{mcc_pwd}}", $pass, $str);

                    $str = str_replace("{{mcc_db}}", $client_db, $str);
                    $str = str_replace("{{analytical_db}}", $setup->analytic_db, $str);
                    $str = str_replace("{{grader_db}}", $setup->grader_db, $str);

                    /* Main MCC DB string replaces */

                    /* OTHER String replaces */

                    $str = str_replace("{{admin_name}}", $setup->name, $str);
                    $str = str_replace("{{setting_cron_key}}", md5($setup->db_name), $str);
                    $sitename = 'http://' . $setup->url;
                    if ($setup->white_lbl != '')
                        $sitename = $setup->white_lbl;

                    $str = str_replace("{{site_name}}", $sitename, $str);
                    $str = str_replace("{{blog_name}}", ucfirst($setup->prefix), $str);
                    $str = str_replace("{{no_replay_email}}", $setup->email, $str);
                    $str = str_replace("{{training_team_name}}", "Training Team at " . $sitename, $str);
                    $str = str_replace("{{announcements_url}}", $sitename . "/announcement", $str);
                    $str = str_replace("{{parent_url}}", admin_url('admin-ajax.php'), $str);
                    file_put_contents($dest_file, $str);

                    /* Other string replaces
                      $message = PHP_EOL."<?php define('DISALLOW_FILE_EDIT', true); ?>".PHP_EOL;
                      file_put_contents($dest_file, $message, FILE_APPEND); */
                }
                else if ($file_name == 'top-menu.php') {

                    $str = file_get_contents(ST_COUNT_PLUGIN_DIR . '/dbs/top_menu.php');
                    file_put_contents($dest_file, $str);
                } else if ($file_name == '.htaccess') {
                    $str = '# BEGIN WordPress
                            <IfModule mod_rewrite.c>
                            RewriteEngine On
                            RewriteBase /
                            RewriteRule ^index\.php$ - [L]
                            RewriteCond %{REQUEST_FILENAME} !-f
                            RewriteCond %{REQUEST_FILENAME} !-d
                            RewriteRule . index.php [L]
                            </IfModule>
                            # END WordPress';

                    file_put_contents($dest_file, $str);
                }
            }
        } else if (!$f->isDot() && $f->isDir()) {
            $file_path = $f->getRealPath();
            if (count($filters) > 0) {
                if (!in_array($file_path, $filters)) {
                    rcopy($file_path, "$dest/$f", $client_db, $user, $pass, $analytic_db, $filters, $setup);
                } else {
                    // create folder only, not to copy inner content
                    $oldmask = umask(0);
                    mkdir("$dest/$f", 0777);
                    umask($oldmask);
                }
            } else {
                rcopy($file_path, "$dest/$f", $client_db, $user, $pass, $analytic_db, $filters, $setup);
            }
        }
    }
}

function rcopyonly($src, $dest) {
    // If source is not a directory stop processing
    if (!is_dir($src))
        return false;

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            $file_name = $f->getFilename();
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($ext != 'zip' && $ext != 'tar' && $ext != 'rar' && $ext != 'xz' && $ext != 'tar.xz') {
                $dest_file = "$dest/" . $file_name;
                copy($f->getRealPath(), $dest_file);
            }
        } else if (!$f->isDot() && $f->isDir()) {
            $file_path = $f->getRealPath();
            if (count($filters) > 0) {
                if (!in_array($file_path, $filters)) {
                    rcopyonly($file_path, "$dest/$f");
                }
            } else {
                rcopyonly($file_path, "$dest/$f");
            }
        }
    }
}

function rcopyfilter($src, $dest, $filters) {

    // If source is not a directory stop processing
    if (!is_dir($src))
        return false;

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            $file_name = $f->getFilename();
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($ext != 'zip' && $ext != 'tar' && $ext != 'rar' && $ext != 'xz' && $ext != 'tar.xz') {
                $dest_file = "$dest/" . $file_name;
                copy($f->getRealPath(), $dest_file);
            }
        } else if (!$f->isDot() && $f->isDir()) {
            $file_path = $f->getRealPath();
            if (count($filters) > 0) {
                if (!in_array($file_path, $filters)) {
                    rcopyfilter($file_path, "$dest/$f", $filters);
                }
            } else {
                rcopyfilter($file_path, "$dest/$f", $filters);
            }
        }
    }
}

function asyn_post($params) {

    $email = trim($email);
    $subject = trim($subject);
    $body = trim($body);
    $key = ST_KEY;
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        $url = admin_url('admin-ajax.php');
    } else {
        $url = str_replace(array('https', 'HTTPS'), array('http', 'HTTP'), admin_url('admin-ajax.php'));
    }

    $params['action'] = 'setup_lib';
    $params['key'] = ST_KEY;
    foreach ($params as $key => &$val) {
        if (is_array($val))
            $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts = parse_url($url);

    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);

    $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
    $out .= "Host: " . $parts['host'] . "\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    if (isset($post_string))
        $out .= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}

function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

/* Email to users Announcement */

function emailtousers($announcement, $uname, $uemail) {

    $site_name = ST_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $subj = "Announcement - " . $announcement->title;

//    $link = "<a href='".ENFUSEN_URL."?announcement=".$announcement->slug."'>Click here to read full announcement.</a>";
//    $msg = "Hi $uname, <br/><br/>";
//    $msg .= "Enfusen team has sent you announcement <strong>".$announcement->title."</strong>. Below you can find detail.<br/><br/> "
//            . "<h4>$announcement->title</h4>"
//            . "<span>Announcement Date :  ".date('D d m Y, H:i',strtotime($announcement->start_date))."</span> <br/>"
//            . "<div>". html_entity_decode($announcement->description) ."</div>"
//            . "<br/><br/>";
//    $msg .= "Thanks, <br/>";
//    $msg .= $site_name;


    $msg = "";
    $msg .= "<section style='font-weight: 600; font-size: 22px; margin-bottom: 30px;'>"
            . "<div style='margin-bottom: 8px;'><center>" . $announcement->title . "</center></div>"
            . "<div style='margin-bottom: 8px;'><center>" . date('m.d.Y', strtotime($announcement->start_date)) . "</center></div>"
            . "<div style='    margin-bottom: 8px; color: #A9A6A6; font-size: 18px;'><center>Announcement from the team at Enfusen</center></div></section>"
            . "<div>" . html_entity_decode($announcement->description) . "</div>"
            . "<br/><br/>";
    $msg .= "Thanks <br/>";
    $msg .= $site_name;

    custom_mail($uemail, $subj, $msg, AN_EMAIL_TYPE, "");
}

function setup_created($setup) {
    $site_name = ST_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $msg = "";
    $subj = $site_name . ' created a setup for you.';

    $msg .= "<section> Hi " . $setup->name . ",<br/><br/>"
            //. "$site_name created new setup for you. <br/><br/>"
            . "The team at Enfusen has setup your agency account. <br/><br/>"
            //. 'URL to access setup is : '.PROTOCOL.$setup->url.'<br/><br/>'
            . 'URL to access setup is : ' . PROTOCOL . $setup->url . '/custom-agency-login/<br/><br/>'
            . 'Username: ' . $setup->login . ' <br/>'
            . 'Password: ' . base64_decode(base64_decode($setup->password)) . ' <br/><br/>';
    $msg .= "Thanks <br/>";
    //$msg .= $site_name;
    $msg .= "Enfusen Optimization Bot";

    custom_mail($setup->email, $subj, $msg, ST_SEMAIL_TYPE, "");


    /* Sending Mail to Admin Also */

    $msg2 = "";
    $subj2 = $site_name . ' has created agency setup.';
    $msg2 .= "<section> Hi Roger,<br/><br/>"
            //. "$site_name created new setup for you. <br/><br/>"
            . "The Client " . $setup->name . " has created agency setup. <br/><br/>"
            //. 'URL to access setup is : '.PROTOCOL.$setup->url.'<br/><br/>'
            . 'URL to access setup is : ' . PROTOCOL . $setup->url . '/custom-agency-login/<br/><br/>'
            . 'Username: ' . $setup->login . ' <br/>'
            . 'Password: ' . base64_decode(base64_decode($setup->password)) . ' <br/><br/>';
    $msg2 .= "Thanks <br/>";
    //$msg .= $site_name;
    $msg2 .= "Enfusen Optimization Bot";
    custom_mail("roger@enfusen.com", $subj2, $msg2, ST_SEMAIL_TYPE, "");
}

function setup_status($status, $setup) {
    $site_name = ST_SITE_NAME;
    $admin_email = get_option('admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $msg = "";
    if ($status == 1) {
        $subj = 'You site ' . $setup->url . " is enabled by " . $site_name;
        $txt = 'enabled ' . PROTOCOL . $setup->url . '.';

        $msg .= "<section> Hi " . $setup->name . ",<br/>"
                . "<div style='margin-bottom: 8px;'> $site_name has $txt </div>"
                . "<br/><br/>";
        $msg .= "Thanks <br/>";
    } else {
        $subj = $setup->url . " disabled by " . $site_name;
        $txt = 'disabled ' . PROTOCOL . $setup->url . ' for some reason.<br/>';

        $msg .= "<section> Hi " . $setup->name . ",<br/>"
                . "<div style='margin-bottom: 8px;'> $site_name has $txt </div><br/>"
                . "For any query, please contact to  $site_name <br/><br/>";
        $msg .= "Thanks <br/>";
    }

    $msg .= $site_name;

    custom_mail($setup->email, $subj, $msg, ST_EMAIL_TYPE, "");
}

function custom_mail_header($fromcntmail = 'enfusen.com') {
    $additional_parameters = '-f notifications@enfusen.com';
    return "Reply-To: $fromcntmail\r\n"
            . "Return-Path: MCC <notifications@" . $fromcntmail . ">\r\n"
            . "From: Enfusen Notifications <notifications@" . $fromcntmail . ">\r\n"
            . "Return-Receipt-To: notifications@" . $fromcntmail . "\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-type: text/html\r\n"
            . "X-Priority: 3\r\n"
            . "X-Mailer: PHP" . phpversion() . "\r\n";
}

function custom_mail($user_email, $setup_sub, $body, $email_type, $reason) {
    $email_template_body = email_template_body($body, $user_email, $email_type);
    @mail($user_email, $setup_sub, $email_template_body, custom_mail_header(), mail_additional_parameters());
    insert_email_historical_report(user_id(), $email_type, $setup_sub, $user_email, $reason, current_id());
}

function an_unsubscribed($email) {
    global $wpdb;
    $tbl_unsbs = $wpdb->prefix . "all_email_subscription";
    $unnsubs = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT setting FROM " . $tbl_unsbs . " WHERE email = %s", $email
            )
    );

    if (empty($unnsubs)) {
        return FALSE;
    }
    $unnsubs = $unnsubs->setting;
    $unnsubs = unserialize($unnsubs);
    $i = 0;
    foreach ($unnsubs as $key => $unnsub) {
        if ($key == AN_EMAIL_TYPE) {
            $i = 1;
            break;
        }
    }

    if ($i == 1) {
        return TRUE;
    }
    return FALSE;
}

function setting_and_email_templates($conn) {
    global $wpdb;

    /*     * ** Insert Into Setting Table *** */

    $setting_tbl = $wpdb->prefix . 'setting';
    $sql_sett = "SELECT count(id) as total FROM " . $setting_tbl;
    $result = mysqli_query($conn, $sql_sett);
    $row = $result->fetch_object();
    if ($row->total == 0) {

        $val = '{"key":"5718e52bd6c62","sts":"y"}';
        $val = mysqli_real_escape_string($conn, $val);
        $sql = "INSERT INTO " . $setting_tbl . " (keyname,keyvalue,type) VALUES('valid_licence', '$val', '')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $setting_tbl . " (keyname,keyvalue,type) VALUES('Office Hours recording', '', 'link')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $setting_tbl . " (keyname,keyvalue,type) VALUES('Google+ Community', 'https://www.google.co.in', 'link')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $setting_tbl . " (keyname,keyvalue,type) VALUES('Help', '', 'link')";
        mysqli_query($conn, $sql);
    }

    /*     * ** Insert Into Setting Table *** */


    /*     * ** Insert Into Training Tool Email Template Table *** */

    $email_templates = $wpdb->prefix . 'email_templates';
    $sql_sett = "SELECT count(id) as total FROM " . $email_templates;
    $result = mysqli_query($conn, $sql_sett);
    $row = $result->fetch_object();
    if ($row->total == 0) {

        $now = date("Y-m-d H:i:s");
        $url = '"{{url}}"';
        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('course_permission_granted',
                            'Permisssion granted for {{course_title}} course',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div>The team at Enfusen has granted you access to {{course_title}} course.</div>
                            <div><a href=$url>Click here to view your course</a></div>
                            <div></div>
                            <div>Thanks,
                            {{site_name}}</div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('mentor_added',
                            'Added as mentor to {{course_title}} course',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div>
                            <div>The team at Enfusen has added you as a mentor for {{course_title}} course.</div>
                            <div>Login your account to know more about course.</div>
                            <div></div>
                            Thanks,
                            {{site_name}}

                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('mentor_removed',
                            'Removed as mentor from {{course_title}} course',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div></div>
                            <div>The team at Enfusen removed you from {{course_title}} course.</div>
                            For any query, please feel free to contact.
                            <div>

                            Thanks,
                            {{site_name}}

                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);


        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('survey_send',
                            'Survey sent regarding your mentor {{mentor_name}}',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div></div>
                            <div>This is the survey regarding your mentor {{mentor_name}}.</div>
                            <div><a href=$url>Please Click Here Fill Survey</a></div>
                            <div></div>
                            <div>
                            <div></div>
                            <div>Thanks,
                            {{site_name}}</div>
                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);


        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('survey_result_user',
                            'Thanks for Survey',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div></div>
                            <div>

                            Thanks for submitting survey {{survey_title}} regarding your mentor {{mentor_name}}.
                            <a href=$url>Click Here To Check Your Survey</a>

                            Thanks,
                            {{site_name}}

                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);


        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('survey_result_mentor',
                            'New Survey Submitted',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div>User {{survey_user}} has been submitted the survey for {{survey_title}} form.</div>
                            <div>

                            <a href=$url>Click Here To Check Survey</a>

                            Thanks,
                            {{site_name}}

                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('mentor_call',
                            'Mentor Call For Course {{course_title}}',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div>This is to inform you that your mentor {{mentor_name}} {{scehulde_or_reschedule}} a call on date {{call_date}} for {{course_title}}</div>
                            <div>Link for meeting is: {{meeting_link}}<a href=$url>Click Here To Accept Invitation And Notify Your Mentor</a>

                            Thanks,
                            {{site_name}}

                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);


        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('mentor_call_cancel',
                            'Mentor Call Cancelled For Course {{course_title}}',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div>

                            This is to inform you that your mentor {{mentor_name}} cancelled call for {{course_title}} course on date {{call_date}}

                            Thanks,
                            {{site_name}}

                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);

        $sql = "INSERT INTO " . $email_templates . "(template,subject,content,created_dt)"
                . "VALUES('mentor_call_reminder',
                            'Mentor call reminder for {{course_title}} course',
                            '<div>Hi {{username}},</div>
                            <div></div>
                            <div>

                            This is to remind you about mentor call on date {{call_date}}
                            Detail you can find bewlow :
                            <div>Course: {{course_title}}</div>
                            <div>Mentor: {{mentor_name}}</div>
                            <div>Link: {{meeting_link}}</div>
                            <div>Call Date: {{call_date}}</div>
                            <div></div>
                            <div>Thanks,
                            {{site_name}}</div>
                            </div>',
                            '" . $now . "')";
        mysqli_query($conn, $sql);
    }

    /*     * ** Insert Into Training Tool Email Template Table *** */
}

function prevent_medstar($db_name) {
    // temp code just to save my domain, because of plugin i am working
    if ($db_name == 'mcc_medstar' || $db_name == 'mcc_mcc') {
        json(0, 'You can update these Setups. Ask you developer to do this.');
    }
// temp code
}
?>
