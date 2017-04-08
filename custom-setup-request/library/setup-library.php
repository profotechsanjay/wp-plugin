<?php

/**
 * AJAX Request Handler
 */
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

if (isset($_REQUEST["param"])) {

    /*
      Setup Status : 0 (Lead) , 1(Rejected) , 2(Created)
     */

    if ($_REQUEST["param"] == "reject_setup") {
        $hasrec = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM wp_setup_requests WHERE id = %d ", $_REQUEST['setupid']
                )
        );
        if ($hasrec > 0) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE wp_setup_requests SET ag_status = %d  WHERE id = %d", 1, $_REQUEST['setupid']
                    )
            );
            json(1, "Agency Setup Cancelled Successfully");
        } else {
            json(0, "Row Not found");
        }
    } else if ($_REQUEST["param"] == "create_setup") {
        $hasrec = $wpdb->get_var
                (
                $wpdb->prepare
                        (
                        "SELECT count(id) FROM wp_setup_requests WHERE id = %d ", $_REQUEST['setupid']
                )
        );
        if ($hasrec > 0) {
            $wpdb->query
                    (
                    $wpdb->prepare
                            (
                            "UPDATE wp_setup_requests SET ag_status = %d  WHERE id = %d", 2, $_REQUEST['setupid']
                    )
            );
            json(1, "Agency Setup Created Successfully");
        } else {
            json(0, "Row Not found");
        }
    } else if ($_REQUEST["param"] == "create_agency_setup") {

        $param = array(
            "fN" => $_REQUEST['first_name'],
            "lN" => $_REQUEST['last_name'],
            "street" => $_REQUEST['address'],
            "city" => $_REQUEST['city'],
            "country" => $_REQUEST['country'],
            "state" => $_REQUEST['state'],
            "emailAddr" => $_REQUEST['email'],
            "phoneNumber" => $_REQUEST['phone_no'],
            "current_number_of_clients" => $_REQUEST['client_num'],
            "website" => $_REQUEST['agency_url'],
            "what_is_your_average_monthly_retainer" => $_REQUEST['mrr'],
        );

        $id = save_to_ss($param);
        if ($id == 0) {

            json(0, $id);
        } else {

            $result = get_lead_from_ss($id);

            foreach ($result->result->lead as $data) {
                $now = date("Y-m-d H:i:s");
                $x = $wpdb->query
                        (
                        $wpdb->prepare
                                (
                                "INSERT INTO wp_setup_requests (lead_id, accountID, ownerID, companyName,firstName,lastName,street,city,country,state,zipcode,emailAddress,website,phoneNumber,mobilePhoneNumber,agency_url,MRR,what_type_of_agency,current_number_of_clients,created_dt,updated_dt) "
                                . "VALUES (%s, %s, %s, %s,%s, %s, %s,%s, %s, %s,%s, %s, %s,%s, %s, %s,%s, %s,%s,%s,%s)", $data->id, $data->accountID, $data->ownerID, $data->companyName, $data->firstName, $data->lastName, $data->street, $data->city, $data->country, $data->state, $data->zipcode, $data->emailAddress, $data->website, $data->phoneNumber, $data->mobilePhoneNumber, $data->agency_url_58135cadd4a6d, $data->what_is_your_average_monthly_retainer__5888beb7f22ec, $data->what_type_of_agency_do_you_work_for__5888bdf694074, $data->current_number_of_clients_5888be690a162, $now, $data->updateTimestamp
                        )
                );
            }

            json(1, "Data Inserted");
        }

        /* $country_detail = $wpdb->get_row
          (
          $wpdb->prepare
          (
          "SELECT name FROM lg_countries WHERE sortname = %s ", $_REQUEST['country']
          )
          );

          $is_created = $wpdb->query
          (
          $wpdb->prepare
          (
          "INSERT INTO wp_setup_requests (firstName, lastName, street,city,country,state,emailAddress,phoneNumber,current_number_of_clients,agency_url,MRR,ag_status,created_dt,updated_dt) "
          . "VALUES (%s, %s, %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", $_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['address'], $_REQUEST['city'], $_REQUEST['country'], $_REQUEST['state'], $_REQUEST['email'], $_REQUEST['phone_no'], $_REQUEST['client_num'], $_REQUEST['agency_url'], $_REQUEST['mrr'], 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")
          )
          );
          if ($wpdb->insert_id > 0) {
          json(1, $wpdb->insert_id);
          } else {
          json(0, "Failed to Create Client");
          } */
    } else if ($_REQUEST["param"] == "state_list") {

        $c_code = $_REQUEST['country_code'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_states.php?id=' . $c_code
        ));
        $result_states = curl_exec($curl);

        $result_states = json_decode($result_states);

        $states_array = array();
        foreach ($result_states as $key => $value) {
            $states_array[] = array("id" => $value->id, "title" => $value->title);
        }
        json(1, $states_array, "Parsed from setup-library");
    } else if ($_REQUEST["param"] == "city_list") {

        $state_code = $_REQUEST['state_code'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://admin.enfusen.com/cron/get_lg_cities.php?id=' . $state_code
        ));
        $result_cities = curl_exec($curl);

        $result_cities = json_decode($result_cities);

        $cities = $wpdb->get_results("select * from cities where state_id =" . $state_code);
        $cities_array = array();
        foreach ($result_cities as $key => $value) {
            $cities_array[] = array("id" => $value->id, "title" => $value->title);
        }
        json(1, array_map("unserialize", array_unique(array_map("serialize", $cities_array))));
    } else if ($_REQUEST["param"] == "sync_ss_data") {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://admin.enfusen.com/cron/cron-invitation-request.php'
        ));
        $data_rs = curl_exec($curl);

        $data_rs = json_decode($data_rs);

        json(1, $data_rs);
    }
}

/**
 * Custom Json Encode Function  
 */
function json($sts, $msg, $arr = array()) {
    $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
    print_r(json_encode($ar));
    die;
}

/* Send data to SS */

function save_to_ss($datas) {

    $accountID = '2_FB16A38E4D3FCEFBE6B79577D52586B7';
    $secretKey = '82C62C3D39E687EBDF344550692DBB7B';
    $leadId = 0;
    $method = 'createLeads';

    $params = array(
        'objects' => array(
            array(
                'firstName' => $datas['fN'],
                'lastName' => $datas['lN'],
                'emailAddress' => $datas['emailAddr'],
                'website' => $datas['website'],
                'street' => $datas['street'],
                'city' => $datas['city'],
                'country' => $datas['country'],
                'state' => $datas['state'],
                'phoneNumber' => $datas['phoneNumber'],
                'mobilePhoneNumber' => $datas['phoneNumber'],
                'current_number_of_clients_5888be690a162' => $datas['current_number_of_clients'],
                'what_is_your_average_monthly_retainer__5888beb7f22ec' => $datas['what_is_your_average_monthly_retainer']
            )
        )
    );
    $requestID = session_id();
    $data = array(
        'method' => $method,
        'params' => $params,
        'id' => $requestID,
    );
    $queryString = http_build_query(array('accountID' => $accountID, 'secretKey' => $secretKey));
    $url = "http://api.sharpspring.com/pubapi/v1/?$queryString";
    $data = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));

    $result = json_decode(curl_exec($ch));
    curl_close($ch);

    foreach ($result->result->creates as $key => $value) {
        $leadId = $value->id;
    }
    return $leadId;
}

function get_lead_from_ss($leadId) {

    $method = "getLead";
    $params = array("id" => $leadId);
    $requestID = session_id();
    $accountID = '2_FB16A38E4D3FCEFBE6B79577D52586B7';
    $secretKey = '82C62C3D39E687EBDF344550692DBB7B';
    $data = array(
        'method' => $method,
        'params' => $params,
        'id' => $requestID,
    );
    $queryString = http_build_query(array('accountID' => $accountID, 'secretKey' => $secretKey));
    $url = "http://api.sharpspring.com/pubapi/v1/?$queryString";

    $data = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
}

?>
