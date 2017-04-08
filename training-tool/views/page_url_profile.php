<?php
wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', TT_VERSION);

// billing
$limit = 10000;
if(defined("BILLING_ENABLE") && BILLING_ENABLE == 1){
    $lmt = check_lp_all_limits();        
    $limit = $lmt['pages_available'];
}
// billing

?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/highcharts/js/highcharts.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/highcharts/js/modules/data.js"></script>
<div class="msg"><div class="messdv"></div></div>
<div class="margin-bottom-15"></div>
<input type="hidden" id="hidpagelimit" name="hidpagelimit" value="<?php echo $limit; ?>" />
<hr/>
<style>
    
    caption {
        font-size: 16px;
        font-weight: 600;
        text-align: center;
        padding-bottom: 12px;
    }
    tr.rowspecial {
        background: #777777;
        color: #fff;
    }
    tr.rowspecial td{
        border-color: #777777 !important;
    }
    .firstlower {
        line-height: 16px;
        font-style: italic;
    
    }
    .col1div{
        display: inline-block;
        width: 510px;       
        vertical-align: top;
        margin-top: 20px;
        min-height: 22px;
/*        height: 60px;
        line-height: 60px;*/
    }
    .col2div{
        display: inline-block;
        width: 49%;
    }
    .secondspan img.imgoffon {
        margin-right: 5px;
    }
    .notebottom {
        margin: 0px 0 0 0;
        line-height: 1.5;
        border-left: 5px solid;
    }
    .ulli li{
        line-height: 20px;
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
    .nexturlsdisb, .nexturlsdisb:active, .nexturlsdisb:focus {
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
        min-height: 20px;
    }
    .secondspan span {
        position: absolute;
    }
    .tblrecomconettn td, .tblrecomconettn th{
        text-align: center;
    }
    .secondspan div{
        display: inline-block; margin-left: 20px; max-width: 500px;
    }
    .htagp {
        margin: 5px 0 0 0 !important; word-break: break-all;
    } 
    .captionect{
        margin-bottom: 20px; display: inline-block;
    }
    .issuesspan span{
        padding: 6px 8px;
        border: 2px solid #000;
        margin-right: 10px;
        font-weight: 600;
    }
    .issuesspan {
        height: 25px;
        margin-top: 10px;
    }
    .pagerunning, .nexturlsdisb, .nexturlsdisb:active, .nexturlsdisb:focus {
        background: #D6D6D6;
        pointer-events: none;
        cursor: not-allowed;
    }
    .contentscore {
        margin: 5px 0;
        font-size: 15px;
        font-weight: 600;
    }
    
    .divheadingtgs .sphead {
        font-weight: 600;
        border-bottom: 1px solid #327ad5;
        padding-bottom: 4px;
        margin-bottom: 8px;
        display: block;
    }
    
    .redscore, .yellowscore, .greenscore{
        font-weight: 800;       
    }

    .redscore{
        color: #ce1616;
    }
    .yellowscore{
        color: #cea500;
    }
    .greenscore{
        color: #029c26;
    }
    .modal .list-group-item a{
        word-wrap: break-word;
        line-height: 24px;
    }
    .groupdv{
        text-align: center;
    }
    .groupdv div {
        margin: 15px 5px;
    }
    .centrgrpdiv {
        font-weight: 700;
        font-size: 20px;
    }
    .plussign {
        font-weight: bold;
        font-size: 22px;
        position: relative;
        top: 4px;
        right: 2px;
    }
    a.globconv {
        float: right;
    }
    a.globconv span {
        font-size: 25px;
    }
    .urlgrpdiv {
        font-weight: 600;
        margin-bottom: 30px !important;
    }
    .urlkeyword {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 30px !important;
    }
    .dyndata span {
        color: #bf4545; line-height: 18px;
    }
    .rowsdyn div {
        text-align: left;
    }
    
    .rowsdyn div label input {
        position: relative;
        top: 4px;
        left: -4px;
        height: 17px;
        width: 18px;
    }
    .vermsg {
        padding: 5px 10px;
        background: #b18c30;
        color: #fdfbf7;
        border-radius: 2px !important;
        float: left;
        font-weight: 600;
    }
    /*
    .bottomshade {
        position: absolute;
        width: 100%;
        height: 100%;    
        background: rgba(255, 255, 255, 0.6);
        z-index: 9998;
        left: 0;
        right: 0;
        
    }
    .overflowhidden{
        overflow-y: hidden;
    }
   
    .alert.noteborder{
            position: fixed;
    top: 30%;
    width: 100%;
    z-index: 999999999;
    left: 0;
    }*/
</style>
<div class="row">    
    <input type="hidden" name="pageurlprofile" id="pageurlprofile" value="1" />
    <div class="col-md-12">
        <span  class="caption-subject bold uppercase captionect">Content Recommendations</span>        
        <div class="pull-right">
            <a href="javascript:;" class="btn btn-info runforpage"> <span class="glyphicon glyphicon-repeat"></span> <span class="textspn"></span> </a>
        </div>
        <div class="clearfix"></div>
        <span class="rank" style="display:block">
                       
            <?php
            global $wpdb; $user_id = $UserID = user_id(); 
            $website = get_user_meta($user_id,'website',true);
            $webchk = trim(str_replace(array("https://","http://","www."), array("","",""), $website),"/");
            $url = isset($_REQUEST['url'])?$_REQUEST['url']:'';
            $gif = TR_COUNT_PLUGIN_URL."/assets/img/ajax_loading.gif";
            $onimg = TR_COUNT_PLUGIN_URL."/assets/images/on.png";
            $ofimg = TR_COUNT_PLUGIN_URL."/assets/images/off.png";
            if($url != ''){
                $urlchk = trim(str_replace(array("https://","http://","www."), array("","",""), $url),"/"); $jk = 0;   
                $rcommend = $wpdb->get_row
                (
                    $wpdb->prepare
                    (
                        "SELECT * FROM cre_urls WHERE user_id = %d AND TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.','')) like '%s'", $user_id, $urlchk
                    )
                );
                
                ?>
                 <input type="hidden" name="pageurl" id="pageurl" value="<?php echo $url; ?>" />
                <?php
                if(empty($rcommend) || trim($rcommend->result) == ''){
                    
                    $index = 0; $is_running = 0;
                    if(!empty($rcommend)){                        
                        $is_running = isset($rcommend->is_running)?intval($rcommend->is_running):0;
                        $index = $rcommend->id; // if value < 0, mean invalid page or page is not scanned yet
                    }
                    
                    echo '<br/><div style="text-align:left;">Content Recommendations are not available </div><br/>';
                    ?>
                    <input type="hidden" name="isrunning" id="isrunning" value="<?php echo $is_running; ?>" />
                    <input type="hidden" name="pagerunorrerun" id="pagerunorrerun" value="<?php echo 'Run CRE Tool For This Page'; ?>" />
                    <input type="hidden" name="pageindex" id="pageindex" value="<?php echo $index; ?>" />
                    <?php
                }
                else{
                    // disable code for while
//                    if(isset($rcommend) && $rcommend->trigger_report == 1){
//                        $rcommen = $wpdb->get_row
//                        (
//                            $wpdb->prepare
//                            (
//                                "SELECT * FROM wp_content_recommend_hisory  WHERE user_id = %d", $user_id
//                            )
//                        );    
//                        $rslt = json_decode(($rcommen->result)); 
//                        if(!empty($rcommen) && !empty($rslt)){
//                            $rcommend = $rcommen;
//                        }
//                    }
//                    $crawl_res = json_decode($rcommend->crawl_result);
//                    $arurls = (array) $crawl_res->urls;
//                    pr($crawl_res);
                      
//                    $alldata = arsearch($data, $url);
//                    $pagedata = isset($alldata['ob'])?$alldata['ob']:array();                   
                    
                    
                    $PAGE_TITLE_RANGE = PAGE_TITLE_RANGE; //cre->minrangetite - $cre->maxrangetite";
                    $KEYWORD_TITLE_DENSITY = KEYWORD_TITLE_DENSITY; //cre->minkeydens - $cre->maxkeydens"; // 5% - min                            
                    $PAGE_DESC_RANGE = PAGE_DESC_RANGE; //cre->minrangedesc - $cre->maxrangedesc";
                    $KEYWORD_DESC_DENSITY = KEYWORD_DESC_DENSITY; //cre->minkeyedesc - $cre->maxkeyedesc"; // 5% - min

                    $MAX_HEADING_TAGS = MAX_HEADING_TAGS; //cre->maxhtags";
                    $MAX_H1_TAGS = MAX_H1_TAGS; //cre->maxh1tags";
                    $HEADING_LENGTH = HEADING_LENGTH; //cre->minheadlength - $cre->maxheadlength";
                    $PAGE_CONTENT_RANGE = PAGE_CONTENT_RANGE; //cre->mincontentrange - $cre->maxcontentrange";
                    $KEYWORD_CONTENT_DENSITY = KEYWORD_CONTENT_DENSITY; //cre->mincontentdensity - $cre->maxcontentdensity"; // 5% - min

                    $EXTRANL_LINKS = EXTRANL_LINKS; //cre->minextlinks - $cre->maxextlinks";
                    $AVG_PAGE_SIZE = AVG_PAGE_SIZE; //cre->minpagesize - $cre->maxpagesize"; // max 1 mb page size
                    $AVG_LOADING_TIME = AVG_LOADING_TIME; //cre->minloadtime - $cre->maxloadtime"; // 0 to 5 seconds
                    $TITLE_RELEVANCY = TITLE_RELEVANCY; //$cre->titlerelevancy;
                    $DESC_RELEVANCY = DESC_RELEVANCY; //$cre->descrelevancy;
                    $TEXT_RATIO = TEXT_RATIO; //cre->mintextratio - $cre->maxtextratio";

                    $OVERALL_KEY_DENSITY = OVERALL_KEY_DENSITY; //cre->maxoverdens"; // greater than equal to 3% - 
                    $OVERALL_PRIMARY_DENSITY = OVERALL_PRIMARY_DENSITY; //cre->maxprimarydens"; // greater than equal to 5%
                    
                    $algo_id = isset($rcommend->algo_id)?intval($rcommend->algo_id):0;                    
                    
                    if($algo_id > 0){
                        $credt = getcrealgo($algo_id);
                    } 
                    else{
                        $credt = getcrealgo(0);
                    }
                    
                    $curversion = 0; $idcre = 0;
                    
                    //$cre = $wpdb->get_var("SELECT credata FROM cre_algovals WHERE id = ".$algo_id);
                    
                    if(!empty($credt)){
                        $curversion = $credt->curver;
                        $idcre =  $credt->id;
                        $cre = json_decode($credt->credata);
                        //pr($cre);
                        //$cre = json_decode($cre);                            
                        $PAGE_TITLE_RANGE = "$cre->minrangetite - $cre->maxrangetite";
                        $KEYWORD_TITLE_DENSITY = "$cre->minkeydens - $cre->maxkeydens"; // 5% - min                            
                        $PAGE_DESC_RANGE = "$cre->minrangedesc - $cre->maxrangedesc";
                        $KEYWORD_DESC_DENSITY = "$cre->minkeyedesc - $cre->maxkeyedesc"; // 5% - min

                        $MAX_HEADING_TAGS = $cre->maxhtags;
                        $MAX_H1_TAGS = $cre->maxh1tags;
                        $HEADING_LENGTH = "$cre->minheadlength - $cre->maxheadlength";
                        $PAGE_CONTENT_RANGE = "$cre->mincontentrange - $cre->maxcontentrange";
                        $KEYWORD_CONTENT_DENSITY = "$cre->mincontentdensity - $cre->maxcontentdensity"; // 5% - min

                        $EXTRANL_LINKS = "$cre->minextlinks - $cre->maxextlinks";
                        $AVG_PAGE_SIZE = "$cre->minpagesize - $cre->maxpagesize"; // max 1 mb page size
                        $AVG_LOADING_TIME = "$cre->minloadtime - $cre->maxloadtime"; // 0 to 5 seconds
                        $TITLE_RELEVANCY = $cre->titlerelevancy;
                        $DESC_RELEVANCY = $cre->descrelevancy;
                        $TEXT_RATIO = "$cre->mintextratio - $cre->maxtextratio";

                        $OVERALL_KEY_DENSITY = "$cre->maxoverdens"; // greater than equal to 3% - 
                        $OVERALL_PRIMARY_DENSITY = "$cre->maxprimarydens"; // greater than equal to 5%
                    }
                    
                   
                    $pagedata = json_decode(trim($rcommend->result));
                    $is_running = isset($rcommend->is_running)?intval($rcommend->is_running):0;
                    $index = $rcommend->id; // if value < 0, mean invalid page or page is not scanned yet
                    
                    ?>
                    <input type="hidden" name="isrunning" id="isrunning" value="<?php echo $is_running; ?>" />
                    <input type="hidden" name="pagerunorrerun" id="pagerunorrerun" value="<?php echo !empty($pagedata)?'Re-Run CRE Tool For This Page':'Run CRE Tool For This Page'; ?>" />
                    <input type="hidden" name="pageindex" id="pageindex" value="<?php echo $index; ?>" />
                    <?php
                    if(empty($pagedata)){
                         echo '<br/><div style="text-align:left;">Content Recommendations are not available</div><br/>';                         
                    }
                    else{                        
                        $analys = $pagedata;     
                        $stscode = isset($analys->pagestatus)?$analys->pagestatus:200; 
                        if($stscode == 404){
                            echo '<br/><div style="text-align:left;">404 - Page Not Found</div><br/>';    
                        }
//                        else if($stscode == 0){
//                            echo '<br/><div style="text-align:left;">Page Status is 0. No Data Found</div><br/>';    
//                        }
                        else{
                        ?>
                        <div class="clearfix"></div>
                        <div class="margin-bottom-15 issuesspan">
                            <span>Title issues : <?php echo isset($analys->issues_count->title_issues)?$analys->issues_count->title_issues:0; ?></span>
                            <span>Meta issues : <?php echo isset($analys->issues_count->meta_issues)?$analys->issues_count->meta_issues:0; ?></span>
                            <span>Content issues : <?php echo isset($analys->issues_count->content_issues)?$analys->issues_count->content_issues:0; ?></span>
                            <span>Heading issues : <?php echo isset($analys->issues_count->heading_issues)?$analys->issues_count->heading_issues:0; ?></span>
                            <span>Link issues : <?php echo isset($analys->issues_count->link_issues)?$analys->issues_count->link_issues:0; ?></span>
                            <span>Image issues : <?php echo isset($analys->issues_count->image_issues)?$analys->issues_count->image_issues:0; ?></span>
                            <p class="pull-right">Last Run : <?php echo $rcommend->rundate; ?></p>
                        </div>
                        <div class="clearfix"></div>
                        <?php                         
                            $scorecls = 'redscore';
                            if( $analys->score >= 51 && $analys->score <= 79 ){
                                $scorecls = 'yellowscore';
                            }
                            else if($analys->score >= 80){
                                $scorecls = 'greenscore';
                            }
                            $scor = $analys->score;
//                            if($scor < 50){
//                                $scor = 50;
//                            }
                        ?>
                        <div class="contentscore"><span>Page Status : <?php echo $stscode; ?> <?php echo codetostatus($stscode); ?> </span> 
                            <span class="pull-right <?php echo $scorecls; ?>">Content Score : <span><?php echo $scor; ?></span> </span></div>
                        <div class="clearfix"></div>
                        
                        <input type="hidden" id="hidcontentrec" name="hidcontentrec" value="1" />
                        <input type="hidden" id="hidgif" name="hidgif" value="<?php echo $gif; ?>" />
                        <input type="hidden" id="onimg" name="onimg" value="<?php echo $onimg; ?>" />
                        <input type="hidden" id="ofimg" name="ofimg" value="<?php echo $ofimg; ?>" />
                        
                        <div class="urldatacontent">
                            <?php                            
//                            include_once(get_template_directory() . '/analytics/my_functions.php');
//                            include_once(get_template_directory() . '/common/report-function.php');
//                            $keywords_order = keywords_order($UserID);      
//                            $from_date = ''; $to_date = '';
//                            $keywords_report = keywords_report($UserID, $from_date, $to_date, $synonyms = 1);
                            
                            if($curversion != $algo_id){
                                echo "<div class='vermsg'>Your version of the CRE is out of date. Please rerun this page to get updated data.</div>";
                            }                            
                            if($analys->keyword != ''){
                                include_once 'page_url_profile_with_keword.php';
                            }
                            else{
                                include_once 'page_url_profile_without_keyword.php';
                            }
                        }
                            ?>
                        </div>                        
                        <?php
                    }                    
                }
                include_once 'links_modal.php';
            }
            ?>
        </span>
    </div>
</div>
<hr>