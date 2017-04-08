
<?php
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$showLostPwd = 0;
/* Making User Logout */
if (isset($_GET['state']) && $_GET['state'] == "lg") {
    unset($_SESSION['customuser']);
    unset($_SESSION['general']);
    unset($_SESSION['keywords']);
    header("location:" . site_url() . "/custom-agency-login");
}

if (isset($_GET['em']) && isset($_GET['action'])) {
    $action = base64_decode(base64_decode($_GET['action']));
    if ($action == "resetpwd") {
        $showLostPwd = 1;
    }
}

if ($_SESSION['customuser']) {
    header("location:" . site_url() . "/agency-home/");
}

if (!empty($_GET['verified']) && $_GET['verified'] == base64_encode(base64_encode("true"))) {
    //$userEmailStatus = get_user_meta(base64_decode(base64_decode($_GET['uid'])), "user_email_status", true);
    if (!get_option("user_email_status")) {
        add_option("user_email_status", 1);
    }
    /* if (empty($userEmailStatus)) {
      add_user_meta(base64_decode(base64_decode($_GET['uid'])), "user_email_status", 1);
      }else{} */
}
?>

<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/css/lg-style.css"/>

<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">

<style>



    .lg-img-div{width: 100%;
                height: 80px;
                display: inline-block;

                overflow: hidden;
                /*background-color: rgba(0,0,0,0.10);*/
                vertical-align: top;
                margin-bottom: 15px;padding:5px;}


    .lg-img-div .item {
        height: 80px; position:relative;
    }


    .lg-img-div .item  img {
        bottom: 0;
        height: auto;
        left: 0;
        margin: auto;
        max-width: inherit;
        position: absolute;
        right: 0;
        top: 0;
        width: auto;
    }


    .slideImg1 {
        max-height: 100% !important;
        max-width: 100% !important;
    }
    .lg-login-bg{     background: #f2f2f2 !important; }
    .lg-login-bg .panel{background-color:transparent !important;}

    #lostpassword #lostpwd label.error{color:red;}

</style>
<?php
$imagePath = site_url() . "/wp-content/plugins/settings/uploads/logo.png";
if (@GetImageSize($imagePath)) {
    $image = 1;
} else {
    $image = 0;
}
?>
<div class="container signup lg-login-bg">
    <div class="panel panel-default">

        <?php if ($showLostPwd) { ?>
            <form action="#" method="post" id="lostpwdPanel" name="lostpwdPanel" class="form-horizontal app-form">
           <!--img src="<?php echo site_url() ?>/wp-content/themes/twentytwelve/images/logo.png" id="enf-logo"-->
                <?php
                if ($image) {
                    ?>
                    <div class="lg-img-div"><div class="item"><img class="slideImg1" src="<?php echo site_url() ?>/wp-content/plugins/settings/uploads/logo.png"/></div></div>
                    <?php
                }
                ?>
                <div class="form-group section-div">
                    <div class="col-md-12 pad_left_0 pad_right_0 input-signup-text">
                        <input type="password"  required class="form-control" id="cpwd" name="cpwd" placeholder="Password">
                    </div>
                </div>                

                <div class="form-group section-div">
                    <div class="col-md-12 pad_left_0 pad_right_0 input-signup-text">
                        <input type="password"  required class="form-control" id="confpwd" name="confpwd" placeholder="Confirm Password">
                    </div>
                </div> 
                <div class="form-group">
                    <div class="col-md-12 pad_left_0 pad_right_0  ">
                        <button type="submit" value="Submit" class="btn btn-success hundredPercent upper-text pad_top_10 pad_bottom_10 mont-font">Submit Password</button>

                    </div>
                </div>
                <div class="form-group section-div">
                    <div class="col-md-12 pad_left_0 pad_right_0 input-signup-text">
                        <a href="<?php echo site_url() . '/custom-agency-login' ?>" class="btn btn-link">&larr; Back to login</a>
                    </div>
                </div> 
            </form>
        <?php } else { ?>


            <form action="#" method="post" id="agencylogin" name="agencylogin" class="form-horizontal app-form">
                <!--h2>Agency Login</h2-->
            <!--img src="<?php echo site_url() ?>/wp-content/themes/twentytwelve/images/logo.png" id="enf-logo"-->
                <?php
                if ($image) {
                    ?>
                    <div class="lg-img-div"><div class="item"><img class="slideImg1" src="<?php echo site_url() ?>/wp-content/plugins/settings/uploads/logo.png"/></div></div>
                    <?php
                }
                ?>
                <!--div class="form-group">
                    <label for="name" class="col-lg-2 control-label">Name* :</label>
                    <div class="col-lg-8">
                        <input type="text" required class="form-control" id="name" name="name" placeholder="Client Name">
                    </div>
                </div-->
                <div class="form-group section-div">
                    <!--<label for="name" class="col-lg-2 control-label">Email* :</label>-->
                    <div class="col-md-12 pad_left_0 pad_right_0 input-signup-text">
                        <input type="text" required class="form-control" id="Cusername" name="Cusername" placeholder="Username">
                    </div>
                </div>                

                <div class="form-group section-div">
                    <!--<label for="name" class="col-lg-2 control-label">Email* :</label>-->
                    <div class="col-md-12 pad_left_0 pad_right_0 input-signup-text">
                        <input type="password"  required class="form-control" id="Cpassword" name="Cpassword" placeholder="Password">
                    </div>
                </div> 

                <div class="form-group section-div">
                    <div class="col-md-12 pad_left_0 pad_right_0 input-signup-text">
                        <a href="javascript:;" class="btn btn-link" data-toggle="modal" data-target="#lostpassword">Lost Password ?</a>
                    </div>
                </div> 

                <div class="form-group">
                    <!-- <label for="add_btn" class="col-lg-2 control-label"></label>-->
                    <div class="col-md-12 pad_left_0 pad_right_0  ">


                        <button type="submit" value="Submit" class="btn btn-success hundredPercent upper-text pad_top_10 pad_bottom_10 mont-font">Login</button>

                    </div>
                </div>

            </form>

        <?php } ?>
    </div>
</div>

<!-- Lost Password Modal -->
<div id="lostpassword" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-body">
                <form id="lostpwd">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="form-group">

                        <img src="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/images/lost-key.png" alt="lost-key"/>

                        <input type="text" name="lostemail" class="form-control" id="lostemail" required placeholder="Email Address" />
                    </div>
                    <button type="submit" class="btn btn-success hundredPercent upper-text pad_top_10 pad_bottom_10 mont-font">Get New Password</button>
                </form>
            </div>
        </div>

    </div>
</div>


