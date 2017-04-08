	<?php
	global $wpdb;
	global $current_user;
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	if($user_id == 0){
		json(0,'Login is required');
	}

    if (isset($_REQUEST["param"])) {
		
		/* get users survey basis mentors - custom*/
		if($_REQUEST["param"]=="get_survey_users"){
		    $mentor_id = $_POST['ment_id'];
			$option_data='';
			$users = $wpdb->get_results("SELECT * FROM wp_mentor_assign where mentor_id = '".$mentor_id."'");
			foreach($users as $user){
			   $uemail = get_userdata($user->user_id);
			   $option_data.="<li><input type='checkbox'  value='".$uemail->ID."' class='chkSt' name='st[]'> ".$uemail->user_email."</li>";
			}
			echo $option_data;
			die;
		}
		/*  ../ends  */
		/* get users survey basis course - custom*/
		if($_REQUEST["param"]=="get_survey_users_by_course"){
		    $course_id = $_POST['cour_id'];
			$option_data='';
			$users = $wpdb->get_results("SELECT * FROM wp_enrollment where course_id = '".$course_id."'");
			foreach($users as $user){
			   $uemail = get_userdata($user->user_id);
			   $option_data.="<li><input type='checkbox'  value='".$uemail->ID."' class='chkSt' name='st[]'> ".$uemail->user_email."</li>";
			}
			echo $option_data;
			die;
		}
		/*  ../ends  */
		
        if ($_REQUEST["param"] == "add_course") {
                       
            $user_id = $current_user->ID;
            $now = date("Y-m-d H:i:s");
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';
            $description = isset($_POST['description'])? htmlspecialchars($_REQUEST["description"]):'';            
            $description = stripcslashes($description);                                    
            
            if($title == ''){
                json(0,'Title is required');
            }
            
            $course = $wpdb->get_row
            (
                    $wpdb->prepare
                    (
                            "SELECT id,user_ids FROM ". courses() . " WHERE id = %d",
                            $course_id
                    )
            );
            
            if(!empty($course)){
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . courses() . " SET title = %s, description = %s WHERE id = %d", 
                                $title, $description, $course_id
                        )
                );
                
                json(1,'Course Updated');
            }
            else{
                
                $ord = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT MAX(ord) FROM " . courses(),""
                        )
                );
                
                $ord = $ord + 1;
                
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . courses() . " (ord, title, description, created_by, created_dt, updated_by) "
                                . "VALUES (%d, %s, %s, %d, '%s', %d)", 
                                $ord, $title, $description, $user_id, $now, $user_id
                        )
                );
                
                $lastid = $wpdb->insert_id;                
                $arr = array("lastid"=>$lastid);
                json(1,'Course Created',$arr);
            }
            
        }
        else if ($_REQUEST["param"] == "enroll_user") {
            
            $uemail = isset($_POST["uemail"])?htmlspecialchars($_POST["uemail"]):0;
            $user = get_user_by("email",$uemail);
            if(empty($user)){
                json(0,'This email is not registered with MCC');
            }
            $course_id = isset($_POST["course_id"])?intval($_POST["course_id"]):0;
            $is_enrol = isset($_POST["is_enrol"])?intval($_POST["is_enrol"]):0;
            $user_id = $user->data->ID;
            $is_enrolled = $wpdb->get_var
                    (
                    $wpdb->prepare
                            (
                            "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
                    )
            );
            
            if($is_enrolled >= 1){
                json(0,'User has been already enrolled for this course.');
            }
            if($is_enrol == 0){
                json(1,'User is available for enrollment.');
            }
            $now = date("Y-m-d H:i:s");
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "INSERT INTO " . enrollment() . " (course_id, user_id, created_dt) "
                            . "VALUES (%d, %d, '%s')", $course_id, $user_id, $now
                    )
            );
            permission_email_course($course_id,$user_id);
            json(1,'User has been sucessfully enrolled for this course');
        }
        
        
        else if ($_REQUEST["param"] == "add_mentor") {
            
            $uemail = isset($_POST["memail"])?htmlspecialchars($_POST["memail"]):0;
            $user = get_user_by("email",$uemail);
            if(empty($user)){
                json(0,'This email is not registered with MCC');
            }
            
            $user_id = $user->data->ID;
            $userrole = new WP_User($user_id);            
            $u_role =  $userrole->roles[0];

//            if($u_role != MENTOR_ROLE){
//                json(0,'User Role is not mentor. Please change user role in User section.');
//            }
            
            $course_id = isset($_POST["course_id"])?intval($_POST["course_id"]):0;
            $is_enrol = isset($_POST["is_enrol"])?intval($_POST["is_enrol"]):0;
            
            $course = $wpdb->get_row
            (
                    $wpdb->prepare
                            (
                            "SELECT id,title,mentor_ids FROM " . courses()." WHERE id = %d", $course_id
                    )
            );
            if(empty($course)){
                json(0,'Invalid Course.');
            }
            
            $mentor_ids = array();
            $mentor_ids = explode(",", $course->mentor_ids);
            if(in_array($user_id, $mentor_ids)){
                json(0,'Mentor already added to this course');
            }          
            if($is_enrol == 0){
                json(1,'Mentor is available.');
            }
            array_push($mentor_ids, $user_id);
            $now = date("Y-m-d H:i:s");
            $mentor_ids = implode(",", $mentor_ids);
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . courses() . " SET mentor_ids = %s WHERE id = %d",$mentor_ids,$course_id                            
                    )
            );
            mentor_add_course_email($course_id,$user_id);
            json(1,'Mentor has been sucessfully added for this course');
        }
        
        else if ($_REQUEST["param"] == "revoke_user") {
            
            $enrol_id = isset($_POST["enrol_id"])?intval($_POST["enrol_id"]):0;            
            $enrolled = $wpdb->get_row
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . enrollment() . " WHERE id = %d", $enrol_id
                    )
            );
            if(empty($enrolled)){
                json(1,'Invalid Enrollment');
            }
            
            $course_id = $enrolled->course_id;
            $user_id = $enrolled->user_id;              
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . enrollment() . " WHERE id = %d", $enrol_id
                    )
            );
            permission_revoke_course($course_id,$user_id);
            json(1,'User permissions has been sucessfully revoked for this course');
        }
        
        else if ($_REQUEST["param"] == "remove_mentor") {
            
            $u_id = isset($_POST["u_id"])?intval($_POST["u_id"]):0;
            $course_id = isset($_POST["course_id"])?intval($_POST["course_id"]):0;
            
            $hasrecord = $wpdb->get_row
                    (
                    $wpdb->prepare
                            (
                            "SELECT mentor_ids FROM " . courses() . " WHERE FIND_IN_SET($u_id,mentor_ids) AND id = %d", $course_id
                    )
            );
            if(empty($hasrecord)){
                json(1,'Invalid Request');
            }
            
            $mentor_ids = explode(",", $hasrecord->mentor_ids);
            $new_str = "";
            foreach($mentor_ids as $mentor_id){
                if($mentor_id != $u_id){
                    $new_str .= $mentor_id.",";
                }
            }            
            $new_str = substr($new_str, 0, -1);
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . courses() . " SET mentor_ids = %s WHERE id = %d", $new_str, $course_id
                    )
            );
            remove_from_course($course_id,$u_id);
            json(1,'Mentor has been sucessfully removed from this course');
        }
        
        else if ($_REQUEST["param"] == "delete_course") {
            $id = isset($_POST["id"])?intval($_POST["id"]):0;
            
            deletemediacourse($id);
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . resources() . " WHERE course_id = %d", $id
                    )
            );
            
             $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . lessons() . " WHERE module_id IN (SELECT id FROM " . modules() . " WHERE course_id = %d)", $id
                    )
            );
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . modules() . " WHERE course_id = %d", $id
                    )
            );
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . courses() . " WHERE id = %d", $id
                    )
            );
            
            json(1,'Course Deleted');
            
        } else if ($_REQUEST["param"] == "add_module") {
            
            $user_id = $current_user->ID;
            $now = date("Y-m-d H:i:s");
            $module_id = isset($_POST['id'])?intval($_POST['id']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';
            $link = isset($_POST['link'])?htmlspecialchars(trim($_POST['link'])):'';            
            
            $description = isset($_POST['description'])? htmlspecialchars($_REQUEST["description"]):'';            
            $description = stripcslashes($description);
            
            if($title == '' || $course_id == 0){
                json(0,'Title and course id are required');
            }
            
            if($module_id > 0){
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . modules() . " SET title = %s, description = %s, external_link = %s WHERE id = %d", 
                                $title, $description, $link, $module_id
                        )
                );                
                json(1,'Module Updated');
            }
            else{
                
                
                
                $ord = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT MAX(ord) FROM " . modules()." WHERE course_id = %d", $course_id
                        )
                );
                
                $ord = $ord + 1;
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . modules() . " (ord, course_id, title, description, external_link, created_by, created_dt, updated_by) "
                                . "VALUES (%d, %d, %s, %s, %s, %d, '%s', %d)", 
                                $ord, $course_id, $title, $description, $link, $user_id, $now, $user_id
                        )
                );
                                
                json(1,'Module Created');
            }
            
        } else if ($_REQUEST["param"] == "delete_module") {
            $id = isset($_POST["id"])?intval($_POST["id"]):0;
            $course_id = isset($_POST["course_id"])?intval($_POST["course_id"]):0;
            
            deletemediamodule($id);
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . resources() . " WHERE module_id = %d", $id
                    )
            );
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . lessons() . " WHERE module_id = %d", $id
                    )
            );
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . modules() . " WHERE id = %d", $id
                    )
            );
            updatehours_and_resources($id,$course_id,0);
            json(1,'Module Deleted');
            
        } else if ($_REQUEST["param"] == "add_lesson") {
            
            $user_id = $current_user->ID;
            $now = date("Y-m-d H:i:s");
            $lesson_id = isset($_POST['lessid'])?intval($_POST['lessid']):0;
            $module_id = isset($_POST['module_id'])?intval($_POST['module_id']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';            
            //$hours = isset($_POST['hours'])?htmlspecialchars(trim($_POST['hours'])):0;            
            $link = isset($_POST['link'])?htmlspecialchars(trim($_POST['link'])):'';            
            $description = isset($_POST['description'])? htmlspecialchars($_REQUEST["description"]):'';            
            $description = stripcslashes($description);
            
            if($title == '' || $module_id == 0){
                json(0,'Title, Time and module id are required');
            }
            
            if($lesson_id > 0){
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . lessons() . " SET title = %s, description = %s, external_link = %s WHERE id = %d", 
                                $title, $description, $link, $lesson_id
                        )
                );
                
                json(1,'Lesson Updated');
            }
            else{
                
                $ord = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT MAX(ord) FROM " . lessons()." WHERE module_id = %d", $module_id
                        )
                );
                
                $ord = $ord + 1;
                
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . lessons() . " (ord, module_id, title, description, external_link, created_by, created_dt, updated_by) "
                                . "VALUES (%d, %d, %s,  %s, %s, %d, '%s', %d)", 
                                $ord, $module_id, $title, $description, $link, $user_id, $now, $user_id
                        )
                );
                
                json(1,'Lesson Created');
            }
            
        } else if ($_REQUEST["param"] == "delete_lesson") {
            $id = isset($_POST["id"])?intval($_POST["id"]):0;
            $module_id = isset($_POST['module_id'])?intval($_POST['module_id']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            deletemedia($id);                        
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . resources() . " WHERE lesson_id = %d", $id
                    )
            );
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . lessons() . " WHERE id = %d", $id
                    )
            );
            updatehours_and_resources($module_id,$course_id,$id);
            json(1,'Lesson Deleted');
            
        }
        else if ($_REQUEST["param"] == "add_resource") {
            
            $user_id = $current_user->ID;
            $now = date("Y-m-d H:i:s");
            $resource_id = isset($_POST['resid'])?intval($_POST['resid']):0;
            $lesson_id = isset($_POST['lesson_id'])?intval($_POST['lesson_id']):0;
            $module_id = isset($_POST['module_id'])?intval($_POST['module_id']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';            
            $button_type = isset($_POST['button_type'])?htmlspecialchars(trim($_POST['button_type'])):'mark'; 
            
            
            $hours = isset($_POST['hours'])?htmlspecialchars(trim($_POST['hours'])):0;            
            $link = isset($_POST['link'])?htmlspecialchars(trim($_POST['link'])):'';            
            
            $description = isset($_POST['description'])? htmlspecialchars($_REQUEST["description"]):'';            
            $description = stripcslashes($description);
            
            if($title == '' || $module_id == 0 || $lesson_id == 0){
                json(0,'Title, module id, Lesson Id are required');
            }
            
            if($resource_id > 0){
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . resources() . " SET title = %s, description = %s, total_hrs = %s, external_link = %s, "
                                . "button_type = %s WHERE id = %d", 
                                $title, $description, $hours, $link, $button_type, $resource_id
                        )
                );
                updatehours_and_resources($module_id,$course_id,$lesson_id);
                json(1,'Exercise Updated');
            }
            else{
                
                $ord = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT MAX(ord) FROM " . resources()." WHERE lesson_id = %d", $lesson_id
                        )
                );
                
                $ord = $ord + 1;
                
                $result = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . resources() . " (ord, course_id, module_id, lesson_id, title, description, total_hrs, external_link, button_type, created_by, created_dt, updated_by) "
                                . "VALUES (%d, %d, %d, %d, %s,  %s, %s, %s, %s, %d, '%s', %d)", 
                                $ord, $course_id, $module_id, $lesson_id, $title, $description, $hours, $link, $button_type, $user_id, $now, $user_id
                        )
                );
                updatehours_and_resources($module_id,$course_id,$lesson_id);
                json(1,'Exercise Created',$result);
            }
            
        }
        else if ($_REQUEST["param"] == "delete_resource") {
            $id = isset($_POST["id"])?intval($_POST["id"]):0;
            $module_id = isset($_POST['module_id'])?intval($_POST['module_id']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $lesson_id = isset($_POST['lesson_id'])?intval($_POST['lesson_id']):0;
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . resources() . " WHERE id = %d", $id
                    )
            );
                       
            updatehours_and_resources($module_id,$course_id,$lesson_id);
            json(1,'Exercise Deleted');
            
        }
        else if ($_REQUEST["param"] == "save_settings") {
            $arr = $_POST; $i = 0;            
            $ids = isset($_REQUEST['ids'])?$_REQUEST['ids']:0;    
            
            if(count($ids) > 0){
                foreach($ids as $id){
                    
                    $key = "key_$id";
                    $keyname = $_POST["$key"];
                    
                    $valkey = "val_$id";
                    $value = $_POST["$valkey"];
                    
                    $show = 0;
                    $showelement = "show_$id";                   
                    if(isset($_POST["$showelement"])){
                        $show = 1;
                    }
                    
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE " . setting() . " SET keyname = %s, keyvalue = %s, is_show = %d WHERE id = %d",
                                    $keyname,
                                    $value,
                                    $show,
                                    $id
                            )
                    ); 
                    $i++;
                }
                
                if($i > 0)
                    json(1,'Settings Saved'); 
            }
            json(0,'Problem In Saving. Please try after again.'); 
        }
        
        else if ($_REQUEST["param"] == "mark_resource") {
                                    
            $user_id = get_current_user_id();
            $userrole = new WP_User($user_id);
            $u_role =  $userrole->roles[0];
            $uid = isset($_POST['uidadmincase'])?intval(($_POST['uidadmincase'])):0; 
            if($uid > 0){
                $user_id = $uid;
            }
            
            $resource_id = isset($_POST["resource_id"])?intval($_POST["resource_id"]):0;
            $status = isset($_POST["resource_id"])?  htmlspecialchars($_POST["status"]):'unmarked';
            $sts = 0;
            if($status == 'unmarked'){
                $sts = 1;
            }
            
            $resource = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT course_id FROM " . resources() . " WHERE id = %d", 
                        $resource_id
                )
            );
            
            if(empty($resource)){
                json(0,'Invalid Exercise');
            }
            
            
            $enroll_id = $wpdb->get_var
            (
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $resource->course_id, $user_id
                    )
            );
            if($enroll_id == '' || empty($enroll_id)){
                json(0,'Invalid Enroll_id');
            }        
            
            $now = date("Y-m-d H:i:s");        
            // insert
            if($sts == 1){
                $res = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . resource_status()  . " (enrollment_id, course_id, resource_id, user_id, created_dt) "
                                    . "VALUES (%d, %d, %d, %d, '%s')", 
                                    $enroll_id,$resource->course_id, $resource_id, $user_id, $now
                        )
                );
                
                json(1,'Exercise marked');
            }
            else{
                // delete
                $res = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . resource_status()  . " WHERE user_id = %d AND resource_id = %d", 
                                    $user_id, $resource_id
                        )
                );
                json(1,'Exercise unmarked');
            }
            
            
        }
        else if ($_REQUEST["param"] == "listenrolled") {
                        
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            
            $usertbl = $wpdb->prefix."users";
            
            $enrolledby = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT e.user_id,e.status,u.display_name,u.user_email FROM " . enrollment() . " e INNER JOIN "
                            . "$usertbl u ON e.user_id = u.ID WHERE course_id = %d", $course_id
                    )
            );            
                        
            json(1,'',$enrolledby);
            
        }
        else if ($_REQUEST["param"] == "listmentorscourses") {
                        
            $ids = isset($_POST['ids'])?  htmlspecialchars($_POST['ids']):0;
            
            $usertbl = $wpdb->prefix."users";
            
            $useres = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT ID,display_name,user_email FROM "
                            . "$usertbl WHERE ID IN(%s)", $ids
                    )
            );            
                        
            json(1,'',$useres);
            
        }
        else if ($_REQUEST["param"] == "add_call") {
            
            
            $callid = isset($_POST['callid'])?intval($_POST['callid']):0;
            
            $iscall = $wpdb->get_row
            (
                    $wpdb->prepare
                            (
                            "SELECT id,is_accepted FROM " . mentorcall() . " WHERE id = %d", $callid
                    )
            );            
            
            $student_user = isset($_POST['student_user'])?intval($_POST['student_user']):0;
            $meetinglink = isset($_POST['meetinglink'])?htmlspecialchars($_POST['meetinglink']):'';
            $course_id = isset($_POST['courseid'])?intval($_POST['courseid']):0;
            $datecall= isset($_POST['datecall'])? htmlspecialchars($_POST['datecall']):date("Y-m-d H:i:s");            
            $datecall = date("Y-m-d H:i:s",  strtotime($datecall));
            
            $mentor_id = isset($_POST['mentorselect'])?intval($_POST['mentorselect']):0;            
            $user_id = $current_user->data->ID;
//            
//            $wpdb->query
//                        (
//                        $wpdb->prepare
//                                (
//                                "UPDATE " . mentorcall()  . " SET status = 'cancelled' WHERE course_id = %d",$course_id                                    
//                        )
//                );
            
            $recur = 0;
            if(isset($_POST['recurcall'])){
                $recur = 1;
            }
            $msd = "Mentor call re-scheduled again.";
            $is_accepted = 0;
            
            $guid = md5(mt_rand(9999, 100099999).time());
            
            if(empty($iscall)){
                $is_accepted = $iscall->is_accepted;
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . mentorcall()  . " (guid, course_id, user_id, link, mentor, mentor_call, mentor_id, created_by, recur_call) "
                                    . "VALUES (%s, %d, %d, %s, %s, '%s', %d, %d, %d)", 
                                    $guid, $course_id, $student_user, $meetinglink, '', $datecall, $mentor_id, $user_id, $recur
                        )
                );
                $msd = "Mentor call scheduled successfully.";
                $callid = $wpdb->insert_id;
            }
            else{                
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . mentorcall()  . " SET guid = %s, course_id = %d, user_id = %d, link = %s, mentor_call = '%s', recur_call = %d "
                                    . " WHERE id = %d", 
                                    $guid, $course_id, $student_user, $meetinglink, $datecall, $recur, $callid
                        )
                );
                $updt = 1;
            }                        
            
            if(isset($_POST['notifyuser'])){
                // Send notification to enrolled users if checked                
                if($mentor_id != $user_id){
                    $current_user = get_user_by("id",$mentor_id);
                }
                notifyenrolleduser($callid,$guid,$current_user,$course_id,$student_user,$meetinglink,$datecall,$updt,$is_accepted);
            }
            
            json(1,$msd);
            
        }
        
        else if ($_REQUEST["param"] == "cancel_call") {
                        
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            $user_id = $current_user->data->ID;   
            $iscall = $wpdb->get_row
            (
                    $wpdb->prepare
                            (
                            "SELECT id,status,mentor_id FROM " . mentorcall() . " WHERE id = %d", $id
                    )
            );  
            if(empty($iscall)){
                json(0,"Invalid Call");
            }
            if($iscall->status != 'active'){
                json(0,"Call is not active already");
            }
            $mentor_id = $iscall->mentor_id;
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . mentorcall()  . " SET status = 'cancelled' WHERE id = %d",                                    
                                    $id
                        )
                );         
                  
            /* email to user, for cancellation */
            
            if($mentor_id != $user_id){
                $current_user = get_user_by("id",$mentor_id);
            }

            notifyuserforcancell($current_user,$id);
            
            /* email to user, for cancellation */
            
            json(1,'Mentor Call Cancelled');
            
        }
        
        else if ($_REQUEST["param"] == "delete_call") {
                        
            $id = isset($_POST['id'])?intval($_POST['id']):0;
               
            $status = $wpdb->get_var
                        (
                        $wpdb->prepare
                                (
                                "SELECT status FROM " . mentorcall()  . " WHERE id = %d",                                    
                                    $id
                        )
                );
            if($status == 'active'){
                json(0,'Mentor call is active. Cancel it to delete.');
            }
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . mentorcall()  . " WHERE id = %d",                                    
                                    $id
                        )
                );         
                        
            json(1,'Mentor Call Deleted');
            
        }        
        else if ($_REQUEST["param"] == "add_projectexcersie") {
            
            $user_id = $current_user->ID;
            $now = date("Y-m-d H:i:s");
            $exid = isset($_POST['exid'])?intval($_POST['exid']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $module_id = isset($_POST['module_id'])?intval($_POST['module_id']):0;
            
            $type = isset($_POST['type'])?htmlspecialchars(trim($_POST['type'])):'';
            if($type == ''){
              json(0,'Something going wrong. please refresh page and try again.');  
            }
            
            if($type != 'module' && $type != 'course'){
                json(0,'Type must be module or course.');  
            }
            
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';
            $hours = isset($_POST['hours'])?htmlspecialchars(trim($_POST['hours'])):'';                        
            $description = isset($_POST['description'])? htmlspecialchars($_REQUEST["description"]):'';            
            $description = stripcslashes($description);            
            
            if($title == ''){
                json(0,'Title is required');
            }
            $status = 0;
            
            if(isset($_POST['isenabled'])){
                $status = 1;                               
            }            
            
            
            if($exid == 0){
                $rs =  $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . project_exercise() . " (type, status, module_id, course_id, title, description, total_hrs, created_by, created_dt, updated_by) "
                                . "VALUES (%s, %d, %d, %d, %s, %s, %s, %d, '%s', %d)", 
                                $type, $status, $module_id, $course_id, $title, $description, $hours, $user_id, $now, $user_id
                        )
                );
                if(isset($_POST['isenabled'])){
                    update_hours($module_id,$course_id,0,$hours);
                } 
                
                
            }
            else{
                
                if($module_id > 0){
                    $projex = $wpdb->get_row(
                        $wpdb->prepare
                                (
                                "SELECT total_hrs,status FROM " . project_exercise() . " WHERE module_id = %d", 
                                $module_id
                        )
                    ); 
                }
                else{
                    
                    $projex = $wpdb->get_row(
                        $wpdb->prepare
                                (
                                "SELECT total_hrs,status FROM " . project_exercise() . " WHERE course_id = %d", 
                                $course_id
                        )
                    );
                }
                
                $projhrs = $projex->total_hrs;
                
                $projsts = $projex->status;
                $rs = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . project_exercise() . " SET status = %d, title = %s, description = %s, total_hrs = %s "
                                . "WHERE id = %d",                                
                                $status, $title, $description, $hours, $exid
                        )
                );
                
                if(isset($_POST['isenabled'])){
                    if($projsts == 0)
                        update_hours($module_id,$course_id,0,$hours);
                    else
                        update_hours($module_id,$course_id,$projhrs,$hours);
                }
                else{
                    if($projsts == 1)
                        update_hours($module_id,$course_id,$projhrs,0);                    
                }
            }            
            
            json(1,'Project Exercise Saved');
            
        }
        else if ($_REQUEST["param"] == "get_exercise") {
                        
            $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
            $type = isset($_REQUEST['type'])?htmlspecialchars($_REQUEST['type']):'';            
            if($type == 'course'){                
                 $detail = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . project_exercise() . " WHERE course_id = %d AND module_id = 0", 
                            $id
                    )
                );
                
            }
            else{
                $detail = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . project_exercise() . " WHERE module_id = %d", 
                            $id
                    )
                );
            }
              
            $exid = 0;
            if(!empty($detail)){
                $exid = $detail->id;
            }
            
            $usertbl = $wpdb->prefix."users";
            $pojects = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT u.user_email,u.display_name,pe.type,pe.title,pe.description,pe.total_hrs,p.links "
                            . " FROM " . projects() . " p INNER JOIN " . project_exercise() . " pe ON p.exercise_id = pe.id INNER JOIN "
                            . "$usertbl u ON p.user_id = u.ID WHERE p.exercise_id = %d", $exid
                    )
            );                         
            
            if(!empty($detail)){
                $desc = html_entity_decode($detail->description);
                $detail->desc = $desc;
            }
            
            $ar = array('info'=>$detail,"projects"=>$pojects);
            
            json(1,'detail',$ar);
            
        }
        else if ($_REQUEST["param"] == "get_submissions") {
                        
            $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;           
            
            $usertbl = $wpdb->prefix."users";
            $pojects = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT u.user_email,u.display_name,pe.type,pe.title,pe.description,pe.total_hrs,p.links "
                            . " FROM " . projects() . " p LEFT JOIN " . project_exercise() . " pe ON p.exercise_id = pe.id INNER JOIN "
                            . "$usertbl u ON p.user_id = u.ID WHERE p.resource_id = %d", $id
                    )
            );                         
            
            $ar = array("projects"=>$pojects);
            
            json(1,'detail',$ar);
            
        }
        else if ($_REQUEST["param"] == "submit_links") {
            
			//print_r($_FILES);
			//die("hello");
			$user_id = get_current_user_id();
			
				$userrole = new WP_User($user_id);
				$u_role =  $userrole->roles[0];
			if($_REQUEST['do']=="noupdate"){
			
				
				if($u_role == 'administrator' || isagencylocation()){
					$uid = isset($_POST['uidadmincase'])?intval(($_POST['uidadmincase'])):0; 
					if($uid > 0){
						$user_id = $uid;
					}
				}
				$now = date("Y-m-d H:i:s");
				$exe_id = isset($_POST['proj'])?intval(trim($_POST['proj'])):0;
				$links = isset($_POST['links'])?htmlspecialchars(trim($_POST['links'])):'';    
				$dattyp = isset($_POST['dattyp'])?htmlspecialchars(trim($_POST['dattyp'])):'';    

				if($links == ''){
					json(0,'Please submit links');
				}
				if($dattyp == 'exercise'){
					$proj_exe = $wpdb->get_var(
						$wpdb->prepare
								(
								"SELECT count(id) as total FROM " . project_exercise() . " WHERE id = %d", 
								$exe_id
						)
					); 
					if($proj_exe == 0)
						json(0,'Invalid Project');

					$wpdb->query(
						$wpdb->prepare
								(
								"DELETE FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", 
								$exe_id,
								$user_id
						)
					);  


					$wpdb->query
							(
							$wpdb->prepare
									(
									"INSERT INTO " . projects()  . " (user_id, exercise_id, links, created_by, created_dt, updated_by) "
										. "VALUES (%d, %d, %s, %d, '%s', %d)", 
										$user_id, $exe_id, $links, $user_id, $now, $user_id
							)
					);
					
					
					
				}
				else{

					$resource_id = $exe_id;
					$resource = $wpdb->get_row(
						$wpdb->prepare
								(
								"SELECT * FROM " . resources() . " WHERE id = %d", 
								$resource_id
						)
					); 
					if(empty($resource))
						json(0,'Invalid Resource');

					$wpdb->query(
						$wpdb->prepare
								(
								"DELETE FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", 
								$resource_id,
								$user_id
						)
					);  


					$wpdb->query
							(
							$wpdb->prepare
									(
									"INSERT INTO " . projects()  . " (user_id, resource_id, links, created_by, created_dt, updated_by) "
										. "VALUES (%d, %d, %s, %d, '%s', %d)", 
										$user_id, $resource_id, $links, $user_id, $now, $user_id
							)
					);

					$enroll_id = $wpdb->get_var
					(
							$wpdb->prepare
									(
									"SELECT id FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", 
									$resource->course_id, $user_id
							)
					);

					$wpdb->query
						   (
						   $wpdb->prepare
								   (
								   "DELETE FROM " . resource_status()  . " WHERE course_id = %d AND resource_id = %d AND user_id = %d",                                   
									   $resource->course_id, $resource_id, $user_id
						   )
				   );

					$wpdb->query
							(
							$wpdb->prepare
									(
									"INSERT INTO " . resource_status()  . " (enrollment_id, course_id, resource_id, user_id, created_dt) "
										. "VALUES (%d, %d, %d, %d, '%s')", 
										$enroll_id,$resource->course_id, $resource_id, $user_id, $now
							)
					);


				}

				$get_prj_details = $wpdb->get_results("select * from ".projects()." order by id desc limit 1");
				//print_r($get_prj_details);
				$last_id='';
				foreach($get_prj_details as $detail){
				   $last_id = $detail->resource_id;
				}
				echo $last_id;
			}else if($_REQUEST['do']=="update"){
			
				/*custom code to take project_id after insert*/
				
				/*Sending Mail to Coach Code - custom*/
				$email_data = $wpdb->get_results("select * from wp_mentor_assign where user_id='".$user_id."'");
				$to_id='';
				 foreach( $email_data as $results ) {
					  $to_id = $results->mentor_id;  
				 }
				 $from_id = $user_id;
				/* ../Code ends */
				
				//print_r($_FILES['responsedoc']);
				
				$names='';
				$file_links = '';
				//$baefilepath = '/assets/files/'.$image['name'];
				//$dir = TR_COUNT_PLUGIN_DIR.$baefilepath; 
				foreach($_FILES as $image){
				   move_uploaded_file($image['tmp_name'],TR_COUNT_PLUGIN_URL."/assets/files/".$image['name']);
				   $names.=TR_COUNT_PLUGIN_URL."/assets/files/".$image['name']." , ";
				   $file_links.="<a href='".TR_COUNT_PLUGIN_URL."/assets/files/".$image['name']."'>".$image['name']."</a>";	
				}
				
				$updated = $wpdb->update(projects(), array("doc_files"=>trim($names,",")), array("resource_id"=>trim($_REQUEST['resourceid'],"0")) );
				if($updated){
					
				    emailtocoachnotify($to_id,$from_id,$links,$file_links);	
				   json(1,'Project Submitted',trim($names,","));
					
				}else{
				   echo "Failed";
				}
			}else{
			   echo "Failed";
			}
			 
        }        
        else if ($_REQUEST["param"] == "get_links") {
            
            $user_id = get_current_user_id();
            $userrole = new WP_User($user_id);
            $u_role =  $userrole->roles[0];
            if($u_role == 'administrator' || isagencylocation()){
                $uid = isset($_POST['uidadmincase'])?intval(($_POST['uidadmincase'])):0; 
                if($uid > 0){
                    $user_id = $uid;
                }
            }
            
            $typ = isset($_POST['typ'])?htmlspecialchars(trim($_POST['typ'])):'exercise'; 
            if($typ == 'resource'){
                $resource_id = isset($_POST['resource_id'])?intval(trim($_POST['resource_id'])):0;
                $proj_links = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT links FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", 
                            $resource_id,
                            $user_id
                    )
                ); 
            }
            else{
                $exe_id = isset($_POST['proj'])?intval(trim($_POST['proj'])):0;
                $proj_links = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT links FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", 
                            $exe_id,
                            $user_id
                    )
                ); 
            }
            
            if(empty($proj_links))
                json(0,'Not Submitted');
            else
                json(1,'Submitted',$proj_links);            
        }
        
        else if ($_REQUEST["param"] == "remove_links") {
            $user_id = get_current_user_id();
            $userrole = new WP_User($user_id);
            $u_role =  $userrole->roles[0];
            if($u_role == 'administrator' || isagencylocation()){
                $uid = isset($_POST['uidadmincase'])?intval(($_POST['uidadmincase'])):0; 
                if($uid > 0){
                    $user_id = $uid;
                }
            }
            $exe_id = isset($_POST['proj'])?intval(trim($_POST['proj'])):0; 
            $typ = isset($_POST['datatyp'])?htmlspecialchars(trim($_POST['datatyp'])):'exercise';
            if($typ == 'resource'){
                
                $resource_id = $exe_id;
                $wpdb->query(
                    $wpdb->prepare
                            (
                            "DELETE FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", 
                            $resource_id,
                            $user_id
                    )
                );
                
                /// also set completed
                
                
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . resource_status()  . " WHERE user_id = %d AND resource_id = %d", 
                                    $user_id, $resource_id
                        )
                );
                
                
                /// also set completed
                
                
            }
            else{
                $wpdb->query(
                    $wpdb->prepare
                            (
                            "DELETE FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", 
                            $exe_id,
                            $user_id
                    )
                );   
            }
            json(1,'Project Links Deleted');            
        }
         else if ($_REQUEST["param"] == "add_video") {
            $typematerial = isset($_REQUEST['typematerial'])?htmlspecialchars($_REQUEST['typematerial']):"lesson";
			$code = isset($_POST['embedcode'])?htmlspecialchars($_POST['embedcode']):0;
            $code = stripslashes($code);
            $user_id = $current_user->ID;   
			 
			if($typematerial == "community_call"){  /*custom code to make add of community calls*/
					$course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;
				    /*####################################*/
				    /*checking video is not */
				    $video = $wpdb->get_row(
						$wpdb->prepare
								(
								"SELECT id FROM " .community_call(). " WHERE course_id = %d AND type= 'video' ", 
								$course_id
						)
					);
				    if(empty($video)){
						$wpdb->query
								(
								$wpdb->prepare
										(
										"INSERT INTO " . community_call()  . " (course_id, type, source, path, created_by) "
											. "VALUES (%d, %s, %s, %s, %d)", 
											$course_id,'video', 'embed', $code, $user_id
								)
						);
						json(1,'Video Added');
					}else{
					    $wpdb->query
								(
								$wpdb->prepare
										(
										"UPDATE " . community_call()  . " SET path = %s WHERE course_id = %d", 
											$code, $course_id
								)
						);
						json(1,'Video Updated');
					}
				
                   /*####################################*/ 
				
            }else{
			 $resource_id = isset($_POST['resource_id'])?intval($_POST['resource_id']):0;
             $lesson_id = isset($_POST['lesson_id'])?intval($_POST['lesson_id']):0;
              
            
            if($typematerial == "lesson"){
                $lesson = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as totaL FROM " . lessons() . " WHERE id = %d ", 
                                $lesson_id
                        )
                    ); 

                if($lesson == 0)
                    json(0,'Invalid Lesson');
            }
            else{
                
                $resource = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as totaL FROM " . resources() . " WHERE id = %d ", 
                                $resource_id
                        )
                    ); 

                if($resource == 0)
                    json(0,'Invalid Exercise');
            }
            $video = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . media() . " WHERE lesson_id = %d AND type= 'video' ", 
                            $lesson_id
                    )
                );
            // insert else update
            if(empty($video)){
                
                if($typematerial == "lesson"){
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . media()  . " (lesson_id, type, source, path, created_by) "
                                        . "VALUES (%d, %s, %s, %s, %d)", 
                                        $lesson_id,'video', 'embed', $code, $user_id
                            )
                    );
                }
                else{
                    $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . media()  . " (resource_id, type, source, path, created_by) "
                                        . "VALUES (%d, %s, %s, %s, %d)", 
                                        $resource_id,'video', 'embed', $code, $user_id
                            )
                    );
                }
                json(1,'Video Added');
            }
            else{
                
                $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . media()  . " SET path = %s WHERE id = %d", 
                                    $code, $video->id
                        )
                );
                json(1,'Video Updated');
            }
			
			} 
			 
           
           
            
        } 
        else if ($_REQUEST["param"] == "save_doc") {
                        
            if(isset($_FILES) && count($_FILES) > 0){
                $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;     
                $typematerial = isset($_REQUEST['typematerial'])?htmlspecialchars($_REQUEST['typematerial']):"lesson";                                
                $now = time();
					$x = 0; $ids = array(); $pos = array(); 
				$file_links='';
				if($typematerial=="community_call"){
					
				    $course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;
					
					/*####################################*/
					
				    $has_docs = $wpdb->get_var(
						$wpdb->prepare
								(
								"SELECT count(*) as total FROM " . community_call() . " WHERE course_id = %d and created_by = %d", 
								$course_id,$user_id
						)
					);
				
				    if($has_docs > 0){
                        $docs = $wpdb->get_results
								(
								$wpdb->prepare
										(
										"SELECT * FROM " . community_call()." WHERE course_id = %d AND created_by = %d ", $course_id,$user_id
								)
						);
						$exist_docs = '';
						foreach($docs as $doc){
						    $exist_docs .= $doc->doc_file_links.",";
						}
						
										   /**/
						    $file_links.= $exist_docs;
						   	
						foreach($_FILES as $image){
						  move_uploaded_file($image['tmp_name'],TR_COUNT_PLUGIN_DIR."/assets/files/".$image['name']);
						   $file_links.=",{".$image['name']."|".TR_COUNT_PLUGIN_URL."/assets/files/".$image['name']."}";
						}
					
						 $wpdb->query
								(
								$wpdb->prepare
										(
										"UPDATE " . community_call()  . " SET doc_file_links = %s WHERE course_id = %d and created_by= %d", 
											$file_links, $course_id,$user_id
								)
						);
						
					   json(1,'Documents Uploaded',$arfinal);
					
					
						
					}else{
					    					   /**/
						
						
						foreach($_FILES as $image){
						   move_uploaded_file($image['tmp_name'],TR_COUNT_PLUGIN_DIR."/assets/files/".$image['name']);
						   $file_links.="{".$image['name']."|".TR_COUNT_PLUGIN_URL."/assets/files/".$image['name']."}";
						}
					
						 $wpdb->query
								(
								$wpdb->prepare
										(
										"UPDATE " . community_call()  . " SET doc_file_links = %s WHERE course_id = %d and created_by= %d", 
											$file_links, $course_id,$user_id
								)
						);
						
						 $wpdb->query
												(
												$wpdb->prepare
														(
														"INSERT INTO " . community_call()  . " (course_id, doc_file_links , created_by,created_dt) "
															. "VALUES (%d, %s, %s, %d)", 
															$course_id,$file_links, $user_id,$now
												)
										);
						
					   json(1,'Documents Uploaded',$arfinal);
							
						   
							

							/**/

						}
						  
					console.log("Failed");
				
                   /*####################################*/ 
				}else{
				
					if($typematerial == 'lesson'){                    
                        $colname = 'lesson_id'; $tbl = lessons();
					}
					else{                   
						$colname = 'resource_id'; $tbl = resources();
					}

					$user_id = $current_user->ID;      

					$res = $wpdb->get_var(
							$wpdb->prepare
									(
									"SELECT count(*) as totaL FROM " . $tbl . " WHERE id = %d ", 
									$id
							)
						); 

					if($res == 0)
						json(0,'Invalid '.$typematerial);

					
					for($flag = 0; $flag < count($_FILES); $flag++){

						if ($_FILES["file-".$flag]["name"] != "") {

								if ($_FILES["file-".$flag]["error"] > 0) {
									continue;
								} else {

								$ext = trim(strtolower(pathinfo($_FILES["file-".$flag]["name"], PATHINFO_EXTENSION)));

								if ($ext == "php" || $ext == "sql" || $ext == "js") {
									continue;
								}

								$othername = $_FILES["file-".$flag]["name"];

								$filenm = str_replace(" ", "_", $_FILES["file-".$flag]["name"]);
								// Path to upload file

								if($typematerial == 'lesson'){
									$filename = $now.'_lesson'.$id.'_'.$filenm;                                
								}
								else{
									$filename = $now.'_resource'.$id.'_'.$filenm;                                
								}


								$baefilepath = '/assets/docs/'.$filename;

								$dir = TR_COUNT_PLUGIN_DIR.$baefilepath;                           

								$result = move_uploaded_file($_FILES["file-".$flag]["tmp_name"], $dir);

								$wpdb->query
										(
										$wpdb->prepare
												(
												"INSERT INTO " . media()  . " ($colname, type, source, path, extra_info, created_by) "
													. "VALUES (%d, %s, %s, %s, %s, %d)", 
													$id,'document', 'upload', $baefilepath, $othername, $user_id
										)
								);
								$lastid = $wpdb->insert_id;
								array_push($ids, $lastid);  
								array_push($pos, $flag);                            
								$x++;

							}

						}
					}
					if($x > 0){
						$arfinal = array('ids' => $ids,'pos' => $pos);
						json(1,'Documents Uploaded',$arfinal);
					}


				}
				json(0,'Failed To Upload');
					
					
				}
				
				
                
            
        }
        else if ($_REQUEST["param"] == "save_doc_titles") {
            
            $ids = isset($_REQUEST['ids'])?htmlspecialchars($_REQUEST['ids']):"";                        
            $ids = array_map('intval', explode(',', $ids));
            
            $pos = isset($_REQUEST['pos'])?htmlspecialchars($_REQUEST['pos']):"";            
            $pos = array_map('intval', explode(',', $pos));
            
            $i = 0;
            if(count($ids) == 0){
                json(0,'Failed to upload..');
            }
            
            foreach($ids as $id){
                
              if(in_array($i, $pos)) {
                    
                  $title = $_POST['doctitles'][$i];
                  if($title != ''){
                    $wpdb->query
                              (
                              $wpdb->prepare
                                      (
                                      "UPDATE " . media()  . " SET extra_info = %s WHERE id = %d", 
                                          $title, $id
                              )
                      );
                  }
                  
              }              
              $i++;  
            }
            if($i > 0){
                json(1,'Document(s) uploaded');
            }
            else{
                json(0,'Failed to upload');
            }
        }
        else if ($_REQUEST["param"] == "delete_doc") {
             
            $doc_id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;     
            
            $doc = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT path FROM " . media() . " WHERE id = %d AND type = 'document'", 
                            $doc_id
                    )
                );
            if(empty($doc)){
                 json(0,'Invalid Document');
            }
            
            $path = TR_COUNT_PLUGIN_DIR."/".$doc->path;
            @unlink($path);
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . media()  . " WHERE id = %d", 
                             $doc_id
                    )
            );
            json(1,'Document Deleted');
            
        }
        else if ($_REQUEST["param"] == "add_note") {
                  
            $typematerial = isset($_REQUEST['typematerial'])?htmlspecialchars($_REQUEST['typematerial']):"lesson";
			$now = date('Y-m-d H:i:s');
			$user_id = $current_user->ID;
			
			if($typematerial == "community_call"){  /*custom code to make add of community calls notes*/
					$course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;
				    $notetxt = isset($_POST['notetxt'])?$_POST['notetxt']:'';
				    $notetxt = html_entity_decode($notetxt);
				    /*####################################*/
				    /*checking notes is not */
				    $has_note = $wpdb->get_var(
						$wpdb->prepare
								(
								"SELECT count(*) as total FROM " . community_call() . " WHERE course_id = %d and created_by = %d", 
								$course_id,$user_id
						)
					);
				
				    if($has_note > 0){
                         $Not = $wpdb->get_results
									(
									$wpdb->prepare
											(
											"SELECT * FROM " . community_call()." WHERE course_id = %d AND created_by = %d ", $course_id,$user_id
									)
							);
							$NotesSaved = '';
							foreach($Not as $n){
								$NotesSaved .= $n->comm_notes."|";
							}
						$wpdb->query
						(
							$wpdb->prepare
								(
								"UPDATE " . community_call()  . " SET comm_notes = %s WHERE course_id = %d and created_by = %d", 
									$NotesSaved."{".$notetxt."}",$course_id,$user_id
								)
						);
						json(1,'Notes Updated');
					}else{
					    $res = $wpdb->query
							(
								$wpdb->prepare
									(
									"INSERT INTO " . community_call()  . " (course_id, comm_notes, created_by,created_dt) "
										. "VALUES (%d,%s,%d, %s)", 
										$course_id, "{".$notetxt."}", $user_id, $now
									)
							);
						json(1,'Notes Added');

						}
						  
					console.log("Failed");
				
                   /*####################################*/ 
				
            }else{
				
				$resource_id = isset($_POST['resourid'])?intval($_POST['resourid']):0; 
				$lesson_id = isset($_POST['lesonid'])?intval($_POST['lesonid']):0;
				$note_id = isset($_POST['noteid'])?intval($_POST['noteid']):0;
                
				if($typematerial == 'lesson'){
					$lesson = $wpdb->get_var(
							$wpdb->prepare
									(
									"SELECT count(*) as total FROM " . lessons() . " WHERE id = %d ", 
									$lesson_id
							)
						); 

					if($lesson == 0)
						json(0,'Invalid Lesson');
				}
				else{
						$resource = $wpdb->get_var(
							$wpdb->prepare
									(
									"SELECT count(*) as total FROM " . resources() . " WHERE id = %d ", 
									$resource_id
							)
						); 

					if($resource == 0)
						json(0,'Invalid Exercise');
				}

				$has_note = $wpdb->get_var(
						$wpdb->prepare
								(
								"SELECT count(*) as total FROM " . lesson_notes() . " WHERE id = %d ", 
								$note_id
						)
					);

				if($has_note > 0){

					$wpdb->query
					(
						$wpdb->prepare
							(
							"UPDATE " . lesson_notes()  . " SET note = %s WHERE id = %d", 
								$notetxt,$note_id
							)
					);
					json(1,'Notes Updated');
				}
				else{

					if($typematerial == 'lesson'){
						$res = $wpdb->query
						(
							$wpdb->prepare
								(
								"INSERT INTO " . lesson_notes()  . " (lesson_id, note, created_by, created_dt, updated_by) "
									. "VALUES (%d, %s, %d, '%s', %d)", 
									$lesson_id, $notetxt, $user_id, $now, $user_id
								)
						);
					}
					else{

						$res = $wpdb->query
						(
							$wpdb->prepare
								(
								"INSERT INTO " . lesson_notes()  . " (resource_id, note, created_by, created_dt, updated_by) "
									. "VALUES (%d, %s, %d, '%s', %d)", 
									$resource_id, $notetxt, $user_id, $now, $user_id
								)
						);

					}
					json(1,'Notes Added');                
				}            
			}
		
       }
        else if ($_REQUEST["param"] == "delete_note") {
                        
            $id = isset($_POST['id'])?intval($_POST['id']):0;  
            $wpdb->query(                    
                    $wpdb->prepare
                            (
                            "DELETE FROM " . lesson_notes()  . " WHERE id = %d",                                    
                                $id
                    )
            );         
                        
            json(1,'Note Deleted');
            
        }
        else if ($_REQUEST["param"] == "delete_hlink") {
                        
            $id = isset($_POST['id'])?intval($_POST['id']):0;
                        
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . media()  . " WHERE id = %d",                                    
                                    $id
                        )
                );         
                        
            json(1,'Link Deleted');
            
        }
        else if ($_REQUEST["param"] == "delete_surveyform") {
                        
            $id = isset($_POST['id'])?intval($_POST['id']):0;
                        
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . survey_results()  . " WHERE survey_id = %d",                                    
                                    $id
                        )
                );         
            
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . survey_forms()  . " WHERE id = %d",                                    
                                    $id
                        )
                );
            
            json(1,'Survey Form Deleted');
            
        }
        else if ($_REQUEST["param"] == "delete_survey") {
                        
            $id = isset($_POST['id'])?intval($_POST['id']):0;
                        
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "DELETE FROM " . survey_results()  . " WHERE id = %d",                                    
                                    $id
                        )
                );         
                       
            json(1,'Survey Deleted');
            
        }
        else if ($_REQUEST["param"] == "save_img") {
                        
            if(isset($_FILES) && count($_FILES) > 0){
                
                $typematerial = isset($_REQUEST['typematerial'])?htmlspecialchars($_REQUEST['typematerial']):"lesson";
                
                $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
                $urlimg = isset($_REQUEST['urlimg'])? htmlspecialchars($_REQUEST['urlimg']):"";
                
                if($typematerial == "lesson"){
                    $tbl = lessons(); $txt = "Lesson"; $col = "lesson_id";
                }
                else{
                    $tbl = resources();  $txt = "Resource"; $col = "resource_id";
                }
                
                $user_id = $current_user->ID;      

                $res = $wpdb->get_var(
                        $wpdb->prepare
                                (
                                "SELECT count(*) as totaL FROM " . $tbl . " WHERE id = %d ", 
                                $id
                        )
                    );                 
                if($res == 0)
                    json(0,'Invalid '.$txt);
                
                $now = time();
                
                $x = 0;
                for($flag = 0; $flag < count($_FILES); $flag++){
                    
                    if ($_FILES["file-".$flag]["name"] != "") {

                            if ($_FILES["file-".$flag]["error"] > 0) {
                                json(0, $_FILES["file-".$flag]["error"]);
                            } else {

                            $ext = trim(strtolower(pathinfo($_FILES["file-".$flag]["name"], PATHINFO_EXTENSION)));

                            if ($ext == "php" || $ext == "sql" || $ext == "js") {
                                json(0, 'You are not allowed to add '.$ext.' files');
                            }
                            
                            $filenm = str_replace(" ", "_", $_FILES["file-".$flag]["name"]);
                            $filename = $now.'_'.$typematerial.'_image_'.$id.'_'.$filenm;

                            // Path to upload file
                            
                            $baefilepath = '/assets/docs/'.$filename;
                            
                            $dir = TR_COUNT_PLUGIN_DIR.$baefilepath;                           
                            
                            $result = move_uploaded_file($_FILES["file-".$flag]["tmp_name"], $dir);

                            $wpdb->query
                                    (
                                    $wpdb->prepare
                                            (
                                            "INSERT INTO " . media()  . " ($col, type, source, path, extra_info, created_by) "
                                                . "VALUES (%d, %s, %s, %s, %s, %d)", 
                                                $id,'image', 'upload', $baefilepath, $urlimg, $user_id
                                    )
                            );
                            $x++;
                            
                        }

                    }
                }
                if($x > 0){
                    json(1,'Video & Image Saved');
                }
                
                
            }
            json(0,'Failed To Upload Image');
            
        }
        else if ($_REQUEST["param"] == "save_urlimg") {
                        
            $imageid = isset($_REQUEST['imageid'])?intval($_REQUEST['imageid']):0;
            $urlimg = isset($_REQUEST['urlimg'])? htmlspecialchars($_REQUEST['urlimg']):"";
                        
            $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "UPDATE " . media()  . " SET extra_info = %s WHERE id = %d",                                    
                                $urlimg, 
                                $imageid
                        )
                );         
                        
            json(1,'Video & Image Url Saved');
            
        }
        else if ($_REQUEST["param"] == "add_hlink") {
                  
            $typematerial = isset($_REQUEST['typematerial'])?htmlspecialchars($_REQUEST['typematerial']):"lesson";
            $resource_id = isset($_POST['resid'])?intval($_POST['resid']):0;            
            $lesson_id = isset($_POST['lessid'])?intval($_POST['lessid']):0;                        
            $helpnk_id = isset($_POST['helpnkid'])?intval($_POST['helpnkid']):0;
            
			if($typematerial == "community_call"){ 
			   $course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;
			   $linktitle = isset($_POST['linktitle'])?htmlspecialchars($_POST['linktitle']):'';
			   $linkurl = isset($_POST['linkurl'])?htmlspecialchars($_POST['linkurl']):'';
				
			/*####################################*/
				    /*checking notes is not */
				    $has_links = $wpdb->get_var(
						$wpdb->prepare
								(
								"SELECT count(*) as total FROM " . community_call() . " WHERE course_id = %d and created_by = %d", 
								$course_id,$user_id
						)
					);
				
				    if($has_links > 0){
                        $helplinks = $wpdb->get_results
								(
								$wpdb->prepare
										(
										"SELECT * FROM " . community_call()." WHERE course_id = %d AND created_by = %d ", $course_id,$user_id
								)
						);
						$linksExists = '';
						foreach($helplinks as $link){
						    $linksExists .= $link->comm_hlp_links.",";
						}
						
						
						$wpdb->query
						(
							$wpdb->prepare
								(
								"UPDATE " . community_call()  . " SET comm_hlp_links = %s WHERE course_id = %d and created_by = %d", 
									$linksExists."(".$linktitle."|".$linkurl.")",$course_id,$user_id
								)
						);
						json(1,'Help links Updated');
					}else{
					    $res = $wpdb->query
							(
								$wpdb->prepare
									(
									"INSERT INTO " . community_call()  . " (course_id, comm_hlp_links, created_by,created_dt) "
										. "VALUES (%d,%s, %d)", 
										$course_id, "(".$linktitle."|".$linkurl.")", $user_id, $now
									)
							);
						json(1,'Help links Added');

						}
						  
					console.log("Failed");
				
                   /*####################################*/ 	
			}else{
			
				    $user_id = $current_user->ID;      
					$now = date('Y-m-d H:i:s');

					if($typematerial == 'lesson'){
						$lesson = $wpdb->get_var(
								$wpdb->prepare
										(
										"SELECT count(*) as total FROM " . lessons() . " WHERE id = %d ", 
										$lesson_id
								)
							); 

						if($lesson == 0)
							json(0,'Invalid Lesson');

					}
					else{
						$resource = $wpdb->get_var(
								$wpdb->prepare
										(
										"SELECT count(*) as total FROM " . resources() . " WHERE id = %d ", 
										$resource_id
								)
							); 

						if($resource == 0)
							json(0,'Invalid Exercise');
					}

					$has_lnk = $wpdb->get_var(
							$wpdb->prepare
									(
									"SELECT count(*) as total FROM " . media() . " WHERE id = %d ", 
									$helpnk_id
							)
						);

					$linktitle = isset($_POST['linktitle'])?htmlspecialchars($_POST['linktitle']):'';
					$linkurl = isset($_POST['linkurl'])?htmlspecialchars($_POST['linkurl']):'';


					if($has_lnk > 0){

						$wpdb->query
						(
							$wpdb->prepare
								(
								"UPDATE " . media()  . " SET path = %s, extra_info = %s WHERE id = %d", 
									$linkurl,$linktitle,$helpnk_id
								)
						);
						json(1,'Link Updated');
					}
					else{

						if($typematerial == 'lesson'){
							$wpdb->query
									(
									$wpdb->prepare
											(
											"INSERT INTO " . media()  . " (lesson_id, type, source, path, extra_info, created_by) "
												. "VALUES (%d, %s, %s, %s, %s, %d)", 
												$lesson_id,'link', 'upload', $linkurl, $linktitle, $user_id
									)
							);
						}
						else{
							$wpdb->query
									(
									$wpdb->prepare
											(
											"INSERT INTO " . media()  . " (resource_id, type, source, path, extra_info, created_by) "
												. "VALUES (%d, %s, %s, %s, %s, %d)", 
												$resource_id,'link', 'upload', $linkurl, $linktitle, $user_id
									)
							);
						}
						json(1,'Link Added');

					}                
				
			}
			
                       
            
            
        }
        else if ($_REQUEST["param"] == "get_rows") {
            
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            $type = isset($_POST['type'])?htmlspecialchars($_POST['type']):"resources";
            $tbl = resources(); $col = 'lesson_id';
            if($type == 'modules'){
                $tbl = modules(); $col = 'course_id';
            }
            else if($type == 'lessons'){
                $tbl = lessons(); $col = 'module_id';             
            }
            
            if($type == 'courses'){
                $rows = $wpdb->get_results(
                        $wpdb->prepare
                                (
                                "SELECT id,title,ord FROM " . courses() . " ORDER BY ord ASC", 
                                ""
                        )
                    );
            }
            else{
                $rows = $wpdb->get_results(
                        $wpdb->prepare
                                (
                                "SELECT id,title,ord FROM " . $tbl . " WHERE $col = %d ORDER BY ord ASC", 
                                $id
                        )
                    );
            }
            if(!empty($rows))
                json(1,'Rows Found',$rows);
            else
                json(0,'No Row Found');
        }
        
         else if ($_REQUEST["param"] == "get_moverows") {
            
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            //$type = isset($_POST['type'])?htmlspecialchars($_POST['type']):"lessons";
            $type = "lessons";
            $tbl = resources(); $col = 'lesson_id';
            if($type == 'modules'){
                $tbl = modules(); $col = 'course_id';
            }
            else if($type == 'lessons'){
                $tbl = lessons(); $col = 'module_id';             
            }
            
            $rows = $wpdb->get_results(
                $wpdb->prepare
                        (
                        "SELECT id,title,ord,module_id FROM " . $tbl . " WHERE $col = %d ORDER BY ord ASC", 
                        $id
                )
            );           
            
            if(!empty($rows)){                                 
                $module_id = $rows[0]->module_id;
                
                $rows_modules = $wpdb->get_results(
                    $wpdb->prepare
                            (
                            "SELECT id,title,ord FROM " . modules() . " WHERE course_id = (SELECT course_id FROM " . modules() . " WHERE id = %d)", 
                            $module_id
                    )
                );  
                
                $ars = array("rows"=>$rows,"rows_modules"=>$rows_modules);
                json(1,'Rows Found',$ars);
            }
            else
                json(0,'No Row Found');
        }
        else if ($_REQUEST["param"] == "save_rows") {
            
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            $type = isset($_POST['type'])?htmlspecialchars($_POST['type']):"resources";
            $rows = isset($_POST['armult'])?($_POST['armult']):"";
            $rows = explode(",", $rows);
            $tbl = resources();
            if($type == 'modules'){
                $tbl = modules();
            }
            else if($type == 'lessons'){
                $tbl = lessons();
            }
            else if($type == 'courses'){
                $tbl = courses();
            }
            $i = 1;
            
            foreach($rows as $row){
              $wpdb->query(
                    $wpdb->prepare
                            (
                            "UPDATE " . $tbl . " SET ord = %d WHERE id = %d", 
                            $i,$row
                    )
                );
              $i++;
            }            
            
            if($i > 1)
                json(1,'Order Updated');
            else
                json(0,'Order Not Updated');
        }
        else if ($_REQUEST["param"] == "move_rows") {
            
            $id = isset($_POST['modid'])?intval($_POST['modid']):0;            
            $rows = isset($_POST['armult'])?($_POST['armult']):"";
            $rows = explode(",", $rows);
            $i = 1;
            foreach($rows as $row){
              $wpdb->query(
                    $wpdb->prepare
                            (
                            "UPDATE " . lessons() . " SET module_id = %d WHERE id = %d", 
                            $id,$row
                    )
                );
              $i++;
            }            
            
            if($i > 1)
                json(1,'Selected Lesson(s) Moved ');
            else
                json(1,'Lesson(s) Not Moved ');
        }
        
        else if ($_REQUEST["param"] == "save_courseimg") {
                
            $course_id = isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;     
            $urlimg = isset($_REQUEST['urlimg'])?htmlspecialchars($_REQUEST['urlimg']):"";
            $now = time();
            $total = $wpdb->get_var(
                    $wpdb->prepare
                            (
                            "SELECT count(id) FROM " . courses() . " WHERE id = %d", 
                            $course_id
                    )
                );
            if($total == 0){
                json(0,'Invalid Course',$total);
            }
                
            if(isset($_FILES) && count($_FILES) > 0){
                
                $user_id = $current_user->ID;      

                $x = 0;
                for($flag = 0; $flag < 1; $flag++){
                    
                    if ($_FILES["file-".$flag]["name"] != "") {

                            if ($_FILES["file-".$flag]["error"] > 0) {
                                json(0, $_FILES["file-".$flag]["error"]);                                
                            } else {

                            $ext = trim(strtolower(pathinfo($_FILES["file-".$flag]["name"], PATHINFO_EXTENSION)));

                            if ($ext == "php" || $ext == "sql" || $ext == "js") {
                                json(0, 'You are not allowed to add '.$ext.' files');
                            }
                            
                            $othername = $_FILES["file-".$flag]["name"];
                            
                            $filenm = str_replace(" ", "_", $_FILES["file-".$flag]["name"]);
                            $filename = "course_".$course_id.'.'.$ext;
                            
                            $baefilepath = '/assets/docs/'.$filename;
                            
                            $dir = TR_COUNT_PLUGIN_DIR.$baefilepath;
                            $fullpath = TR_COUNT_PLUGIN_URL.$baefilepath;
                            
                            $result = move_uploaded_file($_FILES["file-".$flag]["tmp_name"], $dir);
                            
                            $wpdb->query
                                    (
                                    $wpdb->prepare
                                            (
                                            "UPDATE " . courses()  . " SET imgpath = %s, link = %s WHERE id = %d", 
                                                $baefilepath, $urlimg, $course_id
                                    )
                            );
                            
                            $tag = "<a target='_blank' href='$urlimg'><img src='$fullpath' /></a>";
                            $ar = array("tag"=>$tag,"link"=>$urlimg,"fullpath"=>$fullpath);
                            json(1,'Image Uploaded',$ar);
                        }

                    }
                }                
                
            }
            else{
                
                if($urlimg != ''){
                    $wpdb->query
                                    (
                                    $wpdb->prepare
                                            (
                                            "UPDATE " . courses()  . " SET link = %s WHERE id = %d", 
                                                $urlimg, $course_id
                                    )
                            );
                    $ar = array("link"=>$urlimg);
                    json(1,'Link Saved',$ar);
                }
                
            }
            json(0,'Failed To Upload');
            
        }
        else if ($_REQUEST["param"] == "markattendence") {
            
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            
            $mentorcall = $wpdb->get_row
            (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . mentorcall() . " WHERE id = %d", $id
                    )
            ); 
            
            if(empty($mentorcall)){
                json(0,'Invalid Record');
            }
            
            $val = isset($_POST['val'])?htmlspecialchars($_POST['val']):"yes";
            
            $wpdb->query(
                $wpdb->prepare
                        (
                        "UPDATE " . mentorcall() . " SET is_attended = %s WHERE id = %d", 
                        $val,$id
                )
            );
                                   
            json(1,'Attendance marked as <strong>'.$val.'</strong>',$eq);
        }
        else if ($_REQUEST["param"] == "assignmentor") {
            
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;           
            $uid = isset($_POST['uid'])?intval($_POST['uid']):0;
            $isdel = isset($_POST['isdel'])?intval($_POST['isdel']):0;
            $mentor = isset($_POST['mentor'])?intval($_POST['mentor']):0;
            if($uid == 0){
                json(0,'Invalid User');
            }
            
            $id = $wpdb->get_var
            (
                $wpdb->prepare
                        (
                        "SELECT id FROM " . mentor_assign()." "
                        . "WHERE course_id = %d AND user_id = %d",
                        $course_id,
                        $uid
                )
            );
            if($id > 0){
                
               $wpdb->query
                    (
                            $wpdb->prepare
                            (
                                "UPDATE " . mentor_assign()  . " SET mentor_id = %d WHERE id = %d",$mentor, $id
                            )
                    );                 
               json(1,'Mentor successfully changed',$mentor); 
                
            }
            else{
                $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . mentor_assign()  . " (course_id, user_id, mentor_id) "
                                        . "VALUES (%d, %d, %d)", 
                                        $course_id, $uid, $mentor
                            )
                    );
                $id = $wpdb->insert_id;
                json(1,'Mentor successfully assigned',$mentor);  
            }
            /* mentor assigned email if send here */                        
            
        }
        else if ($_REQUEST["param"] == "assignmentormultiple") {
            $ar = isset($_POST['ar'])?htmlspecialchars($_POST['ar']):0;    
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0; 
            $mentor = isset($_POST['mentor'])?intval($_POST['mentor']):0;
            if($mentor == 0){
                json(0,'Invalid Mentor');
            }
            
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "DELETE FROM " . mentor_assign()  . " WHERE user_id IN($ar) AND course_id = %d",$course_id
                    )
            );
            
            $arr = explode(",", $ar);
            foreach($arr as $uid){
               $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "INSERT INTO " . mentor_assign()  . " (course_id, user_id, mentor_id) "
                                        . "VALUES (%d, %d, %d)", 
                                        $course_id, $uid, $mentor
                            )
                    ); 
            }
            json(1,'Mentor successfully assigned to selected users');     
            
        }
        else if ($_REQUEST["param"] == "get_mentor_users") {
            global $wpdb;
            $mentor = isset($_POST['mentor'])?intval($_POST['mentor']):0;
            $course_id = isset($_POST['course_id'])?intval($_POST['course_id']):0;
            $usertbl = $wpdb->prefix."users";
            $students = $wpdb->get_results
            (
                $wpdb->prepare
                        (
                        "SELECT s.ID,s.display_name "
                        . "FROM " . mentor_assign()." map INNER JOIN " . $usertbl ." s ON map.user_id = s.ID "
                        . "WHERE map.mentor_id = %d AND map.course_id = %d ORDER BY s.user_registered DESC",$mentor,$course_id
                )
            );
                        
            json(1,'users',$students);
        }   
        else if ($_REQUEST["param"] == "saveform") {
            global $wpdb;
            $mentor_id = isset($_POST['mentor_id'])?intval($_POST['mentor_id']):0;
            $formtitle = isset($_POST['formtitle'])?htmlspecialchars($_POST['formtitle']):"Dummy Form";  
            $c_id = get_current_user_id();
            $date = date("Y-m-d H:i:s");
            $wpdb->query
                (
                $wpdb->prepare
                        (
                        "INSERT INTO " . survey_forms()  . " (mentor_id, title, data, created_by, created_dt) "
                            . "VALUES (%d, %s, %s, %d, '%s')", 
                            $mentor_id, $formtitle, '', $c_id, $date
                )
            );
                 
            $form_id = $wpdb->insert_id;
            json(1,'form saved',$form_id);
        }
        else if ($_REQUEST["param"] == "updateform") {
            global $wpdb;
            $form_id = isset($_POST['form_id'])?intval($_POST['form_id']):0;
            $form_data = isset($_POST['form_data'])? stripslashes($_POST['form_data']):'';
            $formtitle = isset($_POST['formtitle'])?htmlspecialchars($_POST['formtitle']):"Dummy Form";  
            
            $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . survey_forms()  . " SET title = %s, data = %s WHERE id = %d", 
                            $formtitle, $form_data, $form_id
                )
            );
                               
            $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . survey_results()  . " SET data = %s WHERE survey_id = %d AND is_submitted = 0", 
                            $form_data, $form_id
                )
            );            
            
            json(1,'form updated',$form_data);
        }
        else if ($_REQUEST["param"] == "survey_send") {
			
			/*custom*/
            global $wpdb;
            $form_id = isset($_POST['formid'])?intval($_POST['formid']):0;  
			$mentor_id='';
			/*getting custom values*/
			$utype=$_REQUEST['type'];
			$utypeid=$_REQUEST['typeid'];
			/* ../ends */
			
			if($utype=="Mentor"){ $mentor_id=$utypeid;$mentor = get_user_by('id',$mentor_id); }
			if($utype=="Course"){ 
				$course_id=$mentor_id=$utypeid; 
				$course = $wpdb->get_results("SELECT * FROM wp_courses where id = '".$course_id."'");
				foreach($course as $detail){
				   $c_title = $detail->title;
				   $mentor = $c_title;
				}
			}
			
            $form = $wpdb->get_row
                (
                   /* $wpdb->prepare
                            (
                            "SELECT id,title,mentor_id,data FROM " . survey_forms()." "
                            . "WHERE id = %d",$form_id
                    )*/
				   $wpdb->prepare
                            (
                            "SELECT id,title,data FROM " . survey_forms()." "
                            . "WHERE id = %d",$form_id
                    )
                );
            if(empty($form)){
                json(0,'Invalid Form ID');
            }
            
            //$mentor_id = $form->mentor_id;
            $data = $form->data;
            $c_id = get_current_user_id();
            $date = date("Y-m-d H:i:s");
            
            $users = isset($_POST['users'])?htmlspecialchars($_POST['users']):"";  
            $users = explode(",", $users);
            $emails = "";
            
            //$mentor_id = $form->mentor_id;
            //$mentor = get_user_by('id',$mentor_id);
            $guid = md5(mt_rand(9999, 9999999).time());
            
            foreach($users as $user){                                                
                $userinfo = get_user_by("id",$user);
                if(!empty($userinfo)){
                    
                    $wpdb->query
                    (
                        $wpdb->prepare
                                (
                                "INSERT INTO " . survey_results()  . " (guid, survey_id, mentor_id, user_id, data, created_by, created_dt) "
                                    . "VALUES (%s, %d, %d, %d, %s, %d, '%s')", 
                                    $guid, $form_id, $mentor_id, $user, $data, $c_id, $date
                        )
                    );
                    $insert_id = $wpdb->insert_id;
					if($utype=="Mentor"){ /*custom*/
					   emailforsurvey($guid,$insert_id,$userinfo->data->user_email,$userinfo->data->display_name,$form,$mentor);  
					}else if($utype=="Course"){
					   emailforsurveyCourse($guid,$insert_id,$userinfo->data->user_email,$userinfo->data->display_name,$form,$mentor);  
					}
                       
                }
            }            
            
            json(1,'Survey successfully sent',$emails);
        }
        else if ($_REQUEST["param"] == "saveformresult") {
            
            $survey_id = isset($_POST['survey_id'])?intval($_POST['survey_id']):0;
            $c_id = get_current_user_id();
            $survey = $wpdb->get_row
            (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . survey_results() . " WHERE id = %d", $survey_id
                    )
            ); 
            
            if($survey->is_submitted == 1){
                json(0,'You have already submitted this survey.');
            }
            
            if(empty($survey)){
                json(0,'Invalid Survey');
            }
            if($survey->user_id != $c_id){
                json(0,'Invalid Survey');
            }
            
            $data = json_decode($survey->data);
            $data = $data->fields;
            $values = isset($_POST['values'])?stripslashes(($_POST['values'])):"";
            $values = json_decode($values);
            $i = 1;
            foreach ($data as $d){
                $d->id = $i;
                $d->form_id = $form_id;  
                $i++;
            }
            $i = 1;
            foreach ($data as $newd){
                
                if($newd->field_type == "text" || $newd->field_type == "paragraph" || $newd->field_type == "number" || 
                        $newd->field_type == "website" || $newd->field_type == "email"){
                    if(isset($values->$i) && $values->$i != "undefined"){
                        $val = trim($values->$i);
                        $newd->value = esc_attr($val);
                    }  
                        
                }
                else if($newd->field_type == "radio" || $newd->field_type == "dropdown"){
                    
                    $options = $newd->field_options->options;
                    foreach($options as $option){
                        if(isset($values->$i) && $option->label == $values->$i){
                            $option->checked = true;
                        }
                        else{
                            $option->checked = false;
                        }
                    }
                            
                }
                else if($newd->field_type == "checkboxes"){
                                    
                    $options = $newd->field_options->options;
                    $j = 0;
                    foreach($options as $option){
                        if(isset($values->$i->$j) && $values->$i->$j == 'on' ){
                            $option->checked = true;
                        }
                        else{
                            $option->checked = false;
                        }
                            
                        $j++;
                    }
                    
                }    
                else if($newd->field_type == "address"){
                    
                    $newd->value->city = isset($values->$i->city)?$values->$i->city:'';
                    $newd->value->country = isset($values->$i->country)?$values->$i->country:'';
                    $newd->value->state = isset($values->$i->state)?$values->$i->state:'';
                    $newd->value->street = isset($values->$i->street)?$values->$i->street:'';
                    $newd->value->zipcode = isset($values->$i->zipcode)?$values->$i->zipcode:'';
                    
                }
                else if($newd->field_type == "price"){
                    $newd->value->cents = isset($values->$i->cents)?$values->$i->cents:'';
                    $newd->value->dollars = isset($values->$i->dollars)?$values->$i->dollars:'';                    
                }
                else if($newd->field_type == "time"){
                    $newd->value->am_pm = isset($values->$i->am_pm)?$values->$i->am_pm:'';
                    $newd->value->hours = isset($values->$i->hours)?$values->$i->hours:'';
                    $newd->value->minutes = isset($values->$i->hours)?$values->$i->minutes:'';
                    $newd->value->seconds = isset($values->$i->hours)?$values->$i->seconds:'';
                }               
                else if($newd->field_type == "date"){
                    $newd->value->day = isset($values->$i->day)?$values->$i->day:'';
                    $newd->value->month = isset($values->$i->month)?$values->$i->month:'';
                    $newd->value->year = isset($values->$i->year)?$values->$i->year:'';
                }
                
                $i++;
            }            
            $tosave = new stdClass();
            $tosave->fields = $data;
            
            $tosave = json_encode($tosave);            
            $tosave = trim(str_replace('\n', '<br/>', $tosave));
            
            $wpdb->query
            (
                    $wpdb->prepare
                            (
                            "UPDATE " . survey_results() . " SET data = %s, is_submitted = 1 WHERE id = %d", $tosave, $survey_id
                    )
            ); 
            emailforsurveyresult($survey);
            //print_r($values); die;
            json(1,'Thanks!!.. Survey Has Been Submitted Successfully.. Please wait for redirection...');
        }   
        else if ($_REQUEST["param"] == "update__template") {
                        
            $template_id = isset($_POST['template_id'])?intval($_POST['template_id']):0;
                        
            $template = $wpdb->get_row
            (
                $wpdb->prepare
                (
                        "SELECT * FROM " . email_templates()." WHERE id = %d ", $template_id
                )
            );        
            
            if(empty($template)){
                json(0,'Invalid Template ID');
            }
            
            $sub = isset($_POST['sub'])?esc_attr($_POST['sub']):"";
            $content = isset($_POST['content'])?html_entity_decode(urldecode($_POST['content'])):"";            
            $content = stripcslashes($content);
            if(trim($sub) == '' || trim($content) == ''){
                json(0,'Both Subject and Email content are required');
            }            
            
            $wpdb->query
            (
                    $wpdb->prepare
                            (
                            "UPDATE " . email_templates() . " SET subject = %s, content = %s WHERE id = %d", 
                            $sub, $content, $template_id
                    )
            ); 
            
            json(1,'Email template updated successfully.');
            
        }
        
    }
    

function emailforsurvey($guid,$id,$email,$name,$form,$mentor){
        
    global $wpdb;
    
    $slug = PAGE_SLUG; 
    $btn_url = site_url()."/$slug?survey=".$id."&guid=".$guid;    
    
    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email');
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    $template = tt_get_template("survey_send");            
    $subj = $template->subject;     
    $subj = str_replace("{{mentor_name}}", $mentor->data->display_name, $subj);

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{mentor_name}}','{{url}}','{{site_name}}'),
            array($name,$mentor->data->display_name,$btn_url,$site_name), $msg);
    
    custom_mail($email,$subj,$msg,EMAIL_TYPE,"");    
    
}

/*
  Email Survey for Course - custom
*/
function emailforsurveyCourse($guid,$id,$email,$name,$form,$mentor){
        
    global $wpdb;
    
    $slug = PAGE_SLUG; 
    $btn_url = site_url()."/$slug?survey=".$id."&guid=".$guid;    
    
    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    $template = tt_get_template("survey_send_course");            
    $subj = $template->subject;     
    $subj = str_replace("{{course_name}}", $mentor, $subj);

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{course_name}}','{{url}}','{{site_name}}'),
            array($name,$mentor,$btn_url,$site_name), $msg);
    
    custom_mail($email,$subj,$msg,EMAIL_TYPE,"");    
    
}

/* ../ custom code ends */

/*custom code to send email notification to coach*/
function emailtocoachnotify($to_id,$from_id,$links,$files){
        
    global $wpdb;
	/*coach*/
    $coach_details = get_userdata($to_id);
	$coach_name=$coach_details->first_name." ".$coach_details->last_name;/*coach_name*/
	$coach_email = $coach_details->user_email;  /*coach email*/
	/*student*/
	$student_details = get_userdata($from_id);
	$student_name=$student_details->first_name." ".$student_details->last_name;/*student_name*/
	$student_email = $student_details->user_email;  /*student email*/
    
    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;    
    $headers = 'From: ' . $student_email . "\r\n" .
                'Reply-To: ' . $student_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    $template = tt_get_template("project_submission");            
    $subj = $template->subject;     
    $subj = str_replace("{{mentor_name}}", $coach_name, $subj);

    $msg = $template->content; 
    $msg = str_replace(array('{{student_name}}','{{mentor_name}}','{{links}}','{{site_name}}','{{files}}'),
            array($student_name,$coach_name,$links,$site_name,$files), $msg);
    
    custom_mail($coach_email,$subj,$msg,EMAIL_TYPE,"");     
    
}
/* ../coach email ends*/

function emailforsurveyresult($survey){
    
    global $wpdb;
    $id = $survey->id;
    $user_id = $survey->user_id;
    $user = get_user_by('id', $user_id);
    
    $mentor_id = $survey->mentor_id;
    $mentor = get_user_by('id', $mentor_id);
    
    
    $form = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT * FROM " . survey_forms()." "
                . "WHERE id = %d",$survey->survey_id
        )
    );
    
    $slug = PAGE_SLUG; 
    $url = site_url()."/$slug?survey=".$id."&guid=".$survey->guid;
    $btnaccept = "<a href='".$url."'>Click Here To Check Your Survey</a> <br/><br/>";
    
    $date = date("Y-m-d H:i:s");
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();            
    
    /* for user */
        
    $template = tt_get_template("survey_result_user");            
    $subj = $template->subject;     

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{survey_title}}','{{mentor_name}}','{{url}}','{{site_name}}'),
            array($user->data->display_name,$form->title,$mentor->data->display_nam,$url,$site_name), $msg);
    
    custom_mail($user->data->user_email,$subj,$msg,EMAIL_TYPE,"");
    
    
    /* for mentor */
    
    $url = site_url()."/wp-admin/admin.php?page=survey_result&survey_id=".$id;
       
    $template = tt_get_template("survey_result_mentor");            
    $subj = $template->subject;     

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{survey_user}}','{{survey_title}}','{{url}}','{{site_name}}'),
            array($mentor->data->display_name,$user->data->display_name,$form->title,$url,$site_name), $msg);
    
    custom_mail($mentor->data->user_email,$subj,$msg,EMAIL_TYPE,"");
}

function updatlesson($lesson_id){
    
    global $wpdb;
    $total_lesshrs = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT sum(total_hrs) as totalhrs FROM " . resources()." WHERE lesson_id = %d", $lesson_id
            )
        );

    $total_lessresource = $wpdb->get_var
    (
        $wpdb->prepare
        (
            "SELECT count(id) as totalresource FROM " . resources()." WHERE lesson_id = %d", $lesson_id
        )
    );
    $wpdb->query
                (
                $wpdb->prepare
                        (
                        "UPDATE " . lessons() . " SET total_hrs = %s, total_resources = %d WHERE id = %d", 
                        $total_lesshrs, $total_lessresource, $lesson_id
                )
        );
}

function updatehours_and_resources($module_id,$course_id,$lesson_id){
    global $wpdb;
    
    if($lesson_id > 0){
        updatlesson($lesson_id);    
    }
    else if($lesson_id == 0){
        
        if($module_id > 0){
            $lessons = $wpdb->get_results
                    (
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . lessons()." WHERE module_id = %d", $module_id
                    )
            );
            
            foreach($lessons as $lesson){
                updatlesson($lesson->id);  
            }
            
        }
        
    }
   
    if($module_id > 0){        
        
        $total_modulehrs = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT sum(total_hrs) as totalhrs FROM " . lessons()." WHERE module_id = %d", $module_id
            )
        );
        
        $hrm = updatehrsfrominner('module_id',$module_id);
        $total_modulehrs = $total_modulehrs + $hrm;
        
        $total_moduleresource = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT sum(total_resources) as totalresource FROM " . lessons()." WHERE module_id = %d", $module_id
            )
        );                
        
        $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . modules() . " SET total_hrs = %s, total_resources = %d WHERE id = %d", 
                            $total_modulehrs, $total_moduleresource, $module_id
                    )
            );
    
    }
    
    if($course_id > 0){
                  
       
        $total_coursehrs = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT sum(total_hrs) as totalhrs FROM " . modules()." WHERE course_id = %d", $course_id
            )
        );        
        
        $hr = updatehrsfrominner('course_id',$course_id);
        $total_coursehrs = $total_coursehrs + $hr;
        
        $total_courseresource = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT sum(total_resources) as totalresource FROM " . modules()." WHERE course_id = %d", $course_id
            )
        );
        
        $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . courses() . " SET total_hrs = %s, total_resources = %d WHERE id = %d", 
                            $total_coursehrs, $total_courseresource, $course_id
                    )
            );
    }
}

function json($sts,$msg,$arr = array()){
    $ar = array('sts'=>$sts,'msg'=>$msg,'arr'=>$arr);
    print_r(json_encode($ar));
    die;
}


function notifyenrolleduser($callid,$guid,$current_user,$course_id,$student_user,$meetinglink,$date,$updt,$is_accepted){
    
    global $wpdb;
    $course = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT * FROM " . courses() . " WHERE id = %s", $course_id
        )
    );
    
    $usertbl = $wpdb->prefix."users";    
    $enrolledby = get_user_by('id', $student_user);
    
    $txt = "schedule";    
    if($updt == 1){
        $txt = "re-schedule";        
    }
    $slug = PAGE_SLUG;
    $url = site_url()."/$slug?accept_call=$callid&guid=$guid";
    $btnaccept = "<a href='".$url."'>Click Here To Accept Invitation And Notify Your Mentor</a> <br/><br/>";
    if($is_accepted == 1){
        $btnaccept = '';
    }
    
    $emails = $enrolledby->data->user_email;    
    
    $date = date("D d M Y, h:i a",  strtotime($date));
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();                
    
    $template = tt_get_template("mentor_call");            
    $subj = $template->subject; 
    $subj = str_replace("{{course_title}}", $course->title, $subj);

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{mentor_name}}','{{course_title}}','{{call_date}}','{{url}}','{{meeting_link}}','{{scehulde_or_reschedule}}','{{site_name}}'),
            array($uuser->display_name,$current_user->data->display_name,$course->title,$date,$url,$meetinglink,$txt,$site_name), $msg);
        
    custom_mail($emails,$subj,$msg,EMAIL_TYPE,"");    
}



function notifyuserforcancell($current_user,$id){
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $mentorcal = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT m.*, u.display_name,u.user_email,c.title FROM " . mentorcall()." m LEFT JOIN " . $usertabl ." u ON m.user_id = u.ID "
                . "LEFT JOIN " . courses()." c ON  m.course_id = c.id "
                . "WHERE m.id = %d ORDER BY m.created_dt DESC", $id
        )
    );
      
    $date = $mentorcal->mentor_call;
    
    $emails = $mentorcal->user_email;    
    
    $date = date("D d M Y, h:i a",  strtotime($date));
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
                   
    
    $template = tt_get_template("mentor_call_cancel");            
    $subj = $template->subject; 
    $subj = str_replace("{{course_title}}", $course->title, $subj);

    $msg = $template->content; 
    $msg = str_replace(array('{{username}}','{{mentor_name}}','{{course_title}}','{{call_date}}','{{site_name}}'),
            array($uuser->display_name,$current_user->data->display_name,$course->title,$date,$site_name), $msg);    
    
    custom_mail($emails,$subj,$msg,EMAIL_TYPE,"");
    
}

function updatehrsfrominner($column,$value){
   
    global $wpdb;
    
    if($column == 'course_id'){
        $projhrsm = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT total_hrs,status FROM " . project_exercise() . " WHERE course_id = %d AND module_id = 0", 
                        $value
                )
            ); 
            
    }
    else{
        $projhrsm = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT total_hrs,status FROM " . project_exercise() . " WHERE module_id = %d", 
                        $value
                )
            ); 
    }
    
    $ret = 0;
    if(!empty($projhrsm)){
        if($projhrsm->status == 1){
            $ret = $projhrsm->total_hrs;
        }
        else{
            $ret = -($projhrsm->total_hrs);
        }
    }
    
    return $ret;
}

function update_hours($module_id,$course_id,$removehrs,$hrsadd){
    global $wpdb;
    if($module_id > 0){
        
        
        $total_modulehrs = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT total_hrs FROM " . modules()." WHERE id = %d", $module_id
            )
        );
        $total_modulehrs = ($total_modulehrs + $hrsadd) - $removehrs;
        
        $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . modules() . " SET total_hrs = %s WHERE id = %d", 
                            $total_modulehrs, $module_id
                    )
            );
    
        
        
        $total_coursehrs = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT total_hrs FROM " . courses()." WHERE id = %d", $course_id
            )
        );        
              
        $total_coursehrs = ($total_coursehrs + $hrsadd) - $removehrs;
        $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . courses() . " SET total_hrs = %s WHERE id = %d", 
                            $total_coursehrs, $course_id
                    )
            );
        
        
    }
    else{
        
        $total_coursehrs = $wpdb->get_var
        (
            $wpdb->prepare
            (
                "SELECT total_hrs FROM " . courses()." WHERE id = %d", $course_id
            )
        );
        
        
        $total_coursehrs = ($total_coursehrs + $hrsadd) - $removehrs;
        
        $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE " . courses() . " SET total_hrs = %s WHERE id = %d", 
                            $total_coursehrs, $course_id
                    )
            );
        
    }
}

function deletemedia($lesson_id){
   
    global $wpdb;
    $lessonmedia = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT path FROM " . media() . " WHERE type IN('document','image') AND (lesson_id = %d OR resource_id IN(SELECT id FROM " . resources() ." WHERE lesson_id = %d))", 
                            $lesson_id, $lesson_id
                    )
            ); 
            
    foreach($lessonmedia as $media){
        $path = $media->path;
        $path = TR_COUNT_PLUGIN_DIR.$path;
        @unlink($path);
    }
    
    $wpdb->query(
            $wpdb->prepare
                    (
                    "DELETE FROM " . media() . " WHERE lesson_id = %d OR resource_id IN(SELECT id FROM " . resources() . " WHERE lesson_id = %d)", 
                    $lesson_id, $lesson_id
            )
        );
    
}

function deletemediacourse($id){
    global $wpdb;
    $lessons = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . lessons() . " WHERE module_id IN(SELECT id FROM " . modules() . " WHERE course_id = %d) ",
                            $id
                    )
            );    
    foreach($lessons as $lesson){
        deletemedia($lesson->id);
    }
}

function deletemediamodule($id){
    global $wpdb;
    $lessons = $wpdb->get_results
            (
                    $wpdb->prepare
                            (
                            "SELECT id FROM " . lessons() . " WHERE module_id = %d",
                            $id
                    )
            ); 
    foreach($lessons as $lesson){
        deletemedia($lesson->id);
    }
}


function permission_email_course($course_id,$user_id){
     
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
    (
            $wpdb->prepare
            (
                    "SELECT * FROM ". courses() . " WHERE id = %d",
                    $course_id
            )
    );
    
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
                $wpdb->prepare
                (
                        "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
                )
        );        
        
        if(!empty($uuser)){
            $email = $uuser->user_email;
            $url = site_url()."/".PAGE_SLUG."?course=".$course_id;
            $link = "<a href='".$url."'>Click here to view your course</a>";
                        
            $template = tt_get_template("course_permission_granted");            
            $subj = $template->subject; 
            $subj = str_replace("{{course_title}}", $course->title, $subj);
                  
            $msg = $template->content; 
            $msg = str_replace(array('{{username}}','{{course_title}}','{{url}}','{{site_name}}'),
                    array($uuser->display_name,$course->title,$url,$site_name), $msg);                        
            
            custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    }                    
    
}

function permission_revoke_course($course_id,$user_id){
    /* 
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
    (
            $wpdb->prepare
            (
                    "SELECT * FROM ". courses() . " WHERE id = %d",
                    $course_id
            )
    );
    
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
                $wpdb->prepare
                (
                        "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
                )
        );
        if(!empty($uuser)){
            $email = $uuser->user_email;
                      
            $template = tt_get_template("course_permission_revoked");            
            $subj = $template->subject; 
            $subj = str_replace("{{course_title}}", $course->title, $subj);
                  
            $msg = $template->content; 
            $msg = str_replace(array('{{username}}','{{course_title}}','{{site_name}}'),
                    array($uuser->display_name,$course->title,$site_name), $msg); 
                                                                     
            custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    } 
    */
}

function tt_get_template($template_name){
    global $wpdb;
    $template = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT subject, content FROM " . email_templates()." WHERE template = %s", $template_name
        )
    );
    return $template;
}

function mentor_add_course_email($course_id,$user_id){
     
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
    (
            $wpdb->prepare
            (
                    "SELECT * FROM ". courses() . " WHERE id = %d",
                    $course_id
            )
    );
    
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
                $wpdb->prepare
                (
                        "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
                )
        );
        if(!empty($uuser)){
            $email = $uuser->user_email;
            
            $template = tt_get_template("mentor_added");            
            $subj = $template->subject; 
            $subj = str_replace("{{course_title}}", $course->title, $subj);
                  
            $msg = $template->content; 
            $msg = str_replace(array('{{username}}','{{course_title}}','{{site_name}}'),
                    array($uuser->display_name,$course->title,$site_name), $msg);
                                      
            custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    }
}

function remove_from_course($course_id,$user_id){
    
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $course = $wpdb->get_row
    (
            $wpdb->prepare
            (
                    "SELECT * FROM ". courses() . " WHERE id = %d",
                    $course_id
            )
    );
    
    $site_name = TR_SITE_NAME;    
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
                $wpdb->prepare
                (
                        "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
                )
        );
        if(!empty($uuser)){
            $email = $uuser->user_email;

            $template = tt_get_template("mentor_removed");            
            $subj = $template->subject; 
            $subj = str_replace("{{course_title}}", $course->title, $subj);
                  
            $msg = $template->content; 
            $msg = str_replace(array('{{username}}','{{course_title}}','{{site_name}}'),
                    array($uuser->display_name,$course->title,$site_name), $msg);
                     
            custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
            
        }
    }
    
}


function custom_mail_header($fromcntmail = 'enfusen.com') {
        $additional_parameters = '-f notifications@enfusen.com';
        return "Reply-To: $fromcntmail\r\n"
                . "Return-Path: MCC <notifications@" . $fromcntmail . ">\r\n"
                . "From: Enfusen Notifications <notifications@" . $fromcntmail . ">\r\n"
                . "Return-Receipt-To: notifications@" . $fromcntmail . "\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-type: text/html\r\n"
                . "X-Priority: 3\r\n"
                . "X-Mailer: PHP" . phpversion() . "\r\n";                
    }

function custom_mail($user_email,$setup_sub,$body,$email_type,$reason){        
    if(tr_unsubscribed($user_email) == FALSE){
        $email_template_body = email_template_body($body, $user_email, $email_type);
        @mail($user_email, $setup_sub, $email_template_body, custom_mail_header(), mail_additional_parameters());
        insert_email_historical_report(user_id(), $email_type, $setup_sub, $user_email, $reason, current_id());  
    }      
}

function tr_unsubscribed($email){    
    global $wpdb;
    $tbl_unsbs = $wpdb->prefix."all_email_subscription";
    $unnsubs = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT setting FROM " . $tbl_unsbs." WHERE email = %s",
                    $email
            )
    );
    if(empty($unnsubs)){
        return FALSE;
    }
    $unnsubs = $unnsubs->setting;
    $unnsubs = unserialize($unnsubs);
    $i = 0;
    foreach($unnsubs as $key => $unnsub){
        if($key == EMAIL_TYPE){
            $i = 1;
            break;
        }
    }
    
    if($i == 1){
        return TRUE;
    }
    return FALSE;
}


?>