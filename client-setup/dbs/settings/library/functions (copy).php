
<div class="contaninerinner processing"></div>
<?php
global $wpdb;
$c_id = get_current_user_id();

if (isset($_GET['type']) && $_GET['type'] == 'location_add') {

    $rand = time();        
    $username = "site_location_".$rand;
    $email = $username."@test.com";
    $password = md5(time().$username);
    $mcc_userId = wp_create_user( $username, $password, $email );
    
    if($mcc_userId > 0){
        $is_created = $wpdb->query
        (
            $wpdb->prepare
            (
                    "INSERT INTO " . client_location() . " (MCCUserId, created_by, created_dt) "
                    . "VALUES (%d, %d, '%s')", $mcc_userId, $c_id, date("Y-m-d H:i:s")
            )
        );
        if ($is_created) {
            $id = $wpdb->insert_id;
            $url = site_url() . "/".ST_LOC_PAGE . "?parm=new_location&location_id=" . $id;
        } else {
            $url = site_url() . "/".ST_LOC_PAGE . "?parm=locations";
        }        
    }
    else{
        $url = site_url() . "/".ST_LOC_PAGE . "?parm=locations";
    }
    js_redirect($url);
    
}

function js_redirect($url) {
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    echo $string;
}
?>

