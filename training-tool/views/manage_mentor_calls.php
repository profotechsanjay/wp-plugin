<?php

include_once 'common.php';

global $current_user;
global $wpdb;
$current_user = wp_get_current_user();

$c_id = get_current_user_id();
$userrole = new WP_User($c_id);
$u_role =  $userrole->roles[0];

$user_id = $current_user->data->ID;

$usertabl = $wpdb->prefix."users";

$mentor_id = isset($_GET['mentor'])?intval($_GET['mentor']):0;
$student = isset($_GET['user'])?intval($_GET['user']):0;
$course_id = isset($_GET['course'])?intval($_GET['course']):0;

$mentorcal = new stdClass();

if(isset($_GET['call_id']) && $_GET['call_id'] > 0){
    
    $call_id = isset($_GET['call_id'])?intval($_GET['call_id']):0;
    $mentorcal = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . mentorcall()." WHERE id = %d",$call_id
        )
    );
    if(!empty($mentorcal)){
        $mentor_id = $mentorcal->mentor_id;
        $course_id = $mentorcal->course_id;
        $student = $mentorcal->user_id;
    }
    
}


$users = get_users(array(
    'meta_key'     => ACCESS_ROLE,
    'fields'     => "all",
));

if($u_role == "administrator"  || isagencylocation()){
    $courses = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT id,title FROM " . courses()." ORDER BY ord", ""
            )
    );
}
else{
    $courses = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT id,title FROM " . courses()." WHERE FIND_IN_SET($c_id,mentor_ids) ORDER BY ord", ""
            )
    );
}
    
if($course_id == 0){
    if(!empty($courses)){
        $course_id = $courses[0]->id;
    }
}
$mentorids = $wpdb->get_var
        (
        $wpdb->prepare
                (
                "SELECT mentor_ids FROM " . courses()." WHERE id = %d", $course_id
        )
);

$mentorids = trim($mentorids);
$pos = strpos($mentorids, ",");
if($pos == 0){
    $mentorids = ltrim($mentorids, ',');
}

$mentors = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . $usertabl." WHERE ID IN($mentorids) ORDER BY FIELD(ID, $mentorids) DESC",""
        )
);

$txtsql = "";
if($u_role != "administrator"){
    $txtsql = "WHERE m.mentor_id = $c_id";
}

$mentorcals = $wpdb->get_results
    (
    $wpdb->prepare
            (
            "SELECT m.*, u.display_name,me.display_name as mname,c.title FROM " . mentorcall()." m LEFT JOIN " . $usertabl ." u ON m.user_id = u.ID "
            . "LEFT JOIN " . courses()." c ON  m.course_id = c.id "
            . "LEFT JOIN " . $usertabl." me ON m.mentor_id = me.ID $txtsql ORDER BY m.created_dt DESC",""
    )
);

if($mentor_id > 0){
    if($u_role == 'administrator' || isagencylocation()){
        $c_id = $mentor_id;
    }
    
}


    
        ?>

        <div class="main-section singlepagecourse detailprogress mentorcallpage">
            
            <div class="col-sm-12">
                <h4>Mentor Calls</h4>
                <div class="bread_crumb">
                    <ul>
                        <li title="Course Admin">
                            <a href="admin.php?page=course_admin&course=<?php echo $course_id; ?>">Course Admin</a> >>
                        </li>
                        <li title="Manage Mentor Calls ">
                            Manage Mentor Calls 
                        </li>
                    </ul>
                </div>
                <div class="panel panel-primary">       
                    <div class="panel-heading">Mentor Calls</div>
                    <div class="panel-body">

                        <div class="mentorcallform col-lg-8">
                            <form action="#" method="post" id="addcall" name="addcall" class="form-horizontal">                                
                                <input type="hidden" id="callid" name="callid" value="<?php echo isset($mentorcal->id)?$mentorcal->id:0; ?>" />
                                <div class="form-group">
                                    <label class="col-lg-4">Select Course</label>
                                    <div class="col-lg-8">      
                                    <select required title="Please Select a Course" class="form-control" id="courseid" name="courseid">                                        
                                        <?php
                                        $selcoursename = '';
                                        foreach($courses as $course){
                                            $sel = ''; 
                                            if($course_id == $course->id){
                                                $sel = 'selected="selected"';
                                                $selcoursename = $course->title;
                                            }

                                            ?>
                                            <option <?php echo $sel; ?> value="<?php echo $course->id; ?>"><?php echo $course->title; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                     </div>
                                </div>
                                <?php if($u_role != "administrator"){
                                    ?>
                                    <input type="hidden" id="mentorselect" name="mentorselect" value="<?php echo $c_id; ?>" />
                                    <?php
                                    
                                } else { ?>
                                <div class="form-group">
                                    <label class="col-lg-4">Select Mentor</label>
                                    <div class="col-lg-8">                                        
                                        <select required title="Please Select a Mentor" class="form-control" id="mentorselect" name="mentorselect">
                                        <option value="">Select Mentor</option>
                                        <?php
                                        foreach($mentors as $mentor){
                                            $sel = ''; 
                                            if($mentor_id == $mentor->ID){
                                                $sel = 'selected="selected"';                                               
                                            }

                                            ?>
                                            <option <?php echo $sel; ?> value="<?php echo $mentor->ID; ?>"><?php echo $mentor->display_name; ?></option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label class="col-lg-4">Select User</label>
                                    <div class="col-lg-8">                                        
                                        <select required title="Please Select a User" class="students form-control" id="student_user" name="student_user">
                                            <option value="">Select User</option>
                                             <?php                                             
                                             if($u_role != "administrator"  || $mentor_id > 0){
                                                      
                                                $usertbl = $wpdb->prefix."users";
                                                $students = $wpdb->get_results
                                                (
                                                    $wpdb->prepare
                                                            (
                                                            "SELECT s.ID,s.display_name "
                                                            . "FROM " . mentor_assign()." map INNER JOIN " . $usertbl ." s ON map.user_id = s.ID "
                                                            . "WHERE map.mentor_id = %d AND map.course_id = %d ORDER BY s.user_registered DESC",$c_id, $course_id
                                                    )
                                                );
                                                
                                                foreach($students as $user){
                                                                                                        
                                                    $sel = ''; 
                                                    if($student == $user->ID){
                                                        $sel = 'selected="selected"';                                               
                                                    }

                                                    ?>
                                                    <option <?php echo $sel; ?> value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                                                    <?php
                                                }
                                                         
                                             }
                                             ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-lg-4">Date </label>
                                    <div class="col-lg-8">
                                        <input type="text" value="<?php echo isset($mentorcal->mentor_call)?$mentorcal->mentor_call:''; ?>" required placeholder="Choose Datetime" name="datecall" id='datecall' class="form-control datetimepicker" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4">Link </label>
                                    <div class="col-lg-8">
                                        <input type="text" value="<?php echo isset($mentorcal->link)?$mentorcal->link:''; ?>" required title="Link is required" url="true" placeholder="Enter Link" name="meetinglink" id='meetinglink' class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4">Recur Call (after 2 weeks)  </label>
                                    <div class="col-lg-8">
                                        <input type="checkbox" <?php echo isset($mentorcal->recur_call)?'checked="checked"':''; ?> checked="checked" id='recurcall' name="recurcall" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4">Notify User </label>
                                    <div class="col-lg-8">
                                        <input type="checkbox" checked="checked" id='notifyuser' name="notifyuser" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4"></label>
                                    <div class="col-lg-8">
                                        <?php if(isset($mentorcal->id)) {
                                            ?>
                                            <button  type="submit" class="btn btn-success invtbtn">Re-Schedule Call</button>
                                            <?php
                                        } else {
                                            ?>
                                            <button  type="submit" class="btn btn-success invtbtn">Invite</button>
                                            <?php
                                            
                                        } ?>
                                        
                                        
                                        <button type="reset" class="btn btn-warning resetcall">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <table class="table table-bordered table-striped table-hover" id="data_calls" >
                            <thead>
                                <tr>                        
                                    <th style="width: 4%;">SNo#</th>
                                    <th style="width: 13%;">Course</th>
                                    <?php if($u_role == "administrator" || isagencylocation()): ?>
                                    <th style="width: 12%;">Mentor</th>
                                    <?php endif; ?>
                                    <th style="width: 12%;">User</th>                                    
                                    <th style="width: 14%;">Call Date</th>
                                    <th style="width: 10%;">Is Accepted</th>
                                    <th style="width: 10%;">Is Recur</th>                                    
                                    <th style="width: 12%;">Status</th>                                    
                                    <th style="width: 14%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($mentorcals as $mentorcal) {
                                    $i++;                                    
                                    $isattended = '';
                                    
                                    $now = date("Y-m-d H:i:s");
                                    $calldate = date("Y-m-d H:i:s", strtotime($mentorcal->mentor_call));
                                    if($now > $calldate){
                                        if($mentorcal->is_attended == 'pending'){                                        

                                                $isattended = '<div class="divattended"><div>Call Attended</div> '
                                                        . '<label> <input data-name="'.$mentorcal->display_name.'" data-id="'.$mentorcal->id.'" type="radio" class="attendeornot" name="attendcall" id="attendyes" value="yes" /> Yes</label>'
                                                        . '<label> <input data-name="'.$mentorcal->display_name.'" data-id="'.$mentorcal->id.'" type="radio" class="attendeornot" name="attendcall" id="attendno" value="no" /> No</label></div>';                                        
                                        }
                                        else if($mentorcal->is_attended == 'yes'){
                                            $isattended = '<div class="divattended"><div>Call Attended</div><div class="alert alert-success">Yes</div></div>';
                                        }
                                        else if($mentorcal->is_attended == 'no'){
                                            $isattended = '<div class="divattended"><div>Call Attended</div><div class="alert alert-danger">No</div></div>';
                                        }
                                    }
                                    ?>
                                        <tr class="rowmentor" data-id="<?php echo $mentorcal->id; ?>">                                
                                            <td><?php echo $i; ?></td>
                                            <td class="title" data-title="<?php echo $mentorcal->course_id; ?>" >                                    
                                                <?php echo $mentorcal->title; ?>
                                            </td>
                                            <?php if($u_role == "administrator" || isagencylocation()): ?>
                                            <td class="mentor" data-id="<?php echo $mentorcal->mentor_id; ?>"><?php echo $mentorcal->mname; ?></td>
                                            <?php endif; ?>
                                            <td class="name" data-name="<?php echo $mentorcal->user_id; ?>" data-link="<?php echo $mentorcal->link; ?>">
                                                <?php echo $mentorcal->display_name; ?>
                                                
                                                <div class="attdiv"><?php echo $isattended;  ?></div>
                                            </td>
                                            <td class="date" data-date="<?php echo date("Y-m-d H:i",  strtotime($mentorcal->mentor_call)); ?>">
                                                <a href='admin.php?page=call_detail&mentor_call_id=<?php echo $mentorcal->id; ?>'><?php echo date("D d M Y, h:i a",  strtotime($mentorcal->mentor_call)); ?></a>
                                            </td> 
                                            
                                            <td class="isaccept" data-isaccept="<?php echo $mentorcal->is_accepted; ?>">                                    
                                                <?php echo $mentorcal->is_accepted == 1?"Yes":"No"; ?>
                                            </td>
                                            <td class="isrecur" data-isrecur="<?php echo $mentorcal->recur_call; ?>">                                    
                                                <?php echo $mentorcal->recur_call == 1?"Yes":"No"; ?>
                                            </td>
                                            
                                                <td class="status" data-name="<?php echo $mentorcal->status; ?>">                                    
                                                <?php echo $mentorcal->status == 'active'?"<div class='alert alert-success'>$mentorcal->status</div>":"<div class='alert alert-danger'>$mentorcal->status</div>"; ?>
                                            </td>                                                                 
                                            <td class="actiontd">
                                                <?php if($mentorcal->status == 'active'){ ?>
                                                <a data-id="<?php echo $mentorcal->id; ?>" href="javascript:;" title="Re-Schedule Call" class="reschedulecall btn btn-primary">Re-Schedule</a>
                                                <a style="margin-top: 5px;" data-id="<?php echo $mentorcal->id; ?>" href="javascript:;" title="Cancel Call" class="cancelcall btn btn-danger">Cancel Call</a>
                                                <?php } else { ?>
                                                <a href="javascript:;" data-id="<?php echo $mentorcal->id; ?>" class="deletecall btn btn-danger" title="Delete Call">Delete Call</a>
                                                <?php } ?>
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

        </div>
