<?php
include_once 'common.php';
global $wpdb;

$resource_id = isset($_REQUEST['resource_id'])?intval($_REQUEST['resource_id']):0;
$resource = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . resources()." WHERE id = %d", $resource_id
        )
);

if(empty($resource)){
    die('Invalid Exercise');
}

$lesson_id = $resource->lesson_id;

$lesson = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . lessons()." WHERE id = %d", $lesson_id
        )
);



$videos = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . media()." WHERE resource_id = %d AND type = 'video' ORDER BY created_dt DESC", $resource_id
        )
);

$docs = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . media()." WHERE resource_id = %d AND type = 'document'", $resource_id
        )
);

$helplinks = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . media()." WHERE resource_id = %d AND type = 'link'", $resource_id
        )
);

$notes = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . lesson_notes()." WHERE resource_id = %d", $resource_id
        )
);


$module = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . modules()." WHERE id = %d", $lesson->module_id
        )
);

$module_id = $module->id;
$course = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . courses()." WHERE id = %d", $module->course_id
        )
);


$base_url = site_url();
$slug = PAGE_SLUG;

$vidaddtxt = "Add Video";
if(!empty($videos)){
            $vidaddtxt = "Edit Video";
        }

?>
<div class="contaninerinner">       
    <h4>Exercise - <?php echo $resource->title; ?> [ Manage Resources ]
    <a href="admin.php?page=lesson_detail&lesson_id=<?php echo $lesson_id; ?>" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a>
    </h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Courses List">
                <a href="admin.php?page=triningtool">All Courses</a> >>
            </li>
            <li title="Course">
                <a href="admin.php?page=course_detail&course_id=<?php echo $course->id; ?>"><?php echo $course->title; ?></a> >>
            </li>
            <li title="Module">
                <a href="admin.php?page=module_detail&module_id=<?php echo $module->id; ?>"><?php echo $module->title; ?></a> >>
            </li>
            <li title="Lesson">
                <a href="admin.php?page=lesson_detail&lesson_id=<?php echo $lesson->id; ?>"><?php echo $lesson->title; ?></a> >>                 
            </li>
            <li title="Exercise">
                <?php echo $resource->title; ?>
            </li>
        </ul>
    </div>
    <div class="clearfix"></div>
    <div class="alert alert-info">
        <strong>Frontend URL: <a target='_blank' href="<?php echo $base_url.'/'.$slug."/?exercise_detail=".$resource_id; ?>"><?php echo $base_url.'/'.$slug."/?exercise_detail=".$resource_id; ?></a></strong>
    </div>
        <input type="hidden" id="typematerial" name="typematerial" value="resource" />
            <div class="panel panel-primary">
                <div class="pull-right">
                    <a class="btn btn-success" onclick="openvideodialog(); " href="javascript:;"><?php echo $vidaddtxt; ?></a>                    
                </div>
                <div class="panel-heading">Video

                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="videospace col-lg-12">
                            <?php

                            if(empty($videos)){
                                echo "Video Not Added";
                            }
                            else{
                                ?>
                                <div class="videotxt" style="display: none; visibility: hidden;"><?php echo $videos->path; ?></div>
                                <?php
                                echo html_entity_decode($videos->path);
                            }
                            ?>
                        </div>
                        
                    </div>
                </div>

            </div>            
          
    
    <div class="panel panel-primary">
        <div class="pull-right">
            <a class="btn btn-success" onclick="reset_form(); open_modal('note_dialog');" href="javascript:;">Create New Note</a>                    
        </div>
        <div class="panel-heading">Notes

        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover" id="data_notes" >
                <thead>
                    <tr>                                
                        <th style="width: 5%;">SNo</th>
                        <th style="width: 40%;">Note</th>
                        <th style="width: 20%;">Date</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $f = 0;
                    foreach ($notes as $note) {       
                        $f++;
                        ?>
                            <tr class="rowmodnote" data-id="<?php echo $note->id; ?>">                                        
                                <td><?php echo $f; ?></td>                            
                                <td class="title">  
                                    <div style="display: none; visibility: hidden" class="notetext"><?php echo html_entity_decode($note->note); ?></div>
                                    <?php echo limit_text(html_entity_decode($note->note),10,false); ?>
                                </td>     
                                <td><?php echo date("Y-m-d",  strtotime($note->created_dt)); ?></td>                            
                                <td class="actiontd">                                            
                                    <a href="javascript:;" data-id="<?php echo $note->id; ?>" class="editnote btn btn-primary" title="Edit Note">Edit</a>
                                    <a href="javascript:;" data-id="<?php echo $note->id; ?>" class="deletenote btn btn-danger" title="Delete Note">Delete</a>
                                </td>
                            </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
             
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-primary">
                    <div class="pull-right">
                        <a class="btn btn-success" onclick="reset_form(); open_modal('help_dialog');" href="javascript:;">Add Help Link</a>                    
                    </div>
                    <div class="panel-heading">Help Links

                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-hover" id="data_links" >
                            <thead>
                                <tr>                                
                                    <th style="width: 5%;">SNo</th>
                                    <th style="width: 40%;">Link</th>                                    
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $f=0;
                                foreach ($helplinks as $helplink) {
                                   $f++;
                                    ?>
                                        
                                        <tr class="rowmodlink" data-id="<?php echo $helplink->id; ?>">                                        
                                            <td><?php echo $f; ?></td>    
                                            <td class="title" data-link="<?php echo $helplink->path; ?>" data-title="<?php echo $helplink->extra_info; ?>"> 
                                                <a target="_blank" href="<?php echo $helplink->path; ?>"><?php echo $helplink->extra_info; ?></a>
                                            </td>                                                
                                            <td class="actiontd">        
                                                <a href="javascript:;" data-id="<?php echo $helplink->id; ?>" class="editlink btn btn-primary" title="Edit Link">Edit</a>
                                                <a href="javascript:;" data-id="<?php echo $helplink->id; ?>" class="deletelink btn btn-danger" title="Delete Link">Delete</a>
                                                
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
            <div class="col-lg-6">
                <div class="panel panel-primary">
                    <div class="pull-right">
                        <a class="btn btn-success" onclick="reset_form(); open_modal('download_dialog');" href="javascript:;">Upload New Document</a>                    
                    </div>
                    <div class="panel-heading">Document & Files</div>
                    <div class="panel-body">

                        <table class="table table-bordered table-striped table-hover" id="data_docs" >
                            <thead>
                                <tr>                                
                                    <th style="width: 5%;">SNo</th>
                                    <th style="width: 40%;">Path</th>                                    
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $f=0;
                                foreach ($docs as $doc) {
                                   $f++;
                                    ?>
                                        
                                        <tr class="rowmoddoc" data-id="<?php echo $doc->id; ?>">                                        
                                            <td><?php echo $f; ?></td>    
                                            <td class="title"> 
                                                <a download href="<?php echo TR_COUNT_PLUGIN_URL."/".$doc->path; ?>"><?php echo $doc->extra_info; ?></a>
                                            </td>                                                
                                            <td class="actiontd">                                            
                                                <a href="javascript:;" data-id="<?php echo $doc->id; ?>" class="deletedoc btn btn-danger" title="Delete Document">Delete</a>
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
        </div>
    
    
</div>


<div id="video_dialog" class="modal fade modealrealodonclose">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Video</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="addvideo" name="addvideo" class="form-horizontal">
                                        
                    <input type="hidden" id="resource_id" name="resource_id" value="<?php echo $resource->id; ?>" />
                    
                    
                    <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Embed Code * :</label>
                        <div class="col-lg-8">
                            <textarea rows="8" type="text" required class="form-control" id="embedcode" name="embedcode" placeholder="Embed Your Code Here"></textarea>
                            <small><i>Height should be 500 px. Remove width from embede code, it automatically get width.
                                </i>
                                For eg: 
                                <div><code><?php  echo htmlspecialchars('<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script><div class="wistia_embed wistia_async_j38ihh83m5" style="height:500px;"></div>'); ?></code></div>

                            </small>
                        </div>
                    </div>
                    
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick='jQuery("#addvideo").submit();' class="btnupdt btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div id="download_dialog" class="modal fade modealrealodonclose">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Upload Documents & Files</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="adddoc" name="adddoc" class="form-horizontal">
                                        
                    <input type="hidden" id="resourceid" name="resourceid" value="<?php echo $resource->id; ?>" />

                    <div class="form-group col-lg-12">
                        <label for="title" class="control-label">Upload Document (Press CTRL key For Multiple Documents) * :</label>                        
                    </div>
                    <div class="form-group col-lg-12">                        
                        <div class="">
                            <input type="file" class="form-control" name="responsedoc[]" id="responsedoc" multiple="true" />
                        </div>
                        
                    </div>
                    <div class="clear"></div>
                    <ul id="fileList" class="list-group"><li class="list-group-item">No Files Selected</li></ul>
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick='uploaddocs();' class="btn btn-primary" >Upload</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="note_dialog" class="modal fade modealrealodonclose">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Notes</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="addnote" name="addnote" class="form-horizontal">
                                        
                    <input type="hidden" id="resourid" name="resourid" value="<?php echo $resource_id; ?>" />
                    <input type="hidden" id="noteid" name="noteid" value="0" />
                    <div class="form-group wpeditor">   
                        <label for="title" class="col-lg-2 control-label">Enter Note * :</label>      
                        <div class="col-lg-8">
                        
                            <?php wp_editor("", $id = 'descriptionnote', $prev_id = 'title', $media_buttons = false, $tab_index = 1); ?>
                        </div>
                        
                    </div>                    
                    
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick='jQuery("#addnote").submit();' class="btnupdt btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div id="help_dialog" class="modal fade modealrealodonclose">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Help Link</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="addhlink" name="addhlink" class="form-horizontal">
                                        
                    <input type="hidden" id="resid" name="resid" value="<?php echo $resource_id; ?>" />
                    <input type="hidden" id="helpnkid" name="helpnkid" value="0" />
                    <div class="form-group">   
                        <label for="title" class="col-lg-2 control-label">Title * :</label>      
                        <div class="col-lg-8">
                            <input type="text" required class="form-control" id="linktitle" name="linktitle" placeholder="Enter Link Title" />
                        </div>
                        
                    </div> 
                    <div class="form-group">   
                        <label for="title" class="col-lg-2 control-label">Link * :</label>      
                        <div class="col-lg-8">
                            <input type="text" required url='true' class="form-control" id="linkurl" name="linkurl" placeholder="Enter Link Here" />
                        </div>
                        
                    </div> 
                    
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick='jQuery("#addhlink").submit();' class="btnupdt btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>
