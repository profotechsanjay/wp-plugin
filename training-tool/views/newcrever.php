<?php
/*
$uid = user_id();
if($uid > 0){
    $crever = get_user_meta($uid,'crever',TRUE);
    if($crever != CREVER){
        echo '<style> .newcrever{    position: relative;
            margin: 0 !important;
            padding: 0;
            background: rgb(222, 216, 120) !important;
            border: 0 !important;
            color: #61551f !important; text-align: center;  font-size: 15px; font-weight: 600;} .crossbtn{float: right; color: #6b5f25;  left: -10px;  position: relative; top: -3px;
    font-weight: 900;
    font-size: 20px; } </style>';

        echo "<div class='newcrever alert alert-warning'>"
                ."New Version Of CRE Has Released <a onclick='creversionset()' href='".site_url()."/content-recommendation-dashboard/'> Click here to go</a> <a onclick='creversionset()' href='javascript:;'><span class='crossbtn'>&Cross;</span></a></div>";


    ?>

    <script>

    function creversionset(){
       var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>"; 
        jQuery('.newcrever').remove();
        jQuery.post(ajax_url, "&param=remcremsg&action=training_lib",function(msg){});
    }

    </script>
    <?php    
    }
}*/
?>
