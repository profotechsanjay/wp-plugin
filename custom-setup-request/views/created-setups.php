
<div class="keyword-table">
<table id="agency-created" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Agency URL</th>
                        <th>Phone No.</th>
                        <th>Country</th>
                        <th>No.of Clients</th>
                        <th>Created dt</th>
                    </tr>
                </thead>
               
                <tbody>
                    <?php
                    $setup_rows = $wpdb->get_results
                            (
                            $wpdb->prepare
                                    (
                                    "SELECT * FROM wp_setup_requests where ag_status = %d ORDER BY
                id DESC", 2
                            )
                    );
                    if (!empty($setup_rows)) {
                        $count = 1;
                        foreach ($setup_rows as $key => $value) {
                            if (!empty($value->emailAddress) && !empty($value->agency_url)) {
                                ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo $value->firstName . " " . $value->lastName; ?></td>
                                    <td><?php echo $value->emailAddress; ?></td>
                                    <td><?php echo "http://".strtolower(explode(".", str_replace(array("http://", "https://", "www."), "", $value->agency_url))[0]).".".ST_DOMAIN; ?></td>
                                    <td><?php echo $value->phoneNumber; ?></td>
                                    <td><?php echo $value->country; ?></td>
                                    <td><?php echo $value->current_number_of_clients; ?></td>
                                    <td><?php echo $value->created_dt; ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>

                </tbody>
            </table>
</div>
