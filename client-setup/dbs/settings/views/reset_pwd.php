<?php
function check_code_and_email($email, $code){
    $user = get_user_by('email',$email);
    if(empty($user)){
        die('Invalid Link');
    }    
    $user_id = $user->data->ID;
    $cod = $user->data->user_activation_key;
    if($cod != $code){
        die('Invalid Link');
    }
    
}

$email = isset($_REQUEST['email'])?esc_attr(trim($_REQUEST['email'])):'';
$code = isset($_REQUEST['code'])?esc_attr(trim($_REQUEST['code'])):'';
check_code_and_email($email, $code);

?>
<style>label.error{color: red} </style>
<script type="text/javascript" src="<?php echo SET_COUNT_PLUGIN_URL; ?>/assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo SET_COUNT_PLUGIN_URL; ?>/assets/js/script.js"></script>
<div class="contaninerinner col-lg-8">         
    <div class="panel panel-primary">
        
        <div class="panel-heading">Reset Password</div>
        <div class="panel-body">
            <div class="">
                <form name="pwdresetform" id="pwdresetform" class="form_common" method="post">                
                    <input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(); ?>" />
                    <input type="hidden" name="__email" value="<?php echo $email; ?>" />
                    <input type="hidden" name="__code" value="<?php echo $code; ?>" />
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-3">New Password * </label><div class="col-lg-6"><input required type="password" id="newpassword" name="newpassword" value="" class="form-control"></div></div></div>
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-3">Confirm Password * </label><div class="col-lg-6"><input required equalTo='#newpassword' type="password" id="confirmnewpassword" name="confirmnewpassword" value="" class="form-control"></div></div></div>
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-3"></label><div class="col-lg-6"><input type="submit" value="Reset Password" class="btn"></div></div></div>                    
                </form>
            </div>
            
        </div>
        
    </div>    

</div>
