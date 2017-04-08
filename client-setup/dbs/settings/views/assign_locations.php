<?php
include_once 'common.php';
global $wpdb;
$msg = '';
$base_url = site_url();
$exclude_roles = explode(',', ST_EXCLUDE_ROLES);
$location_id = isset($_REQUEST['location_id']) ? intval($_REQUEST['location_id']) : 0;
$location = $wpdb->get_row
(
    $wpdb->prepare
    (
    "SELECT * FROM " . client_location() . " WHERE id = %d", $location_id
    )
);


if (empty($location)) {
    ?>
    <div class="update-nag">Invalid Location</div>
    <?php
    die;
}

$UserID = $location->MCCUserId;
$location_web = get_user_meta($UserID, 'website', TRUE);
$location_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);

$usertbl = $wpdb->prefix . 'users';

$users = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT l.*,u.* FROM " . location_mapping() . " l INNER JOIN $usertbl u ON l.user_id = u.ID"
                . " WHERE l.location_id = %d ORDER BY l.created_dt DESC", $location_id
        )
);

if(isset($_POST['username']) && $_POST['username'] != ''){
    
    $WP_array = array (		
		//'user_pass' => esc_attr($_POST['user_pass']),
		'user_login' => esc_attr($_POST['username']),
		'user_nicename' => esc_attr($_POST['first_name']),
		'user_email' => esc_attr($_POST['user_email']),
		'display_name' => esc_attr($_POST['username']),
		'nickname' => esc_attr($_POST['username']),
		'first_name' => esc_attr($_POST['first_name']),
		'last_name' => esc_attr($_POST['last_name']),		
                'rich_editing' => false,
                'comment_shortcuts' => false,
                'show_admin_bar_front' => false
	) ;

	$user_id = wp_insert_user( $WP_array ) ;        
        if ( is_wp_error($user_id) ){
            $msg = $user_id->get_error_message();
            $msg = '<div class="messdv alert alert-danger" style=""> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            '.$msg.'</div>';
        }
        else{
            
            if(isset($_POST['is_writer'])){
                if(trim(strtolower(get_user_meta($user_id, 'tag_type', TRUE))) == 'writer'){
                    update_user_meta($user_id,'tag_type','writer');
                }
                else{
                    add_user_meta($user_id,'tag_type','writer');
                }
                
            }
            else{
                delete_user_meta($user_id,'tag_type');
            }
            
            wp_update_user( array ('ID' => $user_id, 'role' => esc_attr($_POST['people_user_role']) ) );
            // addd in location
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "INSERT INTO " . location_mapping() . " (location_id, user_id, created_dt) "
                    . "VALUES(%d, %d, '%s')",
                    $location_id, $user_id, date("Y-m-d H:i:s")
                )
            );
            user_location_add_email($location_id,$user_id);
            
            $users = $wpdb->get_results
                    (
                    $wpdb->prepare
                            (
                            "SELECT l.*,u.* FROM " . location_mapping() . " l INNER JOIN $usertbl u ON l.user_id = u.ID"
                            . " WHERE l.location_id = %d ORDER BY l.created_dt DESC", $location_id
                    )
            );
            
            
            if (isset($_POST['send_invite']) && $_POST['send_invite'] == 1) {
                
                $brand_name = "";
                $user_email = esc_attr($_POST['user_email']);
                $first_name = esc_attr($_POST['first_name']);                
                
                $activation_link = site_url() . '/account-activate/?email=' . $user_email . '&code=' . md5($user_email);
                $email_body = file_get_contents(site_url() . '/email/add_worker.php');
                $setup_sub =  MCC_NAME." - Please activate your Account";
                $body = str_replace('~~FIRST_NAME~~', $first_name, $email_body);
                $body = str_replace('~~BRAND_NAME~~', $location_name, $body);
                $body = str_replace('~~ACTIVATION_LINK~~', $activation_link, $body);
                $body = html_entity_decode($body);
                $email_template_body = email_template_body($body, $user_email, 'add_worker');
                @mail($user_email, $setup_sub, $email_template_body, mail_header(), mail_additional_parameters());
                insert_email_historical_report(user_id(), 'Add Worker', $setup_sub, $user_email, 'Added new worker', current_id());
            }
            else{
                wp_update_user( array ('ID' => $user_id, 'user_pass' => esc_attr($_POST['user_pass'])) );
            }
            
            $msg = "User successfully added and assigned to location";
            $msg = '<div class="messdv alert alert-success" style=""> <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            '.$msg.'</div>';
        }                
            
}

?>
<?php if($msg != ''): ?>
    <div class="msg"> <?php echo $msg; ?> </div>
<?php endif; ?>
<div class="contaninerinner">         
    <h4>Assign Users - <?php echo $location_name . " ( ".$location_web." )"; ?></h4>   
    <div class="bread_crumb">
        <ul>
            <li title="Locations">
                <a href="<?php echo ST_LOC_PAGE; ?>?parm=locations">Locations</a> >>
            </li>
            <li title="Assign Users">
                Assign Users
            </li>
        </ul>
    </div>
    <div class="panel panel-primary">        
        <div class="panel-heading">Assign Users</div>
        <div class="panel-body">            
            <input type="hidden" id="location_id" name="location_id" value="<?php echo $location_id; ?>" />
            <table class="commontable display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="width: 5%">SNo</th>
                        <th style="width: 15%">Username</th>
                        <th style="width: 15%">Login</th>
                        <th style="width: 15%">Email</th>                                                        
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>

                <tbody>

<?php
$j = 0;
foreach ($users as $user) {

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
                                <a href="javascript:;" data-id="<?php echo $user->ID; ?>" class="remove_user btn btn-primary">Unassign User</a>
                                <a href="javascript:;" data-id="<?php echo $user->ID; ?>" class="deleteuser btn btn-danger">Delete User</a>
                            </td>
                        </tr>

    <?php
}
?>

                </tbody>
            </table>

            <div class="staticform">
                <div class="row">
                    <form method="post" name="mentorform" id="mentorform">
                        <div class="control-group">
                            <label class="col-lg-1 lblfind control-label">Find User</label>
                            <div class="col-lg-5">
                                <input type="email" name="memail" id="memail" required email title="Valid Email Required" Placeholder="Search Email..." class="form-control" placeholder="" />
                                <div class="clearfix"></div>                                                        
                            </div>                                                    
                            <div class="col-lg-2">
                                <button type="button" class="btnmentoradd btn btn-success">Assign User</button>
                            </div>
                            <div class="col-lg-1 ORspan">
                                <span>OR</span>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-success" onclick="jQuery('#adduser_dialog').modal();">Create New User</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>


</div>

    <?php
    
    $all_role = all_role_info();    
    
    ?>

<div id="adduser_dialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Create New User</h4>
            </div>
            <div class="modal-body">

                <form id="people_add_Frm" name="people_add_Frm" method="post" action="" enctype="multipart/form-data">
                  

                    <div id="username_div col-md-4">
                        <div class="left_task">Username:</div>

                        <div class="right_task"><input type="text" required name="username" class="required"></div>

                        <div class="clear_both"></div>
                    </div>
                    <div id="worker_type">                                                

                        <div class="left_task">First Name:</div>

                        <div class="right_task"><input type="text" required name="first_name" class="required"></div>

                        <div class="clear_both"></div>



                        <div class="left_task">Last Name:</div>

                        <div class="right_task"><input type="text" name="last_name"  class="required"></div>

                        <div class="clear_both"></div>
                    </div>                                        
                    
                    <div class="left_task">Email:</div>

                    <div class="right_task" id="user_email_div">
                        <input type="text" name="user_email" email="true"  required  class="email required">

                    </div>

                    <div class="clear_both"></div>
                    
                    <div class="left_task">Send Invite:</div>
                    
                    <div class="right_task"><input type="checkbox" checked="true" name="send_invite" onchange="jQuery('#password_div').toggle('slow');" value="1"></div>
                    <div class="clear_both"></div>
                
                    
                    <div id="password_div" style="display: none;">
                        <div class="left_task">Password:</div>

                        <div class="right_task"><input type="password" required name="user_pass" id="user_pass" value="" class="required"></div>

                        <div class="clear_both"></div>
                        
                        <div class="left_task">Confirm Password:</div>

                        <div class="right_task"><input equalTo="#user_pass" type="password" required name="cuser_pass" id="cuser_pass" value="" class="required"></div>

                        <div class="clear_both"></div>
                        
                    </div>
                    
                    <div class="clear_both"></div>
                    
                     <?php if(defined('agency_full_content') && agency_full_content == 1): ?>
                    <div class="left_task">Tag:</div>
                    <div class="right_task" id="user_email_div">
                        <label><input type="checkbox" name="is_writer" value="3"> Enable Writer Panel</label>                                    
                    </div>
                    <div class="clear_both"></div>
                    <?php endif; ?>
                                
                    <div class="left_task">Role:</div>

                    
                <select name="people_user_role" class="required form-control selectrole">

                    <?php
                    foreach ($all_role as $index_role => $single_role) {
                        $rol = $single_role['name'];
                        if(in_array($rol, $exclude_roles)){
                            continue;
                        }
                        $sel = "";
                        if($index_role == 'worker'){
                            $sel = 'selected="selected"';
                        }
                        ?>
                        <option <?php echo $sel ?> value="<?php echo $index_role; ?>"><?php echo $single_role['name']; ?></option>
                        <?php
                    }
                    ?>

                    </select>                    
                    


                    <div class="left_task">&nbsp;</div>
                    <div class="right_task"><a target="_blank" href="<?php echo site_url()."/wp-admin/users.php?page=users-user-role-editor.php"; ?>">Manage Roles And Capabilities</a></div>
                    <div class="clear_both"></div>
                    <div class="right_task"><input onclick="jQuery('#people_add_Frm').submit();" style="color:white;" type="submit" class="btn new_btn_class" value="Add & Assign Location">
                    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
                    </div>
                    <div class="clear_both"></div>

                </form>

            </div>            
        </div>
    </div>
</div>
    
    <script>
    jQuery(document).ready(function(){        
       jQuery('#people_add_Frm').validate(); 
    });
    
    </script>