<?php
include_once 'common.php';
$lesson_id = isset($_REQUEST['lesson_id'])?intval($_REQUEST['lesson_id']):0;

$lesson = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . lessons()." WHERE id = %d", $lesson_id
        )
);
if(empty($lesson)){
    die('Invalid Lesson');
}
$module_id = $lesson->module_id;

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
if(empty($course)){
    die('Invalid Course');
}


$lesson_id = isset($_REQUEST['lesson_id'])?intval($_REQUEST['lesson_id']):0;


?>
<div class="contaninerinner">

    
    <h4>Edit Lesson    
    </h4>
    <div class="bread_crumb">
        <ul>
            <li>
                <a href="admin.php?page=triningtool">All Courses</a> >>
            </li>
            <li>
                <a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>"><?php echo $course->title; ?></a> >>
            </li>
            <li>
                <a href="admin.php?page=module_detail&module_id=<?php echo $module->id; ?>"><?php echo $module->title; ?></a> >>
            </li>
            <li>
                <?php echo $lesson->title; ?>
            </li>
        </ul>
    </div>
    

    
<div class="panel panel-primary">
    <div class="pull-right"><a href="admin.php?page=module_detail&module_id=<?php echo $module->id; ?>" class="btn btn-danger bkbtn"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
    <div class="panel-heading">Edit Lesson - <?php echo $lesson->title; ?></div>
    <div class="panel-body">
        
        
        <form action="#" method="post" id="addlesson" name="addlesson" class="form-horizontal">
                    
            <input type="hidden" id="course_id" name="course_id" value="<?php echo $course->id; ?>" />
            <input type="hidden" id="module_id" name="module_id" value="<?php echo $module_id; ?>" />
            <input type="hidden" id="lessid" name="lessid" value="<?php echo $lesson_id; ?>" />
            <div class="form-group">
                <label for="title" class="col-lg-2 control-label">Name* :</label>
                <div class="col-lg-8">
                    <input type="text" value="<?php echo $lesson->title; ?>" required class="form-control" id="title" name="title" placeholder="Title">
                </div>
            </div>
            <div class="form-group" style="display: none;">
                <label for="title" class="col-lg-2 control-label">Time (Hrs) * :</label>
                <div class="col-lg-8">
                    <input type="number" required class="form-control" id="hours" name="hours" placeholder="Time to complete Lesson (Hrs)">
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-lg-2 control-label">External Link :</label>
                <div class="col-lg-8">
                    <input type="text" value="<?php echo $lesson->external_link; ?>" class="form-control" id="link" name="link" placeholder="External Link">
                </div>
            </div>

            <div class="form-group">
                <label for="cat_name" class="col-lg-2 control-label">Description :</label>
                <div class="col-lg-8 wpeditor">
                    <?php
                    wp_editor(html_entity_decode($lesson->description), $id = 'description', $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                    ?>
                </div>
            </div>   
            <div class="form-group">
                    <label for="add_btn" class="col-lg-2 control-label"></label>
                <div class="col-lg-8">
                    <button type="button" onclick="submitlesson();" class="btn btn-primary" >Update</button> 
                </div>
            </div
    </form>


        
    </div>
</div>
    </div>