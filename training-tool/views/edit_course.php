<?php
include_once 'common.php';
$course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;
$course = $wpdb->get_row
(
        $wpdb->prepare
        (
                "SELECT * FROM ". courses() . " WHERE id = %d",
                $course_id
        )
);
if(empty($course)){
    die('Invalid Course');
}

$users = get_users( array() );

$args = array(	
	'role'         => MENTOR_ROLE,	
	'fields'       => 'all'	
 ); 

$mentors = get_users( $args );

?>
<div class="contaninerinner">


    <h4>Edit Course</h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Courses List">
                <a href="admin.php?page=triningtool">All Courses</a> >>
            </li>
            <li title="Course">
                <?php echo $course->title; ?>
            </li>
        </ul>
    </div>


    <div class="panel panel-primary">
        <div class="pull-right"><a href="admin.php?page=triningtool" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
        <div class="panel-heading">Edit Course - <?php echo $course->title; ?></div>
        <div class="panel-body">
            <form action="#" method="post" id="add_course" name="add_course" class="form-horizontal">
                <input type="hidden" id="course_id" name="course_id" value="<?php echo $course_id; ?>" />

                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">Name* :</label>
                    <div class="col-lg-8">
                        <input type="text" value="<?php echo $course->title; ?>" required class="form-control" id="title" name="title" placeholder="Title">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cat_name" class="col-lg-2 control-label">Description :</label>
                    <div class="col-lg-8 wpeditor">
                        <?php
                        wp_editor(html_entity_decode($course->description), $id = 'description', $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                        ?>
                    </div>
                </div>
                                
                
                <div class="form-group">
                    <label for="add_btn" class="col-lg-2 control-label"></label>
                    <div class="col-lg-8">
                        <input type="submit" id="add_btn" value="Update" class="btn btn-primary"/>           
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>