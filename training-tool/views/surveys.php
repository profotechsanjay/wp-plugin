<?php

include_once 'common.php';

global $wpdb;
$c_id = get_current_user_id();
$userrole = new WP_User($c_id);
$u_role =  $userrole->roles[0];
$usertbl = $wpdb->prefix."users"; 

if($u_role == "administrator" || isagencylocation()){
     /*$forms = $wpdb->get_results
        (
            $wpdb->prepare
                    (
                    "SELECT s.*,m.user_email,m.display_name FROM " . survey_forms()." s INNER JOIN ".$usertbl." m "
                    . "ON s.mentor_id = m.ID  ORDER BY s.created_dt DESC",""
            )
        );*/
	
	 $forms = $wpdb->get_results
        (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . survey_forms()." ORDER BY created_dt DESC",""
            )
        );
	
}
else{
  /* $forms = $wpdb->get_results
        (
            $wpdb->prepare
            (
                "SELECT * FROM " . survey_forms()." WHERE mentor_id = %d ORDER BY created_dt DESC",$c_id
            )
        );*/
	
	$forms = $wpdb->get_results
        (
            $wpdb->prepare
                    (
                    "SELECT * FROM " . survey_forms()." ORDER BY created_dt DESC",""
            )
        );
}


?>
<div class="contaninerinner">


    <h4>Surveys</h4>

    <div class="panel panel-primary">
        <div class="pull-right"><a href="admin.php?page=new_survey" class="btn btn-success"> Create New Survey Form</a></div>
        <div class="panel-heading">Surveys</div>
        <div class="panel-body">

            <table class="table table-bordered table-striped table-hover" id="data_forms" >
                            <thead>
                                <tr>
                                    <th style="width: 6%;">SNo</th>
                                    <th style="width: 20%;">Title</th>
                                    <?php if($u_role == 'administrator' || isagencylocation()): ?>
                                    <!--th style="width: 20%;">Mentor</th-->                                    
                                    <?php endif; ?>
                                    <th style="width: 20%;">Date</th>										
                                    <th style="width: 25%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($forms as $form) {
                                   
                                    ?>
                                        <tr class="rowmod" data-id="<?php echo $form->id; ?>">
                                            <td><?php echo $i + 1; ?></td> 
                                            <td><?php echo $form->title; ?></td>
                                            <?php if($u_role == 'administrator' || isagencylocation()): ?>
                                            <!--td><?php echo $form->display_name; ?></td-->                                            
                                            <?php endif; ?>
                                            <td><?php echo date("Y-m-d h:i a",  strtotime($form->created_dt)); ?></td>                                                                                                  
                                            <td class="actiontd acttd">   
												<div>
                                                <a data-id="<?php echo $form->id; ?>" href="admin.php?page=new_survey&form_id=<?php echo $form->id; ?>" class="btn btn-primary" title="Edit Form">Edit</a>
                                                <a data-id="<?php echo $form->id; ?>" href="admin.php?page=manage_survey&form_id=<?php echo $form->id; ?>" class="btn btn-success" title="Edit Form">Manage Survey</a>
                                                <a href="javascript:;" data-id="<?php echo $form->id; ?>" title="Delete Form" class="deletesurveyform btn btn-danger">Delete</a>                                    
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>



        </div>
    </div>
</div>