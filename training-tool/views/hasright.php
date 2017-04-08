<?php

$pusers = explode(",", $course->user_ids);    
$userlog = new WP_User($user_id);
$ulog_role =  $userlog->roles[0];    


$is_enrolled = $wpdb->get_var
        (
        $wpdb->prepare
                (
                "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
        )
);

//if($ulog_role == 'administrator'){
//    $is_enrolled = 0;
//    //$is_enrolled = auto_enroll_user($course_id, $user_id);
//}
//else{
//    
//    $is_enrolled = $wpdb->get_var
//            (
//            $wpdb->prepare
//                    (
//                    "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
//            )
//    );
//    
//    
//}


function auto_enroll_user($course_id, $user_id){
    global $wpdb;
    $now = date("Y-m-d H:i:s");
    $is_enrolled = $wpdb->get_var
            (
            $wpdb->prepare
                    (
                    "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
            )
    );

    if ($is_enrolled == 0) {
        $now = date("Y-m-d H:i:s");
        $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . enrollment() . " (course_id, user_id, created_dt) "
                        . "VALUES (%d, %d, '%s')", $course_id, $user_id, $now
                )
        );
        $is_enrolled = 1;
    }
    return $is_enrolled;
}

?>
