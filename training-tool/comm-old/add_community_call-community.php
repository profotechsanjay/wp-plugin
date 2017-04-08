<?php
include_once 'common.php';
global $wpdb;
	global $current_user;
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
$lesson_id = isset($_REQUEST['lesson_id'])?intval($_REQUEST['lesson_id']):0;
$course_id= isset($_REQUEST['course_id'])?intval($_REQUEST['course_id']):0;

$lesson = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . lessons()." WHERE id = %d", $lesson_id
        )
);

if(empty($lesson)){
    //die('Invalid Lesson');
}

$video = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . media()." WHERE lesson_id = %d AND type = 'video' ORDER BY created_dt DESC", $lesson_id
        )
);

/*custom code to bring community call video*/
$videos = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . community_call()." WHERE course_id = %d AND type = 'video' ORDER BY created_dt DESC limit 1", $course_id
        )
);


$docs = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . media()." WHERE lesson_id = %d AND type = 'document'", $lesson_id
        )
);

$docs = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . community_call()." WHERE course_id = %d ", $course_id
        )
);

$helplinks = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . community_call()." WHERE course_id = %d", $course_id
        )
);

$notes = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . community_call()." WHERE course_id = %d", $course_id
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

$resources = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . resources()." WHERE lesson_id = %d ORDER BY ord ASC", $lesson_id
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
    <h4>Add Community Call - <?php echo $lesson->title; ?>     
    <a href="admin.php?page=triningtool" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a>
    </h4>
    <div class="bread_crumb">
        <ul>
            <li title="All Courses List">
                <a href="admin.php?page=triningtool">All Courses</a> >> Community Call
			</li>
        </ul>
    </div>
    <div class="clearfix"></div>
    <!--div class="alert alert-info">
        <strong>Frontend URL: <a target='_blank' href="<?php echo $base_url.'/'.$slug."/?lesson_detail=".$lesson_id; ?>"><?php echo $base_url.'/'.$slug."/?lesson_detail=".$lesson_id; ?></a></strong>
    </div-->
        <input type="hidden" id="typematerial" name="typematerial" value="community_call" />
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
            <!--div class="panel panel-primary">
                <div class="pull-right">
                    <a class="btn btn-success" onclick="reset_form(); open_modal('lesson_dialog');" href="javascript:;">Create New Exercise</a>                    
                    <a class="btn btn-warning reorder" href="javascript:;" data-type="resources" data-id="<?php echo $lesson_id; ?>">Re-Order Exercises</a>
                </div>
                <div class="panel-heading">Exercises

                </div>
                <div class="panel-body">

                    <table class="table table-bordered table-striped table-hover" id="data_resources" >
                        <thead>
                            <tr>
                                <th style="width: 4%;">SNo</th>
                                <th style="width: 14%;">Title</th>
                                <th style="width: 18%;">Description</th>
                                <th style="width: 14%;">Button type</th>
                                <th style="width: 12%;">Hours</th>                                									
                                <th style="width: 22%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($resources as $resource) {
                                $title = $resource->title;
                                if(trim($resource->external_link) != ''){
                                    $title = "$resource->title";
                                }
                                ?>
                                    <tr class="rowmod" data-id="<?php echo $resource->id; ?>">
                                        <td><?php echo $resource->ord; ?></td> 
                                        <td class="title" data-btn="<?php echo $resource->button_type; ?>" data-txt="<?php echo $resource->title; ?>" data-lnk="<?php echo $resource->external_link; ?>" ><?php echo $title; ?></td>
                                        <td class="text" >
                                            <div style="display: none; visibility: hidden" class="textdiv"><?php echo html_entity_decode($resource->description); ?></div>
                                            <?php echo limit_text(html_entity_decode($resource->description),10,false); ?>
                                        </td>
                                        <td><?php echo $resource->button_type == 'mark'?'Mark Complete':'Submit Project'; ?>
                                            
                                            <?php
                                            if($resource->button_type == "submit"){
                                                ?>
                                            <div><a data-id="<?php echo $resource->id; ?>" class="sumitted_projs" href="javascript:;" title="View Submitted Projects">View Submissions</a></div>
                                                <?php
                                            }
                                            
                                            ?>
                                        </td>  
                                        <td class="hrs"><?php echo $resource->total_hrs; ?></td>                                                                       
                                        <td class="actiontd">
                                            <a data-id="<?php echo $resource->id; ?>" class="editres btn btn-primary" href="javascript:;" title="Edit Exercise">Edit</a>
                                            <a class="btn btn-success" href="admin.php?page=resource_detail&resource_id=<?php echo $resource->id; ?>" title="Manage Resource">Manage Resource</a>
                                            <a href="javascript:;" data-id="<?php echo $resource->id; ?>" class="deleteres btn btn-danger" title="Delete Exercise">Delete</a>
                                            
                                            
                                        
                                        </td>
                                    </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

            </div--->
          
    
    <div class="panel panel-primary">
        <div class="pull-right">
            <a class="btn btn-success" onclick="reset_form(); open_modal('note_dialog');" href="javascript:;">Create New Note</a>                    
        </div>
		
		<?php 
            $notes_array='';$helplinks_array='';$doc_files_array='';
		   foreach($notes as $note){
			    $notes_array = explode("|",$note->comm_notes);
			    $helplinks_array = explode(",",$note->comm_hlp_links);
			   $doc_files_array = explode(",",$note->doc_file_links);
		   }

		?>
		
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
                    $f = 0;$index=0;
                    foreach ($notes as $note) { 
						   
					       foreach($notes_array as $stat){
							     $f++;$index++;
							if(!empty($stat)){  
                        ?>
                            <tr class="rowmodnote" data-id="<?php echo $note->id; ?>">                                        
                                <td><?php echo $f; ?></td>                            
                                <td class="title"> <?php echo limit_text(html_entity_decode(trim($stat,"{ }")),10,false); ?></td>     
                                <td><?php echo date("Y-m-d",  strtotime($note->created_dt)); ?></td>                            
                                <td class="actiontd">                                            
                                    <a href="javascript:;" data-id="<?php echo $note->id; ?>" class="editnote btn btn-primary" title="Edit Note">Edit</a>
                                    <a href="javascript:;" data-id="<?php echo $note->id; ?>" class="deletenote btn btn-danger" title="Delete Note">Delete</a>
								</td>
                            </tr>
                        <?php
							}
						   }
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
                                    <th style="width: 40%;">URL</th>                                    
                                    <!--th style="width: 20%;">Action</th-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $f=0;
                                foreach ($helplinks as $helplink) {
                                    foreach($helplinks_array as $link){
                                          $sep = explode("|",trim($link,"( )"));
										 $f++; 
										 if(!empty($sep[0])){
									
                                    ?>
                                        
                                        <tr class="rowmodlink" data-id="<?php echo $helplink->id; ?>">                                        
                                            <td><?php echo $f; ?></td>    
                                            <td class="title" data-link="<?php echo $sep[0]; ?>" data-title="<?php echo $sep[0]; ?>"> 
                                                <a target="_blank" href="<?php echo $sep[1]; ?>"><?php echo $sep[0]; ?></a>
                                            </td>    
											<td><a target="_blank" href="<?php echo $sep[1]; ?>"><?php echo $sep[1]; ?></a></td>
                                            <!--td class="actiontd">        
                                                <a href="javascript:;" data-id="<?php echo $helplink->id; ?>" class="editlink btn btn-primary" title="Edit Link">Edit</a>
                                                <a href="javascript:;" data-id="<?php echo $helplink->id; ?>" class="deletelink btn btn-danger" title="Delete Link">Delete</a>
                                                
                                            </td--->
                                        </tr>
                                    <?php
										 }
									}
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
									<th>View</th>
                                    <!--th style="width: 20%;">Action</th-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $f=0;
//print_r($doc_files_array);die;
                                foreach ($docs as $doc) {
								  foreach($doc_files_array as $link){
                                     $sep = explode("|",trim($link,"{ }"));	
									 if(!empty($sep[0])){ 
                                      $f++;
                                    ?>
                                        
                                        <tr class="rowmoddoc" data-id="<?php echo $doc->id; ?>">                                        
                                            <td><?php echo $f; ?></td>    
                                            <td class="title"> 
                                                <a download href="<?php echo $sep[1]; ?>"><?php echo $sep[0]; ?></a>
                                            </td>
											<td>
											  <img src="<?php echo $sep[1]; ?>" height="50" width="50" />
											</td>
                                            <!--td class="actiontd">                                            
                                                <a href="javascript:;" data-id="<?php echo $doc->id; ?>" class="deletedoc btn btn-danger" title="Delete Document">Delete</a>
                                            </td-->
                                        </tr>
                                    <?php
									 }
								  }
                                }
                                ?>
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    
    
</div>


<div id="lesson_dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Exercise</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="addresource" name="addresource" class="form-horizontal">
                    
                    <input type="hidden" id="typerescreated" name="typerescreated" value="direct" />
                    <input type="hidden" id="course_id" name="course_id" value="<?php echo $_REQUEST['course_id']; ?>" />                    
                    <input type="hidden" id="module_id" name="module_id" value="<?php echo $module_id; ?>" />
                    <input type="hidden" id="lesson_id" name="lesson_id" value="<?php echo $lesson->id; ?>" />
                    
                    <input type="hidden" id="resid" name="resid" value="0" />
                    <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Name* :</label>
                        <div class="col-lg-8">
                            <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Time (Hrs) * :</label>
                        <div class="col-lg-8">
                            <input type="number" required class="form-control" id="hours" name="hours" placeholder="Time to complete Exercise (Hrs)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-lg-2 control-label">Button Type :</label>
                        <div class="col-lg-8">
                            <select class="form-control" name="button_type" id="button_type">
                                <option value="mark">Mark Complete</option>
                                <option value="submit">Submit Project</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="display: none; visibility: hidden;">
                        <label for="title" class="col-lg-2 control-label">External Link :</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="link" name="link" placeholder="External Link">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cat_name" class="col-lg-2 control-label">Description :</label>
                        <div class="col-lg-8 wpeditor">
                            <?php
                            wp_editor("", $id = 'description', $prev_id = 'title', $media_buttons = false, $tab_index = 1);
                            ?>
                        </div>
                    </div>                        
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick="submitres();" class="btnupdt btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
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
                                        
                    <input type="hidden" id="lesson_id" name="lesson_id" value="<?php echo $lesson->id; ?>" />

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
                                        
                    <input type="hidden" id="lessonid" name="lessonid" value="<?php echo $lesson->id; ?>" />

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
                                        
                    <input type="hidden" id="lesonid" name="lesonid" value="<?php echo $lesson_id; ?>" />
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
                                        
                    <input type="hidden" id="lessid" name="lessid" value="<?php echo $lesson_id; ?>" />
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




<div id="project_summitted" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Work Submissions By Users</h4>
            </div>
            <div class="modal-body">
                 <div id="listusersdiv" >
                        <div class="loadergif">
                            <img src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
                        </div>
                        <table class="table table-bordered tbluserdv" style="display: none;">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Username</th>
                                    <th style="width: 30%;">Email</th>
                                    <th style="width: 45%;">Links</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
            </div>
            
        </div>
    </div>
</div>