<?php
global $wpdb;
include_once 'common.php';
global $current_user;
$all_role = all_role_info();
$msg = '';
$exclude_roles = explode(',', ST_EXCLUDE_ROLES);

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
        
	$user_id = wp_insert_user( $WP_array );        
        
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
            
            $location_id = isset($_POST['locationassign'])?intval($_POST['locationassign']):0;
            wp_update_user( array ('ID' => $user_id, 'role' => esc_attr($_POST['people_user_role']) ) );
            // addd in location
            
            if($location_id > 0){
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
            }
            
            if (isset($_POST['send_invite']) && $_POST['send_invite'] == 1) {
                                
                $locuid = $wpdb->get_var
                (
                    $wpdb->prepare
                    (
                        "SELECT MCCUserId FROM " . client_location()." WHERE id = %d",$location_id
                    )
                );
                
                $brand_name = get_user_meta($locuid, 'BRAND_NAME', TRUE);
                $user_email = esc_attr($_POST['user_email']);
                $first_name = esc_attr($_POST['first_name']);                
                
                $activation_link = site_url() . '/account-activate/?email=' . $user_email . '&code=' . md5($user_email);
                $email_body = file_get_contents(site_url() . '/email/add_worker.php');
                $setup_sub =  MCC_NAME." - Please activate your Account";
                $body = str_replace('~~FIRST_NAME~~', $first_name, $email_body);
                $body = str_replace('~~BRAND_NAME~~', $brand_name, $body);
                $body = str_replace('~~ACTIVATION_LINK~~', $activation_link, $body);
                $body = html_entity_decode($body);
                $email_template_body = email_template_body($body, $user_email, 'add_worker');
                @mail($user_email, $setup_sub, $email_template_body, mail_header(), mail_additional_parameters());
                insert_email_historical_report(user_id(), 'Add Worker', $setup_sub, $user_email, 'Added new worker', current_id());
            }
            else{
                wp_update_user( array ('ID' => $user_id, 'user_pass' => esc_attr($_POST['user_pass'])) );
            }
          
            $msg = "User successfully added";
            $msg = '<div class="messdv alert alert-success" style=""> <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            '.$msg.'</div>';
                                    
                                    
        }             
           
        if($msg != ''){
            $_SESSION['usrmsg'] = $msg;            
            header("Refresh:0");
        }
}

if (isset($_POST['user_status_change_id'])) {
    $edit_user_id = $_POST['user_status_change_id'];
    if ($_POST['current_user_status'] == 'Inactive') {
        $inactive = 0;
        $wp_capabilities = array('subscriber' => 1);
        update_user_meta($edit_user_id, 'wp_capabilities', $wp_capabilities);
        $succ_msg = 'Successfully activate ' . user_email($_POST['user_status_change_id']);
    } else {
        $inactive = 1;
        $succ_msg = 'Successfully inactivate ' . user_email($_POST['user_status_change_id']);
    }
}

$locations = $wpdb->get_results
(
    $wpdb->prepare
    (
        "SELECT * FROM " . client_location() . " WHERE status = 1 ORDER BY created_dt DESC", ""
    )
);


$loc = isset($_REQUEST['location']) ? htmlspecialchars($_REQUEST['location']) : '';
$usertbl = $wpdb->prefix . 'users';

if ($loc > 0) {
    // location users        

    $userids = $wpdb->get_results
    (
        $wpdb->prepare
        (
            "SELECT user_id FROM " . location_mapping() . " WHERE location_id = %d ORDER BY created_dt DESC", $loc
        )
    );
    
     $users = new stdClass();
    if(!empty($userids)){
        $ar = array();
        foreach ($userids as $userid) {
            array_push($ar, $userid->user_id);
        }
        $args = array(
            'include' => $ar,
            'fields' => 'all',
        );

        $users = get_users($args);
    }
    
    
    $locuserstotal = $wpdb->get_results
    (
        $wpdb->prepare
        (
            "SELECT l.user_id FROM " . location_mapping() . " l INNER JOIN  " . client_location() . " c "
            . "ON l.location_id = c.id group by l.user_id", ''
        )
    );
    $locuserstotal = count($locuserstotal);
    
}
else if ($loc == 'all_users') {
       
    $users = get_users();
    
    $locuserstotal = $wpdb->get_results
    (
        $wpdb->prepare
        (
            "SELECT l.user_id FROM " . location_mapping() . " l INNER JOIN  " . client_location() . " c "
            . "ON l.location_id = c.id group by l.user_id", ''
        )
    );
    $locuserstotal = count($locuserstotal);
 
}
else {
    // all locations
    
    $userids = $wpdb->get_results
    (
        $wpdb->prepare
        (
            "SELECT DISTINCT l.user_id FROM " . location_mapping() . " l INNER JOIN  " . client_location() . " c "
            . "ON l.location_id = c.id", ''
        )
    );
    $locuserstotal = count($userids);
    $users = new stdClass();
    if(!empty($userids)){
        $ar = array();
        foreach ($userids as $userid) {
            array_push($ar, $userid->user_id);
        }
        $args = array(
            'include' => $ar,
            'fields' => 'all',
        );

        $users = get_users($args);  
    }    
      
}

function get_location_total(){
    global $wpdb;         
    $totalusers = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT count(id) as total FROM " . client_location(). " WHERE status = 1", ''
        )
    );    
    return $totalusers->total;
}

function get_users_total(){
    $totl = count(get_users());
    return $totl;    
}

function get_location_total_users($location_id){
    global $wpdb;    
    $totalusers = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT count(distinct(user_id)) as total FROM " . location_mapping() . " WHERE location_id = %d", $location_id
        )
    );   
    return $totalusers->total;
}

function get_agency_locations($single_user_id){ 
    global $wpdb;
    $locs = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT c.MCCUserId FROM " . client_location() . " c INNER JOIN  " . location_mapping() . " l "
                    . "ON c.id = l.location_id WHERE l.user_id = %d", $single_user_id
            )
    );
   
    if(empty($locs)){
        return  ''; 
    }
    $locats = '<div class="tdlocs">';
    foreach($locs as $l){
        $website = get_user_meta($l->MCCUserId, 'BRAND_NAME', true);
        $locats .= "$website, ";
    }
    $locats = substr($locats, 0, -2);
    $locats .= '</div>';
    return $locats;
}

$base_url = site_url();
$location_totlausers = get_location_total();
$loc_str = '';

?>
<?php if(isset($_SESSION['usrmsg']) && $_SESSION['usrmsg'] != ''): ?>
    <div class="msg"> <?php echo $_SESSION['usrmsg']; ?> </div>
    <?php $_SESSION['usrmsg'] = ''; unset($_SESSION['usrmsg']); ?>
<?php endif; ?>
<div class="panel panelmain">
    <div class="col-lg-12">
        <div class="contaninerinner">         
            <h4>Master User List</h4>            
            <div class="row">
                <div class="col-lg-4">
                    <select class="form-control" name="locationdd" id="locationdd">       
                        <?php
                        $selall = ''; $selusers = '';
                        if (isset($_REQUEST['location']) && $_REQUEST['location'] == 'all'){
                          $selall = 'selected="selected"';  
                        }
                                                                        
                        if (isset($_REQUEST['location']) && $_REQUEST['location'] == 'all_users'){
                          $selusers = 'selected="selected"';  
                        }
                        ?>
                        <option <?php echo $selall; ?> value="all">All Locations (<?php echo $locuserstotal; ?>)</option>
                        <?php
                        foreach ($locations as $location) {
                            $website = get_user_meta($location->MCCUserId, 'BRAND_NAME', TRUE);
                            $loc_str .= $website.', ';
                            $total_users = get_location_total_users($location->id);
                            $sel = '';
                            if (isset($_REQUEST['location']) && $_REQUEST['location'] == $location->id) {
                                $sel = 'selected="selected"';
                            }
                            ?>
                                <option <?php echo $sel; ?> value="<?php echo $location->id; ?>"><?php echo $website; ?> (<?php echo $total_users; ?>)</option>
                            <?php
                        }
                        ?>
                        <option <?php echo $selusers; ?> value="all_users">All Users (<?php echo get_users_total(); ?>)</option>
                    </select>
                </div>
                <button type="button" class="btn btn-success" onclick="jQuery('#adduser_dialog').modal();">Add New User</button>
                <div class="clearfix"></div>
            </div>
            <?php 
            $loc_str = substr($loc_str,0,-2);
            ?>
            <input type="hidden" name="all_locs_str" id="all_locs_str" value="<?php echo $loc_str; ?>" />
            <div class="row margin_top_10">
                <div class="col-lg-12 margin_top_10 tblouter" id="tblouter">
                    <table style="font-size:92%!important;width:100%!important;" class="tablleusers table table-striped table-bordered table-hover" cellspacing="0" >
                        <thead style="background-color: #888;color:white;">
                            <tr>
                                <th>Email</th>
                                <th>First & Last Name</th>
                                <th style="text-align: center;">Locations</th>
                                <th style="text-align: center;">Active/Inactive</th>
                                <th style="text-align: center;">Role</th>                                   
                                <th style="text-align: center;">Registration Date</th>
                                <th style="text-align: center; width: 200px; ">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php                                    
                                    foreach ($users as $single_user) {
                                        $single_user_id = $single_user->ID;
                                        $sinle_user_role_name = role($single_user_id);
                                        $show_user = 1;
                                                                               
                                        
                                        if ($show_user == 1) {
                                            
                                            $ulocations = get_agency_locations($single_user_id);
                                            
                                            //$website = get_user_meta($single_user_id, 'website', true);
                                            
                                            ?>
                                            <tr>
                                                <td><?php echo $single_user->user_email; ?></td>
                                                <td style="text-indent: 5px;" id="full_name_<?php echo $single_user_id; ?>">
                                            <?php
                                            $first_name = get_user_meta($single_user_id, 'first_name', true);
                                            $last_name = get_user_meta($single_user_id, 'last_name', true);
                                            echo $full_name = $first_name . ' ' . $last_name;
                                            ?> 
                                        </td>
                                        <td style="text-align: center; " id="BRAND_NAME_<?php echo $single_user_id; ?>">
                                            <?php
                                            echo $ulocations;
                                            ?>
                                            <div class="assignloc"><a data-uid="<?php echo $single_user_id; ?>" href="javascript:;">Assign Location</a></div>
                                        </td>
                                        <td style="text-align: center;" id="user_status_col_<?php echo $single_user_id; ?>">
                                            <?php
                                            if ($sinle_user_role_name == 'canceled_user') {
                                                $user_status = 'Inactive';
                                                $user_change_text = 'Make Active';
                                                $js_text = 'Are you sure ' . $single_user->user_email . ' user will be activate?';
                                            } else {
                                                $user_status = 'Active';
                                                $user_change_text = 'Make Inactive';
                                                $js_text = 'Are you sure ' . $single_user->user_email . ' user will be inactivate?';
                                            }
                                            echo $user_status;
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php
                                            echo $role_display_name = $sinle_user_role_name;                                            
                                            ?>
                                        </td>
                                            <?php if ($role == 'accelerator' || $role == 'enterprise') { ?>
                                            <td style="text-align: center;">
                                                <?php
                                                $analytics_user_id = analytics_user_id($single_user_id);
                                                if (isset($site_audit_user[$analytics_user_id])) {
                                                    echo $site_audit_user[$analytics_user_id];
                                                } else {
                                                    echo 'Not run';
                                                }
                                                ?>
                                            </td>
                                            <?php } ?>
                                        <td style="text-align: center;">
                                            <?php echo date("d M Y", strtotime($single_user->user_registered)); ?>
                                        </td>
                                        <td style="text-align: center; ">
                                            <input type="hidden" id="current_user_status_<?php echo $single_user_id; ?>" value="<?php echo $user_status; ?>">
                                            <a href="javascript:void(0);" onclick="user_status_change('<?php echo $single_user_id; ?>', '<?php echo $single_user->user_email; ?>')" id="user_change_text_<?php echo $single_user_id; ?>"><?php echo $user_change_text; ?></a> |
                                            <!--<a href="<?php echo site_url(); ?>/user-control/?edit-user-id=<?php echo $single_user_id; ?>">Edit</a> |-->
                                            <a onclick="user_edit_func('<?php echo $single_user_id; ?>', '<?php echo $single_user->user_email; ?>')">Edit</a> |
                                            <a href="javascript:void(0);" class="deleteuser" data-id='<?php echo $single_user_id; ?>'> Delete </a>
                                            <!------------->
                                            <span style="display: none;"id="list_username_<?php echo $single_user_id; ?>"><?php echo $single_user->user_login; ?></span>
                                            <span style="display: none;"id="list_first_name_<?php echo $single_user_id; ?>"><?php echo $first_name; ?></span>
                                            <span style="display: none;"id="list_last_name_<?php echo $single_user_id; ?>"><?php echo $last_name; ?></span>
                                            <span style="display: none;"id="list_website_<?php echo $single_user_id; ?>"><?php echo $website; ?></span>
                                            <span style="display: none;"id="list_role_name_<?php echo $single_user_id; ?>"><?php echo $sinle_user_role_name; ?></span>
                                            <span style="display: none;"id="list_brand_name_<?php echo $single_user_id; ?>"><?php echo $single_user_brand_name; ?></span>
                                            <span style="display: none;"id="list_phonenumber_<?php echo $single_user_id; ?>"><?php echo get_user_meta($single_user_id, "phonenumber", true); ?></span>
                                            <span style="display: none;"id="list_streetaddress_<?php echo $single_user_id; ?>"><?php echo get_user_meta($single_user_id, "streetaddress", true); ?></span>
                                            <span style="display: none;"id="list_city_<?php echo $single_user_id; ?>"><?php echo get_user_meta($single_user_id, "city", true); ?></span>
                                            <span style="display: none;"id="list_state_<?php echo $single_user_id; ?>"><?php echo get_user_meta($single_user_id, "state", true); ?></span>
                                            <span style="display: none;"id="list_zip_<?php echo $single_user_id; ?>"><?php echo get_user_meta($single_user_id, "zip", true); ?></span>
                                            <span style="display: none;"id="is_writer_<?php echo $single_user_id; ?>"><?php echo get_user_meta($single_user_id, "tag_type", true); ?></span>
                                            <!------------->
                                        </td>
                                    </tr> 
        <?php
    }
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<form name="delete_user_Frm" method="post">
    <input type="hidden" name="delete_user_id" id="delete_user_id" value="">
    <input type="hidden" name="delete_user_email" id="delete_user_email" value="">
</form>

<!---------------->
<div id="responsive" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="width:800px!important;" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>

                <!----->
                <style>         
                    input[type=checkbox]{
                        position: relative;
                        top: 2px; 
                    }
                    .left_task{width:35%;float:left;font-weight: bold;margin-top: 0px;}
                    .right_task{width:64%;float:left;}
                    .clear_both{clear:both;height:20px;}
                    .required{color:black;}
                    label.error{color:red;margin-left:10px;}
                    .fieldset_class {
                        border: 1px solid #d14836;
                    }
                    .legend_class {
                        margin-left: 50px;
                        padding: 0 10px;
                        font-weight: bold;
                    }
                    .tblouter select, .tblouter input{width:90%;}
                    .pad_20{ padding: 20px; overflow: hidden;} .sendpwd{position: relative; top: 7px;}
                </style>
                <form id="user_edit_Frm" method="post">
                    <div style="width:48%;float:left;">
                        <fieldset class="fieldset_class">
                            <legend class="legend_class" style="text-align:center;font-weight:bold;">Basic Info</legend>
                            <div style="padding:20px;">                                
                                <div class="left_task">Email:</div>
                                <div class="right_task">
                                    <input type="hidden" id="edit_user_id" value="">
                                    <input disabled="true" type="text" name="user_email" id="user_email" value="" class="required">
                                </div>                                                                                                                                       
                                <div class="clear_both"></div>
                                <div class="left_task">Role:</div>
                                <div class="right_task chosen230">
                                    <select name="role_name" id="role_name" style="height:30px;" class="required chosen">
                                        <option value="">Select Role</option>
                                        <?php
                                        foreach ($all_role as $role_name => $role_info) {
                                            $rol = $role_info['name'];
                                            if(in_array($rol, $exclude_roles)){
                                                continue;
                                            }
                                            ?>
                                            <option <?php if ($edit_user_role_name == $role_name) echo 'selected'; ?> value="<?php echo $role_name; ?>"><?php echo $role_info['name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select> 
                                </div>
                                <!--div class="clear_both"></div>
                                <div class="left_task">Access Level:</div>
                                <div class="right_task">
                                <?php
                                //*
                                $Level = get_user_meta($edit_user_id, "USER_LEVEL", true);
                                if (empty($Level)) {
                                    $Level = 'level_1';
                                }
                                $level = array('level_0' => 'Level 0', 'level_1' => 'Level 1', 'level_2' => 'Level 2', 'level_3' => 'Level 3', 'level_4' => 'Level 4', 'level_5' => 'Level 5');
                                ?>
                                    <select name="USER_LEVEL" style="height:30px;">
                                <?php
                                foreach ($level as $key => $va) {
                                    if ($Level == $key) {
                                        $Chk = 'selected="selected"';
                                    } else {
                                        $Chk = '';
                                    }
                                    ?>
                                                                <option <?php echo $Chk; ?>value="<?php echo $key; ?>"><?php echo level_name($key); ?></option>
<?php } ?>
                                    </select>
                                </div-->
                                <div class="clear_both"></div>
                                <div class="left_task">First Name:</div>
                                <div class="right_task">
                                    <input type="text" name="first_name" id="first_name" value="" class="required">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">Last Name:</div>
                                <div class="right_task">
                                    <input type="text" name="last_name" id="last_name" value="" class="required">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">Website:</div>
                                <div class="right_task">
                                    <input type="text" name="website" id="website" value="">
                                </div>
                                <div class="clear_both"></div>
                                
                                
                                <?php if(defined('agency_full_content') && agency_full_content == 1): ?>
                                <div class="left_task">Tag:</div>
                                <div class="right_task" id="user_email_div">
                                    <label><input type="checkbox" name="iswriter" value="3"> Enable Writer Panel</label>                                    
                                </div>
                                <div class="clear_both"></div>
                                <?php endif; ?>
                                
                                
                            </div>
                        </fieldset>   
                    </div>
                    <div style="width:48%;float:right;">
                        <fieldset class="fieldset_class">
                            <legend class="legend_class" style="text-align:center;font-weight:bold;">Billing / Invoicing Info</legend>
                            <div style="padding:20px;">
                                <div style="display: none;">
                                    <div class="left_task">Brand Name:</div>
                                    <div class="right_task">
                                        <input type="text" name="BRAND_NAME" id="BRAND_NAME" value="">
                                    </div>
                                </div>
                                <div class="clear_both"></div> 
                                <div class="left_task">Phone Number:</div>
                                <div class="right_task">
                                    <input type="text" name="phonenumber" id="phonenumber" value="">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">Street Address:</div>
                                <div class="right_task">
                                    <input type="text" name="streetaddress" id="streetaddress" value="">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">City:</div>
                                <div class="right_task">
                                    <input type="text" name="city" id="city" value="">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">State:</div>
                                <div class="right_task">
                                    <input type="text" name="state" id="state" value="">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">Zip:</div>
                                <div class="right_task">
                                    <input type="text" name="zip" id="zip" value="">
                                </div>
                                <div class="clear_both"></div>
                            </div>  
                        </fieldset>
                        
                        
                    </div>  
                    <div class="clear_both"></div>
                    <div>
                        <fieldset class="fieldset_class">
                            <legend class="legend_class" style="text-align:center;font-weight:bold;">Change Password</legend>
                            <div class="pad_20">
                                <div class="left_task">Password:</div>
                                <div class="right_task">
                                    <input type="password" name="pwd" id="pwd" value="">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">Confirm password:</div>
                                <div class="right_task">
                                    <input type="password" name="cpwd" id="cpwd" value="">
                                </div>
                                <div class="clear_both"></div>
                                <div class="left_task">Password Link:</div>
                                <div class="right_task">
                                    <a class="sendpwd" href="javascript:;">Send Password Reset Link</a>
                                </div>
                            </div>
                            
                        </fieldset>
                        
                    </div>
                    
                    <div class="clear_both"></div>
                    <input style="float: right;margin-right: 30px;width:100px;" type="button" onclick="submit_edit_user()" class="new_btn_class" name="user_edit_submit_btn" value="Update User">
                </form>
                <div class="clear_both"></div>
                <!----->
            </div>
        </div>
    </div>
</div>


<div id="locmodal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><strong>Assign Location</strong></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" class="form-horizontal" method="post">
                            <input type="hidden" id="uid" name="uid" value="" />
                            <?php if(empty($locations)){ ?>
                                <div class="alert alert-danger">
                                    <strong>Note : </strong>
                                    You have not added any location for this agency. Please add new location or existing location before assignment.
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Select Location</label>
                                <div class="col-md-8">
                                    <select required class="form-control chosen-select" name="locationname" id="locationname">
                                        <option value="">Select Location</option>
                                        <?php if(!empty($locations)){ ?>
                                            <option value="add_all_locations">Add All Locations</option>
                                        <?php } ?>
                                        <?php
                                            foreach ($locations as $location) {
                                                $website = get_user_meta($location->MCCUserId,'BRAND_NAME',TRUE);
                                                ?>
                                                <option value="<?php echo $location->id; ?>"><?php echo $website; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>                                    
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-3 col-md-offset-4">
                                    <input type="button" class="btn btn-success" style="background:none" id="btn_assign_location" value="Assign">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>        
</div>


<div id="adduser_dialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New User</h4>
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
                    
                    

                    <div class="left_task">Role:</div>

                    
                <select name="people_user_role" class="required form-control chosen-select selectrole">

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
                    
                    <?php if(defined('agency_full_content') && agency_full_content == 1): ?>
                    <div class="left_task">Tag:</div>
                    
                    <div class="right_task" id="user_email_div">
                        <label><input type="checkbox" name="is_writer" value="3"> Enable Writer Panel</label>
                        <div class="clear_both"></div>
                    </div>
                    <?php endif; ?>
                     <div class="left_task">Select Location:</div>
                     <div class="right_task" id="locationassign">
                     <select required class="form-control chosen-select" name="locationassign" id="locationassign">
                            <option value="">Select Location</option>
                            <?php
                                foreach ($locations as $location) {
                                    $website = get_user_meta($location->MCCUserId,'BRAND_NAME',TRUE);                                    
                                    ?>
                                    <option value="<?php echo $location->id; ?>"><?php echo $website; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="clear_both"></div>
                    <div class="right_task">
                        <div class="col-lg-4">
                            <input onclick="jQuery('#people_add_Frm').submit();" style="color:white;" type="submit" class="btn new_btn_class" value="Add User">
                        </div>
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
    
    jQuery(window).bind('load',function(){        
        DoubleScroll(document.getElementById('tblouter'));
    });
    
    
    function role_change_func(role_name) {
        document.location.href = '<?php echo site_url(); ?>/user-control/?type=' + role_name;
    }

    function user_status_change(user_status_change_id, email) {
        var current_user_status = jQuery('#current_user_status_' + user_status_change_id).val();
        if (current_user_status == 'Active') {
            var user_change_text = 'Make Active';
            var user_status_col_text = 'Inactive';
        } else {
            var user_status_col_text = 'Active';
            var user_change_text = 'Make Inactive';
        }
        var con = confirm('Are you sure ' + email + ' user will be ' + user_status_col_text + '?');
        if (con) {
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo site_url(); ?>/ajax-data.php',
                data: {'page': 'user_control', 'type': 'user_status_change', 'user_status_change_id': user_status_change_id, 'current_user_status': current_user_status},
                success: function(html_data)
                {

                    jQuery('#current_user_status_' + user_status_change_id).val(user_status_col_text);
                    jQuery('#user_status_col_' + user_status_change_id).html(user_status_col_text);
                    jQuery('#user_change_text_' + user_status_change_id).html(user_change_text);
                    alert(html_data);
                }
            });
        }
    }

    function delete_user_func(delete_user_id, delete_user_email) {
        var con = confirm("Are you sure to delete " + delete_user_email + "?");
        if (con) {
            jQuery('#delete_user_id').val(delete_user_id);
            jQuery('#delete_user_email').val(delete_user_email);
            document.forms.delete_user_Frm.submit();
        }
    }  


    jQuery(document).ready(function() {
        jQuery('.tabl1').dataTable({
            "order": [[0, "asc"]],
            "iDisplayLength": 25

        });
        
        jQuery('#user_edit_Frm').validate();
    });
    function user_edit_func(edit_user_id, edit_user_email) {
        
        jQuery('#responsive').modal();
        jQuery('#edit_user_id').val(edit_user_id);        
        jQuery('#user_email').val(edit_user_email);
        jQuery('#username').val(jQuery('#list_username_' + edit_user_id).html());
        jQuery('#first_name').val(jQuery('#list_first_name_' + edit_user_id).html());
        jQuery('#last_name').val(jQuery('#list_last_name_' + edit_user_id).html());
        jQuery('#website').val(jQuery('#list_website_' + edit_user_id).html());
        jQuery('#role_name').val(jQuery('#list_role_name_' + edit_user_id).html());
        jQuery('#BRAND_NAME').val(jQuery('#list_brand_name_' + edit_user_id).html());
        jQuery('#phonenumber').val(jQuery('#list_phonenumber_' + edit_user_id).html());
        jQuery('#streetaddress').val(jQuery('#list_streetaddress_' + edit_user_id).html());
        jQuery('#city').val(jQuery('#list_city_' + edit_user_id).html());
        jQuery('#state').val(jQuery('#list_state_' + edit_user_id).html());
        jQuery('#zip').val(jQuery('#list_zip_' + edit_user_id).html());  
        
        if(jQuery.trim(jQuery('#is_writer_' + edit_user_id).html().toLowerCase()) == 'writer'){
            jQuery('input[name=iswriter]').prop('checked',true);
        }
        else{
            jQuery('input[name=iswriter]').prop('checked',false);
        }
        
        
        
        chosen_reinitilize();
    }

    function submit_edit_user() {
        var user_email = jQuery('#user_email').val();
        var edit_user_id = jQuery('#edit_user_id').val();
        var first_name = jQuery('#first_name').val();
        var last_name = jQuery('#last_name').val();
        var website = jQuery('#website').val();
        var role_name = jQuery('#role_name').val();
        var BRAND_NAME = jQuery('#BRAND_NAME').val();
        var phonenumber = jQuery('#phonenumber').val();
        var streetaddress = jQuery('#streetaddress').val();
        var city = jQuery('#city').val();
        var state = jQuery('#state').val();
        var zip = jQuery('#zip').val();
        
        var pwd = jQuery('#pwd').val();
        var cpwd = jQuery('#cpwd').val();
        if(pwd != ''){
            if(pwd != cpwd){
                alert('Please enter same password again!');
                return false;
            }
        }
        
        var state = jQuery('#state').val();
        var zip = jQuery('#zip').val();
        

        if (first_name == '') {
            alert('Please enter first name!');
            return false;
        }
        if (last_name == '') {
            alert('Please enter last name!');
            return false;
        }
        if (role_name == '') {
            alert('Please enter role!');
            return false;
        }

        jQuery('.close').click();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl+"?param=user_edit&action=settings_lib",
            data: {
                'page': 'user_control',
                'type': 'user_edit',
                'edit_user_id': edit_user_id,
                'first_name': first_name,
                'last_name': last_name,
                'website': website,
                'role_name': role_name,
                'BRAND_NAME': BRAND_NAME,
                'phonenumber': phonenumber,
                'streetaddress': streetaddress,
                'city': city,
                'state': state,
                'zip': zip,
                'pwd': pwd,
                'cpwd': cpwd
            },
            success: function(html_data)
            {
                jQuery('#full_name_' + edit_user_id).html(first_name + ' ' + last_name);
                //jQuery('#BRAND_NAME_' + edit_user_id).html(BRAND_NAME);

                jQuery('#list_first_name_' + edit_user_id).html(first_name);
                jQuery('#list_last_name_' + edit_user_id).html(last_name);
                jQuery('#list_website_' + edit_user_id).html(website);
                jQuery('#list_role_name_' + edit_user_id).html(role_name);
                jQuery('#list_phonenumber_' + edit_user_id).html(phonenumber);
                jQuery('#list_streetaddress_' + edit_user_id).html(streetaddress);
                jQuery('#list_city_' + edit_user_id).html(city);
                jQuery('#list_state_' + edit_user_id).html(state);
                jQuery('#list_zip_' + edit_user_id).html(zip);
                jQuery('#list_brand_name_' + edit_user_id).html(BRAND_NAME);
                //alert(html_data);
                jQuery('#pwd').val(''); jQuery('#cpwd').val('');
                alert('Successfully updated  ' + user_email);
            }
        });
    }
    
</script>