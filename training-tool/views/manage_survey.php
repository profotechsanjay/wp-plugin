<?php
include_once 'common.php';
global $wpdb;
$c_id = get_current_user_id();
$userrole = new WP_User($c_id);
$u_role =  $userrole->roles[0];

$form_id = isset($_REQUEST['form_id'])?intval($_REQUEST['form_id']):0;

$form = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT * FROM " . survey_forms()." "
                . "WHERE id = %d",$form_id
        )
    );
if(empty($form)){
    die("Form not Exist");
}

$mentorname = "";
if($u_role == "administrator" || isagencylocation()){
    $mentor_id = $form->mentor_id;
    $mentor = get_user_by("id",$mentor_id);
    $mentorname = "<strong>Mentor: </strong> ".$mentor->data->display_name;
}
$usertbl = $wpdb->prefix."users";
$surveyres = $wpdb->get_results
    (
        $wpdb->prepare
                (
                "SELECT s.*,m.user_email,m.display_name FROM " . survey_results()." s LEFT JOIN ".$usertbl." m "
                . "ON s.user_id = m.ID WHERE survey_id = %d ORDER BY s.created_dt DESC",$form_id
        )
    );
   

?>
<div class="contaninerinner">

    <!--div class="pull-right"><?php echo $mentorname; ?></div-->
    <h4>Manage Survey - <?php echo $form->title; ?> </h4>
    
    <div class="bread_crumb">
        <ul>
            <li title="All Survey List">
                <a href="admin.php?page=surveys">All Surveys</a> >>
            </li>
            <li title="Manage Survey">
                <?php echo $form->title; ?> 
            </li>
        </ul>
    </div>



    <div class="panel panel-primary">
        <div class="pull-right">            
            <a href="admin.php?page=surveys" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
        <div class="panel-heading">Survey - <?php echo $form->title; ?> </div>
        <div class="panel-body">           
            <form action="#" method="post" id="add_survey" name="add_survey">
                <input type="hidden" id="formid" name="formid" value="<?php echo $form->id; ?>" />
                
				<!-- Associate Survey with Code -->
				  <div class="form-group form-inline">
					<label class="col-lg-2 control-label">Associate Survey with : </label>
					<input type="radio" data-target="rdbCourse" class="rdbSurvey" name="rdbSurvey" value="Course"/> Course <input type="radio" class="rdbSurvey" data-target="rdbMentor" name="rdbSurvey" value="Mentor"/>Mentor  
				 </div>                                        
						
						
						<!--- ddMenu for Mentors -->
						<?php 
if($u_role == 'administrator' || isagencylocation()){
                    
                    $args = array(	
                            'role'         => MENTOR_ROLE,	
                            'fields'       => 'all'	
                     ); 

                    $mentors = get_users( $args );
                    
                    ?>
                        <div class="form-group form-inline" id="rdbMentor">
                            <label class="col-lg-2 control-label">Select Mentor</label>
                                                            
                                <select required title="Please Select a Mentor" class="form-control showMentorList" id="creatementorform" name="creatementorform">
                                <option value="">Select Mentor</option>
                                <?php
                                foreach($mentors as $mentor){
                                    $sel = '';
                                    if($form->mentor_id == $mentor->data->ID)
                                        $sel = 'selected="selected"';
									    if(!empty($mentor->data->display_name)){
                                    ?>
                                    <option <?php echo $sel; ?> value="<?php echo $mentor->data->ID; ?>"><?php echo $mentor->data->display_name; ?></option>
                                    <?php
										}
                                }
                                ?>
                                </select>
                        </div> 
						
                    <?php
                }
?>
						<!-- ../ends -->
						
						<!-- ddMenu for Courses --->
												<?php 
if($u_role == 'administrator' || isagencylocation()){
                    
                   $courses = $wpdb->get_results
						(
						$wpdb->prepare
								(
								"SELECT c.*, (SELECT count(id) as total FROM " . enrollment()." WHERE course_id = c.id) as enrolledby FROM " . courses()." c ORDER BY c.ord", ""
						)
				);
                    
                    ?>
                        <div class="form-group form-inline" id="rdbCourse">
                            <label class="col-lg-2 control-label">Select Course</label>
                                                                  
                                <select required title="Please Select a Mentor" class="form-control showCourseList" id="creatementorform" name="creatementorform">
                                <option value="">Select Course</option>
                                <?php
                                foreach ($courses as $course) {
                                    ?>
									<option value="<?php echo $course->id; ?>"><?php echo $course->title; ?></option>
									<?php
                                }
                                ?>
                                </select>
                                
                        </div>                        
                    <?php
                }
?>
						<!-- ../ends -->
			         <div class="form-group form-inline show_students">
						 <label class="col-lg-2 control-label"></label>
						 <input type="radio" id='chkAllSt' name="chkAll" value="check All"/> Check All  <input type="radio" id='unchkAllSt' name="chkAll" value="uncheck_all"/> Uncheck All<br/><br/>
					
				     </div>	 
					 <div class="form-group form-inline show_students">
						 <label class="col-lg-2 control-label head"></label>
						 <span class="showlistbyMent col-lg-8"></span>
				     </div>	
				<!-- ../ends -->
				
                <!--div class="form-group">
                <label class="col-lg-2 control-label">Select Users</label>
                <div class="col-lg-4">                                        
                    <select required multiple="true" data-placeholder="Select Users" class="chosen students form-control" id="student_user" name="student_user">                        
                         <?php 
                         
                            $usertbl = $wpdb->prefix."users";
                            $students = $wpdb->get_results
                            (
                                $wpdb->prepare
                                        (
                                        "SELECT s.ID,s.display_name "
                                        . "FROM " . mentor_assign()." map INNER JOIN " . $usertbl ." s ON map.user_id = s.ID "
                                        . "WHERE map.mentor_id = %d GROUP BY s.ID ORDER BY s.user_registered DESC",$form->mentor_id
                                )
                            );

                            foreach($students as $user){
                                ?>
                                <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                                <?php
                            }
                         
                         ?>
                    </select>                   
                </div>
            </div--->
                                
                <div class="form-group">
                            <label class="col-lg-2 control-label"></label>
                            <div class="col-lg-4">  
                                <a href="javascript:;" data-update="1" class="btn btn-primary sendsurvey">Send Survey</a>
                                
                            </div>
                        </div>
                <div class="clearfix"></div>
                 <div class="row"><hr/></div>
                            
                 <table class="table table-bordered table-striped table-hover" id="data_sresult" >
                            <thead>
                                <tr>
                                    <th style="width: 5%;">SNo</th>
                                    <th style="width: 15%;">Username</th>
                                    <th style="width: 15%;">Email</th>
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
                                            <td><?php echo $surveyr->display_name; ?></td>
                                            <td><?php echo $surveyr->user_email; ?></td>      
                                            <td><?php echo $surveyr->is_submitted == 0?"No":"Yes"; ?></td>      
                                            <td><?php echo date("Y-m-d h:i a",  strtotime($surveyr->created_dt)); ?></td>                                                                                                  
                                            <td class="actiontd acttd">                                                
                                                <a data-id="<?php echo $surveyr->id; ?>" href="admin.php?page=survey_result&survey_id=<?php echo $surveyr->id; ?>" class="btn btn-primary" title="View Result">View Result</a>                                                
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
                
            </form>
        </div>
    </div>
</div>