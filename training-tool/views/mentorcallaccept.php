<?php
$callid = intval($_REQUEST['accept_call']);
$mentorcall = $wpdb->get_row
(
        $wpdb->prepare
                (
                "SELECT * FROM " . mentorcall() . " WHERE id = %d", $callid
        )
); 
if(empty($mentorcall)){
    die('Invalid Mentor Call');
}
$guid = isset($_REQUEST['guid'])?htmlspecialchars($_REQUEST['guid']):'';
$guid = trim($guid);
if(trim($mentorcall->guid) != $guid){
    die('Invalid guid Or Link already opened.');
}

$user_id = $mentorcall->user_id;
$user = get_user_by('id', $user_id );

if ( is_wp_error( $user ) ){
    die('Invalid User');
}
$ilog = 0;
if(!is_user_logged_in()){
    $ilog = 1;    
}
else{
    $c_id = get_current_user_id();
    if($user_id != $c_id){
        $ilog = 1;
    }
}

if($ilog == 1){
    
    wp_clear_auth_cookie();
    wp_set_current_user ( $user->ID, $user_login );
    wp_set_auth_cookie  ( $user->ID );
    do_action( 'wp_login', $user_login );
    wp_redirect($_SERVER['REQUEST_URI']);
    
}

if($mentorcall->is_accepted == 0){
    $guid = md5(mt_rand(9999, 100099999).time());
    $wpdb->query
            (
            $wpdb->prepare
                    (
                    "UPDATE " . mentorcall()  . " SET is_accepted = %d, guid = %s"
                        . " WHERE id = %d", 
                        1, $guid, $callid
            )
    );    
    
    /* Mail for accepted call */
    call_accepted($mentorcall,$user);
    
}

$url = site_url()."/".PAGE_SLUG."?call_status=accepted&callid=$callid";
wp_redirect($url);


function call_accepted($mentorcall,$user){
    
    global $wpdb;
    $course = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT * FROM " . courses() . " WHERE id = %s", $mentorcall->course_id
        )
    );
    
    $mentor_id = $mentorcall->mentor_id;
    $mentor = get_user_by('id', $mentor_id );
    
    $emails = $mentor->data->user_email;    
    
    $date = date("D d M Y, h:i a",  strtotime($mentorcall->mentor_call));
    $site_name = get_bloginfo('name');    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
    
    $subj = $site_name." - Call accepted";
    $msg = "Hi ".$mentor->data->display_name.", <br/><br/>";
    $msg .= "User <strong>".$user->data->display_name."</strong> has accepted your call for course <strong>$course->title</strong> On $date<br/><br/>";    
    $msg .= "Thanks, <br/>";
    $msg .= $site_name;   
    //$emails = "parambir.rudra@gmail.com";
    
    $re = wp_mail( $emails, $subj, $msg, $headers );
    
}

?>
