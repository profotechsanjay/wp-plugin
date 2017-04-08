<?php
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
//print_r($current_user);
$user_id = $current_user->ID;
session_start();
if (!isset($_SESSION['customuser'])) {
    header("location:" . site_url() . "/custom-agency-login/");
}
get_header();
?>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Open+Sans:300,400,600,700" rel="stylesheet">

<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/css/agdash.css"/>
<link rel="stylesheet" href="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/css/modal.css"/>
<style>
    form#camp-del-succ label.error{color:red;}


    #allcamapign-area .tooltip {

        color: white !important;

        font:11px "Open Sans",sans-serif;
        text-transform: capitalize;


    }


    #allcamapign-area .tooltip .tooltip-inner{ border-radius: 3px !important;}

</style>

<div class="sub-header">
    <div class="col-md-12">
        <?php $UserID = user_id(); ?>
        <h3 class="pull-left h3 mar_0">Clients</h3>
        <div class="pull-right col-md-6 pad_top_7 pad_right_0">
            <button  data-toggle="modal" data-target="#create-campaign-modal" data-controls-modal="create-campaign-modal" data-backdrop="static" data-keyboard="false" type="button" class="btn btn-primary btn-sm pull-right radius-4 font-12" id="create_campaign"><span class="cr-d"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create Campaign</span></button>  
            <div class="col-sm-4 pull-right">

                <!-- BEGIN HEADER SEARCH BOX -->
                <form id="tour-clntSrch" class="search-form pull-left" method="GET">
                    <div class="input-group">
                        <?php $clntVal = isset($_GET['cname']) ? $_GET['cname'] : ''; ?>
                        <input type="text" class="form-control" placeholder="Campaign Search" name="cname" id="lc-search" value="<?php echo $clntVal; ?>">

                    </div>
                </form>
                <!-- END HEADER SEARCH BOX -->
            </div>
        </div>
    </div>
</div>
<div class="en_box_container" id="allcamapign-area">

    <?php
    $locations = $wpdb->get_results
            (
            $wpdb->prepare
                    (
                    "SELECT * FROM wp_client_location ORDER BY created_dt DESC", ""
            )
    );
    $locations_list = array();
    foreach ($locations as $location) {
        $website = get_user_meta($location->MCCUserId, 'website', TRUE);
        $country = get_user_meta($location->MCCUserId, 'country', TRUE);
        $geolocation = get_user_meta($location->MCCUserId, 'geo_location', TRUE);
        $ctype = get_user_meta($location->MCCUserId, 'campaignType', TRUE);
        $brand = get_user_meta($location->MCCUserId, 'BRAND_NAME', TRUE);
        if (empty($brand)) {
            $brand = get_user_meta($location->MCCUserId, 'company_name', TRUE);
        }
        if (empty($website)) {
            continue;
        }
        if (isset($_SESSION['general']['website']) && $_SESSION['general']['website'] == $website) {
            continue;
        }
        //$countKeywords = $keywords = $wpdb->get_var("SELECT count(user_id) FROM keyword_opportunity where user_id = " . $location->MCCUserId);

        /* Count Active Keywords */
        $keywordDat = get_user_meta($location->MCCUserId, "Content_keyword_Site", true);
        $activation = $keywordDat["activation"];
        $countActiveKeywords = 0;
        foreach ($activation as $key => $value) {
            if ($value == "active") {
                $countActiveKeywords++;
            }
        }

        array_push($locations_list, array(
            "hostName" => str_replace(array("http://", "https://", "www."), "", $website),
            "website" => $website,
            "MccUserId" => $location->MCCUserId,
            "brand" => $brand,
            "country" => $country,
            "campaignType" => $ctype,
            "geolocation" => $geolocation,
            "totalActiveKeywords" => $countActiveKeywords
        ));
    }

    /* Sorting Function to make all client list as sorter by hostname */

    function compareByName($a, $b) {
        return strcmp($a["hostName"], $b["hostName"]);
    }

    usort($locations_list, 'compareByName');

    foreach ($locations_list as $id => $locArray) {

        $locationId = $locArray['MccUserId'];

        $totalKeywords = $wpdb->get_var("SELECT count(keyword.id) as totalKeywords FROM wp_keywords keyword INNER JOIN wp_keygroup keygroup ON keyword.group_id=keygroup.id and keyword.location_id = " . $locationId . " and keygroup.status = 1");

        //print_r($Query);
        ?>
        <div class="client-column">
            <div class="client-panel">
                <h5 class="ellipsis bold mont-font text-center"><?php echo str_replace(array("http://", "https://", "/"), "", $locArray['website']); ?></h5>
                <h6 class="ellipsis text-center"><?php echo $locArray['brand']; ?></h6>


                <img class="webframe" src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/web-frame.png">
                <div class="brd_txt_div">
                    <a href="<?php echo site_url() . '/user?usider=' . $locArray['MccUserId'] . '&us=qw&search=1' ?>" class="view-colon">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="javascript:;" data-toggle="modal" data-target="#location-edit-modal" class="edit_colon c-edit" data-id="<?php echo $locArray['MccUserId']; ?>" data-cname="<?php echo $locArray['brand']; ?>" data-web="<?php echo $locArray['website']; ?>" data-country="<?php echo $locArray['country']; ?>" data-ctype="<?php echo $locArray['campaignType']; ?>" data-geo="<?php echo $locArray['geolocation']; ?>">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a href="javascript:;" class="delete-colon del_usermeta2" id="del_<?php echo $locArray['MccUserId']; ?>" data-id="<?php echo $locArray['MccUserId']; ?>" data-toggle="modal" data-target="#delete-campaign">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
                <div class="bottom-text">
                    <h5 class="m-b-0 t-keyword text-center"><b>Total Keywords:</b><span class="pad_left_5"><?php echo $totalKeywords ?></span></h5>
                    <h6 class="m-b-5 keyword-target color-grey text-center"><b>Target:</b><span class="pad_left_5"><?php
                            if (!empty($locArray['country'])) {
                                echo lg_code_to_country($locArray['country']);
                            } else {
                                echo "No Target";
                            }
                            ?> </span></h6>

                    <div class="analytics-section">


                        <div class="row">


                            <div class="growth-field">

                                <?php
                                /* GA Connected */
                                //$ga_connected = get_user_meta($locationId, "ga_connected", true);
                                $ga_connected = is_ga_connected($locationId);
                                $ga_status = 0;
                                if (!empty($ga_connected) && $ga_connected == 1) {
                                    $ga_status = 1;
                                }
                                ?>

                                <div class="field-div" data-toggle="tooltip" data-placement="bottom" title="<?php echo $ga_status == 0 ? 'Please connect your Google Analytics' : 'Google analytics connected'; ?>">
                                    <div class="round-symbol"><span class="icon-symbol-div"><img src="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/images/analytics.png"></span></div>
                                    <?php
                                    if ($ga_status) {
                                        ?>
                                        <div class="tick-round"></div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="cross-round"></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class="growth-field">

                                <?php
                                /* Checking Conversation Code */
                                $conv_connected = check_lg_conv_code($locationId);
                                $conv_status = 0;
                                if ($conv_connected == 1) {
                                    $conv_status = 1;
                                }
                                ?>

                                <div class="field-div" data-toggle="tooltip" data-placement="bottom" title="<?php echo $conv_status == 0 ? 'Please install your conversion tracking code' : 'Conversion tracking code Installed'; ?>">
                                    <div class="round-symbol"><span class="icon-symbol-div"><img src="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/images/code.png"></span></div>
                                    <?php
                                    if ($conv_status) {
                                        ?>
                                        <div class="tick-round"></div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="cross-round"></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>



                            <div class="growth-field">
                                <div class="field-div" data-toggle="tooltip" data-placement="bottom" title="<?php echo $totalKeywords > 0 ? 'Keyword report active' : 'Please add your campaign keywords' ?>">
                                    <div class="round-symbol"><span class="icon-symbol-div"><img src="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/images/kyword.png"></span></div>
                                    <?php
                                    if ($totalKeywords > 0) {
                                        ?>
                                        <div class="tick-round"></div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="cross-round"></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>



                            <div class="growth-field">

                                <?php
                                /* Site Audit */
                                $site_audit = $wpdb->get_var("select audit_status from wp_site_audit where user_id = " . $locationId);
                                $site_audit_status = 0;
                                if ($site_audit == "Completed") {
                                    $site_audit_status = 1;
                                }
                                ?>

                                <div class="field-div" data-toggle="tooltip" data-placement="bottom" title="<?php echo $site_audit_status == 0 ? 'Please run a site audit' : 'Site sudit current'; ?>">
                                    <div class="round-symbol"><span class="icon-symbol-div"><img src="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/images/audit.png"></span></div>
                                    <?php
                                    if ($site_audit_status) {
                                        ?>
                                        <div class="tick-round"></div>
                                    <?php } else { ?>
                                        <div class="cross-round"></div>
                                    <?php } ?>
                                </div>
                            </div>



                            <div class="growth-field">
                                <?php
                                /* Citation Tracker */
                                $citation_tracker = $wpdb->get_var("select status from wp_citation_tracker where user_id = " . $locationId);
                                $citation_tracker_status = 0;
                                if ($citation_tracker == "complete") {
                                    $citation_tracker_status = 1;
                                }
                                ?>
                                <div class="field-div" data-toggle="tooltip" data-placement="bottom" title="<?php echo $citation_tracker_status == 0 ? 'Please run a citation audit' : 'Citation audit current'; ?>">
                                    <div class="round-symbol"><span class="icon-symbol-div"><img src="<?php echo LG_COUNT_PLUGIN_URL ?>/assets/images/citation.png"></span></div>
                                    <?php
                                    if ($citation_tracker_status) {
                                        ?>
                                        <div class="tick-round"></div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="cross-round"></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>

                    </div>          

                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="client-column add-compaign">
        <div class="client-panel">


            <div class="add_text_div">
                <a href="javascript:;" data-toggle="modal" data-target="#create-campaign-modal" data-controls-modal="create-campaign-modal" data-backdrop="static" data-keyboard="false" data-id="<?php echo $user_id; ?>" class="remove_last_session"> 
                    <img class="webframe" src="<?php echo LG_COUNT_PLUGIN_URL; ?>/assets/images/n_web_frame.png">
                    <p class="add-text mont-font text-center">Add New Campaign</p>
                </a>

            </div>


        </div>
    </div>
</div>

<?php include_once LG_COUNT_PLUGIN_DIR . '/views/lg-footer.php'; ?>

<?php get_footer(); ?>
