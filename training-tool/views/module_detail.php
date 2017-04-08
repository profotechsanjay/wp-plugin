<?php
include_once 'common.php';
global $wpdb;

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
$course = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . courses()." WHERE id = (SELECT course_id FROM " . modules()." WHERE id = %d)", $module_id
        )
);

$lessons = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . lessons()." WHERE module_id = %d ORDER BY ord ASC", $module_id
        )
);

?>
<div class="contaninerinner">           
    <h4>Manage Module - <?php echo $module->title; ?> 
<!--    <div class="pull-right">
        <a class="btn btn-primary" onclick="getexceise('module',<php echo $module_id; ?>);" href="javascript:;">Exercise For Module</a>
    </div>-->
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
        <div class="pull-right">    
            <a class="btn btn-default" href="admin.php?page=add_exercise&module_id=<?php echo $module_id; ?>">Add New Exercise</a>
            <a class="btn btn-warning movelesson" href="javascript:;" data-type="lessons" data-id="<?php echo $module_id; ?>">Move Lessons</a>
            <a class="btn btn-warning reorder" href="javascript:;" data-type="lessons" data-id="<?php echo $module_id; ?>">Re-Order Lessons</a>
            <a class="btn btn-success" href="admin.php?page=add_lesson&module_id=<?php echo $module_id; ?>">Create New Lesson</a>            
            <a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a>
        </div>
        <div class="panel-heading">List Of Lessons</div>
        <div class="panel-body">

            <table class="table table-bordered table-striped table-hover" id="data_lessons" >
                <thead>
                    <tr>
                        <th style="width: 4%;">SNo</th>
                        <th style="width: 14%;">Title</th>
                        <th style="width: 20%;">Description</th>
                        <th style="width: 14%;">Information</th>
                        <th style="width: 14%;">Date</th>										
                        <th style="width: 24%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($lessons as $lesson) {
                        $title = "<a href='admin.php?page=lesson_detail&lesson_id=$lesson->id'>$lesson->title</a>";
                        ?>
                            <tr class="rowmod" data-id="<?php echo $lesson->id; ?>">
                                <td><?php echo $lesson->ord; ?></td> 
                                <td class="title" data-txt="<?php echo $lesson->title; ?>" data-lnk="<?php echo $lesson->external_link; ?>" ><?php echo $title; ?></td>
                                <td class="text" >
                                    <div style="display: none; visibility: hidden" class="textdiv"><?php echo html_entity_decode($lesson->description); ?></div>
                                    <?php echo limit_text(html_entity_decode($lesson->description),10,false); ?>
                                </td>
                                                                
                                <td>
                                    <div class="infospan">                                       
                                        <div>Total Hours: <?php echo $lesson->total_hrs; ?></div>
                                        <div>Total Exercises: <?php echo $lesson->total_resources; ?></div>
                                        
                                    </div>
                                
                                </td>   
                                
                                <td><?php echo date("Y-m-d",  strtotime($lesson->created_dt)); ?></td>                            
                                <td class="actiontd">
                                    <a class="btn btn-primary" href="admin.php?page=edit_lesson&lesson_id=<?php echo $lesson->id; ?>" title="Edit Lesson">Edit</a>                                    
                                    
                                    <a data-id="<?php echo $lesson->id; ?>" class="btn btn-success" href="admin.php?page=lesson_detail&lesson_id=<?php echo $lesson->id; ?>" title="Manage Lesson">Manage Lesson</a>
                                    
                                    <a href="javascript:;" data-id="<?php echo $lesson->id; ?>" class="deleteless btn btn-danger" title="Delete Module">Delete</a>
                                </td>
                            </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

        </div>

    </div>
</div>


<div id="lesson_dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lesson</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="addlesson" name="addlesson" class="form-horizontal">
                    
                    <input type="hidden" id="course_id" name="course_id" value="<?php echo $course->id; ?>" />
                    <input type="hidden" id="module_id" name="module_id" value="<?php echo $module_id; ?>" />
                    <input type="hidden" id="lessid" name="lessid" value="0" />
                    <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Name* :</label>
                        <div class="col-lg-8">
                            <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
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
                            <input type="text" class="form-control" id="link" name="link" placeholder="External Link">
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
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick="submitlesson();" class="btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="movemodal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title reordertitl">Move Lessons </h4>
            </div>
            <div class="modal-body">                
                <form action="#" method="post" id="moverows" name="moverows" class="form-horizontal">
                    <div class="loadergif">
                        <img src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
                    </div>                
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary movesave" >Move Selected Lessons</button>                
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!--<div id="project_excercise" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Project Exercise For Course</h4>
            </div>
            <div class="modal-body">
                
                <ul class="nav nav-tabs tabcustom">
                    <li class="active"><a href="#formdiv">Add Exercise</a></li>
                    <li><a href="#listusersdiv">Project Submitted</a></li>                    
                </ul>
                
                <div class="tab-content">
                    <div id="formdiv" class="tab-pane fade in active">
                    <form action="#" method="post" id="addprojectexce" name="addprojectexce" class="form-horizontal">

                        <input type="hidden" id="course_id" name="course_id" value="<php echo $course->id; ?>" />
                        <input type="hidden" id="module_id" name="module_id" value="<php echo $module_id; ?>" />
                        <input type="hidden" id="exid" name="exid" value="0" />
                        <input type="hidden" id="type" name="type" value="module" />

                        <div class="form-group">
                            <label for="title" class="col-lg-2 control-label">Enable :</label>
                            <div class="col-lg-8">
                                <input type="checkbox" name="isenabled" id='isenabled' />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title" class="col-lg-2 control-label">Name* :</label>
                            <div class="col-lg-8">
                                <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
                            </div>
                        </div>                    
                        <div class="form-group">
                            <label for="title" class="col-lg-2 control-label">Time (hrs) :</label>
                            <div class="col-lg-8">
                                <input type="number" required class="form-control" id="hours" name="hours" placeholder="Hours to complete project">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cat_name" class="col-lg-2 control-label">Description :</label>
                            <div class="col-lg-8 wpeditor">
                                <php
                                wp_editor("", $id = 'description1', $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                                ?>
                            </div>
                        </div>                        
                    </form>
                    </div>
                    <div id="listusersdiv" class="tab-pane fade">
                        <div class="loadergif">
                            <img src="<php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
                        </div>
                        <table class="table table-bordered tbluserdv" style="display: none;">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Links</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="jQuery('#addprojectexce').submit();" class="btn btn-primary" >Save</button>                
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>-->