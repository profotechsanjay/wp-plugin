<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/css/lg-citation.css"/>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<?php
   is_login_check();
   $user_id = user_id();
   global $wpdb;
   $all_country_list = $wpdb->get_results("SELECT * FROM `wp_country` ORDER BY `name`");
   $sql = "CREATE TABLE IF NOT EXISTS `wp_citation_login_details` (
                  `details_id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(7) NOT NULL,
                  `domain_name` varchar(200) NOT NULL,
                  `login_username` varchar(200) NOT NULL,
                  `login_password` varchar(100) NOT NULL,
                  PRIMARY KEY (`details_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
   $wpdb->query($sql);
   $all_citation_login_details = $wpdb->get_results("SELECT * FROM `wp_citation_login_details` WHERE `user_id` =$user_id");
   if (!empty($all_citation_login_details)) {
       echo '<div id="login_details_div">';
       foreach ($all_citation_login_details as $row_details) {
           $r_domain_name = str_replace(".", "", $row_details->domain_name);
           ?>
<span style="display: none;" id="login_username_<?php echo $r_domain_name; ?>"><?php echo $row_details->login_username; ?></span>
<span style="display: none;" id="login_password_<?php echo $r_domain_name; ?>"><?php echo $row_details->login_password; ?></span>
<?php
   }
   echo '</div>';
   }
   $mcc_url = 'http://mcc.enfusen.com';
   $FinalURL = $mcc_url . '/cron/citaions-instruction.php';
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $FinalURL);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   $setup_ins = curl_exec($ch);
   $setup_ins = unserialize($setup_ins);
   foreach ($setup_ins as $setup_domain_name => $row_details) {
   //pr($row_details);exit;
   $r_domain_name = str_replace(".", "", $setup_domain_name);
   ?>
<span style="display: none;" id="setup_ins_<?php echo $r_domain_name; ?>"><?php echo nl2br(stripslashes($row_details['ins'])); ?></span>
<span style="display: none;" id="login_url_<?php echo $r_domain_name; ?>"><?php echo $row_details['login_url']; ?></span>
<?php
   }
   //pr( $setup_ins['facebook.com']); exit;
   
   
   
   if (isset($_POST['citation_frm_submit_btn'])) {
       //pr($_POST);
   
       update_user_meta($user_id, 'ct_BusinessName', $_POST['ct_BusinessName']);
       update_user_meta($user_id, 'streetaddress', $_POST['streetaddress']);
       update_user_meta($user_id, 'website', $_POST['website']);
       update_user_meta($user_id, 'state', $_POST['state']);
       update_user_meta($user_id, 'city', $_POST['city']);
       update_user_meta($user_id, 'country', $_POST['country']);
       update_user_meta($user_id, 'zip', $_POST['zip']);
       update_user_meta($user_id, 'phonenumber', $_POST['phonenumber']);
       update_user_meta($user_id, 'ct_Keyword', $_POST['ct_Keyword']);
       update_user_meta($user_id, 'ct_GoogleLocation', $_POST['ct_GoogleLocation']);
   
       $placesscout_api_info = placesscout_api_info();
       $username = $placesscout_api_info['username'];
       $password = $placesscout_api_info['password'];
       $main_api_url = $placesscout_api_info['main_api_url'];
   
       //Create New citations
       //$new_citation['ReportId'] = 'FM_CH7ov9EOBXl_fUIx0jw';//need for update
   
       $new_citation['Name'] = get_user_meta($user_id, 'BRAND_NAME', true);
       $new_citation['BusinessInfo']['BusinessName'] = $_POST['ct_BusinessName'];
       $new_citation['BusinessInfo']['StreetAddress'] = $_POST['streetaddress'];
       $new_citation['BusinessInfo']['City'] = $_POST['city'];
       $new_citation['BusinessInfo']['State'] = $_POST['state'];
       $new_citation['BusinessInfo']['ZipCode'] = $_POST['zip'];
       $new_citation['BusinessInfo']['WebsiteUrl'] = $_POST['website'];
       $new_citation['BusinessInfo']['PhoneNumber'] = $_POST['phonenumber'];
       $new_citation['BusinessInfo']['Country'] = str_replace(" ", "", $_POST['country']);
       $new_citation['KeywordSearchesForCompetitiveAnalysis']['Keyword'] = $_POST['ct_Keyword'];
       $new_citation['KeywordSearchesForCompetitiveAnalysis']['GoogleLocation'] = $_POST['ct_GoogleLocation'];
       $new_citation['TotalCompetitorsToAnalyze'] = 10;
       $new_citation['NumResultsToGatherPerQuery'] = 200;
       $new_citation['GatherCitationStrengthData'] = true;
       //pr($new_citation);
       ///*
       if ($_POST['citation_status'] != 'complete') {
           ///*
           $curl_url = 'citationreports';
           $new_citations = pc_post($username, $password, $main_api_url, $curl_url, json_encode($new_citation)); //Create New Citaions
           $new_citations = json_decode($new_citations);
   
           $ReportId = $new_citations->id;
   
           if ($ReportId != "") {
               $insert_citation['user_id'] = $user_id;
               $insert_citation['ReportId'] = $ReportId;
               $insert_citation['status'] = 'In Progress';
               $insert_citation['last_run'] = date('Y-m-d H:i:s');
               $check_existing_ReportId = $wpdb->get_row("SELECT * FROM `wp_citation_tracker` WHERE `user_id` = $user_id");
               if (empty($check_existing_ReportId)) {
                   $wpdb->insert('wp_citation_tracker', $insert_citation);
   
                   // Run citaion report
                   $citation_run['ReportId'] = $ReportId;
                   $curl_url = 'citationreports/' . $ReportId . '/runreport';
                   $citations_run = pc_post($username, $password, $main_api_url, $curl_url, json_encode($citation_run));
               }
           } else {
               echo 'Unable to create citaion report.';
           }
           //*/
       } else {
           // Update citaion report
           $new_citation['ReportId'] = $_POST['citation_ReportId'];
           $curl_url = 'citationreports/' . $_POST['citation_ReportId'];
   
           $update_citaion = pc_put($username, $password, $main_api_url, $curl_url, json_encode($new_citation));
           $update_citaion = json_decode($update_citaion);
           //pr($update_citaion);
   
           $insert_citation['user_id'] = $user_id;
           $insert_citation['ReportId'] = $_POST['citation_ReportId'];
           $insert_citation['status'] = 'In Progress';
           $insert_citation['rerun'] = 'Yes';
           $insert_citation['last_run'] = date('Y-m-d H:i:s');
           $wpdb->insert('wp_citation_tracker', $insert_citation);
   
   
           $citation_run['ReportId'] = $_POST['citation_ReportId'];
           $curl_url = 'citationreports/' . $_POST['citation_ReportId'] . '/runreport';
           $citations_run_result = pc_post($username, $password, $main_api_url, $curl_url, json_encode($citation_run));
   
           //pr($citations_run_result);
           $citations_run_result = json_decode($citations_run_result);
           //pr($citations_run_result);
           //exit;
       }
   
       header("refresh:3;url=" . site_url() . "/agency-competitor");
   }
   
   function box_color($text) {
       if ($text == 'Y') {
           return '#d4f4cd!important';
       } else {
           return '#f4cdd3!important';
       }
   }
   
   $phonenumber = get_user_meta($user_id, 'phonenumber', true);
   if ($phonenumber == '--') {
       $phonenumber = '';
   }
   $sql = "SELECT * FROM `wp_citation_tracker` WHERE `user_id` = $user_id order by `citation_tracker_id` desc LIMIT 1";
   $result_info = $wpdb->get_row($sql);
   $citation_status = $result_info->status;
   $rerurn = $result_info->rerun;
   $citation_ReportId = $result_info->ReportId;
   $citation_tracker_id = $result_info->citation_tracker_id;
   $basic_data = json_decode($result_info->basic_data);
   
   //pr($basic_data);
   $citations_data = json_decode($result_info->citations_data);
   $all_citations = $citations_data->citations;
   
   $competitive_citation = json_decode($result_info->competitive_citation);
   //pr($competitive_citation);exit;
   
   $competitorCitationData = $competitive_citation->competitorCitationData;
   //pr($competitorCitationData);exit;
   
   $businessInfo = $basic_data->businessInfo;
   //pr($basic_data);
   $updatedAt = str_replace(array('/Date(', ')/'), "", $businessInfo->createdAt);
   
   $listings_data = json_decode($result_info->listings_data);
   //pr($listings_data);exit;
   $current_state = trim(get_user_meta($user_id, 'state', true));
   $country = trim(get_user_meta($user_id, 'country', true));
   if ($country == '') {
       $country = 'United States';
   }
   $country_id = $wpdb->get_row("SELECT * FROM `wp_country` WHERE `name` = '$country'")->country_id;
   if (!$country_id > 0) {
       $country_id = 223; //United States
   }
   $state_list = $wpdb->get_results("SELECT * FROM `wp_country_state` WHERE `country_id` = $country_id order by `code` asc");
   
   
   
   if (isset($_POST['download_pdf'])) {
       ///*
       // PDF work
       $str .= pdf_header($user_id);
   
       $session_pdf_citations = $_SESSION['pdf_citations'];
       $session_pdf_competitive_citations = $_SESSION['pdf_competitive_citations'];
       $session_pdf_competitor_citation_opp = $_SESSION['pdf_competitor_citation_opp'];
       $session_box_arr = $_SESSION['box_arr'];
   
       function sortByOrder($a, $b) {
           return $b['da'] - $a['da'];
       }
   
       usort($session_pdf_citations, 'sortByOrder');
   
       function sortByOrder_co($a, $b) {
           return $b['num_competitor'] - $a['num_competitor'];
       }
   
       usort($session_pdf_competitor_citation_opp, 'sortByOrder_co');
   
       $str .= '<style>
                 .padding_full{padding:5px 3px;float:left;font-size: 13px;text-align:center;}
                 .Yclass{background: #d4f4cd!important;}
                 .Nclass{background: #f4cdd3!important;}
                 .tcenter{text-align: center;}
                 .p_box_class{border:4px solid white;padding:15px;color:white;width:17%!important;vertical-align: top;}
             </style>';
       $str .= '<h3 style="text-align:center;">Citation Audit: ' . brand_name($user_id) . '</h3><br/>';
   
       $str .= '<table border="0" width="100%">
         <tr>
             <td align="left" class="p_box_class" style="background-color: #444d58;">
                 <b>Business NAP</b><br/>
                 ' . $businessInfo->businessName . '<br/>
                 ' . $businessInfo->streetAddress . '<br/>
                 ' . $businessInfo->city . ', ' . $businessInfo->state . ' ' . $businessInfo->zipCode . '<br/>
                 ' . $businessInfo->phoneNumber . '<br/>
                 ' . website_format($businessInfo->websiteUrl) . '<br/>
                 ' . get_user_meta($user_id, 'ct_Keyword', true) . '<br/>
                 ' . get_user_meta($user_id, 'ct_GoogleLocation', true) . '
             </td>';
       foreach ($session_box_arr as $row_pdf) {
           $str .= '<td align="center" class="p_box_class" style="background-color:' . $row_pdf['color'] . '">
                 <span style="font-size: 20px;font-weight:bold;">' . $row_pdf['name'] . '</span><br/><br/>
                 <span style="font-size: 47px;text-align:center;">' . $row_pdf['val'] . '</span><br/><br/><br/><br/>
             </td>';
       }
       $str .= '</tr>
      </table>';
       $str .= '<br/><h3 style="text-align:left;">Citations List</h3>';
       $str .= '<table class="c2" style="margin-top:10px; font-size:14px; border-radius: 3px 3px 3px 3px; width:100%; border: 1px solid #cecece;">';
       $str .= '<tr style="background-color:#EBEBEB;">';
       $str .= '<th class="padding_full" style="text-align:left;text-indent:5px;">Domain</th>';
       $str .= '<th class="padding_full">Verified</th>';
       $str .= '<th class="padding_full">Name</th>';
       $str .= '<th class="padding_full">FA</th>';
       $str .= '<th class="padding_full">Phone</th>';
       $str .= '<th class="padding_full">URL</th>';
       $str .= '<th class="padding_full">SA</th>';
       $str .= '<th class="padding_full">City</th>';
       $str .= '<th class="padding_full">State</th>';
       $str .= '<th class="padding_full">Zip</th>';
       $str .= '<th class="padding_full">DA</th>';
       $str .= '<th class="padding_full">PA</th>';
       $str .= '</tr>';
   
       foreach ($session_pdf_citations as $color_index => $row_pdf) {
           $color = $color_index % 2 == 0 ? '#fff' : '#eee';
           $str .= '<tr style="background-color:' . $color . ';border: 1px solid #AAA;">';
           $str .= '<td class="padding_full" style="text-align:left;text-indent:5px;">' . $row_pdf['domain'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['varified'] . 'class">' . $row_pdf['varified'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['name'] . 'class">' . $row_pdf['name'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['full_address'] . 'class">' . $row_pdf['full_address'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['phone'] . 'class">' . $row_pdf['phone'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['url'] . 'class">' . $row_pdf['url'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['street_address'] . 'class">' . $row_pdf['street_address'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['city'] . 'class">' . $row_pdf['city'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['state'] . 'class">' . $row_pdf['state'] . '</td>';
           $str .= '<td class="padding_full ' . $row_pdf['zip'] . 'class">' . $row_pdf['zip'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['da'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['pa'] . '</td>';
           $str .= '</tr>';
       }
       $str .= '</table>';
   
       $str .= '<div style="page-break-before:always">&nbsp;</div>';
       $str .= '<h3 style="text-align:left;">Competitive Citations List</h3>';
       $str .= '<table class="c2" style="margin-top:10px; font-size:14px; border-radius: 3px 3px 3px 3px; width:100%; border: 1px solid #cecece;">';
       $str .= '<tr style="background-color:#EBEBEB;">';
       $str .= '<th class="padding_full" style="text-align:left;text-indent:5px;">Keyword</th>';
       $str .= '<th class="padding_full">Location</th>';
       $str .= '<th class="padding_full">Rank</th>';
       $str .= '<th style="text-align:left;text-indent:5px;">Business Name</th>';
       $str .= '<th class="padding_full">Verified</th>';
       $str .= '<th class="padding_full">Mentions</th>';
       $str .= '<th class="padding_full">DA</th>';
       $str .= '<th class="padding_full">PA</th>';
       $str .= '<th class="padding_full">Moz</th>';
       $str .= '<th class="padding_full">Backlinks</th>';
       $str .= '</tr>';
   
       foreach ($session_pdf_competitive_citations as $color_index => $row_pdf) {
           $color = $color_index % 2 == 0 ? '#fff' : '#eee';
           $str .= '<tr style="background-color:' . $color . ';border: 1px solid #AAA;">';
           $str .= '<td class="padding_full" style="text-align:left;text-indent:5px;">' . $row_pdf['keyword'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['googleLocation'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['rank'] . '</td>';
           $str .= '<td style="text-align:left;text-indent:5px;">' . $row_pdf['title'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['totalVerifiedCitations'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['totalMentions'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['averageDomainAuthority'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['averagePageAuthority'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['averageMozRank'] . '</td>';
           $str .= '<td class="padding_full">' . $row_pdf['averageLinks'] . '</td>';
           $str .= '</tr>';
       }
       $str .= '</table>';
   
       $str .= '<div style="page-break-before:always">&nbsp;</div>';
       $str .= '<h3 style="text-align:left;">Total Citation Opportunities - ' . $session_box_arr['Opportunity']['val'] . '</h3>';
       $str .= '<h3 style="text-align:left;">First Page Citation Opportunities List</h3>';
   
       $str .= '<table class="c2" style="margin-top:10px; font-size:14px; border-radius: 3px 3px 3px 3px; width:100%; border: 1px solid #cecece;">';
       $str .= '<tr style="background-color:#EBEBEB;">';
       $str .= '<th class="padding_full" style="text-align:left;text-indent:5px;">Citation Site</th>';
       $str .= '<th class="padding_full">Num. Competitor</th>';
       $str .= '<th class="padding_full">Have Citation?</th>';
       $str .= '<th class="padding_full">Total Citations </th>';
       $str .= '<th class="padding_full">DA</th>';
       $str .= '<th class="padding_full">MozRank</th>';
       $str .= '</tr>';
   
       foreach ($session_pdf_competitor_citation_opp as $color_index => $row_pdf) {
           if ($color_index > 17) {
               break;
           } else {
               $color = $color_index % 2 == 0 ? '#fff' : '#eee';
               $str .= '<tr style="background-color:' . $color . ';border: 1px solid #AAA;">';
               $str .= '<td class="padding_full" style="text-align:left;text-indent:5px;">' . $row_pdf['site'] . '</td>';
               $str .= '<td class="padding_full">' . $row_pdf['num_competitor'] . '</td>';
               $str .= '<td class="padding_full ' . $row_pdf['have_citation'] . 'class">' . $row_pdf['have_citation'] . '</td>';
               $str .= '<td class="padding_full">' . $row_pdf['total_citaion'] . '</td>';
               $str .= '<td class="padding_full">' . $row_pdf['DA'] . '</td>';
               $str .= '<td class="padding_full">' . $row_pdf['mozRank'] . '</td>';
               $str .= '</tr>';
           }
       }
       $str .= '</table>';
   
       require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
       $dompdf = new DOMPDF();
       $dompdf->load_html($str);
       $dompdf->set_paper('a4', 'landscape');
       $dompdf->render();
       include(ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php');
       $pdf = $dompdf->output();
       $dompdf->stream(str_replace(" ", "_", brand_name($user_id)) . '_Citation_Audit_Report.pdf', array("Attachment" => true));
       exit;
       // PDF work END
       //*/
   }
   
   $all_complete_citations = $wpdb->get_results("SELECT * FROM `wp_citation_tracker` WHERE `user_id` = $user_id && `status` = 'complete' order by `last_run` desc");
   ?>
<style>
   .portlet-body{height:<?php echo $businessInfo->businessName != '' ? '275' : '250' ?>px;}
   .score_c{font-size: 67px;text-align: center;}
   .col-md-3{width:20%;padding-left:5px;padding-right:5px;}
   .c_left{width:40%;float:left;font-weight: bold;}
   .c_right{width:60%;float:left;}
   .required{color:black;}
   label.error{color:red;margin-left:10px;}
   input[type=text]{
   width:90%!important;
   }
   .Yclass{background: #d4f4cd!important;}
   .Nclass{background: #f4cdd3!important;}
   .tcenter{text-align: center;}
   .needs_att{color:#8F44AD;}
   .opportunity{color:#E9505C;}
</style>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.validate.js'></script>
<div id="primary" class="site-content">
   <div id="content" role="main">
      <div class="en-right mar_0 HundredPercent">
         <div class="fix-header">
            <div class="col-md-12 pad_top_15 pad_bottom_15 border-bottom white-bg">
               <div class="logo"><img src="http://112.196.32.246/html-css-enfusion/images/logo.jpg"></div>
            </div>
         </div>
         <!---------->
         <div class="sub-fix-header white-bg">
            <div class="col-md-12">
               <?php
                  $box_arr['citation_score']['name'] = 'Citation Score';
                  $box_arr['citation_score']['bg'] = 't_score.jpg';
                  
                  $box_arr['verified']['name'] = 'Verified';
                  $box_arr['verified']['bg'] = 'urgent.jpg';
                  
                  $box_arr['Needs_Attention']['name'] = 'Needs Attention';
                  $box_arr['Needs_Attention']['bg'] = 'important.jpg';
                  
                  $box_arr['Opportunity']['name'] = 'Opportunity';
                  $box_arr['Opportunity']['bg'] = 'critical.jpg';
                  //if (empty($result_info)) {
                  if ($citation_status != 'In Progress') {
                      ?>
               <div class="pull-left">
                  <?php
                     if ($citation_status != 'complete') {
                         echo 'To run your citation report please complete your citation profile to the right <a class="btn btn-danger font-13 radius_4 action-color mar_left_5" href="' . site_url() . '/agency-competitor">Skip for Now</a>';
                     } else {
                         echo '<b>Last Run:</b> ' . date("d M Y h:i a", strtotime($result_info->last_run));
                     }
                     ?>
               </div>
               <?php
                  $run_update_options = 'No';
                  if ($citation_status == '') {
                      $run_update_options = 'Yes';
                  }
                  if ($citation_status == 'complete' && administrator_permission() == true) {
                      $run_update_options = 'Yes'; //only administrator can rerun/update it
                  }
                  ?>
               <div>
                  <?php
                     if ($citation_status == 'complete') {
                         ?>
                  <div style="float:left;">
                     <a class="btn btn-success" onclick="request_citation_rerun()"> Request Citation Rerun </a>
                     <?php if (count($all_complete_citations) > 1) { ?>
                     <a class="btn btn-success a_history" onclick="citation_func('history')"> History </a>
                     <?php } ?>
                     <a class="btn btn-success" onclick="document.forms.download_pdf_Frm.submit();" > Download Report </a>
                  </div>
                  <?php
                     }
                     if ($run_update_options == 'Yes') {
                         ?>
                  <div class="pull-right">
                     <button class="btn blue-color font-13 radius_4 settings" onclick="jQuery('.all_citaion_div').hide();
                        jQuery('#citaion_profile_from').toggle();"><?php echo $citation_status == 'complete' ? 'Update/rerun' : 'Complete' ?> Citation Profile
                     </button>
                  </div>
                  <?php
                     }
                     ?>
               </div>
               <?php
                  }
                  ?>
            </div>
         </div>
         <div class="col-md-12 pad_10_rem">
            <div class="pad_top_20">
               <div class="col-md-3">
                  <div class="portlet box default" style="margin-bottom:10px;">
                     <div class="portlet-body" style="border:1px solid #d9d9d9; padding:8px;font-size: 17px;background: #444d58;color:white;">
                        <b>Business NAP</b>
                        <div class="margin-bottom-10"></div>
                        <?php
                           if ($businessInfo->businessName != '') {
                               echo $businessInfo->businessName;
                           } else {
                               echo brand_name($user_id);
                           }
                           ?> 
                        <div class="margin-bottom-5"></div>
                        <?php
                           if ($businessInfo->streetAddress != '') {
                               echo $businessInfo->streetAddress;
                           } else {
                               echo get_user_meta($user_id, 'streetaddress', true);
                           }
                           ?>
                        <div class="margin-bottom-5"></div>
                        <?php
                           if ($businessInfo->city != "") {
                               echo $businessInfo->city . ', ' . $businessInfo->state . ' ' . $businessInfo->zipCode;
                           } else {
                               echo get_user_meta($user_id, 'city', true) . ', ' . get_user_meta($user_id, 'state', true) . ' ' . get_user_meta($user_id, 'zip', true);
                           }
                           ?>
                        <div class="margin-bottom-5"></div>
                        <?php
                           if ($businessInfo->phoneNumber != '') {
                               echo $businessInfo->phoneNumber;
                           } else {
                               echo $phonenumber;
                           }
                           ?>
                        <div class="margin-bottom-5"></div>
                        <?php
                           if ($businessInfo->websiteUrl != '') {
                               echo website_format($businessInfo->websiteUrl);
                           } else {
                               echo website_format(get_user_meta($user_id, 'website', true));
                           }
                           if (get_user_meta($user_id, 'ct_Keyword', true) != '') {
                               echo '<div class="margin-bottom-5"></div>' . get_user_meta($user_id, 'ct_Keyword', true) . '<div class="margin-bottom-5"></div>';
                               echo get_user_meta($user_id, 'ct_GoogleLocation', true);
                           }
                           ?>
                        <div class="clear"></div>
                     </div>
                  </div>
               </div>
               <?php foreach ($box_arr as $index_name => $row_box) { ?>
               <div class="col-md-3">
                  <div class="portlet box default" style="margin-bottom:10px;">
                     <div class="portlet-body" style="border:1px solid #d9d9d9; padding:8px;font-size: 17px;color:white; background: url(<?php echo get_template_directory_uri(); ?>/images/<?php echo $row_box['bg']; ?>) no-repeat;background-size:cover;">
                        <div style="font-weight: bold;font-size: 16px;"><?php echo $row_box['name']; ?></div>
                        <div class="clear"></div>
                        <div class="score_c"><span id="<?php echo $index_name . '_count'; ?>">0</span></div>
                        <div class="margin-bottom-20"></div>
                        <div class="clear_both"></div>
                        <div class="clear_both"></div>
                        <div class="clear_both"></div>
                        <?php if ($basic_data->businessInfo != '') echo '<div style="clear:both;height:35px;"></div>'; ?>
                        <div style="text-align: right;"><a style="color:white;" onclick="citation_list_func('<?php echo $index_name; ?>')"><u>View <?php
                           $name = $index_name == 'citation_score' ? 'All' : $index_name;
                           echo ucfirst(str_replace("_", " ", $name));
                           ?></u></a></div>
                        <div class="clear"></div>
                     </div>
                  </div>
               </div>
               <?php } ?>
               <div class="clear_block"></div>
            </div>
            <?php
               //if (!empty($result_info)) {
               
               if ($citation_status == 'In Progress') {
                   $run_text = 'run';
                   if ($rerurn == 'Yes') {
                       $run_text = 'rerun/update';
                   }
                   echo '<div style="font-weight:bold; margin-top:30px;font-size:17px;text-align:center;">This report has been queued to ' . $run_text . ', and the results will be available shortly as soon as the report is finished running. <br/>Report will be come within 30 minutes. </div>';
               } else if ($citation_status == 'complete') {
                   ?>
         </div>
         <div style="float:left;">
            <div class="btn-group btn-group date-range-group">
               <!--<a class="btn btn-success a_listings" onclick="citation_func('listings')">Listings</a>-->
               <a class="btn btn-success active a_citation_recommendation" onclick="citation_func('citation_recommendation')">Citations</a>
               <a class="btn btn-success a_citations_by_site" onclick="citation_func('citations_by_site')">Citations by Site</a>
               <a class="btn btn-success a_competitive_citation" onclick="citation_func('competitive_citation')">Competitive Citations</a>
               <a class="btn btn-success a_citations_opp" onclick="citation_func('citations_opp')"> Citation Opportunities </a>
            </div>
         </div>
         <div style="float:right;margin-right: 20px;">
            <form name="download_pdf_Frm" action="" method="post">
               <input type="hidden" name="download_pdf">
            </form>
         </div>
         <div class="clear_both"></div>
         <?php } if ($citation_status != 'In Progress') { ?>
         <!---------->
         <div id="citaion_profile_from" class="all_citaion_div col-md-12" style="display: none;">
            <div class="row white-bg">
               <form name="citation_Frm" id="citation_Frm" action="" method="post">
                  <div class="clear"></div>
                  <input type="hidden" name="citation_status" value="<?php echo $citation_status; ?>">
                  <?php if ($citation_ReportId != '') { //for rerun / update
                     ?>
                  <input type="hidden" name="citation_ReportId" value="<?php echo $citation_ReportId; ?>">
                  <?php }
                     ?>
                  <div class="ibox-title">
                     <h5>Citation Profile</h5>
                  </div>
                  <div class="ibox-content">
                     <div class="pull-left HundredPercent pad_bottom_10 ">I.e: You need to complete your citation profile to the right in order to be able to run a citation report</div>
                     <div class="row">
                        <div class="col-md-7 pad_left_0 pad_right_0 b-r">
                           <div class="col-md-12 pad_bottom_15">
                              <div class="c_left w-auto font-16"><b>Citation Report Name:</b></div>
                              <div class="c_right w-auto font-16 pad_left_5"><?php echo get_user_meta($user_id, 'BRAND_NAME', true); ?></div>
                           </div>
                           <div class="col-md-12 pad_bottom_15">
                              <div class="c_left HundredPercent mar_bottom_5">Business Name</div>
                              <div class="c_right HundredPercent"><input style="width:100%!important;" type="text" name="ct_BusinessName" placeholder="Enfusen" class="required" value="<?php echo get_user_meta($user_id, 'ct_BusinessName', true); ?>"></div>
                           </div>
                           <div class="col-md-4 pad_bottom_15">
                              <div class="c_left HundredPercent mar_bottom_5">Country</div>
                              <div class="c_right HundredPercent">
                                 <select  name="country"  class="required"  onchange="set_state(this.value, '<?php echo $current_state; ?>')">
                                    <option value="">Select Country</option>
                                    <?php
                                       foreach ($all_country_list as $row_country) {
                                           ?>
                                    <option <?php if ($country == $row_country->name) echo 'selected'; ?> value="<?php echo $row_country->name; ?>"><?php echo $row_country->name; ?></option>
                                    <?php
                                       }
                                       ?>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4 pad_bottom_15">
                              <div class="c_left HundredPercent mar_bottom_5">Street Address</div>
                              <div class="c_right HundredPercent"><input style="width:100%!important;" type="text" name="streetaddress" placeholder="526 S Main Street" class="required" value="<?php echo get_user_meta($user_id, 'streetaddress', true); ?>"></div>
                           </div>
                           <div class="col-md-4 pad_bottom_15">
                              <div class="c_left HundredPercent mar_bottom_5">City</div>
                              <div class="c_right HundredPercent"><input style="width:100%!important;" type="text" name="city" placeholder="Akron" class="required" value="<?php echo get_user_meta($user_id, 'city', true); ?>"></div>
                           </div>
                           <div class="col-md-4 pad_bottom_15">
                              <div class="c_left HundredPercent mar_bottom_5">State</div>
                              <div class="c_right HundredPercent">
                                 <select id="state" name="state"class="required">
                                    <option value="">Select State</option>
                                    <?php
                                       foreach ($state_list as $row_state) {
                                           ?>
                                    <option <?php if ($current_state == $row_state->code || $current_state == $row_state->name) echo 'selected'; ?> value="<?php echo $row_state->code; ?>"><?php echo $row_state->code; ?> (<?php echo $row_state->name; ?>)</option>
                                    <?php
                                       }
                                       ?>
                                 </select>
                                 <!--<input type="text" name="state" placeholder="OH" class="required" value="<?php echo get_user_meta($user_id, 'state', true); ?>">-->
                              </div>
                           </div>
                           <div class="col-md-4 pad_bottom_15">
                              <div class="c_left  HundredPercent mar_bottom_5">Zip Code</div>
                              <div class="c_right  HundredPercent"><input style="width:100%!important;" type="text" name="zip" placeholder="44311" class="required" value="<?php echo get_user_meta($user_id, 'zip', true); ?>"></div>
                           </div>
                           <div class="col-md-4 pad_bottom_15">
                              <div class="c_left  HundredPercent mar_bottom_5"> Phone Number</div>
                              <div class="c_right HundredPercent"><input style="width:100%!important;" type="text" name="phonenumber" placeholder="1-877-999-0271" class="required" value="<?php echo $phonenumber; ?>"></div>
                           </div>
                           <div class="col-md-12 pad_bottom_15">
                              <div class="c_left  HundredPercent mar_bottom_5">Website Url</div>
                              <div class="c_right  HundredPercent"><input style="width:100%!important;" type="text" placeholder="https://www.enfusen.com/" name="website" class="required" value="<?php echo get_user_meta($user_id, 'website', true); ?>"></div>
                           </div>
                           <div class="clear_both"></div>
                        </div>
                        <div class="col-md-5 pad_left_0 pad_right_0">
                           <div class="col-md-12">
                              <div class="c_right HundredPercent font-16 pad_bottom_15"><b>Primary Service Searches For Competitive Analysis</b></div>
                           </div>
                           <div class="col-md-12 pad_bottom_15">
                              <div class="c_left  HundredPercent mar_bottom_5">Primary Service</div>
                              <div class="c_right  HundredPercent"><input style="width:100%!important;" type="text" placeholder="Marketing Agency" name="ct_Keyword" class="required" value="<?php echo get_user_meta($user_id, 'ct_Keyword', true); ?>"></div>
                           </div>
                           <div class="col-md-12 pad_bottom_15">
                              <div class="c_left  HundredPercent mar_bottom_5">Google Location</div>
                              <div class="c_right  HundredPercent"><input style="width:100%!important;" type="text" placeholder="City, State" name="ct_GoogleLocation" class="required" value="<?php echo get_user_meta($user_id, 'ct_GoogleLocation', true); ?>"></div>
                           </div>
                        </div>
                     </div>
                     <div class="hr-line-dashed"></div>
                     <div class="row">
                        <div class="col-sm-4">
                           <button class="btn font-13 radius_4 action-color mar_left_5" type="submit" name="citation_frm_submit_btn" ><?php echo $citation_status == 'complete' ? 'Update/Rerun' : 'Submit & Run' ?> Citation Audit</button>
                        </div>
                     </div>
                     <div class="clear_block"></div>
                  </div>
                  <div class="clear_block"></div>
               </form>
               <script>
                  jQuery('#citation_Frm').validate();
                  function set_state(country, current_state) {
                      jQuery.ajax({
                          type: 'POST',
                          url: '<?php echo site_url(); ?>/ajax-data.php',
                          data: {'page': 'citation_tracker', 'country': country, 'current_state': current_state},
                          success: function (html_data)
                          {
                              //alert(html_data);
                              jQuery('#state').html(html_data);
                          }
                      });
                  }
               </script>
            </div>
         </div>
         <!--------->
         <?php } ?>
         <!----------------- start citaion ----------------->
         <?php
            if ($citation_status == 'complete') {
                $verified_citaions = $needs_attention = $citation_index = 0;
                foreach ($box_arr as $index_name => $row_box) {
                    if ($index_name != 'Opportunity') {
                        ?>
         <div class="<?php echo $index_name; ?> all_citaion_div" style="display: none;">
            <?php //echo $index_name;             ?>
            <table style="font-size:92%!important;width:100%!important;" class="tabl1 table table-striped table-bordered table-hover" cellspacing="0" >
               <thead style="background-color: #888;color:white;">
                  <tr>
                     <th>Domain</th>
                     <th>Verified</th>
                     <th>Name</th>
                     <th>Full Address</th>
                     <th>Phone</th>
                     <th>URL</th>
                     <th>Street Address</th>
                     <th>City</th>
                     <th>State</th>
                     <th>Zip</th>
                     <th>DA</th>
                     <th>PA</th>
                     <th>View</th>
                  </tr>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($all_citations as $row_c) {
                     
                         $show_data = 0;
                         $domain = $row_c->name;
                         if ($index_name == 'citation_score') {
                             if (isset($citaion_site_list[$domain])) {
                                 $citaion_site_list[$domain] += 1;
                             } else {
                                 $citaion_site_list[$domain] = 1;
                             }
                             if (isset($domain_name_arr[$domain])) {
                                 $domain_name_arr[$domain] += 1;
                             } else {
                                 $domain_name_arr[$domain] = 1;
                             }
                             if ($row_c->isCitationVerified == 1) {
                                 $verified_citaions += 1;
                             } else {
                                 $needs_attention += 1;
                             }
                             $show_data = 1;
                         } else if ($index_name == 'verified' && $row_c->isCitationVerified == 1) {
                             $show_data = 1;
                         } else if ($index_name == 'Needs_Attention' && $row_c->isCitationVerified != 1) {
                             $show_data = 1;
                         }
                         if ($show_data == 1) {
                             if ($row_c->isCitationVerified == 1) {
                                 $varified_text = 'Y';
                             } else {
                                 $varified_text = 'N';
                             }
                             if (!isset($citation_status_arr[$domain])) {
                                 $citation_status_arr[$domain] = $varified_text;
                             }
                     
                     
                             $citation_link = $row_c->link;
                             //$rating = $row_c->name;
                             $name = $row_c->hasBusinessName;
                             $full_address = $row_c->hasAddress;
                             $phone = $row_c->hasPhone;
                             $url = $row_c->hasSiteUrl;
                             $street_address = $row_c->hasStreetAddress;
                             $city = $row_c->hasCity;
                             $state = $row_c->hasState;
                             $zip = $row_c->hasZip;
                     
                             $seoMozData = $row_c->seoMozData;
                     
                     
                             $da = $seoMozData->domainAuthority;
                             $pa = $seoMozData->pageAuthority;
                     
                             if ($da == '-1') {
                                 $da = 'N/A';
                             }if ($pa == '-1') {
                                 $pa = 'N/A';
                             }
                             if ($da > 0) {
                                 $da = sprintf("%.2f", $da);
                             }
                             if ($pa > 0) {
                                 $pa = sprintf("%.2f", $pa);
                             }
                             if ($index_name == 'citation_score') {
                                 $pdf_citations[$citation_index]['domain'] = $domain;
                                 $pdf_citations[$citation_index]['varified'] = $varified_text;
                                 $pdf_citations[$citation_index]['name'] = $name;
                                 $pdf_citations[$citation_index]['full_address'] = $full_address;
                                 $pdf_citations[$citation_index]['phone'] = $phone;
                                 $pdf_citations[$citation_index]['url'] = $url;
                                 $pdf_citations[$citation_index]['street_address'] = $street_address;
                                 $pdf_citations[$citation_index]['city'] = $city;
                                 $pdf_citations[$citation_index]['state'] = $state;
                                 $pdf_citations[$citation_index]['zip'] = $state;
                                 $pdf_citations[$citation_index]['da'] = $da;
                                 $pdf_citations[$citation_index]['pa'] = $pa;
                                 $pdf_citations[$citation_index]['citation_link'] = $citation_link;
                                 $citation_index++;
                             }
                             ?>
                  <tr>
                     <td style="text-indent: 5px;">
                        <?php
                           $root_domain = $citation_domain = $row_c->name;
                           $ex_domain = explode(".", $root_domain);
                           $count_ex_domain = count($ex_domain);
                           if ($count_ex_domain > 2) {
                               $root_domain = $ex_domain[$count_ex_domain - 2] . $ex_domain[$count_ex_domain - 1];
                               $citation_domain = $ex_domain[$count_ex_domain - 2] . '.' . $ex_domain[$count_ex_domain - 1];
                           }
                           ?>
                        <a onclick="citation_profile_func('<?php echo str_replace(".", "", $root_domain); ?>', '<?php echo $citation_domain; ?>', 'Y', '<?php echo $varified_text; ?>')"> 
                        <?php
                           echo $domain;
                           ?>
                        </a>
                     </td>
                     <td style="background: <?php echo box_color($varified_text) ?>" class="tcenter"><?php echo $varified_text; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($name) ?>"><?php echo $name; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($full_address) ?>;"><?php echo $full_address; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($phone) ?>"><?php echo $phone; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($url) ?>"><?php echo $url; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($street_address) ?>"><?php echo $street_address; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($city) ?>"><?php echo $city; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($state) ?>"><?php echo $state; ?></td>
                     <td style="text-align:center;background: <?php echo box_color($zip) ?>"><?php echo $zip; ?></td>
                     <td style="text-align:center;"><?php echo $da; ?></td>
                     <td style="text-align:center;"><?php echo $pa; ?></td>
                     <td style="text-align:center;">
                        <a target="_blank" href="<?php echo $citation_link; ?>">View</a>
                     </td>
                  </tr>
                  <?php
                     }
                     }
                     ?>
               </tbody>
            </table>
         </div>
         <?php
            }
            }
            $_SESSION['pdf_citations'] = $pdf_citations;
            ?>
         <!---------- End citation  ---------------->
         <!------ Listings data Start ------->
         <div class="listings all_citaion_div" style="display: none;">
            <table style="font-size:92%!important;width:100%!important;" class="tabl1 table table-striped table-bordered table-hover" cellspacing="0" >
               <thead style="background-color: #888;color:white;">
                  <tr>
                     <th>Directory</th>
                     <th style="width:30%!important;">Listing Business Info.</th>
                     <th>Name</th>
                     <th>Address</th>
                     <th>Phone</th>
                     <th>Website</th>
                     <th>Photos</th>
                     <th>Videos</th>
                  </tr>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($listings_data->listings as $row_c) {
                         ?>
                  <tr>
                     <td class="tcenter"><?php echo $row_c->name; ?></td>
                     <td class="tcenter" style="width:30%!important;">
                        <?php
                           echo '<b>' . $row_c->listingBusinessName . '</b><br/>';
                           echo $row_c->listingAddress . '<br/>';
                           echo $row_c->listingPhone . '<br/>';
                           echo $row_c->listingSiteUrl . '<br/>';
                           ?>
                     </td>
                     <td class="tcenter <?php echo $row_c->hasBusinessName . 'class'; ?>"><?php echo $row_c->hasBusinessName; ?></td>
                     <td class="tcenter <?php echo $row_c->hasAddress . 'class'; ?>"><?php echo $row_c->hasAddress; ?></td>
                     <td class="tcenter <?php echo $row_c->hasPhone . 'class'; ?>"><?php echo $row_c->hasPhone; ?></td>
                     <td class="tcenter <?php echo $row_c->hasSiteUrl . 'class'; ?>"><?php echo $row_c->hasSiteUrl; ?></td>
                     <td class="tcenter"><?php echo $row_c->listingPhotoCount; ?></td>
                     <td class="tcenter"><?php echo $row_c->listingVideoCount; ?></td>
                  </tr>
                  <?php
                     }
                     ?>
               </tbody>
            </table>
         </div>
         <!------- Listings data End ------>
         <!------ Citation by site Start ------->
         <div class="citations_by_site all_citaion_div" style="display: none;">
            <table style="font-size:92%!important;width:100%!important;" class="tabl_citations_by_site table table-striped table-bordered table-hover" cellspacing="0" >
               <thead style="background-color: #888;color:white;">
                  <tr>
                     <th>Citation Site</th>
                     <th style="text-align:center;">Num. Citations for Site</th>
                  </tr>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($domain_name_arr as $domain_name => $row_c) {
                         ?>
                  <tr>
                     <td style="text-indent: 5px;"><?php echo $domain_name; ?></td>
                     <td style="text-align:center;"><?php echo $row_c; ?></td>
                  </tr>
                  <?php
                     }
                     ?>
               </tbody>
            </table>
         </div>
         <!-------Citation by site End ------>
         <!----------------- Competitive Citation Start ----------------->
         <div class="competitive_citation all_citaion_div" style="display: none;">
            <table style="font-size:92%!important;width:100%!important;" class="tabl1 table table-striped table-bordered table-hover" cellspacing="0" >
               <thead style="background-color: #888;color:white;">
                  <tr>
                     <th>Keyword</th>
                     <th>Google Location</th>
                     <th>Result Type</th>
                     <th>Rank</th>
                     <th>Business Name</th>
                     <th>Verified Citations</th>
                     <th>Mentions</th>
                     <th>Avg. DA</th>
                     <th>Avg. PA</th>
                     <th>Avg. Moz Rank</th>
                     <th>Avg. Backlinks</th>
                     <!--<th>View Citation</th>-->
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($competitorCitationData as $com_index => $row_c) {
                         //pr($row_c);
                         $citationReportCitationsId = str_replace("/citations", "", $row_c->citationReportCitationsId);
                         ?>
                  <tr>
                     <td style="text-indent: 5px;"><?php echo $pdf_competitive_citations[$com_index]['keyword'] = $row_c->serpData->keywordSearch->keyword; ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['googleLocation'] = $row_c->serpData->keywordSearch->googleLocation; ?></td>
                     <td style="text-align: center;"><?php echo $row_c->serpData->serpResult->resultType; ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['rank'] = $row_c->serpData->serpResult->rank; ?></td>
                     <td style="text-align: center;" >
                        <a onclick="single_competitor_citaion('<?php echo $citationReportCitationsId; ?>')">
                        <?php echo $pdf_competitive_citations[$com_index]['title'] = $row_c->serpData->serpResult->title; ?>
                        </a> 
                        <span id="business_name_<?php echo $citationReportCitationsId; ?>" style="display: none;"><?php echo $row_c->serpData->serpResult->title; ?></span>
                     </td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['totalVerifiedCitations'] = $row_c->citationReportRun->totalVerifiedCitations; ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['totalMentions'] = $row_c->citationReportRun->totalMentions; ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['averageDomainAuthority'] = sprintf("%.2f", $row_c->citationReportRun->averageDomainAuthority); ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['averagePageAuthority'] = sprintf("%.2f", $row_c->citationReportRun->averagePageAuthority); ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['averageMozRank'] = sprintf("%.2f", $row_c->citationReportRun->averageMozRank); ?></td>
                     <td style="text-align: center;"><?php echo $pdf_competitive_citations[$com_index]['averageLinks'] = sprintf("%.2f", $row_c->citationReportRun->averageLinks); ?></td>
                     <!--<td style="text-align: center;"><a onclick="single_competitor_citaion('<?php echo $citationReportCitationsId; ?>')">View</a></td>-->
                  </tr>
                  <?php
                     }
                     $_SESSION['pdf_competitive_citations'] = $pdf_competitive_citations;
                     ?>
               </tbody>
            </table>
         </div>
         <div style="clear:both;height:20px;"></div>
         <!---------- Competitive Citation End  ---------------->
         <!--- Single Competitor Citaion Popup Start -------------->
         <div id="responsive" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" style="width:95%!important;" >
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                     <?php
                        $sql = "SELECT * FROM `wp_citation_competitor` WHERE `citation_tracker_id` = $citation_tracker_id";
                        $all_competitor_citations = $wpdb->get_results($sql);
                        
                        foreach ($all_competitor_citations as $single_competitor_citaions) {
                            $single_competitor_citaions_data = json_decode($single_competitor_citaions->citations);
                            //pr($single_competitor_citaions_data[0]->citations);exit;
                            ?>
                     <div class="<?php echo $single_competitor_citaions_data[0]->citationReportId; ?> single_citations">
                        <b>Business Name: </b><span id="business_<?php echo $single_competitor_citaions_data[0]->citationReportId; ?>">Enfusen Coprp</span>
                        <div style="clear:both;height:20px;"></div>
                        <table style="font-size:92%!important;width:100%!important;" class="single_citations_table table table-striped table-bordered table-hover" cellspacing="0" >
                           <thead style="background-color: #888;color:white;">
                              <tr>
                                 <th>Site</th>
                                 <th>Verified</th>
                                 <th>Name</th>
                                 <th title="Full Address">FA</th>
                                 <th>Phone</th>
                                 <th>URL</th>
                                 <th title="Street Address">SA</th>
                                 <th>City</th>
                                 <th>State</th>
                                 <th>Zip</th>
                                 <th>PA</th>
                                 <th>DA</th>
                                 <th title="Citation Link">Link</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                                 foreach ($single_competitor_citaions_data[0]->citations as $row_citaions) {
                                     $isCitationVerified = $row_citaions->isCitationVerified;
                                     $isCitationVerified = $isCitationVerified == 1 ? 'Y' : 'N';
                                     $sc_pa = $sc_da = '';
                                     $mozRank = 0;
                                     if (isset($row_citaions->seoMozData->domainAuthority)) {
                                         // pr($row_citaions);exit;
                                         $sc_da = round($row_citaions->seoMozData->domainAuthority, 2);
                                         $mozRank = round($row_citaions->seoMozData->rootDomainMozRank, 2);
                                         // $sc_da = sprintf("%.2f", $row_citaions->seoMozData->domainAuthority);
                                         $sc_pa = round($row_citaions->seoMozData->pageAuthority, 2);
                                     }
                                     if (isset($citation_opp_arr[$row_citaions->name])) {
                                         $citation_opp_arr[$row_citaions->name]['num_competitor'] += 1;
                                         if ($sc_da > $citation_opp_arr[$row_citaions->name]['DA']) {
                                             $citation_opp_arr[$row_citaions->name]['DA'] = $sc_da;
                                         }
                                         if ($mozRank > $citation_opp_arr[$row_citaions->name]['mozRank']) {
                                             $citation_opp_arr[$row_citaions->name]['mozRank'] = $mozRank;
                                         }
                                     } else {
                                         $citation_opp_arr[$row_citaions->name]['site'] = $row_citaions->name;
                                         $citation_opp_arr[$row_citaions->name]['link'] = $row_citaions->link;
                                         $citation_opp_arr[$row_citaions->name]['DA'] = $sc_da;
                                         $citation_opp_arr[$row_citaions->name]['num_competitor'] = 1;
                                         $citation_opp_arr[$row_citaions->name]['mozRank'] = $mozRank;
                                         if (isset($citaion_site_list[$row_citaions->name])) {
                                             $citation_opp_arr[$row_citaions->name]['total_citaion'] = $citaion_site_list[$row_citaions->name];
                                         } else {
                                             $citation_opp_arr[$row_citaions->name]['total_citaion'] = 0;
                                         }
                                     }
                                     ?>
                              <tr>
                                 <td style="text-indent: 5px;"><?php echo $row_citaions->name; ?></td>
                                 <td class="<?php echo $isCitationVerified . 'class' ?> tcenter"><?php echo $isCitationVerified; ?></td>
                                 <td class="<?php echo $row_citaions->hasBusinessName . 'class' ?> tcenter"><?php echo $row_citaions->hasBusinessName; ?></td>
                                 <td class="<?php echo $row_citaions->hasAddress . 'class' ?> tcenter"><?php echo $row_citaions->hasAddress; ?></td>
                                 <td class="<?php echo $row_citaions->hasPhone . 'class' ?> tcenter"><?php echo $row_citaions->hasPhone; ?></td>
                                 <td class="<?php echo $row_citaions->hasSiteUrl . 'class' ?> tcenter"><?php echo $row_citaions->hasSiteUrl; ?></td>
                                 <td class="<?php echo $row_citaions->hasStreetAddress . 'class' ?> tcenter"><?php echo $row_citaions->hasStreetAddress; ?></td>
                                 <td class="<?php echo $row_citaions->hasCity . 'class' ?> tcenter"><?php echo $row_citaions->hasCity; ?></td>
                                 <td class="<?php echo $row_citaions->hasState . 'class' ?> tcenter"><?php echo $row_citaions->hasState; ?></td>
                                 <td class="<?php echo $row_citaions->hasZip . 'class' ?> tcenter"><?php echo $row_citaions->hasZip; ?></td>
                                 <td class="tcenter"><?php echo $sc_pa; ?></td>
                                 <td class="tcenter"><?php echo $sc_da; ?></td>
                                 <td class="tcenter"><a href="<?php echo $row_citaions->link; ?>" target="_blank">Link</a></td>
                              </tr>
                              <?php
                                 }
                                 ?>
                           </tbody>
                        </table>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
         <!--- Single Competitor Citaion Popup Start -------------->
         <!------ Citation Opportunities Start ------->
         <div class="citations_opp all_citaion_div" style="display: none;">
            <table style="font-size:92%!important;width:100%!important;" class="citations_opp_table table table-striped table-bordered table-hover" cellspacing="0" >
               <thead style="background-color: #888;color:white;">
                  <tr>
                     <th>Citation Site</th>
                     <th class="tcenter">Num. Competitor</th>
                     <th class="tcenter">Have Citation?</th>
                     <th class="tcenter">Total Citations </th>
                     <th class="tcenter">DA</th>
                     <th class="tcenter">Domain MozRank</th>
                     <!--<th class="tcenter">Action</th>-->
                  </tr>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $all_opp_site = array();
                     foreach ($citation_opp_arr as $opp_index => $row_c) {
                         if ($row_c['total_citaion'] > 0) {
                             $have_citation = 'Y';
                         } else {
                             $have_citation = 'N';
                         }
                     
                     
                         $root_domain = $citation_domain = $all_opp_site[] = $row_c['site'];
                         $ex_domain = explode(".", $root_domain);
                         $count_ex_domain = count($ex_domain);
                         if ($count_ex_domain > 2) {
                             $root_domain = $ex_domain[$count_ex_domain - 2] . $ex_domain[$count_ex_domain - 1];
                             $citation_domain = $ex_domain[$count_ex_domain - 2] . '.' . $ex_domain[$count_ex_domain - 1];
                         }
                     
                         if (isset($citation_status_arr[$opp_index])) {
                             $status_type = $citation_status_arr[$opp_index];
                         } else {
                             $status_type = 'O'; //opportunities status
                         }
                         ?>
                  <!--<tr id="citation_profile_main_data_for_<?php echo str_replace(".", "", $root_domain); ?>">-->
                  <tr>
                     <td style="text-indent: 5px;">
                        <a onclick="citation_profile_func('<?php echo str_replace(".", "", $root_domain); ?>', '<?php echo $citation_domain; ?>', '<?php echo $have_citation; ?>', '<?php echo $status_type; ?>')">  
                        <?php echo $pdf_competitor_citation_opp[$opp_index]['site'] = $row_c['site']; ?>
                        </a>
                     </td>
                     <td class="tcenter"><?php echo $pdf_competitor_citation_opp[$opp_index]['num_competitor'] = $row_c['num_competitor']; ?></td>
                     <td class="tcenter <?php echo $have_citation . 'class' ?>"><?php echo $pdf_competitor_citation_opp[$opp_index]['have_citation'] = $have_citation; ?></td>
                     <td class="tcenter"><?php echo $pdf_competitor_citation_opp[$opp_index]['total_citaion'] = $row_c['total_citaion']; ?></td>
                     <td class="tcenter"><?php echo $pdf_competitor_citation_opp[$opp_index]['DA'] = $row_c['DA']; ?></td>
                     <td class="tcenter"><?php echo $pdf_competitor_citation_opp[$opp_index]['mozRank'] = $row_c['mozRank']; ?></td>
                     <!--td class="tcenter">
                        <a href="<?php echo $row_c['link']; ?>" target="_blank"> Open Site </a>
                        </td-->
                  </tr>
                  <?php
                     }
                     $_SESSION['pdf_competitor_citation_opp'] = $pdf_competitor_citation_opp;
                     ?>
               </tbody>
            </table>
         </div>
         <!-------Citation Opportunities End ------>
         <!------ History Start ------->
         <div class="history all_citaion_div" style="display: none;">
            <table style="font-size:92%!important;width:100%!important;" class="history_table table table-striped table-bordered table-hover" cellspacing="0" >
               <thead style="background-color: #888;color:white;">
                  <tr>
                     <th class="tcenter">Run Date</th>
                     <th class="tcenter">Citation Score</th>
                     <th class="tcenter">Verified</th>
                     <th class="tcenter">Needs Attention</th>
                     <th class="tcenter">Opportunity</th>
                  </tr>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($all_complete_citations as $row_history) {
                         $calculate_info = unserialize($row_history->calculate_info);
                         ?>
                  <tr>
                     <td class="tcenter"><?php echo date("d M Y h:i a", strtotime($row_history->last_run)); ?></td>
                     <td class="tcenter"><?php echo $calculate_info['citation_score'] . '%'; ?></td>
                     <td class="tcenter"><?php echo $calculate_info['verified_citaions']; ?></td>
                     <td class="tcenter"><?php echo $calculate_info['needs_attention']; ?></td>
                     <td class="tcenter"><?php echo $calculate_info['citation_opp_count']; ?></td>
                  </tr>
                  <?php
                     }
                     ?>
               </tbody>
            </table>
         </div>
         <!-------History End ------>
         <!----- Citation Recommendation part start------>
         <div class="citation_recommendation all_citaion_div">
            <table class="citation_recommendation_table table table-striped table-bordered table-hover" cellspacing="0">
               <thead>
                  <tr>
                     <th>Domain</th>
                     <th>Status</th>
                     <th>Name</th>
                     <th>Full Address</th>
                     <th>Phone</th>
                     <th>URL</th>
                     <th>Street Address</th>
                     <th>City</th>
                     <th>State</th>
                     <th>Zip</th>
                     <th style="width:25%;">What to do</th>
                     <th>num. Competitor</th>
                     <th>DA</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     //$citation_site = arrray();
                     foreach ($all_citations as $row_c) {
                         if ($row_c->isCitationVerified == 1) {
                             $varified_text = 'Y';
                         } else {
                             $varified_text = 'N';
                         }
                         if (!in_array($row_c->name, $citation_site)) {
                             $citation_site[] = $row_c->name;
                             if ($varified_text == 'N') {
                                 $what_to_do = array();
                                 if ($row_c->hasBusinessName == 'N') {
                                     $what_to_do[] = 'Name';
                                 }
                                 if ($row_c->hasAddress == 'N') {
                                     $what_to_do[] = 'Address';
                                 }
                                 if ($row_c->hasPhone == 'N') {
                                     $what_to_do[] = 'Phone';
                                 }
                                 if ($row_c->hasSiteUrl == 'N') {
                                     $what_to_do[] = 'URL';
                                 }
                                 if ($row_c->hasStreetAddress == 'N') {
                                     $what_to_do[] = 'Street Address';
                                 }
                                 if ($row_c->hasCity == 'N') {
                                     $what_to_do[] = 'City';
                                 }
                                 if ($row_c->hasZip == 'N') {
                                     $what_to_do[] = 'Zip';
                                 }
                                 $last_item = '';
                                 if (count($what_to_do) > 1) {
                                     $last_item = ' & ' . $what_to_do[count($what_to_do) - 1];
                                     unset($what_to_do[count($what_to_do) - 1]);
                                 }
                                 $what_to_do_text = 'Verify - ' . implode(", ", $what_to_do) . $last_item;
                     
                                 $root_domain = $citation_domain = $row_c->name;
                                 $ex_domain = explode(".", $root_domain);
                                 $count_ex_domain = count($ex_domain);
                                 if ($count_ex_domain > 2) {
                                     $root_domain = $ex_domain[$count_ex_domain - 2] . $ex_domain[$count_ex_domain - 1];
                                     $citation_domain = $ex_domain[$count_ex_domain - 2] . '.' . $ex_domain[$count_ex_domain - 1];
                                 }
                                 ?>
                  <tr>
                     <td style="text-indent: 10px;">
                        <a onclick="citation_profile_func('<?php echo str_replace(".", "", $root_domain); ?>', '<?php echo $citation_domain; ?>', 'Y', '<?php echo $varified_text; ?>')"> 
                        <?php
                           echo $row_c->name;
                           ?>
                        </a> 
                     </td>
                     <td class="tcenter needs_att">Needs Attention</td>
                     <td class="tcenter <?php echo $row_c->hasBusinessName . 'class'; ?>"><?php echo $row_c->hasBusinessName; ?></td>
                     <td class="tcenter <?php echo $row_c->hasAddress . 'class'; ?>"><?php echo $row_c->hasAddress; ?></td>
                     <td class="tcenter <?php echo $row_c->hasPhone . 'class'; ?>"><?php echo $row_c->hasPhone; ?></td>
                     <td class="tcenter <?php echo $row_c->hasSiteUrl . 'class'; ?>"><?php echo $row_c->hasSiteUrl; ?></td>
                     <td class="tcenter <?php echo $row_c->hasStreetAddress . 'class'; ?>"><?php echo $row_c->hasStreetAddress; ?></td>
                     <td class="tcenter <?php echo $row_c->hasCity . 'class'; ?>"><?php echo $row_c->hasCity; ?></td>
                     <td class="tcenter <?php echo $row_c->hasState . 'class'; ?>"><?php echo $row_c->hasState; ?></td>
                     <td class="tcenter <?php echo $row_c->hasZip . 'class'; ?>"><?php echo $row_c->hasZip; ?></td>
                     <td style="text-indent:5px;width:25%;"><?php echo $what_to_do_text; ?></td>
                     <td class="tcenter"><?php echo $citation_opp_arr[$row_c->name]['num_competitor']; ?></td>
                     <td class="tcenter"><?php echo $citation_opp_arr[$row_c->name]['DA']; ?></td>
                  </tr>
                  <?php
                     }
                     }
                     }
                     foreach ($citation_opp_arr as $opp_index => $row_opp) {
                     if (!in_array($row_opp['site'], $citation_site)) {
                     
                     if ($row_opp['total_citaion'] > 0) {
                         $have_citation = 'Y';
                     } else {
                         $have_citation = 'N';
                     }
                     
                     
                     $root_domain = $citation_domain = $row_opp['site'];
                     $ex_domain = explode(".", $root_domain);
                     $count_ex_domain = count($ex_domain);
                     if ($count_ex_domain > 2) {
                         $root_domain = $ex_domain[$count_ex_domain - 2] . $ex_domain[$count_ex_domain - 1];
                         $citation_domain = $ex_domain[$count_ex_domain - 2] . '.' . $ex_domain[$count_ex_domain - 1];
                     }
                     
                     if (isset($citation_status_arr[$opp_index])) {
                         $status_type = $citation_status_arr[$opp_index];
                     } else {
                         $status_type = 'O'; //opportunities status
                     }
                     ?>
                  <tr>
                     <td style="text-indent: 10px;">
                        <a onclick="citation_profile_func('<?php echo str_replace(".", "", $root_domain); ?>', '<?php echo $citation_domain; ?>', '<?php echo $have_citation; ?>', '<?php echo $status_type; ?>')">  
                        <?php echo $row_opp['site']; ?>
                        </a>
                     </td>
                     <td class="tcenter opportunity">Opportunity</td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td style="width:25%;"></td>
                     <td class="tcenter"><?php echo $row_opp['num_competitor']; ?></td>
                     <td class="tcenter"><?php echo $row_opp['DA']; ?></td>
                  </tr>
                  <?php
                     }
                     }
                     ?>
               </tbody>
            </table>
         </div>
         <style>
            .table { display: table; }
            .head_row { display: table-row; }
            .cell { display: table-cell;border: 1px solid #e7ecf1; padding:5px; text-align: center;}
         </style>
         <?php
            foreach ($citation_opp_arr as $opp_index => $row_opp) {
            
            
                if ($row_opp['total_citaion'] > 0) {
                    $have_citation = 'Y';
                } else {
                    $have_citation = 'N';
                }
            
            
                $root_domain = $citation_domain = $row_opp['site'];
                $ex_domain = explode(".", $root_domain);
                $count_ex_domain = count($ex_domain);
                if ($count_ex_domain > 2) {
                    $root_domain = $ex_domain[$count_ex_domain - 2] . $ex_domain[$count_ex_domain - 1];
                    $citation_domain = $ex_domain[$count_ex_domain - 2] . '.' . $ex_domain[$count_ex_domain - 1];
                }
            
                if (isset($citation_status_arr[$opp_index])) {
                    $status_type = $citation_status_arr[$opp_index];
                } else {
                    $status_type = 'O'; //opportunities status
                }
                ?>
         <div id="citation_profile_main_data_for_<?php echo str_replace(".", "", $root_domain); ?>" style="display: none;">
            <div class="table">
               <div class="head_row" style="font-weight: bold;">
                  <div class="cell">Citation Site</div>
                  <div class="cell">Have Citation?</div>
                  <div class="cell">Num. Competitor</div>
                  <div class="cell">Total Citations</div>
                  <div class="cell">DA</div>
                  <div class="cell">Domain MozRank</div>
                  <!--<div class="cell">Action</div>-->
               </div>
               <div class="head_row">
                  <div class="cell"><?php echo $row_opp['site']; ?></div>
                  <div class="cell <?php echo $have_citation . 'class' ?>"><?php echo $have_citation; ?></div>
                  <div class="cell"><?php echo $row_opp['num_competitor']; ?></div>
                  <div class="cell"><?php echo $row_opp['total_citaion']; ?></div>
                  <div class="cell"><?php echo $row_opp['DA']; ?></div>
                  <div class="cell"><?php echo $row_opp['mozRank']; ?></div>
                  <!--<div class="cell"><a target="_blank" href="<?php echo $row_opp['link']; ?>">Open Site</a></div>-->
               </div>
            </div>
            <div class="clear_both"></div>
         </div>
         <?php
            }
            ?>
         <!------Citation recommendation part end ----->
         <style>
            .dataTables_info{display: none;}
            #have_citation_div .dataTables_filter,#have_citation_div .dataTables_length, #have_citation_div .dataTables_paginate{display: none!important;}
            #citation_profile_competitor_div .dataTables_filter,#citation_profile_competitor_div .dataTables_length, #citation_profile_competitor_div .dataTables_paginate{display: none!important;}
         </style>
         <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery.validate.js"></script>
         <div id="citation_profile" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" style="width:95%!important;" >
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                     <div style="font-weight: bold;font-size: 20px;float:left;">Citation Profile [<span id="citation_domain"></span>] </div>
                     <div style="font-weight: bold;font-size: 20px;float:right;margin-right: 30px;">Status: <span style="margin-left:10px;" id="status_type"></span> </div>
                     <div class="clear_both"></div>
                     <div id="have_citation_div">
                        <table style="font-size:92%!important;width:100%!important;" class="tabl_citation_profile table table-striped table-bordered table-hover" cellspacing="0" >
                           <thead style="background-color: #888;color:white;">
                              <tr>
                                 <th>Domain</th>
                                 <th>Verified</th>
                                 <th>Name</th>
                                 <th>Full Address</th>
                                 <th>Phone</th>
                                 <th>URL</th>
                                 <th>Street Address</th>
                                 <th>City</th>
                                 <th>State</th>
                                 <th>Zip</th>
                                 <th>DA</th>
                                 <th>PA</th>
                                 <th>Link</th>
                              </tr>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                                 foreach ($pdf_citations as $row_citation) {
                                     $domain = $root_domain = $row_citation['domain'];
                                     $ex_domain = explode(".", $domain);
                                     $count_ex_domain = count($ex_domain);
                                     if ($count_ex_domain > 2) {
                                         $root_domain = $ex_domain[$count_ex_domain - 2] . $ex_domain[$count_ex_domain - 1];
                                     }
                                     $pdf_citations[$citation_index]['domain'] = $domain;
                                     $pdf_citations[$citation_index]['varified'] = $varified_text;
                                     $pdf_citations[$citation_index]['name'] = $name;
                                     $pdf_citations[$citation_index]['full_address'] = $full_address;
                                     $pdf_citations[$citation_index]['phone'] = $phone;
                                     $pdf_citations[$citation_index]['url'] = $url;
                                     $pdf_citations[$citation_index]['street_address'] = $street_address;
                                     $pdf_citations[$citation_index]['city'] = $city;
                                     $pdf_citations[$citation_index]['state'] = $state;
                                     $pdf_citations[$citation_index]['zip'] = $state;
                                     $pdf_citations[$citation_index]['da'] = $da;
                                     $pdf_citations[$citation_index]['pa'] = $pa;
                                     $pdf_citations[$citation_index]['citation_link'] = $citation_link;
                                     ?>
                              <tr class="<?php echo str_replace(".", "", $root_domain); ?> c_p_div">
                                 <td class="tcenter <?php echo $name . 'class' ?>"><?php echo $domain; ?></td>
                                 <td class="tcenter <?php echo $row_citation['varified'] . 'class' ?>"><?php echo $row_citation['varified']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['name'] . 'class' ?>"><?php echo $row_citation['name']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['full_address'] . 'class' ?>"><?php echo $row_citation['full_address']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['phone'] . 'class' ?>"><?php echo $row_citation['phone']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['url'] . 'class' ?>"><?php echo $row_citation['url']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['street_address'] . 'class' ?>"><?php echo $row_citation['street_address']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['city'] . 'class' ?>"><?php echo $row_citation['city']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['state'] . 'class' ?>"><?php echo $row_citation['state']; ?></td>
                                 <td class="tcenter <?php echo $row_citation['zip'] . 'class' ?>"><?php echo $row_citation['zip']; ?></td>
                                 <td class="tcenter"><?php echo $row_citation['da']; ?></td>
                                 <td class="tcenter"><?php echo $row_citation['pa']; ?></td>
                                 <td class="tcenter">
                                    <a href="<?php echo $row_citation['citation_link']; ?>" target="_blank">Link</a>
                                 </td>
                              </tr>
                              <?php
                                 }
                                 ?>
                           </tbody>
                        </table>
                     </div>
                     <div class="clear_both"></div>
                     <!---------->
                     <div id="citation_profile_competitor_div" style="display: none;">
                        <div id="citation_profile_competitor_data">
                        </div>
                     </div>
                     <!---------->
                     <div style="width:60%;float:left;" id="ins_div_<?php echo str_replace(".", "", $root_domain); ?>">
                        <b>Instructions on how to claim citation:</b><br/>
                        <span id="popup_ins"></span>
                        <div class="clear_both"></div>
                        <b>Login URL:</b><br/>
                        <a target="_blank" id="popup_login_url"></a>
                     </div>
                     <!---------->
                     <div style="width:37%;float:right;">
                        <b>Citation Login Details:</b>
                        <form id="save_login_Frm" action="" method="post">
                           <div class="clear_both"></div>
                           <div style="width:40%;;float:left;font-weight: bold;">Login User ID: </div>
                           <div style="width:60%;;float:left;">
                              <input type="hidden" name="domain_name" id="domain_name" value="">
                              <input class="required" type="text" name="login_username" id="login_username" value="">
                           </div>
                           <div class="clear_both"></div>
                           <div style="width:40%;;float:left;font-weight: bold;">Login Password: </div>
                           <div style="float:left;width:60%;">
                              <input class="required" type="text" name="login_password" id="login_password" value="">
                              <a class="btn btn-success" id="show_btn" onclick="jQuery('#login_password').show();jQuery('#show_btn').hide();"> Show </a>
                           </div>
                           <div class="clear_both"></div>
                           <div style="width:40%;;float:left;font-weight: bold;">&nbsp;</div>
                           <div style="float:left;width:60%;">
                              <input onclick="save_login_info_func()" style="background:none;" class="btn btn-success" name="save_login_info" id="save_login_info" value="Save" type="button">
                           </div>
                           <div class="clear_both"></div>
                        </form>
                     </div>
                     <!---------->
                     <div class="clear_both"></div>
                     <div style="width:90%;float:left;">
                        <div id="all_notes_html"></div>
                        <div class="clear_both"></div>
                        <form id="profile_notes_Frm" action="" method="post">
                           <b>Notes:</b><br/>
                           <textarea name="profile_notes" id="profile_notes" class="required" style="width:100%; height:100px;"></textarea>
                           <input type="hidden" name="pop_domain" id="pop_domain" value="">
                           <input type="hidden" name="notes_url" id="notes_url" value="">
                           <div class="clear_both"></div>
                           <input type="button" style="background:none;" class="btn btn-success" onclick="save_submit_notes()" name="profile_notes_btn" value="Submit Notes">
                        </form>
                     </div>
                     <div class="clear_both"></div>
                     <div class="clear_both"></div>
                     <div class="clear_both"></div>
                  </div>
               </div>
            </div>
         </div>
         <script>
            // jQuery('#profile_notes_Frm').validate();
            
            function save_submit_notes() {
                var notes_url = jQuery('#notes_url').val();
                var profile_notes = jQuery('#profile_notes').val();
                if (profile_notes == '') {
                    alert('Please enter profile note!');
                    return false;
                }
                var domain = jQuery('#pop_domain').val();
                //alert(profile_notes); return false;
            
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo site_url(); ?>/ajax-data.php',
                    data:
                            {
                                'page': 'citation_profile_notes',
                                'user_id': '<?php echo $user_id; ?>',
                                'notes_url': notes_url,
                                'profile_notes': profile_notes,
                            },
                    dataType: 'json',
                    success: function (data)
                    {
                        //jQuery('#notes_' + domain).html(profile_notes);
                        jQuery('#profile_notes').val(null);
                        all_notes_html = data['all_notes_html'];
                        jQuery('#all_notes_html').html(all_notes_html);
                        alert('Successfully Saved.');
            
                    }
                });
                return false; // keeps the page from not refreshing  
            }
            
            // jQuery('#save_login_Frm').validate();
            function save_login_info_func() {
                var domain_name = jQuery('#domain_name').val();
                //alert(domain_name);
            
                var login_username = jQuery('#login_username').val();
                var login_password = jQuery('#login_password').val();
                var pop_domain = jQuery('#pop_domain').val();
                //alert(login_username);
                if (login_username == '') {
                    alert('Please enter login username!');
                    return false;
                }
                if (login_password == '') {
                    alert('Please enter login password!');
                    return false;
                }
            
                if (login_username != '' && login_password != "") {
            
                    jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo site_url(); ?>/ajax-data.php',
                        data:
                                {
                                    'page': 'citation_profile_login_details',
                                    'user_id': '<?php echo $user_id; ?>',
                                    'domain_name': domain_name,
                                    'login_username': login_username,
                                    'login_password': login_password,
                                },
                        dataType: 'json',
                        success: function (data)
                        {
                            // jQuery('#notes_' + domain).html(profile_notes);
            
                            //login_details_div
                            if (jQuery('#login_username_' + pop_domain).length > 0) {
                                jQuery('#login_username_' + pop_domain).html(login_username);
                                jQuery('#login_password_' + pop_domain).html(login_password);
                            } else {
                                var html = '<span style="display: none;" id="login_username_' + pop_domain + '">' + login_username + '</span><span style="display: none;" id="login_password_' + pop_domain + '">' + login_password + '</span>';
                                jQuery('#login_password').hide();
                                jQuery('#show_btn').show();
                                alert('Successfully Saved.');
                                jQuery('#login_details_div').append(html);
                            }
            
                        }
                    });
                }
            
            }
         </script>
         <!------- Citation Profile End ------->
         <?php
            $total_citaion = count($all_citations);
            
            $citation_score = sprintf("%.2f", ($verified_citaions / $total_citaion) * 100);
            $citation_opp_count = count($citation_opp_arr);
            $_SESSION['citation_opp_arr'] = $citation_opp_arr; //its need ajax data page for citation profile
            //pr($_SESSION['citation_opp_arr']['facebook.com']);
            $box_arr['citation_score']['val'] = $citation_score . '%';
            $box_arr['citation_score']['color'] = '#3598DC';
            
            $box_arr['verified']['val'] = $verified_citaions;
            $box_arr['verified']['color'] = '#32C4D3';
            
            $box_arr['Needs_Attention']['val'] = $needs_attention;
            $box_arr['Needs_Attention']['color'] = '#8F44AD';
            $box_arr['Opportunity']['val'] = $citation_opp_count;
            $box_arr['Opportunity']['color'] = '#E9505C';
            
            $_SESSION['box_arr'] = $box_arr;
            ?>
         <script>
            function request_citation_rerun() {
                var con = confirm('Are you sure you want to re-run citation?');
                if (con) {
                    jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo site_url(); ?>/ajax-data.php',
                        data: {'page': 'request_citation', 'user_id': <?php echo $user_id; ?>},
                        success: function (html_data)
                        {
                            //alert(html_data);
                            alert('Successfully sent a request to re-rerun for <?php echo brand_name($user_id); ?>. Thanks!');
                        }
                    });
            
                } else {
                    return false;
                }
            }
            function citation_profile_func(domain, citation_domain, have_citation, status_type) {
                jQuery('#all_notes_html').html(null);
            
                jQuery('#all_notes_html').html('<img style="margin-left:20px;" src="<?php echo get_template_directory_uri() . '/images/point-loader.gif' ?>">');
                jQuery('#citation_profile').modal();
            
            
                jQuery('.c_p_div').hide();
                jQuery('.' + domain).show();
                jQuery('#citation_domain').html(citation_domain);
            
            
            
                jQuery('#popup_ins').html(null);
                jQuery('#popup_ins').html(jQuery('#setup_ins_' + domain).html());
            
                jQuery('#popup_login_url').html(null);
                jQuery('#popup_login_url').html(jQuery('#login_url_' + domain).html());
            
                jQuery('#domain_name').val(citation_domain);
                var login_username = '';
                if (jQuery('#login_username_' + domain).length > 0) {
                    login_username = jQuery('#login_username_' + domain).html();
                }
                //alert(login_username);
                jQuery('#login_username').val(login_username);
            
                jQuery('#login_password').val(jQuery('#login_password_' + domain).html());
            
                jQuery('#login_password').show();
                jQuery('#show_btn').hide();
                if (login_username != '') {
                    jQuery('#login_password').hide();
                    jQuery('#show_btn').show();
                    jQuery('#save_login_info').val('Update');
                } else {
                    jQuery('#save_login_info').val('Save');
                }
            
            
            
                jQuery('#notes_url').val(citation_domain);
            
            
            
            
                if (status_type == 'Y') {
                    var status_type_text = '<span style="color:#32C4D3;">Verified</span>';
                } else if (status_type == 'N') {
                    var status_type_text = '<span style="color:#8F44AD;">Needs Attention</span>';
                } else {
                    var status_type_text = '<span style="color:#E9505C;">Opportunity</span>';
                }
                jQuery('#status_type').html(status_type_text);
            
                if (have_citation == 'Y') {
                    jQuery('#have_citation_div').show();
                } else {
                    jQuery('#have_citation_div').hide();
                }
                var citation_profile_main_data = jQuery('#citation_profile_main_data_for_' + domain).html();
                jQuery('#citation_profile_competitor_data').html(citation_profile_main_data);
                jQuery('#citation_profile_competitor_div').show();
            
                jQuery('#pop_domain').val(domain);
            
            
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo site_url(); ?>/ajax-data.php',
                    data:
                            {
                                'page': 'citation_profile_notes',
                                'user_id': '<?php echo $user_id; ?>',
                                'notes_url': citation_domain,
                            },
                    dataType: 'json',
                    success: function (data)
                    {
                        //jQuery('#notes_' + domain).html(profile_notes);
                        all_notes_html = data['all_notes_html'];
                        jQuery('#all_notes_html').html(all_notes_html);
                    }
                });
                //jQuery('#profile_notes').val(profile_notes);
            
            
            }
            function single_competitor_citaion(citationReportId) {
                jQuery('#responsive').modal();
                jQuery('.single_citations').hide();
                var business_name = jQuery('#business_name_' + citationReportId).html();
                jQuery('#business_' + citationReportId).html(business_name);
                jQuery('.' + citationReportId).show();
            }
            jQuery('#citation_score_count').html('<?php echo $citation_score; ?>%');
            jQuery('#verified_count').html('<?php echo $verified_citaions; ?>');
            jQuery('#Needs_Attention_count').html('<?php echo $needs_attention; ?>');
            jQuery('#Opportunity_count').html('<?php echo $citation_opp_count; ?>');
            jQuery(document).ready(function () {
                jQuery('.tabl1').dataTable({
                    "order": [[1, "desc"]],
                    "iDisplayLength": 50
                });
            });
            
            jQuery(document).ready(function () {
                jQuery('.tabl_citations_by_site').dataTable({
                    "order": [[1, "desc"]],
                    "iDisplayLength": 50
                });
            });
            jQuery(document).ready(function () {
                jQuery('.tabl_citation_profile').dataTable({
                    "order": [[1, "desc"]],
                    "iDisplayLength": 500
                });
            });
            jQuery(document).ready(function () {
                jQuery('.single_citations_table').dataTable({
                    "order": [[11, "desc"]],
                    "iDisplayLength": 50
                });
            });
            jQuery(document).ready(function () {
                jQuery('.citations_opp_table').dataTable({
                    "order": [[1, "desc"]],
                    "iDisplayLength": 50
                });
            });
            
            jQuery(document).ready(function () {
                jQuery('.history_table').dataTable({
                    "order": [[0, "desc"]],
                    "iDisplayLength": 50
                });
            });
            function citation_list_func(type) {
                jQuery('.all_citaion_div').hide();
                if (type == 'Opportunity') {
                    citation_func('citations_opp');
                } else if (type == 'citation_score') {
                    citation_func('citation_score');
                } else {
                    jQuery('.' + type).show();
                }
            
            }
            
            function citation_func(div_name) {
                jQuery('.all_citaion_div').hide();
                jQuery('.' + div_name).show();
                jQuery(".btn-success").removeClass("active");
                jQuery(".a_" + div_name).addClass("active");
            }
            
            jQuery(document).ready(function () {
                jQuery('.citation_recommendation_table').dataTable({
                    "order": [[1, "asc"], [12, "desc"]],
                    "iDisplayLength": 100
            
                });
            });
            
         </script>
         <?php
            }
            //}
            ?>
      </div>
   </div>
</div>
<div class="clear_both"></div>
<?php
   if ($result_info->status == 'complete') {
       $current_ReportId = $result_info->ReportId;
       if ($current_ReportId != "") {
           $get_citaion_doamin = get_user_meta($user_id, 'get_citaion_report_id', true);
           if ($get_citaion_doamin != $current_ReportId) {
               update_user_meta($user_id, 'get_citaion_report_id', $current_ReportId);
               $ajax_data = array_merge($citation_site, $all_opp_site);
               $ajax_data = array_unique($ajax_data);
               $ajax_data = implode(",", $ajax_data);
               ?>
<script>
   //function test_func(){   
   jQuery.ajax({
       type: 'POST',
       url: '<?php echo $mcc_url; ?>/cron/citaions-instruction.php',
       data: {'page': 'citation_domain_add', 'user_id': '<?php echo $user_id ?>', 'citation_opp_arr': '<?php echo $ajax_data; ?>'},
       success: function (html_data)
       {
           //alert(html_data);
           //$('#abc').html(html_data);
       }
   });
   // }   
</script>
<?php
   }
   }
   }
   ?>
