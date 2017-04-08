<form class="form-horizontal" id="campaign-home">
    <div class="form-group m-b-0">
        <input type="hidden" value="<?php echo $current_user->ID; ?>" name="usid" id="usid"/>
        <input type="hidden" value="<?php echo $current_user->user_email; ?>" name="useremail" id="useremail"/>
        <input type="hidden" value="<?php echo $current_user->display_name; ?>" name="uname" id="uname"/>
        <input type="hidden" value="<?php echo get_user_meta($current_user->ID, 'user_phone', true) ?>" name="userphone" id="userphone"/>

        <div class="col-md-12 col-xs-12 pad_bottom_10">
            <input type="text" class="form-control h-auto font_13" id="cname" value="<?php echo isset($_SESSION['general']['brand']) ? $_SESSION['general']['brand'] : ''; ?>" required="" placeholder="Company Name">
        </div>
    </div>
    <div class="form-group m-b-0">
        <!--div class="col-md-2 col-xs-2 pad_bottom_10"> 
            <select class='form-control h-auto font_13' name="web_ppt" id="web_ppt">
                <option value="http://" <?php
        if ($_SESSION['general']['proto'] == "http://") {
            echo "selected";
        }
        ?>>http://</option><option value="https://" <?php
        if ($_SESSION['general']['proto'] == "https://") {
            echo "selected";
        }
        ?>>https://</option>
            </select>
        </div--->
        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" value="<?php echo isset($_SESSION['general']['website']) ? $_SESSION['general']['website'] : ''; ?>" id="website" required="" name="website" placeholder="Enter Website URL with http:// or https://">
        </div>
    </div>
    <!--div class="form-group m-b-0">

        <div class="col-md-12 col-xs-12 pad_bottom_10">
            <select class='form-control h-auto font_13' name="country_location" id="country_location">
               <option value="-1">Choose Country</option>
    <?php
    $clients = $_SESSION['general']['country'];
    foreach ($country_array as $code => $value) {
        $selected = '';
        if ($code == $clients) {
            $selected = "selected";
        }
        ?>
                                                                        <option value="<?php echo $code; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
        <?php
    }
    ?>
            </select>
        </div>
    </div-->

    <!-- Country -->
    <div class="form-group m-b-0">
        <div class="col-md-4 col-xs-4 pad_bottom_10">
            <select class="country_location form-control h-auto font_13" name="country_location" id="country_location">
                <option value="-1">Choose Country</option>
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

        <div class="col-md-3 col-xs-3 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" required="" value="<?php echo isset($_SESSION['general']['street']) ? $_SESSION['general']['street'] : ''; ?>" id="street_general" name="street_general" placeholder="Enter Street Address">
        </div>
        
        <div class="col-md-3 col-xs-3 pad_bottom_10">
            <input type="text" name="city_location" value="<?php echo isset($_SESSION['general']['city']) ? $_SESSION['general']['city'] : ''; ?>" placeholder="Enter Geo Location / City" class="form-control h-auto font_13" id="city_location2"/>
        </div>
        
         <div class="col-md-3 col-xs-3 pad_bottom_10">
            <select class="state_location form-control h-auto font_13" name="state_location" id="state_location">
                <option value="-1">Choose State</option>
            </select>
        </div>
        
        <div class="col-md-3 col-xs-3 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" required="" value="<?php echo isset($_SESSION['general']['zipcode']) ? $_SESSION['general']['zipcode'] : ''; ?>" id="zipcode_general" name="zipcode_general" placeholder="Enter Zip code">
        </div>

    </div>

    <!--div class="form-group m-b-0">

        <div class="col-md-12 col-xs-12 pad_bottom_10"> 
            <input type="text" class="form-control h-auto font_13" required="" value="<?php echo isset($_SESSION['general']['geolocation']) ? $_SESSION['general']['geolocation'] : ''; ?>" id="geolocation" name="geolocation" placeholder="Enter Geo Location">
        </div>
    </div-->
    <div class="form-group m-b-0">

        <div class="col-md-12 col-xs-12 pad_bottom_10">
            <select class='form-control h-auto font_13' name="ctype" id="ctype">

                <?php
                $campType = array("Local Campaign", "National Campaign", "Ecommerce Campaign");
                foreach ($campType as $camp) {
                    $selected = '';
                    if ($_SESSION['general']['campaignType'] == $camp) {
                        $selected = "selected";
                    }
                    ?>
                    <option value="<?php echo $camp; ?>"><?php echo $camp; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <!--div class="form-group m-b-0">

    </div-->
    <div class="form-group m-b-0 bottom-footer radius-bottom">
        <div class="col-md-12">

            <a id="tab-item1" data-toggle="tab" class="btn btn-success">Next <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>


        </div>
    </div>

</form>

