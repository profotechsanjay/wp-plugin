<?php

include_once 'common.php';
global $wpdb;

$base_url = site_url();
$info = $wpdb->get_row
(
        $wpdb->prepare
        (
                "SELECT * FROM ". client_company_info(),""
        )
);
if (session_status() == PHP_SESSION_NONE)
    session_start();

$company_info = json_decode($info->company_info);

?>
<style>
    .widcustom{
        width: 120px;
    }
</style>
<div class="contaninerinner">     
    <h4>Company Info</h4>
    <div class="panel panel-primary">
        <input type="hidden" id="baseurlcomp" name="baseurlcomp" value="<?php echo $info->original_url != ''?$info->original_url:$base_url; ?>" />
               <input type="hidden" id="slogin" name="slogin" value="<?php if(isset($_SESSION['slogin']) && $_SESSION['slogin'] == 1){ echo '1'; } else { echo '0'; }; ?>" />
               
        <div class="panel-heading">Company Logo</div>
        <div class="panel-body">
            <form role="form" name="logoform" id="logoform" method="post" enctype="multipart/form-data">
                <input type="hidden" name="__logo_upload" value="1" />
                <div class="form-group">
                    <div class="row">
                        <?php
                            if (session_status() == PHP_SESSION_NONE) { session_start(); }

                            if(isset($_SESSION['uploadmsg']) && $_SESSION['uploadmsg'] != ''){
                                $msg = json_decode($_SESSION['uploadmsg']);                    
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
                                $_SESSION['uploadmsg'] = '';
                                unset($_SESSION['uploadmsg']);
                            }
                            ?>
                        <div class="col-lg-5">
                            <label for="application_logo" class="control-label lblcontrol">Application Logo
                                 <span class="small"><i> (Appears in top left of the screen )</i></span>
                            </label>
                                                        
                            <input type="file" id="application_logo" name="application_logo" accept="image/*" />                            
                        </div>
                        <div class="col-lg-7">                            
                            <div class="logo_img">
                                <?php if($info->logo != ''){
                                    ?>
                                    <img src="<?php echo $info->logo; ?>" />
                                    <?php
                                } ?>
                            </div>                            
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-12">
                            <button type="submit" class="btn new_btn_class margin_top_10">Save</button>    
                        </div>
                        <hr/>
                        <div class="clearfix"></div>
                         <hr/>
                         <div class="clearfix"></div>
                        <div class="col-lg-12">
                            <label  class="control-label lblcontrol margin_top_10">Adjust Logo Positioning </label>
                        </div>
                        <div class="">
                            <?php 
                            $height = 0; $width = 178; $martop = 0;
                            if($wpdb->query("SHOW COLUMNS FROM ".client_company_info()." LIKE 'logo_css'") == 1){
                                
                                $css = $wpdb->get_var("SELECT logo_css FROM ".client_company_info());                                
                                if(!empty($css)){
                                    $css = json_decode($css);
                                    $height = $css->height;
                                    if($css->height == 'auto'){
                                        $height = 0;
                                    }
                                    $width = $css->width; $martop = $css->toplogo;                                    
                                }
                                
                            }

                            ?>
                            <div class="col-lg-3">
                                <div>Height : <input type="number" value="<?php echo $height; ?>" data-type='height' class="widcustom" name="heightlogo" id="heightlogo" /> px</div>
                                <span><i>Default Height: Auto (set 0 for auto height)</i></span>
                            </div>
                            <div class="col-lg-3">
                                <div>Width : <input type="number" value="<?php echo $width; ?>" data-type='width' class="widcustom"  name="widthlogo" id="widthlogo" /> px</div>
                                <span><i>Default Width: 178 pixel</i></span>
                            </div>
                            <div class="col-lg-3">
                                <div>Top : <input type="number" value="<?php echo $martop; ?>" data-type='margin-top' class="widcustom"  name="toplogo" id="toplogo" /> px</div>
                                <span><i>Default Top: 0 pixel</i></span>
                            </div>
                            
                        </div>
                         <div class="clearfix"></div>
                         <div class="col-lg-12">
                             <button type="submit" name="save_css" value="1" class="btn btn-success margin_top_10">Save Logo Position</button>    
                        </div>
                    </div>
                </div>                                    
                                
            </form>            

            
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">PDF Reports Logo</div>
        <div class="panel-body">
            <form role="form" name="reportlogo" id="reportlogo" method="post" enctype="multipart/form-data">
                <input type="hidden" name="__logo_report" value="1" />
                <div class="form-group">
                    <div class="row">
                        <?php
                            if (session_status() == PHP_SESSION_NONE) { session_start(); }

                            if(isset($_SESSION['reportmsg']) && $_SESSION['reportmsg'] != ''){
                                $msg = json_decode($_SESSION['reportmsg']);                    
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
                                $_SESSION['reportmsg'] = '';
                                unset($_SESSION['reportmsg']);
                            }
                            ?>
                        <div class="col-lg-5">
                            <label for="pdf_logo" class="control-label lblcontrol">PDF Report Logo
                                 <span class="small"><i> (If logo is not added, it will add website logo on pdf report)</i></span>
                            </label>
                                                        
                            <input type="file" id="pdf_logo" name="pdf_logo" accept="image/*" />                            
                        </div>
                        <div class="col-lg-7">                            
                            <div class="logo_img">
                                <?php
                                $padflogo = ABSPATH."wp-content/plugins/settings/uploads/pdf_logo.jpg";                                
                                if(file_exists($padflogo) == true){
                                    $padflogopath = site_url().'/wp-content/plugins/settings/uploads/pdf_logo.jpg?'.time();
                                    ?>
                                    <img src="<?php echo $padflogopath; ?>" />
                                    <?php
                                } ?>
                            </div>                            
                        </div>
                        
                    </div>
                </div>
                
                <button type="submit" class="btn new_btn_class">Save</button>        
                                
            </form>            

            
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Company Detail</div>
        <div class="panel-body">
            <form role="form" name="detailcompanyform" id="detailcompanyform" method="post">               
                <div class="row">
                                   
                    <div class="col-lg-6">
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">Company Name *</label></div>
                                                        <div class="col-lg-8"><input type="text" required class="form-control" value="<?php echo get_option('blogname'); ?>" id="company_name" name="company_name" /></div>
                        </div>
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">Street</label></div>
                            <div class="col-lg-8"><input type="text" class="form-control" value="<?php echo $company_info->street; ?>" id="street" name="street" /></div>
                        </div>
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">State</label></div>
                            <div class="col-lg-8"><input type="text" class="form-control" value="<?php echo $company_info->state; ?>" id="state" name="state" /></div>
                        </div>
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">Currency</label></div>
                            <div class="col-lg-8">
                             <select class="form-control" id="currency" name="currency" data-currencylist="USD,AUD,EUR">                                 
                                 <option <?php echo $company_info->currency == 'USD'?'selected="selected"':''; ?> value="USD">United States Dollar</option>
                                 <option <?php echo $company_info->currency == 'GBP'?'selected="selected"':''; ?> value="GBP">British Pound</option>
                                 <option <?php echo $company_info->currency == 'EUR'?'selected="selected"':''; ?> value="EUR">Euro</option>                                 
                             </select>                            
                            </div>
                        </div>                        
                    </div>
                    <div class="col-lg-6">
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">Email *</label></div>
                            <div class="col-lg-8"><input type="text" required email="true" value="<?php echo get_option('admin_email'); ?>" class="form-control" id="company_email" name="company_email" /></div>
                        </div>
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">City</label></div>
                            <div class="col-lg-8"><input type="text" class="form-control" value="<?php echo $company_info->city; ?>" id="city" name="city" /></div>
                        </div>
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">Country</label></div>
                            <div class="col-lg-8">
                                <select id="country" class="form-control" name="country">
                                    <option <?php echo $company_info->country == 'United States'?'selected="selected"':''; ?> value="United States">United States</option>
                                    <option <?php echo $company_info->country == 'United Kingdom'?'selected="selected"':''; ?> value="United Kingdom">United Kingdom</option>
                                    <option <?php echo $company_info->country == 'France'?'selected="selected"':''; ?> value="France">France</option>
                                    <option <?php echo $company_info->country == 'Germany'?'selected="selected"':''; ?> value="Germany">Germany</option>
                                    <option <?php echo $company_info->country == 'Netherlands'?'selected="selected"':''; ?> value="Netherlands">Netherlands</option>
                                </select>
                            </div>
                        </div>
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-4"><label class="control-label lblcontrol">Language</label></div>
                            <div class="col-lg-8">                                
                                <select id="language" class="form-control" name="language">
                                    <option selected="selected" value="en">English</option>
                                    <option <?php echo $company_info->language == 'de'?'selected="selected"':''; ?> value="de">Deutsch</option>
                                    <option <?php echo $company_info->language == 'fr'?'selected="selected"':''; ?> value="fr">Francais</option>
                                    <option <?php echo $company_info->language == 'it'?'selected="selected"':''; ?> value="it">Italiano</option>                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class='row margin-bottom-10'>
                            <div class="col-lg-2"><label class="control-label lblcontrol">Description</label></div>
                            <div class="col-lg-10"><textarea rows="6" class="form-control" name="description" id="description" ><?php echo stripcslashes(get_option('blogdescription')); ?></textarea></div>
                        </div>
                    </div>  
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>                                
            </form>
        </div>
    </div>
    <input type="hidden" name="locpage" id="locpage" value="<?php echo ST_LOC_PAGE; ?>" />
    <div class="panel panel-primary">
        <div class="panel-heading">White labeling</div>
        <div class="panel-body">
            <form role="form" name="whitelabeling" id="whitelabeling" method="post">               
               <div class="row margin-bottom-10">
                   <div class="col-lg-2"><label class="control-label lblcontrol">URL Rewrite</label></div>
                   <div class="col-lg-6"><input <?php if(!empty($info) && $info->is_white_label == 1) echo 'checked="checked"'; ?> type="checkbox" class="form-control" id="urlrewrite" name="urlrewrite" /></div>
                </div>                
                <div class="row  margin-bottom-10">
                   <div class="col-lg-2"><label class="control-label lblcontrol">White Label URL</label></div>
                   <div class="col-lg-6"><input url='true' type="text" class="form-control" value="<?php echo $info->white_label_url; ?>" id="urlwhitelable" name="urlwhitelable" />
                       <div class="small"><i>Note 1 : Once you use white label service, <b><?php echo site_url(); ?></b> always redirect to white label url. </i></div>
                       <div class="small"><i>Note 2 : You need to contact with administrator in order to change URL of schedule jobs on server. </i></div>
                   </div>
                </div>                
                <button type="submit" class="btn new_btn_class">Save</button>        
            </form>            
        </div>
    </div>
</div>
<script>
    jQuery('.widcustom').bind('keyup mouseup',function(){
        var val = jQuery(this).val(); 
        var attrb = jQuery(this).attr("data-type");
        if(attrb == 'height'){
            if(val == 0){
                val = 'auto';
            }            
        }
        if(val != 'auto'){
            val = val+"px";
        }
        jQuery(".logo-default").css(attrb,val);
    });
</script>
