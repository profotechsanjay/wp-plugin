<?php
$openpop = 0; $ms = '';
if(isset($_SESSION['landingmsg']) && $_SESSION['landingmsg'] != ''){
    $openpop = 1; $ms = $_SESSION['landingmsg'];
    unset($_SESSION['landingmsg']);
}
?>
<input type="hidden" name="opnepopoup" id="opnepopoup" value="<?php echo $openpop; ?>" />
<div class="modal fade modalinternallinks" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">Internal Links</h4>
            </div>
            <div class="modal-body">
                <?php
                if (!isset($analys->links->internal_links) || count($analys->links->internal_links) == 0) {
                    echo "<div>No Link Found</div>";
                } else {
                    echo "<ul class='list-group'>";
                    foreach ($analys->links->internal_links as $link) {
                        echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade modalexternallinks" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">External Links</h4>
            </div>
            <div class="modal-body">
                <?php
                if (!isset($analys->links->internal_links) || count($analys->links->internal_links) == 0) {
                    echo "<div>No Link Found</div>";
                } else {
                    echo "<ul class='list-group'>";
                    foreach ($analys->links->external_links as $link) {
                        echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade modalbrokenlinks" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">Broken Links</h4>
            </div>
            <div class="modal-body">
                <?php
                if (!isset($analys->links->broken_links) || count($analys->links->broken_links) == 0) {
                    echo "<div>No Link Found</div>";
                } else {
                    echo "<ul class='list-group'>";
                    foreach ($analys->links->broken_links as $link) {
                        echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade modaltitlelinks" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">Links Without Title Attributes</h4>
            </div>
            <div class="modal-body">
                <?php
                if (!isset($analys->links->no_title) || count($analys->links->no_title) == 0) {
                    echo "<div>No Link Found</div>";
                } else {
                    echo "<ul class='list-group'>";
                    foreach ($analys->links->no_title as $link) {
                        echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade modalrellinks" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">Links Without Rel Attributes</h4>
            </div>
            <div class="modal-body">
                <?php
                if (!isset($analys->links->no_rel) || count($analys->links->no_rel) == 0) {
                    echo "<div>No Link Found</div>";
                } else {
                    echo "<ul class='list-group'>";
                    foreach ($analys->links->no_rel as $link) {
                        echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade modalallimages" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">All Images</h4>
            </div>
            <div class="modal-body">
                <?php
                if (isset($analys->images->all_images) && $analys->images->all_images > 0) {
                    echo "<ul class='list-group'>";
                    foreach ($analys->images->all_images as $link) {
                        echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<div>No Image Found</div>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade modalgrouping" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">Keyword Group</h4>
            </div>
            <div class="modal-body groupdv">
                <div class="urlkeyword">Keyword : <span class="keywordspn"></span></div>
                <div class="aleradyadddpop">
                    <div class="form-group">
                        <label class="control-label">Select Campaign * :</label>
                        <select class="form-control camselecttop" name="campaign_id" id="campaign_id">
                            <option value="">Select Campaign</option>
                            <?php
                            $campaigns = $wpdb->get_results("select id, name from wp_campaigns WHERE location_id = $user_id");
                            foreach($campaigns as $camp){
                                $sel = '';
                                if($campaign == $camp->id){
                                    $sel = 'selected="selected"';
                                }
                                ?>
                                <option <?php echo $sel; ?> value="<?php echo $camp->id; ?>"><?php echo $camp->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Group * : </label>
                        <label><input class="radiogrp" type="radio" name="groupsel" id="groupnew" value="0" /> New Group</label>
                        <label><input class="radiogrp" type="radio" name="groupsel" id="groupext" value="1" /> Existing Group</label>
                    </div>
                    <div class="form-group groupdd hidden">
                        <label class="control-label">Select Group * :</label>
                        <select class="form-control grpselect" name="grpid" id="grpid">                       

                        </select>
                    </div>
                </div>           
                
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-primary addkeywordintool addbtnkeyword" >Add</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade modaladdlanding" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
                <h4 class="modal-title">Add Page To Landing Or Conversion URLs</h4>
            </div>
            <div class="modal-body groupdv">                
                <?php if($openpop == 1){
                    ?>
                    <div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <?php echo $ms; ?></div>
                    <?php
                } ?>
                <div class="urlgrpdiv">Url : <span class="urlspn"><?php echo $CurPageURL; ?></span></div>
                <?php
                $isinconv = 0; $isinland = 0;
                $conv = result_array("SELECT urls_id FROM `global_conversion_urls` where "
                        . "TRIM(BOTH '/' FROM REPLACE(REPLACE(REPLACE (globalPageURL, 'http://', ''),'https://',''),'www.','')) like '$shorturl'"
                        . " AND `MCCUserId` = " . $user_id);
                
                if(!empty($conv)){
                    $isinconv = 1;
                }
                
                if($isinconv == 0){
                    $landing = result_array("SELECT urls_id FROM `global_landing_urls` where "
                        . "TRIM(BOTH '/' FROM REPLACE(REPLACE(REPLACE (landing_url, 'http://', ''),'https://',''),'www.','')) like '$shorturl'"
                        . " AND `MCCUserId` = " . $user_id);                    
                    if(!empty($landing)){
                        $isinland = 1;
                    }
                }                
                if($isinconv == 0 && $isinland == 0){
                    ?>
                    <div><a href='javascript:;' class="btn btn-primary pagetolanding">Add To Landing Page URLs </a></div>
                    <div class="centrgrpdiv"><span>OR</span></div>
                    <div><a href='javascript:;' class="btn btn-primary pagetothank">Add To Thank You Page URLs</a></div>
                    <?php
                }
                else if($isinconv == 1 && $isinland == 0){
                    if($openpop == 0){
                    ?>   
                        <div class="alert alert-danger">URL Already Added As Thank You Page</div>
                    <?php } ?>
                        <div><a href='javascript:;' class="btn btn-primary pagetothankrm">Click Here To Remove</a></div>
                    <?php
                    
                }
                else if($isinconv == 0 && $isinland == 1){
                    if($openpop == 0){
                    ?>   
                        <div class="alert alert-danger">URL Already Added As Landing Page</div>
                    <?php } ?>
                        <div><a href='javascript:;' class="btn btn-primary pagetolandingrm">Click Here To Remove</a></div>
                    <?php
                }
                ?>                                                
            </div>
            <form class="hidden" method="post" id="urladdform" name="urladdform">
                <input type="hidden" name="urltoaddhid" id="urltoaddhid" value="<?php echo $CurPageURL; ?>" />
                <input type="hidden" name="urltotype" id="urltotype" />
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
