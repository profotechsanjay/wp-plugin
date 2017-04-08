
<style>
    th.email-length, th.agency-length{width:200px !important;}
    .keyword-table .dataTable .btn{ margin: 5px 5px 0px !important; }
</style>

<div class="keyword-table lead-page">


    <table id="agency-request" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th class="email-length">Email</th>
                <th class="agency-length">Agency URL</th>
                <th>Phone No.</th>
                <th>Country</th><th>MRR</th>
                <th>No.of Clients</th>
                <th>Date Requested</th>
                <th class="button-area">Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $setup_rows = $wpdb->get_results
                    (
                    $wpdb->prepare
                            (
                            "SELECT * FROM enfusen_mcc_new.wp_setup_requests where ag_status = %d OR ag_status IS NULL ORDER BY
                updated_dt DESC", 0
                    )
            );

            if (!empty($setup_rows)) {
                $count = 1;$lead_array=array();
                foreach ($setup_rows as $key => $value) {
                    if (!empty($value->emailAddress) && !empty($value->agency_url)) {
                        
                        if(in_array($value->lead_id, $lead_array)){ continue; }else{ array_push($lead_array,$value->lead_id);  }
                        

                        ?>
                        <tr id="row<?php echo $value->id; ?>" data-id="<?php echo $count; ?>">
                            <td><?php echo $count; ?></td>
                            <td><?php echo $value->firstName . " " . $value->lastName; ?></td>
                            <td><?php echo $value->emailAddress; ?></td>
                            <td><?php echo $value->agency_url; ?></td>
                            <td><?php echo $value->phoneNumber; ?></td>
                            <td><?php echo $value->country; ?></td>
                            <td><?php echo $value->MRR; ?></td> 
                            <td><?php echo $value->current_number_of_clients; ?></td>
                            <td><?php echo $value->updated_dt; ?></td>
                            <td>
                                <button type="button" class="btn btn-primary appenddetails" data-toggle="modal" data-target="#loginDetails" data-name="<?php echo $value->firstName . " " . $value->lastName ?>" data-id="<?php echo $value->id; ?>" data-email="<?php echo $value->emailAddress; ?>" data-prefix="<?php echo explode(".", str_replace(array("http://", "https://", "www."), "", $value->agency_url))[0]; ?>"><i class="fa fa-cogs" aria-hidden="true"></i> Setup Agency</button>

                                <button class="btn btn-danger del-request" data-id="<?php echo $value->id; ?>" data-type="reject"><i class="fa fa-times" aria-hidden="true"></i> Cancel Request</button>
                            </td>
                        </tr>
                        <?php
                        $count++;
                    }
                }
            }
            ?>

        </tbody>
    </table>
</div>
