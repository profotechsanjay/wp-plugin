<?php

$style = '<style>
    table.tabl-sort thead th.tablesorter-headerAsc,
    table.tabl-sort thead th.tablesorter-headerDesc,
    table.tabl-sort thead th.tablesorter-headerUnSorted{

        padding-right:15px !important;
        vertical-align:middle;
        background-position:right center;
        background-repeat:no-repeat;
        cursor:pointer;

    }
    table.tabl-sort thead th.tablesorter-headerUnSorted{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/bg.gif);
    }
    table.tabl-sort thead th.tablesorter-headerAsc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/desc.gif);
    }
    table.tabl-sort thead th.tablesorter-headerDesc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/asc.gif);
    }
</style>';
$db_report_name = ST_CONVERSION_REPORT;

include_once get_template_directory() . '/common/schedule-report.php';
include get_template_directory(). '/common/report-function.php';
include_once(get_template_directory() . '/analytics/my_functions.php');
include_once(SET_COUNT_PLUGIN_DIR . '/custom_functions.php');
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';

if (isset($_POST['btn_download-report'])) {
    
    ob_end_clean();
    $fromdate = $_POST['fromdate'];
    $todate = $_POST['todate'];
    $fromdate = date('Y-m-d', strtotime($fromdate));
    $todate = date('Y-m-d', strtotime($todate));
    
    if ($_POST['dwnld_type'] == 'pdf') {
        $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                .keyword_width{width:20%;} .text-center{S;} 
                .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                td.text-center { text-align: center; padding: 10px 5px; } .tbl{text-align: center; border: 1px solid #ddd;
                padding: 10px; width: 300px;  display: inline-block; margin-right: 15px;} .col-md-4 .col-md-12{padding: 10px;}
                            .location {padding: 15px; width: 100%; margin-bottom: 20px; } td, th {
                padding: 6px;
            } .text-right {
                text-align: center;
                margin-bottom: 13px;
            } </style>';
        
        $str .= rd_pdf_header();        
        $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Conversion Report</h3><br/>';  
        
        $str .= '<div class="content">';
        
        foreach ($locations as $location) {
                
                $location_id = $location->id;
                $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
                $client_website = $website = get_user_meta($UserID, 'website', TRUE);
                $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);        
                
                $str .= '<fieldset class="location">
                <div><h4>Location : '.$brand.' ( '.$website.' )</h4></div>';
                
                $str .= '
                <div class="reportdiv">';
                    
                    if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'")) == 0) {
                        
                        $str .= '<div class="alert alert-danger">Table Not Found </div>';                        
                    }
                    else{
                        
                        $all_conv_landing_page = conversions_report($analytics_user_id, $fromdate, $todate);
                        $conversions_source_report = conversions_source_report($analytics_user_id, $fromdate, $todate, $all_conv_landing_page);                                    
                        
                        $str .= '
                        <div class="row">
                            <div class="col-md-12">
                                <table border="1" cellspacing="0" class="c2" style="position: relative; top: 15px; border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Source</th>
                                            <th style="text-align: center;">Visits</th>
                                            <th style="text-align: center;">Conversions</th>
                                            <th style="text-align: center;">Conversions Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:20px!important;">';
                                        
                                        $colors = array("#55BF3B", "#DF5353", "#7798BF", "#ff0066", "#8d4654", "#f45b5b", "#8085e9", "#7798BF", "#aaeeee", "#aaeeee", "#eeaaee");
                                        $i = 0;
                                        foreach ($conversions_source_report as $row_source_report) {
                                            $cols = isset($colors[$i]) ? 'color:' . $colors[$i] . '; background:' . $colors[$i] : '';
                                            $str .='
                                            <tr>   
                                                <td><i class="fa fa-bar-chart fa-1x" style="'.$cols.'"></i> '.$row_source_report['name'].'</td>
                                                <td style="text-align: center;">'.$row_source_report['all_visit'].'</td>
                                                <td style="text-align: center;">';
                                                    
                                                    $str .= $row_source_report['all_conv'];
                                                    $total_conversions += $row_source_report['all_conv'];
                                                    
                                            $str .= '</td><td style="text-align: center;">'.$row_source_report['conv_rate'].'</td></tr>';                                            
                                            $i++;
                                        }
                                        $str .='
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br /><br />      

                        <table border="1" cellspacing="0" class="c2" style="position: relative; top: 15px; border-color: #ddd; margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Landing Page</th>
                                    <th style="text-align: center;">Visits</th>                                    
                                    <th style="text-align: center;">Conversions</th>
                                    <th style="text-align: center;">Conversions Rate </th>
                                </tr>
                            </thead>
                            <tbody style="font-size:20px!important;">';
                                        
                                foreach ($all_conv_landing_page as $row_con) {     
                                    
                                    $str .= '<tr>   
                                        <td>                                            
                                            <a href="'.site_url().'/url-profile/?url='.$row_con['url'].'">'.$row_con['url'].'</a>  
                                        </td>
                                        <td style="text-align: center;">
                                            '.$row_con['total_visits'].'</td>
                                        <td style="text-align: center;">
                                            '.$row_con['total_conv'].'</td>
                                        <td style="text-align: center;">
                                            '.sprintf("%.2f", ($row_con['total_conv'] * 100) / $row_con['total_visits'], 2) . '%'.'</td>
                                    </tr>';
                                }
                            $str .= '</tbody>
                        </table>';
                        
                    }
                                                      
                $str .= '</div>';
                $str .='<div style="clear:both;height:20px;"></div></fieldset>';
            }
        
        $str .= '</div>';
        
        require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($str);
        $dompdf->set_paper('A4','landscape');        
        $dompdf->render();
        
        $user_id = $UserID;
        
        include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
        $pdf = $dompdf->output();
        $dompdf->stream("conversion_report.pdf", array("Attachment" => true));
        exit;        
    }
    else if ($_POST['dwnld_type'] == 'csv') {
        
        $FilePath = "conversion_report.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename=' . $FilePath);    
        ob_clean();
        $fp = fopen('php://output', "w");
        
        $headertop = array('','','Source','Visits','Conversions','Conversions Rate');
        $headerbott = array('','','Landing page','Visits','Conversions','Conversions Rate');        
        $headerempty = array('','','','','','');
        
        foreach ($locations as $location) {

            $location_id = $location->id;
            $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
            $client_website = $website = get_user_meta($UserID, 'website', TRUE);
            $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);        
            
            $headerlocname = array('Location',$brand.' ('.$website.')','','','','');
            fputcsv($fp, $headerlocname);
            
            if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'")) == 0) {
                
                $headernotbl = array('','','Table does not exist','','','');
                fputcsv($fp, $headernotbl);                
            }
            else{

                $all_conv_landing_page = conversions_report($analytics_user_id, $fromdate, $todate);
                $conversions_source_report = conversions_source_report($analytics_user_id, $fromdate, $todate, $all_conv_landing_page);                                    
                
                fputcsv($fp, $headerempty);
                fputcsv($fp, $headertop);
                fputcsv($fp, $headerempty);
                foreach ($conversions_source_report as $row_source_report) {                                        
                    $arrtoptbl = array('','',$row_source_report['name'],$row_source_report['all_visit'],$row_source_report['all_conv'],$row_source_report['conv_rate']);                    
                    fputcsv($fp, $arrtoptbl);
                }
                fputcsv($fp, $headerempty);
                fputcsv($fp, $headerempty);               
                fputcsv($fp, $headerbott);
                fputcsv($fp, $headerempty);
                foreach ($all_conv_landing_page as $row_con) {                         
                    $percent = sprintf("%.2f", ($row_con['total_conv'] * 100) / $row_con['total_visits'], 2).'%';
                    $arrbottbl = array('','',$row_con['url'],$row_con['total_visits'],$row_con['total_conv'],$percent);
                    fputcsv($fp, $arrbottbl);
                }
                if(empty($all_conv_landing_page)){
                    $arrbottbl = array('','','No Landing page Found','','','');
                    fputcsv($fp, $arrbottbl);
                }                    
            }

            fputcsv($fp, $headerempty);
            fputcsv($fp, $headerempty);
            fputcsv($fp, $headerempty);
            fputcsv($fp, $headerempty);
            fputcsv($fp, $headerempty);            
        }
        
        ob_flush(); 
        fclose($fp);
        exit;
        
    }
    
}
else if (isset($_POST['btn_schedule-executive-report'])) {
    
    $shEmails = array_filter($_POST['sh-email'], function($a) { //IF PHP >=3.5 ELSE create_function('$a','!empty($a["to"]) && empty($a["id"]);')
        //if 'id'-key is set, then do not remove/filter this item from the array. The 'id'-key items will be handled differently to delete or edit;
        return !empty($a["to"]) || !empty($a["id"]);
    });
    if (empty($shEmails)) {
        $popupErrMsg = 'Please type your email';        
        
    } else {
        
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $sql = "SELECT sch_id FROM {$wpdb->prefix}mcc_sch_settings WHERE sch_type='$db_report_name'";
        $settingId = $wpdb->get_var($sql);        
        $sch_status = empty($_POST['sh-send-report-via-email']) ? 0 : 1;
        $schData = array(
            'sch_frequency' => $_POST['sh-how-often'],
            'sch_reportVolume' => $_POST['sh-volume'],
            'report_type' => $_POST['report_type'],           
            'sch_status' => $sch_status,		            
            'sch_type' => $db_report_name,
            'sch_uId' => $user_id,
        );
        
        if (empty($settingId)) {
            $queryStatus = $wpdb->insert(
            	"{$wpdb->prefix}mcc_sch_settings",
				$schData,
				array('%s', '%s', '%s', '%d', '%s', '%d')
            );

            if ($queryStatus === false) {
                $popupErrMsg = 'Invalid Query! Please try again.';

            } else {
                $settingId = $wpdb->insert_id;
                $popupSucsMsg = 'Email scheduler is successfully added';
                unset($_POST);

            }
        } else {
                                    
            $tbl = $wpdb->prefix."mcc_sch_settings";           
            $queryStatus = $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . $tbl . " SET sch_frequency = %s, report_type = %s, sch_reportVolume = %s, sch_status = %s "
                        . "WHERE sch_id = %d", 
                        $_POST['sh-how-often'], $_POST['report_type'], $_POST['sh-volume'], $sch_status, $settingId
                )
            );
                            
            if ($queryStatus === false) {
                $popupErrMsg = 'Invalid Query! Please try again';

            } else {
                $popupSucsMsg = 'Email scheduler is successfully updated';
                unset($_POST);

            }
        }
        
         //Finally process emails:
        if (!empty($settingId)) {
            $queryStatus2 = array();
            foreach ($shEmails as $em) {
                if (empty($em['id'])) {
                    $queryStatus2[] = $wpdb->insert(
                        "{$wpdb->prefix}mcc_sch_emails",
						array(
							'em_sch_id' => $settingId,
							'em_emailTo' => $em['to'],
							'em_status' => $em['st']
                        ),
						array('%d', '%s', '%d')
                    );

                } else {
                    if (empty($em['to'])) {
                        $queryStatus2[] = $wpdb->delete(
                        	"{$wpdb->prefix}mcc_sch_emails",
							array('em_id' => $em['id']),
							array('%d')
                        );

                    } else {
                        $queryStatus2[] = $wpdb->update(
                            "{$wpdb->prefix}mcc_sch_emails",
							array(
								//'em_sch_id'		=> $settingId,
								'em_emailTo' => $em['to'],
								'em_status' => $em['st']
                            ),
							array('em_id' => $em['id']),
							array('%s', '%d'),
							array('%d')
                        );
						
                    }
                }
            }			
        }
        
        
    }
}


$from_date = date('Y-m-d', time() - 31 * 24 * 3600);
$to_date = date('Y-m-d', time() - 2 * 24 * 3600);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['analytics_date_frm_btn'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));
    if (isset($_POST['min_organic_visitors'])) {
        $min_organic_visitors = $_POST['min_organic_visitors'];
    }
}

echo $style;
?>

<div class="dateformdiv">
    <form id="analytics_Frm" action="" method="post" class="form-inline">        
        <label for="from" class="control-label"><b>From</b></label>
            <input type="text" name="from_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($from_date)); ?>">
            <label for="to"><b>To</b></label>
            <input type="text" name="to_date" class="form-control datepicker required" size="10" value="<?php echo date("m/d/Y", strtotime($to_date)); ?>">
        <input type="submit" class="btn btn-success"  style="background:none;" name="analytics_date_frm_btn" value="Submit">

    </form>
</div>

<?php      
foreach ($locations as $location) {

    $location_id = $location->id;
    $analytics_user_id = $user_id = $UserID = $location->MCCUserId;
    $client_website = $website = get_user_meta($UserID, 'website', TRUE);
    $brand = get_user_meta($UserID, 'BRAND_NAME', TRUE);        
    
    ?>
    <div class="reportdiv">        
        <h5>Conversion Report - <?php echo $brand. ' ( '.$website.' )'; ?>
            <div class="pull-right locreport"><a href="?parm=execution&function=conversion_report&location_id=<?php echo $location_id; ?>" target="_blank" class="btn btn-primary ">Location Full Report</a></div>
        </h5>
        <?php
        
        if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'short_analytics_" . $analytics_user_id . "'")) == 0) {
            ?>
            <div class='alert alert-danger'>Table Not Found </div>
            <?php
        }
        else{
        $all_conv_landing_page = conversions_report($analytics_user_id, $from_date, $to_date); 
        
        $conversions_source_report = conversions_source_report($analytics_user_id, $from_date, $to_date, $all_conv_landing_page);                                    

        $start_date = $from_date;
        $end_date = $to_date;
        while ($start_date <= $end_date) {
            $dates_arr[] = $start_date;
            $date_str .= '"' . date("M d", strtotime($start_date)) . '",';
            $start_date = date('Y-m-d', strtotime('+1 days', strtotime($start_date)));
        }
       
        ?>
        <div class="row">
            <div class="col-md-12">
                <table class="tabl1 table table-striped table-bordered table-hover tabl-sort tabl-sort-1" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th style="text-align: center;">Visits</th>
                            <th style="text-align: center;">Conversions</th>
                            <th style="text-align: center;">Conversions Rate</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:20px!important;">
                        <?php
                        $colors = array("#55BF3B", "#DF5353", "#7798BF", "#ff0066", "#8d4654", "#f45b5b", "#8085e9", "#7798BF", "#aaeeee", "#aaeeee", "#eeaaee");
                        $i = 0;

                        foreach ($conversions_source_report as $row_source_report) {
                            ?>
                            <tr>   
                                <td><i class="fa fa-bar-chart fa-1x" style=" <?php echo isset($colors[$i]) ? 'color:' . $colors[$i] . '; background:' . $colors[$i] : ''; ?>"></i> <?php echo $row_source_report['name']; ?></td>
                                <td style="text-align: center;"><?php echo $row_source_report['all_visit']; ?></td>
                                <td style="text-align: center;">
                                    <?php
                                    echo $row_source_report['all_conv'];
                                    $total_conversions += $row_source_report['all_conv'];
                                    ?>
                                </td>
                                <td style="text-align: center;"><?php echo $row_source_report['conv_rate']; ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br /><br />      

        <table class="tabl2 table table-striped table-bordered table-hover tabl-sort tabl-sort-2" cellspacing="0">
            <thead>
                <tr>
                    <th>Landing Page</th>
                    <th style="text-align: center;">Visits</th>
                    <!--<th style="text-align: center;">Form Page Visits</th>-->
                    <th style="text-align: center;">Conversions</th>
                    <th style="text-align: center;">Conversions Rate </th>
                </tr>
            </thead>
            <tbody style="font-size:20px!important;">
                <?php foreach ($all_conv_landing_page as $row_con) { ?>    
                    <tr>   
                        <td>
                            <?php ?>
                            <a href="<?php echo site_url(); ?>/url-profile/?url=<?php echo $row_con['url']; ?>">
                                <?php
                                echo $row_con['url'];
                                ?>
                            </a>  
                        </td>
                        <td style="text-align: center;">
                            <?php
                            echo $row_con['total_visits'];
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php
                            echo $row_con['total_conv'];
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php
                            echo sprintf("%.2f", ($row_con['total_conv'] * 100) / $row_con['total_visits'], 2) . '%';
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php        
        }
        ?>
    </div>
    <?php    
}

?>
<script type="text/javascript">
    
jQuery(document).ready(function(){
    
   jQuery('.tabl-sort-1').tablesorter({
        sortList: [[2, 1]], // etc. [[4, 1]]
    });
    jQuery('.tabl-sort-2').tablesorter({
        sortList: [[1, 1]], // etc. [[4, 1]]
    });
   
});
        
</script>
<?php include_once 'download_report.php'; ?>