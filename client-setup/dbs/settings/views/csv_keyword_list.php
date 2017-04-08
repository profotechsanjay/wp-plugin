<?php
$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once $path . '/global_config.php';
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;
$Fld = array();
if(isset($_SESSION["Current_user_live"]) && $_SESSION["Current_user_live"] > 0){
    $UserID = $_SESSION["Current_user_live"];
    $UserID = isset($_GET['mccuserid'])?intval($_GET['mccuserid']):$UserID;
    $csv_type = $_GET['csv_type'];    
    ob_end_clean();
    if ($csv_type == 'keyword_list') {
        $th = array('Primary Keyword', 'Synonym 1', 'Synonym 2', 'Synonym 3', 'Synonym 4', 'Synonym 5', 'Landing Page','Live Date', 'Home Page', 'Resource Page', 'Target Keyword', 'Activation', 'Notes to Writers');
        $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
        
        if (!empty($keywordDat)) {

            $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
            $primarylander = $keywordDat["primarylander"];
            $secondarylander = $keywordDat["secondarylander"];
            $Additionalsnotes = $keywordDat["Additionalsnotes"];

            $landingpage = $keywordDat["landing_page"];
            $livedate = $keywordDat['live_date'];
            $activation = $keywordDat["activation"];
            $target_keyword = $keywordDat["target_keyword"];
            $delete = $keywordDat["delete"];
        }

        $KeyWordQuery = $wpdb->query('select * from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%"');
        $ks = 1;

        for ($j = 0; $j < $KeyWordQuery; $j++) {

            $keywords = get_user_meta($UserID, "LE_Repu_Keyword_" . $ks, TRUE);

            if (!empty($keywords)) {
                if ($delete[$j] != 1) {
                $Fld[$j][] = $keywords;

                for ($h = 0; $h < 5; $h++) {
                    $Fld[$j][] = $Synonyms_keyword[$j][$h];
                }
                $Fld[$j][] = $landingpage[$j][0];
                $Fld[$j][] = $livedate[$j][0];
                $Fld[$j][] = $primarylander[$j];
                $Fld[$j][] = $secondarylander[$j];

                $target_key_val = 'No';
                if($target_keyword[$j] == 'Yes'){
                    $target_key_val = 'Yes';
                }
                $Fld[$j][] = $target_key_val;

                $active_type = 'Active';
                if($activation[$j] == 'inactive'){
                    $active_type = 'Inactive';
                }
                $Fld[$j][] = $active_type;            

                $Fld[$j][] = $Additionalsnotes[$j];
                }
            }
            $ks++;
        }
    } else if ($csv_type == 'kpi_tracker') {
        $th = $_SESSION['csvArr_head'];
        $ks = 0;
        $limit = count($_SESSION['csvArr']) / count($_SESSION['csvArr_head']);
        for ($i = 0; $i < $limit; $i++) {
            for ($j = 0; $j < count($_SESSION['csvArr_head']); $j++) {
                $Fld[$i][] = $_SESSION['csvArr'][$ks];
                $ks++;
            }
        }
    } else if ($csv_type == 'site_audit_tracker') {
        $th = array('Audit Title', 'Audit Result', 'Audit Date');
        $ks = 0;
        $limit = count($_SESSION['site_audit_tracker']) / 3;
        for ($i = 0; $i < $limit; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $Fld[$i][] = $_SESSION['site_audit_tracker'][$ks];
                $ks++;
            }
        }
    }

    $FilePath = $csv_type . '_' . $UserID . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header("Cache-Control: no-store, no-cache");
    header('Content-Disposition: attachment; filename=' . $FilePath);

    $fp = fopen('php://output', "w");

    fputcsv($fp, $th);

    foreach ($Fld as $fields1) {
        fputcsv($fp, $fields1);
        ob_flush();
    }

    fclose($fp);
}
exit();

?>
