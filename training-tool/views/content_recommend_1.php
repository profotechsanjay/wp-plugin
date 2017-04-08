<?php

login_check();
wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
wp_enqueue_style('chosen.css', TR_COUNT_PLUGIN_URL .'/assets/css/chosen.css');  
wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', TT_VERSION);
wp_enqueue_script('chosen.jquery.js', TR_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', TT_VERSION);

//$time1 = microtime(TRUE);
//$url = "www.medstarhealth.org/content/plugins/dzs-videogallery/videogallery/vplayer.css?ver=3ddd8be";

//echo get_remote_size($url); die;
//
//$url = get_user_meta(user_id(),'website',true);
//require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
//$browser = &new SimpleBrowser();
//                    
//$driver = array();
//$data['url'] = $url;
//$data['keyword'] = "";
//$res = page_analysis(json_decode(json_encode($data)),$browser);
//pr($res);
//die;

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

        <div id="primary" class="site-content" style="min-height: 400px">

            <div id="content" role="main">

                <div class='col-md-12'>
                    <h4>CRE Dashboard                  
                        <div class="pull-right">
                            
                            <a href="javascript:;" class="checkga btn <?php echo $ga == 0?'btn-danger':'btn-success'; ?>"><?php echo $ga == 0?'Check':'Re-Check'; ?> GA Connection</a>
                            <a href="javascript:;" class="checkcc btn <?php echo $ct == 0?'btn-danger':'btn-success'; ?>"><?php echo $ct == 0?'Check':'Re-Check'; ?> Conversion Code</a>
                            
                            <?php if($ga == 1 && $ct == 1){ ?>
                            <a href="javascript:;" class="crerun btn btn-primary">Run CRE</a>
                            <?php } ?>
                        </div>

                    </h4>
                    <div class='row'>
                        <div class='col-md-12'>                        
                            <?php
                            if($rcommend->trigger_report == 1){
                                ?>
                                <div class="alert alert-info">
                                    <strong> Note : </strong>
                                    Content Recommendation Tool (CRE) is fetching data your keywords. You will get notified once fetch all data.
                                    OR you can refresh browser after some time to check data.
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class='row'>
                        <div class='col-md-12'>                        
                            <?php
                            if($rcommend->result == "" || $rcommend->result == NULL){
                                ?>
                                <div class="well">
                                    No Result
                                </div>
                                <?php
                            }
                            else{
                                
                                $data = json_decode($rcommend->result);
                                //$data = json_decode(stripcslashes($rcommend->result));
                                
                                ?>
                                <table class="table table-bordered tblrecomconettn">
                                    <thead>
                                    <tr>
                                        <th style="width: 15%;">Keyword</th>
                                        <th style="width: 17%;">Target URL</th>
                                        <th style="width: 15%;">Title Found</th>
                                        <th style="width: 15%;">Meta Desc</th>
                                        <th style="width: 15%;">Page Size</th>
                                        <th style="width: 15%;">Page Load Time</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach($data as $dat){
                                            $analysis = $dat->analysis;
                                            $aprtsurl = parse_url($analysis->url);
                                            $i = $i+1;
                                            
                                            $total_pagesize = $analysis->page_size + $analysis->arcss->css_size + $analysis->js->js_size + $analysis->images->img_size;
                                            ?>

                                            <tr>
                                                <td><?php echo $analysis->keyword;  ?></td>
                                                <td> <a target="_blank" alt="<?php echo $analysis->url; ?>" href="<?php echo $analysis->url; ?>">
                                                    <?php echo rtrim($aprtsurl['path'],"/");  ?>
                                                </a>
                                                </td>
                                                <td><?php echo $analysis->title->title_tag == 1?"<img class='imgoffon' src='$onimg' />":"<img class='imgoffon' src='$ofimg' />";  ?></td>
                                                <td><?php echo $analysis->desc->is_meta_desc == 1?"<img class='imgoffon' src='$onimg' />":"<img class='imgoffon' src='$ofimg' />";  ?></td>
                                                <td> <?php echo $total_pagesize;  ?> bytes</td>
                                                <td> <?php echo number_format($analysis->page_speed, 2);  ?> seconds</td>
                                                <td>
                                                    <a href="<?php echo $baseurl; ?>?page_dashboard&page_detail=<?php echo $i; ?>" class="btn btn-primary">Page Dashboard</a>
                                                </td>
                                            </tr>

                                            <?php


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

        $sql = 'SELECT meta_key FROM wp_usermeta WHERE = ' . $location_id . ' and user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
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