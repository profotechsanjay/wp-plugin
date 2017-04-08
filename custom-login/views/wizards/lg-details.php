<?php if (!empty(get_user_meta($user_id, "user_email_status", true)) && empty(get_user_meta($user_id, "user_profile_status", true))) { ?>
    <div class="col-md-4 col-xs-12 " id="profile-div" style="">
        <div class="user-info">
            <form class="form-horizontal app-form H_hundredPercent" id="show-info-div">
                <div class="icon-label text-center"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/></div>
                <div class="inner-section col-md-12 text-center pad_top_10">
                    <h3 class="mont-font cap-text normal font_24 m-b-0">Provide Your name</h3>
                    <p class="font-14">Please provide your name</p>
                </div>
                <div class="button-section pad_bottom_15">
                    <button id="btn-modal1" data-toggle="modal" data-target="#profile-modal-new" class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="button">Get Started</button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<?php if (empty(get_user_meta($user_id, "user_email_status", true))) { ?>
    <div class="col-md-4 col-xs-12 disabledbutton" id="profile-div" style="">
        <div class="user-info">
            <form class="form-horizontal app-form H_hundredPercent" id="show-info-div">
                <div class="icon-label text-center"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/></div>
                <div class="inner-section col-md-12 text-center pad_top_10">
                    <h3 class="mont-font cap-text normal font_24 m-b-0">Provide Your name</h3>
                    <p class="font-14">Please provide your name</p>
                </div>
                <div class="button-section pad_bottom_15">
                    <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="button">Get Started</button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<?php if (!empty(get_user_meta($user_id, "user_profile_status", true))) { ?>
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

    <div class="col-md-4 col-xs-12 show-me-at" style="display:none">
        <div class="user-info">
            <form class="form-horizontal app-form H_hundredPercent">
                <div class="icon-label text-center animateMe"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/></div>
                <div class="inner-section col-md-12 text-center pad_top_10">
                    <h3 class="mont-font cap-text normal font_24 m-b-0">Provide Your name</h3>
                    <p class="font-14">Hi <b><span id="u_name"></span></b>! Thanks for signing up.</p>
                </div>
                <div class="button-section pad_bottom_15 text-center">
                    <div class="round-div"><i class="fa fa-check"></i></div>
                </div>
            </form>
        </div>
    </div>


