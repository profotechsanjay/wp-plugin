<?php
include_once 'common.php';
global $wpdb;

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
$modules = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . modules()." WHERE course_id = %d ORDER BY ord ASC", $course_id
        )
);


$mentorcals = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . mentorcall()." WHERE course_id = %d ORDER BY created_dt DESC", $course_id
        )
);

if(isset($_REQUEST['show'])){ 

$community_calls = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . community_call()." WHERE course_id = %d ORDER BY created_dt DESC", $course_id
        )
);							 
							 
?>

<div class="contaninerinner">       
    <h4>List of Calls - <?php echo $course->title; ?> 
<!--    <div class="pull-right">
        <a class="btn btn-primary" onclick="getexceise('course',<php echo $course_id; ?>);" href="javascript:;">Exercise For Course</a>
    </div>-->
    </h4>
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
        <div class="pull-right">            
            <a class="btn btn-success" href="admin.php?page=add_community_call&course_id=<?php echo $course->id; ?>&create=new">Create New Call</a>
            <a href="admin.php?page=triningtool" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a>
        </div>
        <div class="panel-heading">List Of Community Calls</div>
        <div class="panel-body">

            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 4%;">SNo</th>
                        <th style="width: 14%;">Title</th>
                        <!--th style="width: 20%;">Description</th-->
                                        
                        <th style="width: 14%;">Date</th>										
                        <th style="width: 24%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
	$f=1;
                    foreach ($community_calls as $call) {
                       
                        ?>
                            <tr class="rowmod">
                                <td><?php echo $f++; ?></td> 
                                <td class="title" ><?php echo $call->call_heading; ?></td>
                                                               
                                  <input type="hidden" value="<?php echo $call->id; ?>" class="delete-call"/>                               
                                <td><?php echo date("Y-m-d",  strtotime($call->created_dt)); ?></td>                            
                                <td class="actiontd">
                                    <a href="admin.php?page=add_community_call&course_id=<?php echo $course_id; ?>&callid=<?php echo $call->id; ?>" title="Edit Module" class="btn btn-primary">View / Edit</a>
                                    
                                   
                                    
                                    <button class=" btn btn-danger" title="Delete Call" onclick="DeleteCall(<?php echo $call->id; ?>);">Delete</button>
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


<div id="confirm_dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Module</h4>
            </div>
            <div class="modal-body">
                
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
                <button type="button" onclick="submitmodle();" class="btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>
	
	
<?php }else{ ?>
<div class="contaninerinner">       
    <h4>Manage Course - <?php echo $course->title; ?> 
<!--    <div class="pull-right">
        <a class="btn btn-primary" onclick="getexceise('course',<php echo $course_id; ?>);" href="javascript:;">Exercise For Course</a>
    </div>-->
    </h4>
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
        <div class="pull-right">            
            <a class="btn btn-success" href="admin.php?page=add_module&course_id=<?php echo $course_id; ?>">Create New Module</a>
            <a class="btn btn-warning reorder" href="javascript:;" data-type="modules" data-id="<?php echo $course_id; ?>">Re-Order Modules</a>
            <a href="admin.php?page=triningtool" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a>
        </div>
        <div class="panel-heading">List Of Modules</div>
        <div class="panel-body">

            <table class="table table-bordered table-striped table-hover" id="data_modules" >
                <thead>
                    <tr>
                        <th style="width: 4%;">SNo</th>
                        <th style="width: 14%;">Title</th>
                        <th style="width: 20%;">Description</th>
                        <th style="width: 15%;">Information</th>                        
                        <th style="width: 14%;">Date</th>										
                        <th style="width: 24%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($modules as $module) {
                        $title = "<a href='admin.php?page=module_detail&module_id=$module->id'>$module->title</a>";
                        ?>
                            <tr class="rowmod" data-id="<?php echo $module->id; ?>">
                                <td><?php echo $module->ord; ?></td> 
                                <td class="title" data-txt="<?php echo $module->title; ?>" data-lnk="<?php echo $module->external_link; ?>" ><?php echo $title; ?></td>
                                <td class="text" >
                                    <div style="display: none; visibility: hidden" class="textdiv"><?php echo html_entity_decode($module->description); ?></div>
                                    <?php echo limit_text(html_entity_decode($module->description),10,false); ?>
                                </td>                                
                                <td>
                                    <div class="infospan">                                       
                                        <div>Total Hours: <?php echo $module->total_hrs; ?></div>
                                        <div>Total Exercises: <?php echo $module->total_resources; ?></div>
                                        
                                    </div>
                                
                                </td>                                  
                                <td><?php echo date("Y-m-d",  strtotime($module->created_dt)); ?></td>                            
                                <td class="actiontd">
                                    <a href="admin.php?page=edit_module&module_id=<?php echo $module->id; ?>" title="Edit Module" class="btn btn-primary">Edit</a>
                                    
                                    <a data-id="<?php echo $module->id; ?>" href="admin.php?page=module_detail&module_id=<?php echo $module->id; ?>" class="btn btn-success" title="Manage Module">Manage Module</a>
                                    
                                    <a href="javascript:;" data-id="<?php echo $module->id; ?>" class="deletemod btn btn-danger" title="Delete Module">Delete</a>
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


<div id="confirm_dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Module</h4>
            </div>
            <div class="modal-body">
                
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
                <button type="button" onclick="submitmodle();" class="btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?php }
?>



