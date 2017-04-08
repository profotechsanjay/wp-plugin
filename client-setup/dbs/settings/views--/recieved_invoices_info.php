<?php

include_once 'common.php';
include("wp-load.php");
include('wp-content/themes/twentytwelve/analytics/my_functions.php');
die;
global $wpdb;

$main_webiste = SET_PARENT_URL;
$website_var = parse_url($main_webiste);

$base_url = site_url();
$database_name = $wpdb->dbname;
$main_web_url = $website_var[scheme]."://".$website_var[host];
$current_page_url = site_url()."/location-settings/?parm=invoices_recieved";

$limit = 15;  //Limit for per page
if($_GET['pagination']){
    $offset = $limit*($_GET['pagination']-1);
    $page = $_GET['pagination'];
} else {
    $offset = 0;
    $page = 1;
}

$data = array('accountname' => $database_name, 'limit' => $limit, 'offset' => $offset);

//print_r($data);

$data_string = http_build_query($data);
$url = $main_web_url."/agency_invoices_api.php";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



$result = curl_exec($ch);
/*
try{
    
    $output = $result;
    
    print_r(json_decode($output)); 
    
} catch (Exception $ex) {
    echo $ex->getMessage();
}
*/

/*
$result_stripslash = stripslashes($result);
$results = json_decode($result_stripslash);
echo "<pre>"; 
print_r($results->invoice_array);
print_r($results->rowcount);
die;
*/
?>
<div class="contaninerinner">     
    <h4>Invoices</h4>
    <div class="panel panel-primary">
        <div class="panel-heading">Invoices Info</div>
        <div class="panel-body">
            <?php
            $result_stripslash = stripslashes($result);
            $get_results = json_decode($result_stripslash);
            //echo "<pre>"; 
            //print_r($get_results);
            //die;
            ?>           
            <div id="post-body-content">
                <div class="dataTables_wrapper" id="wp-list-table_wrapper">
                    <table cellspacing="0" class="wp-list-table widefat fixed invoices_recieved" id="wp-list-table">
                        <thead>
                            <tr>
                                <td><strong>Title</strong></td>
                                <td><strong>Total Paid</strongtdth>
                                <td><strong>Date</strong></td>
                                <td><strong>Status</strong></td>
                                <td><strong>Type</strong></td>
                                <td><strong>Invoice ID</strong></td>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $n = 0;?>
                            <?php foreach($get_results->invoice_array as $get_result){
                                $n = $n + 1;
                                if($n%2 == 0){
                                    $class_name = "even";
                                } else {
                                    $class_name = "odd";
                                }
                                
                                $post_id = $get_result->ID;

                                $post_date = $get_result->post_date;

                                $post_status = $get_result->post_status;

                                $invoice_id = $get_result->invoice_id;

                                $hash = $get_result->hash;

                                $total_payments = $get_result->total_payments;

                                $type = $get_result->type;

                                $invoice_permalink = $get_result->invoice_permalink;

                                $current_time = new DateTime();
                                $invoice_time = new DateTime($post_date);
                                $interval = $current_time->diff($invoice_time);


                                if($interval->y != 0){
                                    $time = $interval->y ." Year";
                                }elseif($interval->m != 0){
                                    $time = $interval->m ." Month";
                                }elseif($interval->d != 0){
                                    $time = $interval->d ." Days";
                                }elseif($interval->h != 0){
                                    $time = $interval->h ." Hour";
                                }elseif($interval->i != 0){
                                    $time = $interval->i ." Minute";
                                }elseif($interval->s != 0){
                                    $time = $interval->s ." Seconds";
                                }

                                //echo "Updated " . $interval->h . " hours ago";
                                ?>

                                <tr class="<?php echo $class_name;?>">
                                    <td><?php echo $get_result->post_title;?></td>
                                    <td>$<?php echo $total_payments;?></td>
                                    <td><?php echo $time." ago";?></td>
                                    <td><?php echo ucfirst($post_status);?></td>
                                    <td><?php echo ucfirst($type);?></td>
                                    <td><a target="_blank" href="<?php echo $invoice_permalink;?>"><?php echo $invoice_id;?></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>                               
                    </table>
                        
                </div>
            </div> 
            <div class="pagination_section">
                <?php
                $total_results = $get_results->rowcount;
                $total_a = floor($total_results/$limit);
                $total_b = $total_results%$limit;
                
                if($total_b > 0){
                    $total_pages = $total_a+1;
                } else {
                    $total_pages = $total_a;
                }
                
                if($total_pages > 1){
                    echo "<ul class='invoice_pagination'>";
                    for($i=1; $i <= $total_pages; $i++) {
                        if($page == $i){
                            echo "<li class='invoice_page active_page'><span>";
                            echo $i;
                            echo "</span></li>";
                        } else {
                            $page_link = $current_page_url."&pagination=".$i;
                            echo "<li class='invoice_page'><a href='".$page_link."'>";
                            echo $i;
                            echo "</a></li>";
                        }
                    }
                    echo "</ul>";
                }
                
                ?>
            </div>
        </div>
    </div>
</div>
