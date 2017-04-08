<?php
include_once 'common.php';
global $wpdb;

$course_id = isset($_REQUEST['course'])?intval($_REQUEST['course']):0;

$courses = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT id,title FROM " . courses()." ORDER BY ord", ""
        )
);
if($course_id == 0){
    if(!empty($courses)){
        $course_id = $courses[0]->id;
    }
}

if($course_id == 0){
    die("Course Not Available");
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

$students = get_users(array(
        'meta_key'     => ACCESS_ROLE,
        'fields'     => "all",
    ));


?>
<div class="contaninerinner">
    <h4>Map Mentors</h4>
                <div class="bread_crumb">
                <ul>
                    <li title="Course Admin">
                        <a href="admin.php?page=course_admin">Course Admin</a> >>
                    </li>
                    <li title="Course">
                        Map Mentors
                    </li>
                </ul>
            </div>
    <div class="panel panel-primary">        
        <div class="panel-heading">Assign Users (Students) To Mentors</div>
        <div class="panel-body">
            <div class="row">
                <div class="control-group">
                    <label class="col-lg-2 control-label">Course</label>
                    <div class="col-lg-5">
                        <select class="form-control" id="courseselect" name="courseselect">                        
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
            </div>
            
            <div class="row">
                <div class="control-group">
                    <label class="col-lg-2 control-label">Assign To Selected</label>
                    <div class="col-lg-5">
                        <div class="mentordd assigntosel">
                            <select class="form-control" id="mentordropdown" name="mentordropdown">
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
                    </div>
                    <div class="col-lg-3"><a class="btn btn-success assignselected" href="javascript:;">Assign Mentor To Selected Users</a></div>
                    
                </div>
            </div>
                        
            <table class="table table-bordered table-striped table-hover" id="data_assign" >
                <thead>
                    <tr>
                        <th style="width: 5%;"><input name="select_all" id="select_all" value="1" type="checkbox"></th>
                        <th style="width: 25%;">Username</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 30%;">Mentor</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($students as $user) {                        
                        $mentor = get_mentor($user->ID,$course_id);
                        
                        $name = "<i>Not Assigned</i>";
                        if(!empty($mentor)){
                            $name = $mentor->display_name;                            
                        }
                        ?>
                            <tr class="mentorrow" data-uid="<?php echo $user->ID; ?>">
                                <td><input class="chkcommon" name="select_chk" id="select_chk" value="<?php echo $user->ID; ?>" type="checkbox"></td>
                                <td><?php echo $user->display_name; ?></td> 
                                <td><?php echo $user->user_email; ?></td> 
                                <td class="mentortd" data-mid="<?php echo $mentor->mentor_id; ?>"><?php echo $name; ?></td>
                                <td> 
                                    <div class="btndiv">
                                        <a data-uid="<?php echo $user->ID; ?>" class="btn btn-primary assignmentor" href="javascript:;">Assign Mentor</a>
                                    </div>
                                    <div class="dddiv" data-id="<?php echo $user->id; ?>" data-uid="<?php echo $user->ID; ?>">
                                        
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
