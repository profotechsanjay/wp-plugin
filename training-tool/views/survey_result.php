<?php
include_once 'common.php';
global $wpdb;
$c_id = get_current_user_id();
$userrole = new WP_User($c_id);
$u_role = $userrole->roles[0];

$survey_id = isset($_REQUEST['survey_id']) ? intval($_REQUEST['survey_id']) : 0;

$survey = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . survey_results() . " WHERE id = %d", $survey_id
        )
);
if (empty($survey)) {
    die("Survey not Exist");
}

$form_id = $survey->survey_id;


$form = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . survey_forms() . " "
                . "WHERE id = %d", $form_id
        )
);
if (empty($form)) {
    die("Form not Exist");
}

$mentorname = "";
if ($u_role == "administrator" || isagencylocation()) {
    $mentor_id = $form->mentor_id;
    $mentor = get_user_by("id", $mentor_id);
    $mentorname = "<strong>Mentor: </strong> " . $mentor->data->display_name;
}


$data = $survey->data;


?>

<style>
    .fr_bottom{
        display: none;
    }
    .fr_error{
        display: none;
    }
</style>
<div class="contaninerinner">

    <div class="pull-right"><?php echo $mentorname; ?></div>
    <h4>Manage Survey - <?php echo $form->title; ?> </h4>
    <?php if(isset($_GET['course']) && $_GET['course'] != ''): ?>
        <div class="bread_crumb">
            <ul>
                <li title="Course Admin">
                    <a href="admin.php?page=course_admin&course=<?php echo intval($_GET['course']); ?>">Course Admin</a> >>
                </li>
                <li title="User Record">
                    <a href="admin.php?page=user_record&user_id=<?php echo $survey->user_id; ?>&course=<?php echo intval($_GET['course']); ?>">User Records</a> >>
                </li>
                <li title="Survey Result ">
                    Survey Result
                </li>
            </ul>
        </div>
    <?php else: ?>
    <div class="bread_crumb">
        <ul>
            <li title="All Survey List">
                <a href="admin.php?page=surveys">All Surveys</a> >>
            </li>
            <li title="Manage Survey">
                <a href="admin.php?page=manage_survey&form_id=<?php echo $form_id; ?>"> <?php echo $form->title; ?> </a> >>
            </li>
            <li title="Survey Result">
                Survey Result
            </li>
        </ul>
    </div>
    <?php endif; ?>


    <div class="panel panel-primary">
        <div class="pull-right">  
             <?php if(isset($_GET['course']) && $_GET['course'] != ''): ?>
                <a href="admin.php?page=user_record&user_id=<?php echo $survey->user_id; ?>&course=<?php echo intval($_GET['course']); ?>" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
            <?php else: ?>
                <a href="admin.php?page=manage_survey&form_id=<?php echo $form_id; ?>" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
            <?php endif; ?>
        <div class="panel-heading">Survey Result - <?php echo $form->title; ?> </div>
        <div class="panel-body"> 
            <?php if(isset($survey->is_submitted) && $survey->is_submitted == 0){ ?>
                <div class="col-lg-12">
                    <div class="update-nag">
                        <strong>Note : </strong> Survey Not Submitted Yet
                    </div>
                </div>
            <div class="clearfix"></div>
            <?php } ?>
            
            <div class="surveyfields backpage">

                <div style='display: none;'>                    
                    <select id='lib' class='js_stored_val config_select'>
                        <option value=''>No library</option>                    
                        <option selected="selected" value='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'>Bootstrap</option>                    
                    </select>

                    <select id='fixture' class='js_stored_val config_select'>
                        <option value='KITCHEN_SINK'>Kitchen Sink</option>                    
                    </select>
                </div>
                <div data-formrenderer></div>
                
                <?php include_once 'formshowscript.php'; ?>
            </div>            
        </div>
    </div>
</div>

              



