<?php
login_check();
wp_enqueue_style('bootstrap', TR_COUNT_PLUGIN_URL .'/assets/css/bootstrap.css','', TT_VERSION);
wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
wp_enqueue_style('recom_style.css', TR_COUNT_PLUGIN_URL .'/assets/css/recom_style.css','', TT_VERSION);
wp_enqueue_style('chosen.css', TR_COUNT_PLUGIN_URL .'/assets/css/chosen.css');  

wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', TT_VERSION);
wp_enqueue_script('chosen.jquery.js', TR_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', TT_VERSION);

global $wpdb;
$user_id = $UserID = $current_id = user_id();

$ga = 0;
$hasgaconn = get_user_meta($user_id,'ga_connected',true);
if($hasgaconn == 1){
    $ga = 1;
}

$ct = 0;
$conversiontrack = get_user_meta($user_id,'tracking_code',true);
if($conversiontrack == 1){
    $ct = 1;
}

// temporary check, comment line once GA and Conversion checked
$ga = 1; $ct = 1; 

include_once get_template_directory() . '/analytics/AdWordsUtils.php';
include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
include_once get_template_directory() . '/analytics/AnalyticsUtils.php';

$location_web = get_user_meta($UserID,'website',TRUE);
$location_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);

$gif = TR_COUNT_PLUGIN_URL."/assets/img/ajax_loading.gif";
$onimg = TR_COUNT_PLUGIN_URL."/assets/images/on.png";
$ofimg = TR_COUNT_PLUGIN_URL."/assets/images/off.png";


/* Target Pages */

$keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
if (!empty($keywordDat)) {        

    $activation = $keywordDat["activation"];
    $target_keyword = $keywordDat["target_keyword"];
    $delete = $keywordDat["delete"];
    $landingpage = $keywordDat["landing_page"];

} else {
    $keywordDat['keyword_count'] = 0;
}


$order_by_con = 'desc';
$sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" order by `meta_value` desc';
$KeyWordQuery = $wpdb->get_results($sql);     
$target_pages = array();
foreach ($KeyWordQuery as $row_key) {    
    $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
    $j = $ks - 1;
    if ($delete[$j] == 0) {   
        
        if ($activation[$j] != 'inactive') {            
            $page = trim(trim($landingpage[$j][0]),"/");            
            if($page != '' && !in_array($page, $target_pages)){
                array_push($target_pages, $page);
            }
        }
    }
}
//$target_pages = array_unique($target_pages);
//$sql = 'SELECT meta_key, meta_value FROM wp_usermeta WHERE user_id = ' . $UserID . ' and meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
//$null_key_index = $wpdb->get_results($sql);


/* Target Pages */


$rcommend = $wpdb->get_row
(
    $wpdb->prepare
    (
        "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
    )
);


//$url = get_user_meta($user_id,'website',true);

//$headers = implode(" ", get_headers("https://www.enfusen.com/"));
//$headers = explode("charset=", $headers);  
//$encoding = trim($headers[1]);
//pr($encoding); die;
//$res = crawl_page($url);
//$urls = $res['urls'];
//$ar = array();

//
//$res['urls'] = $ar;
//$crawl_result = json_encode($res);
//
//$x = $wpdb->query
//        (
//            $wpdb->prepare
//            (
//            "UPDATE wp_content_recommend SET crawl_result = %s WHERE user_id = %d", 
//             $crawl_result, $user_id
//            )
//        );

//require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
//$browser = &new SimpleBrowser();
//$urls = $crwalres = json_decode(stripcslashes($rcommend->crawl_result));
//$urls = $urls->urls;
//$urls = array_slice($urls, 0, 10);
//$outerar = array(); 
//
//
//$total_title_issues = 0; $total_meta_issues = 0; $total_content_issues = 0;
//$total_heading_issues = 0; $total_link_issues = 0; $total_image_issues = 0;
//
//foreach($urls as $url){        
//   
//    $urlstrtosend = $url->url;        
//    $data = array(                
//        'url' => $urlstrtosend,
//        'keyword' => 'data insights'
//    );    
//    $data = arrtoobj($data);    
//    $analysis = page_analysis($data,$browser);
//    
//    $total_title_issues = $total_title_issues + $analysis['issues_count']['title_issues'];
//    $total_meta_issues = $total_meta_issues + $analysis['issues_count']['meta_issues'];
//    $total_content_issues = $total_content_issues + $analysis['issues_count']['content_issues'];
//    $total_heading_issues = $total_heading_issues + $analysis['issues_count']['heading_issues'];
//    $total_link_issues = $total_link_issues + $analysis['issues_count']['link_issues'];
//    $total_image_issues = $total_image_issues + $analysis['issues_count']['image_issues'];
//    
//    $data->analysis = $analysis;    
//    array_push($outerar, $data);
//    
//}
//
//$outerar['total_title_issues'] = $total_title_issues;
//$outerar['total_meta_issues'] = $total_meta_issues;
//$outerar['total_content_issues'] = $total_content_issues;
//$outerar['total_heading_issues'] = $total_heading_issues;
//$outerar['total_link_issues'] = $total_link_issues;
//$outerar['total_image_issues'] = $total_image_issues;
//
//$final_result = json_encode($outerar);
//
//
//$x = $wpdb->query
//(
//    $wpdb->prepare
//    (
//    "UPDATE wp_content_recommend SET result = %s WHERE user_id = %d", 
//     $final_result, $user_id
//    )
//);
//
//die;

//$crawlres = json_decode($rcommend->result);
//pr(json_decode($rcommend->crawl_result)->urls); die;


/* // code commented for while
if(isset($rcommend) && $rcommend->trigger_report == 1){
    $rcommen = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT * FROM wp_content_recommend_hisory  WHERE user_id = %d", $user_id
        )
    ); 
    
    $rslt = json_decode(($rcommen->result)); 
    if(!empty($rcommen) && !empty($rslt)){
        $rcommend = $rcommen;
    }
            
}*/


$rs = json_decode(($rcommend->result)); 
$rscnt = (array) $rs;

$issuelist = array('total_title_issues','total_meta_issues','total_content_issues','total_heading_issues','total_link_issues','total_image_issues');
$totalissues = 0;
$issues = array(
    'title' => array('color' => '#2b94e1', 'id' => 'titleid'),
    'meta' => array('color' => '#a52600', 'id' => 'metaid'),
    'content' => array('color' => '#bf9e6b', 'id' => 'contentid'),
    'heading' => array('color' => '#4fae33', 'id' => 'headingid'),
    'link' => array('color' => '#ff7f00', 'id' => 'linkid'),
    'image' => array('color' => '#6666cc', 'id' => 'imageid')
);
$totalurls = 0;
if(count($rscnt) > count($issues)){
    $totalurls = $cnt = count($rscnt) - count($issues);
}


$title = 0; $meta = 0; $content = 0; $heading = 0; $link = 0; $image = 0;                            
                                                        
$rs = (array) $rs;

$reference_array = array();

$tarpages = ""; $allpages = "";

foreach($rs as $key => $row) {                                        
    if(in_array($row, $issuelist)){
        $title = $title + $row->analysis->issues_count->title_issues;
        $meta = $meta + $row->analysis->issues_count->meta_issues;
        $content = $content + $row->analysis->issues_count->content_issues;
        $heading = $heading + $row->analysis->issues_count->heading_issues;
        $link = $link + $row->analysis->issues_count->link_issues;
        $image = $image + $row->analysis->issues_count->image_issues;
        
        $tarpages .= "";
        
    }
    $reference_array[$key] = $row->total_issues;
}

array_multisort($reference_array, SORT_DESC, $rs);

//pr($rs);
$issues['title']['value'] = $title;
$issues['meta']['value'] = $meta;
$issues['content']['value'] = $content;
$issues['heading']['value'] = $heading;
$issues['link']['value'] = $link;
$issues['image']['value'] = $image;                                

$totalissues = 0;
foreach($issues as $issue){
    $totalissues = $totalissues + $issue['value'];
}

?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/highcharts/js/highcharts.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/highcharts/js/modules/data.js"></script>

<input type="hidden" id="hidcontentrec" name="hidcontentrec" value="1" />
<input type="hidden" id="hidgif" name="hidgif" value="<?php echo $gif; ?>" />

<input type="hidden" id="onimg" name="onimg" value="<?php echo $onimg; ?>" />
<input type="hidden" id="ofimg" name="ofimg" value="<?php echo $ofimg; ?>" />
<style>
    td.center, th.center{
      text-align: center;  
    }
    .notebottom {
        margin: 20px 0 0 0;
        padding: 3px 8px 3px 8px;
    }
    .list-group-item{
        margin: 0 0 -1px 0px !important;
    }
    .dropdown-menu{
        top: inherit !important;
    }
    .margin_bottom_10{
        margin-bottom: 10px;
    }
    .nexturlsdisb, .nexturlsdisb:active, .nexturlsdisb:focus {
        background: #D6D6D6 !important;
        pointer-events: none;
        cursor: not-allowed;
    }
    h4{
        font-size: 18px !important;
        font-weight: 600;
    }
    .ddlocs{
        max-width: 200px;
    }
    .addurl{
        max-width: 300px;
    }
    .chosen-container.chosen-container-single{
        width: 200px;
    }
    
    .percentbise {
        position: absolute;        
        top: 0;                
        text-align: center;
        width: 100%;
        z-index: 9999999999;        
        color: #fff;
        font-size: 18px;
        font-weight: bold;
    }
    label.rightlblsts {
        float: right;
    }
    .rightlblsts img, .imgoffon{
        max-width: 20px !important;
    }
    .imgdiv span{
        font-size: 14px;
    }
    .firstspan{
        min-width: 200px;
        display: inline-block;
        vertical-align: top;
        font-size: 14px;
        font-weight: 600;
    }
    .secondspan{
        min-width: 300px;
        display: inline-block;
    }
    .secondspan span {
        position: absolute;
    }
    .tblrecomconettn td, .tblrecomconettn th{
        text-align: center;
    }
    .secondspan div{
        display: inline-block; margin-left: 20px;
    }
    .htagp {
        margin: 5px 0 0 0 !important;
    }
    .noresultcre {
        margin: 70px 0 0 0;
        /* display: inline-block; */
        text-align: center;
        font-size: 20px;
    }
    
    
</style>
<div class="msg"></div>
<div class="main-wrapper">
    <div class="upper-bar">
        <?php
        if($rcommend->trigger_report == 1){
            ?>              
            <div class="alert alert-info">
              <h6><strong> Alert ! </strong>Your campaign for content recommendation is running. It may take some hours. You will be notified as soon as campaign completed</h6>
            </div>
        <?php } ?>
        
    </div>
    <div class="main-sction">
                      
        <div class="title-section">
            <h3><span>Content Recommendations :</span> <?php echo $location_name; ?></h3>
            <div class="right-btn">

                <a href="javascript:;" class="re-compain runcampaign <?php echo $rcommend->trigger_report == 1?'nexturlsdisb':''; ?>"><span class="glyphicon glyphicon-repeat"></span><?php echo !empty($rcommend)?'Re-run campaign':'Run campaign'; ?></a>

                <a href="javascript:;" class="pdf pdfreportgen"><span class="glyphicon glyphicon-log-in"></span> Report</a>
                

            </div>
            <div class="selection-dropdown">               
                <div class="web-link"><?php echo get_user_meta(user_id(),'website',true); ?></div>
                <div class="update">Last update : <?php echo $rcommend->rundate != ''?date('D d M Y, h:i a', strtotime($rcommend->rundate)):'<i>Not Run Yet</i>'; ?></div>
            </div>
        </div>
        <div class="tabs-section">
                    
            <?php if(empty($rcommend) || $rcommend->crawl_result == ''): ?>
            <div class="optimization-section">
                 <div class="noresultcre">
                    No Result
                  </div>
            </div>
            
            <?php else: ?>
                    <?php
                    $crawlres = json_decode(stripcslashes($rcommend->crawl_result));
                    $total_urls = $totalurls;
                    
                    ?>
                    <div class="">
                        <div class="left-div alert alert-danger">

                            <h4>Total <b class="totalissueb"><?php echo $totalissues; ?></b> issues found from <b><?php echo $total_urls; ?></b> pages </h4>                            
                        </div>
                    </div>
                    <div class="idea-frame">

                        <div class="left-div">

                            <h4>Issues Breakdown<span class="glyphicon glyphicon-info-sign"></span></h4>
                            <div id="container_graph" style="height:300px; width: 300px;"></div>
                        </div>

                        <div class="right-div">

                            <ul>

                                <li><span class="tag" style='background-color: <?php echo $issues['title']['color']; ?>'>Ti</span><h4>Title issues <span class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['title']['id']; ?>" class="issues-point"><?php echo $issues['title']['value']; ?></span></li>
                                <li><span class="tag" style='background-color: <?php echo $issues['meta']['color']; ?>'>Me</span><h4>Meta Issues<span class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['meta']['id']; ?>" class="issues-point"><?php echo $issues['meta']['value']; ?></span></li>

                                <li><span class="tag" style='background-color: <?php echo $issues['content']['color']; ?>'>Co</span><h4>Content Issues<span class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['content']['id']; ?>" class="issues-point"><?php echo $issues['content']['value']; ?></span></li>
                                <li><span class="tag" style='background-color: <?php echo $issues['heading']['color']; ?>'>He</span><h4>Heading Issues<span class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['heading']['id']; ?>" class="issues-point"><?php echo $issues['heading']['value']; ?></span></li>
                                
                                <li><span class="tag" style='background-color: <?php echo $issues['link']['color']; ?>'>Li</span><h4>Link issues<span class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['link']['id']; ?>" class="issues-point"><?php echo $issues['link']['value']; ?></span></li>
                                <li><span class="tag" style='background-color: <?php echo $issues['image']['color']; ?>'>Im</span><h4>Image issues<span class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['image']['id']; ?>" class="issues-point"><?php echo $issues['image']['value']; ?></span></li>
                                
                            </ul>

                        </div>

                        <div class="clearfix"></div>
                    </div>

                    <div class="optimization-section">
                        <?php 
                        if(count($rs) > 0){
                            ?>
                            <input type="hidden" id="checkanalyticdt" value="1" />
                            <?php
                        }
                        ?>
                        
                        <h4>TOP pages to optimize</h4>
                        <table class="table tbldashboard">
                            <thead>
                                <tr>
                                    <th class="center">Page <span class="glyphicon glyphicon-info-sign"></span></th>
                                    <th class="center">Organic Visits <span class="glyphicon glyphicon-info-sign"></span></th>
                                    <th class="center">Organic Conversions <span class="glyphicon glyphicon-info-sign"></span></th>
                                    <th class="center">Time On Site <span class="glyphicon glyphicon-info-sign"></span></th>
                                    <th class="center">Bounce Rate <span class="glyphicon glyphicon-info-sign"></span></th>
                                    <th class="center">Issues <span class="glyphicon glyphicon-info-sign"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            <?php
                            $rundate = '2016-07-10'; //$rcommend->rundate;
                            $datevisit = date("Y-m-d",  strtotime("$rundate"));
                            
                            $conn = anconn();
                            $sql = "SELECT PageURL,sum(`Total`) as Total_val, sum(organic) as organic_val, sum(`TimeOnSite`) as TOS_val, sum(`BounceRate`) as bounce_rate_val FROM `short_analytics_$user_id` WHERE `DateOfVisit` >= '$datevisit' AND PageURL != '' group by PageURL order by `DateOfVisit` desc";
                            
                            $result = mysqli_query($conn, $sql);
                            
                            $rows = array();
                            if(isset($result) && !empty($result)){
                                while($row = $result->fetch_object()){

                                    $urlidx = trim(trim(str_replace(array("http://","https://"), array("",""), $row->PageURL),"/"));
                                    $rows["$urlidx"] = $row;
                                }                                  
                            }
                            
                            $sql = "SELECT conv_id, organic,date,url FROM `all_conversions_data` WHERE date >= '$datevisit' AND user_id = $user_id group by url order by date desc";
                            $result = mysqli_query($conn, $sql);                            
                            $convrows = array();
                            if(isset($result) && !empty($result)){
                                while($rowdt = $result->fetch_object()){                                
                                    $urlidx = trim(trim(str_replace(array("http://","https://"), array("",""), $rowdt->url),"/"));
                                    $convrows["$urlidx"] = $rowdt;
                                }
                            }
                            mysqli_close($conn); 
                           
                            $ij = 0;  
                            
                            foreach($rs as $key => $crawlre){
                                
                                if(in_array($crawlre, $issuelist)){
                                    continue;
                                }                                                                                                                              
                                
                                if($ij > 5){
                                    //break;
                                }
                                
                                unset($visits_data); unset($conv_data);
                                $urlcrawl = trim(trim(str_replace(array("http://","https://"), array("",""), $crawlre->url),"/"));
                                
                                if(isset($rows["$urlcrawl"])){
                                    $visits_data = $rows["$urlcrawl"];                                    
                                }
                                
                                if(isset($convrows["$urlcrawl"])){
                                    $conv_data = $convrows["$urlcrawl"];                                    
                                }                                
                                
                                $urlprofilepage = site_url().'/url-profile?url='.$crawlre->url;
                                
                                $totalurlissue = $crawlre->analysis->issues_count->title_issues + $crawlre->analysis->issues_count->meta_issues + 
                                        $crawlre->analysis->issues_count->content_issues + $crawlre->analysis->issues_count->heading_issues + 
                                        $crawlre->analysis->issues_count->link_issues + $crawlre->analysis->issues_count->image_issues;
                                
                                $datafetched = 1;
                                
                                if(isset($crawlre->analysis->is_running) && $crawlre->analysis->is_running == 1){
                                    $datafetched = 0;
                                }
                                
                                ?>                                
                                <tr class="rowidx<?php echo $ij; ?>" >
                                    <td class="first-link"><?php print_r($crawlre->url); ?></td>
                                    <td class="center"><?php echo isset($visits_data)?$visits_data->organic_val:0; ?></td>
                                    <td class="center"><?php echo isset($conv_data)?$conv_data->organic:0; ?></td>
                                    <td class="center"><?php echo isset($visits_data)?formatsecondsToMinSec2($visits_data->TOS_val/$visits_data->Total_val):0; ?></td>
                                    <td class="center"><?php echo isset($visits_data)?formatpercent(($visits_data->bounce_rate_val/$visits_data->Total_val)*100):"0%"; ?></td>
                                    <td class="center last-link">                                        
                                        <a data-idx="<?php echo $key; ?>" href="<?php echo $urlprofilepage; ?>" class="anchrissues btn-success <?php echo $datafetched == 0?'hidden':''; ?>"><?php echo $totalurlissue; ?> issues</a>
                                        <span data-idx="<?php echo $key; ?>" class="sploader <?php echo $datafetched == 1?'hidden':''; ?>"><img src="<?php echo $gif; ?>" /> </span>
                                    </td>
                                </tr>                                
                                
                                <?php
                                    $ij++;
                                }                                                                    
                                ?>
                            </tbody>
                        </table>


                        <div class="view-bt"><a href="<?php echo site_url(); ?>/<?php echo CRE_SLUG; ?>">View all page issues</a></div>

                        <div class="clearfix"></div> 
                    </div>
             <?php endif; ?>
            </div>
    </div>
    <div class="clearfix"></div>
</div>
<?php if(!empty($rcommend) && $rcommend->crawl_result != ''): ?>
<script>        
    
    jQuery(function() {                
        
        var options_circle = {
            chart: {
                events: {
                    drilldown: function(e) {
                        if (!e.seriesOptions) {
                            var chart = this;
                            // Show the loading label
                            chart.showLoading('Loading ...');
                            setTimeout(function() {
                                chart.hideLoading();
                                chart.addSeriesAsDrilldown(e.point, series);
                            }, 1000);
                        }

                    }
                },
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '',
                style: {
                    display: 'none'
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Keyword Number'
                }
            },
           
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    format: '{point.y}',
                    dataLabels: {
                        enabled: true,
                    },
                    tooltip: {
                        pointFormat: '{point.name}: <b>{point.y}</b>'
                    },
                    legend: false
                },
                pie: {
                    dataLabels: {
                        enabled: true,
                        distance: -20,
                        format: '{point.y}',
                        style: {
                            fontWeight: 'bold',
                            color: '#fff',
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    showInLegend: false
                }
            },
            series: [{
                    name: 'Issues ',
                    innerSize: '50%',
                    colorByPoint: true,
                    data: [                        
                        <?php foreach($issues as $key => $issue){ ?>
                            
                            {
                                name: '<?php echo ucfirst($key); ?>',
                                y: <?php echo $issue['value']; ?>,
                                color: '<?php echo $issue['color']; ?>'
                            },
                            
                        <?php } ?>                                                
                    ]
                }],
        };
        options_circle.chart.renderTo = 'container_graph';
        options_circle.chart.type = 'pie';
        var chart1 = new Highcharts.Chart(options_circle);
        chartfunc = function(chart_type) {
            options_circle.chart.renderTo = 'container_graph';
            options_circle.chart.type = chart_type;
            var chart1 = new Highcharts.Chart(options_circle);
        }

    });

</script>
<?php endif; ?>