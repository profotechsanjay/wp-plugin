<form class="form-horizontal" id="campaign-competitor">

                            <div class="form-group m-b-0">
                                <input type="hidden" value="<?php echo $current_user->ID; ?>" name="usid" id="usid"/>
                                <input type="hidden" value="<?php echo $current_user->user_email; ?>" name="useremail" id="useremail"/>
                                <input type="hidden" value="<?php echo $current_user->display_name; ?>" name="uname" id="uname"/>
                                <input type="hidden" value="<?php echo get_user_meta($current_user->ID, 'user_phone', true) ?>" name="userphone" id="userphone"/>
                            </div>

                            <?php for ($row = 1; $row <= 3; $row++) { ?>

                                <div class="form-group m-b-0">

                                    <div class="col-md-12 col-xs-12 pad_bottom_10">
                                        <label class="control-label m-b-5 f-bold" for="<?php echo 'compurl_' . $row; ?>">Competitor URL <?php echo $row; ?>:</label>
                                        <input type="url" required class="form-control" class="validurl" id="<?php echo 'compurl_' . $row; ?>" name="<?php echo 'compurl_' . $row; ?>" placeholder="Enter Competitor URL <?php echo $row; ?>">
                                    </div>
                                </div>

                            <?php } ?>

                            <div class="form-group m-b-0 bottom-footer radius-bottom">
                                <div class="col-md-12">
                                    <!-- <a href="<?php echo site_url() ?>/dashboard/" id="esc-tab-5" class="btn btn-danger" data-id="<?php echo $current_user->ID;?>">Skip</a>-->
<a data-toggle="tab" id="tab-item5" class="btn btn-success" data-id="<?php echo $current_user->ID;?>"><i class="fa fa-location-arrow" aria-hidden="true"></i> Submit</a>
 <a  on_click="" id="esc-tab-5" class="btn btn-danger" data-id="<?php echo $current_user->ID;?>">Skip <i class="fa fa-caret-right" aria-hidden="true"></i></a>
                                    
                                </div>
                            </div>


                        </form>

