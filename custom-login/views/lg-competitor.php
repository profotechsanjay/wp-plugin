<?php
if(!isset($_SESSION['customuser'])){ header("location:".site_url()."/custom-agency-login/"); }
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$CURENT_ID = $current_user->ID;
session_start();
if (is_user_logged_in()) {

    $UserID = $user_ID;
    /* Site Saved in Create Campaign Page */
    $web_url = get_user_meta($UserID, "website", true);
    if (isset($_POST["ownweb"]) && $_POST["ownweb"] != '') {
        $url_name = $web_url;
        $key_limit_for_url = isset($_POST["webkeylimit"]) ? intval($_POST["webkeylimit"]) : 250;
        $URI = site_url() . '/cron/competitor-url-key-insert.php?user_id=' . $UserID . '&keyword_opportunity_user=' . $UserID . '&ownweb=' . $url_name . '&keywordlimit=' . $key_limit_for_url;
        $ch = curl_init($URI);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result_on = curl_exec($ch);
    }

    /*     * *********** TOOLTIP ********** */
    ?>


    <!--<div class="social_media_form">-->
    <div class="item-Notes">

        <div class="en-right">
            <div class="contaninerinner trackdiv">     

                <div class="panel panel-primary">
                    <div class="panel-heading">Competitor Url </div>
                    <div class="panel-body">

                        <div class="clearfix"></div>

                        <div class="gatrack">

                            <div style="float:right; width:100%;" id="tbl_mcc">

                                <form class="form-horizontal" id="runcomp" name="runcomp">
                                    
                                    <?php
                                    for ($row = 1; $row <= 3; $row++) {
                                        ?>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="<?php echo 'compurl_'.$row; ?>">Competitor URL <?php echo $row; ?>:</label>
                                            <div class="col-sm-10">
                                                <input type="url" required class="form-control" class="validurl" id="<?php echo 'compurl_'.$row; ?>" name="<?php echo 'compurl_'.$row; ?>" placeholder="Enter Competitor URL <?php echo $row; ?>">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
<input type="hidden" value="250" name="keywordlimit" id="keywordlimit"/>
                                    <div class="form-group"> 
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-default" id="btncomp">Finish</button> &nbsp;&nbsp; <a class="btn btn-danger" href="<?php echo site_url(); ?>">Skip for Now</a>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <div style="clear: both;"></div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
        <div class="clear_both"></div>
        <?php
    }
    ?>




