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

<div>

        <div class="tab-content">
          <?php include_once CA_COUNT_PLUGIN_DIR . '/views/created-setups.php'; ?> 
        </div>
        <?php include_once CA_COUNT_PLUGIN_DIR . '/views/cs-footer.php'; ?>

</div>
