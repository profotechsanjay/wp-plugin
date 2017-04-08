<?php
wp_enqueue_style('jquery-ui.css', "//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css");
include_once 'common.php';
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();

$c_id = get_current_user_id();
$userrole = new WP_User($c_id);
$u_role =  $userrole->roles[0];

$user_id = isset($_REQUEST['user_id'])?intval($_REQUEST['user_id']):0;
$course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;
$user = get_user_by("id",$user_id);
if(empty($user)){
    ?>
        <div class="update-nag">Invalid User</div>
    <?php
    die;
}

$txtsql = ""; $txtsql1 = "";
if($u_role != "administrator"){
    $txtsql = "AND m.mentor_id = $c_id";
    $txtsql1 = "AND s.mentor_id = $c_id";
    
}

$usertabl = $wpdb->prefix."users";
$mentorcals = $wpdb->get_results
    (
    $wpdb->prepare
            (
            "SELECT m.*, u.display_name,me.display_name as mname,c.title FROM " . mentorcall()." m LEFT JOIN " . $usertabl ." u ON m.user_id = u.ID "
            . "LEFT JOIN " . courses()." c ON  m.course_id = c.id "
            . "LEFT JOIN " . $usertabl." me ON m.mentor_id = me.ID WHERE m.user_id = $user_id $txtsql ORDER BY m.created_dt DESC",""
    )
);


if($u_role == "administrator" || isagencylocation()){
    $courses = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT c.* FROM " . courses()." c INNER JOIN ". enrollment() ." e ON c.id = e.course_id WHERE e.user_id = %d ORDER BY ord", $user_id
            )
    );
}
else{
    $courses = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT c.id,c.title,c.description FROM " . courses()." c INNER JOIN ". enrollment() ." e ON c.id = e.course_id WHERE e.user_id = %d"
                    . " AND FIND_IN_SET($c_id,c.mentor_ids) ORDER BY ord", $user_id
            )
    );
}



$usertbl = $wpdb->prefix."users";
$surveyres = $wpdb->get_results
    (
        $wpdb->prepare
                (
                "SELECT s.*,m.user_email,m.display_name,f.title FROM " . survey_results()." s LEFT JOIN ".$usertbl." m "
                . "ON s.mentor_id = m.ID INNER JOIN ".  survey_forms()." f ON s.survey_id = f.id "
                . "WHERE s.user_id = %d $txtsql1 ORDER BY s.created_dt DESC",$user_id
        )
    );
   
//    print_r("SELECT s.*,m.user_email,m.display_name,f.title FROM " . survey_results()." s LEFT JOIN ".$usertbl." m "
//                . "ON s.mentor_id = m.ID INNER JOIN ".  survey_forms()." f ON s.survey_id = f.id "
//                . "WHERE s.user_id = $user_id $txtsql1 ORDER BY s.created_dt DESC");
?>        
<div class="contaninerinner">       
    <div class="pull-right">
        <?php echo $user->data->display_name; ?> [<?php echo $user->data->user_email; ?>]
    </div>
    <h4>User Record </h4>
    <div class="bread_crumb">
        <ul>
            <li title="Course Admin">
                <a href="admin.php?page=course_admin&course=<?php echo $course_id; ?>">Course Admin</a> >>
            </li>            
            <li title="User Record">
                Detail Of <?php echo $user->data->display_name; ?> [<?php echo $user->data->user_email; ?>]
            </li>
        </ul>
    </div>
    <div class="panel panel-primary">
        
        <div class="panel-heading">Courses Enrolled By <?php echo $user->data->display_name; ?></div>
        
        <div class="panel-body">
            
            <div class="accordion">
                
                <?php
                foreach($courses as $course){
                    
                    $submissions = get_submissions($user_id,$course->id);
                    
                    ?>                                                
                        <h3><?php echo $course->title; ?>                            
                        </h3>
                        <div>                            
                            <?php
                            if(empty($submissions)){
                                echo "<div><i>No Work Submitted</i></div>";
                            }
                            else{
								
								
                                foreach($submissions as $submission){
									
                                    $links = explode(",", $submission->links);
                                    $files = explode(",", $submission->doc_files);
                                    $docs = explode(",", $submission->docs);
                                    echo "<div class='work_detail'><h5><span>Module : $submission->mod_title</span> <span>Lesson : $submission->lesson_title</span> <span>Exercise : $submission->exercise_title</span></h5>";
                                    echo "<h5>Submitted Links</h5>";
									foreach($links as $link){
                                        echo "<div class='divlik'>"                                       
                                        . "<a target='_blank' href='$link'>$link</a>"
                                        . "</div>";
                                    }
                                   
									echo "<h5>Submitted Files</h5>";
									foreach($files as $file){
                                        echo "<div class='divlik'>"                                       
                                        . "<a target='_blank' href='$file'>$file</a>"
                                        . "</div>";
                                    }
                                    echo "</div>";
                                }
                            }
                            
                            ?>                            
                            
                        </div>
                    <?php
                }
                ?>
                                
                
            </div>
            
            
        </div>


    </div>
    <div class="panel panel-primary">
        
        <div class="panel-heading">Mentor Call History</div>
        
        <div class="panel-body">
            
            <table class="table table-bordered table-striped table-hover" id="data_calls" >
                            <thead>
                                <tr>                        
                                    <th style="width: 13%;">Course</th>
                                    <?php if($u_role == "administrator" || isagencylocation()): ?>
                                    <th style="width: 12%;">Mentor</th>
                                    <?php endif; ?>                                                                    
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
                                            <td class="title" data-title="<?php echo $mentorcal->course_id; ?>" >                                    
                                                <?php echo $mentorcal->title; ?>
                                            </td>
                                            <?php if($u_role == "administrator" || isagencylocation()): ?>
                                            <td class="mentor" data-id="<?php echo $mentorcal->mentor_id; ?>"><?php echo $mentorcal->mname; ?></td>
                                            <?php endif; ?>
                                            
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
    
    <div class="panel panel-primary">
        
        <div class="panel-heading">Survey Submitted</div>
        
        <div class="panel-body">
            
            <table class="table table-bordered table-striped table-hover commontbl" >
                            <thead>
                                <tr>
                                    <th style="width: 5%;">SNo</th>
                                    <th style="width: 15%;">Survey</th>                                    
                                    <?php if($u_role == "administrator" || isagencylocation()): ?>
                                    <th style="width: 15%;">Mentor</th>
                                    <?php endif; ?> 
                                    <th style="width: 15%;">Survey Submitted</th>      
                                    <th style="width: 15%;">Date</th>										
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($surveyres as $surveyr) {
                                   
                                    ?>
                                        <tr class="rowmod" data-id="<?php echo $surveyr->id; ?>">
                                            <td><?php echo $i + 1; ?></td> 
                                            <td><?php echo $surveyr->title; ?></td>
                                            <?php if($u_role == "administrator" || isagencylocation()): ?>
                                            <td>
                                                <div><?php echo $surveyr->display_name; ?></div>
                                                <div><?php echo $surveyr->user_email; ?></div>
                                            </td>      
                                            <?php endif; ?>
                                            <td><?php echo $surveyr->is_submitted == 0?"No":"Yes"; ?></td>      
                                            <td><?php echo date("D d M Y, h:i a",  strtotime($surveyr->created_dt)); ?></td>                                                                                                  
                                            <td class="actiontd acttd">                                                
                                                <a data-id="<?php echo $surveyr->id; ?>" href="admin.php?page=survey_result&course=<?php echo $course_id; ?>&survey_id=<?php echo $surveyr->id; ?>" class="btn btn-primary" title="View Result">View Result</a>                                                
                                                <a href="javascript:;" data-id="<?php echo $surveyr->id; ?>" title="Delete Form" class="deletesurvey btn btn-danger">Delete</a>                                    
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
            
            
        </div>


    </div>
    
</div>


