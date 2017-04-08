<script>
sessionStorage.setItem("lastname", "Smith");
</script>
<?php
include_once 'common.php';
include_once ABSPATH. '/wp-content/themes/twentytwelve/analytics/CommonUtils.php';
global $wpdb;

$base_url = site_url();
$locations = $wpdb->get_results
(
    $wpdb->prepare
    (
            "SELECT * FROM " . client_location() . " ORDER BY created_dt DESC",""
    )
);


?>
<style>
    .btnparent{
        background-color: #337ab7 !important; border-color: #2e6da4 !important; color: #fff !important;
        background-image: none !important;
    } 
    .btnparent:hover{color: #fff !important;}
    .gatrack select{
        height: 33px;
    }
</style>
<div class="contaninerinner trackdiv">     
    <h4>Google Analytic Connect</h4>
    <div class="panel panel-primary">
        <div class="panel-heading">Connect Location With Google Analytic </div>
        <div class="panel-body">
         

                <div class="form-group">
                    <label class="col-md-3 control-label">Select Location (Account)</label>
                    <div class="col-md-6">
                        <select required class="form-control chosen" name="gaconnect" id="gaconnect">
                            <option value="">Select Location (Account)</option>
                            <?php
                            foreach ($locations as $location) {
                                $id_loc = intval($_REQUEST['location_id']);
                                $sel = '';
                                if($id_loc == $location->id){
                                    $sel = 'selected="selected"';
                                }
                                $brand = get_user_meta($location->MCCUserId, 'BRAND_NAME', TRUE);
                                if (empty($brand)) {
                                    $brand = get_user_meta($location->MCCUserId, 'company_name', TRUE);
                                }
                                ?>
                                <option <?php echo $sel; ?> value="<?php echo $location->id; ?>"><?php echo $brand; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                <div class="row"><hr/></div>
                <div class="gatrack">
                    <?php if(isset($_REQUEST['location_id']) && intval($_REQUEST['location_id']) > 0){
                                                                               
                            $idloc = intval($_REQUEST['location_id']);
                            $loc = $wpdb->get_row
                                    (
                                    $wpdb->prepare
                                            (
                                            "SELECT MCCUserId,conv_verified FROM " . client_location() . " WHERE id = %d", $idloc
                                    )
                            );

                            if (!empty($loc)) {

                                $UserID = $loc->MCCUserId;
                                include_once get_template_directory() . '/analytics/AdWordsUtils.php';
                                include_once get_template_directory() . '/analytics/BrightLocalUtils.php';
                                include_once get_template_directory() . '/analytics/AnalyticsUtils.php';
                                $check_clients_table = GetAllRowsFromTableWithSpecifiedColumns('clients_table', ' MCCUserID,Name,AnalyticsToken ', "MCCUserID = $UserID", " `MCCUserID` ASC ");                                
                                
                                $_REQUEST['ClientID'] = $UserID;
                                
                                                                        
                                //set path to authorization by rudra 
                         
				$servername="admin.enfusen.com/cron/agencygaconnect.php";

				$RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .$servername ;

                                //Rudra Code Ends

                                $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);                                
                                PrevInitAdwordsUserSettings($user);                                                                        
                                $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);
                                
                                if (isset($_GET['code']) && trim($_GET['code']) != '' && !empty($_SESSION['ClientID'])) {

                                        $AuthGetCode = $_GET['code'];

                                        if (!isset($_SESSION['used_codes']))
                                            $_SESSION['used_codes'] = '';

                                        //if( strpos($_SESSION['used_codes'], $AuthGetCode) === FALSE )  
                                        //{

                                        $_SESSION['used_codes'] .= $AuthGetCode;

                                        $CurClientID = $_REQUEST['ClientID']; //$_SESSION['ClientID'];

                                        if (isset($_SESSION['AdWordsNewToken'])) {
                                            $user->SetOAuth2Info($user->GetOAuth2Handler()->GetAccessToken($user->GetOAuth2Info(), $AuthGetCode, $RedirectURL));
                                            $info = $user->GetOAuth2Info();

                                            $AccessTokenAsOneCSVStr = SaveAssoteatedArrayToOneCSVStr($info);


                                            saveAdwordsAccess($AccessTokenAsOneCSVStr);
                                            
                                            $ChildAccountsList = '';
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
                                        //Rudra Code Starts

                                        $locid = intval($_REQUEST['location_id']);

                                        $redurl = site_url().'/'.ST_LOC_PAGE.'?parm=ga_connect&location_id='.$locid;

                                        header("Location: $redurl");

                                        //Rudra Code Ends
                                        exit();

                                        //}//if( strpos($_SESSION['used_codes'], $AuthGetCode) === FALSE )   
                                    }
                                
                                if (!empty($_REQUEST['Action'])) {

                                    $Action = $_REQUEST['Action'];

                                    $ActionParam1 = $_REQUEST['ActionParam1'];

                                    $CurClientID = $UserID;

                                    if (!empty($_REQUEST['SelectedChildAccountComboBox' . $CurClientID])) {

                                        UpdateRowsInTable(AccessTokensDBTableName, array('AdWordsChildActId'), array($_REQUEST['SelectedChildAccountComboBox' . $CurClientID]), 'MCCUserID = ' . $CurClientID, array(1));

                                        $AllClientsFromDB = GetAccessTokensFromTable();
                                    }                          

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
                                    }

                                    if ($Action == "AddNewClient") {

                                        $NewClientID = InsertOneRowInToTable(AccessTokensDBTableName, array('AdWordsToken', 'AnalyticsToken'), array('', ''), array(1, 1));

                                        if ($NewClientID !== false) {

                                            CreateAnalyticsTablesForNewClient($NewClientID);

                                            CreateConvTrackTablesForNewClient($NewClientID);
                                        }
                                    }
                                    else if ($Action == "DeleteCleint" && !empty($CurClientID)) {

                                        DeleteAllRowsFromTable(AccessTokensDBTableName, 'MCCUserID = ' . $CurClientID);

                                        DeleteTableForClient(AnalyticsDataDBTableName, $CurClientID);

                                        DeleteTableForClient(AnalyticsCacheDataDBTableName, $CurClientID);

                                        DeleteTableForClient(ConvTrackingDBTableName, $CurClientID);

                                        DeleteTableForClient(ConvTrackingCacheDBTableName, $CurClientID);

                                        DeleteTableForClient(ConvTrackingFilteredDBTableName, $CurClientID);

                                        DeleteTableForClient(ConvTrackingUrlsDBTableName, $CurClientID);
                                    }
                                    else if ($Action == "ConnectAdwords" && !empty($CurClientID)) {
                                        connectAdwords();        
                                    }
                                    else if ($Action == "DisconnectAdwords" && !empty($CurClientID)) {

                                        UpdateRowsInTable(AccessTokensDBTableName, array('AdWordsToken', 'AdWordsChildActId', 'AdWordsAccountName'), array('', '', ''), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));
                                    }
                                    else if ($Action == "ConnectAnalytics" && !empty($CurClientID)) {

                                        $_SESSION['ClientID'] = $CurClientID;

                                        $_SESSION['AnalyticsNewToken'] = true;

                                        $authorizationUrl = $GLOBALS["Client"]->createAuthUrl();

                                        header("Location: " . $authorizationUrl);
                                    }
                                    else if ($Action == "DisconnectAnalytics" && !empty($CurClientID)) {

                                        UpdateRowsInTable(AccessTokensDBTableName, array('AnalyticsToken', 'AnalyticsChildActId', 'AnalyticsAccountName'), array('', '', ''), 'MCCUserID = ' . $CurClientID, array(1, 1, 1));
                                    }
                                    else if ($Action == "AdWordsShowSavedKeywordIdeasFromDB" && !empty($CurClientID)) {

                                        $OnlyEmpty = isset($_REQUEST['AdWordsOnlyEmptyCheckBox' . $CurClientID]);

                                        $ActionResHTML .= ShowSavedKeywordIdeasFromDB($CurClientID, LoadAdWordsKeywordsForPlannerFromDB($CurClientID, $OnlyEmpty), isset($_REQUEST['AdWordsFormatDataCheckBox' . $CurClientID]));
                                    }
                                    else if ($Action == "AdwordsDemoCampRpt" && !empty($CurClientID)) {

                                        LoadAdWordsAccessTokenFromDB($user, GetCurrentClient($AllClientsFromDB, $CurClientID));

                                        $StartDate = date(AdWordsRptDateFormat, strtotime('-30 day', strtotime('today')));

                                        $EndDate = date(AdWordsRptDateFormat, strtotime('-1 day', strtotime('today')));

                                        $RptFields = array('CampaignId', 'CampaignName', 'Cost', 'Clicks', 'ConversionsManyPerClick', 'ConversionRateManyPerClick', 'ValuePerConversionManyPerClick');

                                        $RptData = GetReportsData_through_AdHoc_ReportsEx($user, $user->GetClientCustomerId(), 'CAMPAIGN_PERFORMANCE_REPORT', 'Campaigns Performance Report ', $RptFields, $StartDate, $EndDate, array());

                                        $ActionResHTML .= '<h3>Demo report data from  Google Adwords:</h3>' . ForMatAdWordsRptDataToHTMlTable($RptData, $RptFields);
                                    }
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
                                        }

                                        if (empty($tableId)) {

                                            $ProfilesIds = getProfilesIds(true);

                                            foreach ($ProfilesIds as $CurKey => $CurVal) {

                                                $tableId = $CurKey;

                                                break;
                                            }
                                        }

                                        $StartDate = !empty($_REQUEST['startdate' . $CurClientID]) ? InvertDateForAnalyticsRpt($_REQUEST['startdate' . $CurClientID]) : strtotime('-1 day', strtotime('today'));

                                        $EndDate = !empty($_REQUEST['enddate' . $CurClientID]) ? InvertDateForAnalyticsRpt($_REQUEST['enddate' . $CurClientID]) : strtotime('-60 day', strtotime('today'));



                                        if ($Action == "AnalyticsDownloadAndSaveToDBReport") {

                                            $Metrics = GAPrefix . "visits, " . GAPrefix . "timeOnSite, " . GAPrefix . "bounceRate, " . GAPrefix . "avgSessionDuration, " . GAPrefix . "pageviews,  " . GAPrefix . "goalConversionRateAll"; //.GAPrefix."adClicks"          

                                            $Dimensions = GAPrefix . "hostname, " . GAPrefix . "pagePath, " . GAPrefix . "isoYear, " . GAPrefix . "medium, " . GAPrefix . "month, " . GAPrefix . "year, " . GAPrefix . "yearMonth";

                                            $RptParams = array('dimensions' => $Dimensions, 'sort' => "-" . GAPrefix . "medium", 'max-results' => '10000', 'start-index' => 1);



                                            $StopFlag = false;

                                            $RptDataRows = array();

                                            $cc = 0;

                                            while (!$StopFlag) {

                                                $RptData = $GLOBALS["Analytics"]->data_ga->get(urldecode(GAPrefix . $tableId), $StartDate, $EndDate, $Metrics, $RptParams);

                                                $CurRptDataRows = $RptData->getRows();

                                                foreach ($CurRptDataRows as $CurRptDataRow) {



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





                                        $ActionResHTML .= '<br/>' . ShowSavedKeywordIdeasFromDB($CurClientID, $KeywordsIdeas2DAr, $NeedFormatData);



                                        if ($FromCron) {

                                            $LogMessage = 'Adwords keywords ideas for client with ID \'' . $CurClientID . '\' was successful saved to DB.';

                                            AddToLog(cron_log_file_name, $LogMessage);

                                            $ActionResHTML = $LogMessage;
                                        }
                                    }
                                }
                                
                                $AnalyticsToken_check = $check_clients_table[0][2];
                                
                                if (isset($_POST['parent_analytics_user_id'])) {                                    
                                                                        
                                    update_user_meta($UserID, 'parent_analytics_user_id', $_POST['parent_analytics_user_id']);                                                                        
                                }
                                
                                $parent_analytics_user_id = get_user_meta($UserID, 'parent_analytics_user_id', true);
                                
                                
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
                                        else {                                            
                                            $AllClientsFromDB = GetAccessTokensFromTable();
                                        }
                                        
                                        
                                        $CurClientID = !empty($_REQUEST['ClientID']) ? $_REQUEST['ClientID'] : null;                                        
                                        $CurrentClient = GetCurrentClient($AllClientsFromDB, $CurClientID);
                                        
                                        
                                        
                                        $ClientsHTMlTable = BuildClientsHTMLTable($user, $CurrentClient, $idloc);
                                        
                                        ?>
                                        <div  style="display: none;" id ="PleaseWaitDivID">

                                            Please wait, data is loading....

                                        </div>
                                         <form name="AuthDemoForm" id="AuthDemoFormFormID" method="POST">
                                             
                                            <?php echo $ClientsHTMlTable; ?>                                             

                                            <div  style="display: none;">

                                                <input type="hidden" name="ClientID" id="ClientIDEditID" />

                                                <input type="hidden" name="Action" id="ActionEditID" />

                                                <input type="hidden" name="ActionParam1" id="ActionParam1EditID" />    

                                                <input type="submit" name="Submit" id="SubmitButtonID" />

                                            </div>

                                        </form>
                                        <?php


                                    } catch (Exception $ex) {
                                        
                                        $loc = $_REQUEST['location_id'];
                                        if (preg_match("/NOT_ADS_USER/", $ex->getMessage())) {
                                            ?>
                                            <script>
                                                location.href = "?parm=ga_connect&location_id=<?php echo $loc; ?>&force_adwords=1";
                                            </script>
                                            <?php
                                        } else {
                                            ?>
                                            <script>
                                                location.href = "?parm=ga_connect&location_id=<?php echo $loc; ?>&force_analytics=1";
                                            </script>
                                            <?php
                                        }
                                    }
                                }
                                    
                                if ($AnalyticsToken_check == '') {
                
                                    $all_users_list = GetAllRowsFromTableWithSpecifiedColumns('clients_table', ' MCCUserID,Name ', "AnalyticsToken != '' ", " `MCCUserID` ASC ");

                                    ?>
                                    <form name="parent_analytics_Frm" action="" method="post">
                                        <div style="font-weight:bold;font-size: 16px;float:left;">Set Parent Analytics User</div>
                                        <div style="float:left;margin-left:20px;">
                                            <select name="parent_analytics_user_id" id="parent_analytics_user_id" class='form-control'>
                                                <option value="">Select Client</option>    
                                                <?php
                                                foreach ($all_users_list as $single_user) {
                                                    $analytics_MCCUserID = $single_user[0];
                                                    $active_role = role($analytics_MCCUserID);
                                                    $analytics_website = get_user_meta($analytics_MCCUserID, 'website', true);
                                                    if ($active_role != 'worker' && $active_role != 'canceled_user' && $analytics_website != '') {
                                                        $brand_name = brand_name($analytics_MCCUserID);
                                                        if ($brand_name != "") {
                                                            ?>
                                                            <option <?php if ($analytics_MCCUserID == $parent_analytics_user_id) echo 'selected'; ?> value="<?php echo $analytics_MCCUserID ?>"><?php echo $brand_name; ?> - <?php echo $analytics_MCCUserID; ?>(<?php echo $analytics_website; ?>) </option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>  
                                            </select>
                                            <?php 
                                            if($parent_analytics_user_id > 0){
                                                ?>
                                                    <a href="javascript:;" onclick='remove_parent_user(<?php echo $UserID; ?>,<?php echo $idloc; ?>)'>Remove Parent User</a>
                                                <?php
                                            }
                                            
                                            ?>
                                        </div>
                                        <input style="float:left;margin-left:10px;" class='btn btnparent' onclick="check_parent_analytics()" type="button" name="parent_analytics_btn" value="Set Parent Analytics User">
                                    </form>
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
                                
                                
                            } else {
                                ?>
                                <div class="centerlocmsg">Invalid Location</div>
                                <?php
                            }

                            ?>                           
                        <?php                        
                        
                    } else {
                        ?>
                        <div class="centerlocmsg">No Location Selected</div>
                        <?php
                    } ?>
                </div>
                                


        </div>
    </div>


</div>
<?php

 function BuildClientsHTMLTable($user, $CurClient, $idloc) {

    $ClientsHTMLTable = '';


    $AdWordsAccountData = ''; //'<b>Google Adwords </b>';

    if (true) {//if( !empty($CurClient[1]) ) 
        LoadAdWordsAccessTokenFromDB($user, $CurClient);

        $OAuth2Info = $user->GetOAuth2Info();
        
        $time = $OAuth2Info['timestamp'] + $OAuth2Info['expires_in'];

       

        $AdwordsAccessToken_key = md5($OAuth2Info['access_token'] . $OAuth2Info['refresh_token'] . $OAuth2Info['timestamp'] . $OAuth2Info['expires_in']);



        if (!isset($_SESSION['AdwordsChildAccountsCache']))
            $_SESSION['AdwordsChildAccountsCache'] = array();



        if (!array_key_exists($AdwordsAccessToken_key, $_SESSION['AdwordsChildAccountsCache'])) {
            $ChildAccountsList = '';
            //$ChildAccountsList = GetChildAccountsListAsAr($user, $MCCAccountName);

            $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key] = $ChildAccountsList;

            $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key . 'MCCAccountName'] = $MCCAccountName;
        }



        $ChildAccountsList = $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key];

        $MCCAccountName = $_SESSION['AdwordsChildAccountsCache'][$AdwordsAccessToken_key . 'MCCAccountName'];

      
    }
    

    $AnalyticsAccountData = '<b>Google Analytics </b>';

    
    if (isset($_GET['force_analytics']))
        $CurClient[4] = "";

   
    if (!empty($CurClient[4])) {
        
        LoadAnalyticsAccessTokenFromDB($CurClient);
        
        if (empty($GLOBALS["Analytics"]))/////////////////////
            $GLOBALS["Analytics"] = new Google_Service_Analytics($GLOBALS["Client"]);

        $AnalyticsAccountData .= '<i>Account name: </i><b>' . $CurClient[6] . '</b>';

        $AnalyticsAccountData .= '<input style="margin-left:20px;" type="button" value="Disconnect" class="btnparent"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'DisconnectAnalytics\', \'\')" />';

        $AnalyticsAccountData .= '<br/><br/>';

        $AnalyticsSelChildAccountID = -1;

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



        $AnalyticsAccountData .= 'Select Child Account: ' . CreateHTMLSelectWithItems($AnalyticsChildAccountsList, $AnalyticsSelChildAccountID, 'SelectedAnalyticsChildAccount', $CurClient[0], 'OnChange="DoAction(\'' . $CurClient[0] . '\', \'SelectedAnalyticsChildAccountChanged\', \'\')"');

        $AnalyticsAccountData .= '&nbsp;&nbsp; Select Propertie: ' . CreateHTMLSelectWithItems($AnalyticsWebPropertiesList, $AnalyticsSelWebPropertieID, 'SelectedAnalyticsWebPropertie', $CurClient[0], 'OnChange="DoAction(\'' . $CurClient[0] . '\', \'SelectedAnalyticsWebPropertieChanged\', \'\')"');

        $AnalyticsAccountData .= '&nbsp;&nbsp; Select Profile: ' . CreateHTMLSelectWithItems($AnalyticsSelProfilesList, $AnalyticsSelProfileID, 'SelectedAnalyticsProfile', $CurClient[0]);



        $AnalyticsAccountData .= '<br/>';

        //$AnalyticsAccountData .= '<input type="button" value="Run demo report"  OnClick="DoAction(\''.$CurClient[0].'\', \'AnalyticsDemoReport\', \'\' )" />';
        //$AnalyticsAccountData .= '<input type="checkbox"  name="AnalyticsFormatDataCheckBox'.$CurClient[0].'" '.(isset($_REQUEST['AnalyticsFormatDataCheckBox'.$CurClient[0]]) ? 'CHECKED' : '' ).' /> Format Data';               

        $RemoveQueryParamsCheckBoxChecked = isset($_REQUEST['RemoveQueryParamsCheckBox' . $CurClient[0]]);

        $RemoveWWWPrefixCheckBoxChecked = isset($_REQUEST['RemoveWWWPrefixCheckBox' . $CurClient[0]]);

        if (!isset($SESSION['FirtRun'])) {

            $RemoveQueryParamsCheckBoxChecked = true;

            $RemoveWWWPrefixCheckBoxChecked = false;
        }



        $AnalyticsAccountData .= '<input type="checkbox" style="display:none;"  name="RemoveQueryParamsCheckBox' . $CurClient[0] . '" ' . ($RemoveQueryParamsCheckBoxChecked ? 'CHECKED' : '' ) . ' /> '; //Remove Query Params
        //$AnalyticsAccountData .= '<input type="checkbox"  name="RemoveWWWPrefixCheckBox' . $CurClient[0] . '" ' . ($RemoveWWWPrefixCheckBoxChecked ? 'CHECKED' : '' ) . ' /> Remove WWW Prefix';



        $StartDate = !empty($_REQUEST['startdate' . $CurClient[0]]) ? $_REQUEST['startdate' . $CurClient[0]] : '01-04-2015' /* date(CalendarControlRptDateFormat, strtotime('-60 day', strtotime('today'))) */;

        $EndDate = !empty($_REQUEST['enddate' . $CurClient[0]]) ? $_REQUEST['enddate' . $CurClient[0]] : date(CalendarControlRptDateFormat, strtotime('-1 day', strtotime('today')));

        //$AnalyticsAccountData .= 'Start date: <input type="text" name="startdate' . $CurClient[0] . '" id="startdate"  value="' . $StartDate . '" onfocus="this.select();lcs(this)"  onclick="event.cancelBubble=true;this.select();lcs(this)">';
        //$AnalyticsAccountData .= 'End date: <input type="text" name="enddate' . $CurClient[0] . '" id="enddate" value="' . $EndDate . '" onfocus="this.select();lcs(this)"  onclick="event.cancelBubble=true;this.select();lcs(this)">';

        $AnalyticsAccountData .= '<br/>';

        //$AnalyticsAccountData .= '<input type="button" value="Download report and save to DB"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'AnalyticsDownloadAndSaveToDBReport\', \'\' )" />';
        //$AnalyticsAccountData .= '<input style="margin-left:20px;" type="button" value="Show saved reports from DB"  OnClick="DoAction(\'' . $CurClient[0] . '\', \'AnalyticsShowSavedReportsFromDB\', \'\' )" />';
        // Only For First Time
        $an_from_date = date('Y-m-d', strtotime('first day of last month'));
        $an_to_date = date('Y-m-d', strtotime('-1 days', time()));
        //$daily_data_url = site_url() . '/cron/pullga.php?client_id=' . $CurClient[0] . '&start_date=' . $an_from_date . '&end_date=' . $an_to_date . '&save_mode=1&page=analytics';
        //$AnalyticsAccountData .= '<input class="btn btn-success" style="margin-left:20px;background:none;" type="button" value="Get Analytics Historical Data"   />'; //onclick="document.location.href=\'' . $daily_data_url . '\'"
        
        $daily_data_url = 'client_id=' . $CurClient[0] . '&start_date=' . $an_from_date . '&end_date=' . $an_to_date . '&save_mode=1&page=analytics';
        $urlchangeuser = '?parm=execution&function=location_change&location_id='.$idloc;
        $AnalyticsAccountData .= '<a href="'.$urlchangeuser.'&' . $daily_data_url . '" class="btn btn-success"> Pull Last 30 Days Data </a>';
        //
    }//if( !empty($CurClient[4]) ) 
    else {//////////NEW ANALYTICS
        $AnalyticsAccountData .= 'not connected ';

   // header("Location: $redurl");
      
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
 analyticfromadmin(locid);
//window.top.location.href = "RedirURL2";
if( inIframe () )

window.top.location.href = "RedirURL1";

else

window.location.href = "RedirURL1";   

}             

</script>

EOD;
        $_SESSION['ClientID'] = $CurClient[0];

        $_SESSION['AnalyticsNewToken'] = true;

        //$GAAuthorizationUrl = $GLOBALS["Client"]->createAuthUrl();

	//Rudra Code Starts
	$servername="admin.enfusen.com/cron/agencygaconnect.php";

	$RedirectURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .$servername ;

	$RedirectURL1 = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/analytics-settings/';

	$GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true); //'/?action=go_to_predictive_analytics_settings'


	$authorizationUrl = base64_encode($GLOBALS["Client"]->createAuthUrl());

	$servername= base64_encode($RedirectURL1) ;

	$currenturl = md5((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'].$user_id);

	$GAAuthorizationUrl=$RedirectURL."?authorise_url=".$authorizationUrl."&md5_user_id=".$currenturl."&servername=".$servername;

	$AnalyticsAccountDataSub = str_replace('RedirURL1', $GAAuthorizationUrl, $AnalyticsAccountDataSub);

	//Rudra Code Ends


        //$AnalyticsAccountDataSub = str_replace('RedirURL2', $GAAuthorizationUrl2, $AnalyticsAccountDataSub);
        
        $AnalyticsAccountDataSub = str_replace('locid', $_REQUEST['location_id'], $AnalyticsAccountDataSub);

        $AnalyticsAccountData .= $AnalyticsAccountDataSub;
$RedirectURL1 = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/analytics-settings/';


        $AnalyticsAccountData .= '<input type="button" value="Connect"  class="btnparent"  OnClick="DoGoogleAnalyticsRedirect()" />';

        //$AnalyticsAccountData .= '<a target="_parent" href="'.$GAAuthorizationUrl.'"><input type="button" value="Connect"   /></a>';
        //$AnalyticsAccountData .= '<input type="button" value="Connect"  OnClick="DoAction(\''.$CurClient[0].'\', \'ConnectAnalytics\', \'\')" />';
//$RedirectURL = site_url().'/analytics-settings/';



        PrevInitAdwordsUserSettings($user);

        $GLOBALS["Client"] = BasicallyInitGoogleClient($RedirectURL, true);
    }//else from if( !empty($CurClient[4]) ) 



    GenerateBTLToken();

    $BTLAccountData = '';

    $BTLChildAccountsList = GetBTLChildAccountsIDsAndNames(GetBTLChildAccountsList());

    $BTLChilActID = $CurClient[7];

    $BTLReportsList = GetBTLReportsIDsAndNames(GetBTLReportsList($BTLChilActID), false);



    $ClientsHTMLTable .= $AdWordsAccountData . '<br />' . '<br />'; // .= '<td>'.$AdWordsAccountData.'</td>';

    $ClientsHTMLTable .= $AnalyticsAccountData . '<br />' . '<br />'; //'<td>'.$AnalyticsAccountData.'</td>'

    $ClientsHTMLTable .= $BTLAccountData . '<br />'; //'<td>'.$AnalyticsAccountData.'</td>'

    
    return $ClientsHTMLTable;
}
                    
?>

<script type="text/javascript">
    
    function DoAction(ClientID, Action, ActionParam1){

        jQuery("#AuthDemoFormFormID").hide();
        jQuery("#PleaseWaitDivID").show();
        jQuery("#ClientIDEditID").val(ClientID);
        jQuery("#ActionEditID").val(Action);
        jQuery("#ActionParam1EditID").val(ActionParam1);
        jQuery("#AuthDemoFormFormID").submit();

    }
    
    function DeleteClient(ClientID){

    if (confirm("Are you sure you want to delete this client account?\n All client data will be deleted!!\n This is not reversible."))
        DoAction(ClientID, 'DeleteCleint', '');

}


function ShowHideAddNewKeywordTextArea(ClientID){

    document.getElementById("NewKeywordsDivID" + ClientID).style.display = document.getElementById("AdWordsAddNewKeywordsCheckBoxID" + ClientID).checked ? 'block' : 'none';

}

</script>
