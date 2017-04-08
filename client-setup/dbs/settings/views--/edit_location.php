<?php

include_once 'common.php';
global $wpdb;

$base_url = site_url();


$locations = $wpdb->get_results
    (
    $wpdb->prepare
            (
            "SELECT * FROM " . client_location()." ORDER BY created_dt DESC",""
    )
);


?>
<div class="contaninerinner">         
    <h4>Locations</h4>
    <div class="pull-right">
        <a href="<?php echo ST_LOC_PAGE; ?>?parm=new_location" class="btn btn-success">Add New Location</a>
        <a href="<?php echo ST_LOC_PAGE; ?>?parm=assign_locations" class="btn btn-warning">Assign Locations</a>
    </div>
    <div class="panel panel-primary">        
        <div class="panel-heading">Location Info</div>
        <div class="panel-body">            
            <table class="table table-bordered table-striped table-hover" id="data_location" >
                <thead>
                    <tr>
                        <th style="width: 6%;">SNo</th>
                        <th style="width: 20%;">Location Name</th>
                        <th style="width: 25%;">Address</th>                        
                        <th style="width: 20%;">Date</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($locations as $location) {
                            $i = $i++;                                            
                        ?>
                            <tr class="rowmod" data-id="<?php echo $location->id; ?>">
                                <td><?php echo $i; ?></td> 
                                <td><?php echo $location->location_name; ?></td>
                                <td><?php echo $location->address; ?></td>                                                                
                                <td><?php echo date("Y-m-d",  strtotime($location->created_dt)); ?></td>                            
                                <td class="actiontd acttd">
                                    <div>
                                    <a data-id="<?php echo $location->id; ?>" href="page=edit_location&course_id=<?php echo $location->id; ?>" class="btn new_btn_class" title="Edit Location">Edit</a>                                    
                                    <a href="javascript:;" data-id="<?php echo $location->id; ?>" title="Delete Location" class="deletecou btn btn-danger">Delete</a>                                    
                                </td>
                            </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            

        </div>
    </div>


</div>
