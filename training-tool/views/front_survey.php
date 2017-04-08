<?php

$survey_id = intval($_REQUEST['survey']);
$survey = $wpdb->get_row
(
        $wpdb->prepare
                (
                "SELECT * FROM " . survey_results() . " WHERE id = %d", $survey_id
        )
); 
if(empty($survey)){
    die("Survey not Exist");
}
$form_id = $survey->survey_id;

$guid = isset($_REQUEST['guid'])?htmlspecialchars($_REQUEST['guid']):'';
$guid = trim($guid);

if($survey->guid != $guid){
    die('Invalid guid.');
}


$form_id = $survey->survey_id;
$form = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT * FROM " . survey_forms()." "
                . "WHERE id = %d",$form_id
        )
    );

$mentorname = "";
$mentor_id = $form->mentor_id;
$mentor = get_user_by("id", $mentor_id);
$mentorname = "<strong>Mentor: </strong> " . $mentor->data->display_name;

$user_id = $survey->user_id;
$user = get_user_by('id', $user_id );

if ( is_wp_error( $user ) ){
    die('Invalid User');
}

$ilog = 0;
if(!is_user_logged_in()){
    $ilog = 1;    
}
else{    
    $c_id = get_current_user_id();
    if($user_id != $c_id){
        $ilog = 1;
    }
}

if($ilog == 1){
    
    wp_clear_auth_cookie();
    wp_set_current_user ( $user->ID, $user_login );
    wp_set_auth_cookie  ( $user->ID );
    do_action( 'wp_login', $user_login );
    wp_redirect($_SERVER['REQUEST_URI']);
    
}

$data = $survey->data;
/* survey not submitted*/
?>

    <div class="surveyfields frontpage">
        <div class="pull-right"><?php echo $mentorname; ?></div>
        <h4><?php echo $form->title; ?> </h4>
            <div style='display: none;'>
                <input type="hidden" id="survey_id" name="survey_id" value="<?php echo $survey_id; ?>" />
                <select id='lib' class='js_stored_val config_select'>
                    <option value=''>No library</option>                    
                    <option selected="selected" value='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'>Bootstrap</option>                    
                </select>

                <select id='fixture' class='js_stored_val config_select'>
                    <option value='KITCHEN_SINK'>Kitchen Sink</option>                    
                </select>
            </div>
            <form data-formrenderer></form>

            <?php include_once 'formshowscript.php'; ?>
        </div>    


<?php
if($survey->is_submitted == 1){    
    ?>
    <input type="hidden" id="survey_sumitted" name="survey_sumitted" value="1" />
    <script>
        $(document).ready(function(){
            if($(".fr_bottom,.fr_error").length > 0)
                $(".fr_bottom,.fr_error").remove(); 
            $(".fr_page input,.fr_page textarea,.fr_page select").attr("disabled","disabled");
        });
    </script>
    <?php
}

?>
