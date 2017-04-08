<?php
include_once 'common.php';
global $wpdb;

?>
<div class="contaninerinner">         
    <h4>Locations</h4>
    <div class="panel panel-primary">        
        <div class="panel-heading">Location Info</div>
        <div class="panel-body">
            <?php
            require 'payment_for_locations.php';  //This section active if agency Not pay initial payment for Locations
            ?>
        </div>
    </div>
</div>