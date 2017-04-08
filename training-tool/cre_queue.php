<?php

/** Queue System for Content Recommendtion Tool - 22 Nov 2016 - Rudra Innovative Soffware Pvt Ltd */
/** Need to run for cron job, so regularly check queue */
/** Run from only main admin setup */
// run after every 5 minutes */5 * * * * wget http://mcc.enfusenlocal.com//wp-content/plugins/training-tool/cre_queue.php -O /dev/null

error_reporting(0);
$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/global_config.php';
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

//$pageindex = "29";
//$queueprocessed = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30';
//$queueprocessed = explode(",", $queueprocessed);
//$newstr = '';
//foreach($queueprocessed as $queueproc){
//    if(intval($queueproc) != $pageindex){
//        $newstr .= $queueproc.',';
//    }
//}
//if($newstr != ''){
//    $newstr = substr($newstr, 0, -1);
//}
//pr($newstr); die;

//$ar = json_decode(stripcslashes($str));
//$ar = (array) $ar;
//pr($ar); die;
//unset($ar[6]);
//$ar = array_values($ar);
//unset($ar[7]);
//$ar = array_values($ar);
//unset($ar[8]);
//$ar = array_values($ar);
//unset($ar[9]);
//$ar = array_values($ar);
////$ar = array_diff( $ar, array("https://www.enfusen.com/why-should-i-use-just-in-time-marketing/") );
//pr($ar);
//die;

function queue_exe(){
    global $wpdb;
    $queue = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT * FROM cre_queue ORDER BY id ASC LIMIT 1", ""
        )
    );
    
    $crelimit = CRE_QUEUE_LIMIT;
    if(!empty($queue)){
                
        $fromlimit = $queue->lt;
        $lastval = 0;
        $agency_url = $queue->agency_url;
        $urlremote = $agency_url."/wp-admin/admin-ajax.php";
        $user_id = $queue->user_id;                
        $db = $queue->db;
        $keyval = md5($db);
        $urls = json_decode(stripcslashes($queue->urls));
                        
        
        if($_SERVER['SERVER_ADDR'] == '127.0.0.4' || $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
            date_default_timezone_set("Asia/Kolkata");
        }        
        
        $queueprocessed = isset($queue->processed)?trim($queue->processed):'';        
        $datequeue = strtotime($queue->updated_dt); 
//        pr($queue);
//        pr('Last Processed: '. $queue->updated_dt.' = Current: '.date("Y-m-d H:i:s"));
        $now = time();
        echo $diff = floor(($now - $datequeue) / 60);
        //pr($queueprocessed);
        if(trim($queueprocessed) != '') {
            if($diff > CRE_REPROCESS_TIME){
                // reprocess existing queue if time difference betwen last updation time and current time more than CRE_REPROCESS_TIME time (30 minutes) - like no wok last 30 minutes
                $reporecessvals = explode(",", $queueprocessed);

                foreach($reporecessvals as $reporeces){
                    $keyid = trim($reporeces);
                    if(is_object($urls)){
                        $rs = $urls->{$keyid};
                    }
                    else{
                        $rs = $urls["$keyid"];
                    }  
                    if(!empty($rs)){
                        if(is_object($rs)){
                            $urlvalue = $rs->{0};
                        }
                        else{
                            $urlvalue = $rs[0];
                        }

                        $params = array(
                            'urlremote' => $urlremote,
                            'key' => $keyval,
                            'url' => $urlvalue,
                            'pageindex' => $keyid,
                            'user_id' => $user_id,
                            'param' => 'singlepageanalysis',
                            'action' => 'training_lib',
                            'queue_id' => $queue->id
                        );                    

                        foreach ($params as $kevl => &$val) {
                            if (is_array($val)) $val = implode(',', $val);
                            $post_params[] = $kevl.'='.urlencode($val);
                        }                    
                        $post_string = implode('&', $post_params);                    
                        silent_post($params,$urlremote);                     
                    }
                }  
                
                if(!empty($reporecessvals)){
                    $wpdb->query
                    (
                        $wpdb->prepare
                        (
                            "UPDATE cre_queue SET updated_dt = NOW() WHERE id = %d", $queue->id
                        )
                    );
                }
                

            }
        }
                
        //@mail("parambir.rudra@gmail.com","Before queue started", json_encode($queueprocessed));
        if($queueprocessed == ''){
        //if(1){            
            
            if(is_object($urls)){
                $totalurls = count((array) $urls);
            }
            else{
                $totalurls = count($urls);
            }
                       
            if($fromlimit > $totalurls){
                
                //@mail("parambir.rudra@gmail.com","Queue Empty", "fromlimit : $fromlimit, totalurls : $totalurls, Queue : " . json_encode($queue));
                //set trigger report to 0 and email to custom that all urls are scanned
                $params = array(
                    'urlremote' => $urlremote,                        
                    'user_id' => $user_id,
                    'key' => $keyval,
                    'param' => 'cretriggerstop',
                    'action' => 'training_lib'                        
                );
                
                foreach ($params as $key => &$val) {
                    if (is_array($val)) $val = implode(',', $val);
                    $post_params[] = $key.'='.urlencode($val);
                }

                $post_string = implode('&', $post_params);                
                silent_post($params,$urlremote);                
                
                // delete record from queue                
                $wpdb->query
                (
                    $wpdb->prepare
                    (
                        "DELETE FROM cre_queue WHERE id = %d", $queue->id
                    )
                );
                // execute same queue again
                queue_exe();
            }
            else{                           

                $flg = 0;
                $urllist = array();
                // queue system - working
                $ik = 0;
                foreach($urls as $keyid => $urlvalue){  
                    
                    if(is_object($urlvalue)){
                        $urlvalue = trim($urlvalue->{0});
                    }
                    else if(is_array($urlvalue)){
                        $urlvalue = trim($urlvalue[0]);
                    }                    
                    else{
                        $urlvalue = trim($urlvalue);
                    }
                    if($urlvalue == ''){
                        continue;
                    }
                    
                    $ik++;
                    if($ik < $fromlimit){            
                        // skip rows that are executed
                        continue;
                    }                    
                    
                    if($flg >= $crelimit){
                        break;
                    }              
                    $lastval = $ik;
                    $urllist[] = $keyid;
                    //array_push($urllist, $key);                    
                    $params = array(
                        'urlremote' => $urlremote,
                        'key' => $keyval,
                        'url' => $urlvalue,
                        'pageindex' => $keyid,
                        'user_id' => $user_id,
                        'param' => 'singlepageanalysis',
                        'action' => 'training_lib',
                        'queue_id' => $queue->id
                    );                    
                    
                    foreach ($params as $kevl => &$val) {
                        if (is_array($val)) $val = implode(',', $val);
                        $post_params[] = $kevl.'='.urlencode($val);
                    }                    
                    $post_string = implode('&', $post_params);       
                    //@mail("parambir.rudra@gmail.com","Queue working for Url : $urlvalue", "Url : $urlvalue,  Agency: $urlremote, pageindex: $keyid, user_id: $user_id");
                    silent_post($params,$urlremote);                    
                    $flg++;
                    
                }                
                
                //@mail("parambir.rudra@gmail.com","CRE Queue processed from index $fromlimit for $agency_url", json_encode($urllist));
                $lastval = $lastval + 1;           
                $urllist = implode(",", $urllist);                
                $wpdb->query
                (
                    $wpdb->prepare
                    (
                        "UPDATE cre_queue SET lt = %d, processed = %s WHERE id = %d", $lastval, $urllist, $queue->id
                    )
                );
            }
        }
       
    }
}

queue_exe();