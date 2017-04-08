<?php

//login_check();
wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
wp_enqueue_style('chosen.css', TR_COUNT_PLUGIN_URL .'/assets/css/chosen.css');  
wp_enqueue_style('recom_style.css', TR_COUNT_PLUGIN_URL .'/assets/css/recom_style.css','', TT_VERSION);
wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', TT_VERSION);
wp_enqueue_script('chosen.jquery.js', TR_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', TT_VERSION);

global $wpdb;
$user_id = user_id();
//$user_info = get_userdata($user_id);
//pr($user_info->data->user_email); die;
//$url = 'caringvillage.com';
//$res = crawl_page($user_id, $url);
//pr($res);
die;
//$href = 'http://www.medstarorthopaedicinstitute.org/';
//$hashhref = explode("#", $href);
//if(count($hashhref) > 1){
//    $href = $hashhref[0];
//}
//echo $href;
//die;
//$urls = array('https://www.enfusen.com','https://www.enfusen.com/blog/');
//$agency_url = 'http://reports.enfusen.com/';
//pr('Before');
//$out = add_global_queue($agency_url,$urls,711);
//pr($out);
//die;
 
//$urltocheck = "www.enfusen.com";
//$dir_yslow = TR_COUNT_PLUGIN_DIR."/yslow.js";    
//$cmd ="phantomjs --proxy-type=none --ssl-protocol=any --ignore-ssl-errors=true ".$dir_yslow." --info basic --format json ".$urltocheck;
//exec($cmd, $output, $return_var); $score = 0; $pagespreed = 0;    
//if(isset($output[0])){
//    $dt = json_decode($output[0]);
//    pr($dt); die;
//    $score = isset($dt["o"])?$dt["o"]:0;
//    $pagespreed = isset($dt["lt"])?$dt["lt"]:0;                
//}

//$time1 = microtime(TRUE);
//$url = "simplex-it.com "; //get_user_meta($user_id,'website',true);
//$res = crawl_page($user_id, $url);
//pr($res); die;

//echo get_remote_size($url); die;
//
//

//$titlekeywords = getLongTailKeywords("Visitor Information - MedStar Washington Hospital Center", 3, 0);
//
//$html = file_get_contents("https://www.rudrainnovatives.com/");
//$bodydom = new DOMDocument;
//$bodydom->loadHTML($html);
//removeElementsByTagName('script', $bodydom);
//removeElementsByTagName('noscript', $bodydom);
//removeElementsByTagName('style', $bodydom);
//$databodyhtml = $bodydom->saveHtml(); 
//$bodaydata = iconv( 'utf-8', "utf-8", $databodyhtml );
//$bodaydata = strip_html_tags( $bodaydata ); 
////$bodaydata = html_entity_decode( $bodaydata, ENT_QUOTES, "UTF-8" );
////pr($bodaydata);
////$title = 'Best Website Design';
//
//$resocuur = fnd_pos(strtolower($keyword),strtolower($title));
//
//similar_text($title, strtolower($bodaydata), $percent);
//print_r($percent); die;



//
//$params = array('param'=>'content_recommend','user_id'=>$user_id, "typerequest"=>'all', 'history_entry'=>1);
//silent_post($params);
//
//die;
//$url = 'https://www.enfusen.com/'; //get_user_meta(user_id(),'website',true);


//$url = 'http://www.medstarorthopaedicinstitute.org/doctors';
////$html = file_get_contents($url);  
//$html = url_get_content($url);
//pr(htmlspecialchars($html)); die;
//$url = appendhttp($url);
//$parseurl = parse_url($url);
//$baseurl = $parseurl['host'];
//$serviceurl = 'http://icons.better-idea.org/allicons.json';
//$returndata = @file_get_contents($serviceurl.'?'.'url='.$baseurl);
//$returndata = json_decode($returndata);
//$faviconurl = 0;
//if(isset($returndata->icons[0]->url) && $returndata->icons[0]->url != ''){
//    $faviconurl = $returndata->icons[0]->url;                 
//}
//
//$hasfavicon = get_user_meta($user_id, 'webfavicon', TRUE);
//if($hasfavicon == ''){
//    add_user_meta( $user_id, 'webfavicon', $faviconurl );
//}
//else{
//    update_user_meta( $user_id, 'webfavicon', $faviconurl );
//}
//pr($hasfavicon);
//die;

//$baseurl = trim(trim(str_replace(array("http://","https://"), array("",""), $baseurl),"/"));
//$serviceurl = 'http://icons.better-idea.org/allicons.json';
//$returndata = @file_get_contents($serviceurl.'?'.'url='.$baseurl);
//pr($returndata); die;

//$body = 'A marketing analytics platform for digital marketing agencies, Enfusen provides data driven marketing insights to help you drive engagement and conversion';
//$title = 'Enfusen I Data Driven Marketing Insights';
//$ar = getLongTailKeywords($title, 1, 0);
//$total = count($ar); $matchcont = 0;
//foreach($ar as $key => $a){
//    $resocuur = fnd_pos(strtolower($key),strtolower($body));
//    if(!empty($resocuur)){
//        $matchcont++;
//    }
//}
//
//$percent = (($matchcont / $total) * 100);
//pr($percent."%");
//die;


//$faiconurl = 'http://www.medstargeorgetown.org/favicon.ico';
//$faiconexisst = urlexist($faiconurl);
//pr($faiconexisst);
//die;
//$url = 'http://www.medstarwashington.org/sitemap.xml';
//pr(pathinfo(basename($url), PATHINFO_EXTENSION)); die;
//pr(crawl_page($user_id, $url));
//die;

$url = 'http://www.medstarwashington.org/';
require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
$browser = &new SimpleBrowser();
                    
$driver = array();
$data['url'] = $url;
$data['keyword'] = "";
$data['tarkeyword'] = '';
//$data['tarkeyword'] = json_decode('[{"type":"primary","keyword":"predictive analytics"},{"type":"synonym","keyword":"Predictive Analytics Marketing","synonymof":"Just in time marketing"},{"type":"synonym","keyword":"xx","synonymof":"Just in time marketing"},{"type":"synonym","keyword":"bb","synonymof":"Just in time marketing"},{"type":"primary","keyword":"digital marketing"},{"type":"synonym","keyword":"abc","synonymof":"digital marketing"},{"type":"synonym","keyword":"xyz","synonymof":"digital marketing"}]');

$res = page_analysis(json_decode(json_encode($data)),$browser);
pr($res);
die;


//pr(check_lp_all_limits()); die;

//pr(crawl_page($user_id, 'https://www.enfusen.com')); die;

//pr(crawl_page(1139, 'http://www.medstarorthopaedicinstitute.org')); die;
//pr(target_pages($user_id));
//pr(target_page_keywords('https://www.enfusen.com/should-you-hire-a-seo-agency', $user_id)); die;
//$ga = 0;
//$hasgaconn = get_user_meta($user_id,'ga_connected',true);
//if($hasgaconn == 1){
//    $ga = 1;
//}

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
    
if($_SERVER['SERVER_PORT'] == 443){
    $cururl =  "https://"."{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; 
}
else{
    $cururl =  "http://"."{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; 
}

?>
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
        top: inherit;
    }
    .margin_bottom_10{
        margin-bottom: 10px;
    }
    .pagerunning, .nexturlsdisb, .nexturlsdisb:active, .nexturlsdisb:focus {
        background: #D6D6D6;
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
/*    .tblrecomconettn td, .tblrecomconettn th{
        text-align: center;
    }*/
    .secondspan div{
        display: inline-block; margin-left: 20px;
    }
    .htagp {
        margin: 5px 0 0 0 !important;
    }
    
    
</style>
<div class="msg"></div>
<?php


$parseurl = parse_url($cururl);
$baseurl = $parseurl['scheme'].'://'.$parseurl['host'].$parseurl['path'];

$tool_started = isset($_REQUEST['tool_started'])?1:0;

$rcommend = $wpdb->get_row
(
    $wpdb->prepare
    (
        "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
    )
);

pr((json_decode($rcommend->result))); die;
//$pageindex = 7; $url = "https://www.enfusen.com/careers/sr-digital-marketing-manager";
//singlepagecall($pageindex, $url, $user_id);

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
}
*/

$page_dashboard = isset($_REQUEST['page_dashboard'])?1:0;
$showpage = 0; $page = 0;
if($page_dashboard == 1){
        
    $page = isset($_REQUEST['page_detail'])?intval($_REQUEST['page_detail']):0;
    if($page > 0){
        $showpage = 1;
    }
    else{
        
        $urlparse = parse_url($cururl);
        if($_SERVER['SERVER_PORT'] == 443){
            $redurl = "https://".$urlparse['host'].$urlparse['path'];        
        }
        else{
            $redurl = "http://".$urlparse['host'].$urlparse['path'];        
        }        
        wp_redirect($redurl);
    }
}
if($showpage == 1){
    
    if(trim($rcommend->result) != '' && $rcommend->result != NULL){    
        $analysis = json_decode($rcommend->result);   
        
        $indx = $page - 1;
        $pagedata = $analysis["$indx"];
        $analys = $pagedata->analysis;
        include_once 'analysis_detail.php';
    }
    else{
        ?>
        <div id="primary" class="site-content" style="min-height: 400px">
            <div id="content" role="main">

                <div class='col-md-12'>
                    <div class="alert alert-danger">
                        <strong>Error : Data not found</strong>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
else{
        
    if($ga == 0 || $ct == 0){
        $tool_started = 0;
    }
    
    if($tool_started == 0){

        ?>

        <div class="main-wrapper">

            <div id="content" role="main">

                <div class='col-md-12'>
                    <h4>All Scanned Pages
                    <div class="pull-right">
                        <a href="<?php echo site_url().'/'.CRE_DASH; ?>" class="btn btn-danger">Back To Dashboard</a>
                    </div>
                    </h4>
                    <div class='row'>
                        <div class='col-md-12 optimization-section'>                        
                            <?php
                            
                            $rs = json_decode(($rcommend->result));                            
                            $crawlres = json_decode(stripcslashes($rcommend->crawl_result));                        
                            $total_urls = count($crawlres->urls);
                            if($total_urls <= 0){
                                ?>
                                <div class="well">
                                    No Result
                                </div>
                                <?php
                            }
                            else{                                  
                                                                 
                                if(count($rs) > 0){
                                    ?>
                                    <input type="hidden" id="checkanalyticdt" value="1" />
                                    <?php
                                }
                                ?>
                                <table class="table table-condensed tblrecomconettn">
                                    <thead>
                                    <tr>
                                        <th class="center">Page <span class="glyphicon glyphicon-info-sign"></span></th>
                                        <th class="center">Organic Visits <span class="glyphicon glyphicon-info-sign"></span></th>
                                        <th class="center">Organic Conversions <span class="glyphicon glyphicon-info-sign"></span></th>
                                        <th class="center">Time On Site <span class="glyphicon glyphicon-info-sign"></span></th>
                                        <th class="center">Bounce Rate <span class="glyphicon glyphicon-info-sign"></span></th>
                                        <th></th>
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
                                        
                                        mysqli_close($conn); $idx = 0;
                                        $issuelist = array('total_title_issues','total_meta_issues','total_content_issues','total_heading_issues','total_link_issues','total_image_issues');
                                        foreach($rs as $key => $crawlre){
                                            unset($visits_data); unset($conv_data);
                                            if(in_array($key, $issuelist)){
                                                continue;
                                            }
                                            
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
                                                   
                                            $datafetched = 0;
                                            if(isset($crawlre->analysis) && !empty($crawlre->analysis)){
                                                $datafetched = 1;
                                            }
                                            ?>                                            
                                            <tr class="rowidx<?php echo $idx; ?>" >
                                                <td class="first-link"><?php print_r($crawlre->url); ?></td>
                                                <td class="center"><?php echo isset($visits_data)?$visits_data->organic_val:0; ?></td>
                                                <td class="center"><?php echo isset($conv_data)?$conv_data->organic:0; ?></td>
                                                <td class="center"><?php echo isset($visits_data)?formatsecondsToMinSec2($visits_data->TOS_val/$visits_data->Total_val):0; ?></td>
                                                <td class="center"><?php echo isset($visits_data)?formatpercent(($visits_data->bounce_rate_val/$visits_data->Total_val)*100):"0%"; ?></td>
                                                <td><?php echo $totalurlissue; ?></td>
                                                <td class="center last-link">                                        
                                                    <a data-idx="<?php echo $key; ?>" href="<?php echo $urlprofilepage; ?>" class="anchrissues btn-success <?php echo $datafetched == 0?'hidden':''; ?>"><?php echo $totalurlissue; ?> issues</a>
                                                    <span data-idx="<?php echo $key; ?>" class="sploader <?php echo $datafetched == 1?'hidden':''; ?>"><img src="<?php echo $gif; ?>" /> </span>
                                                </td>
                                            </tr> 
                                            <?php
                                            $idx++;
                                            
                                        }

                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div>

                    </div>
                </div>

            </div>
        </div>

        <?php

    }
    else{

        $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);

        if (!empty($keywordDat)) {        

            $activation = $keywordDat["activation"];
            $target_keyword = $keywordDat["target_keyword"];
            $delete = $keywordDat["delete"];
            $landingpage = $keywordDat["landing_page"];

        } else {
            $keywordDat['keyword_count'] = 0;
        }


        if ($sort_by == 'primary-key-asc') {
            $order_by_con = 'asc';
        } else {
            $order_by_con = 'desc';
        }
        $sql = 'select meta_key from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" && `meta_value` != "" order by `meta_value` ' . $order_by_con;
        $KeyWordQuery = $wpdb->get_results($sql);     

        $sql = 'SELECT meta_key FROM wp_usermeta WHERE user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
        $null_key_index = $wpdb->get_results($sql);
        $KeyWordQuery = array_merge($KeyWordQuery, $null_key_index);


        ?>

        <?php
        if(!empty($rcommend)){
            ?>

            <input type="hidden" id="hidprocessrun" name="hidprocessrun" value="<?php echo $rcommend->urlscanning; ?>" />
            <input type="hidden" id="reports_triggered" name="reports_triggered" value="<?php echo $rcommend->trigger_report; ?>" />

            <?php
        }

        ?>    

        <div id="primary cretooldiv" class="site-content" style="min-height: 400px">

            <div id="content" role="main">

                <div class='col-md-12'>
                    <h4>CRE Tool <b><?php echo $location_name; ?> [<?php echo $location_web; ?>]</b>
                    <div class="pull-right">
                        <a href="javascript:;" class="backtodashboard btn btn-danger">Back To Dashboard</a>
                    </div>
                    </h4>
                    <div class='row'>
                        <div class='col-md-12'>
                            <h4>
                                <div class="pull-right imgdiv margin_bottom_10">
                                    <span class="disclbls <?php if(empty($rcommend)){ echo 'hidden'; } ?>">
                                        <img class="imgoffon" src='<?php echo $onimg; ?>' /> <span>= Traget Url matched with keyword</span>
                                        &nbsp;&nbsp;
                                        <img class="imgoffon" src='<?php echo $ofimg; ?>' /> <span>= Traget Url Is not matched with keyword</span>
                                        &nbsp;&nbsp;
                                    </span>
                                    <?php if(!empty($rcommend) && $rcommend->urlscanning == 1){
                                        ?>
                                        <a href="javascript:;" class="btn btn-primary nexturls nexturlsdisb">Scanning...</a>
                                        <?php
                                    } else if(!empty($rcommend) && $rcommend->urlscanning == 0){
                                        ?>
                                        <a href="javascript:;" class="btn btn-primary nexturls">Re-Scan</a>
                                        <span class="btnifcorrect"></span>
                                        <?php
                                    }                                
                                    else { ?>
                                        <a href="javascript:;" class="btn btn-primary nexturls">Scan</a>
                                    <?php } ?>
                                </div>
                            </h4>
                            <form name='contentform' id='contentform' method="post">

                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 40%">Target Keyword</th>
                                        <th style="width: 60%">URL</th>
                                    </tr>
                                    <tbody>
                                        <?php

                                        $totaltargetwords = 0;
                                        foreach ($KeyWordQuery as $row_key) {                                    
                                            $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
                                            $j = $ks - 1;
                                            if ($delete[$j] == 0) {                                        
                                                if ($activation[$j] != 'inactive' && $target_keyword[$j] == 'Yes') {
                                                    $keywords = trim(get_user_meta($UserID, "LE_Repu_Keyword_" . $ks . "",TRUE));
                                                    $totaltargetwords++;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <label class="keywordtxt keyword_<?php echo $ks; ?>"><?php echo $keywords; ?></label>
                                                            <label data-id='<?php echo $ks; ?>' class="rightlblsts sts_<?php echo $ks; ?>">
                                                            <?php if(!empty($rcommend) && $rcommend->urlscanning == 1){                                                        
                                                              ?>
                                                                <img data-type='loading' src='<?php echo $gif; ?>' />
                                                                <?php
                                                            }
                                                            else if(!empty($rcommend)){

                                                              $lasturls = json_decode($rcommend->scannedurls);
                                                              $lasturlval = array();
                                                              foreach($lasturls as $lasturlva){ 

                                                                  if($lasturlva->datdid == $ks){                                                            
                                                                      $lasturlval = $lasturlva;   
                                                                      break;
                                                                  }
                                                              }                                                                                                            

                                                              if(isset($lasturlval) && $lasturlval->available == 1){
                                                                    ?>
                                                                      <img data-img='on' title='Keyword Matched' src='<?php echo $onimg; ?>' />
                                                                      <?php
                                                                }
                                                                else if(isset($lasturlval) && $lasturlval->available == 0){
                                                                    ?>
                                                                      <img data-img='of' title='Please assign correct target URL for this keyword.' src='<?php echo $ofimg; ?>' />
                                                                      <?php
                                                                }                                                      
                                                            }
                                                            ?>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="lblurl lblurl_<?php echo $ks; ?>">
                                                                <label data-id='<?php echo $ks; ?>'> <?php echo $landingpage[$j][0]; ?></label>
                                                                &nbsp;&nbsp; <a class="assignurl" data-id='<?php echo $ks; ?>' href="javascript:;">Assign New Url </a>
                                                            </div>
                                                            <div class="ddurls ddurls_<?php echo $ks; ?> hidden">
                                                                <select class="ddlocs" data-id='<?php echo $ks; ?>' name="locsdd_<?php echo $ks; ?>" id="locsdd_<?php echo $ks; ?>">
                                                                    <option>URL Options loading.....please wait...</option>
                                                                </select>
                                                                &nbsp; OR &nbsp;
                                                                <input type="text" url='true' value="<?php echo $landingpage[$j][0]; ?>" placeholder="Assign New URL" class="addurl" name="addurl_<?php echo $ks; ?>" id="addurl_<?php echo $ks; ?>" />
                                                                &nbsp;&nbsp; <a class="updtvalurl" data-id='<?php echo $ks; ?>'  href="javascript:;">Update </a>
                                                                &nbsp;&nbsp;
                                                                <a class="cancelupdt" data-id='<?php echo $ks; ?>' href="javascript:;">Cancel </a>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                    <?php

                                                }
                                            }
                                        }

                                        if($totaltargetwords == 0){
                                            ?>
                                                    <tr>
                                                        <td colspan="2">No Active Target Keyword Found</td>
                                                    </tr>
                                            <?php
                                        }

                                        ?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="totaltargetwords" id="totaltargetwords" value="<?php echo $totaltargetwords; ?>" class='form-control' />                  
                                <div class='row hidden'>
                                    <div class='col-md-5'>
                                        <input type="text" name="weburl" id="weburl" class='form-control' />
                                    </div>
                                    <div class='col-md-5'>
                                        <a href='javascript:;' class='getcontentreport btn btn-primary'>Run Report</a>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                    <div>

                    </div>
                </div>

            </div>
        </div>
    <?php } 

}
?>

            
<div class="modal fade modalanalytic" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
        <h4 class="modal-title">Google Analytic and Conversion Code JS</h4>
      </div>
      <div class="modal-body">
        
          <div class="messagemodal margin_bottom_10"></div>
          <div class="margin_top_10">
              <a href="<?php echo site_url().'/analytics-settings' ?>"><span class="lnkspan"></span></a>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->