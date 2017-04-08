<?php
include_once 'common.php';
$setup_id = isset($_REQUEST['setup_id'])?intval($_REQUEST['setup_id']):0;
if($setup_id > 0){
    include_once 'new_step2.php';
}
else{
    include_once 'new_step1.php';
}
?>

