<?php
include_once 'common.php';

$course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;

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

?>
<div class="contaninerinner">

    
    <h4>Create New Module    
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
                Create New Module 
            </li>
        </ul>
    </div>
    

    
<div class="panel panel-primary">
    <div class="pull-right"><a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>" class="btn btn-danger bkbtn"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
    <div class="panel-heading">New Module</div>
    <div class="panel-body">
        <form action="#" method="post" id="addmodules" name="addmodules" class="form-horizontal">
                    
                <input type="hidden" id="course_id" name="course_id" value="<?php echo $course_id; ?>" />
                <input type="hidden" id="id" name="id" value="0" />
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">Name* :</label>
                    <div class="col-lg-8">
                        <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">External Link :</label>
                    <div class="col-lg-8">
                        <input type="text" url='true' class="form-control" id="link" name="link" placeholder="External Link">
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
                        <button type="button" onclick="submitmodle();" class="btn btn-primary" >Submit</button> 
                    </div>
                </div>
            </form>
    </div>
</div>
    </div>