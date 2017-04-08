<?php
$call_status = htmlspecialchars($_REQUEST['call_status']);
$callid = isset($_REQUEST['callid'])?intval($_REQUEST['callid']):0;
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

if($mentorcall->is_accepted == 0){
     wp_redirect(site_url());   
}

if($call_status == 'accepted'){
    
    ?>        
    <div class="contaninerinner">

        <h4>Mentor Invitation Accepted</h4>       

        <div class="panel-body">
            <div class="alert alert-success">
                <strong>Success!! </strong> You have successfully accepted mentor invitation.
            </div>
        </div>
    </div>
        

    <?php
    
}
else{
    wp_redirect(site_url());
}

?>
