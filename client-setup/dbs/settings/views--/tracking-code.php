<?php

include_once 'common.php';
include_once ABSPATH. '/wp-content/themes/twentytwelve/analytics/CommonUtils.php';
global $wpdb;

$base_url = site_url();
$locations = $wpdb->get_results
(
    $wpdb->prepare
    (
            "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC",""
    )
);

?>

<div class="contaninerinner trackdiv">     
    <h4>Tracking Code </h4>
    <div class="panel panel-primary">
        <div class="panel-heading">Tracking Code</div>
        <div class="panel-body">
            <form action="#" class="form-horizontal" method="post">

                <div class="form-group">
                    <label class="col-md-3 control-label">Select Location (Account)</label>
                    <div class="col-md-6">
                        <select required class="form-control chosen" name="tackcodechange" id="tackcodechange">
                            <option value="">Select Location (Account)</option>
                            <?php
                            foreach ($locations as $location) {
                                $id_loc = intval($_REQUEST['location_id']);
                                $sel = '';
                                if($id_loc == $location->id){
                                    $sel = 'selected="selected"';
                                }
                                $brand = get_user_meta($location->MCCUserId, 'BRAND_NAME', TRUE);
                                if (empty($brand)) {
                                    $brand = get_user_meta($location->MCCUserId, 'company_name', TRUE);
                                }
                                ?>
                                <option <?php echo $sel; ?> value="<?php echo $location->id; ?>"><?php echo $brand; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                <div class="row"><hr/></div>
                <div class="trackingcode">
                    <?php if(isset($_REQUEST['location_id']) && intval($_REQUEST['location_id']) > 0){
                        ?>                       
                            <?php

                            $idloc = intval($_REQUEST['location_id']);
                            $loc = $wpdb->get_row
                            (
                                $wpdb->prepare
                                (
                                        "SELECT MCCUserId,conv_verified FROM " . client_location() . " WHERE id = %d",$idloc
                                )
                           );
                            
                           if(!empty($loc)){
                                $is_trackverified = $loc->conv_verified;
                                $locbrand = get_user_meta($loc->MCCUserId, 'BRAND_NAME', TRUE);
                                $locwebsite = get_user_meta($loc->MCCUserId, 'website', TRUE);
                                $ConvTrackJSCode = GetConvTrackJSCodeForClient($loc->MCCUserId);
                                ?>
                                <h4><?php echo $locbrand; ?> <a class="btn btn-primary pull-right copycode" href="javascript:;">Copy To Clipboard</a> </h4>

                                <textarea id="copyTarget" rows="18" class="form-control"><?php echo trim(htmlspecialchars($ConvTrackJSCode)); ?></textarea>

                                <div class="clearfix"></div>
                                <div>                                        
                                    <a href="javascript:;" data-web='<?php echo $locwebsite; ?>' data-location='<?php echo $idloc; ?>' class="btn <?php echo $is_trackverified == 0?'btn-red':'btn-green'; ?> whitecol verificode" ><?php echo $is_trackverified == 0?'Verify Code Working On':'Verified !!.. Re-verify Code Working On'; ?> <?php echo $locwebsite; ?></a>
                                </div>
                                <div class="hidden"><iframe class="framediv" id="framediv"></iframe></div>
                                <?php
                           }
                            else {
                                ?>
                                <div class="centerlocmsg">Invalid Location</div>
                                <?php
                            }

                            ?>                           
                        <?php                        
                        
                    } else {
                        ?>
                        <div class="centerlocmsg">No Location Selected</div>
                        <?php
                    } ?>
                </div>
                    
            </form>


        </div>
    </div>


</div>
