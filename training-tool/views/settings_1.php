<?php

include_once 'common.php';
global $wpdb;

$settings = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . setting()." WHERE keyname NOT IN('valid_licence')", ""
        )
);
?>
<div class="contaninerinner">       
    <ul class="nav nav-tabs tabcustom">
        <li class="active"><a href="javascript:;">Settings</a></li>
        <li><a href="admin.php?page=course_images">Image By Course</a></li>                    
    </ul>
    <div class="panel tab-content">
        <div class="panel-body">
            <form action="#" method="post" id="settingform" name="settingform" class="form-horizontal">
                <?php
                foreach ($settings as $setting) {
                    ?>
                    <input type="hidden" name="ids[]" value="<?php echo $setting->id; ?>" />                    
                    <div class="form-group">
                        <div class="col-lg-1">
                            <label> <input class="form-control lblchksettings" <?php echo $setting->is_show == 1?'checked="checked"':''; ?> type="checkbox" id="show_<?php echo $setting->id; ?>" name="show_<?php echo $setting->id; ?>" /> Show </label>
                        </div>
                        <div class="col-lg-3">                            
                            <input class="form-control" placeholder="Enter Value" type="text" id="key_<?php echo $setting->id; ?>" name="key_<?php echo $setting->id; ?>" value="<?php echo $setting->keyname; ?>" />
                        </div>
                        <div class="col-lg-6">
                            <input class="form-control" placeholder="Enter Value" type="text" id="val_<?php echo $setting->id; ?>" name="val_<?php echo $setting->id; ?>" value="<?php echo $setting->keyvalue; ?>" />
                        </div>
                    </div>                    
                    <?php
                }
                ?>                
                <div class="form-group rowdiv">                    
                    <div class="col-lg-12">
                        <a href="javascript:;" class="settingbtn btn btn-success">Save Settings</a>
                    </div>
                </div>   
            </form>
        </div>


    </div>
</div>


