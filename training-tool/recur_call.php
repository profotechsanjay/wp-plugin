<?php

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;
$mentorcall = $wpdb->prefix . 'mentorcall';
$usertabl = $wpdb->prefix."users";
$courses = $wpdb->prefix . 'courses';

/* if call is accepted and status is active and is recur, this works
 * job run on daily basis, but check record of last day, so call date ended, and we can schedule a new call automatically.
 * 
 */

$start_date = date("Y-m-d 00:00:00", strtotime("-1 day"));
$end_date = date("Y-m-d 23:59:59", strtotime("-1 day"));

$mentorcals = $wpdb->get_results
(
    $wpdb->prepare
    (
        "SELECT * FROM " . $mentorcall." WHERE recur_call = 1 AND status = 'active' AND parent_id = 0 AND is_accepted = 1 AND "
            . "mentor_call BETWEEN '$start_date' AND '$end_date' ORDER BY created_dt DESC",""
    )
);

foreach($mentorcals as $mentorcal){
        
    $calldate = $mentorcal->mentor_call;
    $calldate = date("Y-m-d H:i:s", strtotime("$calldate +2 week"));
    $guid = md5(mt_rand(9999, 100099999).time());
    $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . mentorcall()  . " (guid, course_id, user_id, link, mentor, mentor_call, mentor_id, created_by, recur_call, is_accepted) "
                            . "VALUES (%s, %d, %d, %s, %s, '%s', %d, %d, %d, %d)", 
                            $guid, $mentorcal->course_id, $mentorcal->user_id, $mentorcal->link, '', $calldate, $mentorcal->mentor_id, $mentorcal->created_by, 0, 1
                )
        );
    
    
}


