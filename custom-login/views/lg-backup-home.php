<?php

   global $wpdb;
   global $current_user;
   $current_user = wp_get_current_user();
   
   $user_id = $current_user->ID;
   /* Country List */
   $resultsctr = array();
   $resultsctr[0][id] = "AUS";
   $resultsctr[0][name] = "Australia";
   $resultsctr[1][id] = "CAN";
   $resultsctr[1][name] = "Canada";
   $resultsctr[2][id] = "DEU";
   $resultsctr[2][name] = "Germany";
   $resultsctr[3][id] = "HKG";
   $resultsctr[3][name] = "Hong Kong";
   $resultsctr[4][id] = "IRL";
   $resultsctr[4][name] = "Ireland";
   $resultsctr[5][id] = "MAC";
   $resultsctr[5][name] = "Macau";
   $resultsctr[7][id] = "NLD";
   $resultsctr[7][name] = "Netherlands";
   $resultsctr[8][id] = "NZL";
   $resultsctr[8][name] = "New Zealand";
   $resultsctr[9][id] = "SGP";
   $resultsctr[9][name] = "Singapore";
   $resultsctr[10][id] = "ZAF";
   $resultsctr[10][name] = "South Africa";
   $resultsctr[11][id] = "PHL";
   $resultsctr[11][name] = "Philippines";
   $resultsctr[12][id] = "TWN";
   $resultsctr[12][name] = "Taiwan";
   $resultsctr[13][id] = "GBR";
   $resultsctr[13][name] = "United Kingdom";
   $resultsctr[14][id] = "USA";
   $resultsctr[14][name] = "United States";
   $resultsctr[15][id] = "EST";
   $resultsctr[15][name] = "Estonia";
   
   // get cuntry select options
   foreach ($resultsctr as $countryitem) {
       $selectcheck = "";
       if ($country == $countryitem["id"]) {
           $selectcheck = "selected";
       }
       $countryoptions .= '<option value="' . $countryitem["id"] . '" ' . $selectcheck . '>' . $countryitem["name"] . '</option>';
   }

   is_login_check();

   ?>
<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/css/dashboard.css"/>
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">


<div class="col-md-12 pad_top_15 pad_bottom_15 border-bottom">
         <div class="logo"><img src="http://112.196.32.246/html-css-enfusion/images/logo.jpg"></div>
</div>

<div class="container">
   <div class="row">
      <div class="col-md-12 col-xs-12">
         <h2 class="text-center font_30 m-t-20">Welcome to <strong class="blue-color upper-text">Enfusen</strong></h2>
      </div>
      <!-- Email Confirmation DIV -->
      <div class="col-md-4 col-xs-12">
         <div class="panel panel-default mail-section" id="confirm">
            <form class="form-horizontal app-form H_hundredPercent" id="confirm-email">
               <div class="icon-label text-center">
                  <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-mail.png" alt="icon-mail"/>
               </div>
               <div class="inner-section col-md-12 text-center pad_top_10">
                  <h3 class="mont-font cap-text normal font_24 m-b-0">Email Confirmation</h3>
                  <p class="font-15">Email <strong><?php echo "roger@enfusen.com";//$current_user->user_email; ?></strong><br/> has been confirmed.</p>
               </div>
               <div class="button-section pad_bottom_15">
               <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="submit" value="Next" >Next</button>
               </div>
            </form>
         </div>
      </div>
      <!-- Email Confirmation Ends Here Completed Profile DIV -->
      <div class="col-md-4 col-xs-12">
         <div class="panel panel-default user-info" id="complete-profile">
            <form action="#" method="post" id="add_profile_info" name="add_profile_info" class="form-horizontal app-form H_hundredPercent">
               <div class="icon-label text-center">
                  <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-member.png" alt="icon-mail"/>
               </div>
               <div class="inner-section col-md-12 text-center pad_top_10">
                  <h3 class="mont-font cap-text normal font_24 m-b-0">Provide Your Details</h3>
                  <div class="form-group m-b-5">
                     <label for="name" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13">Email*</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <input type="text" email='true' readonly required value="<?php echo 'roger@enfusen.com';//$current_user->user_email; ?>" class="form-control" id="email" name="email">
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label for="name" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13">Name* :</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <input type="text" required class="form-control" id="name" name="name" placeholder="Client Name">
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label for="login" class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13">Phone No*</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <input type="text" required class="form-control" id="login" name="login" placeholder="Enter Phone No">
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="country">Country</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <select name="country" required id="country" class="form-control">
                        <?php echo $countryoptions; ?>
                        </select>
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="clients">No. of Clients</label>
                     <div class="col-md-12 col-xs-12 text_field font_13">
                        <select name="clients" required id="clients" class="form-control">
                           <option value="1-10">1 - 10</option>
                           <option value="11-20">11 - 20</option>
                           <option value="21-30">21 - 30</option>
                        </select>
                     </div>
                  </div>
                  <input type="hidden" id="userid" name="userid" value="<?php echo $user_id; ?>"/>
                  <div class="button-section pad_bottom_15">
                     <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="submit" value="Submit" >Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <!-- ../Profile DIV Ends Here ! Campaign Form Starts -->
      <div class="col-md-4 col-xs-12">
         <div class="panel panel-default campaign-section" id="campaign">
            <form id="campaign-form" class="form-horizontal app-form H_hundredPercent">
               <div class="icon-label text-center">
                  <img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/icon-campaign.png" alt="icon-mail"/>
               </div>
               <div class="inner-section col-md-12 text-center pad_top_10">
                  <h3 class="mont-font cap-text normal font_24 m-b-0">Create Campaign</h3>
                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="cname">Company Name</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <input type="text" required="" class="form-control" id="cname" name="cname" placeholder="Enter Company Name">
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="curl">Website</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <input type="url" required class="form-control" id="curl" name="curl" placeholder="Enter Website">
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="country">Country</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <select name="country" required id="country" class="form-control">
                        <?php echo $countryoptions; ?>
                        </select>
                     </div>
                  </div>

                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="geo_location">Geo Location</label>
                     <div class="col-md-12 col-xs-12 text_field">
                        <input type="text" required class="form-control" id="geo_location" name="geo_location" placeholder="Enter Geo Location ( Mohali , IN )">
                     </div>
                  </div>
                  <div class="form-group m-b-5">
                     <label class="col-md-12 col-xs-12 control-label mont-font upper-text text-left font_13" for="ctype">Campaign Type</label>
                     <div class="col-md-12 col-xs-12 text_field font_13">
                        <select name="ctype" required id="ctype" class="form-control">
                           <option value="local">Local Campaign</option>
                           <option value="national">National Campaign</option>
                           <option value="ecommerce">Ecommerce Campaign</option>
                        </select>
                     </div>
                  </div>
                  <input type="hidden" name="userid" id="userid" value="<?php echo $user_id; ?>"/>
                  <input type="hidden" name="useremail" id="useremail" value="<?php echo $current_user->user_email; ?>"/>
                  <div class="button-section pad_bottom_15">
                     <button class="col-md -12 col-xs-12 btn btn-success mont-font upper-text pad_top_10 pad_bottom_10 color-white upper-text" type="submit" value="Submit">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <!-- ../First Campaign Create Ends Here ! -->
   </div>
</div>

<?php include_once LG_COUNT_PLUGIN_DIR . '/views/lg-footer.php'; ?>
