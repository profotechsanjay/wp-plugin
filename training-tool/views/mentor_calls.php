<?php
include_once 'common.php';

$args = array(	
	'role'         => '',	
	'fields'       => 'all'	
 ); 

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->data->ID;

//$users = get_users( $args );

        ?>

        <div class="main-section singlepagecourse detailprogress">

            <div class="col-sm-12">

                <h4>Mentors</h4>
                
                <a href="admin.php?page=manage_mentor_calls&user_id=<?php echo $user_id; ?>" class="menotrcall btn btn-primary ">Manage Calls</a>
            </div>

        </div>
