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
                "SELECT u.* "
                . "FROM " . $usertbl." u WHERE u.ID IN($mentorids) ORDER BY FIELD(ID, $mentorids) DESC",""
        )
);

$base_url = site_url();
$slug = PAGE_SLUG;
?>
<div class="contaninerinner coursereportpage">     
    <h4>Course Admin</h4>
    <div class="panel panel-primary mentorhandlepage">
        
        <div class="panel-heading">Course</div>
        <div class="panel-body">  
<!-- iframeType Starts -->                      
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

<!--- WebType Starts -->
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
                        <li><a href="admin.php?page=course_admin&course=<?php echo $course_id; ?>" >Users</a></li>
                        <li class="active"><a href="javascript:;">Mentors</a></li>                    
                    </ul>
                </div>
            <?php } else{
                ?>
                    <div class="row"><hr/></div>
                <?php
            } ?>
                        
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="reportarea">
                            <?php if($course_id <= 0): ?>
                                <div class="nocourseselected">No Course Selected</div>
                            <?php else: ?>
                                
                                <div class="coursename">
                                    <h4><?php echo $selcoursename; ?> - Mentor List</h4>
                                </div>
                                <div class="coursementor">
                                    
                                    <table class="tblenrolled display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                                <tr>
                                                        <th style="width: 5%">SNo</th>
                                                        <th style="width: 15%">Mentor Name</th>
                                                        <th style="width: 15%">Login</th>
                                                        <th style="width: 15%">Email</th>                                                        
                                                        <th style="width: 20%">Action</th>
                                                </tr>
                                        </thead>

                                        <tbody>

                                            <?php
                                            $j = 0;
                                            foreach($mentors as $user) {                                                                                   
                                                      
                                                    $j++;
                                                    ?>

                                                    <tr class="mentorrow" data-uid="<?php echo $user->ID; ?>">
                                                            <td><?php echo $j; ?></td>
                                                            <td>
                                                                <?php echo $user->display_name; ?>                                                                
                                                            </td>  
                                                            <td>
                                                                <?php echo $user->user_login; ?>                                                                
                                                            </td>  
                                                            <td>
                                                                <a href="mailto:<?php echo $user->user_email; ?>"><?php echo $user->user_email; ?></a>
                                                            </td>                                                                                                                                                                                                                                            
                                                            <td>
                                                                <a href="javascript:;" data-id="<?php echo $user->ID; ?>" class="remove_mentor btn btn-danger margin_top_10">Remove From Course</a>
                                                            </td>
                                                    </tr>

                                                    <?php


                                            }


                                            ?>

                                        </tbody>
                                </table>
                                    
                                    <!-- iframeType Starts -->
                                     <div class="staticform iframeType">
                                        <div class="row">
                                            <form method="post" name="mentorform" id="mentorform">
                                                <div class="control-group">
                                                    <label class="col-sm-1 lblfind control-label margin_top_10">Find Mentor</label>
                                                    <div class="col-sm-5">
                                                        <input type="email" name="memail" id="memail" required email title="Valid Email Required" Placeholder="Search Email..." class="form-control" placeholder="" />
                                                        <div class="clearfix"></div>
                                                        <span class="msgsml small">Press Enter to check mentor available</span>
                                                    </div>                                                    
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btnmentoradd btn btn-success">Add Mentor</button>
                                                    </div>
                                                </div>
                                            </form>
                                            
                                        </div>
                                    </div>

			<!-- webType Starts -->
			<div class="staticform webType">
						                <div class="row">
						                    <form method="post" name="mentorform" id="mentorform">
						                        <div class="control-group">
						                            <label class="col-lg-1 lblfind control-label margin_top_10">Find Mentor</label>
						                            <div class="col-lg-5">
						                                <input type="email" name="memail" id="memail" required email title="Valid Email Required" Placeholder="Search Email..." class="form-control" placeholder="" />
						                                <div class="clearfix"></div>
						                                <span class="msgsml small">Press Enter to check mentor available</span>
						                            </div>                                                    
						                            <div class="col-lg-2">
						                                <button type="button" class="btnmentoradd btn btn-success">Add Mentor</button>
						                            </div>
						                        </div>
						                    </form>
						                    
						                </div>
						            </div>
						            
						            
						        </div>
						        
						    <?php endif; ?>
					    </div>
					    
					</div>                
				    </div>
				</div>

			    </div>


</div>

