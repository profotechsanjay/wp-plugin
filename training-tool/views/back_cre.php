<style>
    .content_header{
        margin-top: 25px;
    }
    .borderddiv:first-child{
        margin-top: 35px;
    }
    .borderddiv{
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
            margin-top: 15px;
    }
    .borderddiv b{
        width: 350px;
        display: inline-block;
    }
    .smalldt {
        font-size: 12px;
        margin-left: 25px;
        color: #6D6C6C;
    }
    .divpullright{
        display: inline;
        font-size: 13px;
        margin-left: 20px;
    }
</style>
<?php
global $wpdb;
//$cre = get_option('credata');
$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;

$cls = ''; $attr = '';

if(isset($_REQUEST['id'])){
    $cls = 'disabled readonly';
    $attr = 'disabled = "disabled" readonly = "readonly"';
}

$crealldt = $wpdb->get_results("SELECT id FROM cre_algovals ORDER BY id DESC LIMIT 5000");

if($id == 0){
    $credt = $wpdb->get_row("SELECT id, credata, created_dt FROM cre_algovals ORDER BY id DESC LIMIT 1");
}
else{
    $credt = $wpdb->get_row("SELECT id, credata, created_dt FROM cre_algovals WHERE id = ".$id);
}
$dt = isset($credt->created_dt)?$credt->created_dt:date('Y-m-d H:i:s');
$cre = array();
if(!empty($credt)){
    $cre = (array) json_decode($credt->credata);
} 

$msg = '';

if(isset($_POST['btnsave']) && $_POST['btnsave'] == 'saveoptionsdata'){
    
    $data = $_POST; $k = 0;  $jk = 0;
    if($data === $cre){
        // check if similar to previous array
        $jk = 1;
    }
    $msg = 'Oh!!.. You have not changed anything';
    if($jk == 0){
        $wpdb->query(

            $wpdb->prepare
            (
                "INSERT INTO cre_algovals (credata) VALUES(%s)",				
                json_encode($data)
            ) 
        );
        $msg = 'Configuration Saved successfully.';
    }    
    $cre = $data;
    
    
}

if(empty($cre)){
    $cre = array(
        "minrangetite" => 60,
        "maxrangetite" => 70,
        "minkeydens" => 2,
        "maxkeydens" => 3,
        "minrangedesc" => 150,
        "maxrangedesc" => 160,
        "minkeyedesc" => 2,
        "maxkeyedesc" => 3,
        "minheadlength" => 1,
        "maxheadlength" => 50,
        "mincontentrange" => 500,        
        "mincontentdensity" => 2,
        "maxcontentdensity" => 3,
        "minextlinks" => 1,
        "maxextlinks" => 6,
        "minpagesize" => 1,
        "maxpagesize" => 1048576,
        "minloadtime" => 0,
        "maxloadtime" => 2,
        "titlerelevancy" => 1,
        "descrelevancy" => 1,
        "mintextratio" => 10,
        "maxtextratio" => 20,
        "maxoverdens" => 3,
        "maxprimarydens" => 5,
        "maxhtags" => 30,
        "maxh1tags" => 1        
    );
}


?>
<div class="content_header">
    <h2>Content Recommendation Engine 
    <div class="divpullright">
        Version : 
        <select id="versionselect">
            <?php 
            $dataid = 0; $firstid = 0;
            foreach($crealldt as $creald){
                if($dataid == 0){
                    $firstid = $creald->id;
                }
                $txt = '';
                if($id == $creald->id){
                    $txt = 'selected="selected"';
                }
                ?>
                <option <?php echo $txt; ?> value="<?php echo $creald->id; ?>"><?php echo $creald->id; ?></option>
                <?php
                $dataid++;
            }
            ?>
        </select>
        <input type="hidden" name="selectedid" id="selectedid" value="<?php echo $id; ?>" />
        <input type="hidden" name="firstid" id="firstid" value="<?php echo $firstid; ?>" />
        <span class="smalldt"> Created Date : <?php echo $dt; ?></span>
    </div>
    </h2>
    <hr/>
    <div class="content">
        <?php
        if($msg != ''){
            ?>
            <div class="updated"> <p><?php echo $msg; ?></p> </div>
            <?php
        }
        ?>
        <form method="post" action="#">
            <div class="borderddiv">
                <b>PAGE TITLE RANGE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minrangetite" value="<?php echo $cre['minrangetite']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxrangetite" value="<?php echo $cre['maxrangetite']; ?>" ></span>
                
            </div>
            <div class="borderddiv">
                <b>KEYWORD TITLE DENSITY</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minkeydens" value="<?php echo $cre['minkeydens']; ?>" > %</span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxkeydens" value="<?php echo $cre['maxkeydens']; ?>" > %</span>
                
                
            </div>
            
            <div class="borderddiv">
                <b>PAGE DESCRIPTION RANGE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minrangedesc" value="<?php echo $cre['minrangedesc']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxrangedesc" value="<?php echo $cre['maxrangedesc']; ?>" ></span>
                
            </div>
            
            <div class="borderddiv">
                <b>KEYWORD DESCRIPTION DENSITY</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                

                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minkeyedesc" value="<?php echo $cre['minkeyedesc']; ?>" > %</span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxkeyedesc" value="<?php echo $cre['maxkeyedesc']; ?>" > %</span>
                
            </div>
            
            <div class="borderddiv">
                <b>HEADING LENGTH</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minheadlength" value="<?php echo $cre['minheadlength']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxheadlength" value="<?php echo $cre['maxheadlength']; ?>" ></span>
                
            </div>
            
            <div class="borderddiv">
                <b>MAX HEADING TAGS ON PAGE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span> Equal to <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxhtags" value="<?php echo $cre['maxhtags']; ?>" > </span> &nbsp;&nbsp;                                
            </div>     
            
            <div class="borderddiv">
                <b>MAX H1 TAGS ON PAGE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span> Equal to <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxh1tags" value="<?php echo $cre['maxh1tags']; ?>" > </span> &nbsp;&nbsp;                                
            </div>            
            
            <div class="borderddiv">
                <b>MINIMUM PAGE WORDS</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="mincontentrange" value="<?php echo $cre['mincontentrange']; ?>" ></span>                
            </div>
            
            <div class="borderddiv">
                <b>KEYWORD CONTENT DENSITY</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="mincontentdensity" value="<?php echo $cre['mincontentdensity']; ?>" > %</span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxcontentdensity" value="<?php echo $cre['maxcontentdensity']; ?>" > %</span>                
                
            </div>
            
            <div class="borderddiv">
                <b>EXTERNAL LINKS</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minextlinks" value="<?php echo $cre['minextlinks']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxextlinks" value="<?php echo $cre['maxextlinks']; ?>" ></span>
                
            </div>
            <div class="borderddiv">
                <b>PAGE SIZE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minpagesize" value="<?php echo $cre['minpagesize']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxpagesize" value="<?php echo $cre['maxpagesize']; ?>" ></span>
                
            </div>
            <div class="borderddiv">
                <b>LOADING TIME</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="minloadtime" value="<?php echo $cre['minloadtime']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxloadtime" value="<?php echo $cre['maxloadtime']; ?>" ></span>
                
            </div>
            
            <div class="borderddiv">
                <b>TEXT RATIO</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>Min: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="mintextratio" value="<?php echo $cre['mintextratio']; ?>" ></span> &nbsp;&nbsp;
                <span>Max: <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxtextratio" value="<?php echo $cre['maxtextratio']; ?>" ></span>
                %
            </div>
            
           <div class="borderddiv">
                <b>TITLE RELEVANCY</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span> More than equal to<input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="titlerelevancy" value="<?php echo $cre['titlerelevancy']; ?>" > %</span> &nbsp;&nbsp;                                
            </div>
            
            <div class="borderddiv">
                <b>META DESC RELEVANCY</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span> More than equal to<input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="descrelevancy" value="<?php echo $cre['descrelevancy']; ?>" > %</span> &nbsp;&nbsp;                                
            </div>
                        
            <div class="borderddiv">
                <b>OVERALL KEYWORD DENSITY</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span> More than equal to <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxoverdens" value="<?php echo $cre['maxoverdens']; ?>" > %</span> &nbsp;&nbsp;                                
            </div>            
            
            <div class="borderddiv">
                <b>OVERALL KEYWORD DENSITY (PRIMARY + SYNONYM)</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span> More than equal to <input type="number" <?php echo $attr; ?> class='<?php echo $cls; ?>' name="maxprimarydens" value="<?php echo $cre['maxprimarydens']; ?>" > %</span> &nbsp;&nbsp;                                
            </div>
            
            <br/>           
            <?php if(!isset($_REQUEST['id'])): ?>
            <div class="fl-right">
                <button type="submit" value="saveoptionsdata" class="button button-primary button-large" name="btnsave" >Save & Create New Version</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<script>
    jQuery(document).on("change","#versionselect",function(){
        var id = jQuery(this).val();
        var url = '<?php echo site_url(); ?>/wp-admin/admin.php?page=cre_options_page&id='+id;
        if(id == jQuery("#firstid").val()){
            url = '<?php echo site_url(); ?>/wp-admin/admin.php?page=cre_options_page';
        }
        window.location.href = url;
    });
</script>


