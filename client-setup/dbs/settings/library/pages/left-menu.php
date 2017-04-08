<?php

function count_order($status,$writer_id) {
    global $wpdb;
    if($status == 'all'){
       return $wpdb->get_row("SELECT count(*) as total_order FROM wp_content_order WHERE writer_id = $writer_id")->total_order; 
    } else {
    return $wpdb->get_row("SELECT count(*) as total_order FROM wp_content_order WHERE status = '" . $status . "'  and writer_id = $writer_id")->total_order;
    }
}
?>

<div class="accordion">
    <div class="accoSet">
        <ul>
            
            <li><a href="<?php echo site_url(); ?>/content-admin?type=Ordered">New Order(<?php echo count_order('Ordered',$writer_id); ?>)</a></li>
            <li><a href="<?php echo site_url(); ?>/content-admin?type=Delivered">Delivered Order(<?php echo count_order('Delivered',$writer_id); ?>)</a></li>
            <li><a href="<?php echo site_url(); ?>/content-admin?type=Approved">Approved Order(<?php echo count_order('Approved',$writer_id); ?>)</a></li>
            <li><a href="<?php echo site_url(); ?>/content-admin?type=Request_Changes">Request Changes(<?php echo count_order('Request Changes',$writer_id); ?>)</a></li>
            <li><a href="<?php echo site_url(); ?>/content-admin?type=all-order">All Order(<?php echo count_order('all',$writer_id); ?>) </a></li>
        </ul>
    </div>
</div>