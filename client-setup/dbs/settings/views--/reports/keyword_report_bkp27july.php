<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/css/theme.blue.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.widgets.min.js"></script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-timepicker-addon.js"></script>
    
<style> 
    
    .topPart{width:100%; padding:20px 0 10px; overflow:hidden}

    .fancybox-overlay .right_task input[type="text"]{width:300px}

    .fancybox-overlay .right_task select{width:314px !important}

    .fancybox-overlay .right_task small{font-size:12px}

    #mult_email_div p{padding-bottom:2px}

    .addMore{padding:3px 6px; border-radius:6px; font-weight:bold; font-size:12px; color:#fff; background:#fb6800; margin-top:4px; display:inline-block; cursor:pointer}

    .addMore:hover{color:#fff; background-color:#e14e00}

    #dwnldContRprt{float:right; margin-right:20px}

    .errMsg, .sucsMsg{color:red; border:1px solid cyan; padding:10px; background:#ffe4c4}

    .sucsMsg{color:green; background-color:#d5f15c;}



    p,.th1,.th2,th,td{font-family:'Open Sans';font-weight:400;margin:0; font-size:13px}

    thead th, thead th.th1, thead th.th2{font-weight:bold}

    thead th.th1{font-size:16px}

    .sectionC p, .sectionC th{font-size:12px}

    .sectionC td{font-size:15px}

    .sectionC td a{font-size:14px}

    .sectionC td.cl-3 i{font-size:19px; font-weight:bold}

    .sectionC td.cl-4 i{font-size:16px; font-weight:bold}

    .sectionC td small{font-size:12px; display:block; padding-top:3px}

    .sectionB th{line-height:17px; padding-top:3px; position:relative}

    .sectionB th{text-align:center}

    .sectionB thead th.th1{text-align:left}

    /*.sectionB tbody th.thr{text-align:right}*/

    table th span.s-icn,

    table td span.s-icn{width:12px; margin-left:4px; display:inline-block}

    .sectionB td span.s-icn{}

    .sectionC td.cl-3 span.s-icn{margin-left:0}

    table th i,

    table td i{min-width:42px; display:inline-block; font-style:normal; text-align:right}

    .sectionC td i{text-align:left}

    .sectionC td.cl-3 i{min-width:25px}

    .sectionC td.cl-4 i{min-width:82px}

    caption, th, td{text-align:center}

    body{margin:0 auto}

    /*table{width:auto !important}*/

    /*.en-right{width:1200px}*/

    .col-3-sm{width:50%;float:left}

    .col-6-sm{width:50%;float:left}

    .col-12-sm{width:100%;float:left;position:relative}

    .sectionA,

    .sectionB{border:1px solid black;border-radius:10px;margin-top:20px; position:relative}

    .sectionA,

    .sectionB{

        width:99%;

        height:175px;

        float:left;

        padding:10px;

        box-sizing:border-box;

    }

    .sectionB{float:right}

    .orank{font-size:70px;margin:0;padding-left:10px}

    .orank span{padding-left:5px}

    .irank{font-size:35px;margin:0;padding-left:5px;vertical-align: top;}

    .irrank{margin-top:10px; font-weight:400; font-size:16px}

    .irank span{padding-left: 10px;}

    .leftbox{width:300px;/*height:200px;*/padding-left:10px}

    .uprightbox{width:275px;height:100px;float:right}

    .botrightbox{width:275px;height:100px;float:right}

    .th1 {width:300px; text-align:left; padding-bottom:20px;}

    .th2 {width:120px; text-align:left; padding-bottom:20px;}

    .th3 {width:120px; text-align:left; padding-bottom:20px;}

    .thk {width:120px; text-align:left;}

    .thr {width:120px; text-align:left;padding-left: 5px;}

    tbody .thb{border-top: 1px solid black;}

    tbody .thb:first-child{border:0}

    table{border-collapse:collapse;}

    #content{

        width:100%;

        box-sizing:border-box;

        overflow:hidden;

    }

    /*.en-right{width:83%}*/

    #dwnldContRprt{float:right}

    .leftbox,

    .botrightbox,

    .uprightbox{

        width:100%;

        box-sizing:border-box;

    }

    .sectionBIn{

        width:100%;

        height:100%;

        padding:0;

        margin:0;

        overflow:auto;

    }

    .en-right.anlRprt table{

        width:100% !important;

        padding:0;

        margin:0;

        box-sizing:border-box;

    }

    .en-right.anlRprt table th{

        padding:5px !important;

        line-height:14px;

        color:#444 !important;

        width:auto !important;

    }

    .sectionC table th{font-weight:bold}

    .sectionC table td,

    .sectionC table th{

        padding:6px;

        border:none;

        text-align:center;

        vertical-align:middle;

    }

    .sectionC table tr td.cl-1,

    .sectionC table tr td.cl-2,

    .sectionC table tr td.cl-3,

    .sectionC table tr td.cl-4{text-align:left}

    .sectionC table tbody td{border-left:1px solid #cdcdcd}

    .sectionC table tbody tr td.cl-1{border-left:none}

    .sectionC table td.cl-3{min-width:75px}

    .sectionC table td.cl-4{min-width:140px}

    .tablesorter-header-inner{text-align:center}

    .sectionC table thead th,

    .tablesorter-sticky-wrapper{background:#ffffff}

    .sectionC table thead th.tablesorter-headerAsc,

    .sectionC table thead th.tablesorter-headerDesc,

    .sectionC table thead th.tablesorter-headerUnSorted{

        padding-right:15px !important;

        vertical-align:middle;

        background-position:right center;

        background-repeat:no-repeat;

        cursor:pointer;

    }
   
    @media screen and (max-width: 1100px){

        .en-right{width:80%}

    }

    @media only screen and (max-width:760px), (min-device-width:768px) and (max-device-width:991px){

        .en-right{width:75%}

        .sectionC table td,

        .sectionC table th{

            text-align:left !important;

            min-height:20px;

        }

        .sectionC table tbody tr td{border-left:none}

        .sectionC table,

        .sectionC table thead,

        .sectionC table tbody,

        .sectionC table th,

        .sectionC table td,

        .sectionC table tr{

            display:block;

        }

        .sectionC table thead tr{

            position:absolute;

            top:-9999px;

            left:-9999px;

        }

        .sectionC table tr{border:1px solid #ccc}

        .sectionC table td{

            border:none;

            border-bottom:1px solid #eee;

            position:relative;

            padding-left:50%;

        }

        .sectionC table td:before {

            font-weight:bold;

            position:absolute;

            top:6px;

            left:6px;

            width:45%;

            padding-right:10px;

        }

        .sectionC table tr td.cl-1:before{content:"Keyword"}

        .sectionC table tr td.cl-2:before{content:"Ranking URL"}

        .sectionC table tr td.cl-3:before{content:"Rank"}

        .sectionC table tr td.cl-4:before{content:"SEOv"}

        .sectionC table tr td.cl-5:before{content:"Organic Visits"}

        .sectionC table tr td.cl-6:before{content:"Organic Conv"}

        .sectionC table tr td.cl-7:before{content:"Conv Rate"}

        .sectionC table tr td.cl-8:before{content:"Avg Monthly Searches"}

        .sectionC table tr td.cl-9:before{content:"Competition"}

        .sectionC table tr td.cl-10:before{content:"Suggested Bid"}

    }

    @media screen and (max-width: 767px){

        #content{padding-left:10px}

        .sectionA,

        .sectionB{width:100%; height:auto}

        .sectionBIn{height:auto}

        .en-right{width:100%}

        .en-right.anlRprt .col-6-sm.sec-full{width:100%}

        #analytics_Frm{

            width:100%;

            overflow:hidden;

        }

        #analytics_Frm > div{margin-right:0 !important}

        #analytics_Frm > div input.datepicker.required{width:68px}

        #analytics_Frm input[type="submit"]{

            width:auto;

            padding:4px 5px 3px;

            margin:0 5px 0 0;

        }

        .en-left{

            width:100%;

            padding-bottom:0;

        }

    }

    @media screen and (max-width: 390px){

        ul.enterprise li:first-child a{

            padding-top:7px;

            padding-bottom:7px;

        }

    }


    .cus-btn{
        width:106px !important;
    }

    .sectionC table thead th.tablesorter-headerUnSorted{

        background-image:url(<?php echo get_template_directory_uri(); ?>/images/sort/bg.gif);

    }

    .sectionC table thead th.tablesorter-headerAsc{

        background-image:url(<?php echo get_template_directory_uri(); ?>/images/sort/desc.gif);

    }

    .sectionC table thead th.tablesorter-headerDesc{

        background-image:url(<?php echo get_template_directory_uri(); ?>/images/sort/asc.gif);

    }

</style>
<?php
$from_date = date('Y-m-d', time() - 31 * 24 * 3600);
$to_date = date('Y-m-d', time() - 2 * 24 * 3600);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['analytics_date_frm_btn'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));    
}

$start = 0;
$limit = 10;

function user_limited_keywords($user_id, $start, $limit) {
    $active_key = array();
    $all_keyword_list = get_user_meta($user_id, "Content_keyword_Site", true);
    if (!empty($all_keyword_list)) {
        for ($i = 1; $i <= $all_keyword_list['keyword_count']; $i++) {
            if (trim($all_keyword_list['LE_Repu_Keyword_' . $i]) != "") {
                if ($all_keyword_list['activation'][$i - 1] != 'inactive') {
                    $active_key[] = trim($all_keyword_list['LE_Repu_Keyword_' . $i]);
                }
            }
        }
    }
    return $active_key;
}

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
    $UserID = $location->MCCUserId;
    $website = get_user_meta($UserID, 'website', TRUE);
    $download_from_date = date('Y-m-d', time() - 30 * 24 * 3600);
    $download_to_date = date("Y-m-d");
    
    $analytics_user_id = analytics_user_id($UserID);
    $target_url = target_url($UserID);
    $page_name = 'keywords-report';
    include_once( get_template_directory() . '/analytics/BrightLocalUtils.php');
    include_once(get_template_directory() . '/analytics/my_functions.php');
    include_once(get_template_directory() . '/common/report-function.php');

    ?>
    <div class="reportdiv">        
        <h5>Keyword Report - <?php echo $website; ?>
            <div class="pull-right locreport"><a href="?parm=execution&function=location_full_report&location_id=<?php echo $location_id; ?>" target="_blank" class="btn btn-primary ">Location Full Report</a></div>
        </h5>
        
        <div class="commnerpoert keyword_report<?php echo $UserID; ?>">
            
            <?php
                $primary_html = '<span style="color:white;background:#22B04B;display:inline-block;padding:3px;height:14px;width:11px;margin-right:6px;">P</span>';
                $secondary_html = '<span style="color:white;background:#FF7F27;display:inline-block;padding:3px;height:14px;width:11px;margin-left: 10px;margin-right:6px;">S</span>';
                $client_website = get_user_meta($UserID, 'website', true);
                $ReportID = get_user_meta($UserID, 'btl_campaign', true);
                if ($ReportID > 0) {
 
                    $last_Adwords_report = row_array("SELECT * FROM `seo` WHERE `MCCUserId` = $UserID order by `DateOfRank` desc LIMIT 1");

                    $last_Adwords_report = $last_Adwords_report['DateOfRank'];

                    $left_rank_update = row_array("SELECT * FROM `seo` WHERE `MCCUserId` = $UserID and `rank_update_date` = '' order by `rank_update_date` desc LIMIT 1");

                }
                
                $sql = "SELECT * FROM `seo` WHERE `MCCUserId` = $UserID order by `DateOfRank` DESC LIMIT 1";
                $rank_update_date = row_array($sql);
                $rank_update_date = $rank_update_date['DateOfRank'];
                
                $schedule_report_page = 'keyword_grouping';
                include_once(get_template_directory() . '/common/schedule-report.php');
                $_SESSION['pdf_report'] = array();
                $_SESSION['csv_report'] = array();
                
            ?>
            
            <div class="portlet light ">                
                
                <div class="portlet-body">
                    <?php
                    
                    $btl_report = get_user_meta($UserID, 'btl_report_result', true);
                    if (empty($btl_report)) {
                        $btl_id = get_user_meta($UserID, 'btl_campaign', true);
                        if ($btl_id > 0) {
                            $btl_report = GetBTLReport($btl_id);
                            update_user_meta($UserID, 'btl_report_result', $btl_report);
                        }
                    }
                    $btl_ranking_type = array('Google', 'Yahoo', 'Bing');
                    $targeting = get_user_meta($UserID, 'adwords-pull', true);
                    if ($targeting == 'local') {
                        $btl_ranking_type = array('Yahoo', 'yahoo-local_<?php echo $location_id ?>', 'Bing', 'bing-local_<?php echo $location_id ?>');
                    }

                    $city = get_user_meta($UserID, "city", true);

                    $txt = "Target National";
                    if ($targeting != "national"){
                        if($city != '')
                            $txt = "Target Local ({$city})";
                    }                                                                
                    
                    include('grouping-keywords-new.php');
                    if(empty($all_active_keywords)){
                        echo "<div class='alert alert-danger'>No Keyword added yet in this location</div>"; 
                    }
                    else{
                    ?>
                    
                    <div class="row">
                        <div class="col-md-6">

                            <div class="btn-group btn-group">
                                <a href="javascript:;" class="btn btn-success search-engine-type_<?php echo $location_id ?> active" data-type="google" onclick="btl_rank_show_func_<?php echo $location_id; ?>('Google', '<?php echo $targeting; ?>')"> Google rank  </a>
                                <a href="javascript:;" class="btn btn-success search-engine-type_<?php echo $location_id ?>" data-type="yahoo" onclick="btl_rank_show_func_<?php echo $location_id; ?>('Yahoo', '<?php echo $targeting; ?>')"> Yahoo rank  </a>
                                <a href="javascript:;" class="btn btn-success search-engine-type_<?php echo $location_id ?>" data-type="bing" onclick="btl_rank_show_func_<?php echo $location_id; ?>('Bing', '<?php echo $targeting; ?>')"> Bing rank  </a>
                            </div>                             

                        </div>
                                                      
                    </div>
                    
                    <div class="row google-rank-block_<?php echo $location_id ?> margin_top_10">
                        
                        <div class="col-md-12">
                            <h4>Google Rank Data <?php echo $txt; ?></h4>                           
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="clearfix margin-bottom-5"> </div>
                                    <?php //echo $pimary_g_html; ?>
                                </div>
                            </div>       
                        </div>

                    </div>
                    <div class="row bing-rank-block_<?php echo $location_id ?> hidden">
                        <div class="col-md-12">
                            <h4>Bing Rank Data <?php echo $txt; ?></h4>                           
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="clearfix margin-bottom-5"> </div>
                                    <?php // echo $pimary_b_html; ?>
                                </div>
                            </div>       
                        </div>                                        
                    </div>
                    <div class="row yahoo-rank-block_<?php echo $location_id ?> hidden">
                        <div class="col-md-12">
                            <h4>Yahoo Rank Data <?php echo $txt; ?></h4>                            
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="clearfix margin-bottom-5"> </div>
                                    <?php //echo $pimary_y_html; ?>
                                </div>
                            </div>       
                        </div>                                        
                    </div>
                    
                    <script>
                        function btl_rank_show_func_<?php echo $location_id ?>(type, targeting) {
                            
                                jQuery('.search-engine-type_<?php echo $location_id ?>').removeClass('active');
                                jQuery('.grouping-box_<?php echo $location_id ?>').removeClass('hidden');                                                                               
                                jQuery('.btn-keyword-history_<?php echo $location_id ?>').removeClass('active');
                                        
                                if (type == 'Google') {
                                        jQuery('#individual_btl_rank').hide();
                                        jQuery('.google-rank-block_<?php echo $location_id ?>').show();
                                        jQuery('.google-rank-block_<?php echo $location_id ?>').removeClass('hidden');
                                        
                                        
                                        jQuery('.yahoo-rank-block_<?php echo $location_id ?>').addClass('hidden');
                                        jQuery('.bing-rank-block_<?php echo $location_id ?>').addClass('hidden');
                                        jQuery('.search-engine-type_<?php echo $location_id ?>[data-type="google"]').addClass('active');
                                        
                                } else {
                                jQuery('#individual_btl_rank_<?php echo $location_id ?>').show();
                                        jQuery('.google-rank-block_<?php echo $location_id ?>').hide();
                                        jQuery('.google-rank-block_<?php echo $location_id ?>').addClass('hidden');
                                }
                                
                                jQuery('.all_btl_report').hide();
                                jQuery('.' + type).show();
                                
                                if (type == 'Bing') {
                                            jQuery('.bing-rank-block_<?php echo $location_id ?>').removeClass('hidden');
                                        jQuery('.yahoo-rank-block_<?php echo $location_id ?>').addClass('hidden');
                                        jQuery('.search-engine-type_<?php echo $location_id ?>[data-type="bing"]').addClass('active');
                                        
                                }
                                if (type == 'Yahoo') {
                                    
                                        jQuery('.yahoo-rank-block_<?php echo $location_id ?>').removeClass('hidden');
                                        jQuery('.bing-rank-block_<?php echo $location_id ?>').addClass('hidden');
                                        jQuery('.search-engine-type_<?php echo $location_id ?>[data-type="yahoo"]').addClass('active');
                                        
                                }

                                if (targeting == 'local') {
                                if (type == 'Bing') {
                                jQuery('.bing-local_<?php echo $location_id ?>').show();
                                }
                                if (type == 'Yahoo') {
                                jQuery('.yahoo-local_<?php echo $location_id ?>').show();
                                }
                                }

                                jQuery('.rank_data_name_<?php echo $location_id ?>').html(type);
                           }
                    </script>
                    
                    <?php } ?>
                </div>
            </div>
            
            
        </div> 
    </div>    


    <?php
    
    
    
    
}
