<?php
include_once 'common.php';

$args = array(	
	'role'         => MENTOR_ROLE,	
	'fields'       => 'all'	
 ); 

$mentors = get_users( $args );

$users = get_users( array() );
?>
<div class="contaninerinner">


    <h4>Create New Course    
    </h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Courses List">
                <a href="admin.php?page=triningtool">All Courses</a> >>
            </li>
            <li title="Course">
                Create New Course 
            </li>
        </ul>
    </div>



    <div class="panel panel-primary">
        <div class="pull-right"><a href="admin.php?page=triningtool" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
        <div class="panel-heading">New Course</div>
        <div class="panel-body">
            <form action="#" method="post" id="add_course" name="add_course" class="form-horizontal">

                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">Name* :</label>
                    <div class="col-lg-8">
                        <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
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
                        <input type="submit" id="add_btn" value="Next" class="btn btn-primary"/>           
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>