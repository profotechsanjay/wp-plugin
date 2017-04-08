<?php
include_once 'common.php';
global $wpdb;

$mentor_call_id = isset($_REQUEST['mentor_call_id'])?intval($_REQUEST['mentor_call_id']):0;

$usertabl = $wpdb->prefix."users";
$mentorcal = $wpdb->get_row
    (
    $wpdb->prepare
            (
            "SELECT m.*, u.display_name,me.display_name as mname,c.title FROM " . mentorcall()." m LEFT JOIN " . $usertabl ." u ON m.user_id = u.ID "
            . "LEFT JOIN " . courses()." c ON  m.course_id = c.id "
            . "LEFT JOIN " . $usertabl." me ON m.mentor_id = me.ID WHERE m.id = %d ORDER BY m.created_dt DESC",$mentor_call_id
    )
);
if(empty($mentorcal)){
    die("invalid Call");
}
//print_r($mentorcal); die;
?>
<div class="contaninerinner detailcallpage">       
    <h4>Mentor Call Detail</h4>
    <div class="bread_crumb">
        <ul>
            <li title="Course Admin">
                <a href="admin.php?page=course_admin&course=<?php echo $mentorcal->course_id; ?>">Course Admin</a> >>
            </li>
            <li title="Manage Mentor Calls ">
                    <a class="backbread" href="admin.php?page=manage_mentor_calls&course=<?php echo $mentorcal->course_id; ?>">Manage Mentor Calls</a> >>
            </li>
            <li title="Mentor Call Detail">
                Mentor Call Detail
            </li>
        </ul>
    </div>
    <div class="panel tab-content">
        <div class="panel-body">
            <ul class="list-group grouplist">
               
                <li class="list-group-item">
                    <label>Course : </label> <span><?php echo $mentorcal->title; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Mentor : </label> <span><?php echo $mentorcal->mname; ?></span>
                </li>
                <li class="list-group-item">
                    <label>User : </label> <span><?php echo $mentorcal->display_name; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Call Date : </label> <span><?php echo $mentorcal->mentor_call; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Link : </label> <span><?php echo $mentorcal->link; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Status : </label> <span><?php echo $mentorcal->status; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Is Recur Call? : </label> <span><?php echo $mentorcal->recur_call == 1?'Yes':'No'; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Is Accepted? : </label> <span><?php echo $mentorcal->is_accepted == 1?'Yes':'No'; ?></span>
                </li>
                <li class="list-group-item">
                    <label>Is Attended? : </label> <span><?php echo $mentorcal->is_attended == 1?'Yes':'No'; ?></span>
                </li>
                
            </ul>
            <?php if($mentorcal->status == "active") { ?>
                <a data-id="<?php echo $mentorcal->id; ?>" href="javascript:;" title="Re-Schedule Call" class="reschedulecall btn btn-primary">Re-Schedule</a>
               <a data-id="<?php echo $mentorcal->id; ?>" href="javascript:;" title="Cancel Call" class="cancelcall btn btn-danger">Cancel Call</a>
            <?php } else { ?>
            <a href="javascript:;" data-id="<?php echo $mentorcal->id; ?>" class="deletecall btn btn-danger" title="Delete Call">Delete Call</a>
            <?php } ?>                                    
        </div>


    </div>
</div>


