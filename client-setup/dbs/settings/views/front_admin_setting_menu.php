<?php
global $wpdb;
$c_id = get_current_user_id();
$user = new WP_User($c_id);
$u_role =  $user->roles[0];
if($u_role == 'administrator' || administrator_permission()): ?>
<li><a href="<?php echo site_url() . '/' . ST_LOC_PAGE; ?>"><i class="icon-settings "></i> Admin Settings</a></li>
<!--<li><a href="<?php echo site_url() . '/' . ST_LOC_PAGE."?parm=master-user-list"; ?>"><i class="icon-user "></i> Master User List</a></li>
<li><a href="<?php echo site_url() . '/wp-admin/index.php'; ?>"><i class="icon-settings "></i> WP Admin Area</a></li>-->
<?php endif; ?>