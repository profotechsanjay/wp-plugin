<?php
global $wpdb;
include_once 'common.php';
global $current_user;

$msg = '';

$locations = $wpdb->get_results
(
    $wpdb->prepare
    (
        "SELECT * FROM " . client_location() . " WHERE status = 1 ORDER BY created_dt DESC", ""
    )
);

$stle= '<style>

    .sectionC table thead th.tablesorter-headerAsc,
    .sectionC table thead th.tablesorter-headerDesc,
    .sectionC table thead th.tablesorter-headerUnSorted{

        padding-right:15px !important;
        vertical-align:middle;
        background-position:right center;
        background-repeat:no-repeat;
        cursor:pointer;

    }

    .white-table thead th{ color:#FFFFFF; }

    .sectionC table thead th.tablesorter-headerUnSorted{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/bg.gif);
    }
    .sectionC table thead th.tablesorter-headerAsc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/desc.gif);
    }
    .sectionC table thead th.tablesorter-headerDesc{
        background-image:url('.site_url().'/wp-content/themes/twentytwelve/images/sort/asc.gif);
    }

    .cus-btn
    {
        width:106px !important;
    }
    td i.fa-bar-chart{
        margin-right: 5px;
    } 
    
</style>';

$base_url = site_url();

?>
<?php if($msg != ''): ?>
    <div class="msg"> <?php echo $msg; ?> </div>
<?php endif; ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/css/theme.blue.min.css">
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/analytics/tablesorter_dist/js/jquery.tablesorter.widgets.min.js"></script>    
<div class="panel panelmain">
    <div class="">
        <div class="contaninerinner">         
            <h4>Master Admin Reports</h4>     
            <div class="row">
                <div class="col-lg-4">
                    <select class="form-control" name="reportsdd" id="reportsdd">                              
                        <option <?php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'executive_summary'?'selected="selected"':''; ?> value="executive_summary">Executive Summary Report</option>
                        <option <?php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'keyword'?'selected="selected"':''; ?> value="keyword">Keyword Report</option>                        
                        <option <?php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'traffic'?'selected="selected"':''; ?> value="traffic">Traffic Report</option>
                        <option <?php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'conversion'?'selected="selected"':''; ?> value="conversion">Conversion Report</option>
                        <option <?php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'target_ranking'?'selected="selected"':''; ?> value="target_ranking"> Target vs Ranking Report</option>
                        <option <?php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'competitor'?'selected="selected"':''; ?> value="competitor">Competitor Report</option>                        
                        <!--<option <php echo isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'kpi_report'?'selected="selected"':''; ?> value="kpi_report">KPI Report</option>-->
                    </select>
                </div>
                <div class="col-lg-4"></div>
                <div class="col-lg-4">
                    <div class="pull-right">
                        
                        <?php 
                        
                        if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'traffic'){
                                 ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success admin_dwnld_rprt">Download Traffic Report</a>
                                <?php
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'conversion'){
                                 ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success admin_dwnld_rprt">Download Conversion Report</a>
                                <?php
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'target_ranking'){
                                 ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success admin_dwnld_rprt">Download Rank Vs Target Report</a>
                                <?php
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'competitor'){
                                ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success admin_dwnld_rprt">Download Competitor Report</a>                                
                                <?php
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'keyword'){
                                 ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success admin_dwnld_rprt">Download Keyword Grouping Report</a>                                
                                <?php
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'kpi_report'){
                                ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success dwnld-conv-rprt">Download KPI Report</a>                                
                                <?php
                            }                            
                            else{
                                ?>
                                <a href="javascript:void(0);" class="pull-right btn btn-success admin_dwnld_rprt">Download Executive Summary Report</a>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="row"><hr/></div>
            </div>
            <div class="row margin_top_10">
                <div class="col-lg-12 margin_top_10">
                     
                        <?php
                        if(empty($locations)){
                            echo "No Location Found";
                        } else {
                                
                            if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'traffic'){
                                include_once 'reports/traffic.php';
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'conversion'){
                                include_once 'reports/conversion.php';
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'target_ranking'){
                                include_once 'reports/target_ranking.php';
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'competitor'){
                                include_once 'reports/competitor.php';
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'keyword'){
                                include_once 'reports/keyword_report.php';
                            }
                            else if(isset($_REQUEST['report-type']) && $_REQUEST['report-type'] == 'kpi_report'){
                                include_once 'reports/kpi_report.php';
                            }                            
                            else{                                
                                include_once 'reports/executive_summary.php';
                            }
                                                        
                        }
                        ?>                       
                </div>                
            </div>            
        </div>
    </div>
</div>