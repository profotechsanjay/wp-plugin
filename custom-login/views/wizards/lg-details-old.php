<div class="col-md-4 col-xs-12 <?php
        if (empty(get_user_meta($user_id, 'user_profile_status', true)) && empty(get_user_meta($user_id, 'user_email_status', true))) {
            echo "disabledbutton";
        }
        ?>" id="profile-div" style="<?php
             if (!empty(get_user_meta($user_id, 'user_profile_status', true)) && !empty(get_user_meta($user_id, 'user_email_status', true))) {
                 echo "display:none";
             }
             ?>">
            <div class="user-info">
                <form class="form-horizontal app-form H_hundredPercent" id="show-info-div">
                    <div class="icon-label text-center"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/></div>
                    <div class="inner-section col-md-12 text-center pad_top_10">
                        <h3 class="mont-font cap-text normal font_24 m-b-0">Provide Your name</h3>
                        <p class="font-14">Please provide your name</p>
                    </div>
                    <div class="button-section pad_bottom_15">
                        <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="<?php
                        if (empty(get_user_meta($user_id, 'user_profile_status', true)) && !empty(get_user_meta($user_id, 'user_email_status', true))) {
                            echo "submit";
                        } else {
                            echo "button";
                        }
                        ?>">Get Started</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty(get_user_meta($user_id, "user_profile_status", true)) && !empty(get_user_meta($user_id, "user_email_status", true))) { ?>
            <div class="col-md-4 col-xs-12">
                <div class="user-info">
                    <form class="form-horizontal app-form H_hundredPercent">
                        <div class="icon-label text-center animateMe"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/></div>
                        <div class="inner-section col-md-12 text-center pad_top_10">
                            <h3 class="mont-font cap-text normal font_24 m-b-0">Provide Your name</h3>
                            <p class="font-14">Hi <b><?php echo $current_user->display_name; ?></b>! Thanks for signing up.</p>
                        </div>
                        <div class="button-section pad_bottom_15 text-center">
                            <div class="round-div"><i class="fa fa-check"></i></div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <div class="col-md-4 col-xs-12" id="profile-div-form" style="display:none;">
            <div class="panel panel-default user-info" id="complete-profile">
                <form action="#" method="post" id="show-info" name="show-info" class="form-horizontal app-form H_hundredPercent">
                    <div class="icon-label text-center">
                        <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/>
                    </div>
                    <div class="inner-section col-md-12 text-center pad_top_10">

                        <div class="form-group m-b-10">
                            <label for="name" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5">Email</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <input type="text" email='true' readonly required value="<?php echo $current_user->user_email; ?>" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label for="name" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5">Name</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <input type="text" required class="form-control" value="<?php echo $current_user->display_name; ?>" id="name" name="name" placeholder="Client Name">
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label for="login" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5 hundredPercent">Phone No</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <!--input type="text" required class="form-control" id="login" name="login" placeholder="Enter Phone No"-->
                                <input required class="form-control hundredPercent"  name="phone" value="<?php echo get_user_meta($current_user->ID, 'user_phone', true); ?>" placeholder="Enter Phone No" id="phone" type="tel">
                            </div>
                        </div>
                        <div class="form-group m-b-10">
                            <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13 m-b-5" for="country">Country</label>
                            <div class="col-md-12 col-xs-12 text_field">
                                <select name="country" required id="country" class="form-control">
                                    <?php
                                    $clients = get_user_meta($current_user->ID, "user_country", true);
                                    foreach ($resultsctr as $data) {
                                        $selected = '';
                                        if ($data['id'] == $clients) {
                                            $selected = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $data['id']; ?>" <?php echo $selected; ?>><?php echo $data['name']; ?></option>
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
                        <div class="button-section pad_bottom_15">
                            <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
