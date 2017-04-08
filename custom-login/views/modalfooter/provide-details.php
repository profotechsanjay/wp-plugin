 <!-- Modal -->
<div id="profile-modal-new" class="modal inmodal fade detail" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detail</h4>
            </div>
            <div class="modal-body pad_25 radius-bottom p_inherit">
                <form action="#" method="post" id="show-info" name="show-info" class="form-horizontal app-form">
                   

                        <div class="form-group m-b-10">
                            <label for="name" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5 pad_top_0 ">Email</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <p><b><?php echo $current_user->user_email; ?></b></p>
				<input type="hidden" name="email" id="email" value="<?php echo $current_user->user_email; ?>"/>
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label for="name" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5">Name</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <input type="text" required class="form-control" value="" id="name" name="name" placeholder="Client Name">
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label for="login" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5 hundredPercent">Phone No</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <!--input type="text" required class="form-control" id="login" name="login" placeholder="Enter Phone No"-->
                                <input required class="form-control hundredPercent"  name="phone" value="" placeholder="Enter Phone No" id="phone" type="tel">
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5" for="country">Country</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <select name="country" required id="country" class="form-control">
                                     <option value="-1">Choose Country</option>
                                     <?php 
					foreach ($countries as $code=>$value) {
								
									    ?>
									    <option value="<?php echo $value->code; ?>"><?php echo $value->title; ?></option>
						            <?php
						        }
					?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5" for="clients">No. of Clients</label>
                            <div class="col-md-12 col-xs-12 text_field font_13">
                                <select name="clients" required id="clients" class="form-control">
                                    <?php
                                    $clientsArray = array("1-10", "11-20", "21-30");
                                    $clients = get_user_meta($current_user->ID, "user_clients", true);
                                    foreach ($clientsArray as $data) {
                                        $selected = '';
                                        if ($data == $clients) {
                                            $selected = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $data; ?>" <?php echo $selected; ?>><?php echo $data; ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="userid" name="userid" value="<?php echo $user_id; ?>"/>
                        




                        <div class="form-group m-b-0 bottom-footer radius-bottom">
                        <div class="col-md-12">

                            <button type="submit" name="btn-edit-camp" class="btn btn-success">Save</button>

                        </div>
                    </div>





                    
                </form>
            </div>
        </div>

    </div>
</div>
