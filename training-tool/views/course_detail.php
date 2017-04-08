<style>
    .entry-title{
        visibility: hidden;
    }
    .subheader{
        display: none;
    }
</style>

<?php

function styleandscripts() {
    // register your script location, dependencies and version and enqueue the script
    //wp_enqueue_script('jquery');	        
    wp_enqueue_script('vendor.js', TR_COUNT_PLUGIN_URL . '/assets/js/vendor.js');
    wp_enqueue_script('jquery-ui.min.js', TR_COUNT_PLUGIN_URL . '/assets/js/jquery-ui.min.js');

    wp_enqueue_script('formbuilder.js', TR_COUNT_PLUGIN_URL . '/assets/js/formbuilder.js');
    wp_enqueue_script('jquery.datetimepicker.full.js', TR_COUNT_PLUGIN_URL . '/assets/js/jquery.datetimepicker.full.js');

    wp_enqueue_script('bootstrap.js', TR_COUNT_PLUGIN_URL . '/assets/js/bootstrap.js');
    wp_enqueue_script('jquery.visible.min.js', TR_COUNT_PLUGIN_URL . '/assets/js/jquery.visible.min.js');

    wp_enqueue_script('jquery.validate.js', TR_COUNT_PLUGIN_URL . '/assets/js/jquery.validate.js');

    wp_enqueue_script('jquery.dataTables.js', TR_COUNT_PLUGIN_URL . '/assets/js/jquery.dataTables.js');



    wp_enqueue_script('formrenderer.uncompressed.js', TR_COUNT_PLUGIN_URL . '/assets/js/formrenderer.uncompressed.js');


    wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL . '/assets/js/script.js?ver=', '', TT_VERSION);


    // style        
    wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL . '/assets/css/style.css', '', TT_VERSION);
    wp_enqueue_style('bootstrap.css', TR_COUNT_PLUGIN_URL . '/assets/css/bootstrap.css', '', TT_VERSION);
    wp_enqueue_style('jquery.datetimepicker.css', TR_COUNT_PLUGIN_URL . '/assets/css/jquery.datetimepicker.css');
    wp_enqueue_style('font-awesome.min.css', TR_COUNT_PLUGIN_URL . '/assets/css/font-awesome.min.css');
    wp_enqueue_style('jquery.dataTables.css', TR_COUNT_PLUGIN_URL . '/assets/css/jquery.dataTables.css');
    wp_enqueue_style('formbuilder.css', TR_COUNT_PLUGIN_URL . '/assets/css/formbuilder.css');
    wp_enqueue_style('vendor.css', TR_COUNT_PLUGIN_URL . '/assets/css/vendor.css');

    wp_enqueue_style('preview.css', TR_COUNT_PLUGIN_URL . '/assets/css/preview.css');
    wp_enqueue_style('formrenderer.uncompressed.css', TR_COUNT_PLUGIN_URL . '/assets/css/formrenderer.uncompressed.css');
    wp_enqueue_style('components.min.css', site_url() . '/wp-content/themes/twentytwelve/report-theme/assets/global/css/components.min.css');    
        
}

styleandscripts();
include_once 'common.php';
if (isset($_REQUEST['accept_call']) && $_REQUEST['accept_call'] > 0) {
    include_once 'mentorcallaccept.php';
} else if (isset($_REQUEST['call_status']) && $_REQUEST['call_status'] == 'accepted') {
    include_once 'call_status.php';
} else if (isset($_REQUEST['survey']) && $_REQUEST['survey'] != '') {
    include_once 'front_survey.php';
} else if (isset($_REQUEST['course_description']) && $_REQUEST['course_description'] != '') {
    include_once 'detail_only_course.php';
}
else {

    if ((isset($_REQUEST['lesson_detail']) && $_REQUEST['lesson_detail'] > 0) || isset($_REQUEST['exercise_detail']) && $_REQUEST['exercise_detail'] > 0) {
        if (isset($_REQUEST['exercise_detail']) && $_REQUEST['exercise_detail'] > 0) {
            $resource_id = intval($_REQUEST['exercise_detail']);
            $resource = $wpdb->get_row
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM " . resources() . " WHERE id = %d", $resource_id
                    )
            );
            if (empty($resource)) {
                die('Invalid Exercise');
            }
            $lesson_id = $resource->lesson_id;
            $file = 'fron_resource_detail.php';
        } else {
            $lesson_id = intval($_REQUEST['lesson_detail']);
            $file = 'fron_lesson_detail.php';
        }

        $lesson = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . lessons() . " WHERE id = %d", $lesson_id
                )
        );
        if (empty($lesson)) {
            die('Invalid Lesson');
        }
        $module_id = $lesson->module_id;

        $module = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT id,title FROM " . modules() . " WHERE id = %d", $module_id
                )
        );

        $course = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . courses() . " WHERE id IN (SELECT course_id FROM " . modules() . " WHERE id = %d) ", $module_id
                )
        );
        $course_id = 0;
        if (!empty($course)) {
            $course_id = $course->id;
        }

        include_once $file;
    } else {
        $course_id = isset($_REQUEST['course']) ? intval($_REQUEST['course']) : 0;
        $course = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . courses() . " WHERE id =  %d", $course_id
                )
        );

        if (!empty($course)) {
            include_once 'single_course_detail.php';
        } else {
            include_once 'all_courses.php';
        }
    }
}
?>
