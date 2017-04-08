<?php

include_once 'common.php';
global $wpdb; global $current_user;
$base_url = site_url();
$get_account_info = wp_get_current_user();

$info = $wpdb->get_row
(
        $wpdb->prepare
        (
                "SELECT * FROM ". client_company_info(),""
        )
);

?>
<div class="contaninerinner">     
    <h4>Account Info</h4>
    <div class="panel panel-primary">
        
        <div class="panel-heading">Account Info</div>
        <div class="panel-body">
            <div class="well">
                <h5>Basic Info</h5>
                <div class="block_txt"> <?php echo $get_account_info->data->display_name; ?> </div>            
                <div> Setup Created <?php echo time_elapsed_string($get_account_info->data->user_registered); ?> </div>
            </div>
            
            <div class="well">
                <?php
                if (session_status() == PHP_SESSION_NONE) { session_start(); }
                
                if(isset($_SESSION['passmsg']) && $_SESSION['passmsg'] != ''){
                    $msg = json_decode($_SESSION['passmsg']);                    
                    if($msg->sts == 1){
                        ?>
                            <div class="alert alert-success"><?php echo $msg->msg; ?></div>
                        <?php
                    }
                    else{
                        ?>
                            <div class="alert alert-danger"><?php echo $msg->msg; ?></div>
                        <?php
                    }
                    $_SESSION['passmsg'] = '';
                    unset($_SESSION['passmsg']);
                }
                ?>
                <h5>Change Password</h5>
                <form name="pwdchangeform" id="pwdchangeform" class="form_common" method="post">                
                    <input type="hidden" name="__change_pwd" value="1" />
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-2">Old Password * </label><div class="col-lg-6"><input required type="password" id='oldpassword' name="oldpassword" value="" class="form-control"></div></div></div>                        
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-2">New Password * </label><div class="col-lg-6"><input required type="password" id="newpassword" name="newpassword" value="" class="form-control"></div></div></div>
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-2">Confirm Password * </label><div class="col-lg-6"><input required equalTo='#newpassword' type="password" id="confirmnewpassword" name="confirmnewpassword" value="" class="form-control"></div></div></div>
                    <div class="row margin-bottom-10"><div class="control-group"><label class="control-label col-lg-2"></label><div class="col-lg-6"><input type="submit" name="submit" value="Change Password" class="btn new_btn_class"></div></div></div>                    
                </form>
            </div>
            
        </div>
        
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Facebook Connect Code</div>
        <div class="panel-body">
            <form role="form" class="form_scripts" method="post">
                <div class="row margin-bottom-10">                 
                   <div class="col-lg-12">
                       <input type="hidden" name="codefor" value="fb_connect_code" />
                       <textarea rows="6" class="form-control" id="fb_connect_code" name="fb_connect_code" ><?php echo stripcslashes($info->fb_connect_code); ?> </textarea>                       
                   </div>
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>        
            </form>            
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Google Tag Manager</div>
        <div class="panel-body">
            <form role="form" class="form_scripts" method="post">
                <div class="row margin-bottom-10">                 
                   <div class="col-lg-12">
                       <input type="hidden" name="codefor" value="google_tag_manager" />
                       <textarea rows="6" class="form-control" id="google_tag_manager" name="google_tag_manager" ><?php echo stripcslashes($info->google_tag_manager); ?> </textarea>                       
                   </div>
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>        
            </form>            
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Client Success Manager</div>
        <div class="panel-body">
            <form role="form" class="form_scripts" method="post">
                <div class="row margin-bottom-10">                 
                   <div class="col-lg-12">
                       <input type="hidden" name="codefor" value="client_success_manager" />
                       <textarea rows="6" class="form-control" id="client_success_manager" name="client_success_manager" ><?php echo stripcslashes($info->client_success_manager); ?> </textarea>                       
                   </div>
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>        
            </form>            
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Intercom Chat</div>
        <div class="panel-body">
            <form role="form" class="form_scripts" method="post">
                <div class="row margin-bottom-10">                 
                   <div class="col-lg-12">
                       <input type="hidden" name="codefor" value="intercom_chat" />
                       <textarea rows="6" class="form-control" id="intercom_chat" name="intercom_chat" ><?php echo stripcslashes($info->intercom_chat); ?> </textarea>                       
                       <span class="small">For dynamic content you can mention it like this : <i>
                            name: "{{user_name}}",
                            email: "{{user_email}}",
                            created_at: {{timestamp}}
                           </i></span>
                   </div>
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>        
            </form>            
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Extra Footer Code</div>
        <div class="panel-body">
            <form role="form" class="form_scripts" method="post">                                            
                <div class="row margin-bottom-10">                 
                   <div class="col-lg-12">
                       <input type="hidden" name="codefor" value="extra_footer_code" />
                       <textarea rows="6" class="form-control" id="extra_footer_code" name="extra_footer_code" ><?php echo stripcslashes($info->extra_footer_code); ?> </textarea>                       
                       <span class="small"><i>
                           For multiple script, please press enter twice before add
                           </i></span>
                   </div>
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>        
            </form>            
        </div>
    </div>

</div>
