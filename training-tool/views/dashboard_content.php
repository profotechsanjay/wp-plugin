<?php
global $wpdb;
$user_id = user_id();
$tpages = web_target_pages($user_id);

// condition if has target page and organic traffic ( > 0)
// condition if has target page and organic traffic ( = 0)
// condition if organic traffic ( > 0) and no target page
// condition if organic traffic ( = 0) and no target page, fetch page from all pages order by issues desc
// No Page Found - value 0
$conn = anconn();
if($tpages != ''){
        
    $sql = "SELECT PageURL, sum(organic) as organic_val FROM `short_analytics_$user_id` WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (PageURL, 'http://', ''),'https://',''),'www.','')) IN ($tpages) order by organic_val desc LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        $data = $result->fetch_object();
        $pageURL = $data->PageURL;
        $organic_val = $data->organic_val;
        if($organic_val > 0){
            $tpage = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $pageURL),"/"));
            // condition if has target page and organic traffic ( > 0)
            $rcommendtions = $wpdb->get_row
            (
                $wpdb->prepare
                (
                    "SELECT id, url, total_issues, result FROM cre_urls WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.',''))  = '$tpage' AND user_id = %d ORDER BY total_issues DESC", $user_id
                )
            );                        
        }        
    }
    
    if(empty($rcommendtions)){
        
        // condition if has target page and organic traffic ( = 0)
        $rcommendtions = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT id, url, total_issues, result FROM cre_urls WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.','')) IN($tpages) AND user_id = %d ORDER BY total_issues DESC LIMIT 1", $user_id
            )
        );
       
    }
    
}

if(empty($rcommendtions)){

    $allpages = web_all_pages($user_id);
    
    // condition if organic traffic ( > 0) and no target page
    $sql = "SELECT PageURL, sum(organic) as organic_val FROM `short_analytics_$user_id` WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (PageURL, 'http://', ''),'https://',''),'www.','')) IN ($allpages) order by organic_val desc LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        $data = $result->fetch_object();
        $pageURL = $data->PageURL;
        $organic_val = $data->organic_val;
        if($organic_val > 0){
            $tpage = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $pageURL),"/"));
	
            // condition if has target page and organic traffic ( > 0)
            $rcommendtions = $wpdb->get_row
            (
                $wpdb->prepare
                (
                    "SELECT id, url, total_issues, result FROM cre_urls WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (url, 'http://', ''),'https://',''),'www.',''))  like '$tpage' AND user_id = %d ORDER BY total_issues DESC", $user_id
                )
            );  
                 
        }        
    }
        
} 


if(empty($rcommendtions)){
    
    // condition if organic traffic ( = 0) and no target page, fetch page from all pages order by issues desc
    $rcommendtions = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT id, url, total_issues, result FROM cre_urls WHERE user_id = %d ORDER BY total_issues DESC", $user_id
        )
    );

}


// In Last if no data - value filled 0

$pagedata = isset($rcommendtions->result)?json_decode($rcommendtions->result):'';

if(isset($pagedata->issues_count) && $pagedata->issues_count != ''){
    $issues = array(
        'title' => array('value' => $pagedata->issues_count->title_issues, 'color' => '#2b94e1'),
        'meta' => array('value' => $pagedata->issues_count->meta_issues, 'color' => '#a52600'),
        'content' => array('value' => $pagedata->issues_count->content_issues, 'color' => '#bf9e6b'),
        'heading' => array('value' => $pagedata->issues_count->heading_issues, 'color' => '#4fae33'),
        'link' => array('value' => $pagedata->issues_count->link_issues, 'color' => '#ff7f00'),
        'image' => array('value' => $pagedata->issues_count->image_issues, 'color' => '#6666cc')
    );
}
else{
        $issues = array(
        'title' => array('value' => 0, 'color' => '#2b94e1'),
        'meta' => array('value' => 0, 'color' => '#a52600'),
        'content' => array('value' => 0, 'color' => '#bf9e6b'),
        'heading' => array('value' => 0, 'color' => '#4fae33'),
        'link' => array('value' => 0, 'color' => '#ff7f00'),
        'image' => array('value' => 0, 'color' => '#6666cc')
    );
}

$totalissues = 0;
foreach($issues as $issue){
    $totalissues = $totalissues + $issue['value'];
}

?>
<style>
    .urldash{
        word-wrap: break-word;
    }
    .tag {
    background-color: #2b94e1;
    color: #ffffff;
    display: inline-block;
    font-size: 16px;
    height: 30px;
    margin-right: 8px;
    padding: 4px 0;
    text-align: center;
    vertical-align: middle;
    width: 30px;
}

.breakdowndiv ul {
    display: block;
    list-style: outside none none;
    margin: 0;
    overflow: hidden;
    padding: 0;
}

.breakdowndiv ul li {
    float: left;
    margin: 0;
    margin-bottom: 15px;
    width: 50%;
}
    
</style>
<div class="min-ht">
    <div class="ves-content">
      <div class="pg-cnt">
        <label>page link</label>
        
        <?php 
        $urllnk = 'No Recommendation Found'; $urltxt = '';
        if(isset($rcommendtions->url) && $rcommendtions->url != ''){
            $urlshow = appendhttp($rcommendtions->url);
            $urlpars = parse_url($urlshow);

            if(isset($urlpars['path']) && $urlpars['path'] != ''){
                $urltxt = $urlpars['path'];
            }
            if(isset($urlpars['query']) && $urlpars['query'] != ''){
                $urltxt .= '?'.$urlpars['query'];
            }
            if($urltxt == ''){
                $urltxt = $rcommendtions->url;
            }
            $urllnk = "<a href = '".site_url()."/url-profile?url=$rcommendtions->url'>$urltxt</a>";

        }
        echo '<div class="p_link">'.$urllnk.'</div>';


        ?>
      </div>
    </div>
    <div class="pg-cnt">
      <label>breakdown</label>
      <ul>
        <li> <span class="label" style='background-color: <?php echo $issues['title']['color']; ?>'>Ti-<?php echo $issues['title']['value']; ?></span> </li>
        <li> <span class="label" style='background-color: <?php echo $issues['meta']['color']; ?>'>Me-<?php echo $issues['meta']['value']; ?></span> </li>
        <li> <span class="label" style='background-color: <?php echo $issues['content']['color']; ?>'>Co-<?php echo $issues['content']['value']; ?></span> </li>
        <li> <span class="label" style='background-color: <?php echo $issues['heading']['color']; ?>'>He-<?php echo $issues['heading']['value']; ?></span> </li>
        <li> <span class="label" style='background-color: <?php echo $issues['link']['color']; ?>'>Li-<?php echo $issues['link']['value']; ?></span> </li>
        <li> <span class="label" style='background-color: <?php echo $issues['image']['color']; ?>'>Im-<?php echo $issues['image']['value']; ?></span> </li>
      </ul>
    </div>
</div>
<?php
$linkissues = '';
if(isset($rcommendtions->url) && $rcommendtions->url != ''){
    $linkissues = site_url().'/url-profile/?url='.$rcommendtions->url;
}
?>
<!--            <a href="<?php echo $linkissues; ?>">Open Insight</a>-->
<div class="clear"></div>           
<div class="sales-buttn"> <a href="<?php echo site_url().'/'.CRE_DASH; ?>">see all content recommendations</a> </div>