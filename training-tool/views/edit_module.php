<?php
include_once 'common.php';

$module_id = isset($_REQUEST['module_id'])?intval($_REQUEST['module_id']):0;

$module = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . modules()." WHERE id = %d", $module_id
        )
);
if(empty($module)){
    die('Invalid Module');
}

$course_id = $module->course_id;

$course = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . courses()." WHERE id = %d", $course_id
        )
);


?>
<div class="contaninerinner">

    
    <h4>Edit Module    
    </h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Courses List">
                <a href="admin.php?page=triningtool">All Courses</a> >>
            </li>
            <li title="Course">
                <a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>"><?php echo $course->title; ?></a> >>
            </li>
            <li title="Module">
                <?php echo $module->title; ?>
            </li>
        </ul>
    </div>
    

    
<div class="panel panel-primary">
    <div class="pull-right"><a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>" class="btn btn-danger bkbtn"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
    <div class="panel-heading">Edit Module - <?php echo $module->title; ?></div>
    <div class="panel-body">
        <form action="#" method="post" id="addmodules" name="addmodules" class="form-horizontal">
                    
                <input type="hidden" id="course_id" name="course_id" value="<?php echo $course_id; ?>" />
                <input type="hidden" id="id" name="id" value="<?php echo $module_id; ?>" />
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">Name* :</label>
                    <div class="col-lg-8">
                        <input type="text" required value="<?php echo $module->title; ?>" class="form-control" id="title" name="title" placeholder="Title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">External Link :</label>
                    <div class="col-lg-8">
                        <input type="text" url='true' value="<?php echo $module->external_link; ?>" class="form-control" id="link" name="link" placeholder="External Link">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cat_name" class="col-lg-2 control-label">Description :</label>
                    <div class="col-lg-8 wpeditor">
                        <?php
                        wp_editor(html_entity_decode($module->description), $id = 'description', $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                <label for="add_btn" class="col-lg-2 control-label"></label>
                <div class="col-lg-8">
                    <button type="button" onclick="submitmodle();" class="btn btn-primary" >Update</button> 
                </div>
            </div>
            </form>
    </div>
</div>
    </div>