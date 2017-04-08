<style>
    input[name=content_section]{
        position: relative;
        top: -3px;
        right: 4px;
    }
</style>
<?php
$setup = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . setup_table() . " WHERE id = %d", $setup_id
        )
);

if (empty($setup)) {
    ?>
    <div class="update-nag">Invalid Setup ID</div>
    <?php
    exit;
}

if (!empty($setup)) {
    //pr($setup);
    /********* On Local server Connection START **********/
    /*
    $servername = "192.168.1.106";
    $username = 'root';
    $password = 'rudra@1234#';
    */
    /********* On Local server Connection STOP **********/
    //pr($setup);

    /********* On Live server Connection START **********/

    $servername = DB_HOST;

    //$password = base64_decode(base64_decode($setup->db_password));

    if($servername == '192.168.1.106'){
        $username = 'root';
        $password = 'rudra@1234#';
    }else{
        $username = $setup->db_username;
        //echo $get_agency->db_password;
        $password = base64_decode(base64_decode($getagencyrow->db_password));
    }

    /********* On Live server Connection STOP **********/

    $dbname = $setup->db_name;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    $sql = "SELECT * FROM wp_agency_trialperiod";
    $trial_result = $conn->query($sql);
}

$option_name = 'client_setup_' . $setup->id;
$compl = get_option($option_name);
if (!$compl) {
    $compl = 0;
}

function string_sanitize($s) {
    $result = trim($s, '"');
    $result = trim($result, "'");
    return $result;
}

// Add define variables here, that you want not to include under normal view
$arr_not_include = array('agency_full_content', 'MCC_DIR', 'ST_CRON_KEY', 'SpecSeparatorStr', 'SET_PARENT_URL', 'oauth2_clientId_old', 'oauth2_clientId_old2', 'MICROS_CONV',
    'AccessTokensDBTableName', 'AnalyticsDataDBTableName', 'AnalyticsCacheDataDBTableName', 'ConvTrackingDBTableName', 'ConvTrackingCacheDBTableName',
    'ConvTrackingFilteredDBTableName', 'ConvTrackingUrlsDBTableName', 'ConvTrackJSCodeFileName', 'SEODBTableName', 'cron_log_file_name',
    'error_log_file_name', 'ST_LOC_PAGE');

$global_config = $setup->dir . "/global_config.php";
$analytic_file = $setup->analytic_dir . "/analytics_config.php";

if (isset($_POST['saveconfigfile'])) {

    $keys_excluded = array('saveconfigfile', 'mainheadval', 'savesimple');
    $data = $_POST;
    $str = "<?php \n\n";
    $m = 0;
    $n = 0;
    foreach ($data as $key => $value) {
        if (!in_array($key, $keys_excluded)) {
            $haskey = explode("heading_", $key);
            if (isset($haskey[1]) && intval($haskey[1]) > 0) {
                if ($m > 0)
                    $str .= "\n\n";

                $str .= trim($data['mainheadval'][$m]);
                $str .= "\n";
                $m++;
            }
            else {
                if ($key != "helpval") {

                    $vl = '';
                    if ($data['helpval'][$n] != '')
                        $vl = " " . trim($data['helpval'][$n]);

                    if ($value == 'true' || $value == 'false') {
                        $str .= "define('$key', $value);" . $vl;
                    } else {
                        $str .= "define('$key', '$value');" . $vl;
                    }
                    $str .= "\n";

                    $n++;
                }
            }
        }
    }
    $str .= "\n";
    $str .= "?>";

    file_put_contents($global_config, $str); // add data in config file
    file_put_contents($analytic_file, $str); // add data in analytic file
    if (isset($_POST['savesimple']) && $_POST['savesimple'] == 2)
        echo "<script> show_msg(1,'Content section updated successfully') </script>";
    else
        echo "<script> show_msg(1,'Configuration file updated successfully') </script>";

    if (isset($_POST['TRIAL_PERIOD']) && !empty($_POST['TRIAL_PERIOD']) && isset($_POST['TRIAL_LOCATIONS']) && !empty($_POST['TRIAL_LOCATIONS'])) {
        if ($trial_result->num_rows > 0) {
            $sql = "SELECT * FROM wp_agency_trialperiod";
            $trial_result = $conn->query($sql);
            while ($row = $trial_result->fetch_assoc()) {
                //pr($row['trial_id']);
                //$_POST['BILLING_ENABLE'];
                if(empty($_POST['BILLING_ENABLE'])){
                    $billing = 0;
                }else{
                    $billing = $_POST['BILLING_ENABLE'];
                }
                $currentdate = date("Y-m-d h:i:s");
                $query = "UPDATE `wp_agency_trialperiod` SET  `billing_enable` = '" . $billing . "',`trial_nooflocations` = '" . $_POST['TRIAL_LOCATIONS'] . "',`days` = '" . $_POST['TRIAL_PERIOD'] . "',`update` = '" . $currentdate . "' WHERE `trial_id` = '" . $row['trial_id'] . "'";

                $update_trial = $conn->query($query);
            }
        } else {
            $currentdate = date("Y-m-d h:i:s");
            $query = "INSERT INTO `wp_agency_trialperiod`(`trial_nooflocations`, `days`, `date`, `update`) VALUES ('" . $_POST['TRIAL_LOCATIONS'] . "','" . $_POST['TRIAL_PERIOD'] . "','" . $currentdate . "','" . $currentdate . "')";
            $update_trial = $conn->query($query);
        }
    } else {
        echo "Failed";
    }
} else if (isset($_POST['config_post'])) {

    if ($_POST['config_post'] == 'post') {
        $config_data = isset($_POST['config_data']) ? trim($_POST['config_data']) : '';
        if ($config_data != '') {
            $config_data = stripcslashes($config_data);
            file_put_contents($global_config, $config_data); // add data in config file
            file_put_contents($analytic_file, $config_data); // add data in analytic file
            echo "<script> show_msg(1,'Configuration file updated successfully') </script>";
        }
    } else if ($_POST['config_post'] == 'config_auto') {

        $pass = base64_decode(base64_decode($setup->db_password));
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "SET PASSWORD FOR " . $setup->db_username . "@" . DB_HOST . " = PASSWORD('" . $pass . "')", ""
                )
        );
        $dest_file = $setup->dir . "/global_config.php";
        $analytic_file = $setup->analytic_dir . "/analytics_config.php";

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
        $str = str_replace("{{mcc_url}}", 'http://' . $setup->url, $str);
        $str = str_replace("{{analytical_url}}", 'http://' . $setup->analytic_url, $str);
        $sitename = 'http://' . $setup->url;
        if ($setup->white_lbl != '')
            $sitename = $setup->white_lbl;

        $str = str_replace("{{site_name}}", $sitename, $str);
        $str = str_replace("{{no_replay_email}}", $setup->email, $str);
        $str = str_replace("{{training_team_name}}", "Training Team at " . $sitename, $str);
        $str = str_replace("{{announcements_url}}", $sitename . "/announcement", $str);
        $str = str_replace("{{parent_url}}", admin_url('admin-ajax.php'), $str);

        /* Other string replaces */
        file_put_contents($dest_file, $str);
        file_put_contents($analytic_file, $str); // add data in analytic file

        /* $message = PHP_EOL."<?php define('DISALLOW_FILE_EDIT', true); ?>".PHP_EOL;
          file_put_contents($dest_file, $message, FILE_APPEND);
          file_put_contents($analytic_file, $message, FILE_APPEND); // append data in analytic file
         */

        echo "<script> show_msg(1,'Configuration file successfully reset') </script>";
    }
}


$url = PROTOCOL . $setup->url;

if (trim($setup->white_lbl) != '') {
    $url = $setup->white_lbl;
}
?>

<div class="contaninerinner setuppageinner">

    <h4>Setup For Client <?php echo $setup->prefix; ?></h4>
    <div class="bread_crumb">
        <ul>
            <li title="Client Setups">
                <a href="admin.php?page=client_setups">Client Setups</a> >>
            </li>
            <li title="New Setup Step Second">
                New Setup Step Second
            </li>
        </ul>
    </div>
    <div class="clearfix"></div>
<?php if ($setup->db_name != '') { ?>
        <div class="backgroundscannings hide">
            <div class="backlbl">Setup Background Scanning</div>
            <div class="progress">
                <div class="progress-bar progressbar" role="progressbar" style="width:70%">
                    70% Completed
                </div>
            </div>
        </div>
        <input type="hidden" value="<?php echo $compl; ?>" id="iscompleted" name="iscompleted" />
<?php } ?>

    <div class="url_setup">
        <div class="alert alert-info">
            <div class="pull-right">
                <a target="_blank" class="adminlogin btn btn-primary" href="<?php echo admin_url('admin-ajax.php') . '?action=login&client=' . $setup->id; ?>">Login as administrator</a></div>
            <div class=""><strong>SETUP URL : </strong><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></div>
            <div class="small smallmsgnote"><i><b>Note : </b> In case of white label URL, please copy crons with new url and paste in crontab (mentioned in Cron List Tab below).</i></div>
<?php
if (trim($setup->white_lbl) != '') {
    $guid = md5($setup->db_name);
    $tempurl = PROTOCOL . $setup->url . '?temp_login&guid=' . $guid;
    ?>
                <div class="clearfix"></div>
                <div class=" margin_top_10"><strong>Temporary URL : </strong><a target="_blank" href="<?php echo $tempurl; ?>"><?php echo $tempurl; ?></a></div>
                <div class="clearfix"></div>
                <div class="small smallmsgnote"><i><b>Note : </b> Use above url, in case you are unable to login with setup url.</i></div>
            <?php } ?>
        </div>
    </div>
    <div class="form-group">
<?php if ($setup->db_name != '') {
    ?>
            <input type="submit" onclick="jQuery('#setup_update').val(1); jQuery('#confiure_setup').submit();" value="Re-Build Full Setup" class="btn btn-primary"/>

            <form method="post" id="global_config_auto" name="global_config_auto" class="display_inline">
                <input type="hidden" value="config_auto" name="config_post" />
                <input type="button" value="Reset Configuration File" class="btn btn-primary rebuilconfig"/>
            </form>
    <?php
    if ($setup->status == 1) {
        ?>
                <a href="javascript:;" data-id="<?php echo $setup->id; ?>" data-sts="Disable" data-attr="0" class="btn btn-warning statussetup">Disable</a>
        <?php
    } else {
        ?>
                <a href="javascript:;" data-id="<?php echo $setup->id; ?>" data-attr="1" data-sts="Enable" class="btn btn-success statussetup">Enable</a>
                <?php
            }
            ?>
            <a href="<?php echo admin_url('admin-ajax.php') . '?action=login&param=account_settings&client=' . $setup->id; ?>" target="_blank" class="btn btn-primary">Account Settings</a>
            <a href="<?php echo admin_url('admin-ajax.php') . '?action=login&wpadmin&client=' . $setup->id; ?>" target="_blank" class="btn btn-primary">Go To WP Admin Area</a>
            <?php ?>
            <?php
        } else {
            ?>
            <input type="submit" onclick="jQuery('#setup_update').val(0); jQuery('#confiure_setup').submit();" value="Build Setup" class="btn btn-primary"/>
    <?php }
?>

        <a href="javascript:;" data-id="<?php echo $setup->id; ?>" class="btn btn-danger deletesetup pull-right">Delete Setup</a>
    </div>
        <?php
        $content = file_get_contents($global_config);
        $file = $content = stripcslashes($content);
        if (trim($setup->white_lbl) != '') {
            $ur1 = trim($setup->url, "/");
            $ur2 = trim(str_replace(array('http://', 'https://'), array('', ''), $setup->white_lbl), "/");
            $file = str_replace($ur1, $ur2, $file);
        }

        if ($content == '') {
            $content = "Setup is not build yet.";
        }
        ?>

    <div class="panel-body margin-bottom-10" style="background: #fff;">
        <div class="col-lg-2 row">Content Section : </div>
        <div class="col-lg-3"><label><input type="radio" value="1" name="content_section" /> Full Content Section</label></div>
        <div class="col-lg-4"><label><input type="radio" value="2" name="content_section" /> Content Section With Enfusen Writers</label></div>
        <div class="col-lg-3"><label><input type="radio" value="3" name="content_section" /> No Content Section</label></div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Configuration File</div>
        <div class="panel-body">

            <div class="row">
                <ul class="nav nav-tabs">
                    <li class="<?php echo!isset($_GET['view']) || $_GET['view'] == 'normal' ? 'active' : ''; ?>"><a data-toggle="tab" href="#normalview">Normal View</a></li>
                    <li class="<?php echo isset($_GET['view']) || $_GET['view'] == 'developer' ? 'active' : ''; ?>"><a data-toggle="tab" href="#developerview">Developer View</a></li>
                    <li class="<?php echo isset($_GET['view']) || $_GET['view'] == 'cron' ? 'active' : ''; ?>"><a data-toggle="tab" href="#cronview">Cron List</a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div id="normalview" class="tab-pane fade <?php echo!isset($_GET['view']) || $_GET['view'] == 'normal' ? 'in active' : ''; ?>">

                    <form role="form" action="#" method="post" id="viewformnormal" name="viewformnormal" class="form-horizontal margin_top_10">
                        <input type="hidden" class="form-control" name="savesimple" id="savesimple" value="1" />
<?php
$content_sec = 0;
$file = str_replace("<?php", "", $file);
$file = str_replace("?>", "", $file);
$file = explode("\n", $file);
$i = 0;
$jk = 0;
foreach ($file as $f) {
    $jk++;
    if (strpos($f, 'define(') !== false) {
        $line = explode(";", $f);
        $defineline = trim($line[0]);
        $defineline = substr($defineline, 0, -1);
        $defineline = ltrim($defineline, "define(");

        $expdefline = str_getcsv($defineline, ",", "'");
        $key = trim($expdefline[0]);
        $value = trim($expdefline[1]);
        $key = string_sanitize($key);
        $display = '';
        if (in_array($key, $arr_not_include)) {
            $display = 'display: none';
        }
        $value = string_sanitize($value);

        if ($key == 'agency_full_content') {
            $content_sec = intval($value);
        }
        ?>
                                <div style='<?php echo $display; ?>' class="form-group">
                                    <label for="<?php echo $key; ?>" class="col-lg-3"> <?php echo $key; ?></label>
                                    <div class="col-lg-9">
                                        <input type="hidden" class="form-control" name="heading_<?php echo $i; ?>" value="<?php echo $i; ?>" />
                                        <input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
                                        <input type="hidden" class="form-control" name="helpval[]" value="<?php echo $line[1]; ?>" />
                                        <span class="small"><?php print_r(trim(str_replace("//", "", $line[1]))); ?></span>
                                    </div>
                                </div>
        <?php
    } else {
        if (trim($f) != '') {
            ?>
                                    <input type="hidden" class="form-control" name="mainheadval[]" value="<?php echo $f; ?>" />
                                    <h5 class="h5headerconfig"><?php echo $f; ?></h5>
            <?php
            $i++;
        }
    }
}
if ($jk > 0) {
    ?>
                            <div class="form-group">
                                <label for="" class="col-lg-3"> </label>
                                <div class="col-lg-9">
                                    <button type="submit" name="saveconfigfile" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        <?php
                        } else {
                            echo "Setup is not build yet.";
                        }
                        ?>
                    </form>

                </div>
                <div id="developerview" class="tab-pane fade <?php echo isset($_GET['view']) || $_GET['view'] == 'developer' ? 'in active' : ''; ?>">
                    <div class="alert alert-info">
                        <strong>Note : </strong> Use only signle quote (') in order to define a variable
                    </div>
                    <form action="#" method="post" id="global_config_form" name="global_config_form" class="form-horizontal">
                        <input type="hidden" value="post" name="config_post" />
                        <div class="form-group">
                            <textarea rows="30" name="config_data" class="form-control"><?php echo $content; ?></textarea>
                            <div class="clearfix"></div>
                        </div>
<?php if ($setup->db_name != '') { ?>
                            <div>
                                <input type="button" value="Update" class="btn btn-primary updateconfig margin_top_10" />
                            </div>
<?php } ?>
                    </form>
                </div>
                <div id="cronview" class="tab-pane fade <?php echo isset($_GET['view']) || $_GET['view'] == 'cron' ? 'in active' : ''; ?>">
<!--                    <div class="alert alert-info">
                        <strong>Note : </strong> Copy below cron jobs and place it on server using crontab.
                        <input type="button" class="btn btn-primary pull-right copybcode" value="Copy To Clipboard" />
                    </div>-->
                    
                     <?php
                        //Code starts by Rudra
                        $count_crons=$wpdb->get_results("SELECT cc.id FROM `client_crons` as cc JOIN `master_crons` as ts  ON cc.cron_type=ts.cron_type WHERE cc.agency_id=".$setup->id." AND cc.status=1");
                        $count_crons=count($count_crons);
                        if($count_crons>0){?>
                         <input type="button" data-id="<?php echo $setup->id;?>" data-status="0" class="btn btn-warning pull-Left disablecron" style="margin-bottom:5px;" value="Disable Crons" />
                          <?php }else{?>
                            <input type="button" data-id="<?php echo $setup->id;?>" data-status="1" class="btn btn-success pull-Left disablecron" style="margin-bottom:5px;" value="Enable Crons" />
                        <?php }
                        //Code ends by Rudra

                        ?>
                    
                    <div class="cronlist">

                      <?php
                      //Code starts by rudra dated:14 jan-2017
                      global $wpdb;
                      $tbl = 'client_crons';
                      $copidejobs = '';
                      $jobs='';
                      $jd="";
                      $select_crons=$wpdb->get_results("SELECT cc.agency_id,cc.cron_link,cc.cron_type,ts.cron_time FROM `client_crons` as cc JOIN `master_crons` as ts  ON cc.cron_type=ts.cron_type WHERE cc.agency_id=".$setup->id);
                      $loop_select_crons=$select_crons;
                      $setup_crons = count($select_crons);
                      if($setup_crons==0){
                          $copidejobs="Setup is not build yet.";
                      }else{
                          foreach($loop_select_crons as $select_cron){
                              $copidejobs .= $select_cron->cron_time."  ".$select_cron->cron_link."\n";
                          }
                      }
                      //Code end by rudra dated:14 jan-2017
                      ?>
                    <textarea id="copybTarget" rows="20" class="form-control"><?php echo $copidejobs; ?></textarea>
                    </div>
<!--                    <div class="alert alert-warning margin_top_10">
                        <strong>Steps to Add Job Set in Crontab : </strong>
                        <div class="margin_top_10">Step 1: Copy the above set of jobs. </div>
                        <div class="margin_top_10">Step 2: Connect Server through username and password Or from SSH access </div>
                        <div class="margin_top_10">Step 3: Go to Root User, by executing command : >> sudo su </div>
                        <div class="margin_top_10">Step 4: Open Crontab, by executing command : >> crontab -e </div>
                        <div class="margin_top_10">Step 5: Scroll Down to bottom from mouse or Page Down key and paste jobs. [Paste Shortcut: CTRL + SHIFT + V] OR Right Click and paste it </div>
                        <div class="margin_top_10">Step 6: Save Jobs, by pressing CTRL + X and then press Y to save and N to not save </div>
                    </div>-->
                </div>
            </div>

            <form action="#" method="post" id="confiure_setup" name="confiure_setup" class="form-horizontal">
                <input type="hidden" id="setup_id" name="setup_id" value="<?php echo $setup->id; ?>" />
                <input type="hidden" id="setup_update" name="setup_update" value="0" />

            </form>
        </div>
    </div>

</div>
<script>
    jQuery(document).ready(function () {
        var content_sec = "<?php echo $content_sec; ?>";
        if (content_sec == 1 || content_sec == 2) {
            jQuery('input[name=content_section][value=' + content_sec + ']').attr('checked', 'checked');
        } else {
            jQuery('input[name=content_section][value=3]').attr('checked', 'checked');
        }

    });


    jQuery(document).on('click', 'input[name=content_section]', function () {
        var conf = confirm('Are you sure for this step?');
        if (conf) {
            var val = jQuery(this).val();
            jQuery("input[name=agency_full_content]").val(val);
            jQuery('#savesimple').val('2');
            jQuery("button[name=saveconfigfile]").click();
        }
    });

</script>