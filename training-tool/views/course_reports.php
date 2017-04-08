<?php
include_once 'common.php';
global $wpdb;
$c_id = get_current_user_id();
$userrole = new WP_User($c_id);
$u_role =  $userrole->roles[0];

$course_id = isset($_REQUEST['course'])?intval($_REQUEST['course']):0;

if($u_role == "administrator" || isagencylocation()){
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

$usertbl = $wpdb->prefix."users";

$mentors = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . $usertbl." WHERE ID IN($mentorids) ORDER BY FIELD(ID, $mentorids) DESC",""
        )
);

$usersenrolled = new stdClass();
if($course_id > 0){
          
    if($u_role == "administrator"  || isagencylocation()){
        $usersenrolled = $wpdb->get_results(
            $wpdb->prepare
                    (
                    "SELECT en.id,en.course_id,en.status,en.created_dt as date_enrol,u.user_login,u.ID,u.user_email,u.display_name, (select count(id) as total FROM " . resources() . " WHERE course_id = %d) as total_resources,"
                    . "(select count(rs.id) as covered FROM " . resource_status() . " rs INNER JOIN " . resources() . " re ON rs.resource_id = re.id WHERE rs.course_id = %d AND rs.user_id = u.ID) as total_covered, "
                    . "m.mentor_id,me.user_email as memail,me.display_name as mname FROM " . $usertbl . " u LEFT JOIN " . enrollment() . " en ON "
                    . "u.ID = en.user_id LEFT JOIN " . resource_status() . " rs ON en.id = rs.enrollment_id "
                    . "LEFT JOIN " . mentor_assign() . " m ON u.ID = m.user_id LEFT JOIN " . $usertbl . " me ON m.mentor_id = me.ID "
                    . "WHERE en.course_id = %d GROUP BY u.user_login ORDER BY en.created_dt DESC", 
                    $course_id,
                    $course_id,
                    $course_id
            )
        );  
         
        
    }
    else{
       $usersenrolled = $wpdb->get_results(
            $wpdb->prepare
                    (
                    "SELECT en.id,en.course_id,en.status,en.created_dt as date_enrol,u.user_login,u.ID,u.user_email,u.display_name, (select count(id) as total FROM " . resources() . " WHERE course_id = %d) as total_resources,"
                    . "(select count(rs.id) as covered FROM " . resource_status() . " rs INNER JOIN " . resources() . " re ON rs.resource_id = re.id WHERE rs.course_id = %d AND rs.user_id = u.ID) as total_covered, "
                    . "m.mentor_id,me.user_email as memail,me.display_name as mname FROM " . $usertbl . " u LEFT JOIN " . enrollment() . " en ON "
                    . "u.ID = en.user_id LEFT JOIN " . resource_status() . " rs ON en.id = rs.enrollment_id "
                    . "INNER JOIN " . mentor_assign() . " m ON u.ID = m.user_id LEFT JOIN " . $usertbl . " me ON m.mentor_id = me.ID "
                    . "WHERE en.course_id = %d AND m.course_id = %d AND m.mentor_id = %d GROUP BY u.user_login ORDER BY en.created_dt DESC", 
                    $course_id,
                    $course_id,
                    $course_id,
                    $course_id,
                    $c_id
            )
        );  
        
    }
     
  
    
}

$base_url = site_url();
$slug = PAGE_SLUG;
?>
<div class="contaninerinner coursereportpage">     
    <h4>Course Admin</h4>
    <div class="panel panel-primary">
        
        <div class="panel-heading">Course</div>
        <div class="panel-body">   
<!-- iframeType Start -->                     
            <div class="row toprow iframeType">
                <div class="col-sm-2"></div>
                <div class="col-sm-6">
                    <select class="form-control" id="reportcourse" name="reportcourse">                        
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
                <div class="col-sm-2">
                    <a href="javascript:;" class="generatereport btn btn-success">Generate Report</a>
                </div>
                <div class="col-sm-2"></div>
            </div>

<!-- WebType Start -->
<div class="row toprow webType">
                <div class="col-lg-2"></div>
                <div class="col-lg-6">
                    <select class="form-control" id="reportcourse" name="reportcourse">                        
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
                <div class="col-lg-2">
                    <a href="javascript:;" class="generatereport btn btn-success">Generate Report</a>
                </div>
                <div class="col-lg-2"></div>
            </div>
            
            
            <?php if($u_role == 'administrator' || isagencylocation()){ ?>
                <div class="">
                    <ul class="nav nav-tabs tabadminreports">
                        <li class="active"><a href="javascript:;">Users</a></li>
                        <li><a href="admin.php?page=course_admin_mentors&course=<?php echo $course_id; ?>">Mentors</a></li>                    
                    </ul>
                </div>
            <?php } else{
                ?>
                    <div class="row"><hr/></div>
                <?php
            } ?>
            
            <div class="mentordd" style="display: none; visibility: hidden;">
                <select class="form-contol" id="mentordropdown" name="mentordropdown">
                    <option value="">Select Mentor</option>
                    <?php
                    foreach($mentors as $mentor){
                        ?>
                        <option value="<?php echo $mentor->ID; ?>"><?php echo $mentor->display_name; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="reportarea">
                            <?php if($course_id <= 0): ?>
                                <div class="nocourseselected">No Course Selected</div>
                            <?php else: ?>
                                
                                <div class="coursename">
                                    <h4><?php echo $selcoursename; ?> - User List</h4>
                                </div>
                                <div class="coursementor">
                                    
                                    <table class="tblenrolled display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                                <tr>
                                                        <th style="width: 5%">SNo</th>
                                                        <th style="width: 15%">User</th>
                                                        <?php if($u_role == "administrator"  || isagencylocation()): ?>
                                                        <th style="width: 15%">Mentor</th>
                                                        <?php endif; ?>
                                                        <th style="width: 15%">Course Progress</th>                                                        
                                                        <th style="width: 15%">Next Call</th>
                                                        <th style="width: 20%">Action</th>
                                                </tr>
                                        </thead>

                                        <tbody>

                                            <?php
                                            $j = 0;
                                            foreach($usersenrolled as $user) {                                                                                   
                                                
                                                    $total_resources = $user->total_resources;
                                                    $total_covered = $user->total_covered; 
                                                    
                                                    $percent = 0;                                                    
                                                    if($total_covered > 0)
                                                        $percent = floor(($total_covered / $total_resources) * 100);
                                                    
                                                    $txtassign = 'Change';
                                                    if($user->mname == ''){
                                                        $txtassign = 'Assign';
                                                    }
                                                                                                                                                           
                                                    $j++;
                                                    $next_call_date = "<i>Not Found</i>"; 
                                                    ?>

                                            <tr class="mentorrow" data-uid="<?php echo $user->ID; ?>">
                                                            <td><?php echo $j; ?></td>
                                                            <td>
                                                                <div><?php echo $user->display_name; ?></div>
                                                                <div><a href="mailto:<?php echo $user->user_email; ?>"><?php echo $user->user_email; ?></a></div>
                                                                <div>
                                                                    <a href="admin.php?page=user_record&user_id=<?php echo $user->ID; ?>&course_id=<?php echo $course_id; ?>" class="btn_small">View Record</a>
                                                                </div>
                                                            </td>                                                            
                                                            <?php if($u_role == 'administrator' || isagencylocation()) {  
                                                                
                                                                $mentor = get_mentor($user->ID,$course_id);
                                                                $name = "<i>Not Assigned</i>"; $mentor_id = 0;
                                                                if(!empty($mentor)){
                                                                    $name = $mentor->display_name;                            
                                                                    $mentor_id = $mentor->mentor_id;
                                                                    $next_call_date = get_next_calldate($user->ID,$course_id,$mentor_id);
                                                                }
                                                                
                                                                ?>
                                                                <td>
                                                                    <span class="mentorspan" data-mid="<?php echo $mentor->mentor_id != ''?$mentor->mentor_id:''; ?>">
                                                                        <?php echo $name; ?>
                                                                    </span>                                                                    
                                                                    <div class="btndiv"><a class="assignmentor" data-uid="<?php echo $user->ID; ?>" href='javascript:;'><?php echo $txtassign; ?> Mentor</a></div>
                                                                    <div class="dddiv" data-uid="<?php echo $user->ID; ?>">
                                        
                                                                    </div>                                                                    
                                                                </td>
                                                            <?php                                                                     
                                                                }
                                                                else{
                                                                    
                                                                    $next_call_date = get_next_calldate($user->ID,$course_id,$c_id);
                                                                }                                                            
                                                            
                                                            ?>
                                                            <td>                                                                 
                                                                <div class="progress_inner">
                                                                    <div class="bar_info">
                                                                        <span><label class="perint"><?php echo $percent; ?></label>% Complete</span>
                                                                        <div class="bar-progress">
                                                                            <div class="perdiv" class="bar wip" style="width:<?php echo $percent; ?>%"></div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                
                                                            </td>                                                            
                                                            <td>
                                                                <?php echo $next_call_date; ?>
                                                            </td>
                                                            <td>
                                                                <a href="admin.php?page=progress_detail&user=<?php echo $user->ID; ?>&course=<?php echo $course_id; ?>" class="btn btn-success">Progress</a>
                                                                <a data-course="<?php echo $course_id; ?>" data-uid="<?php echo $user->ID; ?>" href="javascript:;" class="callsch btn btn-warning">Schedule Call</a>
                                                            
                                                                 <?php if($u_role == 'administrator' || isagencylocation()){ ?>
                                                                    <a href="javascript:;" data-id="<?php echo $user->id; ?>" class="revoke_course_access btn btn-danger margin_top_10">Revoke Access</a>
                                                                 <?php } ?>
                                                            </td>
                                                    </tr>

                                                    <?php


                                            }


                                            ?>

                                        </tbody>
                                </table>
                                    
                                     <?php if($u_role == 'administrator' || isagencylocation()){ ?>
<!-- iframeType Start -->
                                    <div class="staticform iframeType">
                                        <div class="row">
                                            <form method="post" name="userform" id="userform">
                                                <div class="control-group">
                                                    <label class="col-sm-1 lblfind control-label margin_top_10">Find User</label>
                                                    <div class="col-sm-5">
                                                        <input type="email" name="uemail" id="uemail" required email title="Valid Email Required" Placeholder="Search Email..." class="form-control" placeholder="" />
                                                        <div class="clearfix"></div>
                                                        <span class="msgsml small">Press Enter to check user available</span>
                                                    </div>                                                    
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btnenrolclk btn btn-success">Enroll as Student</button>
                                                    </div>
                                                </div>
                                            </form>
                                            
                                        </div>
                                    </div>

<!-- webType Start -->

 <div class="staticform webType">
                                        <div class="row">
                                            <form method="post" name="userform" id="userform">
                                                <div class="control-group">
                                                    <label class="col-lg-1 lblfind control-label margin_top_10">Find User</label>
                                                    <div class="col-lg-5">
                                                        <input type="email" name="uemail" id="uemail" required email title="Valid Email Required" Placeholder="Search Email..." class="form-control" placeholder="" />
                                                        <div class="clearfix"></div>
                                                        <span class="msgsml small">Press Enter to check user available</span>
                                                    </div>                                                    
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btnenrolclk btn btn-success">Enroll as Student</button>
                                                    </div>
                                                </div>
                                            </form>
                                            
                                        </div>
                                    </div>
                                     <?php } ?>
                                    
                                </div>
                                
                            <?php endif; ?>
                    </div>
                    
                </div>                
            </div>
        </div>

    </div>


</div>

