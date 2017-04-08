<?php
$uu_id = get_current_user_id();
$userlog = new WP_User($uu_id);
$ulog_role =  $userlog->roles[0];
if($ulog_role == 'administrator'){
?>
<div class="row">
    <div class="col-lg-12">
        
        <ul class="nav nav-tabs tabstop">
            <li class="<?php echo ($_GET['page'] == 'triningtool') || ($_GET['page'] == 'edit_course')?'active':''; ?>"><a href="admin.php?page=triningtool">Courses</a></li>                        
            <li class="<?php echo ($_GET['page'] == 'settings') || $_GET['page'] == 'course_images'?'active':''; ?>"><a href="admin.php?page=settings">Settings</a></li>            
            <li class="<?php echo ($_GET['page'] == 'course_admin') || $_GET['page'] == 'user_record' || $_GET['page'] == 'progress_detail'
                     || $_GET['page'] == 'course_admin_mentors' || $_GET['page'] == 'map_mentors' || $_GET['page'] == 'manage_mentor_calls'?'active':''; ?>"><a href="admin.php?page=course_admin">Course Admin</a></li>            
            <li class="lastchild <?php echo ($_GET['page'] == 'surveys') || $_GET['page'] == 'new_survey' || $_GET['page'] == 'manage_survey'
                     || $_GET['page'] == 'survey_result'?'active':''; ?>"><a href="admin.php?page=surveys">Surveys</a></li>                        
        </ul>
        
    </div>
</div>
<?php 

}
else{
    ?>
<div class="row">
    <div class="col-lg-12">
        
        <ul class="nav nav-tabs tabstop">            
            <li class="<?php echo ($_GET['page'] == 'course_admin') || $_GET['page'] == 'user_record' || $_GET['page'] == 'progress_detail'
                     || $_GET['page'] == 'map_mentors' || $_GET['page'] == 'manage_mentor_calls'?'active':''; ?>"><a href="admin.php?page=course_admin">Course Admin</a></li>            
            <li class="lastchild <?php echo ($_GET['page'] == 'surveys') || $_GET['page'] == 'new_survey' || $_GET['page'] == 'manage_survey'
                     || $_GET['page'] == 'survey_result'?'active':''; ?>"><a href="admin.php?page=surveys">Surveys</a></li>                      
        </ul>
        
    </div>
</div>
<?php
    
}
?>


