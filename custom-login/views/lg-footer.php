<?php
/* Country List */
include_once LG_COUNT_PLUGIN_DIR . '/library/lg-country-list.php';
?>
<style>
    .disabledTab {
        pointer-events: none;
    }
</style>

<!-- Change Email -->
<div id="change-email-modal" class="modal fade inmodal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/change-email.php'; ?> 
    </div>
</div>
<!-- Modal -->

<div class="modal fade inmodal " id="create-campaign-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content light-bg">
            <div class="modal-header bg-white radius border-width text-center pad_0 pad_top_15">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-campain.jpg" alt="compain-image" />
                <h4 class="modal-title text-center mont-font pad_bottom_25 font-18">Create Campaign</h4>
                <div class="compaign-nav">
                    <ul class="nav nav-tabs">
                        <li class="active col-md-4 pad_0" id="campaign-tab1">
                            <a data-toggle="tab" href="#home" id="tabmenu0">Company Info<!--span class="tab_click"><i class="fa fa-check"></i></span--></a>
                        </li>
                        <?php //pr($_SESSION);?>
                        <?php if (!empty($_SESSION['key_exist'])) { ?>           
                            <li class="col-md-4 pad_0 " id="campaign-tab2">
                                                        <a data-toggle="tab" href="#menu1">keywords<!--span class="tab_click"><i class="fa fa-check"></i></span--></a> 

                            <?php } else { ?>
                            <li class="col-md-4 pad_0 disabledTab" id="campaign-tab2">
                                                        <a data-toggle="tab" href="#menu1">keywords <!--span class="tab_click"><i class="fa fa-check"></i></span--></a> 
                            </li>         
                        <?php } ?>
                        <?php if (!empty($_SESSION['integrationed'])) { ?>

        <li class="col-md-4 pad_0 " id="campaign-tab3"><a data-toggle="tab" href="#menu2" id="tabmenu2">Connect GA<!--span class="tab_click"><i class="fa fa-check"></i></span--></a></li>
                        <?php } else { ?>
        <li class="col-md-4 pad_0 disabledTab" id="campaign-tab3"><a data-toggle="tab" href="#menu2" id="tabmenu2">Connect GA<!--span class="tab_click"><i class="fa fa-check"></i></span--></a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['integration'])) { ?>
                            <li class="col-md-4 pad_0" id="campaign-tab4"><a data-toggle="tab" href="#menu3">Citations<!--span class="tab_click"><i class="fa fa-check"></i></span--></a></li>
                        <?php } else { ?>
        <li class="col-md-4 pad_0 disabledTab" id="campaign-tab4"><a data-toggle="tab" href="#menu3">Citations<!--span class="tab_click"><i class="fa fa-check"></i></span--></a></li>
                        <?php } ?>
                        <?php if (!empty($_SESSION['citation'])) { ?>
                            <li class="col-md-4 pad_0" id="campaign-tab5"><a data-toggle="tab" href="#menu4">Competitor<!--span class="tab_click"><i class="fa fa-check"></i></span--></a></li>
                        <?php } else { ?>
        <li class="col-md-4 pad_0 disabledTab" id="campaign-tab5"><a data-toggle="tab" href="#menu4">Competitor<!--span class="tab_click"><i class="fa fa-check"></i></span--></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>


            <div class="modal-body pad_25 radius-bottom p_inherit">
                <div class="tab-content">


                    <div id="home" class="tab-pane fade in active">
                        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/new-location.php'; ?>

                    </div>


                    <div id="menu1" class="tab-pane fade">
                        <p><b>Note* : Press Ctrl and click on keyword to select more than 1 keyword.</b></p>
                        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/keywords.php'; ?>
                    </div>


                    <div id="menu2" class="tab-pane fade">
                   
                        <?php 
                   ob_start();
                  $file = LG_COUNT_PLUGIN_DIR . '/views/modalfooter/gaconnent.php';
                  include_once $file;
                   
                  
  ?>

                 
                    </div>


                    <div id="menu3" class="tab-pane fade">

                        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/citation.php'; ?>

                    </div>


                    <div id="menu4" class="tab-pane fade">
                        <?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/competitor.php'; ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="delete-campaign" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="height:auto !important;">
                <form id="camp-del-succ">
                    <div class="form-group">
			<input type="hidden" name="lc_id" id="lc_id"/><input type="hidden" id="delete_id"/>
                        <label for="txtConfirm"><b>To Delete Campaign , Type 'DELETE' and hit submit</b></label>
                        <input type="text" class="form-control" required name="txtConfirm" id="txtConfirm" placeholder="Enter DELETE">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/edit-campaign.php'; ?>

<?php include_once LG_COUNT_PLUGIN_DIR . '/views/modalfooter/provide-details.php'; ?>

