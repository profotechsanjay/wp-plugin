<?php
if(empty($course)){
    $course_id = isset($_GET['course_description'])?intval($_GET['course_description']):0;
    $course = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . courses() . " WHERE id = %d", $course_id
            )
    );    
}


$current_user = wp_get_current_user();
$user_id = $current_user->data->ID;

$is_enrolled = $wpdb->get_var
        (
        $wpdb->prepare
                (
                "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
        )
);

if($is_enrolled == 0){
    header('Location: '.site_url()."/".PAGE_SLUG);
}

$base_url = site_url();
$slug = PAGE_SLUG;

?>
<div class="main-section well">

    <input type="hidden" name="url_redirect" id="url_redirect" value="<?php echo $base_url.'/'.$slug.'?course='; ?>"
    <div class="container">
        <h4>Course Detail</h4>
            <div class="bread_crumb">
                <ul>
                    <li title="All Courses List">
                        <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                    </li>
                    <li title="Course">
                        <?php echo $course->title; ?>
                    </li>
                </ul>
            </div>
        <div class="">
            <h2 class="h2main"><?php echo $course->title;?></h2>    
            <span class="fade_txt">Created by <?php echo $current_user->data->display_name; ?>, <?php echo date('D d M Y',  strtotime($course->created_dt));?></span>
            <div class="desccontent texteditor">
                <?php echo html_entity_decode($course->description); ?>
            </div>
            <div class="enrollmentdiv">
                <?php 
                $base_url = site_url();
                $slug = PAGE_SLUG;
                $url = "$base_url/$slug?course=$course->id"; 
                ?>
                <a href='<?php echo $url; ?>' class="btn btn-primary">Go To Curriculum</a>
            </div>
        </div>
    </div>
</div>