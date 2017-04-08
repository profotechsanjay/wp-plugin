<!-- Edit Modal for Location -->
<div id="location-edit-modal" class="modal fade inmodal agency-location" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Agency - Edit Location</h4>
            </div>
            <div class="modal-body pad_25 radius-bottom p_inherit">
                <form class="form-horizontal" id="campaign-edit">
<div class="form-group m-b-0">
 <div class="col-md-4 col-xs-4 pad_bottom_10"> 
                            <!--select class='form-control h-auto font_13' name="website_ppt" id="website_ppt">
				<option value="http://">http://</option><option value="https://">https://</option>
			    </select-->
                        </div>
                        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
                            <input type="text" val="" class="form-control h-auto font_13" readonly  id="website_ed"
 readonly name="website_edit">
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <input type="hidden" value="<?php echo $current_user->ID; ?>" name="usid" id="usid_edit"/>
                        <input type="hidden"  name="mccuser_edit" id="mccuser_edit"/>
                        <input type="hidden" name="campaign_type" id="campaign_type"/>
                        <input type="hidden" value="<?php echo $current_user->user_email; ?>" name="useremail" id="useremail_edit"/>
                        <input type="hidden" value="<?php echo $current_user->display_name; ?>" name="uname" id="uname_edit"/>
                        <input type="hidden" value="<?php echo get_user_meta($current_user->ID, 'user_phone', true) ?>" name="userphone" id="userphone_edit"/>

                        <div class="col-md-12 col-xs-12 pad_bottom_10">
                            <input type="text" class="form-control h-auto font_13" name="cname_edit" id="cname_edit" required="" placeholder="Company Name">
                        </div>
                    </div>
                    
                    <div class="form-group m-b-0">

                        <div class="col-md-12 col-xs-12 pad_bottom_10">
                            <select class='form-control h-auto font_13' name="country_location_edit" id="country_location_edit">
                                <?php
                                foreach ($country_array as $code=>$value) {
                                    ?>
                                    <option value="<?php echo $code; ?>"><?php echo $value; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-b-0">

                        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
                            <input type="text" class="form-control h-auto font_13" required=""  id="geolocation_edit" name="geolocation" placeholder="Enter Geo Location">
                        </div>
                    </div>
                    <div class="form-group m-b-0">

                        <div class="col-md-12 col-xs-12 pad_bottom_10">
                            <select class='form-control h-auto font_13' name="ctype" id="ctype_edit">

                                <?php
                                $campType = array("Local Campaign", "National Campaign", "Ecommerce Campaign");
                                foreach ($campType as $camp) {
                                   ?>
                                    <option value="<?php echo $camp; ?>"><?php echo $camp; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group m-b-0 bottom-footer radius-bottom">
                        <div class="col-md-12">

                            <button type="submit" name="btn-edit-camp" class="btn btn-success">Submit</button>

                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

