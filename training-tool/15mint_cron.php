<?php

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/global_config.php';
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;

/* For Announcement Plugin- check if announcement expire - changes status*/
$announcements = $wpdb->prefix . 'en_announcements';
$todaydate = date("Y-m-d H:i:s");
$wpdb->query
    (
        $wpdb->prepare
            (
            "UPDATE ".$announcements." SET status = 2 WHERE expire_date < '%s'",$todaydate
    )
);
/* For Announcement Plugin- check if announcement expire - changes status*/


$mentorcall = $wpdb->prefix . 'mentorcall';
$usertabl = $wpdb->prefix."users";
$courses = $wpdb->prefix . 'courses';

$date = date("Y-m-d H:i",strtotime("-15 minutes"));
$mentorcals = $wpdb->get_results
    (
        $wpdb->prepare
            (
            "SELECT m.*, u.display_name,u.user_email,me.display_name as mname,c.title FROM " . $mentorcall." m LEFT JOIN " . $usertabl ." u ON m.user_id = u.ID "
            . "LEFT JOIN " . $courses." c ON  m.course_id = c.id "
            . "LEFT JOIN " . $usertabl." me ON m.mentor_id = me.ID"
                . " WHERE m.status = 'active' AND m.is_accepted = 1 AND "
                . "m.mentor_call like '%s' ORDER BY m.created_dt DESC","%$date%"
    )
);

foreach($mentorcals as $mentorcal){
        
    $site_name = MCC_SITE_NAME;
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            
    $template = tt_get_template("mentor_call_reminder");            
    $subj = $template->subject; 
    $subj = str_replace("{{course_title}}", $mentorcal->title, $subj);

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{mentor_name}}','{{course_title}}','{{call_date}}','{{meeting_link}}','{{site_name}}'),
            array($mentorcal->display_name,$mentorcal->mname,$mentorcal->title,$date,$mentorcal->link,$site_name), $msg);
            
    $email = $mentorcal->user_email;    
    custom_mail($email,$subj,$msg,"Training Tool Email","");
}


function custom_mail($user_email,$setup_sub,$body,$email_type,$reason){        
    $email_template_body = email_template_body($body, $user_email, $email_type);
    @mail($user_email, $setup_sub, $email_template_body, custom_mail_header(), mail_additional_parameters());
    insert_email_historical_report(user_id(), $email_type, $setup_sub, $user_email, $reason, current_id());    
}

function custom_mail_header($fromcntmail = 'enfusen.com') {
        $additional_parameters = '-f notifications@enfusen.com';
        return "Reply-To: $fromcntmail\r\n"
                . "Return-Path: MCC <notifications@" . $fromcntmail . ">\r\n"
                . "From: Enfusen Notifications <notifications@" . $fromcntmail . ">\r\n"
                . "Return-Receipt-To: notifications@" . $fromcntmail . "\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-type: text/html\r\n"
                . "X-Priority: 3\r\n"
                . "X-Mailer: PHP" . phpversion() . "\r\n";                
    }
