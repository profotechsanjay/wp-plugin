<?php

include_once 'common.php';

//include_once '../custom_functions.php';
global $wpdb;
global $billing_enable;

$base_url = site_url();

if(($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['select_package'])){
    $package_type = $_POST['package_type'];
    $package_id = $_POST['package_id'];
    $_SESSION["packagetype"] = $package_type;
    $_SESSION["packageid"] = $package_id;
    unset($_SESSION["showpackages"]);
    //$redirecturl = site_url()."/location-settings/?parm=billing_payment";    
}

?>
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_coupon_active'])) {
    $_SESSION["old_packagetype"] = $_SESSION["packagetype"];
    $_SESSION["old_packageid"] = $_SESSION["packageid"];
    $_SESSION["packagetype"] = $_POST['discount_coupon'];
    $_SESSION["packageid"] = $_POST['discount_dcid'];
    //$_SESSION["packageprice"] = $_POST['discount_price'];
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_coupon_reset'])) {
    if(isset($_SESSION["old_packagetype"]) && isset($_SESSION["old_packageid"])){
        $_SESSION["packagetype"] = $_SESSION["old_packagetype"];
        $_SESSION["packageid"] = $_SESSION["old_packageid"];
        unset($_SESSION['old_packagetype']);
        unset($_SESSION['old_packageid']);
        unset($_SESSION['packageprice']);
    }else{
        unset($_SESSION['packagetype']);
        unset($_SESSION['packageid']);
        unset($_SESSION["showpackages"]);
        unset($_SESSION['packageprice']);
        unset($_SESSION["old_packagetype"]);
        unset($_SESSION["old_packageid"]);
        $_SESSION["showpackages"] = 'show_packages';
    }
    //pr($_SESSION); die;
}
?>
<?php
if(($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['distroy_session'])){
    unset($_SESSION["packagetype"]);
    unset($_SESSION["packageid"]);
    unset($_SESSION["packageprice"]);
    $_SESSION["showpackages"] = 'show_packages';
    //$redirecturl = site_url()."/location-settings/?parm=billing_payment";
    //header( "refresh:0;url=".$redirecturl );
}
?>

<?php
if(!isset($_SESSION["packagetype"]) && !isset($_SESSION["packageid"]) && !isset($_SESSION["showpackages"])){
    /********** If Discount Code Already Assign START ****************/

    $dc_assigned = $wpdb->get_row("SELECT * FROM `wp_billingdiscount` WHERE `bd_status` = 'active'");
    if(!empty($dc_assigned)){

        //pr($dc_assigned);
        $_SESSION['packagetype'] = 'discount_coupon';
        $_SESSION['packageid'] = $dc_assigned->bd_dcname;
        $_SESSION['packageprice'] = $dc_assigned->bd_price;

    }
    /********** If Discount Code Already Assign END ****************/
}
?>

<!-------------- Billing Form Code START ----------------->
<?php
$billing_query = $wpdb->get_row("SELECT * FROM `wp_pay_for_locations` ORDER BY `payment_id` DESC LIMIT 1");
$billing_querystatus = $billing_query->status;
$billing_count = $wpdb->num_rows;
//pr($billing_query);
?>
<?php if(($billing_enable == 1) && (($billing_querystatus != 'paid') || empty($billing_query))){?>

    <?php if(isset($_SESSION["packagetype"]) && isset($_SESSION["packageid"])){ ?>

        <div class="contaninerinner">
            <div class="panel panel-primary">        
                <div class="panel-heading">Location Info</div>
                
                <div class="panel-body">
                    <?php
                    require 'payment_for_locations.php';  //This section active if agency Not pay initial payment for Locations
                    ?>
                </div>
            </div>
        </div>
    <?php }else{

        
        
        require 'choose_package.php';  //This section active if agency Not pay initial payment for Locations          
    } ?>
    
<?php } ?>
<!-------------- Billing Form Code END ----------------->


