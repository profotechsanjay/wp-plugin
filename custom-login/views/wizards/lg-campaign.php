<?php if (empty(get_user_meta($user_id, "user_campaign_status", true)) && !empty(get_user_meta($user_id, "user_profile_status", true)) && !empty(get_user_meta($user_id, "user_email_status", true))) { ?>
            <div class="col-md-4 col-xs-12" id="campaign-div">
                <div class="mail-section">
                    <div class="icon-label text-center"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-campaign.png" alt="icon-mail"/></div>
                    <div class="inner-section col-md-12 text-center pad_top_10">
                        <h3 class="mont-font cap-text normal font_24 m-b-0">create campaign</h3>
                        <p class="font-14">Create your first campaign !</p>
                    </div>
                    <div class="button-section pad_bottom_15">
                        <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" data-toggle="modal" data-target="#create-campaign-modal" data-controls-modal="create-campaign-modal" data-backdrop="static" data-keyboard="false" type="submit" id="create_campaign">Create Campaign</button>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="col-md-4 col-xs-12 disabledbutton" id="campaign-div">
                <div class="mail-section">
                    <div class="icon-label text-center"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-campaign.png" alt="icon-mail"/></div>
                    <div class="inner-section col-md-12 text-center pad_top_10">
                        <h3 class="mont-font cap-text normal font_24 m-b-0">create campaign</h3>
                        <p class="font-14">Create your first campaign !</p>
                    </div>
                    <div class="button-section pad_bottom_15">
                        <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="submit" >Create Campaign</button>
                    </div>
                </div>
            </div>
        <?php } ?>

<div class="col-md-4 col-xs-12" id="campaign-div-ref" style="display:none;">
                <div class="mail-section">
                    <div class="icon-label text-center"><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-campaign.png" alt="icon-mail"/></div>
                    <div class="inner-section col-md-12 text-center pad_top_10">
                        <h3 class="mont-font cap-text normal font_24 m-b-0">create campaign</h3>
                        <p class="font-14">Create your first campaign !</p>
                    </div>
                    <div class="button-section pad_bottom_15">
                        <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" data-toggle="modal" data-target="#create-campaign-modal" data-controls-modal="create-campaign-modal" data-backdrop="static" data-keyboard="false" type="submit" id="create_campaign">Create Campaign</button>
                    </div>
                </div>
            </div>
