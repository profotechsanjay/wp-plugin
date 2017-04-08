<?php

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->data->ID;
include 'hasright.php';
if($is_enrolled == 0){
    header("Location: " . site_url());
}
if(empty($lesson)){
    
    ?>

     <div class="main-section">
        <div class="container">
            <h4>Lesson Detail</h4>
            <div class="bread_crumb">
                <ul>
                    <li title="All Courses List">
                        <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                    </li>
                    <li title="Course">
                        <a href="<?php echo site_url()."/".PAGE_SLUG."?course=".$course_id; ?>"><?php echo $course->title; ?></a> >> 
                    </li>
                    <li title="Lesson">
                        No Lesson Detail
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <div class="alert alert-success">
                        <strong>Note: </strong>
                        Lesson Not Found
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
    
    
}
else {

$videos = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info FROM " . media()." WHERE lesson_id = %d AND type = 'video' ORDER BY created_dt DESC", $lesson_id
        )
);

$img = $wpdb->get_row
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info FROM " . media()." WHERE lesson_id = %d AND type = 'image' ORDER BY created_dt DESC", $lesson_id
        )
);

$docs = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info FROM " . media()." WHERE lesson_id = %d AND type = 'document'", $lesson_id
        )
);

$helplinks = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT path,extra_info  FROM " . media()." WHERE lesson_id = %d AND type = 'link'", $lesson_id
        )
);

$notes = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT note FROM " . lesson_notes()." WHERE lesson_id = %d", $lesson_id
        )
);


$resources = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT * FROM " . resources()." WHERE lesson_id = %d ORDER BY ord", $lesson_id
        )
);


$res_sts = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT rs.resource_id FROM " . resource_status() . " rs INNER JOIN " . resources() . " r ON "
                . "rs.resource_id = r.id WHERE rs.user_id = %d AND r.lesson_id = %d", 
                $user_id,
                $lesson_id
        )
);

$arr_resoucesmark = array();
foreach($res_sts as $res_st){
    array_push($arr_resoucesmark, $res_st->resource_id);
}   


$base_url = site_url();
$slug = PAGE_SLUG;

?>

<div class="main-section frontlessonpage">

    <div class="container">
        <div class="row">            
<!--            <h4 class="main-title">Lesson - <php echo $lesson->title; ?></h4>-->
            <div class="bread_crumb">
                <ul>
                    <li title="All Courses List">
                        <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                    </li>
                    <li title="Course">
                        <a href="<?php echo site_url()."/".PAGE_SLUG."?course=".$course_id; ?>"><?php echo $course->title; ?> [<?php echo $module->title; ?>]</a> >> 
                    </li>
                    <li title="Lesson">
                        <?php echo $lesson->title; ?>
                    </li>
                </ul>
            </div>
            <div class="col-sm-1 left_pd">
                <div class="sect_left">
                    <ul>
						<?php 
	                      if(empty($videos)){
						  ?>
						<li class="licls" ><a data-type='description' href="javascript:;">
                                <i aria-hidden="true" class="icon-ic_visibility_black_24px"></i>
                                <span>Description</span>
                            </a>
                        </li>

                        <li class="licls" ><a data-type='resource' href="javascript:;">
                                <i aria-hidden="true" class="icon-ic_card_travel_black_24px"></i>
                                <span>Exercises</span>
                            </a>
                        </li>
						<?php
						  }else{
						  ?>
						<li class="current licls" ><a data-type='dashboard' href="javascript:;">
                                    <i aria-hidden="true" class="icon-ic_home_black_24px"></i>
                                    <span>Dashboard</span>
                                </a>
                        </li>
                        
                        <li class="licls" ><a data-type='description' href="javascript:;">
                                <i aria-hidden="true" class="icon-ic_visibility_black_24px"></i>
                                <span>Description</span>
                            </a>
                        </li>

                        <li class="licls" ><a data-type='resource' href="javascript:;">
                                <i aria-hidden="true" class="icon-ic_card_travel_black_24px"></i>
                                <span>Exercises</span>
                            </a>
                        </li>
						<?php
						  }
	                    ?>
                        
                    </ul>
                </div>
            </div>
            
            <div class="dashboard clscomman" style="<?php if(empty($videos->path)){ echo 'display:none;'; } ?>">
                <div class="col-sm-8 video_out">                
                     <?php
                        if(empty($videos)){
                            echo "<div class='' style='display:none;'><img src='".TR_COUNT_PLUGIN_URL."/assets/images/novideo.jpg'></a></div>";
                        }
                        else{                        
                            echo html_entity_decode($videos->path);
                        }
                    ?>
                </div>
                <div class="col-sm-3 pd_right thumb_main">
					
                    <?php
	                  
                        if($course->imgpath == ''){
                             echo "<div class=''><img src='".TR_COUNT_PLUGIN_URL."/assets/images/defaultimg.jpg'></a></div>";
                        }
                        else{
                            ?>
                             <a target="_blank" href="<?php echo $course->link; ?>" ><img src="<?php echo TR_COUNT_PLUGIN_URL.$course->imgpath; ?>" class="course-image-icon"/></a>
                            <?php                                
                        }
                    ?>
                </div>
            </div>  
            <div style="<?php if(empty($videos->path)){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>" class="description clscomman col-sm-11 texteditor">
                <div class="">
                <?php
                if(empty($lesson->description)){
                    echo "No Description Found";
                }
                else{
                    echo html_entity_decode($lesson->description);
                }
                ?>
                </div>
            </div>
            <div style="display: none;" class="resource clscomman col-sm-11">
                <div class="col-sm-8">
                <?php
                if(empty($resources)){
                    echo "No Exercises Found";
                }
                foreach ($resources as $resource) {
                    $hassubmitted = '';
                    if($resource->button_type == 'mark'){                                                            
                        $classmsrk='unmarkeddiv';
                        $txtmarked = 'unmarked'; $marktxt = 'Mark Complete';
                        if(in_array($resource->id, $arr_resoucesmark)){
                            $txtmarked = 'marked'; $marktxt = 'Completed';
                            $classmsrk='markeddiv';
                            $completed_resources++;
                        }
                    }
                    else{

                        $classmsrk='unmarkeddiv';
                        $txtmarked = 'unmarked'; $marktxt = 'Submit Project';
                        if(in_array($resource->id, $arr_resoucesmark)){
                            $txtmarked = 'marked'; $marktxt = 'Submitted';
                            $classmsrk='markeddiv';
                            $completed_resources++;
                            $hassubmitted = get_project_links($resource->id);
                        }

                    }
                    
                    $resourceurl = site_url()."/".PAGE_SLUG."?exercise_detail=".$resource->id;
                    $rtitt = "<a target='_blank' href='$resourceurl'>$resource->title</a>";    
                    
                    ?>
                    
                    <div class="block_resources <?php echo $classmsrk; ?>" id="resource_<?php echo $resource->id; ?>">
                                            <div class="submit_buttons">
                                                <a class="sub_btn markresource" data-buttontype="<?php echo $resource->button_type; ?>" data-status = "<?php echo $txtmarked; ?>" data-attr="<?php echo $resource->id; ?>" href="javascript:;"><?php echo $marktxt; ?></a>
                                            </div>

                                            <div class="block_main">
                                                <span class="block_left">
                                                    <i class="icon-scales"></i>
                                                </span>
                                                <div class="block_info">
                                                    <div class="sub-block_time">
                                                        <i class="icon-time"></i> <?php echo $resource->total_hrs; ?> Hours
                                                    </div>

                                                    <div class="block_txt">
                                                        <?php echo $rtitt; ?>
                                                    </div>
                                                    <div class="full_descrp texteditor">
                                                        <div class="smallinfo">
<?php echo limit_text(html_entity_decode($resource->description), 30); ?>
                                                        </div>
                                                        <div class="largeinofinfo">
<?php echo full_text(html_entity_decode($resource->description)); ?>                                                
                                                        </div>

                                                    </div>
                                                    <div class="sublinksstudents"  style="<?php echo $hassubmitted == ''?'display: none;':''; ?>">
                                                        <h6>Submitted Links</h6>
                                                        <div class="projlinksdiv">
                                                            <?php echo $hassubmitted; ?>
                                                        </div>
                                                    </div>
													 <div class="submittedfiles" style="<?php if($resource->id==$_REQUEST['lesson_detail']){ }else{ echo 'display:none;'; } ?>">
																		   <?php
					                                                          $doc_file =  $wpdb->get_results("select * from ".projects()." where resource_id = ".$_REQUEST['lesson_detail']);
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


                                        </div>
                    <?php
                    
                    
                }
                
                ?>
                
               </div> 
                
            </div>

        </div>

        
           <!-- submit project box -->
                <div class="arrow_box submit_project" style="display: none;">
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


<?php
    }
?>