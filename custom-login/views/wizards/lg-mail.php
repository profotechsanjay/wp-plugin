<?php if (empty(get_user_meta($user_id, "user_email_status", true))) { ?>
                <div class="panel panel-default mail-section" id="confirm">
                    <form class="form-horizontal app-form H_hundredPercent" id="confirm-email">
                        <div class="icon-label text-center">
                            <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-mail.png" alt="icon-mail"/>
                        </div>
                        <div class="inner-section col-md-12 text-center pad_top_10">
                            <h3 class="mont-font cap-text normal font_24 m-b-0">Email Confirmation</h3>
                            <p class="font-14">Please confirm your email address to get started <br/><b><?php echo $current_user->user_email; ?></b>.<br/><a href='javascript:;' data-toggle="modal" data-target="#change-email-modal" class="btn btn-link pad_0">change email</a></p>
                        </div>
                        <input type="hidden" value="<?php echo $current_user->ID; ?>" name="uid" id="uid"/>
                        <input type="hidden" value="<?php echo $current_user->user_email; ?>" name="uemail" id="uemail"/>
                        <div class="button-section pad_bottom_15">
                            <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="submit">Send Confirmation</button>
                        </div>
                    </form>
                </div>
            <?php } ?>

            <?php if (!empty(get_user_meta($user_id, "user_email_status", true))) { ?> 
                <div class="panel panel-default mail-section" id="confirmed">
                    <form class="form-horizontal app-form H_hundredPercent" id="email-confirmed">
                        <div class="icon-label text-center">
                            <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-mail.png" alt="icon-mail"/>
                        </div>
                        <div class="inner-section col-md-12 text-center pad_top_10">
                            <h3 class="mont-font cap-text normal font_24 m-b-0">Email Confirmation</h3>
                            <p class="font-14">Email<br/><strong><?php echo $current_user->user_email; ?></strong><br/> has been confirmed.</p>
                        </div>
                        <div class="button-section pad_bottom_15 text-center">
                            <div class="round-div"><i class="fa fa-check"></i></div>
                        </div>
                    </form>
                </div>

            <?php } ?>
