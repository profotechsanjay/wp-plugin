<?php
include_once 'common.php';
global $wpdb;


$courses = $wpdb->get_results
        (
        $wpdb->prepare
                (
                "SELECT c.*, (SELECT count(id) as total FROM " . enrollment()." WHERE course_id = c.id) as enrolledby FROM " . courses()." c ORDER BY c.ord", ""
        )
);

$base_url = site_url();
$slug = PAGE_SLUG;
?>
<div class="contaninerinner">     
     <ul class="nav nav-tabs tabcustom">
        <li><a href="admin.php?page=settings">Settings</a></li>
        <li class="active"><a href="javascript:;">Image By Course</a></li>                    
    </ul>
    <div class="panel tab-content">
               
        <div class="panel-body">
            
            <table class="table table-bordered table-striped table-hover" id="coursesimages" >
                <thead>
                    <tr>
                        <th style="width: 4%;">SNo</th>
                        <th style="width: 15%;">Course</th>
                        <th style="width: 20%;">Image</th>                        
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($courses as $course) {
                                                
                        $title = "$course->title";
                        $img = "<i>Not uploaded</i>"; $path = '';
                        if($course->imgpath != ''){
                            $path = TR_COUNT_PLUGIN_URL.$course->imgpath;
                            $img = "<a target='_blank' href='$course->link'><img src='$path' /></a>";
                        }
                        
                        
                        ?>
                            <tr class="rowmod" data-id="<?php echo $course->id; ?>">
                                <td><?php echo $course->ord; ?></td> 
                                <td><?php echo $title; ?></td>
                                <td class="imgtd" data-img="<?php echo $path; ?>" data-link="<?php echo $course->link; ?>"><?php echo $img; ?></td>                                
                                <td class="actiontd acttd">
                                    <a data-id="<?php echo $course->id; ?>" href="javascript:;" class="uploadcourseimg btn btn-primary" title="Upload Course Image">Upload Course Image</a>                                    
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


<div id="image_dialog" class="modal fade modealrealodonclose">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Video & Image</h4>
            </div>
            <div class="modal-body">
                
                <form action="#" method="post" id="addimg" name="addimg" class="form-horizontal">
                                        
                    <input type="hidden" id="course_id" name="course_id" value="0" />
                                                            
                    <div class="form-group">   
                        <label for="title" class="col-lg-3 control-label">Upload Image <br/>(Image Dimension: 275 X 500):</label>      
                        <div class="col-lg-8">
                            <input type="file" class="form-control" name="responseimg" id="responseimg" accept="image/*" />                            
                            <div class="uploadedimg">                                
                            </div>                                                        
                        </div>
                        
                    </div>
                    <div class="form-group">   
                        <label for="title" class="col-lg-3 control-label">Url (Call to action)  :</label>      
                        <div class="col-lg-8">
                            <input type="text" class="form-control" url='true' value="" id="urlimg" name="urlimg" placeholder="Enter URL" />
                        </div>
                        
                    </div>
                    
                    
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" onclick='$("#addimg").submit();' class="btn btn-primary" >Submit</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>
