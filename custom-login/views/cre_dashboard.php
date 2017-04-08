<div class="credashpage">
<?php
is_login_check();
wp_enqueue_style('bootstrap', TR_COUNT_PLUGIN_URL .'/assets/css/bootstrap.css','', TT_VERSION);
wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
wp_enqueue_style('recom_style.css', TR_COUNT_PLUGIN_URL .'/assets/css/recom_style.css','', TT_VERSION);
wp_enqueue_style('chosen.css', TR_COUNT_PLUGIN_URL .'/assets/css/chosen.css');  

wp_enqueue_script('script.js', LG_COUNT_PLUGIN_URL .'/assets/js/lg-cre-script.js?ver=','', TT_VERSION);
wp_enqueue_script('chosen.jquery.js', TR_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', TT_VERSION);

wp_enqueue_script('jquery-ui.js', get_template_directory_uri() .'/js/jquery-ui.js?ver=','', TT_VERSION);
wp_enqueue_script('jquery-ui-timepicker-addon.js', get_template_directory_uri() .'/js/jquery-ui-timepicker-addon.js?ver=','', TT_VERSION);
wp_enqueue_script('highcharts.js', get_template_directory_uri() .'/report-theme/assets/global/plugins/highcharts/js/highcharts.js?ver=','', TT_VERSION);
wp_enqueue_script('data.js', get_template_directory_uri() .'/report-theme/assets/global/plugins/highcharts/js/modules/data.js?ver=','', TT_VERSION);

global $wpdb;
$user_id = $UserID = $current_id = user_id();

$location_web = get_user_meta($UserID,'website',TRUE);
$webname = trim(str_replace(array("https://","http://","www."), array("","",""), $location_web));

// Code starts By rudra 21-02-2017

$rcommend = $wpdb->get_row
(
    $wpdb->prepare
    (
        "SELECT crawl_result, trigger_report, auto_trigger, rundate FROM wp_content_recommend WHERE user_id = %d", $user_id
    )
);

$date=$rcommend->rundate;
$newtimestamp = strtotime($date.' + 15 minute');
$new_rundate= date('Y-m-d H:i:s', $newtimestamp);
$current_date=date('Y-m-d H:i:s');
if($new_rundate<=$current_date){
	if($rcommend->trigger_report==1){
		$agency_url=site_url();               //agency url
		$agency_user_id=$user_id;             //current_user
		$post_string['agency_url']=$agency_url;
		$post_string['agency_user_id']=$agency_user_id;
                $post_string['agency_id']= $_SESSION['agency_id'];
                $url="http://admin.enfusen.com/cron/cre_queue_check.php";
                //hit main agency(admin)
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                $data=curl_exec($ch);
                curl_close ($ch);   
	}
}
// Code ends By rudra 21-02-2017

$conn = anconn();
$sql = "SELECT PageURL,sum(`Total`) as Total_val, sum(organic) as organic_val, sum(`TimeOnSite`) as TOS_val, sum(`BounceRate`) as bounce_rate_val FROM `short_analytics_$user_id` WHERE PageURL != '' AND PageURL like '%$webname%' group by PageURL order by `DateOfVisit` desc LIMIT 10000";

$result = mysqli_query($conn, $sql);
$rows = array();
if(isset($result) && !empty($result)){
    while($row = $result->fetch_object()){

        $urlidx = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $row->PageURL),"/"));
        $rows["$urlidx"] = $row;
    }                                  
}

$sql = "SELECT conv_id, organic,date,url FROM `all_conversions_data` WHERE user_id = $user_id AND url like '%$webname%' group by url order by date desc LIMIT 10000";
$result = mysqli_query($conn, $sql);                            
$convrows = array();
if(isset($result) && !empty($result)){
    while($rowdt = $result->fetch_object()){                                
        $urlidx = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $rowdt->url),"/"));
        $convrows["$urlidx"] = $rowdt;
    }
}                            
mysqli_close($conn);

include_once get_template_directory() . '/analytics/AdWordsUtils.php';
include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
include_once get_template_directory() . '/analytics/AnalyticsUtils.php';


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
            $page = str_replace(array("http://","https://","www."), array("","",""), $page);
            if($page != '' && !in_array($page, $target_pages)){                
                array_push($target_pages, $page);
            }
        }
    }
}

/* Target Pages */



$rs = $wpdb->get_results
(
    $wpdb->prepare
    (
        "SELECT * FROM cre_urls WHERE user_id = %d ORDER BY total_issues DESC", $user_id
    )
);

//$anyrunning = $wpdb->get_var
//(
//    $wpdb->prepare
//    (
//        "SELECT count(id) as total FROM cre_urls WHERE user_id = %d AND is_running = 1 ORDER BY total_issues DESC", $user_id
//    )
//);
//
//if(empty($anyrunning)){    
//    $wpdb->query
//    (
//        $wpdb->prepare
//        (
//            "UPDATE wp_content_recommend SET trigger_report = 0 WHERE user_id = %d", $user_id            
//        )
//    );
//}


$hassitemap = 0; $sitemapcorrupt = 0; $sitemapurl = "";

if(isset($rcommend->crawl_result) && $rcommend->crawl_result != ''){
    $crawlrs = json_decode($rcommend->crawl_result);
    if($crawlrs->sitemap_corrupted == 1){
        $sitemapcorrupt = 1;
    }
    if($crawlrs->sitemap == 1){
        $hassitemap = 1;
    }
    if($crawlrs->sitemapurl != ''){
        $sitemapurl = $crawlrs->sitemapurl;
    }
}

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

$title = 0; $meta = 0; $content = 0; $heading = 0; $link = 0; $image = 0;                            
  
$totlurlscompleted = 0;
$totalcompleted_score_val = 0;
//$rs = (array) $rs;

$reference_array = array();

$tarpages = ""; $allpages = "";
$total_score_val = 0;
$running = 0;

foreach($rs as $rowvaldata) {     
            
    // condition run first time, when load from ajax
//    if(isset($checkifurlscoming)){
//        if($rowvaldata->is_running ==  0){
//            $wpdb->query("DELETE FROM cre_urls WHERE id = ".$rowvaldata->id);            
//        }                
//    }
    // condition run first time, when load from ajax
    
    $key = $rowvaldata->id;
    $row = json_decode($rowvaldata->result);    
    $title = $title + $row->issues_count->title_issues;
    $meta = $meta + $row->issues_count->meta_issues;
    $content = $content + $row->issues_count->content_issues;
    $heading = $heading + $row->issues_count->heading_issues;
    $link = $link + $row->issues_count->link_issues;
    $image = $image + $row->issues_count->image_issues;


    $crawlre = $row;                
    unset($visits_data); unset($conv_data);
    $urlcrawl = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $crawlre->url),"/"));

    if(isset($rows["$urlcrawl"])){
        $visits_data = $rows["$urlcrawl"];                                    
    }

    if(isset($convrows["$urlcrawl"])){
        $conv_data = $convrows["$urlcrawl"];                                    
    }                                

    $urlprofilepage = site_url().'/url-profile?url='.$rowvaldata->url;

    $totalurlissue = $crawlre->issues_count->title_issues + $crawlre->issues_count->meta_issues + 
            $crawlre->issues_count->content_issues + $crawlre->issues_count->heading_issues + 
            $crawlre->issues_count->link_issues + $crawlre->issues_count->image_issues;

   
    $urlsparse = parse_url($rowvaldata->url);    
    $path = isset($urlsparse['path'])?trim(trim($urlsparse['path']),"/"):'';
    if($path == ""){
        $query = isset($urlsparse['query'])?trim(trim($urlsparse['query']),"/"):'';
        if($query != ''){
            $path = '/'.$query;
        }
        else{
            $path = $rowvaldata->url;
        }
    }
    else{
        $path = '/'.$path;
    }
    
    $rowdata = '';
    $rowdata .= '                         
    <tr class="rowidx'.$ij.'" >
        <td class="first-link linkoverflow"><a class="linka" href="'.$urlprofilepage.'">'.$path.'</a></td>
        <td class="center">';
    if(isset($visits_data))
        $rowdata .= $visits_data->organic_val;
    else
        $rowdata .= 0;

    $rowdata .= '</td>
        <td class="center">';

    if(isset($conv_data))
        $rowdata .= $conv_data->organic;
    else
        $rowdata .= 0;


    $rowdata .='</td>
        <td class="center">';

    if(isset($visits_data))
        $rowdata .= formatsecondsToMinSec2($visits_data->TOS_val/$visits_data->Total_val);
    else
        $rowdata .= 0;

        $rowdata .=  '</td>
            <td class="center">';

    if(isset($visits_data))
        $rowdata .= formatpercent(($visits_data->bounce_rate_val/$visits_data->Total_val)*100);
    else
        $rowdata .= 0;            

    $datafetched = 1; $shidden = 'hidden'; $ahidden = '';  $rerun = '';    
    
    if(isset($rowvaldata->is_running) && $rowvaldata->is_running == 1){
        $datafetched = 0; $shidden = ''; $ahidden = 'hidden';  $rerun = 'nexturlsdisb';
    }
    
    $scorecls = 'redscore';
    if( $crawlre->score >= 51 && $crawlre->score <= 79 ){
        $scorecls = 'yellowscore';
    }
    else if($crawlre->score >= 80){
        $scorecls = 'greenscore';
    }
    
    $stscode = isset($crawlre->pagestatus)?$crawlre->pagestatus:200;
    $totalurlissuetxt = $totalurlissue.' issues'; $scor = $crawlre->score;
    
    if($stscode == 404){
        $totalurlissuetxt = '404 Error';
        $scor = '0'; $scorecls = '';
    }
//    else if($stscode == 0){
//        $totalurlissuetxt = 'Error';
//        $scor = '0'; $scorecls = '';
//    }
        
    $rowdata .=  '</td><td><span class="scroetd" data-idx="'.$key.'"><span class="'.$scorecls.'">'.$scor.'</span></span> </td>'
            . '<td>'.$totalurlissue.'</td>';       
        
    $rowdata .=  '<td class="last-link lastcoltd">                                        
        <div data-idx="'.$key.'" class="divissues '.$ahidden.'">
            <a data-idx="'.$key.'" href="'.$urlprofilepage.'" class="anchrissues btn-success">'.$totalurlissuetxt.'</a>                
        </div>
        <span data-idx="'.$key.'" class="sploader '.$shidden.'"><img src="'.$gif.'" /> </span>
        </td>
        <td class="reruntd"><a href="javascript:;" data-idx="'.$key.'" data-adr="'.$crawlre->url.'" class="btn-warning runpagebtn '.$rerun.'">Re-run</a></td>
    </tr>                                
    ';
    
    $tarurl = trim(trim(str_replace(array("https://","http://","www."), array("","",""), $rowvaldata->url),"/")); 
    
    if(in_array($tarurl, $target_pages)){
        $tarpages .= $rowdata;
    } else {                    
        $allpages .= $rowdata;
    }
    $total_score_val =  $total_score_val + $crawlre->score; 
    if($rowvaldata->is_running == 1){
        $running++;
    }
    else{
        $totalcompleted_score_val = $totalcompleted_score_val + $crawlre->score; 
        $totlurlscompleted++;
    }    
    $totalurls++;    
}

if($rcommend->trigger_report == 1){
    $avgscore = round(($totalcompleted_score_val / $totlurlscompleted),2); 
}
else{
    $avgscore = round(($total_score_val / $totalurls),2); 
}
//array_multisort($reference_array, SORT_DESC, $rs);

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

// billing
$limit = 10000;
if(defined("BILLING_ENABLE") && BILLING_ENABLE == 1){
    $lmt = check_lp_all_limits();        
    $limit = $lmt['pages_available'];
}
// billing

$loadercurls = 0;
if(!empty($rcommend) && $rcommend->auto_trigger == 1){
    if(empty($rs)){
        $loadercurls = 1;        
    }    
    ?>
    <input type="hidden" id="hidcrawlingpages" name="hidcrawlingpages" value="1" />
    <?php
}

$gaurl = site_url()."/analytics-settings/";
$locid = $wpdb->get_var($wpdb->prepare("SELECT id FROM wp_client_location WHERE MCCUserId = %d",$UserID));
if($locid > 0){
    $gaurl = site_url()."/location-settings/?parm=ga_connect&location_id=".$locid;
}
?>


<input type="hidden" id="hidpagelimit" name="hidpagelimit" value="<?php echo $limit; ?>" />

<input type="hidden" id="hidcontentrec" name="hidcontentrec" value="1" />
<input type="hidden" id="hidgif" name="hidgif" value="<?php echo $gif; ?>" />

<input type="hidden" id="onimg" name="onimg" value="<?php echo $onimg; ?>" />
<input type="hidden" id="ofimg" name="ofimg" value="<?php echo $ofimg; ?>" />
<style>
    .first-link {
        text-align: left !important;
    }
    .linka{
        color: #0f568c !important;
    }
    .lastcoltd, .lastcol{
        text-align: center;  
    }
    .runpagebtn{
        margin-left: 5px;
    }
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
    .tbltarget td, .tbltarget th, .tblrecomconettn td, .tblrecomconettn th{
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
    .noteborder{
        border-left: 5px solid #caa900 !important;
    }
    
    .bottomshade {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.78);
            z-index: 9998;
            left: 0;
            right: 0;            
            top: 0;
    }
    .overflowhidden{
        overflow-y: hidden;
    }
    
    .gachecker .alert.noteborder {
        z-index: 9999;
        position: relative;
        /* width: 100%; */
        left: 0;
        right: 0;
    }
    
/*    .alert.noteborder{
            position: fixed;
    top: 30%;
    width: 100%;
    z-index: 999999999;
    left: 0;
    }*/
</style>
<div class="msg"></div>
<input type="hidden" id="pagerecdash" value="1" />
<div class="main-wrapper credashboardpage">
    <div class="gachecker hidden">
        <div class="alert alert-warning noteborder">
            <strong>
                Warning : You have to connect with Google Analytic, in order to use Content Recommendation Tool
                <a href="<?php echo $gaurl; ?>">Click Here To Connect</a>
            </strong>
        </div>
        <div class="bottomshade">
            
        </div>
    </div>
    <div class="upper-bar topcontentmsg">
        <?php
        if($rcommend->trigger_report == 1){
            ?>              
            <div class="alert alert-info">
              <h6><strong> Alert ! </strong>Your Content Recommendation Engine is running. Please see below for your overall progress. This report can take some time to completely run.</h6>
            </div>
        <?php } ?>
        
    </div>
    <div class="main-sction">
                      
        <div class="title-section">
            <h3><span>Content Recommendations :</span> <?php echo $location_name; ?></h3>
            <div class="right-btn">

                <a href="javascript:;" data-from='allpage' class="re-compain runcampaign <?php echo $rcommend->trigger_report == 1?'nexturlsdisb':''; ?>"><span class="glyphicon glyphicon-repeat"></span><?php echo !empty($rcommend)?'Re-run campaign for all pages':'Run campaign for all pages'; ?></a>                
                <a href="javascript:;" data-from='targetpage' class="re-compain runtargetcampaign <?php echo $rcommend->trigger_report == 1?'nexturlsdisb':''; ?>"><span class="glyphicon glyphicon-repeat"></span><?php echo !empty($rcommend)?'Re-run campaign for target pages':'Run campaign for target pages'; ?></a>
                <a href="javascript:;" class="pdf pdfreportgen"><span class="glyphicon glyphicon-log-in"></span> History</a>
                

            </div>
            <div class="selection-dropdown">               
                <div class="web-link"><?php echo get_user_meta(user_id(),'website',true); ?></div>
                <div class="update">Last update : <?php echo $rcommend->rundate != ''?date('D d M Y, h:i a', strtotime($rcommend->rundate)):'<i>Not Run Yet</i>'; ?></div>
                        <div class="pull-right rempagesscan <?php echo $rcommend->trigger_report == 0?'hidden':''; ?>">
                                <span>
                                    <?php echo $running; ?> Pages Remaining Out Of <?php echo $totalurls; ?> Pages
                                </span>
                        </div>
            </div>
        </div>
        <div class="tabs-section">
                    
            <?php if(empty($rs)): ?>
            <div class="optimization-section">
                <?php 
                if($loadercurls == 1){
                    ?>
                     <div class="noresultcre">
                        Loading....
                      </div>
                    <script>
                        jQuery('.rempagesscan').addClass("hidden");
                    </script>
                    <?php
                }
                else{
                    ?>
                     <div class="noresultcre">
                        No Result
                      </div>
                    <?php
                }
                ?>
            </div>
            
            <?php else: ?>
                    <?php
                    
                    $scorecls = 'alert-danger';
                    if( $avgscore >= 51 && $avgscore <= 79 ){
                        $scorecls = 'alert-warning';
                    }
                    else if($avgscore >= 80){
                        $scorecls = 'alert-success';
                    }
//                    if($avgscore < 50){
//                        $avgscore = 50;
//                    }    
				    
                    ?>
                    <div class="">
                       
                    </div>
                    <div class="idea-frame">

                        
						<div class="graph-result">
							
							
							<h3>OverAll Content Score</h3>
							
							 <h1><?php echo $avgscore; ?></h1>  
							
							
							
						</div>  
                           
                           
                         <div class="graph-div">
                            
                           <div class="left-div">

                            <h4>Issues Breakdown<span class="glyphicon glyphicon-info-sign"></span></h4>
                            <div id="container_graph" style="height:250px; width: 250px;"></div>
                        </div>

                        
                            <div class="analatics-div">
                            
                            
                             <div class="left-div alert alert-warning divtopalert">                            
                            <h4>
                                <span>Total <b class="totalissueb"><?php echo $totalissues; ?></b> issues found from <b><?php echo $totalurls; ?></b> pages </span>
                                <span class="pull-right"></span>
                            </h4>                            
                        </div>
                            
                        <div class="right-div">

                            <ul>

                                <li><span title="Title Issues" class="tag" style='background-color: <?php echo $issues['title']['color']; ?>'>Ti</span><h4>Title issues <span title="Title Issues" class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['title']['id']; ?>" class="issues-point"><?php echo $issues['title']['value']; ?></span></li>
                                <li><span title="Meta Description Issues"  class="tag" style='background-color: <?php echo $issues['meta']['color']; ?>'>Me</span><h4>Meta Issues<span title="Meta Description Issues" class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['meta']['id']; ?>" class="issues-point"><?php echo $issues['meta']['value']; ?></span></li>

                                <li><span title="Content Issues" class="tag" style='background-color: <?php echo $issues['content']['color']; ?>'>Co</span><h4>Content Issues<span title="Content Issues" class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['content']['id']; ?>" class="issues-point"><?php echo $issues['content']['value']; ?></span></li>
                                <li><span title="Heading Issues" class="tag" style='background-color: <?php echo $issues['heading']['color']; ?>'>He</span><h4>Heading Issues<span title="Heading Issues" class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['heading']['id']; ?>" class="issues-point"><?php echo $issues['heading']['value']; ?></span></li>
                                
                                <li><span title="Link Issues" class="tag" style='background-color: <?php echo $issues['link']['color']; ?>'>Li</span><h4>Link issues<span title="Link Issues" class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['link']['id']; ?>" class="issues-point"><?php echo $issues['link']['value']; ?></span></li>
                                <li><span title="Image Issues" class="tag" style='background-color: <?php echo $issues['image']['color']; ?>'>Im</span><h4>Image issues<span title="Image Issues" class="glyphicon glyphicon-info-sign"></span></h4><span id="<?php echo $issues['image']['id']; ?>" class="issues-point"><?php echo $issues['image']['value']; ?></span></li>
                                
                            
                            
								<div class="clear"></div>
                            </ul>

                        </div>
                        
							 </div> 
                        
                        
                        
                        
                        
                        
                        
                        
							 <div class="clear"></div>
                        
                         </div>
                        
                        
                        

                        <div class="clearfix"></div>
                    </div>
                    <?php 
                       if(count($rs) > 0){
                           ?>
                           <input type="hidden" id="checkanalyticdt" value="1" />
                           <?php
                       }
                       ?>

                    <div class="optimization-section">                       
                        <h4>Target Pages</h4>
                        <table class="table tbldashboard tbltarget">
                            <thead>
                                <tr>
                                    <th title="Pages"  style="width: 40%;" class="center">Page <span title="Pages" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Organic Visits" style="width: 8%;" class="center">OV <span title="Organic Visits" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Organic Conversions" style="width: 8%;" class="center">OC <span title="Organic Conversions" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Time On Site" style="width: 8%;" class="center">TOS <span title="Time On Site" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Bounce Rate" style="width: 8%;" class="center">BR <span title="Bounce Rate" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Score" style="width: 9%;" class="center">Score <span title="Page Score" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th></th>
                                    <th title="Issues Found On Page" style="width: 14%;" class="lastcol">Issues <span title="Issues Found On Page" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th style="width: 14%;"> Action</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <?php
                                    echo $tarpages;
                                ?>                                                                
                            </tbody>
                        </table>

                        <div class="clearfix"></div> 
                    </div>
                    <div class="row"><hr/></div>      
                    <div class="optimization-section">                       
                        <h4>All Pages</h4>
                        <table class="table tbldashboard tblrecomconettn">
                            <thead>
                                <tr>
                                    <th title="Pages"  style="width: 40%;" class="center">Page <span title="Pages" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Organic Visits" style="width: 8%;" class="center">OV <span title="Organic Visits" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Organic Conversions" style="width: 8%;" class="center">OC <span title="Organic Conversions" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Time On Site" style="width: 8%;" class="center">TOS <span title="Time On Site" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Bounce Rate" style="width: 8%;" class="center">BR <span title="Bounce Rate" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th title="Score" style="width: 9%;" class="center">Score <span title="Page Score" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th></th>
                                    <th title="Issues Found On Page" style="width: 14%;" class="lastcol">Issues <span title="Issues Found On Page" class="glyphicon glyphicon-info-sign"></span></th>
                                    <th style="width: 14%;"> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <?php                            
                                    echo $allpages;
                                ?>                                
                                
                            </tbody>
                        </table>

                        <div class="clearfix"></div> 
                    </div>
                           
             <?php endif; ?>
            </div>
    </div>
    <div class="clearfix"></div>
</div>

<div class="modal fade modalhistorydata" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
        <h4 class="modal-title">History For <?php echo $location_web; ?></h4>
      </div>
      <div class="modal-body historydata">
          Loading....
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<?php if(!empty($rs)): ?>
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
</div>
