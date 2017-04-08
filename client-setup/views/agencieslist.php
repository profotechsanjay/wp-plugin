<style>
    .listagency {
        max-width: 400px;
        margin-bottom: 15px;
        font-weight: 600;
        border: 2px solid #888;
        height: 36px;
    }
</style>
<?php
global $wpdb;
$tbl = $wpdb->prefix."setup_table";
$sql = "SELECT id, client_id, name, db_name, email, url, white_lbl FROM $tbl where status = 1 AND client_id IS NOT NULL";
$list = $wpdb->get_results($sql);
$wrid = current_id();
$typord = isset($_GET['type'])?$_GET['type']:'Ordered';
if(!empty($list)){
    ?>    
    <select class="form-control listagency" name="agencyls" id="agencyls" onchange="agencyorders(this);">
        <option value="">Show Agencies Orders</option>
        <?php
        
        foreach($list as $ls){
            $url = httpurl($ls->url);
            $status = isset($_GET['type'])?$_GET['type']:'all-order';
            $params = array('type'=>'countorders','status'=>$status,'writer_id'=>$wrid);
            $total = dbexecute($ls->db_name,$params);
            if($ls->white_lbl != ''){
                $url = httpurl($ls->white_lbl);
            }
            $sel = '';
            if(isset($_GET['agency']) && intval($_GET['agency']) == $ls->client_id){
                $sel = 'selected="selected"';
            }
            
            ?>
            <option <?php echo $sel; ?> value="<?php echo $ls->client_id; ?>"><?php echo $ls->name; ?> [ <?php echo $url; ?> ] (<?php echo $total; ?>)</option>
            <?php
        }
        ?>
    </select>
    <?php    
}

?>

<script>
   function agencyorders(obj){
       var usid = jQuery(obj).val();
       var url = "<?php echo site_url(); ?>/content-admin/?type=<?php echo $typord; ?>";
       if(usid != ''){
           url = "<?php echo site_url(); ?>/content-admin/?type=<?php echo $typord; ?>&agency="+usid;
       }
       location.href = url;
   }
</script>
    