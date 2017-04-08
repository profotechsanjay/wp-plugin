<style>
  #campaign-citation label.error{ color:red; }
  #campaign-citation{overflow-y: auto;height: 43vh;overflow-x: hidden;}
</style>

<form class="form-horizontal" id="campaign-citation">
    <div class="form-group m-b-0">
        <input type="hidden" value="<?php echo $current_user->ID; ?>" name="usid" id="usid"/>
        <input type="hidden" value="<?php echo $current_user->user_email; ?>" name="useremail" id="useremail"/>
        <input type="hidden" value="<?php echo $current_user->display_name; ?>" name="uname" id="uname"/>
        <input type="hidden" value="<?php echo get_user_meta($current_user->ID, 'user_phone', true) ?>" name="userphone" id="userphone"/>


        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" id="rprt_name" value="<?php echo isset($_SESSION['general']['brand']) ? $_SESSION['general']['brand'] : ''; ?>" required  name="rprt_name" placeholder="Business Name">
        </div>

        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" id="bus_name" value="<?php echo isset($_SESSION['general']['website']) ? $_SESSION['general']['website'] : ''; ?>" required  name="bus_name" placeholder="Website URL">
        </div>

    </div>

    <div class="form-group m-b-0">

        <div class="col-md-6 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" id="pr_prod" required name="pr_prod" placeholder="Primary Product/Service Keyword">
        </div>

        <div class="col-md-6 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" id="uphoneNo" required   name="uphoneNo" placeholder="Phone Number">
        </div>

    </div>

    <div class="form-group m-b-0">

        <div class="col-md-12 col-xs-12 pad_bottom_10">
            <select class="form-control h-auto font_13 country_location" name="country_citation" id="country_citation">
                <?php
                $clients = $_SESSION['general']['country'];
                foreach ($countries as $code => $value) {
                    $selected = '';
                    if ($value->code == $clients && !empty($value->code)) {
                        $selected = "selected";
                    }
                    ?>
                    <option value="<?php echo $value->code; ?>" <?php echo $selected; ?>><?php echo $value->title; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>

    </div>
    <div class="form-group m-b-0">

    </div>

    <div class="form-group m-b-0">
        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" required="" value="<?php echo isset($_SESSION['general']['city']) ? $_SESSION['general']['city'] : ''; ?>" id="geolocation_citation" name="geolocation_citation" placeholder="Geolocation/City">
        </div>
    </div>

    <div class="form-group m-b-0">

        <div class="col-md-6 col-xs-12 pad_bottom_10">
            <input type="text" class="form-control h-auto font_13" value="<?php echo isset($_SESSION['general']['street']) ? $_SESSION['general']['street'] : ''; ?>" id="street_addr" name="street_addr" required="" placeholder="Street Address">
        </div>

        <div class="col-md-6 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" value="<?php echo isset($_SESSION['general']['zipcode']) ? $_SESSION['general']['zipcode'] : ''; ?>" required="" id="zipcode_addr" name="zipcode_addr" placeholder="Zip Code">
        </div>

    </div>

    <div class="form-group m-b-0 bottom-footer radius-bottom">
        <div class="col-md-12">
            <!--a data-toggle="tab" id="tab-item4" href="" class="btn btn-success">Run Citation Report</a-->
            <button type="submit" class="btn btn-success"><i class="fa fa-location-arrow" aria-hidden="true"></i> Submit</button>
            <a data-toggle="tab" id="esc-tab-item4" href="#menu4" class="btn btn-danger">Skip <i class="fa fa-caret-right" aria-hidden="true"></i></a>

        </div>
    </div>

</form>
