<?php
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->data->ID;

$courses = $wpdb->get_results
    (
    $wpdb->prepare
            (
            "SELECT * FROM " . courses()." WHERE id IN(SELECT course_id FROM"
            . " " . enrollment()." WHERE user_id = %d) ORDER BY ord ASC",
            $user_id
    )
);


//$view = isset($_GET['view'])?trim($_GET['view']):'my_courses';
//
//$logged = 1;
//if ($user_id == "" || empty($user_id)) {    
//    $view = 'all_courses';    
//    $logged = 0;
//}    
//
//if($view == "all_courses"){
//    $courses = $wpdb->get_results
//        (
//        $wpdb->prepare
//                (
//                "SELECT * FROM " . courses()." WHERE enable_permission = 0 ORDER BY ord ASC"
//        )
//    );
//    
//}
//else{
//    $courses = $wpdb->get_results
//        (
//        $wpdb->prepare
//                (
//                "SELECT * FROM " . courses()." WHERE id IN(SELECT course_id FROM"
//                . " " . enrollment()." WHERE user_id = %d) ORDER BY ord ASC",
//                $user_id
//        )
//    );
//    
//}


if(empty($courses)){            
        
    ?>

    <div class="main-section homeallpage">                
                    <div class="container">
                        
                        <div class="btncours">
                            <a href="?view=all_courses" class="btn btn-primary" >All Courses</a>
                        </div>
                        <h4 class="h4tagfront">My Courses</h4>    
                        
                        <div class="row">
                            <div class="col-sm-12">

                                <div class="alert alert-success">
                                    <strong>Note: </strong>
                                    No Course Found
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

    <?php
    
    
}
else
    {

$base_url = site_url();
$slug = PAGE_SLUG;

?>

<div class="main-section homeallpage">

    <input type="hidden" name="url_redirect" id="url_redirect" value="<?php echo $base_url.'/'.$slug.'?course='; ?>"
    <div class="container">
        <div class="row">           
            <h4 class="h4tagfront">My Courses</h4>            
            <div class="row allcourses">
                <?php
                
                $user = new WP_User($user_id);
                $u_role =  $user->roles[0];
                foreach($courses as $course){
                    
                    $url = "$base_url/$slug?course=$course->id";
                    $title = "$course->title";
                    
                    
                    $total_resources = $wpdb->get_var
                    (
                        $wpdb->prepare
                                (
                                "select count(id) as total FROM " . resources() . " WHERE course_id = %d", $course->id
                        )
                    );
                    
                    $percent = 0;      
                    if($total_resources > 0){
                        $total_covered = $wpdb->get_var
                        (
                            $wpdb->prepare
                                    (
                                    "select count(id) as covered FROM " . resource_status() . " WHERE course_id = %d AND user_id = %d", $course->id,$user_id
                            )
                        );
                        $percent = floor(($total_covered / $total_resources) * 100);
                     }
                    
                    
                    ?>
                        <div class="col-lg-3">      
                            <div class="borderdvcourse">
                            <div class="innerpanel">
                                <div>
                                    <img src="<?php echo TR_COUNT_PLUGIN_URL."/assets/images/data_workshop_thumb.jpg" ?>" />
                                </div>
                                <div class="btnurriculum"><a href='<?php echo $url; ?>' class="btn btn-success">Go To Curriculum</a></div>
                            </div>
                                <div class="lowerpanel">
                                    <h4><a href="<?php echo site_url()."/".PAGE_SLUG."?course_description=".$course->id; ?>"><?php echo $title; ?></a></h4>
                                                                    
                                <div class="row"><hr/></div>
                                <div class="clearfix"></div>
                                    <div class="progressallinner">
                                    <div class="bar_info">
                                        <span><label class="perint"><?php echo $percent; ?></label>% Complete</span>
                                        <div class="bar-progress">
                                            <div class="perdiv" class="bar wip" style="width:<?php echo $percent; ?>%"></div>
                                        </div>

                                    </div>
                                </div>
                                
                            </div>
                            </div>
                        </div>
                    <?php
                    
                }
                ?>
            </div>
         </div>
    </div>

</div>
<?php
    }
?>