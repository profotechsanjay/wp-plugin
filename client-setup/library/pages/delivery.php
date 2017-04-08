<?php
$all_order = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'content_order WHERE user_id =' . $UserID . ' order by order_id desc');

$order_canceled_permission = 0;
if(check_enfusen_worker(1)){
    $order_canceled_permission = 1;
} else if(administrator_permission()){
    $order_canceled_permission = 1;
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<style type="text/css">
    thead th{text-transform: none !important;font-size: 12px;}
    .fancybox-overlay .right_task input[type="text"]{width:300px}
    .fancybox-overlay .right_task select{width:314px !important}
    .fancybox-overlay .right_task small{font-size:12px}
    #mult_email_div p{padding-bottom:2px}
    .addMore{padding:3px 6px; border-radius:6px; font-weight:bold; font-size:12px; color:#fff; background:#fb6800; margin-top:4px; display:inline-block; cursor:pointer}
    .addMore:hover{color:#fff; background-color:#e14e00}
    #dwnldContRprt{float:right}
    .errMsg, .sucsMsg{color:red; border:1px solid cyan; padding:10px; background:#ffe4c4}
    .sucsMsg{color:green; background-color:#d5f15c;}
    table.dataTable td.tdActn a{margin-top:4px; display:inline-block}
    .left_task{width:30%;}
</style>
<div class="accoSet">
    <h2 class="fulllist">All Order</h2>
</div>
<div class="clear_both"></div>
<div class="item-delivery">
    <a href="#dwnldContRprtFrm" id="dwnldContRprt">Download Content Report</a>
    <div class="clear_both"></div>
    <table style="font-size:92%!important;width:100%!important;" id="example" class="tabl1 table table-striped table-bordered table-hover" cellspacing="0" >
        <thead style="background-color: #888;color:white;">
            <tr>
                <th style="width:3%;text-align: center;">#ID</th>
                <th style="width:10%">Date</th>
                <th style="width:35%!important;">Sites</th>
                <th style="width:22%">Keyword</th>
                <th style="width:10%">Status</th>
                <th style="width:5%">Action</th>
                <th style="width:3%">DL</th>
                <th style="width:3%">SS</th>
                <th style="width:3%">SL</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($all_order)) {
                foreach ($all_order as $in => $r_order) {
                    if ($r_order->post_to == 'Buffer Site') {
                        $post_to = 'buffer';
                    }
                    if ($r_order->post_to == 'Primary Site') {
                        $post_to = 'onsite';
                    }
                    ?>
                    <tr style="text-align:center;font-size: 13px;">
                        <td style="width:5%;text-align: center;"><?php echo $r_order->order_id; ?></td>
                        <td style="width:10%"><?php echo $r_order->order_date; ?></a></td>
                        <td style="width:31%!important;"><a style="text-decoration:none;" target="_blank" href="<?php echo $r_order->sites; ?>"><?php echo $r_order->sites; ?></td>
                        <td style="width:22%"><?php
                            $keywords = $r_order->keys;
                            echo $keywords;
                            ?></td>
                        <td style="width:10%">
                            <?php
                            echo $r_order->status;
                            if ($r_order->status_date != "") {
                                echo '<br/><span style="font-size:10px;">' . date('d M Y h:i:a', strtotime($r_order->status_date)) . '</span>';
                            }
                            ?>
                        </td>
                        <td style="width:9%" class="tdActn">
                            <?php if ($r_order->status != 'Ordered') { ?>
                                <a style="text-decoration: none;" href="<?php echo site_url() . '/order-content/?type=view-content&post-to=' . $post_to . '&order_id=' . $r_order->order_id; ?>">View</a> 
                                <?php
                            }

                            if ($r_order->status == 'Ordered') {
                                ?>
                                <a style="text-decoration:none;" href="<?php echo site_url() . '/order-content/?type=edit-order&post-to=' . $post_to . '&order_id=' . $r_order->order_id; ?>">Edit</a>
                                <?php
                            }
                            if ($order_canceled_permission == 1) {
                                if ($r_order->status != 'Approved' && $r_order->status != 'Canceled') {
                                    ?>
                                    | <a style="text-decoration: none;" href="<?php echo site_url() . '/order-content/?type=canceled-order&order_id=' . $r_order->order_id; ?>">Canceled</a>
                                    <?php
                                }
                            }
                            if ($r_order->status == 'Approved') {
                                $already_given_feedback = $wpdb->get_row("SELECT * FROM `wp_feedback` WHERE `order_id` = $r_order->order_id && `sender_user_id` = $UserID");
                                if (empty($already_given_feedback)) {
                                    if (0) { //Only for the Enfusen Workers //??????????????????????????????????????????????
                                        ?>
                                        | <a style="text-decoration:none;" href="<?php echo site_url() . '/order-content/?type=edit-order&post-to=' . $post_to . '&order_id=' . $r_order->order_id; ?>">Edit</a>
                                        <?php }
                                    ?>
                                    | <a style="text-decoration: none;" href="<?php echo site_url() . '/order-content/?type=feedback&feedback_order_id=' . $r_order->order_id; ?>">Give Feedback</a>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                        <td style="width:5%"><?php echo $r_order->dl == '' ? 'n/a' : $r_order->dl; ?></td>
                        <td style="width:5%"><?php echo $r_order->ss == '' ? 'n/a' : $r_order->ss; ?></td>
                        <td style="width:5%"><?php echo $r_order->sl == '' ? 'n/a' : $r_order->sl; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>

    <?php
    /*
      if (!empty($all_order)) {
      ?>
      <table class="orderTbl" style="margin-top:10px; border-radius: 3px 3px 3px 3px; width:100%; float:left; border: 1px solid #cecece;">
      <tbody>
      <tr style="background-color:#F3F4F4;">
      <th style="width:5%">Order ID</th>
      <th style="width:10%">Date</th>
      <th style="width:25%">Sites</th>
      <th style="width:22%">Keyword</th>
      <th style="width:10%">Status</th>
      <th style="width:5%">Action</th>
      <th style="width:5%">DL</th>
      <th style="width:5%">SS</th>
      <th style="width:5%">SL</th>
      </tr>
      <?php foreach ($all_order as $in => $r_order) { ?>
      <tr style="font-size:14px;background-color: <?php echo $in % 2 == 0 ? '#fff' : '#eee' ?>; text-align:center;">
      <td><?php echo $r_order->order_id; ?></td>
      <td><?php echo $r_order->order_date; ?></a></td>
      <td><a style="text-decoration:none;" target="_blank" href="<?php echo $r_order->sites; ?>"><?php echo $r_order->sites; ?></td>
      <td><?php
      $keywords = $r_order->keys;
      echo $keywords;
      ?></td>
      <td>
      <?php
      echo $r_order->status;
      if ($r_order->status_date != "") {
      echo '<br/><span style="font-size:10px;">' . date('d M Y h:i:a', strtotime($r_order->status_date)) . '</span>';
      }
      ?>
      </td>
      <td>
      <?php if ($r_order->status != 'Ordered') { ?>
      <a style="text-decoration: none;" href="<?php echo site_url() . '/order-content/?type=view-content&order_id=' . $r_order->order_id; ?>">View</a>
      <?php } if ($r_order->status == 'Ordered') { ?>
      <a style="text-decoration: none;" href="<?php echo site_url() . '/order-content/?type=edit-order&order_id=' . $r_order->order_id; ?>">Edit</a>
      <?php
      } if ($r_order->status == 'Approved') {
      $already_given_feedback = $wpdb->get_row("SELECT * FROM `wp_feedback` WHERE `order_id` = $r_order->order_id && `sender_user_id` = $UserID");
      if (empty($already_given_feedback)) {
      ?>
      | <a style="text-decoration: none;" href="<?php echo site_url() . '/order-content/?type=feedback&feedback_order_id=' . $r_order->order_id; ?>">Give Feedback</a>
      <?php
      }
      }
      ?>
      </td>
      <td><?php echo $r_order->dl == '' ? 'n/a' : $r_order->dl; ?></td>
      <td><?php echo $r_order->ss == '' ? 'n/a' : $r_order->ss; ?></td>
      <td><?php echo $r_order->sl == '' ? 'n/a' : $r_order->sl; ?></td>
      </tr>
      <?php } ?>
      </tbody>
      </table>
      <style type="text/css">
      .orderTbl th,.orderTbl td{padding:9px 4px}
      </style>
      <?php
      } else {
      echo '<br/><center><h3>You have no previous order.</h3></center>';
      }
     */
    ?>  
</div>
<div id="dwnldContRprtFrm" style="display:none; min-width:340px">
    <form action="" method="post">
        <fieldset class="fieldset_class">
            <legend class="legend_class" style="text-align:center;font-weight:bold;" id="header_text">Download Options</legend>
            <div style="padding:20px;">
                <?php if (!empty($popupErrMsg) || !empty($popupSucsMsg)): ?>
                    <div class="<?php echo empty($popupErrMsg) ? 'sucsMsg' : 'errMsg'; ?>"><?php echo empty($popupErrMsg) ? $popupSucsMsg : $popupErrMsg; ?></div>
                    <div class="clear_both"></div>
                <?php endif; //pr($_POST); ?>
                <div class="left_task"><label for="dwn-from_date">Start Date:</label></div>
                <div class="right_task">
                    <input class="datepicker required" id="dwn-from_date" name="from_date" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : date("m/d/Y", strtotime($download_from_date)); ?>" type="text">
                </div>
                <div class="clear_both"></div>

                <div class="left_task"><label for="dwn-to_date">End Date:</label></div>
                <div class="right_task">
                    <input class="datepicker required" id="dwn-to_date" name="to_date" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : date("m/d/Y", strtotime($download_to_date)); ?>" type="text">
                </div>
                <div class="clear_both"></div>

                <div class="left_task">Type of Content: </div>
                <div class="right_task">
                    <label><input type="checkbox" name="content_type[]" value="Ordered"<?php if (!isset($_POST['content_type']) || in_array('Ordered', $_POST['content_type'])) echo ' checked'; //default;  ?>> Ordered</label><br />
                    <label><input type="checkbox" name="content_type[]" value="Delivered"<?php if (!isset($_POST['content_type']) || in_array('Delivered', $_POST['content_type'])) echo ' checked'; ?>> Delivered</label><br />
                    <label><input type="checkbox" name="content_type[]" value="Approved"<?php if (!isset($_POST['content_type']) || in_array('Approved', $_POST['content_type'])) echo ' checked'; ?>> Approved</label><br />
                    <label><input type="checkbox" name="content_type[]" value="Request Changes"<?php if (!isset($_POST['content_type']) || in_array('Request Changes', $_POST['content_type'])) echo ' checked'; ?>> Request Changes</label>
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <div class="left_task">Download Type: </div>
                <div class="right_task">
                    <label><input type="radio" name="dwnld_type" value="csv"<?php if (!isset($_POST['dwnld_type']) || $_POST['dwnld_type'] == 'csv') echo ' checked'; //default;  ?>> CSV</label>
                    <label><input type="radio" name="dwnld_type" value="pdf"<?php if (isset($_POST['dwnld_type']) && $_POST['dwnld_type'] == 'pdf') echo ' checked'; ?>> PDF</label>
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <div class="left_task">&nbsp;</div>
                <div class="right_task">
                    <input type="submit" class="new_btn_class" name="btn_download-content-report" value="Submit">
                </div>
                <div class="clear_both"></div>
            </div>
        </fieldset>

        <fieldset class="fieldset_class" style="margin-top:20px">
            <legend class="legend_class" style="text-align:center;font-weight:bold;" id="header_text2">Schedule E-Mail Report</legend>
            <div style="padding:20px;">
                <?php
                global $wpdb;
                $sql = "SELECT * FROM {$wpdb->prefix}mcc_sch_settings WHERE sch_uId={$UserID} AND sch_type='content_order'";
                $settingRow = $wpdb->get_row($sql, ARRAY_A, 0);
                if (empty($settingRow)) {
                    //Default Values:
                    //$ContenOCEmail = get_user_meta($UserID, "Order_Content_Email", true); //????????????????????
                    $userInfo = get_userdata($UserID);
                    $settingRow = array(
                        'sch_status' => 0,
                        'sch_outTime' => '06:00:00',
                        'sch_frequency' => 'Daily',
                        'sch_reportVolume' => 'Last 24 Hours',
                        'sch_otherConfig' => array('Ordered', 'Delivered', 'Approved', 'Request Changes'),
                        //'sch_emailTo' => '',//$userInfo->user_email,
                        'emails' => array()
                    );
                } else {
                    //Skip seconds:
                    if (!empty($settingRow['sch_outTime']))
                        $settingRow['sch_outTime'] = '06:00:00';//substr($settingRow['sch_outTime'], 0, 5);

                    if (!empty($settingRow['sch_otherConfig']))
                        $settingRow['sch_otherConfig'] = maybe_unserialize($settingRow['sch_otherConfig']);

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
                <div class="left_task">Send report via email: </div>
                <div class="right_task">
                    <input type="checkbox" name="sh-send-report-via-email" value="1"<?php if ((isset($_POST['sh-send-report-via-email']) && in_array('1', $_POST['sh-send-report-via-email'])) || $settingRow['sch_status']) echo ' checked'; //default; ?>><br />
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>
                
                <div class="left_task">Schedule Report Type: </div>
                <div class="right_task">
                    <input type="radio" name="report_type" <?php if($settingRow['report_type'] == 'pdf') echo 'checked'?> value="pdf"> PDF
                    <input style="margin-left:20px;" type="radio" <?php if($settingRow['report_type'] == 'csv' || $settingRow['report_type'] == '') echo 'checked'?> name="report_type" value="csv"> CSV
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <div class="left_task">E-Mail to: </div>
                <div class="right_task">
                    <div id="mult_email_div" data-indx="<?php echo count($settingRow['emails']); ?>"><?php foreach ($settingRow['emails'] as $indx => $em) { ?>
                            <p>
                                <input class="required" id="sh-email-to-<?php echo $indx; ?>" name="sh-email[<?php echo $indx; ?>][to]" value="<?php echo $em['to']; ?>" type="text">
                                <label><input type="checkbox" name="sh-email[<?php echo $indx; ?>][st]" value="1"<?php checked($em['st'], 1, true); ?>> Active?</label>
                            <?php if ($em['id']): ?><input type="hidden" name="sh-email[<?php echo $indx; ?>][id]" value="<?php echo $em['id']; ?>"><?php endif; ?>
                            </p>
    <?php
}
?>
                    </div>
                    <small>*NB: Unsubscribed email(s) are unchecked. Keep the field(s) empty to delete.</small>
                    <br /><br />
                    <a class="addMore" onclick="add_more_func_em('#mult_email_div')">Add More</a>
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <div class="left_task">When do you wish to receive the report? </div>
                <div class="right_task">
                    <select name="sh-how-often" style="width:180px">
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
                    <br/><small>*NB: Monthly report will be sent 1st of month at 6am EST and <br/> Weekly report will be sent on Monday at 6am EST</small>
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <!--div class="left_task">What time of the day? </div>
                <div class="right_task">
                    <input class="timepick required" id="sh-day-time" name="sh-day-time" value="<?php //echo isset($_POST['sh-day-time']) ? $_POST['sh-day-time'] : $settingRow['sch_outTime']; ?>" type="text"><br />
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div-->

                <div class="left_task">Report Volume: </div>
                <div class="right_task">
                    <select name="sh-volume" style="width:180px">
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
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <div class="left_task">Type of Content: </div>
                <div class="right_task">
<?php
$arr = isset($_POST['sh-content_type']) ? $_POST['sh-content_type'] : $settingRow['sch_otherConfig'];
foreach (array('Ordered', 'Delivered', 'Approved', 'Request Changes') as $t):
    ?>
                        <label><input type="checkbox" name="sh-content_type[]" value="<?php echo $t; ?>"<?php checked((in_array($t, $arr) ? 1 : 0), 1); ?>> <?php echo $t; ?></label><br />
                    <?php endforeach; ?>
                    <div class="clear_both"></div>
                </div>
                <div class="clear_both"></div>

                <div class="left_task">&nbsp;</div>
                <div class="right_task">
                    <input type="submit" class="new_btn_class" name="btn_schedule-content-report" value="Set Schedule">
                </div>
                <div class="clear_both"></div>
            </div>
        </fieldset>
    </form>
</div>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-timepicker-addon.js"></script>
<style type="text/css">
    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { height: 25px; margin-bottom:-25px}
    .ui-timepicker-div dl dd { margin: 0 10px 10px 80px}
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label{margin-left:0 !important}
    .ui-tpicker-grid-label td{text-align:left !important}
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
    /*.timepick{text-align:center}*/
</style>


<script>
     jQuery(document).ready(function() {
        jQuery('.tabl1').dataTable({
            "order": [[0, "desc"]],
            "iDisplayLength": 50

        }); 
    });
</script>
<script type="text/javascript">
                        jQuery(document).ready(function($) {
                          

                            $('#dwnldContRprt').fancybox()<?php if (!empty($popupErrMsg) || !empty($popupSucsMsg)): ?>.trigger('click')<?php endif; ?>;
                                    $(".datepicker").datepicker({
                                changeMonth: true,
                                changeYear: true,
                                yearRange: "-100:+0"
                            });

                            $('.timepick').timepicker({
                                hourGrid: 4,
                                minuteGrid: 15,
                                stepMinute: 15
                            });
                        });

                        function add_more_func_em(_sel) {
                            var indx = jQuery(_sel).data('indx');
                            var html = '<p>' +
                                    '<input class="required" id="sh-email-to-' + indx + '" name="sh-email[' + indx + '][to]" value="" type="text"> ' +
                                    '<label><input type="checkbox" name="sh-email[' + indx + '][st]" value="1" checked="checked" /> Active?</label>' +
                                    '</p>';
                            jQuery(_sel).data('indx', (indx + 1)).append(html);
                        }
</script>
