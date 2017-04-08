<?php
global $wpdb;
$c_id = get_current_user_id();
$user = new WP_User($c_id);
$u_role =  $user->roles[0];

if($u_role == 'administrator'){
    
    $setup_id = isset($_GET['client'])?intval($_GET['client']):0;
    $setup = $wpdb->get_row
    (
            $wpdb->prepare
            (
                    "SELECT * FROM ". setup_table() . " WHERE id = %d",
                    $setup_id
            )
    );
    if(empty($setup)){
        wp_redirect(site_url());
    }
    
    $url = $setup->url;    
    $servername = DB_HOST;
    $db_name = $setup->db_name;
    $db_user = $setup->db_username;
    $db_password = base64_decode(base64_decode($setup->db_password));
    if($db_name == ''){
        print_r('User setup is not completed'); die;
    }
    $conn = new mysqli($servername, $db_user, $db_password, $db_name);
    if ($conn->connect_error) {
        print_r('Error MySqli',$conn->connect_error); die;
    }
    
    $tokens = "super_tokens";    
    $sql = "SHOW TABLES LIKE '".$tokens."'";
    $result = mysqli_query($conn, $sql);  
    $row = $result->fetch_array(MYSQLI_NUM);
    if(empty($row)){
        $sql = "CREATE TABLE ".$tokens." ( "
                . "id int(100) primary key auto_increment,"
                . "token varchar(500) not null,"
                . "created_dt TIMESTAMP NOT NULL"
                . " )";
        mysqli_query($conn, $sql); 
    }
    
    $token = md5($setup->id.time().$setup->url) ;        
    $sql = "INSERT INTO ".$tokens." (token) VALUES('$token')";
    mysqli_query($conn, $sql);    
    $conn->close();
    $param = isset($_REQUEST['param'])?trim(htmlspecialchars($_REQUEST['param'])):'';
    $url = PROTOCOL.$url;
    if(trim($setup->white_lbl) != ''){
        $url = $setup->white_lbl;
    }
        
    $wpadmin = '&wpadmin=0';
    if(isset($_REQUEST['wpadmin'])){
        $wpadmin = '&wpadmin=1';
    }
    if($param != ''){
        wp_redirect($url.'/mcc_login.php?token='.$token.'&param='.$param.$wpadmin);
    }
    else{
        wp_redirect($url.'/mcc_login.php?token='.$token.$wpadmin);
    }
    die;
}
else{
    wp_redirect(site_url());
}