<?php

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->data->ID;
include 'hasright.php';
if($is_enrolled == 0){
    header("Location: " . site_url());
}
$videos = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info FROM " . media()." WHERE resource_id = %d AND type = 'video' ORDER BY created_dt DESC", $resource_id
        )
);

$img = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info FROM " . media()." WHERE resource_id = %d AND type = 'image' ORDER BY created_dt DESC", $resource_id
        )
);

$docs = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info FROM " . media()." WHERE resource_id = %d AND type = 'document'", $resource_id
        )
);

$helplinks = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info  FROM " . media()." WHERE resource_id = %d AND type = 'link'", $resource_id
        )
);

$notes = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT note FROM " . lesson_notes()." WHERE resource_id = %d", $resource_id
        )
);


$base_url = site_url();
$slug = PAGE_SLUG;

$res_sts = $wpdb->get_var
        (
        $wpdb->prepare
                (
                "SELECT count(id) FROM ". resource_status() ." WHERE resource_id = %d AND user_id = %d", 
                $resource_id, 
                $user_id                
        )
);

$hassubmitted = '';
if($resource->button_type == 'mark'){ 
    $classmsrk='unmarkeddiv';
    $txtmarked = 'unmarked'; $marktxt = 'Mark Complete';
    if($res_sts > 0){
        $txtmarked = 'marked'; $marktxt = 'Completed';
        $classmsrk='markeddiv';    
    }
}
else{
    $classmsrk='unmarkeddiv';
    $txtmarked = 'unmarked'; $marktxt = 'Submit Project';
    if($res_sts > 0){
        $txtmarked = 'marked'; $marktxt = 'Submitted';
        $classmsrk='markeddiv';     
        $hassubmitted = get_project_links($resource->id);
    }
}


?>

<div class="main-section frontlessonpage">

    <div class="container">
        <div class="row">            
<!--            <h4 class="main-title">Exercise - <php echo $resource->title; ?></h4>-->
            <div class="bread_crumb">
                <ul>
                    <li title="All Courses List">
                        <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                    </li>
                    <li title="Course">
                        <a href="<?php echo site_url()."/".PAGE_SLUG."?course=".$course_id; ?>"><?php echo $course->title; ?> [<?php echo $module->title; ?>]</a> >> 
                    </li>
                    <li title="Lesson">
                        <a href="<?php echo site_url()."/".PAGE_SLUG."?lesson_detail=".$lesson->id; ?>"><?php echo $lesson->title; ?></a> >>                 
                    </li>
                    <li title="Exercise">
                        <?php echo $resource->title; ?>
                    </li>
                </ul>
            </div>
            <div class="col-sm-1 left_pd" style="<?php if(empty($videos->path)){ echo 'display:none;'; } ?>">
                <div class="sect_left">
                    <ul>
                        <li class="current licls"><a data-type='dashboard' href="javascript:;">
                                    <i aria-hidden="true" class="icon-ic_home_black_24px"></i>
                                    <span>Dashboard</span>
                                </a>
                        </li>
                        
                        <li class="licls" ><a data-type='description' href="javascript:;">
                                <i aria-hidden="true" class="icon-ic_visibility_black_24px"></i>
                                <span>Description</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            
            <div class="dashboard clscomman" >
                <div class="col-sm-8 video_out" style="<?php if(empty($videos->path)){ echo 'display:none;'; } ?>">                
                     <?php
                        if(empty($videos)){
                            echo "<!--div class=''><img src='".TR_COUNT_PLUGIN_URL."/assets/images/novideo.jpg'></a></div--->";
                        }
                        else{                        
                            echo html_entity_decode($videos->path);
                        }
                    ?>
                </div>
                <div class="col-sm-3 pd_right thumb_main" style="<?php if(empty($videos->path)){ echo 'display:none;'; } ?>">
                    <?php
                        if($course->imgpath == ''){
                            echo "<div class=''><img src='".TR_COUNT_PLUGIN_URL."/assets/images/defaultimg.jpg'></a></div>";
                        }
                        else{
							  if(empty($videos)){}else{
                            ?>
                             <a target="_blank" href="<?php echo $course->link; ?>" ><img src="<?php echo TR_COUNT_PLUGIN_URL.$course->imgpath; ?>" /></a>
                            <?php                                
                        }
						}
                    ?>
                </div>
            </div>  
            <div style="<?php if(empty($videos->path)){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>" class="description clscomman <?php if(empty($videos->path)){ echo 'col-sm-12'; }else{ echo 'col-sm-11'; } ?>  texteditor">
                <?php
                if(empty($resource->description)){
                    echo "No Description Found";
                }
                else{
                    echo html_entity_decode($resource->description);
                }
                ?>
            </div>
            

        </div>
        <div class="clearfix"></div>
        <div class="buttonres ">   
            <div class="<?php echo $classmsrk; ?>" id="resource_<?php echo $resource->id; ?>">
                <div class="submit_buttons">
                    <a class="sub_btn markresource" data-buttontype="<?php echo $resource->button_type; ?>" data-status = "<?php echo $txtmarked; ?>" data-attr="<?php echo $resource->id; ?>" href="javascript:;"><?php echo $marktxt; ?></a>            
                </div>            
            <div class="sublinksstudents"  style="<?php echo $hassubmitted == ''?'display: none;':''; ?>">
                <h6>Submitted Links</h6>
                <div class="projlinksdiv">
                    <?php echo $hassubmitted; ?>
                </div>
            </div>
				<div class="submittedfiles">
																		   <?php 
																             $doc_file =  $wpdb->get_results("select * from ".projects()." where resource_id = ".$_REQUEST['exercise_detail']);
																             //print_r($doc_file);
																            // echo count($doc_file);
																             if(count($doc_file)==1){
																			   echo "<h6>Submitted Files</h6>";	 
																			   foreach($doc_file as $key=>$value){
																					$image_arr=explode(",",$value->doc_files);
																					 foreach($image_arr as $image){
																						echo "<a href='".$image."' target='_blank'>".$image."</a><br/>";
																					 }
																				 }
																			 }else{
																			    
																			 }
																			 
																            ?>
					
																		</div>
           </div>
        </div>
        <div class="clearfix"></div>
        <div class="row mg_top">           
                    <div class="col-sm-8">
                        <div class="notesdiv">
                        <h2>Instructor Notes</h2>
                         <?php if(!empty($notes)) { ?>
                        <?php     

                            foreach ($notes as $note) {      
                                ?>
                                <div>
                                   <?php echo html_entity_decode($note->note); ?>
                                </div>                    

                                <?php
                            }
                            ?>                 
                        <?php }
                            else{
                                echo "<i>Notes Not Available</i>";
                            }
                            ?>
                        
                        </div>
                    </div>




                    <div class="col-sm-4 colrights">  

                        <div class="row">

                            <div class="col-sm-12 docsdiv">
                                <h2>Resources</h2>
                                <?php if(!empty($docs)) { ?>
                                <ul class="help_links">
                                <?php                               
                                    foreach ($docs as $doc) {
                                        ?>
                                        <li><a download href="<?php echo TR_COUNT_PLUGIN_URL."/".$doc->path; ?>"><?php echo $doc->extra_info; ?></a></li>
                                        <?php
                                    }                                
                                ?>
                                </ul>                    
                                <?php }
                                else{
                                    echo "<i>Downloads Not Available</i>";
                                }
                                ?>
                            </div>


                            <div class="col-sm-12 helpdiv">
                                <h2>Get Help</h2>
                                 <?php if(!empty($helplinks)) { ?>
                                <ul class="help_links">
                                    <?php

                                    foreach ($helplinks as $helplink) {
                                        ?>
                                    <li><a target="_blank" href="<?php echo $helplink->path; ?>"><?php echo $helplink->extra_info; ?></a></li>
                                        <?php
                                    }
                                    ?>

                                </ul>
                                 <?php }
                                else{
                                    echo "<i>Not Available</i>";
                                }
                                ?>
                            </div>
                        </div>



                    </div>
                </div>
    </div>    
</div>


<!-- submit project box -->
    <div class="arrow_box submit_project respage" style="display: none;">
        <span class="close-btn">
            <span class="glyphicon glyphicon-remove btnclospop" aria-hidden="true"></span>

        </span>
       <form enctype="multipart/form-data">
                        <div class="controls">
                            <div class="row">
                            <div class="col-sm-10">
                                <input type="text" name="project_links" placeholder="Project Links (Use comma to separate if multiple)" class="wk_project_link form-control " data-project="220">
                                 
								<br/>
								<input type="file"  name="responsedoc[]" id="responsedoc" multiple="true"><br/>
								 <div class="remove_project_ctn" style="display: none;">
									<a href="javascript:void(0)" class="remove_project_link" target="_blank">Remove link / Files</a>
								  </div>
								<br/>
								 <a href="javascript:;" class="btn pink project-submit-btn sub_btn">Submit</a>
							</div>
                            
                            </div>
                          
                        </div>
                    </form>
    </div>
    <!-- submit project box -->