<?php

include_once 'common.php';
global $wpdb;

function adttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}
$base_url = site_url();
$results = $wpdb->get_results
(
        $wpdb->prepare
        (
                "SELECT * FROM ". setup_table() . " ORDER BY created_dt DESC ",""
        )
);
 

?>
<div class="contaninerinner">     
    <h4>Client Setups
    <div class="pull-right"> 
        <a href="admin.php?page=client_setups&deployment" class="btn btn-primary margin-bottom-10">Deployment</a>
    </div>    
    </h4>
    <div class="clearfix"></div>
    <div class="panel panel-primary">
        <div class="pull-right">                        
            <a class="btn btn-danger rebuildhtaccess" href="javascript:;">Re-Build Root Htaccess</a>
            <a class="btn btn-success" href="admin.php?page=new_setup">Create New Setup</a>
        </div>        
        <div class="panel-heading">Setup List</div>
        <div class="panel-body">
           
            <table class="table table-bordered table-striped table-hover" id="data_announcement" >
                <thead>
                    <tr>                        
                        <th style="width: 17%;">Client</th>
                        <th style="width: 15%;">URL</th>
                        <th style="width: 15%;">DIR</th>                        
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Created On</th>
                        <th style="width: 23%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                   <?php
                   
                   foreach($results as $result){                                              
                       
                       $uniq_key = md5(mt_rand(99999999, 99999999999999).$result->name);                       
                       $url = adttp($result->url);
                       if(trim($result->white_lbl) != ''){
                           $url = adttp($result->white_lbl);
                       }
                       
                       $sts = $result->status == 1?'<div class="alert alert-success">Enabled</div>':'<div class="alert alert-danger">Disabled</div>';
                       $option = 'client_setup_'.$result->id;
                       if(get_option($option) == 0){
                           $sts = '<div class="alert alert-warning">In-Progress</div>';
                       }
                       
//                        $setup = $result;
//                        $select_crons=$wpdb->get_results("SELECT cc.agency_id,cc.cron_link,cc.cron_type,ts.cron_time FROM `client_crons` as cc JOIN `master_crons` as ts  ON cc.cron_type=ts.cron_type WHERE cc.agency_id=".$setup->id);
//                        $loop_select_crons=$select_crons;
//                        $setup_crons = count($select_crons);
//                        if($setup_crons==0){
//                        $currentdate = $setup->created_dt;
//                        // default Crons from table not from file
//                        $datas = $wpdb->get_results($wpdb->prepare("SELECT default_cron,cron_time,cron_type FROM master_crons where status=1",""));   //master "cron time" table
//                          foreach ($datas as $key => $data) {
//                              $data->default_cron = str_replace("{{base_dir}}", $setup->dir, $data->default_cron);
//                              $data->default_cron = str_replace("{{analytics_dir}}", $setup->analytic_dir, $data->default_cron);
//                              $data->default_cron = str_replace("{{analytic_url}}", PROTOCOL . $setup->analytic_url, $data->default_cron);
//                              $data->default_cron = str_replace("{{url}}", $url, $data->default_cron);
//                              $data->default_cron = str_replace("{{key}}", md5($setup->db_name), $data->default_cron);
//                              $datajob[$data->cron_type]=$data;
//                          }
//                          foreach ($datajob as $key_val => $job) {
//                              $insert_crons = $wpdb->query("INSERT INTO `client_crons`(`agency_id`, `cron_link`, `cron_type`,`order_by`, `created`) VALUES ('".$setup->id."','".$job->default_cron."','".$job->cron_type."','".$setup->id."','" . $currentdate . "')");
//                              }
//                        }
                       
                       
                       ?>
                        <tr>
                            <td>
                                <div><?php echo $result->name; ?></div>
                                <div><a target="_blank" class="adminlogin" href="<?php echo admin_url('admin-ajax.php').'?action=login&client='. $result->id; ?>">Login as administrator</a></div>
                            </td>
                            <td><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></td>
                            <td><?php echo $result->dir; ?></td>
                            <td class="statustd" data-id="<?php echo $result->id; ?>"><?php echo $sts; ?></td>
                            <td><?php echo $result->created_dt; ?></td>
                            <td>
                                <a href="admin.php?page=new_setup&setup_id=<?php echo $result->id; ?>" class="btn btn-primary margin-bottom-10">Configure</a>
                                <?php if($result->status == 1){
                                    ?>
                                        <a href="javascript:;" data-id="<?php echo $result->id; ?>" data-sts="Disable" data-attr="0" class="btn btn-warning margin-bottom-10 statussetup">Disable</a>
                                    <?php
                                } else {
                                    
                                    ?>
                                        <a href="javascript:;" data-id="<?php echo $result->id; ?>" data-attr="1" data-sts="Enable" class="btn btn-success margin-bottom-10 statussetup">Enable</a>
                                    <?php
                                } ?>
                            </td>
                        </tr>
                        <?php
                   }
                   
                   ?>
                </tbody>
            </table>

        </div>

    </div>


</div>
