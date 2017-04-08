<?php

/*** General Variables **/

define('MCC_URL', '{{mcc_url}}'); // global site url
define('ANALYTICAL_URL', '{{analytical_url}}'); // Analytical url
define('MCC_DIR', dirname(__FILE__)); // root directory
define('MCC_ADMIN_NAME', '{{admin_name}}');
define('MCC_SITE_NAME', '{{site_name}}'); // global website name
define('MCC_NAME', '{{blog_name}}'); // Name of Blog
define('MCC_NO_REPLY_EMAIL', '{{no_replay_email}}'); // global mcc no replay email
define('MAX_KEYWORD_SHOW_REPORT', 100); // Maximum number keywords show in case of multiple locations
define('FB_APP_ID', '{{fb_id}}'); // FB APP ID
define('FB_APP_SECRET', '{{fb_secret}}'); // FB APP SECRET
define('ST_CRON_KEY', '{{setting_cron_key}}');
define('ST_LOC_PAGE', 'location-settings');

/*** MCC Wordpress Database Variables **/
define('database_host_wp','{{mcc_host}}' );  // MCC Database Host
define('database_name_wp','{{mcc_db}}' );  // MCC Database Name
define('database_user_wp','{{mcc_user}}' ); // MCC Database User
define('database_password_wp','{{mcc_pwd}}' ); // MCC Database Password

/*** Analytical Database Variables **/
define('database_host','{{mcc_host}}' );   // Analytics Database Host
define('database_name','{{analytical_db}}' ); // Analytics Database Name
define('database_user','{{mcc_user}}' ); // Analytics Database User
define('database_password','{{mcc_pwd}}' );  // Analytics Database Password

/*** Grader Database Variables **/
define('database_host_grader', '{{mcc_host}}'); // Grader Database Host
define('database_name_grader', '{{grader_db}}'); // Grader Database Name
define('database_user_grader', '{{mcc_user}}'); // Grader Database User
define('database_password_grader', '{{mcc_pwd}}'); // Grader Database Password


/*** Parent DB **/
define('parent_host', '{{parent_host}}'); // Parent Database Host
define('parent_db', '{{parent_db}}'); // Parent Database Name
define('parent_user', '{{parent_user}}'); // Parent Database User
define('parent_pwd', '{{parent_pwd}}'); // Parent Database Password


/******* Brightlocal Global Variable *****/    
define('BTL_API_KEY', 'e9ac379e22500737efe4e1e0a700d84e304126d6'); // Brighlocal API Key
define('BTL_API_SECRET', '5216018119bf2'); // Brighlocal API Secret
define('BTL_API_TOKEN_IN_SESSION', 'BTL_API_TOKEN_IN_SESSION');  // Brighlocal API Token Session
define('BTL_MAIN_URL', 'http://tools.brightlocal.com/seo-tools/');  // Brighlocal Main URL
define('BTL_BASE_URL', 'http://tools.brightlocal.com/seo-tools/api/v2/');  // Brighlocal Base URL
define('BTL_BASE_URL_new_client', 'http://tools.brightlocal.com/seo-tools/api/v1/');  // Brighlocal Base URL New Client


/******* Start AnalyticsUtils.php  Global Variable *****/
define('APP_NAME', 'Google Analytics Reporting Application');  // Google Analytics App Name
define('ANALYTICS_SCOPE_READ', 'https://www.googleapis.com/auth/analytics.readonly'); // Google Analytics Read Scope
define('ANALYTICS_SCOPE_WRITE', 'https://www.googleapis.com/auth/analytics'); // Google Analytics Write Scope
define('ANALYTICS_SCOPE_MANAGE_USERS', 'https://www.googleapis.com/auth/analytics.manage.users'); // Google Analytics Manage User Scope     
define('GAPrefix', 'ga:'); // Google Analytics prefix
define('AnalyticsRptDateFormat','Y-m-d'); // Google Analytics Report Date Format


/******* SEM RUSH API *****/

define('semrush', '562e2601cd42d050497232b4b6510a31'); // SEM Rush Key
define("semrush_main_api_url", "http://api.semrush.com/"); // SEM Main Url
define('semrush_project_url', 'http://api.semrush.com/reports/v1/projects/'); // SEM Project URL


/* PLACESSCOUT API */

define("placesscout_username", "rbryan"); // Placesscout API Username
define("placesscout_password", "TG7RN5XvKaJvt9UiAKYpAy8"); // Placesscout API Password
define("placesscout_main_api_url", "https://apihost1.placesscout.com/"); // Placesscout API Main URL

/******* YEXT API Global Variable *****/
define('YEXT_API', '4eyo0t3Jxg6yl5YhIo56');


/**** Announcement Plugin Global Variable *****/
define('AN_SITE_NAME', '{{site_name}}'); // Website Name
define('ENFUSEN_URL', '{{announcements_url}}'); // Announcement URL
define('SET_PARENT_URL', '{{parent_url}}'); //Start Parent API - Saving its url

/****** Common Utils - analytics/analytics/CommonUtils.php ****/

define('USER_AGENT','Your firm title User Agent'); // User Agent
define('DEVELOPER_TOKEN','x30PWVF2e00-OSKg4dcvlQ');  // Developer Token
define('SpecSeparatorStr', ',}/$^&'); // Spec Separator Str
define('oauth2_clientId','113963285856-fqsnvffu5emnspk6llf611eo9ciin2e4.apps.googleusercontent.com');  // Googla Oauth Client ID                            
define('oauth2_clientSecret','GaM_cdyY9u_XSp-UmlcZOKkD');  // Googla Oauth Client Secret

define('oauth2_clientId_old','895018816070-do7temdmb22evbbqglne510iondr7b1d.apps.googleusercontent.com');                                                       
define('oauth2_clientSecret_old','X23w1UfgYMFf5si38l2GocFG');
define('oauth2_clientId_old2','353872139611-ju6r114s71atg8rq12kbi08p2f8245ij.apps.googleusercontent.com'); 
define('oauth2_clientSecret_old2','oU58Oyg3hvnPrt8ppkkyVXc3'); 
define('CalendarControlRptDateFormat','d-m-Y'); // Calendar Control Report Date Format
define('MySQlDateFormat','Y-m-d'); // Mysql Date Format
define('MICROS_CONV',1000000.0);

define('AccessTokensDBTableName', 'clients_table');        
define('AnalyticsDataDBTableName', 'main_analytics');           
define('AnalyticsCacheDataDBTableName', 'main_analytics_cache');
define('ConvTrackingDBTableName', 'conv_tracking');                      
define('ConvTrackingCacheDBTableName', 'conv_tracking_cache');                      
define('ConvTrackingFilteredDBTableName', 'conv_tracking_filtered');                      
define('ConvTrackingUrlsDBTableName', 'conv_tracking_urls');                      
define('ConvTrackJSCodeFileName', 'conv_track_js_code.js');
define('SEODBTableName', 'seo');
define('cron_log_file_name', 'cron_log.txt');           
define('error_log_file_name', 'error_log.txt');

/* Disallow Or Allow agency to edit files from wp editor */
define('DISALLOW_FILE_EDIT', true); // possible values: true, false
define('BILLING_ENABLE', 1); // possible values: Enable = 1, Disable = 0

?>
