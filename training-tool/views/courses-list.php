<?php

include_once 'common.php';
global $wpdb;


$courses = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT c.*, (SELECT count(id) as total FROM " . enrollment()." WHERE course_id = c.id) as enrolledby FROM " . courses()." c ORDER BY c.ord", ""
        )
);


$base_url = site_url();
$slug = PAGE_SLUG;
?>
<div class="contaninerinner">     
    <h4>Courses</h4>
    <div class="panel panel-primary">
        <div class="pull-right">
            <a class="btn btn-warning reorder" href="javascript:;" data-type="courses" data-id="">Re-Order Courses</a>
            <a class="btn btn-success" href="admin.php?page=new_course">Create New Course</a>
        </div>
        <div class="panel-heading">Courses</div>
        <div class="panel-body">
            <div class="alert alert-info">
                <strong>Frontend URL: <a target='_blank' href="<?php echo $base_url.'/'.$slug; ?>"><?php echo $base_url.'/'.$slug; ?></a></strong>
            </div>
            <table class="table table-bordered table-striped table-hover" id="data_courses" >
                <thead>
                    <tr>
                        <th style="width: 4%;">SNo</th>
                        <th style="width: 15%;">Title</th>
                        <th style="width: 20%;">Description</th>
                        <th style="width: 16%;">Information</th>                                               
                        <th style="width: 15%;">Date</th>										
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($courses as $course) {
                                                
                        $title = "<a href='admin.php?page=course_detail&course_id=$course->id'>$course->title</a>";
                        $viewlink = "<a class='btn btn-primary' target='_blank' href='$base_url/$slug?course=$course->id'>View Course</a>";
                        $listenrolledby = $course->enrolledby;
                        if($listenrolledby > 0)
                            $listenrolledby = "$course->enrolledby <a href='javascript:;' data-title='$course->title' data-attr='$course->id' class='enrollbylist' > View List</a>"
                                
                        ?>
                            <tr class="rowmod" data-id="<?php echo $course->id; ?>">
                                <td><?php echo $course->ord; ?></td> 
                                <td><?php echo $title; ?>                                    
                                </td>
                                <td><?php echo limit_text(html_entity_decode($course->description),10,false); ?></td>
                                <td>
                                    <div class="infospan">
                                        <div>Enrolled By: <?php echo $listenrolledby; ?></div>
                                        <div>Total Hours: <?php echo $course->total_hrs; ?></div>
                                        <div>Total Exercises: <?php echo $course->total_resources; ?></div>                                        
                                    </div>                                
                                </td>                                                                                                
                                
                                <td><?php echo date("Y-m-d",  strtotime($course->created_dt)); ?></td>                            
                                <td class="actiontd acttd">
                                    <div>
                                    <a data-id="<?php echo $course->id; ?>" href="admin.php?page=edit_course&course_id=<?php echo $course->id; ?>" class="btn btn-primary" title="Edit Course">Edit</a>                                    
                                    <a data-id="<?php echo $course->id; ?>" href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>" class="btn btn-success" title="Manage Course">Manage Course</a>                                    
										<a data-id="<?php echo $course->id; ?>" href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>&show=calls" class="btn btn-success showbtnstyle" style="margin-top:5px;" title="Add Community Call">View Community Calls</a>                                    
                                    </div>
                                    <div>
                                        <?php echo $viewlink; ?>
                                    <a href="javascript:;" data-id="<?php echo $course->id; ?>" title="Delete Course" class="deletecou btn btn-danger">Delete</a>                                    
                                    </div>
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


<div id="enrolled_dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" id="ernrolledlist">                
                <div class="loadergif">
                    <img src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
                </div>
            </div>            
        </div>
    </div>
</div>


<div id="mentors_dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Mentors</h4>
            </div>
            <div class="modal-body" id="mentorsid">                
                <div class="loadergif">
                    <img src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
                </div>
            </div>            
        </div>
    </div>
</div>

<div style="display: none; visibility: hidden" class="gifhidden">
    <div class="loadergif">
        <img src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
    </div>
</div>