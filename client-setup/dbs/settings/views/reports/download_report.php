<div id="admin_responsive" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><strong>Download Options</strong></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" class="form-horizontal" method="post" id="reportdownload" name="reportdownload">
                            <?php if (!empty($popupSucsMsg)) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                    <?php echo $popupSucsMsg; ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($popupErrMsg)) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                    <?php echo $popupErrMsg; ?>
                                </div>
                            <?php } ?>                                                        
                            <?php
                            if (isset($_REQUEST['report-type']) && ($_REQUEST['report-type'] == 'traffic' || $_REQUEST['report-type'] == 'conversion')) {
                                $today = isset($to_date) ? date("m/d/Y", strtotime($to_date)) : date('m/d/Y');
                                $from = isset($from_date) ? date("m/d/Y", strtotime($from_date)) : date('m/d/Y', strtotime("-30 days"));
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Date:</label>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6"><input type="text" name="fromdate" placeholder="mm/dd/yyyy" class="form-control datepicker " size="10" value="<?php echo $from; ?>" id="fromdate"><span class="small">From Date</span></div>
                                            <div class="col-md-6"><input type="text" name="todate" placeholder="mm/dd/yyyy"  class="form-control datepicker " size="10" value="<?php echo $today; ?>" id="todate"><span class="small">To Date</span></div>
                                        </div>
                                    </div>
                                </div>
                                <?php }
                            ?>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Download Type:</label>
                                <div class="col-md-8">
                                    <div class="radio-list">
                                        <label class="radio-inline">
                                            <div class="radio"><input name="dwnld_type" value="csv" type="radio"  <?php if (isset($_POST['dwnld_type']) && $_POST['dwnld_type'] == 'csv') echo ' checked'; ?>></div> CSV </label>
                                        <label class="radio-inline">
                                            <div class="radio"><input name="dwnld_type" value="pdf" type="radio" <?php if (!isset($_POST['dwnld_type']) || $_POST['dwnld_type'] == 'pdf') echo ' checked'; ?>></span></div> PDF </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <input type="submit" class="new_btn_class btn btn-success" name="btn_download-report" value="Submit" style="background:none">
                                </div>
                            </div>

                            <h4 class="modal-title"><strong>Schedule E-Mail Report</strong></h4><hr/>

                            <?php
                            global $wpdb;
                            $sql = "SELECT * FROM {$wpdb->prefix}mcc_sch_settings WHERE sch_type='$db_report_name'";
                            $settingRow = $wpdb->get_row($sql, ARRAY_A, 0);

                            if (empty($settingRow)) {


                                $userInfo = get_userdata($UserID);

                                $settingRow = array(
                                    'sch_status' => 0,
                                    'sch_outTime' => '00:00',
                                    'sch_frequency' => 'Daily',
                                    'sch_reportVolume' => 'Last 24 Hours',
                                    //'sch_emailTo' => '',//$userInfo->user_email,
                                    'emails' => array(),
                                );
                            } else {

                                //Skip seconds:

                                if (!empty($settingRow['sch_outTime']))
                                    $settingRow['sch_outTime'] = substr($settingRow['sch_outTime'], 0, 5);

                                //Query all emails from DB:
                                $sql = "SELECT * FROM {$wpdb->prefix}mcc_sch_emails WHERE em_sch_id={$settingRow['sch_id']} ORDER BY em_id";
                                $dbEmails = $wpdb->get_results($sql);

                                //Process DB-Emails for template uses:
                                $settingRow['emails'] = array();
                                if (!empty($dbEmails)) {
                                    foreach ($dbEmails as $dbem) {
                                        $settingRow['emails'][] = array(
                                            'to' => $dbem->em_emailTo,
                                            'st' => $dbem->em_status,
                                            'id' => $dbem->em_id
                                        );
                                    }
                                }
                            }


                            //Must keep submitted data if there is any error to make corrections:
                            if (!empty($popupErrMsg) && isset($_POST['sh-email']))
                                $settingRow['emails'] = $_POST['sh-email'];

                            //Just to ensure that we have at least one input field whatever happened before:
                            if (empty($settingRow['emails'])) {
                                $settingRow['emails'] = array(
                                    array(
                                        'to' => '', //$userInfo->user_email,
                                        'st' => '1'
                                    )
                                );
                            }

                            //pr($settingRow,'======$settingRow====');
                            ?>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Send report via email:</label>
                                <div class="col-md-8">
                                    <label class="checkbox-inline" style="padding-left:1px;"><input type="checkbox" name="sh-send-report-via-email" value="1"<?php if ((isset($_POST['sh-send-report-via-email']) && in_array('1', $_POST['sh-send-report-via-email'])) || $settingRow['sch_status']) echo ' checked'; ?>></label>                                        
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Schedule Report Type:</label>
                                <div class="col-md-8">
                                    <div class="radio-list">
                                        <label class="radio-inline">
                                            <div class="radio"><input name="report_type" value="pdf" type="radio" <?php if ($settingRow['report_type'] == 'pdf' || $settingRow['report_type'] == '') echo 'checked' ?>></span></div> PDF </label>

                                        <label class="radio-inline">
                                            <div class="radio"><input name="report_type" value="csv" type="radio"  <?php if ($settingRow['report_type'] == 'csv') echo 'checked' ?> ></div> CSV </label>
                                    </div>
                                </div>
                            </div>

                            <?php $i = 1;
                            foreach ($settingRow['emails'] as $indx => $em) { ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php echo $i == 1 ? 'E-Mail to:' : ''; ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control required" type="text" id="sh-email-to-<?php echo $indx; ?>" name="sh-email[<?php echo $indx; ?>][to]" value="<?php echo $em['to']; ?>" >

                                    </div>
                                    <div class="col-md-3">
                                        <label class="checkbox-inline" style="padding-left:1px;"><input type="checkbox" name="sh-email[<?php echo $indx; ?>][st]" value="1"<?php checked($em['st'], 1, true); ?>> Active</label>
                                    </div>
                                </div>
                                <?php if ($em['id']): ?><input type="hidden" name="sh-email[<?php echo $indx; ?>][id]" value="<?php echo $em['id']; ?>"><?php endif; ?>
                                <?php $i++;
                            } ?>

                            <div class="form-group" id="add-more-box">
                                <div class="col-md-8 col-md-offset-4">
                                    <span class="help-block"><small>*NB: Unsubscribed email(s) are unchecked. Keep the field(s) empty to delete.</small></span>
                                    <a href="javascript:void(0);" class="add-more-emails btn btn-success btn-sm" style="background:none; padding: 1px 5px !important;" data-indx="<?php echo count($settingRow['emails']); ?>">add more</a>                 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">When do you wish to receive the report?:</label>
                                <div class="col-md-8">
                                    <select name="sh-how-often" class="form-control" >
                                        <?php
                                        $items = array(
                                            //'Daily',
                                            'Weekly',
                                            'Monthly'
                                        );
                                        foreach ($items as $when):
                                            ?>
                                            <option value="<?php echo $when; ?>" <?php selected(isset($_POST['sh-how-often']) ? $_POST['sh-how-often'] : $settingRow['sch_frequency'], $when); ?>><?php echo $when; ?></option>
<?php endforeach; ?>
                                    </select>
                                    <span class="help-block"><small>*NB: Monthly report will be sent 1st of month at 6am EST and Weekly report will be sent on Monday at 6am EST</small></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Report Volume:</label>
                                <div class="col-md-8">
                                    <select name="sh-volume" class="form-control" >
                                        <?php
                                        $items = array(
                                            //'Last 24 Hours'	=> 'Last 24 Hours - Daily',
                                            'Last 7 Days' => 'Last 7 Days - Weekly',
                                            'Last 30 Days' => 'Last 30 Days - Monthly'
                                        );
                                        foreach ($items as $k => $when):
                                            ?>
                                            <option value="<?php echo $k; ?>" <?php selected(isset($_POST['sh-volume']) ? $_POST['sh-volume'] : $settingRow['sch_reportVolume'], $k); ?>><?php echo $when; ?></option>
<?php endforeach; ?>
                                    </select>
                                </div>
                            </div>                                                        

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <input type="submit" class="btn btn-success" style="background:none" name="btn_schedule-executive-report" value="Set Schedule">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>        
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        if (typeof $(":checkbox").uniform() !== 'undefined')
            $(":checkbox").uniform();

        jQuery(".admin_dwnld_rprt").on('click', function () {
            jQuery('#admin_responsive').modal();
        });

<?php if (!empty($popupErrMsg) || !empty($popupSucsMsg)) {
    ?>
            jQuery('#admin_responsive').modal();
    <?php }
?>

        jQuery(".add-more-emails").on('click', function () {
            var $this = $(this);
            var $indx = $this.attr('data-indx');

            var $row = '<div class="form-group">'
                    + '<label class="col-md-4 control-label"></label>'
                    + '<div class="col-md-5">'
                    + '<input class="form-control required" type="text" id="sh-email-to-' + $indx + '" name="sh-email[' + $indx + '][to]">'

                    + '</div>'
                    + '<div class="col-md-3">'
                    + '<label class="checkbox-inline" style="padding-left:1px;"><input type="checkbox" name="sh-email[' + $indx + '][st]" value="1" checked="checked"> Active</label>'
                    + '</div>'
                    + '</div>';
            $('#admin_responsive #add-more-box').before($row);
            var newindx = parseInt($indx) + 1;
            $this.attr('data-indx', newindx);
            $(":checkbox").uniform();

        });

    });

</script>   