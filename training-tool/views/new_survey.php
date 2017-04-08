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

$data = $form->data;
?>
<div class="contaninerinner">


    <h4><?php echo $form->title != ""?"Edit Survey - ".$form->title:"Create New Survey"; ?> </h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Survey List">
                <a href="admin.php?page=surveys">All Surveys</a> >>
            </li>
            <li title="Survey">
                <?php echo $form->title != ""?$form->title:"Create New Survey Form"; ?> 
            </li>
        </ul>
    </div>



    <div class="panel panel-primary">
        <div class="pull-right">
            <a href="admin.php?page=new_survey" class="btn btn-success"> Create New Survey Form</a>
            <a href="admin.php?page=surveys" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
        <div class="panel-heading">New Survey</div>
        <div class="panel-body">
            <div class="savedjsondata" style="display: none;">
                <?php echo $data; ?>
            </div>
            <form action="#" method="post" id="add_survey" name="add_survey" class="form-horizontal">
                <input type="hidden" id="formid" name="formid" value="<?php echo $form->id; ?>" />
                <?php
                if($u_role == 'administrator' || isagencylocation()){
                    
                    $args = array(	
                            'role'         => MENTOR_ROLE,	
                            'fields'       => 'all'	
                     ); 

                    $mentors = get_users( $args );
                    
                    ?>
                        <!--div class="form-group">
                            <label class="col-lg-2 control-label">Select Mentor</label>
                            <div class="col-lg-4">                                        
                                <select required title="Please Select a Mentor" class="form-control" id="creatementorform" name="creatementorform">
                                <option value="">Select Mentor</option>
                                <?php
                                foreach($mentors as $mentor){
                                    $sel = '';
                                    if($form->mentor_id == $mentor->data->ID)
                                        $sel = 'selected="selected"';
                                    ?>
                                    <option <?php echo $sel; ?> value="<?php echo $mentor->data->ID; ?>"><?php echo $mentor->data->display_name; ?></option>
                                    <?php
                                }
                                ?>
                                </select>
                                
                            </div>
                        </div-->                        
                    <?php
                }
                else{
                    ?>
                    <input type="hidden" id="creatementorform" name="creatementorform" value="<?php echo $c_id; ?>" />
                    <?php
                }
                ?>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Form Title</label>
                    <div class="col-lg-4">                                        
                        <input type="text"  title="Form Title is required" class="form-control" name="formtitle" id="formtitle" placeholder="Enter Form Title" value="<?php echo $form->title; ?>" />

                    </div>
                </div>
                
                <div class="form-group">
                            <label class="col-lg-2 control-label"></label>
                            <div class="col-lg-4">  
                                <?php if(!empty($form)){
                                    ?>
                                    <a href="javascript:;" data-update="1" class="btn btn-primary openform">Update Form</a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="javascript:;" data-update="0" class="btn btn-primary openform">Create Form</a>
                                    <?php
                                } ?>
                                
                            </div>
                        </div>
                <div class="clearfix"></div>
                 <div class="row"><hr/></div>
                        
                <div class="customformbuilder"></div>                
                
            </form>
        </div>
    </div>
</div>