<?php
if(isset($_GET['function']) && (($_GET['function'] == 'location_full_report') || ($_GET['function'] == 'competitor_full_report')
        || $_GET['function'] == 'location_traffic_report' || $_GET['function'] == 'conversion_report' || $_GET['function'] == 'rank_target_report' 
        || $_GET['function'] == 'visibility_report' || $_GET['function'] == 'location_change' || 
        $_GET['function'] == 'citation_report' || $_GET['function'] == 'siteaudit_report')){
                
    $location_id = isset($_GET['location_id'])?intval($_GET['location_id']):0;

    $user_id = $wpdb->get_var
    (
        $wpdb->prepare
        (
            "SELECT MCCUserId FROM ". client_location()." WHERE id = %d",$location_id
        )
    );                
    if($user_id > 0){
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['location'] = $location_id;           
        $_SESSION["Current_user_live"] = $user_id;
        
        if($_GET['function'] == 'location_full_report'){
            header('Location: '.site_url()."/keywords-report");
        }
        else if($_GET['function'] == 'location_change'){            
            $daily_data_url = site_url() . '/cron/pullga.php?client_id=' . $_REQUEST['client_id'] . '&start_date=' . $_REQUEST['start_date'] . '&end_date=' . $_REQUEST['end_date'] . '&save_mode=1&page=analytics';            
            header('Location: '.$daily_data_url);
        }
        else if($_GET['function'] == 'location_traffic_report'){
            header('Location: '.site_url()."/traffic-reports");
        }
        else if($_GET['function'] == 'competitor_full_report'){
            header('Location: '.site_url()."/competitor-report");
        }
        else if($_GET['function'] == 'conversion_report'){
            header('Location: '.site_url()."/conversion-url-report");
        }
        else if($_GET['function'] == 'rank_target_report'){
            header('Location: '.site_url()."/ranking-url-vs-target-url");
        }
        else if($_GET['function'] == 'visibility_report'){
            header('Location: '.site_url()."/executive-summary-report");
        }
        else if($_GET['function'] == 'citation_report'){
            header('Location: '.site_url()."/citation-tracker");
        }
        else if($_GET['function'] == 'siteaudit_report'){
            header('Location: '.site_url()."/site-audit-url");
        }
    }
    else{
        header('Location: '.site_url());
    }
    
}
else{
    header('Location: '.site_url());
}
?>
