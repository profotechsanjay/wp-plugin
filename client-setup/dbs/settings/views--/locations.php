<?php

include_once 'common.php';
global $wpdb;
//$locations_package_prices;  All Prices for location is get by api from main agency Wesbsite and file is /var/www/html/enfusen.com/sunil/wp-content/plugins/settings/get_location_package_prices.php


$base_url = site_url();
$locations = $wpdb->get_results
    (
        $wpdb->prepare
        (
                "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC",""
        )
   );
$available_noof_locations = $wpdb->num_rows;
//$available_noof_locations = '5';

$ar = array();
$arrwebsite = array();

//$number_of_locations = 0;
//$location_package = "pending";
?>

<div class="contaninerinner">  
<?php
  /* $uid= get_current_user_id();
   echo "Locations : ".get_user_meta($uid, 'number_of_locations', true);*/
 ?>       
    <h4>Locations</h4>
    <?php
    //echo $location_package;
    //echo $billing_enable = '1';
    //if(($number_of_locations == 0) && ($location_package == 'pending') && ($billing_enable == 1)){ 

    //$location_package="paid";

    ?>
    
        <div class="pull-right">
            <?php /* if($available_noof_locations < $number_of_locations){ ?>
                <a href="<?php echo ST_LOC_PAGE; ?>?parm=functions&type=location_add" class="btn btn-success">Create New Location</a>        
                <a href="javascript:;" class="btn btn-warning add_exiting_account">Add Existing Location</a> 
            <?php } else { ?>
                <a href="<?php echo ST_LOC_PAGE; ?>?parm=add_paid_location" class="btn btn-success">Pay Now For Add More Locations</a>
            <?php } */ ?>
            <a href="<?php echo ST_LOC_PAGE; ?>?parm=functions&type=location_add" class="btn btn-success">Create New Location</a>
            <a href="javascript:;" class="btn btn-warning add_exiting_account">Add Existing Location</a> 
            <a href="javascript:;" class="btn btn-danger remove_exiting_account">Unassign Location From List</a>        
        </div>
        <div class="panel panel-primary">        
            <div class="panel-heading">Location Info</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover" id="data_location" >
                    <thead>
                        <tr>
                            <th style="width: 6%;">SNo</th>
                            <th style="width: 20%;">Website</th>
                            <th style="width: 18%;">Name</th>
                            <th style="width: 20%;">Date</th>
                            <th style="width: 26%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($locations as $location) {
                            $i = $i + 1;                                                
                            array_push($ar, $location->MCCUserId);                                                
                            $website = get_user_meta($location->MCCUserId,'website',TRUE);

                            $brand = get_user_meta($location->MCCUserId, 'BRAND_NAME',TRUE);
                            if(empty($brand)){
                                $brand = get_user_meta($location->MCCUserId, 'company_name',TRUE);                            
                            }                      
                            $arvals = array('id' => $location->id, 'site' => $brand);
                            array_push($arrwebsite, $arvals);
                            $name = get_user_meta($location->MCCUserId,'BRAND_NAME',TRUE);                        
                            $eurl = ST_LOC_PAGE."?parm=new_location&location_id=" . $location->id;
                            $url = ST_LOC_PAGE."?parm=location_sites&location_id=" . $location->id;
                            $k_url = ST_LOC_PAGE."?parm=keywords&location_id=" . $location->id;
                            $date = date("Y-m-d H:i:s",  strtotime($location->created_dt));
                            if($location->status == 0){
                                $website = $name = '<i>--Draft Created--</i>';                            
                            }

                            ?>
                                <tr class="rowmod" data-id="<?php echo $location->id; ?>">
                                    <td><?php echo $i; ?></td> 
                                    <td><?php echo $website; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $date; ?></td>                            
                                    <td class="actiontd acttd">       
                                        <?php if($location->status == 1){ ?>
                                        <div class="margin-bottom-10">                                        
                                            <a href="<?php echo $eurl; ?>" class="btn new_btn_class" title="Sites">Edit</a>
                                            <a href="<?php echo $url; ?>" class="btn new_btn_class" title="Sites">Sites</a>
                                            <a href="<?php echo $k_url; ?>" class="btn new_btn_class" title="Site Keywords">Site Keywords</a>
                                        </div>
                                        <div>
                                            <a href="<?php echo ST_LOC_PAGE; ?>?parm=assign_users&location_id=<?php echo $location->id; ?>" class="btn new_btn_class" title="Assign Users">Assign Users</a>                                        
                                            <a href="javascript:;" data-id="<?php echo $location->id; ?>" title="Delete Location" class="del_client_loc btn btn-danger">Delete</a>
                                        </div>
                                        <?php } else {
                                            ?>
                                            <a href="<?php echo $eurl; ?>" class="btn new_btn_class" title="Sites">Add Information</a>
                                            <a href="javascript:;" data-id="<?php echo $location->id; ?>" title="Delete Location" class="del_client_loc btn btn-danger">Delete</a>
                                            <?php
                                        }
                                        ?>
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

<?php

$usersacc = new stdClass();
$args = array(    
    'exclude' => $ar,    
    'fields' => 'all',
);
$usersacc = get_users( $args );


?>
<div id="removeaccount" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><strong>Unassign Location</strong></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" class="form-horizontal" method="post">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Select Location (Account)</label>
                                <div class="col-md-8">
                                    <select required class="form-control chosen" name="rmlocationid" id="rmlocationid">
                                        <option value="">Select Location (Account)</option>
                                        <?php
                                            foreach($arrwebsite as $website){
                                               
                                                ?>
                                                <option value="<?php echo $website['id']; ?>"><?php echo $website['site']; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <input type="button" class="btn btn-success" style="background:none" id="btn_remove_location" value="Unassign">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>        
</div>

<div id="existingaccount" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><strong>Add Existing Location</strong></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" class="form-horizontal" method="post">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Select Location (Account)</label>
                                <div class="col-md-8">
                                    <select required class="form-control chosen" name="locationname" id="locationname">
                                        <option value="">Select Location (Account)</option>
                                        <?php
                                            foreach($usersacc as $userac){
                                                $brand = get_user_meta($userac->ID, 'BRAND_NAME',TRUE);                                                
                                                if(empty($brand)){
                                                    $brand = get_user_meta($userac->ID, 'company_name',TRUE);                                                    
                                                }
                                                if(empty($brand)){
                                                    continue;
                                                }                                                
                                                ?>
                                                <option value="<?php echo $userac->ID; ?>"><?php echo $brand; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <input type="button" class="btn btn-success" style="background:none" id="btn_add_location" value="Add">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>        
</div>

