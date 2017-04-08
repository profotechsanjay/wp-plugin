<?php

global $current_user;
$current_user = wp_get_current_user();

$user_id = $current_user->data->ID;
if ($user_id > 0) {


    $is_enrolled = $wpdb->get_var
            (
            $wpdb->prepare
                    (
                    "SELECT count(id) as enrolled FROM " . enrollment() . " WHERE course_id = %d AND user_id = %d", $course_id, $user_id
            )
    );

    // check course enrolled or not, 0 = not enrolled
    if ($is_enrolled == 0) {
        
        include_once 'detail_only_course.php';
        
    } else {
        
        
        $mentorcal = $wpdb->get_row
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . mentorcall()." WHERE course_id = %d AND status = 'active' ORDER BY created_dt DESC", $course_id
                )
        );
        
        $links = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT * FROM " . setting() . " WHERE type = 'link'", ""
                )
        );

        $lessons = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT l.*,m.ord as module_ord, m.id as module_id,m.course_id,m.title as mtitle, m.description as mdescription, m.external_link as mexternal_link,"
                        . "m.total_hrs as mtotal_hrs, m.total_resources as mtotal_resources "
                        . "FROM " . lessons() . " l LEFT JOIN " . modules() . " m ON l.module_id = m.id "
                        . "WHERE m.course_id = %d ORDER BY m.ord,l.ord", $course_id
                )
        );
               
        $innerarr = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT l.*, r.ord as resource_ord, r.id as resource_id, r.lesson_id, r.button_type, r.module_id, r.course_id,r.title as rtitle, r.description as rdescription, r.external_link as rexternal_link,"
                        . "r.total_hrs as rtotal_hrs, m.ord as module_ord, m.id as module_id, m.course_id,m.title as mtitle, m.description as mdescription,"
                        . "m.external_link as mexternal_link,m.total_hrs as mtotal_hrs, m.total_resources as mtotal_resources "
                        . "FROM " . lessons() . " l LEFT JOIN " . modules() . " m ON l.module_id = m.id "
                        . "LEFT JOIN " . resources() . " r ON l.id = r.lesson_id WHERE m.course_id = %d "
                        . " ORDER BY m.ord,l.ord,r.ord", $course_id
                )
        );  
                    
        $res_sts = $wpdb->get_results
                (
                $wpdb->prepare
                        (
                        "SELECT rs.resource_id FROM " . resource_status() . " rs INNER JOIN " . resources() . " r ON "
                        . "rs.resource_id = r.id WHERE rs.user_id = %d AND rs.course_id = %d", 
                        $user_id,
                        $course_id
                )
        );
        
        $arr_resoucesmark = array();
        foreach($res_sts as $res_st){
            array_push($arr_resoucesmark, $res_st->resource_id);
        }                
        $user = get_user_by('id', $course->created_by);
                                
        $percent = 0;   
        $completed_resources = 0;
        $total_resources = 0;
        
        
        if (empty($lessons)) {
            ?>
           
                <div class="main-section">
                    <div class="container">  
                        <h4>Course Detail</h4>
                        <div class="bread_crumb">
                            <ul>
                                <li title="All Courses List">
                                    <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                                </li>
                                <li title="Course">
                                    <?php echo $course->title; ?>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                
                                <div class="alert alert-success">
                                    <strong>Note: </strong>
                                    No Data Added Yet In this Course
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            <?php
        } else {
            
            $globalmodid = $lessons[0]->module_id;
            ?>

            <div class="main-section singlepagecourse">
                
                <div class="container">
                    
                    <h4>Course Detail</h4>
                    <div class="bread_crumb">
                        <ul>
                            <li title="All Courses List">
                                <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                            </li>
                            <li title="Course">
                                <?php echo $course->title; ?>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="sidebar-left">                    
                                <?php

                                $get_project = get_project('course',$lessons[0]->course_id,'check');                                
                                $flg = 0;
                                $inc = 1;
                                $headerpt = 0;
                                $innercnt = 0;
                                foreach ($lessons as $lesson) {
                
                                        $isheader = 0;
                                        if ($flg == 0) {
                                            $isheader = 1;
                                        } else {
                                            if ($globalmodid != $lesson->module_id) {
                                                $isheader = 1;
                                                $globalmodid = $lesson->module_id;
                                            }
                                        }
                                        $innercnt++;
                                        if ($isheader == 1) {
                                            $headerpt++;
                                            ?>
                                                                <ul>
                                                                    <li class="module" data-attr="mod<?php echo $headerpt; ?>" id="limodule<?php echo $inc; ?>"><a href="#module<?php echo $lesson->id; ?>"><?php echo $headerpt; ?>. <?php echo $lesson->mtitle; ?></a></li>

                                                                </ul>
                                                                <ul class="subheader modulelesson<?php echo $inc; ?>">
                                            <?php
                                        }
                                        ?>
                                                                <li data-attr="mod<?php echo $headerpt; ?>" class="leson"><a href="#lesson<?php echo $lesson->id; ?>"><span class="sub_point"><?php echo $headerpt . '.' . $innercnt; ?></span><span class="point_title"> <?php echo $lesson->title; ?></span></a></li>
                                                                <?php
                                                                $oldid = $lesson->module_id;
                                                                $flg++;
                                                                $inc++;
                                                                // End od subheader ul
                                                                if ($lessons[$flg]->module_id != $oldid) {
                                                                    
                                                                    $get_mod = get_project('module',$oldid);                                                                    
                                                                    if(!empty($get_mod)){
                                                                        ?>
                                                                        <li data-attr="mod<?php echo $headerpt; ?>" class="leson"><a href="#proj<?php echo $get_mod->id; ?>"><span class="sub_point"><?php echo $headerpt . '.' . ($innercnt + 1); ?></span><span class="point_title"> <?php echo $get_mod->title; ?></span></a></li>
                                                                
                                                                        <?php
                                                                    }
                                                                    $innercnt = 0;
                                                                    ?>
                                                                </ul>
                                                                    <?php
                                                                }
                                        }
                                        
                                        if(!empty($get_project)){
                                            $headerpt = $headerpt + 1;
                                                                                
                                        ?>
                                
                                        <!-- for last project part -->

                                            <ul>
                                                <li class="module" data-attr="mod<?php echo $headerpt; ?>" id="limodule<?php echo $inc++; ?>"><a href="#proj<?php echo $get_project->id; ?>"><?php echo $headerpt; ?>. <?php echo $get_project->title; ?></a></li>
                                            </ul>                                    

                                        <!-- for last project part -->
                                        <?php } ?>
                            </div>
                        </div>



                        <div class="col-sm-9">
                            <div class="row">
                                <div id="contentheader" class="content_header">
                                    <div class="col-sm-9 col-md-7">
                                        <h2 class="h2main"><?php echo $course->title; ?></h2>
                                        <?php if(!empty($mentorcal)){ ?>
                                            <h5><a class="mentorcalla" href="javascript:;">Next Mentor Call:</a> <?php echo date("D d M Y, h:i a",  strtotime($mentorcal->mentor_call)); ?></h5>
                                        <?php } ?>
                                        <div class="content_info">
                                            <h5>


                                                <div class="info_inner">
                                                    <div class="info_pic">

                                                        <img width="25" height="25" style="width: 25px;height: 25px;" alt="Claudia Gold" src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/profile_pic.jpg">

                                                    </div>

                                                    <span style="text-transform: none;font-size: 15px;" rel="author"><?php echo $user->data->display_name; ?></span>,



                                                    <span style="text-transform: none;font-size: 15px;" class="fade_txt"> <?php echo $user->roles[0]; ?></span>
                                                </div>


                                            </h5>
                                        </div>

                                    </div>


                                    <div class="col-md-5 col-sm-3">
                                        <div class="resources_out">
                                            <i class=" icon-three"></i> <?php echo $course->total_resources; ?> Resources
                                        </div>

                                        <div class="tme_out">
                                            <i class="icon-time"></i> <?php echo $course->total_hrs; ?>+ Hours
                                        </div>

                                        <div class="share_out">
            <?php
            foreach ($links as $link) {
                ?>
                                                <a target="_blank" href="<?php echo $link->keyvalue; ?>">
                <?php echo $link->keyname; ?>
                                                </a>                                
                                                <?php
                                            }
                                            ?>                                
                                        </div>

                                    </div>




                                </div>
                            </div>


                            <div class="content_main">
                                <div class="progress_outer">

                                    <h5 class="up_title">Hello, <?php echo $current_user->data->display_name; ?></h5>
                                    <div class="progress_inner">
                                        <div class="bar_info">
                                            <span><label class="perint"><?php echo $percent; ?></label>% Complete</span>
                                            <div class="bar-progress">
                                                <div class="perdiv" style="width:<?php echo $percent; ?>%" class="bar wip"></div>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-12 innerdata">

            <?php
            $globalmodid = $innerarr[0]->module_id;
            $flg = 0;
            $inc = 1;
            $headerpt = 0;
            $inclesson = 1;


            $globallesson = $innerarr[0]->id;
            
            foreach ($innerarr as $lesson) {                
                
                $isheader = 0;
                $islesson = 0;
                if ($flg == 0) {
                    $isheader = 1;
                    $islesson = 1;
                } else {
                    if ($globalmodid != $lesson->module_id) {
                        $isheader = 1;
                        $globalmodid = $lesson->module_id;
                    }

                    if ($globallesson != $lesson->id) {
                        $islesson = 1;
                        $globallesson = $lesson->id;
                    }
                }

                if ($isheader == 1) {
                    $tit = $lesson->mtitle;
                    if ($lesson->mexternal_link != '') {
                        $tit = "<a target='_blank' href='$lesson->mexternal_link'>$tit</a>";
                    }
                    ?>
                                            <div class="first_block blockcontent" id="module<?php echo $lesson->id; ?>">

                                                <header>

                                                    <span class="block_time">
                                                        <i class="icon-time"></i> <?php echo $lesson->mtotal_hrs; ?>+ Hours
                                                    </span>
                                                    <h2 class="h2main"><?php echo $tit; ?></h2>
                                                    <div class="descrp_main">
                                                        <p>
                                                        <div class="smallinfo">
                                                        <?php echo html_entity_decode($lesson->mdescription); ?>
                                                        </div>   
                                                       
                                                        </p>
                                                    </div>

                                                            <?php
                                                        }


                                                        if ($islesson == 1) {

                                                            $titt = $lesson->title;
//                                                            if ($lesson->external_link != '') {
//                                                                $titt = "<a target='_blank' href='$lesson->external_link'>$titt</a>";
//                                                            }
                                                            $lessonurl = site_url()."/training-tool?lesson_detail=".$lesson->id;
                                                            $titt = "<a target='_blank' href='$lessonurl'>$titt</a>";
                                                            
                                                            ?>

                                                    <div class="sub_block blockcontent" id="lesson<?php echo $lesson->id; ?>">
                                                        <header>
                                                            <span class="block_time">
                                                                <i class="icon-time"></i> <?php echo $lesson->total_hrs; ?>+ Hours
                                                            </span>
                                                            <h4 class="h4main"><?php echo $titt; ?></h4>

                                                        </header>

                                                        <div class="descrp_main">

                                                            <p>
                                                            <div class="smallinfo">
                    <?php echo limit_text(html_entity_decode($lesson->description), 30); ?>                                           
                                                            </div>
                                                            <div class="largeinofinfo">
                    <?php echo full_text(html_entity_decode($lesson->description)); ?>                                                
                                                            </div>

                                                            </p>

                                                                <?php
                                                            }
                                                                    
                                                            ?>
                                                            <?php if($lesson->resource_id != ''){ 
                                                                
                                                                $rtitt = $lesson->rtitle;
                                                            
                                                                if($lesson->button_type == 'mark'){                                                            
                                                                    $classmsrk='unmarkeddiv';
                                                                    $txtmarked = 'unmarked'; $marktxt = 'Mark Complete';
                                                                    if(in_array($lesson->resource_id, $arr_resoucesmark)){
                                                                        $txtmarked = 'marked'; $marktxt = 'Completed';
                                                                        $classmsrk='markeddiv';
                                                                        $completed_resources++;
                                                                    }
                                                                }
                                                                else{

                                                                    $classmsrk='unmarkeddiv';
                                                                    $txtmarked = 'unmarked'; $marktxt = 'Submit Project';
                                                                    if(in_array($lesson->resource_id, $arr_resoucesmark)){
                                                                        $txtmarked = 'marked'; $marktxt = 'Submitted';
                                                                        $classmsrk='markeddiv';
                                                                        $completed_resources++;
                                                                    }

                                                                }
                                                                
                                                                
                                                                $total_resources++;
                                                                
                                                                ?>
                                                            <div class="block_resources <?php echo $classmsrk; ?>" id="resource_<?php echo $lesson->resource_id; ?>">
                                                            <div class="submit_buttons">
                                                                <a class="sub_btn markresource" data-buttontype="<?php echo $lesson->button_type; ?>" data-status = "<?php echo $txtmarked; ?>" data-attr="<?php echo $lesson->resource_id; ?>" href="javascript:;"><?php echo $marktxt; ?></a>
                                                            </div>
                                                                
                                                            <div class="block_main">
                                                                <span class="block_left">
                                                                    <i class="icon-scales"></i>
                                                                </span>
                                                                <div class="block_info">
                                                                    <div class="sub-block_time">
                                                                        <i class="icon-time"></i> <?php echo $lesson->rtotal_hrs; ?> Hours
                                                                    </div>

                                                                    <div class="block_txt">
                                                                        <?php echo $rtitt; ?>
                                                                    </div>
                                                                    <div class="full_descrp">
                                                                        <div class="smallinfo">
                <?php echo limit_text(html_entity_decode($lesson->rdescription), 30); ?>
                                                                        </div>
                                                                        <div class="largeinofinfo">
                <?php echo full_text(html_entity_decode($lesson->rdescription)); ?>                                                
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                               

                                                        </div>
                                                             <?php } ?>

                <?php
                $flg++;
                $inc++;

                $oldlessid = $lesson->id;
                // end of lesson
                if ($innerarr[$flg]->id != $oldlessid) {
                    $inclesson++;
                    ?>
                                                        </div>                                            
                                                    </div>
                                                            <?php
                                                        }

                                                        $oldid = $lesson->module_id;
                                                        // End od subheader ul
                                                        if ($innerarr[$flg]->module_id != $oldid) {
                                                            
                                                            $get_mod = get_project('module',$oldid,'check');
                                                            if(!empty($get_mod)){
                                                                
                                                                $total_resources++;
                                                                $clstop = ''; $txtsum = 'Submit Project'; $txtsumcls = '';
                                                                if(isset($get_mod->links) && $get_mod->links != ''){
                                                                    $clstop = 'submittedproj'; $txtsum = 'Submitted'; $txtsumcls = 'linksumitted';
                                                                    $completed_resources++;
                                                                    
                                                                }
                                                                
                                                                ?>
                                                                    
                                                                
                                                                <div class="sub_block blockcontent <?php echo $clstop; ?>" id="proj<?php echo $get_mod->id; ?>">
                                                                        <header>
                                                                            <span class="block_time">
                                                                                <i class="icon-time"></i> <?php echo $get_mod->total_hrs; ?>+ Hours
                                                                            </span>
                                                                            <h4 class="h4main"><?php echo $get_mod->title; ?></h4>

                                                                        </header>

                                                                        <div class="descrp_main">
                                                                            
                                                                            <div class="full_descrp">
                                                                                <div class="smallinfo">
                                                                                    <?php echo limit_text(html_entity_decode($get_mod->description), 30); ?>
                                                                                </div>
                                                                                <div class="largeinofinfo">
                                                                                    <?php echo full_text(html_entity_decode($get_mod->description)); ?>                                                
                                                                                </div>

                                                                            </div>
                                                                            
                                                                            <div class="block_resources">
                                                                                <div class="submit_buttons">
                                                                                    <a class="sub_btn submitproj <?php echo $txtsumcls; ?>" data-id="<?php echo $get_mod->id; ?>" href="javascript:;"><?php echo $txtsum; ?></a>
                                                                                </div>

                                                                                <div class="block_main">
                                                                                    <span class="block_left">
                                                                                        <i class="icon-scales"></i>
                                                                                    </span>
                                                                                    <div class="block_info">                                            
                                                                                        <div class="block_txt projlnk">
                                                                                            
                                                                                            <?php if(isset($get_mod->links) && $get_mod->links != ''){ ?>
                                                            
                                                                                                <?php
                                                                                                $linkssp = explode(",",$get_mod->links);

                                                                                                foreach($linkssp as $links){
                                                                                                    echo "<a target='_blank' href='$links'>$links</a> <br/>";
                                                                                                }
                                                                                                ?>                                                            
                                                                                                <?php } else {
                                                                                                    ?>
                                                                                                     <a target="_blank" href="javascript:;">Submit project for this module</a>
                                                                                                    <?php
                                                                                                } ?>
                                                                                            
                                                                                        </div>                                        
                                                                                    </div>
                                                                                </div>
                                                                        </div>
                                                                            
                                                                            
                                                                        </div>
                                                                  </div>
                                                    
                                                                    
                                                    
                                                    
                                                                <?php
                                                                
                                                            }
                                                            
                                                            ?>
                                                </header>
                                            </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                    
                                    
                            <?php if(!empty($get_project)) {
                                $total_resources++;
                                $clstop = ''; $txtsum = 'Submit Project'; $txtsumcls = '';
                                if(isset($get_project->links) && $get_project->links != ''){
                                    $clstop = 'submittedproj'; $txtsum = 'Submitted'; $txtsumcls = 'linksumitted';
                                    $completed_resources++;
                                }
                                
                                ?>    
                                    
                            <div class="first_block blockcontent lastproj <?php echo $clstop; ?>" id="proj<?php echo $get_project->id; ?>">
                                <header>

                                    <span class="block_time">
                                        <i class="icon-time"></i> <?php echo $get_project->total_hrs; ?>+ Hours
                                    </span>
                                    <h2 class="h2main"><?php echo $get_project->title; ?></h2>

                                    <div class="descrp_main">
                                       <?php echo html_entity_decode($get_project->description); ?>
                                    
                                        <div class="block_resources">
                                                <div class="submit_buttons">
                                                    <a class="sub_btn submitproj <?php echo $txtsumcls; ?>" data-id="<?php echo $get_project->id; ?>" href="javascript:;"><?php echo $txtsum; ?></a>
                                                </div>
                                        
                                                <div class="block_main">
                                                    <span class="block_left">
                                                        <i class="icon-scales"></i>
                                                    </span>
                                                    <div class="block_info">                                                                                                    
                                                        <div class="lastfinal block_txt projlnk">
                                                            <?php if(isset($get_project->links) && $get_project->links != ''){ ?>
                                                            
                                                            <?php
                                                            $linkssp = explode(",",$get_project->links);
                                                           
                                                            foreach($linkssp as $links){
                                                                echo "<a target='_blank' href='$links'>$links</a> <br/>";
                                                            }
                                                            ?>                                                            
                                                            <?php } else {
                                                                ?>
                                                                 <a target="_blank" href="javascript:;">Complete final project</a>
                                                                <?php
                                                            } ?>
                                                        </div>                                        
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    
                                </header>
                            </div>
                                    
                            <?php } 
                            
                            
                                                        
                            if($completed_resources > 0){
                                $percent = floor(($completed_resources / $total_resources) * 100);
                            }
                            
                            
                            
                            
                            ?>         
                                    
                                <input type="hidden" name="percent_bar" id="percent_bar" value="<?php echo $percent; ?>" />
                                <input type="hidden" name="total_resources" id="total_resources" value="<?php echo $total_resources; ?>" />
                                <input type="hidden" name="completed_resources" id="completed_resources" value="<?php echo $completed_resources; ?>" />
                              

                                </div>                    


                            </div>

                        </div>

                    </div>

                </div>

                
                
                
                <!-- submit project box -->
                <div class="arrow_box submit_project" style="display: none;">
                    <span class="close-btn">
                        <span class="glyphicon glyphicon-remove btnclospop" aria-hidden="true"></span>
                        
                    </span>
                    <form>
                        <div class="controls">
                            <div class="row">
                            <div class="col-sm-10">
                                <input type="text" name="project_links" placeholder="Project Links (Use comma to separate if multiple)" class="wk_project_link form-control " data-project="220">
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:;" class="btn pink project-submit-btn sub_btn">Submit</a>
                            </div>
                            </div>
                            <div class="remove_project_ctn" style="display: none;">
                                <a href="javascript:void(0)" class="remove_project_link" target="_blank">Remove link</a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- submit project box -->
                
                
                
                <div class="fixed_header" style="display: none;">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="head_title">
                                    <h3><strong><?php echo $course->title; ?></strong></h3>
                                    by
                                    <span><?php echo $user->data->display_name; ?>, <?php echo $user->roles[0]; ?> </span>
                                </div>
                            </div>

                            <div class="col-sm-6">

                                <div class="progress_inner">
                                    <div class="bar_info">
                                        <span><label class="perint"><?php echo $percent; ?></label>% Complete</span>
                                        <div class="bar-progress">
                                            <div class="perdiv" class="bar wip" style="width:<?php echo $percent; ?>%"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>



                    </div>


                </div>
            </div>



            <?php
        }
    }
} else {
    ?>

    <div class="main-section">
        <div class="container">
            <h4>Course Detail</h4>
            <div class="bread_crumb">
                <ul>
                    <li title="All Courses List">
                        <a href="<?php echo site_url()."/".PAGE_SLUG ?>">All Courses</a> >> 
                    </li>
                    <li title="Course">
                        <?php echo $course->title; ?>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <div class="alert alert-danger">
                        <strong>Note: </strong>
                        You need to login access course.
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
}
?>