<?php 
     login_check(); 
    /* All CSS files are enqueue from Here ! */
    //wp_enqueue_style('cs1-bootstrap.min.css', CA_COUNT_PLUGIN_URL . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('cs1-sweetalert.css', CA_COUNT_PLUGIN_URL . '/assets/css/sweetalert.css');
    wp_enqueue_style('cs1-datatable.css', CA_COUNT_PLUGIN_URL . '/assets/css/jquery.dataTables.min.css');
    wp_enqueue_style('cs1-style.css', CA_COUNT_PLUGIN_URL . '/assets/css/setup-style.css');
    /* Script */
    wp_enqueue_script('cs1-validate.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/jquery.validate.min.js');
    //wp_enqueue_script('cs1-bootstrap.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/bootstrap.min.js');
    wp_enqueue_script('cs1-sweetalert.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/sweetalert.min.js');
    wp_enqueue_script('cs1-datatable.min.js', CA_COUNT_PLUGIN_URL . '/assets/js/jquery.dataTables.min.js');
    wp_enqueue_script('cs1-script.js', CA_COUNT_PLUGIN_URL . '/assets/js/setup-script.js');
?>
<style>
.btn-primary:hover,.btn-primary:focus {
    background-color: #337ab7 !important;
    border-color: #2e6da4 !important;
    color: #fff !important;
}
.btn.btn-primary.btn-button:hover,.btn.btn-primary.btn-button:active,.btn.btn-primary.btn-button:focus{
 background-color: #337ab7 !important;
    border-color: #2e6da4 !important;   
    color: #fff !important;
    background-image:none;
}

.radius-4{border-radius: 4px !important;
    padding: 5px 10px !important;
    font-size: 12px !important;}



 .page-footer{position: fixed;
    
    bottom: 0; left:0; right:0;}


html{overflow-y: auto;}

html,body{height: 100%;}


</style>
<div class="container-fluid pad_15">

<div class="card block">
    
<div class="block-header pad_t_l_r">
    <h2>Custom <span class="bold">Agency Setup</span></h2>

</div>
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#leads">Leads</a></li>
        <li><a data-toggle="tab" href="#created">Created Agency</a></li>
        <li><a data-toggle="tab" href="#rejected">Rejected</a></li>
        <li class="pull-right create_setup">
            <button type="button" class="btn btn-primary btn-button radius-4" id="btn-button" data-toggle="modal" data-target="#createDetails">Create Agency Request</button>
         </li>
    </ul>

    <div class="tab-content">
        <div id="leads" class="tab-pane fade in active">
            
             <?php include_once CA_COUNT_PLUGIN_DIR.'/views/leads.php'; ?> 
        </div>
        <div id="created" class="tab-pane fade">
           
            <?php include_once CA_COUNT_PLUGIN_DIR.'/views/created-setups.php'; ?> 
        </div>
        <div id="rejected" class="tab-pane fade">
           
            <?php include_once CA_COUNT_PLUGIN_DIR.'/views/rejected-setups.php'; ?>
        </div>
    </div>
    <?php include_once CA_COUNT_PLUGIN_DIR.'/views/cs-footer.php'; ?>


<div class="clear_block"></div>

</div>

</div>
