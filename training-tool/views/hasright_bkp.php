<?php

$enable_permission = $course->enable_permission;
if($enable_permission == 1){
    $pusers = explode(",", $course->user_ids);    
    $userlog = new WP_User($user_id);
    $ulog_role =  $userlog->roles[0];    
    if($ulog_role == 'administrator' || in_array($user_id, $pusers)){
        $is_enrolled = auto_enroll_user($course_id, $user_id);
    }
    else{
        $is_enrolled = 0;
    }
}
else{
    $is_accerlator = strtolower(get_user_meta($user_id, ACCESS_ROLE, true));

    if ($is_accerlator == 'yes') {
            $is_enrolled = auto_enroll_user($course_id, $user_id);
       
    } else {

         $is_enrolled = $wpdb->get_var
                    (
                    $wpdb->prepare
                            (
                            "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
                    )
            );


    }
}


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
