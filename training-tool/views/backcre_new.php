
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
    .borderddiv strong {
      width: 187px;
      display: inline-block;
      text-align: center;
      margin-left: 42px;
    }
    .borderddiv b {
        width: 315px;
        display: inline-block;

    }
    .smalldt {
        font-size: 12px;
        margin-left: 25px;
        color: #6D6C6C;
    }
    input.high{
    width: 130px;
    margin-left:92px;
    text-align: center;
    }

    input.average{
    width: 130px;
    margin-left:78px;
    text-align: center;
    }

    input.low{
    width: 130px;
    text-align: center;
    }
    .divpullright{
        display: inline;
        font-size: 13px;
        margin-left: 20px;
    }
</style>
<?php
global $wpdb;
$msg = '';
$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
$cls = ''; $attr = '';
if(isset($_REQUEST['id'])){
    $cls = 'disabled readonly';
    $attr = 'disabled = "disabled" readonly = "readonly"';
}
if($id == 0){
    $credt = $wpdb->get_results("SELECT * FROM cre_maxmin_algovals ORDER BY version DESC LIMIT 19");
}
else{
    $credt = $wpdb->get_results("SELECT * FROM cre_maxmin_algovals WHERE version = ".$id);
}
$dt = isset($credt[0]->created_dt)?$credt[0]->created_dt:date('Y-m-d H:i:s');
$credata = '';
if(!empty($credt)){
  foreach ($credt as $key => $cred) {
    $credd[$key]['id']=$cred->id;
    $credd[$key]['title']=$cred->title;
    $credd[$key]['version']=$cred->version;
    $credd[$key]['min']=$cred->min;
    $credd[$key]['max']=$cred->max;
    $credd[$key]['average']=$cred->average;
    $credd[$key]['created_at']=$cred->created_at;
    $credd[$key]['status']=$cred->status;
    $creddd[$key]['title']=$cred->title;
    $creddd[$key]['min']=$cred->min;
    $creddd[$key]['max']=$cred->max;
    $creddd[$key]['average']=$cred->average;
  }
    $credata = $credd;
}
$msg = '';
if(isset($_POST['btnsave']) && $_POST['btnsave'] == 'saveoptionsdata'){
  $data = $_POST['data']; $k = 0;  $jk = 0;
    if($data === $creddd){
        // check if similar to previous array
        $jk = 1;
    }
    $msg = 'Oh!!.. You have not changed anything';
    if($jk == 0){
      $query = $wpdb->get_row("SELECT * FROM cre_maxmin_algovals ORDER BY id DESC LIMIT 19");
      $counter=$query->id;
      $version=($counter/19)+1;
      foreach ($_POST['data'] as $key => $cre) {
      $title=$cre['title'];
      $min=$cre['min'];
      $average=$cre['average'];
      $max=$cre['max'];
      $created_at=date('Y-m-d H:i:s');
      $wpdb->query(
          $wpdb->prepare
          (
              "INSERT INTO cre_maxmin_algovals (title,min,max,average,created_at,version) VALUES(%s,%s,%s,%s,%s,%s)",
            $title,$min,$max,$average,$created_at,$version
          )
      );
      }
      $msg = 'Configuration Saved successfully.';
}
$credata = $data;
}
if(empty($credata)){
  $credata = array(
    "0"=>array(
      'title'=>'PAGE TITLE RANGE',
      'min'=>'60',
      'average'=>'65',
      'max'=>'70',
    ),
    "1"=>array(
      'title'=>'KEYWORD TITLE DENSITY(%)',
      'min'=>'2',
      'average'=>'2.5',
      'max'=>'3',
    ),
    "2"=>array(
      'title'=>'PAGE DESCRIPTION RANGE',
      'min'=>'150',
      'average'=>'165',
      'max'=>'170',
    ),
    "3"=>array(
      'title'=>'KEYWORD DESCRIPTION DENSITY(%)',
      'min'=>'2',
      'average'=>'2.5',
      'max'=>'3',
    ),
    "4"=>array(
      'title'=>'HEADING LENGTH',
      'min'=>'1',
      'average'=>'25',
      'max'=>'50',
    ),
    "5"=>array(
      'title'=>'HEADING TAGS ON PAGE(Equal to)',
      'min'=>'10',
      'average'=>'20',
      'max'=>'30',
    ),
    "6"=>array(
      'title'=>'H1 TAGS ON PAGE(Equal to)',
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "7"=>array(
      'title'=>'PAGE WORDS',
      'min'=>'500',
      'average'=>'700',
      'max'=>'1000',
    ),
    "8"=>array(
      'title'=>'KEYWORD CONTENT DENSITY(%)',
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "9"=>array(
      'title'=>'EXTERNAL LINKS',
      'min'=>'500',
      'average'=>'700',
      'max'=>'1000',
    ),
    "10"=>array(
      'title'=>'PAGE SIZE',
      'min'=>'1',
      'average'=>'8576',
      'max'=>'1048576',
    ),
    "11"=>array(
      'title'=>'LOADING TIME',
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "12"=>array(
      'title'=>'TEXT RATIO',
      'min'=>'10',
      'average'=>'20',
      'max'=>'30',
    ),
    "13"=>array(
      'title'=>'Bounce Rate (%)',
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "14"=>array(
      'title'=>'TIME ON SITE',
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "15"=>array(
      'title'=>"TITLE RELEVANCY(More than equal to %)",
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "16"=>array(
      'title'=>"META DESC RELEVANCY(More than equal to %)",
      'min'=>'10',
      'average'=>'20',
      'max'=>'30',
    ),
    "17"=>array(
      'title'=>"OVERALL KEYWORD DENSITY(More than equal to %)",
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    ),
    "18"=>array(
      'title'=>"OVERALL KEYWORD DENSITY (PRIMARY + SYNONYM)(More than equal to %)",
      'min'=>'1',
      'average'=>'2',
      'max'=>'3',
    )
  );
}
$versionquery = $wpdb->get_results("SELECT DISTINCT(version) FROM cre_maxmin_algovals ORDER BY version DESC LIMIT 5000");
?>


<div class="content_header">
    <h2>Content Recommendation Engine
    <div class="divpullright">
        Version :
        <select id="versionselect">
            <?php
            $dataid = 0; $firstid = 0;

            foreach($versionquery as $creald){
                if($dataid == 0){
                    $firstid = $creald->version;
                }
                $txt = '';
                if($id == $creald->version){
                    $txt = 'selected="selected"';
                }
                ?>
                <option <?php echo $txt; ?> value="<?php echo $creald->version; ?>"><?php echo $creald->version; ?></option>
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
              <strong></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span><strong>Low</strong></span>
            <span><strong>High</strong></span>
              <span><strong>Average</strong></span>
            </div>
            <?php foreach($credata as $key=>$cre){?>
              <div class="borderddiv">
                  <b><?php echo $cre['title'];?>
                  <input type="hidden" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="data[<?php echo $key;?>][title]" value="<?php echo $cre['title']; ?>" >
                    </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="data[<?php echo $key;?>][min]" value="<?php echo $cre['min']; ?>" ></span> &nbsp;&nbsp;
                  <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="data[<?php echo $key;?>][max]" value="<?php echo $cre['max']; ?>" ></span>&nbsp;&nbsp;
                  <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="data[<?php echo $key;?>][average]" value="<?php echo $cre['average']; ?>" ></span>
              </div>
          <?php  }?>
<!--
            <div class="borderddiv">
                <b><?php echo $cre['keydens'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minkeydens" value="<?php echo $cre['minkeydens']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxkeydens" value="<?php echo $cre['maxkeydens']; ?>" ></span>&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagekeydens" value="<?php echo $cre['averagekeydens']; ?>" ></span>
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['rangedesc'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minrangedesc" value="<?php echo $cre['minrangedesc']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxrangedesc" value="<?php echo $cre['maxrangedesc']; ?>" ></span>&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagerangedesc" value="<?php echo $cre['averagerangedesc']; ?>" ></span>
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['keyedesc'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minkeyedesc" value="<?php echo $cre['minkeyedesc']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxkeyedesc" value="<?php echo $cre['maxkeyedesc']; ?>" ></span>&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagekeyedesc" value="<?php echo $cre['averagekeyedesc']; ?>" ></span>
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['headlength'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minheadlength" value="<?php echo $cre['minheadlength']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxheadlength" value="<?php echo $cre['maxheadlength']; ?>" ></span>&nbsp;&nbsp;
                  <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averageheadlength" value="<?php echo $cre['averageheadlength']; ?>" ></span>
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['htags'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minhtags" value="<?php echo $cre['minhtags']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxhtags" value="<?php echo $cre['maxhtags']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagehtags" value="<?php echo $cre['averagehtags']; ?>" > </span> &nbsp;&nbsp;
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['h1tags'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minh1tags" value="<?php echo $cre['minh1tags']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxh1tags" value="<?php echo $cre['maxh1tags']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averageh1tags" value="<?php echo $cre['averageh1tags']; ?>" > </span> &nbsp;&nbsp;
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['contentrange'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="mincontentrange" value="<?php echo $cre['mincontentrange']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxcontentrange" value="<?php echo $cre['maxcontentrange']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagecontentrange" value="<?php echo $cre['averagecontentrange']; ?>" ></span>
          </div>

            <div class="borderddiv">
                <b><?php echo $cre['contentdensity'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="mincontentdensity" value="<?php echo $cre['mincontentdensity']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxcontentdensity" value="<?php echo $cre['maxcontentdensity']; ?>" ></span>&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagecontentdensity" value="<?php echo $cre['averagecontentdensity']; ?>" ></span>&nbsp;&nbsp;
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['extlinks'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minextlinks" value="<?php echo $cre['minextlinks']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxextlinks" value="<?php echo $cre['maxextlinks']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averageextlinks" value="<?php echo $cre['averageextlinks']; ?>" ></span> &nbsp;&nbsp;
            </div>
            <div class="borderddiv">
                <b><?php echo $cre['pagesize'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="numbloadtimeer" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minpagesize" value="<?php echo $cre['minpagesize']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxpagesize" value="<?php echo $cre['maxpagesize']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagepagesize" value="<?php echo $cre['averagepagesize']; ?>" ></span>
            </div>
            <div class="borderddiv">
                <b><?php echo $cre['loadtime'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minloadtime" value="<?php echo $cre['minloadtime']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxloadtime" value="<?php echo $cre['maxloadtime']; ?>" ></span>&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averageloadtime" value="<?php echo $cre['averageloadtime']; ?>" ></span>&nbsp;&nbsp;
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['textratio'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="mintextratio" value="<?php echo $cre['mintextratio']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxtextratio" value="<?php echo $cre['maxtextratio']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagetextratio" value="<?php echo $cre['averagetextratio']; ?>" ></span> &nbsp;&nbsp;

            </div>

          <div class="borderddiv">
                <b><?php echo $cre['bounce'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minbounce" value="<?php echo $cre['minbounce']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxbounce" value="<?php echo $cre['maxbounce']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagebounce" value="<?php echo $cre['averagebounce']; ?>" > </span> &nbsp;&nbsp;
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['timeonsite'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="mintimeonsite" value="<?php echo $cre['mintimeonsite']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxtimeonsite" value="<?php echo $cre['maxtimeonsite']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagetimeonsite" value="<?php echo $cre['averagetimeonsite']; ?>" > </span> &nbsp;&nbsp;
            </div>

           <div class="borderddiv">
                <b><?php echo $cre['titlerelevancy'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="mintitlerelevancy" value="<?php echo $cre['mintitlerelevancy']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxtitlerelevancy" value="<?php echo $cre['maxtitlerelevancy']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagetitlerelevancy" value="<?php echo $cre['averagetitlerelevancy']; ?>" ></span> &nbsp;&nbsp;
          </div>

            <div class="borderddiv">
                <b><?php echo $cre['descrelevancy'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="mindescrelevancy" value="<?php echo $cre['mindescrelevancy']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxdescrelevancy" value="<?php echo $cre['maxdescrelevancy']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averagedescrelevancy" value="<?php echo $cre['averagedescrelevancy']; ?>" ></span> &nbsp;&nbsp;
            </div>

            <div class="borderddiv">
                <b><?php echo $cre['overdens'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minoverdens" value="<?php echo $cre['minoverdens']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxoverdens" value="<?php echo $cre['maxoverdens']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averageoverdens" value="<?php echo $cre['averageoverdens']; ?>" ></span> &nbsp;&nbsp;
              </div>

            <div class="borderddiv">
                <b><?php echo $cre['primarydens'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='low <?php echo $cls; ?>' name="minprimarydens" value="<?php echo $cre['minprimarydens']; ?>" ></span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='high <?php echo $cls; ?>' name="maxprimarydens" value="<?php echo $cre['maxprimarydens']; ?>" > </span> &nbsp;&nbsp;
                <span><input type="number" <?php echo $attr; ?> class='average <?php echo $cls; ?>' name="averageprimarydens" value="<?php echo $cre['averageprimarydens']; ?>" > </span> &nbsp;&nbsp;
            </div>
-->

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
        var url = '<?php echo site_url(); ?>/wp-admin/admin.php?page=cre_options_new_page&id='+id;
        if(id == jQuery("#firstid").val()){
            url = '<?php echo site_url(); ?>/wp-admin/admin.php?page=cre_options_new_page';
        }
        window.location.href = url;
    });
</script>
