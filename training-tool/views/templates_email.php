<?php

include_once 'common.php';
global $wpdb;


$templates = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . email_templates()." ORDER BY created_dt", ""
        )
);


$base_url = site_url();
$slug = PAGE_SLUG;
?>
<style>
.form-group {
    margin-bottom: 15px;
    display: inline-block;
    width: 100%;
}
</style>

<div class="contaninerinner">     
    <h4>Email Templates</h4>
    
    <?php foreach($templates as $template){
        
        ?>
        <div class="panel panel-primary">        
            <div class="pull-right">            
                <a class="btn btn-success template_update" data-id="<?php echo $template->id; ?>" href="javascript:;">Update Template</a>
            </div>
            <div class="panel-heading">Template - <?php echo ucwords(str_replace("_", " ", $template->template)); ?></div>
            <div class="panel-body">
              
                 <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">SUbject * :</label>
                    <div class="col-lg-8">
                        <input type="text" value="<?php echo $template->subject; ?>"  class="form-control" id="subject_<?php echo $template->id; ?>" name="subject_<?php echo $template->id; ?>" placeholder="Email Subject">
                    </div>
                </div>
                <div class="clear"></div>
                
                <div class="form-group">
                    <label class="col-lg-2 control-label">Content * :</label>
                    <div class="col-lg-8 wpeditor">
                        <?php
                        wp_editor(html_entity_decode($template->content), $id = 'content_'.$template->id, $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                        ?>
                    </div>
                </div>
                
            </div>
        </div>
        <?php
        
    } ?>    

</div>
