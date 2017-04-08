<?php
session_start();
global $wpdb;
?>
<style>
  #campaign-keywords h4{ font-size: 2rem !important; }
  #campaign-keywords h4 span{ font-weight:bold; }
</style>
<form class="form-horizontal" id="campaign-keywords">
    <div class="form-group">

        <div class="col-md-12">
            <input type="hidden" name="mccuserid" id="mccuserid" value="<?php echo isset($_SESSION['general']['mcc_userid']) ? $_SESSION['general']['mcc_userid'] : '16655'; ?>"/>
            <input type="hidden" name="brandName" id="brandName"/>
            <select multiple='multiple' id='choosekeywords' name="choosekeywords[]" size="10">
                <?php
                if (isset($_SESSION['keywords'])) {
                    foreach ($_SESSION['keywords'] as $key => $value) {
                        ?>
                        <option value="<?php echo $value->keyword; ?>"><?php echo $value->keyword; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
            <h4 style="display:none" id="key-msg"></h4>
        </div>
    </div>
    <div class="form-group m-b-0 bottom-footer radius-bottom">
        <div class="col-md-12">
            <!--a data-toggle="tab" id="tab-item2"  onclick="" class="btn btn-success">Next</a-->
            <button type="submit" class="btn btn-success">Next <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
        </div>
    </div>

</form>


