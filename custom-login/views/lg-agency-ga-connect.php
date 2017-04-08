<?php
session_start();
// Create file By rudra 02-03-2017
// Code starts By Rudra 02-03-2017 
global $wpdb;
$UserID=$_SESSION['MccUserId'];

$locationUserID=$_SESSION['MccUserId'];
$current_url="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$current_url_re = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/ga-connect/';
$UserLevel = get_user_meta($UserID, "USER_LEVEL", true);

$ACCESS_LEVEL_ASSIGN = get_option("ACCESS_LEVEL_ASSIGN");

$Order_One_Time_Content = get_user_meta($UserID, "Order_One_Time_Content", true);

$Chkmain = $ACCESS_LEVEL_ASSIGN[$UserLevel];
$_SESSION['AnalyticsNewToken'] = true; 

include_once get_template_directory() . '/analytics/AdWordsUtils.php';

include_once get_template_directory() . '/analytics/BrightLocalUtils.php';

include_once get_template_directory() . '/analytics/AnalyticsUtils.php';

$check_clients_table = GetAllRowsFromTableWithSpecifiedColumns('clients_table', ' MCCUserID,Name,AnalyticsToken ', "MCCUserID = $UserID", " `MCCUserID` ASC ");
//pr($check_clients_table);
$AnalyticsToken_check = $check_clients_table[0][2];

if (isset($_POST['parent_analytics_user_id'])) {
    update_user_meta($UserID, 'parent_analytics_user_id', $_POST['parent_analytics_user_id']);

}
$parent_analytics_user_id = get_user_meta($UserID, 'parent_analytics_user_id', true);
?>
<script type="text/javascript">
window.analytics=true;
</script>
<div class="row" id="ga_connective">
             <div class="col-md-12 col-xs-12 pad_bottom_10 text-center">
                                <div class="internal-div align-centre" style="">

                                    <div class="clear_block hundredPercent">
                                        <label class="icon-round"><span><img src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/g-analatics.png" alt="google-analytics"></span></label>
                                    </div>                 
  
            <?php
            if ($AnalyticsToken_check == '') {

                $all_users_list = GetAllRowsFromTableWithSpecifiedColumns('clients_table', ' MCCUserID,Name ', "AnalyticsToken != '' ", " `MCCUserID` ASC ");

                ?>

                <script>
                    function check_parent_analytics() {
                        var parent_analytics_user_id = jQuery('#parent_analytics_user_id').val();
                        if (parent_analytics_user_id == '') {
                            alert('Please select a client!');
                            return false;
                        } else {
                            document.forms.parent_analytics_Frm.submit();
                        }
                    }
                </script>
                <div class="clear_both"></div>


                <?php
            }
            if (!$parent_analytics_user_id > 0) {
                $FromCron = isset($_REQUEST['from_cron']);





                try {

                    if ($FromCron && isset($_REQUEST['client_number'])) {

                        $CurClientNumber = $_REQUEST['client_number'];

                        $AllClientsFromDB = GetAccessTokensFromTable();



                        if ($CurClientNumber < count($AllClientsFromDB)) {

                            $CurClientID = $AllClientsFromDB[$CurClientNumber][0];

                            $_REQUEST['ClientID'] = $CurClientID;

                            if (!empty($_REQUEST['get_analytics_report'])) {

                                if (empty($_REQUEST['startdate' . $CurClientID]) && empty($_REQUEST['enddate' . $CurClientID])) {

                                    $CurAnalyticsDataDBTableName = GetDBTableNameForClient(AnalyticsDataDBTableName, $CurClientID);

                                    $IsAnyRptDataForClient = GetCountOfAllRowsFromTable($CurAnalyticsDataDBTableName);

                                    $_REQUEST['startdate' . $CurClientID] = date(CalendarControlRptDateFormat, strtotime(($IsAnyRptDataForClient ? '-1 day' : '-6 month'), strtotime('today')));

                                    $_REQUEST['enddate' . $CurClientID] = date(CalendarControlRptDateFormat, strtotime('-1 day', strtotime('today')));
                                }//if( empty($_REQUEST['startdate'.$CurClientID]) && empty($_REQUEST['enddate'.$CurClientID]) )



                                $_REQUEST['Action'] = 'AnalyticsDownloadAndSaveToDBReport';

                                $_REQUEST['RemoveQueryParamsCheckBox' . $CurClientID] = 1;
                            }//if( !empty( $_REQUEST['get_analytics_report'] ) )
                            else if (!empty($_REQUEST['update_adwords_keywords'])) {

                                $_REQUEST['Action'] = 'AdwordsDownloadKeywordIdeasAndSaveToDB';

                                if (isset($_REQUEST['only_empty']))
                                    $_REQUEST['AdWordsOnlyEmptyCheckBox' . $CurClientID] = true;
                                else
                                    unset($_REQUEST['AdWordsOnlyEmptyCheckBox' . $CurClientID]);
                            }//else if( !empty( $_REQUEST['update_adwords_keywords'] ) )

                            $_REQUEST['ActionParam1'] = 'reserved for future use';
                        }
                    }//if( $FromCron  && isset($_REQUEST['client_number']) )
                    else
                        $_REQUEST['ClientID'] = $UserID;

                    function BuildClientsHTMLTable($user, $CurClient) {

                        $ClientsHTMLTable = '';



                        //$ClientsHTMLTable .= '<table border=1>';
                        // $ClientsHTMLTable .= '<tr>';
                        // $ColumnHeaders = array('Client number', 'Adwords account', 'Analytics account', 'Spec actions');
                        //        foreach ($ColumnHeaders as $header)
                        //       $ClientsHTMLTable .= '<th>' . $header . '</th>';
                        //  $ClientsHTMLTable .= '</tr>';
                        // $Num = 1;



                        $AdWordsAccountData = ''; //'<b>Google Adwords </b>';

                        if (true) {//if( !empty($CurClient[1]) )
                            LoadAdWordsAccessTokenFromDB($user, $CurClient);

                            $OAuth2Info = $user->GetOAuth2Info();

                            $time = $OAuth2Info['timestamp'] + $OAuth2Info['expires_in'];

                            if ($time < time()) {

                                //connectAdwords();
                                //$_REQUEST['Action'] = "ConnectAdwords";
                            }
//else
//	echo $time . " - " . time();

                            $AdwordsAccessToken_key = md5($OAuth2Info['access_token'] . $OAuth2Info['refresh_token'] . $OAuth2Info['timestamp'] . $OAuth2Info['expires_in']);



                            if (!isset($_SESSION['AdwordsChildAccountsCache']))
                                $_SESSION['AdwordsChildAccountsCache'] = array();



                            if (!array_key_exists($AdwordsAccessToken_key, $_SESSION['AdwordsChildAccountsCache'])) {
                                 $ChildAccountsList="";
                               // $ChildAccountsList = GetChildAccountsListAsAr($user, $MCCAccountName);

                                $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key] = $ChildAccountsList;

                                $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key . 'MCCAccountName'] = $MCCAccountName;
                            }



                            $ChildAccountsList = $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key];

                            $MCCAccountName = $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key . 'MCCAccountName'];

                             unset($_SESSION['AdwordsChildAccountsCache']);



                            /*

                              $AdWordsAccountData .= '<i>Account name: </i><b>'.$CurClient[3].'</b>';

                              $AdWordsAccountData .= '<input type="button" value="Disconnect"  OnClick="DoAction(\''.$CurClient[0].'\', \'DisconnectAdwords\', \'\')" />';

                              $AdWordsAccountData .= '<i>Select child account: </i>'.CreateHTMLSelectWithItems($ChildAccountsList, $CurClient[2], "SelectedChildAccount", $CurClient[0]);

                              

                              $AdWordsAccountData .= '<input type="button" value="Download keyword ideas and save to DB"  OnClick="DoAction(\''.$CurClient[0].'\', \'AdwordsDownloadKeywordIdeasAndSaveToDB\', \'\' )" />';

                              $AdWordsAccountData .= '<input type="checkbox"  name="AdWordsOnlyEmptyCheckBox'.$CurClient[0].'" '.(isset($_REQUEST['AdWordsOnlyEmptyCheckBox'.$CurClient[0]]) ? 'CHECKED' : '' ).' /> Only with empty params';

                              $AdWordsAccountData .= '<input type="checkbox"  name="AdWordsFormatDataCheckBox'.$CurClient[0].'" '.(isset($_REQUEST['AdWordsFormatDataCheckBox'.$CurClient[0]]) ? 'CHECKED' : '' ).' /> Format Data';

                              $AdWordsAccountData .= '<input type="button" value="Show keyword ideas from DB"  OnClick="DoAction(\''.$CurClient[0].'\', \'AdWordsShowSavedKeywordIdeasFromDB\', \'\' )" />';

                             

                              $AdWordsAccountData .= '<input type="checkbox"  name="AdWordsAddNewKeywordsCheckBox'.$CurClient[0].'" id="AdWordsAddNewKeywordsCheckBoxID'.$CurClient[0].'"   OnClick="ShowHideAddNewKeywordTextArea(\''.$CurClient[0].'\')" /> Add New Keywords';

                              $AdWordsAccountData .= '<div  name="NewKeywordsDiv'.$CurClient[0].'" id="NewKeywordsDivID'.$CurClient[0].'"  style="display: none;" >';

                              $AdWordsAccountData .= '<span>Please enter new keywords (one per line):</span> ';

                           

                              $AdWordsAccountData .= '<textarea rows="10" name="NewKeywordsTextArea'.$CurClient[0].'" id="NewKeywordsTextAreaID'.$CurClient[0].'"  cols="50" name="text"></textarea>';

                              $AdWordsAccountData .= '</div> '; */

                            //$AdWordsAccountData .= '<input type="button" value="Run demo campaign report"  OnClick="DoAction(\''.$CurClient[0].'\', \'AdwordsDemoCampRpt\', \'\' )" />';
                        }//if(true)//if( !empty($CurClient[1]) )
                        else {

                            //$AdWordsAccountData .= 'not connected ';
                            //$AdWordsAccountData .= '<input type="button" value="Connect"  OnClick="DoAction(\''.$CurClient[0].'\', \'ConnectAdwords\', \'\')" />';
                        }//else from if( !empty($CurClient[1]) )

                        //$AnalyticsAccountData = '<b>Google Analytics </b>';

                        //$CurClient[4] = "";
                        if (isset($_GET['force_analytics']))
                            $CurClient[4] = "";

                        if (!empty($CurClient[4])) {

                            LoadAnalyticsAccessTokenFromDB($CurClient);
                            //print_r($GLOBALS["Client"]->setAccessToken());
                            if (empty($GLOBALS["Analytics"]))/////////////////////
                                $GLOBALS["Analytics"] = new Google_Service_Analytics($GLOBALS["Client"]);
  $AnalyticsAccountData .= '<div class="main_account"><div class="account_label">Account Name</div><div class="account_name"><b>' . $CurClient[6] . '</b></div></div><div class="disconnect-button">';?>


                           <?php 
              
        $AnalyticsAccountData .= '<input class="btn btn-success btn-disconnect" type="button" value="Disconnect From Google Analytics"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'DisconnectAnalytics\', \'\')" /></div>';
                           ?>    

                           

                           <?php $AnalyticsSelChildAccountID = -1;

                            $AnalyticsSelWebPropertieID = -1;

                            $AnalyticsSelProfileID = -1;

                            $AnalyticsChildAccountFromDB = $CurClient[5];

                            if (strpos($AnalyticsChildAccountFromDB, SpecSeparatorStr) !== false) {

                                $AnalyticsChildAccountFromDBAr = explode(SpecSeparatorStr, $AnalyticsChildAccountFromDB);

                                if (count($AnalyticsChildAccountFromDBAr) > 0)
                                    $AnalyticsSelChildAccountID = $AnalyticsChildAccountFromDBAr[0];

                                if (count($AnalyticsChildAccountFromDBAr) > 1)
                                    $AnalyticsSelWebPropertieID = $AnalyticsChildAccountFromDBAr[1];

                                if (count($AnalyticsChildAccountFromDBAr) > 2)
                                    $AnalyticsSelProfileID = $AnalyticsChildAccountFromDBAr[2];
                            }//if( strpos($AnalyticsChildAccountFromDB, SpecSeparatorStr) !== false )



                            $AnalyticsAccessToken = $CurClient[4];

                            $AnalyticsAccessToken_key = md5($AnalyticsAccessToken);



                            if (!isset($_SESSION['AnalyticsChildAccountsCache']))
                                $_SESSION['AnalyticsChildAccountsCache'] = array();



                            if (!array_key_exists($AnalyticsAccessToken_key, $_SESSION['AnalyticsChildAccountsCache']))
                                $_SESSION['AnalyticsChildAccountsCache'][$AnalyticsAccessToken_key] = getAccounts_Ids();



                            $AnalyticsChildAccountsList = $_SESSION['AnalyticsChildAccountsCache'][$AnalyticsAccessToken_key];





                            if ($AnalyticsSelChildAccountID == -1 && count($AnalyticsChildAccountsList) > 0)
                                $AnalyticsSelChildAccountID = $AnalyticsChildAccountsList[0][0];



                            if (!isset($_SESSION['AnalyticsWebPropertiesCache']))
                                $_SESSION['AnalyticsWebPropertiesCache'] = array();



                            $AnalyticsWebPropertiesList = array();

                            if ($AnalyticsSelChildAccountID != -1) {



                                $AnalyticsAccessTokenAndChildAccount_key = md5($AnalyticsAccessToken . $AnalyticsSelChildAccountID);

                                if (!array_key_exists($AnalyticsAccessTokenAndChildAccount_key, $_SESSION['AnalyticsWebPropertiesCache']))
                                    $_SESSION['AnalyticsWebPropertiesCache'][$AnalyticsAccessTokenAndChildAccount_key] = getWebProperties_Ids($AnalyticsSelChildAccountID);



                                $AnalyticsWebPropertiesList = $_SESSION['AnalyticsWebPropertiesCache'][$AnalyticsAccessTokenAndChildAccount_key];
                            }//if( $AnalyticsSelChildAccountID != -1 )



                            if ($AnalyticsSelWebPropertieID == -1 && count($AnalyticsWebPropertiesList) > 0)
                                $AnalyticsSelWebPropertieID = $AnalyticsWebPropertiesList[0][0];



                            if (!isset($_SESSION['AnalyticsProfileCache']))
                                $_SESSION['AnalyticsProfileCache'] = array();



                            $AnalyticsSelProfilesList = array();

                            if ($AnalyticsSelChildAccountID != -1 && $AnalyticsSelWebPropertieID != -1) {



                                $AnalyticsAccessTokenAndChildAccountAndWebPropertie_key = md5($AnalyticsAccessToken . $AnalyticsSelChildAccountID . $AnalyticsSelWebPropertieID);

                                if (!array_key_exists($AnalyticsAccessTokenAndChildAccountAndWebPropertie_key, $_SESSION['AnalyticsProfileCache']))
                                    $_SESSION['AnalyticsProfileCache'][$AnalyticsAccessTokenAndChildAccountAndWebPropertie_key] = getProfiles_Ids($AnalyticsSelChildAccountID, $AnalyticsSelWebPropertieID);



                                $AnalyticsSelProfilesList = $_SESSION['AnalyticsProfileCache'][$AnalyticsAccessTokenAndChildAccountAndWebPropertie_key];
                            }//if( $AnalyticsSelChildAccountID != -1&& $AnalyticsSelWebPropertieID != -1 )





                            if ($AnalyticsSelProfileID == -1 && count($AnalyticsSelProfilesList) > 0)
                                $AnalyticsSelProfileID = $AnalyticsSelProfilesList[0][0];





                            UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsChildActId'), array(implode(SpecSeparatorStr, array($AnalyticsSelChildAccountID, $AnalyticsSelWebPropertieID, $AnalyticsSelProfileID))), 'MCCUserID = ' . $CurClient[0], array(1));

                            $AnalyticsAccountData .="<div class='child_account'>";
                           
                            $AnalyticsAccountData .="<div class='row pad_top_15'>";
                             $AnalyticsAccountData .="<div class='col-md-4 col-xs-12'>";
                            $AnalyticsAccountData .= '<label class="child_a text-left mont-font">Select child account: </label>';

   $AnalyticsAccountData .= CreateHTMLSelectWithItems($AnalyticsChildAccountsList, $AnalyticsSelChildAccountID, 'SelectedAnalyticsChildAccount', $CurClient[0], 'OnChange="DoAction(\'' . $CurClient[0] . '\', \'SelectedAnalyticsChildAccountChanged\', \'\')"');
 $AnalyticsAccountData .="</div>";
   $AnalyticsAccountData .="<div class='col-md-4 col-xs-12'>";
                            $AnalyticsAccountData .= '<label  class="child_a text-left mont-font">Select properties: </label>' . CreateHTMLSelectWithItems($AnalyticsWebPropertiesList, $AnalyticsSelWebPropertieID, 'SelectedAnalyticsWebPropertie', $CurClient[0], 'OnChange="DoAction(\'' . $CurClient[0] . '\', \'SelectedAnalyticsWebPropertieChanged\', \'\')"');
 $AnalyticsAccountData .="</div>";
   $AnalyticsAccountData .="<div class='col-md-4 col-xs-12'>";
                            $AnalyticsAccountData .= '<label  class="child_a text-left mont-font">Select profile: </label>' . CreateHTMLSelectWithItems($AnalyticsSelProfilesList, $AnalyticsSelProfileID, 'SelectedAnalyticsProfile', $CurClient[0]);
 $AnalyticsAccountData .="</div>";
                    $AnalyticsAccountData .="</div>";
                    $AnalyticsAccountData .="</div>";
                           

                            //$AnalyticsAccountData .= '<input type="button" value="Run demo report"  OnClick="DoAction(\''.$CurClient[0].'\', \'AnalyticsDemoReport\', \'\' )" />';
                            //$AnalyticsAccountData .= '<input type="checkbox"  name="AnalyticsFormatDataCheckBox'.$CurClient[0].'" '.(isset($_REQUEST['AnalyticsFormatDataCheckBox'.$CurClient[0]]) ? 'CHECKED' : '' ).' /> Format Data';

                            $RemoveQueryParamsCheckBoxChecked = isset($_REQUEST['RemoveQueryParamsCheckBox' . $CurClient[0]]);

                            $RemoveWWWPrefixCheckBoxChecked = isset($_REQUEST['RemoveWWWPrefixCheckBox' . $CurClient[0]]);

                            if (!isset($SESSION['FirtRun'])) {

                                $RemoveQueryParamsCheckBoxChecked = true;

                                $RemoveWWWPrefixCheckBoxChecked = false;
                            }



                         //   $AnalyticsAccountData .= '<input type="checkbox" style="display:none;"  name="RemoveQueryParamsCheckBox' . $CurClient[0] . '" ' . ($RemoveQueryParamsCheckBoxChecked ? 'CHECKED' : '' ) . ' /> '; //Remove Query Params

                            //$AnalyticsAccountData .= '<input type="checkbox"  name="RemoveWWWPrefixCheckBox' . $CurClient[0] . '" ' . ($RemoveWWWPrefixCheckBoxChecked ? 'CHECKED' : '' ) . ' /> Remove WWW Prefix';



                            $StartDate = !empty($_REQUEST['startdate' . $CurClient[0]]) ? $_REQUEST['startdate' . $CurClient[0]] : '01-04-2015' /* date(CalendarControlRptDateFormat, strtotime('-60 day', strtotime('today'))) */;

                            $EndDate = !empty($_REQUEST['enddate' . $CurClient[0]]) ? $_REQUEST['enddate' . $CurClient[0]] : date(CalendarControlRptDateFormat, strtotime('-1 day', strtotime('today')));

                            //$AnalyticsAccountData .= 'Start date: <input type="text" name="startdate' . $CurClient[0] . '" id="startdate"  value="' . $StartDate . '" onfocus="this.select();lcs(this)"  onclick="event.cancelBubble=true;this.select();lcs(this)">';

                            //$AnalyticsAccountData .= 'End date: <input type="text" name="enddate' . $CurClient[0] . '" id="enddate" value="' . $EndDate . '" onfocus="this.select();lcs(this)"  onclick="event.cancelBubble=true;this.select();lcs(this)">';

                           

                            //$AnalyticsAccountData .= '<input type="button" value="Download report and save to DB"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'AnalyticsDownloadAndSaveToDBReport\', \'\' )" />';

                            //$AnalyticsAccountData .= '<input style="margin-left:20px;" type="button" value="Show saved reports from DB"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'AnalyticsShowSavedReportsFromDB\', \'\' )" />';

                            // Only For First Time
                            $an_from_date = date('Y-m-d',strtotime('first day of last month'));
                            $an_to_date = date('Y-m-d', strtotime('-1 days', time()));
                            $daily_data_url = site_url() . '/cron/pullga.php?client_id=' . $CurClient[0] . '&start_date=' . $an_from_date . '&end_date=' . $an_to_date . '&save_mode=1&page=analytics';
                            //$AnalyticsAccountData .= '<input class="btn btn-success" style="margin-left:20px;background:none;" type="button" value="Get Analytics Historical Data"   />'; //onclick="document.location.href=\'' . $daily_data_url . '\'"
                           // $AnalyticsAccountData .= '<a href="'.$daily_data_url.'" target="_blank" class="btn btn-success">Pull Last 30 Days Data</a>';
                            //
                        }//if( !empty($CurClient[4]) )
                        else {//////////NEW ANALYTICS
                          //  $AnalyticsAccountData .= 'not connected ';

                            $AnalyticsAccountDataSub = <<<EOD

<script  type="text/javascript">

function inIframe () {

    try {

        return window.self !== window.top;

    } catch (e) {

        return true;

    }

}



function DoGoogleAnalyticsRedirect()

{
//window.top.location.href = "RedirURL2";
    if( inIframe () )

       window.top.location.href = "RedirURL1";

    else

       var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
                 screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
                 outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
                 outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
                 width    = 700,
                 height   = 450,
                 left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
                 top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
                 features = (
                    'width=' + width +
                    ',height=' + height +
                    ',left=' + left +
                    ',top=' + top
                  );
 
            newwindow=window.open("RedirURL1",'GA_Connect',features);
 
           if (window.focus) {newwindow.focus();}
          return false;
    /*var newWindow = window.open("RedirURL1", 'name', 'height=600,width=450');
      if (window.focus) {
       newWindow.focus();
         window.close();
     }*/
     //  window.top.location.href = "RedirURL1";
  // localStorage.setItem("integrate-id", "4");



}

</script>

EOD;

                        $_SESSION['ClientID'] = $CurClient[0];

                        $_SESSION['AnalyticsNewToken'] = true;


                        /*  $GAAuthorizationUrl = $GLOBALS["Client"]->createAuthUrl();

                            $GLOBALS["Client"] = BasicallyInitGoogleClient(site_url(), true); //'/?action=go_to_predictive_analytics_settings'

                            $GAAuthorizationUrl2 = $GLOBALS["Client"]->createAuthUrl();

                            $AnalyticsAccountDataSub = str_replace('RedirURL1', $GAAuthorizationUrl, $AnalyticsAccountDataSub);

                            $AnalyticsAccountDataSub = str_replace('RedirURL2', $GAAuthorizationUrl2, $AnalyticsAccountDataSub);*/

			//Rudra Code Starts

			$servername="admin.enfusen.com/cron/agencygaconnect.php";

			$RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .$servername ;
                          
			$RedirectURL1 = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] .'/ga-connect/';;
			$GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true); //'/?action=go_to_predictive_analytics_settings'

			$authorizationUrl = base64_encode($GLOBALS["Client"]->createAuthUrl());

			$servername= base64_encode($RedirectURL1) ;

			$currenturl = md5((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] .'/ga-connect/'.$user_id);

			$GAAuthorizationUrl=$RedirectURL."?authorise_url=".$authorizationUrl."&md5_user_id=".$currenturl."&servername=".$servername;

			$AnalyticsAccountDataSub = str_replace('RedirURL1', $GAAuthorizationUrl, $AnalyticsAccountDataSub);


			//Rudra Code Ends

                        $AnalyticsAccountData .= $AnalyticsAccountDataSub;
                      
                         //$AnalyticsAccountData .= '<input type="button" value="Connect"  OnClick="DoGoogleAnalyticsRedirect()" />';?>
                 

                                 <?php


  $AnalyticsAccountData .= "<input type='button' value='Connect Google Analytics'  class='btn btn-success btn-connect connection' OnClick='DoGoogleAnalyticsRedirect()'  />";?>






                        <?php     PrevInitAdwordsUserSettings($user);

                            $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);
                        }//else from if( !empty($CurClient[4]) )



                        GenerateBTLToken();

                        $BTLAccountData = '';

                        $BTLChildAccountsList = GetBTLChildAccountsIDsAndNames(GetBTLChildAccountsList());

                        $BTLChilActID = $CurClient[7];

                        $BTLReportsList = GetBTLReportsIDsAndNames(GetBTLReportsList($BTLChilActID), false);



                        //file_put_contents(dirname(__FILE__) .'/aaa4.txt', print_r($BTLReportsList, true));


                        /*
                        $BTLAccountData .= '<i>Select Bright Local Client: </i>' . CreateHTMLSelectWithItems($BTLChildAccountsList, $BTLChilActID, "SelectedBTLChildAccount", $CurClient[0], 'OnChange="DoAction(\'' . $CurClient[0] . '\', \'SelectedBTLChildAccountChanged\', \'\')"');

                       
                        $BTLRptID = $CurClient[8];

                        $BTLAccountData .= '<i>Select Bright Local Report: </i>' . CreateHTMLSelectWithItems($BTLReportsList, $BTLRptID, "SelectedBTLReport", $CurClient[0]);

                      

                        $BTLAccountData .= '<input type="button" value="Save"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'SaveBTLChildAccountToDB\', \'\' )" />';

                        $BTLAccountData .= '<input type="button" value="Download last report from Bright Local"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'BTLDownloadAndSaveToDBReport\', \'\' )" />';
                        */
                        //  $ClientsHTMLTable .= '<tr>';
                        //  $ClientsHTMLTable .= '<td>'.$Num++.'</td>';

                        $ClientsHTMLTable .= $AdWordsAccountData ; // .= '<td>'.$AdWordsAccountData.'</td>';

                        $ClientsHTMLTable .= $AnalyticsAccountData ; //'<td>'.$AnalyticsAccountData.'</td>'

                        $ClientsHTMLTable .= $BTLAccountData ; //'<td>'.$AnalyticsAccountData.'</td>'





                        return $ClientsHTMLTable;
                    }

//function BuildClientsHTMLTable($user, $CurClient )
//$RedirectURL = "http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_NAME'] == 'localhost' ? ":8080" : "").str_replace(' ', '%20', $_SERVER['PHP_SELF']);


		     //Rudra Code Starts

		     $servername="admin.enfusen.com/cron/agencygaconnect.php";

	             $RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .$servername ;

		     //Rudra Code Ends

                     PrevInitAdwordsUserSettings($user);

                     $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);

                     $ActionResHTML = '';


//$RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .$servername . '/agency-home/';
                    if (!empty($_GET['error']) && $_GET['error'] == 'access_denied') {

                        unset($_SESSION['AdWordsNewToken']);

                        unset($_SESSION['AnalyticsNewToken']);

                        unset($_SESSION['ClientID']);

                        header("Location: $RedirectURL");
                    }//if ( !empty($_GET['error']) && $_GET['error'] = 'access_denied' )

                    /*
                      echo "<pre>";
                      print_r($_SESSION['ClientID']);
                      echo "</pre>";


                      if ( isset($_GET['code']) && !empty($_SESSION['ClientID']) )
                      echo "TRUE";
                      else
                      echo "FALSE";

                      die();
                     *//////////////////////////
                    if (isset($_GET['code']) && !empty($_SESSION['ClientID'])) {

                        $AuthGetCode = $_GET['code'];


                        if (!isset($_SESSION['used_codes']))
                            $_SESSION['used_codes'] = '';

                        //if( strpos($_SESSION['used_codes'], $AuthGetCode) === FALSE )
                        //{

                        $_SESSION['used_codes'] .= $AuthGetCode;
                        if(!empty($_SESSION['used_codes'])){
                       $get_code_already = get_user_meta($UserID,'ga_connect_campaign',true);
                        if(!empty($get_code_already)){
                         update_user_meta($UserID, "ga_connect_campaign",json_encode($_SESSION['used_codes']));
                         }else{
                        add_user_meta($UserID, "ga_connect_campaign",json_encode($_SESSION['used_codes']));
                         }
                        $_SESSION['integrationed']=1;
                        }

                        $CurClientID = $_REQUEST['ClientID']; //$_SESSION['ClientID'];

                        if (isset($_SESSION['AdWordsNewToken'])) {


                            $user->SetOAuth2Info($user->GetOAuth2Handler()->GetAccessToken($user->GetOAuth2Info(), $AuthGetCode, $RedirectURL));
                            $info = $user->GetOAuth2Info();


                            $AccessTokenAsOneCSVStr = SaveAssoteatedArrayToOneCSVStr($info);


                            saveAdwordsAccess($AccessTokenAsOneCSVStr);
                              $ChildAccountsList="";

                            //$ChildAccountsList = GetChildAccountsListAsAr($user, $MCCAccountName);

                            $FirstChildAccountID = GetFirstChildAccountsClientID($user, $ChildAccountsList);



                            UpdateRowsInTable(AccessTokensDBTableName, array('AdWordsToken', 'AdWordsChildActId', 'AdWordsAccountName'), array($AccessTokenAsOneCSVStr, $FirstChildAccountID, $MCCAccountName), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));

                            UpdateRowsInTable(AccessTokensDBTableName, array('AdWordsToken', 'AdWordsAccountName'), array($AccessTokenAsOneCSVStr, $MCCAccountName));



                            unset($_SESSION['AdWordsNewToken']);
                        }//if ( isset($_SESSION['AdWordsNewToken'])  )
                        else if (isset($_SESSION['AnalyticsNewToken'])) {

                            $FromIframe = !empty($_REQUEST['from_iframe']);



                            if ($FromIframe)
                                $GLOBALS["Client"] = BasicallyInitGoogleClient(site_url(), true); //'/?action=go_to_predictive_analytics_settings'



                            $AnalyticsAccessToken = $GLOBALS["Client"]->authenticate($AuthGetCode);



                            $AnalyticsAccountName = GetUserEmailFromGoogle();

                            $AnalyticsChildActId = '';

                            UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsToken', 'AnalyticsChildActId', 'AnalyticsAccountName'), array($AnalyticsAccessToken, $AnalyticsChildActId, $AnalyticsAccountName), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));

                            UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsToken', 'AnalyticsAccountName'), array($AnalyticsAccessToken, $AnalyticsAccountName));

                            if ($FromIframe)
                                $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);

                            unset($_SESSION['AnalyticsNewToken']);
                        }//else if ( isset($_SESSION['AnalyticsNewToken'])  )

                        unset($_SESSION['ClientID']);

                        //Rudra Code Starts*/

                      //  $RedirectURL_past=site_url().$_SERVER[REQUEST_URI];
			$url_re=$current_url_re;
			$data_re=explode("?code=",$url_re);
			$RedirectURL_past=$data_re[0];
			if(empty($data_re[0])){
			$RedirectURL_past=$current_url_re;
			}else{
			$RedirectURL_past=$data_re[0];
			}


                        header("Location: $RedirectURL_past");

                         exit();

                        //Rudra Code Ends*/


                        //}//if( strpos($_SESSION['used_codes'], $AuthGetCode) === FALSE )
                    }//if ( isset($_GET['code']) && !empty($_SESSION['ClientID']) )





                    $AllClientsFromDB = GetAccessTokensFromTable();



//foreach($AllClientsFromDB as $CurClient)
                    // CreateConvTrackTablesForNewClient($CurClient[0]); //CreateAnalyticsTablesForNewClient($CurClient[0]);
                    //exit(0);



                    $CurClientID = !empty($_REQUEST['ClientID']) ? $_REQUEST['ClientID'] : null;

                    file_put_contents(dirname(__FILE__) . '/aaa5.txt', $CurClientID);

                    if (!empty($_REQUEST['Action'])) {

                        $Action = $_REQUEST['Action'];

                        $ActionParam1 = $_REQUEST['ActionParam1'];



                        if (!empty($_REQUEST['SelectedChildAccountComboBox' . $CurClientID])) {

                            UpdateRowsInTable(AccessTokensDBTableName, array('AdWordsChildActId'), array($_REQUEST['SelectedChildAccountComboBox' . $CurClientID]), 'MCCUserID = ' . $CurClientID, array(1));

                            $AllClientsFromDB = GetAccessTokensFromTable();
                        }//if( !empty($_REQUEST['SelectedChildAccountComboBox'.$CurClientID]) )
                        //file_put_contents(dirname(__FILE__) .'/aaa10.txt', $_REQUEST['SelectedAnalyticsChildAccountComboBox'.$CurClientID]);
                        //file_put_contents(dirname(__FILE__) .'/aaa11.txt', $_REQUEST['SelectedAnalyticsWebPropertieComboBox'.$CurClientID]);
                        //file_put_contents(dirname(__FILE__) .'/aaa12.txt', $_REQUEST['SelectedAnalyticsProfileComboBox'.$CurClientID]);

                        if (!empty($_REQUEST['SelectedAnalyticsChildAccountComboBox' . $CurClientID]) &&
                                !empty($_REQUEST['SelectedAnalyticsWebPropertieComboBox' . $CurClientID])  //&&

                        /* !empty($_REQUEST['SelectedAnalyticsProfileComboBox'.$CurClientID]) */) {

                            if (empty($_REQUEST['SelectedAnalyticsProfileComboBox' . $CurClientID]))
                                $_REQUEST['SelectedAnalyticsProfileComboBox' . $CurClientID] = -1;



                            $NewAnalyticsChildActId = implode(SpecSeparatorStr, array($_REQUEST['SelectedAnalyticsChildAccountComboBox' . $CurClientID],
                                ( $Action != 'SelectedAnalyticsChildAccountChanged' ? $_REQUEST['SelectedAnalyticsWebPropertieComboBox' . $CurClientID] : -1),
                                ( $Action != 'SelectedAnalyticsChildAccountChanged' && $Action != 'SelectedAnalyticsWebPropertieChanged' ? $_REQUEST['SelectedAnalyticsProfileComboBox' . $CurClientID] : -1)));





                            UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsChildActId'), array($NewAnalyticsChildActId), 'MCCUserID = ' . $CurClientID, array(1));

                            $AllClientsFromDB = GetAccessTokensFromTable();
                        }//if( !empty($_REQUEST['SelectedAnalyticsChildAccountComboBox'.$CurClientID]) &&


                        /*
                        if (isset($_REQUEST['SelectedBTLChildAccountComboBox' . $CurClientID])) {

                            $BTLRptId = isset($_REQUEST['SelectedBTLReportComboBox' . $CurClientID]) ? $_REQUEST['SelectedBTLReportComboBox' . $CurClientID] : '';

                            SaveBTLChildActIDToDB($_REQUEST['SelectedBTLChildAccountComboBox' . $CurClientID], $BTLRptId, $CurClientID);

                            $AllClientsFromDB = GetAccessTokensFromTable();

                            //update_user_meta($CurClientID, 'btl_campaign', $_REQUEST['SelectedBTLReportComboBox' . $CurClientID]);
                        }//if( isset($_REQUEST['SelectedBTLChildAccountComboBox'.$CurClientID]) )
                        */


                        if ($Action == "AddNewClient") {

                            $NewClientID = InsertOneRowInToTable(AccessTokensDBTableName, array('AdWordsToken', 'AnalyticsToken'), array('', ''), array(1, 1));

                            if ($NewClientID !== false) {

                                CreateAnalyticsTablesForNewClient($NewClientID);

                                CreateConvTrackTablesForNewClient($NewClientID);
                            }//if( $NewClientID !== false )
                        }//if($Action == "AddNewClient")
                        else if ($Action == "DeleteCleint" && !empty($CurClientID)) {

                            DeleteAllRowsFromTable(AccessTokensDBTableName, 'MCCUserID = ' . $CurClientID);

                            DeleteTableForClient(AnalyticsDataDBTableName, $CurClientID);

                            DeleteTableForClient(AnalyticsCacheDataDBTableName, $CurClientID);

                            DeleteTableForClient(ConvTrackingDBTableName, $CurClientID);

                            DeleteTableForClient(ConvTrackingCacheDBTableName, $CurClientID);

                            DeleteTableForClient(ConvTrackingFilteredDBTableName, $CurClientID);

                            DeleteTableForClient(ConvTrackingUrlsDBTableName, $CurClientID);
                        }//else if($Action == "DeleteCleint" && !empty($CurClientID))
                        else if ($Action == "ConnectAdwords" && !empty($CurClientID)) {
                            connectAdwords();
                            /*
                              $_SESSION['ClientID'] = $CurClientID;

                              $_SESSION['AdWordsNewToken'] = true;

                              $authorizationUrl = $user->GetOAuth2Handler()->GetAuthorizationUrl($user->GetOAuth2Info(), $RedirectURL, true);

                              $authorizationUrl .= "&approval_prompt=force";
                              echo "TESTing";
                              echo "<script>location.href='{$authorizationUrl}';</script>";
                             */
//        header("Location: ".$authorizationUrl);
                        }//else if($Action == "ConnectAdwords" && !empty($CurClientID))
                        else if ($Action == "DisconnectAdwords" && !empty($CurClientID)) {

                            UpdateRowsInTable(AccessTokensDBTableName, array('AdWordsToken', 'AdWordsChildActId', 'AdWordsAccountName'), array('', '', ''), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));
                        }//else if($Action == "DisconnectAdwords" && !empty($CurClientID))
                        else if ($Action == "ConnectAnalytics" && !empty($CurClientID)) {

                            $_SESSION['ClientID'] = $CurClientID;

                            $_SESSION['AnalyticsNewToken'] = true;

                            $authorizationUrl = $GLOBALS["Client"]->createAuthUrl();


                            header("Location: " . $authorizationUrl);
                        }//else if($Action == "ConnectAnalytics" && !empty($CurClientID))
                        else if ($Action == "DisconnectAnalytics" && !empty($CurClientID)) {
                            UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsToken', 'AnalyticsChildActId', 'AnalyticsAccountName'), array('', '', ''), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));
                        }//else if($Action == "DisconnectAnalytics" && !empty($CurClientID))
                        else if ($Action == "AdWordsShowSavedKeywordIdeasFromDB" && !empty($CurClientID)) {

                            $OnlyEmpty = isset($_REQUEST['AdWordsOnlyEmptyCheckBox' . $CurClientID]);

                            $ActionResHTML .= ShowSavedKeywordIdeasFromDB($CurClientID, LoadAdWordsKeywordsForPlannerFromDB($CurClientID, $OnlyEmpty), isset($_REQUEST['AdWordsFormatDataCheckBox' . $CurClientID]));
                        }//else if($Action == "AdWordsShowSavedKeywordIdeasFromDB" && !empty($CurClientID))
                        else if ($Action == "AdwordsDemoCampRpt" && !empty($CurClientID)) {

                            LoadAdWordsAccessTokenFromDB($user, GetCurrentClient($AllClientsFromDB, $CurClientID));

                            $StartDate = date(AdWordsRptDateFormat, strtotime('-30 day', strtotime('today')));

                            $EndDate = date(AdWordsRptDateFormat, strtotime('-1 day', strtotime('today')));

                            $RptFields = array('CampaignId', 'CampaignName', 'Cost', 'Clicks', 'ConversionsManyPerClick', 'ConversionRateManyPerClick', 'ValuePerConversionManyPerClick');

                            $RptData = GetReportsData_through_AdHoc_ReportsEx($user, $user->GetClientCustomerId(), 'CAMPAIGN_PERFORMANCE_REPORT', 'Campaigns Performance Report ', $RptFields, $StartDate, $EndDate, array());

                            $ActionResHTML .= '<h3>Demo report data from  Google Adwords:</h3>' . ForMatAdWordsRptDataToHTMlTable($RptData, $RptFields);
                        }//else if($Action == "AdwordsDemoCampRpt" && !empty($CurClientID))
                        else if (array_search($Action, array('AnalyticsDemoReport', 'AnalyticsDownloadAndSaveToDBReport', 'AnalyticsShowSavedReportsFromDB')) !== false && !empty($CurClientID)) {

                            $CurrentClient = GetCurrentClient($AllClientsFromDB, $CurClientID);

                            LoadAnalyticsAccessTokenFromDB($CurrentClient);

                            $GLOBALS["Analytics"] = new Google_Service_Analytics($GLOBALS["Client"]);



                            $tableId = null;

                            $AnalyticsChildAccountFromDB = $CurrentClient[5];

                            if (strpos($AnalyticsChildAccountFromDB, SpecSeparatorStr) !== false) {

                                $AnalyticsChildAccountFromDBAr = explode(SpecSeparatorStr, $AnalyticsChildAccountFromDB);

                                if (count($AnalyticsChildAccountFromDBAr) > 2)
                                    $tableId = $AnalyticsChildAccountFromDBAr[2];
                            }//if( strpos($AnalyticsChildAccountFromDB, SpecSeparatorStr) !== false )







                            if (empty($tableId)) {

                                $ProfilesIds = getProfilesIds(true);

                                foreach ($ProfilesIds as $CurKey => $CurVal) {

                                    $tableId = $CurKey;

                                    break;
                                }//foreach($ProfilesIds as $CurKey => $CurVal)
                            }//if( empty($tableId) )





                            $StartDate = !empty($_REQUEST['startdate' . $CurClientID]) ? InvertDateForAnalyticsRpt($_REQUEST['startdate' . $CurClientID]) : strtotime('-1 day', strtotime('today'));

                            $EndDate = !empty($_REQUEST['enddate' . $CurClientID]) ? InvertDateForAnalyticsRpt($_REQUEST['enddate' . $CurClientID]) : strtotime('-60 day', strtotime('today'));



                            if ($Action == "AnalyticsDownloadAndSaveToDBReport") {

                                $Metrics = GAPrefix . "visits, " . GAPrefix . "timeOnSite, " . GAPrefix . "bounceRate, " . GAPrefix . "avgSessionDuration, " . GAPrefix . "pageviews,  " . GAPrefix . "goalConversionRateAll"; //.GAPrefix."adClicks"
                                //$Dimensions = GAPrefix."hostname, ".GAPrefix."pagePath, ".GAPrefix."source, ".GAPrefix."medium, ".GAPrefix."date, ".GAPrefix."hour, ".GAPrefix."minute";

                                $Dimensions = GAPrefix . "hostname, " . GAPrefix . "pagePath, " . GAPrefix . "isoYear, " . GAPrefix . "medium, " . GAPrefix . "month, " . GAPrefix . "year, " . GAPrefix . "yearMonth";

                                $RptParams = array('dimensions' => $Dimensions, 'sort' => "-" . GAPrefix . "medium", 'max-results' => '10000', 'start-index' => 1);



                                $StopFlag = false;

                                $RptDataRows = array();

                                $cc = 0;

                                while (!$StopFlag) {

                                    $RptData = $GLOBALS["Analytics"]->data_ga->get(urldecode(GAPrefix . $tableId), $StartDate, $EndDate, $Metrics, $RptParams);

                                    $CurRptDataRows = $RptData->getRows();

                                    foreach ($CurRptDataRows as $CurRptDataRow) {

                                        //if( !file_exists(dirname(__FILE__) .'/aaa6.txt') )
                                        //  file_put_contents(dirname(__FILE__) .'/aaa6.txt', $CurRptDataRow[0].' '.$CurRptDataRow[1]);



                                        $CurRptDataRow[2] = ' ';

                                        $Year = $CurRptDataRow[5];

                                        if (strlen($Year) == 1)
                                            $Year = '0' . $Year;

                                        $Month = $CurRptDataRow[4];

                                        if (strlen($Month) == 1)
                                            $Month = '0' . $Month;

                                        $CurRptDataRow[4] = $Year . $Month . '01';

                                        $CurRptDataRow[5] = '05';

                                        $CurRptDataRow[6] = '0';



                                        $RptDataRows[] = $CurRptDataRow;
                                    }

                                    //$StopFlag = true;

                                    $StopFlag = !($RptData != null && $RptData->nextLink != null);

                                    $RptParams['start-index'] = $RptParams['start-index'] + $RptParams['max-results'];

                                    file_put_contents(dirname(__FILE__) . '/aaa2.txt', $RptData->totalResults);

                                    if ($cc++ >= 3)
                                        break;
                                }//while(!$StopFlag)







                                $PageColIndex = isset($_REQUEST['RemoveQueryParamsCheckBox' . $CurClientID]) ? 1 : -1;

                                $RemoveHostPrefixes = isset($_REQUEST['RemoveWWWPrefixCheckBox' . $CurClientID]) ? array('www.') : null;



                                $RptDataRowsFormated2 = AggregateAndRemoveQueryParamsFromAnalyticsRptData($RptDataRows, array(0, 1, 2, 3, 4, 5), $PageColIndex, array(8, 9), array(6, 7, 10), $RemoveHostPrefixes);





                                SaveAnalyticsReportToDBTable($RptDataRowsFormated2, $CurClientID, $tableId);

                                array_splice($RptDataRowsFormated2, 0);



                                if ($FromCron) {

                                    $LogMessage = 'Analytics Reports for client with ID \'' . $CurClientID . '\' was successful saved to DB. Date range is from ' . $StartDate . ' to ' . $EndDate;

                                    AddToLog(cron_log_file_name, $LogMessage);

                                    $ActionResHTML = $LogMessage;
                                }//if( $FromCron )
                            }  //if( $Action == "AnalyticsDownloadAndSaveToDBReport" )
                            else if ($Action == "AnalyticsDemoReport") {

                                $Metrics = GAPrefix . "visits, " . GAPrefix . "timeOnSite, " . GAPrefix . "bounceRate, " . GAPrefix . "avgSessionDuration, " . GAPrefix . "pageviews";

                                $Dimensions = GAPrefix . "hostname, " . GAPrefix . "pagePath, " . GAPrefix . "date";

                                $RptParams = array('dimensions' => $Dimensions, 'sort' => "-" . GAPrefix . "pagePath", 'max-results' => '20000');

                                $RptData = $GLOBALS["Analytics"]->data_ga->get(urldecode(GAPrefix . $tableId), $StartDate, $EndDate, $Metrics, $RptParams);



                                $RptDataRows = $RptData->getRows();

                                $PageColIndex = isset($_REQUEST['RemoveQueryParamsCheckBox' . $CurClientID]) ? 1 : -1;

                                $RemoveHostPrefixes = isset($_REQUEST['RemoveWWWPrefixCheckBox' . $CurClientID]) ? array('www.') : null;

                                $RptDataRowsFormated = AggregateAndRemoveQueryParamsFromAnalyticsRptData($RptDataRows, array(0, 1), $PageColIndex, array(4, 5), array(2, 3, 6), $RemoveHostPrefixes);

                                $RptDataRowsFormated2 = isset($_REQUEST['AnalyticsFormatDataCheckBox' . $CurClientID]) ? DemoOfReportFormating($RptDataRowsFormated) : $RptDataRowsFormated;



                                $ColumnHeaders = array("Page", "Date", "Visits", "Time On Site", "Bounce Rate", "Avg Session Duration", "Page Views");





                                $ActionResHTML .= '<h3>Demo report data from  Google Analytics:</h3>' . ForMatAnalyticsRptDataToHTMlTableNew($RptDataRowsFormated2, $ColumnHeaders);
                            } //else if( $Action == "AnalyticsDemoReport" )



                            if (array_search($Action, array('AnalyticsDownloadAndSaveToDBReport', 'AnalyticsShowSavedReportsFromDB')) !== false && !isset($_REQUEST['from_cron'])) {



                                $StartDate = !empty($_REQUEST['startdate' . $CurClientID]) ? CalendarControlDateToRptDate($_REQUEST['startdate' . $CurClientID]) : strtotime('-1 day', strtotime('today'));

                                $EndDate = !empty($_REQUEST['enddate' . $CurClientID]) ? CalendarControlDateToRptDate($_REQUEST['enddate' . $CurClientID]) : strtotime('-60 day', strtotime('today'));

                                $ColumnHeaders = array("MCC Client ID", "GA Table ID", "Page", "Source", "Medium", "Visits", "Time On Site", "Bounce Rate");

                                $RptDataRowsFromDBAggregated = LoadAnalyticsInfoFromDB($CurClientID, $tableId, $StartDate, $EndDate, true);



                                $ActionResHTML .= '<h3>Cached Google Analytics Aggregated  Report data from DB :</h3>' . ForMatAnalyticsRptDataToHTMlTableNew($RptDataRowsFromDBAggregated, $ColumnHeaders);

                                $ColumnHeaders[] = "Date Of Visit";

                                $ColumnHeaders[] = "Time Of Visit";

                                $RptDataRowsFromDB = LoadAnalyticsInfoFromDB($CurClientID, $tableId, $StartDate, $EndDate, false);

                                $ActionResHTML .= '<h3>Google Analytics Report data from DB:</h3>' . ForMatAnalyticsRptDataToHTMlTableNew($RptDataRowsFromDB, $ColumnHeaders);
                            }
                        }//else if( array_search($Action, array('AnalyticsDemoReport', 'AnalyticsDownloadAndSaveToDBReport', 'AnalyticsShowSavedReportsFromDB')) != false && !empty($CurClientID))
                        else if ($Action == "SaveBTLChildAccountToDB" && !empty($CurClientID)) {

                            //code is above
                        }//else if($Action == "SaveBTLChildAccountToDB" && !empty($CurClientID))
                        else if ($Action == "BTLDownloadAndSaveToDBReport" && !empty($CurClientID)) {

                            $BTLChildActID = GetBTLChildActIDFromDB($CurClientID, $BTLRptID);



                            if (!empty($BTLChildActID) && !empty($BTLRptID)) {

                                GenerateBTLToken();

                                //$ReportIDWithLastRun = GetReportIDWithLastRun($BTLChildActID, $ReportLastRunDateT);

                                $ReportIDWithLastRun = $BTLRptID;

                                $ReportLastRunDateT = GetReportLastRunDate(GetBTLReportHistory($BTLRptID));



                                if ($ReportIDWithLastRun != null) {

                                    $ReportData = GetBTLReport($ReportIDWithLastRun);



                                    $ReportDataParsed = ParseBtlReportData($ReportData, $ReportLastRunDateT, ' ');

                                    $ColumnHeaders = array('Keyword', 'URL', 'Google Rank', 'Date', 'Other ranks');

                                    $ActionResHTML .= '<h3>Bright Local data from last report:</h3>' . ForMatAnalyticsRptDataToHTMlTableNew($ReportDataParsed, $ColumnHeaders);

                                    $LastRptDate = CheckIdAvailableAndDownloadBTLReport($CurClientID, true);

                                    if (!empty($LastRptDate)) {

                                        $_REQUEST['BTLLastRptDate'] = $LastRptDate;

                                        $_REQUEST['Action'] = 'AdwordsDownloadKeywordIdeasAndSaveToDB';

                                        $_REQUEST['AdWordsOnlyEmptyCheckBox' . $CurClientID] = true;
                                    }//if( !empty($LastRptDate) )
                                }//if(  $ReportIDWithLastRun != null)
                            }////if( !empty($BTLChildActID) )
                            else
                                $ActionResHTML .= 'Please select Bright Local client first!';
                        }//else if($Action == "BTLDownloadAndSaveToDBReport" && !empty($CurClientID))



                        if ($_REQUEST['Action'] == "AdwordsDownloadKeywordIdeasAndSaveToDB" && !empty($CurClientID)) {

                            $CurrentClient = GetCurrentClient($AllClientsFromDB, $CurClientID);

                            LoadAdWordsAccessTokenFromDB($user, $CurrentClient);

                            //$Keywords = array('digital marketing services', 'content creation services', 'marketing strategy', 'online marketing budget', 'how to write a business blog', 'how to build an email marketing campaign', 'creating a customer avatar', 'SEO agency', 'what is a landing page', 'closed loop reporting', 'inbound marketing funnel', 'lead magnet examples', 'what is a call to action', 'Virtual CMO', 'sales and marketing automation', 'marketing productivity',  'marketing funnel', 'marketing automation', 'marketing plan', 'marketing budget', 'Advertising', 'analytics', 'Brand identity', 'Content Creation ', 'Drive traffic', 'Dynamic content', 'email marketing', 'Event marketing', 'Landing pages', 'PPC', 'SEO', 'Small business Marketing', 'Social Media Marketing', 'Web design', 'Virtual Chief Marketing Officer');

                            $Keywords = array();

                            $OnlyEmpty = isset($_REQUEST['AdWordsOnlyEmptyCheckBox' . $CurClientID]);

                            $NeedFormatData = isset($_REQUEST['AdWordsFormatDataCheckBox' . $CurClientID]);



                            $IsNewKeywords = isset($_REQUEST['AdWordsAddNewKeywordsCheckBox' . $CurClientID]) && !empty($_REQUEST['NewKeywordsTextArea' . $CurClientID]);

                            if ($IsNewKeywords) {

                                $KeywordsRaw = explode(PHP_EOL, $_REQUEST['NewKeywordsTextArea' . $CurClientID]);

                                $Keywords = array();

                                foreach ($KeywordsRaw as $CurKeywordRaw)
                                    if (!empty($CurKeywordRaw) && array_search($CurKeywordRaw, $Keywords) === false)
                                        $Keywords[] = $CurKeywordRaw;

                                $IsNewKeywords = count($Keywords) > 0;
                            }//if($IsNewKeywords)



                            if (!$IsNewKeywords) {

                                $BTLLastRptDate = isset($_REQUEST['BTLLastRptDate']) ? $_REQUEST['BTLLastRptDate'] : '';

                                $KeywordsWithStats = LoadAdWordsKeywordsForPlannerFromDB($CurClientID, $OnlyEmpty, $BTLLastRptDate);

                                $Keywords = GetKeywordsFromAdWordsKeywordsForPlanner($KeywordsWithStats);
                            }//if(!$IsNewKeywords)

                            $KeywordsIdeas = count($Keywords) > 0 ? GetKeywordIdeasExample($user, $Keywords, 'STATS') : array();

                            $KeywordsIdeas2DAr = AdWordsKeywPlannerDataTo2DArray($KeywordsIdeas);

                            if ($IsNewKeywords)
                                InsertNewKeywordsIdeasToDB($CurClientID, $KeywordsIdeas2DAr);
                            else
                                SaveKeywordsIdeasToDB($CurClientID, $KeywordsWithStats, $KeywordsIdeas2DAr);

                            //$KeywordsIdeas = GetKeywordIdeasExample($user, $Keywords, 'IDEAS');





                            $ActionResHTML .=  ShowSavedKeywordIdeasFromDB($CurClientID, $KeywordsIdeas2DAr, $NeedFormatData);



                            if ($FromCron) {

                                $LogMessage = 'Adwords keywords ideas for client with ID \'' . $CurClientID . '\' was successful saved to DB.';

                                AddToLog(cron_log_file_name, $LogMessage);

                                $ActionResHTML = $LogMessage;
                            }//if( $FromCron )
                        }//else if($Action == "AdwordsDownloadKeywordIdeasAndSaveToDB    " && !empty($CurClientID))
                    }//if(!empty($_REQUEST['Action']) && !empty($_REQUEST['ClientID']))





                    $AllClientsFromDB = GetAccessTokensFromTable();

                    $CurrentClient = GetCurrentClient($AllClientsFromDB, $CurClientID);




                    $ClientsHTMlTable = !$FromCron && $CurrentClient != null ? BuildClientsHTMLTable($user, $CurrentClient) : '';

//if($FromCron)
                    //  $ActionResHTML = '';



                    $SESSION['FirtRun'] = 1;
                }//try
                catch (Exception $e) {
                    if (preg_match("/NOT_ADS_USER/", $e->getMessage())) {
                        ?>
                        <script>
                         //   location.href = "?force_adwords=1";
                        </script>
                        <?php
                    } else {
                        ?>
                        <script>
                          //  location.href = "?force_analytics=1";
                        </script>
                        <?php
                    }

                      UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsToken', 'AnalyticsChildActId', 'AnalyticsAccountName'), array('', '', ''), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));
                    $ErrorMsg = "<pre>" . $e->getMessage() . " " . $e->getTraceAsString() . "</pre>";

                    if ($FromCron)
                        AddToLog(error_log_file_name, $ErrorMsg);
                    else
                        $ActionResHTML = $ErrorMsg;
                }//catch (Exception $e)
                ?>



                <div  style="display: block;" id ="PleaseWaitDivID">

                     In progress, please wait ...

                </div>

              <div class="connect_form" style="display:none;">
                <form name="AuthDemoForm" id="AuthDemoFormFormID" method="POST">

                    <?php echo $ClientsHTMlTable; ?>

                    <?php echo $ActionResHTML; ?>

                    <div  style="display: none;">

                        <input type="hidden" name="ClientID" id="ClientIDEditID" />

                        <input type="hidden" name="Action" id="ActionEditID" />

                        <input type="hidden" name="ActionParam1" id="ActionParam1EditID" />

                        <input type="submit" name="Submit" id="SubmitButtonID" />

                    </div>

                </form>
</div>



                <script  type="text/javascript">



                    function DeleteClient(ClientID)

                    {

                        if (confirm("Are you sure you want to delete this client account?\n All client data will be deleted!!\n This is not reversible."))
                            DoAction(ClientID, 'DeleteCleint', '');

                    }//function DeleteClient(ClientID)





                    function DoAction(ClientID, Action, ActionParam1)

                    {
                        // localStorage.setItem("integrate-id", "4");
                            
                          
                         
                        document.getElementById("AuthDemoFormFormID").style.display = 'none';

                        document.getElementById("PleaseWaitDivID").style.display = 'block';



                        document.getElementById("ClientIDEditID").value = ClientID;

                        document.getElementById("ActionEditID").value = Action;

                        document.getElementById("ActionParam1EditID").value = ActionParam1;

                        document.getElementById("AuthDemoFormFormID").submit();

                    }//function DoAction(ClientID, Action, ActionParam1)



                    function ShowHideAddNewKeywordTextArea(ClientID)

                    {

                        document.getElementById("NewKeywordsDivID" + ClientID).style.display = document.getElementById("AdWordsAddNewKeywordsCheckBoxID" + ClientID).checked ? 'block' : 'none';

                    }



                </script>

                <?php
                /*
                  if(count($_POST) > 0)
                  {
                  if(isset($_POST['adwords-pull']))
                  {
                  $meta = get_user_meta($UserID, "adwords-pull");

                  if(empty($meta))
                  add_user_meta($UserID, "adwords-pull", $_POST['adwords-pull']);
                  else
                  update_user_meta($UserID, "adwords-pull", $_POST['adwords-pull']);


                  }
                  }
                  $meta = get_user_meta($UserID, "adwords-pull", true);
                  $city = get_user_meta($UserID, "city", true);
                  $zip = get_user_meta($UserID, "zip", true);
                  ?>



                  <form method="post">
                  Adwords Database Pull:
                  <select name="adwords-pull">
                  <option value="national" <?= ($meta == "national")? "SELECTED" : ""; ?> >National</option>
                  <option value="local" <?= ($meta == "local")? "SELECTED" : ""; ?> >Local - <?= $city . " " . $zip; ?></option>
                  </select>

                  <input type="submit" value="Save" />

                  </form>
                 */
                ?>



    <script type="text/javascript">



        var _paq = _paq || [];

        _paq.push(["setDocumentTitle", document.title]);

        _paq.push(["setCookieDomain", "*." + window.location.host]);

        _paq.push(["setDomains", ["*." + window.location.host]]);

        _paq.push(["setDoNotTrack", true]);

        _paq.push(["disableCookies"]);

        _paq.push(['trackPageView']);

        _paq.push(['enableLinkTracking']);

        (function() {

            var u = "<?php echo ANALYTICAL_URL; ?>/";

            _paq.push(['setTrackerUrl', u + 'analytics/conv_tracking.php']);

            _paq.push(['setSiteId', '634']);

            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];

            g.type = 'text/javascript';
            g.async = true;
            g.defer = true;
            g.src = u + 'analytics/conv_track.js';
            s.parentNode.insertBefore(g, s);

            console.log(_paq);

        })();



    </script>

    <noscript><p><img src="<?php echo ANALYTICAL_URL; ?>/analytics/conv_tracking.php?idsite=604" style="border:0;" alt="" /></p></noscript>

    <?php


    if (isset($_GET['force_adwords']))
        connectAdwords();

    function connectAdwords() {
        global $user;
        global $RedirectURL;
        global $CurClientID;

        $_SESSION['ClientID'] = $CurClientID;
        $_SESSION['AdWordsNewToken'] = true;

        $authorizationUrl = $user->GetOAuth2Handler()->GetAuthorizationUrl($user->GetOAuth2Info(), $RedirectURL, true);

        //$authorizationUrl .= "&approval_prompt=force";
        $authorizationUrl .= "&prompt=consent";
        //echo "TESTing";
        echo "<script>location.href='{$authorizationUrl}';</script>";
    }

}
?>
</div></div>
<?php  


//Rudra Code Starts

			$servername="admin.enfusen.com/cron/agencygaconnect.php";

			$RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .$servername ;
                          
			$RedirectURL1 = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER[REQUEST_URI];
			$GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true); //'/?action=go_to_predictive_analytics_settings'

			$authorizationUrl = base64_encode($GLOBALS["Client"]->createAuthUrl());

			$servername= base64_encode($RedirectURL1) ;

			$currenturl = md5((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'].$user_id);

			$GAAuthorizationUrl=$RedirectURL."?authorise_url=".$authorizationUrl."&md5_user_id=".$currenturl."&servername=".$servername;

			$AnalyticsAccountDataSub = str_replace('RedirURL1', $GAAuthorizationUrl, $AnalyticsAccountDataSub);


			//Rudra Code Ends

                        $AnalyticsAccountData .= $AnalyticsAccountDataSub;

?>
<div class="form-group m-b-0 bottom-footer radius-bottom" style="display:none;">
        <div class="col-md-12">
            <a data-toggle="tab" id="tab-item3"  onclick="" data-id="<?php echo $locationUserID;?>" data-user="<?php echo user_id();?>"  data-website="<?php $url= get_user_meta($UserID, 'website', TRUE); echo appendhttp($url);?>" class="btn btn-success">Next</a>
        </div>
    </div>
 </div>
<style>
input.btn.btn-disconnect.btn-success{
    color: #fff !important;
       background-color: #ed6b75 !important;
    border-color: #ea5460 !important;

    background-image: none !important;

}

input.btn.btn-success.btn-connect{
 color: #fff !important;
    background-color: #27a4b0 !important;
    border-color: #208992 !important;
    background-image: none !important;
}
div#PleaseWaitDivID {
    font-size: 14px;
    font-weight: 500;
    margin-top: 5px;
}
.child_a{
width:500px !important;
float:left  !important;
}
#AuthDemoFormFormID > select {
    width: 300px !important;
}
</style>
<?php // Code starts By Rudra 02-03-2017 ?>

<div id="test" data-id="<?php echo $UserID; ?>"></div>
<script>
$(document).ready(function() {
  setTimeout(function() {
    $("#test").trigger('click');
  }, 2000);
});

</script>
 
