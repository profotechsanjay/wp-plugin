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
<style>
    
.pull-right.divinstr .btn {
    margin: 0px !important;
}
.insth4{
    margin-left: 24px !important;
}

</style>
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
                                <h4><?php echo $locbrand; ?> 
                                    <div class="pull-right divinstr">
                                        <a class="btn btn-danger" onclick="jQuery('.instructionsmodal').modal();" href="javascript:;">Instructions</a>
                                        <a class="btn btn-primary copycode" href="javascript:;">Copy To Clipboard</a> 
                                    </div>
                                </h4>
                                <div class="clearfix"></div>
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


<div class="modal fade instructionsmodal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><strong>How to install the conversion tracking code</strong></h4>
            </div>
            <div class="modal-body">
                <h4 class="insth4">Method 1: To install directly to your website</h4>
                <ul class="list-group">
                    <li class="list-group-item">Step 1 : Copy below code</li>
                    <li class="list-group-item">Step 2 : Open header file Or master file from server (that can be common in all pages) </li>
                    <li class="list-group-item">Step 3 : Paste conversion code just above <?php echo htmlspecialchars('</head>'); ?>tag </li>
                    <li class="list-group-item">Step 4 : Upload and Save file</li>
                    <li class="list-group-item">Step 5 : Verify Code is active</li>
                </ul>
                
                <h4 class="insth4">Method 2: If google tag manager installed on your website</h4>
                <ul class="list-group">
                    <li class="list-group-item">Step 1 : Copy below code</li>
                    <li class="list-group-item">Step 2 : Open Google Tag Manage https://tagmanager.google.com </li>
                    <li class="list-group-item">Step 3 : Add New Tag and Click on Tag Configuration </li>
                    <li class="list-group-item">Step 4 : Choose Custom HTML tag type </li>
                    <li class="list-group-item">Step 5 : Paste conversion code in textarea and click on save</li>
                    <li class="list-group-item">Step 6 : Before saving click on 'Add Trigger' and choose a trigger (All Pages) </li>
                    <li class="list-group-item">Step 7 : Save Tag </li>                    
                    <li class="list-group-item">Step 8 : Verify Code is active</li>
                </ul>
                <div class="insth4">For more info, you can visit : <a href='https://support.google.com/tagmanager/answer/6102821?hl=en'>https://support.google.com/tagmanager/answer/6102821?hl=en</a></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                
            </div>
        </div>
    </div>
</div>