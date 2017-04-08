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


$lessons = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT id,title FROM " . lessons()." WHERE module_id = %d ORDER BY ord ASC", $module_id
        )
);

?>
<div class="contaninerinner">

    
    <h4>Create New Exercise
    </h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Courses">
                <a href="admin.php?page=triningtool">All Courses</a> >>
            </li>
            <li title="Course">
                <a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>"><?php echo $course->title; ?></a> >>
            </li>
            <li title="Module">
                <a href="admin.php?page=module_detail&module_id=<?php echo $module->id; ?>"><?php echo $module->title; ?></a> >>
            </li>
            <li title="Add New Exercise">
                Add New Exercise
            </li>
        </ul>
    </div>
    

    
<div class="panel panel-primary">
    <div class="pull-right"><a href="admin.php?page=module_detail&module_id=<?php echo $module->id; ?>" class="btn btn-danger bkbtn"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
    <div class="panel-heading">New Exercise</div>
    <div class="panel-body">
        
        
        <form action="#" method="post" id="addresource" name="addresource" class="form-horizontal">
                    
            <input type="hidden" id="typerescreated" name="typerescreated" value="page" />
            <input type="hidden" id="course_id" name="course_id" value="<?php echo $course->id; ?>" />
            <input type="hidden" id="module_id" name="module_id" value="<?php echo $module_id; ?>" />            
            
            <div class="form-group">
                <label for="title" class="col-lg-2 control-label">Lesson * :</label>
                <div class="col-lg-8">
                    <select class="form-control" required title="Please select a lesson" id="lesson_id" name="lesson_id">
                        <option value="">Select Lesson</option>
                        <?php
                        foreach ($lessons as $lesson) {
                            ?>
                            <option value="<?php echo $lesson->id; ?>"><?php echo $lesson->title; ?></option>
                            <?php
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-lg-2 control-label">Name* :</label>
                <div class="col-lg-8">
                    <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
                </div>
            </div>
            <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Time (Hrs) * :</label>
                        <div class="col-lg-8">
                            <input type="number" required class="form-control" id="hours" name="hours" placeholder="Time to complete Exercise (Hrs)">
                        </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-lg-2 control-label">Button Type :</label>
                <div class="col-lg-8">
                    <select class="form-control" name="button_type" id="button_type">
                        <option value="mark">Mark Complete</option>
                        <option value="submit">Submit Project</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="cat_name" class="col-lg-2 control-label">Description :</label>
                <div class="col-lg-8 wpeditor">
                    <?php
                    wp_editor("", $id = 'description', $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                    ?>
                </div>
            </div>   
            <div class="form-group">
                    <label for="add_btn" class="col-lg-2 control-label"></label>
                <div class="col-lg-8">
                    <button type="button" onclick="submitres();" class="btn btn-primary" >Submit</button> 
                </div>
            </div>
    </form>


        
    </div>
</div>
    </div>