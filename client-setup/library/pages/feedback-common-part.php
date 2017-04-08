<style>
    .image_c{height:20px;width:20px;cursor:pointer;}
    .left_f_c{width:20%;float:left;font-weight: bold;}
    .right_f_c{width:60%;float:left;}
    .orange_btn_c{background: #FB6800!important;color:white!important;font-weight: bold!important;}
    .success_c{color:green;text-align: center;font-weight: bold;font-size:17px;}
    .required{color:black;}
</style>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.validate.js'></script>
<div class="accoSet">
    <h2 class="fulllist">Feedback to <?php echo $feed_back_to; ?> <span style="margin-left: 20px;">Order ID: #<?php echo $feedback_order_id; ?></span></h2>
</div>

<div style="padding: 20px;line-height: 23px;">
    <?php echo $succ_msg; ?>
    <form id="feedback_Frm" action="" method="post">
        
        <div class="left_f_c"><?php echo $feed_back_to; ?> Name:</div>
        <div class="right_f_c">
            <?php 
            if($feed_back_to == 'Writer'){
            echo full_name($feedback_order_info->writer_id); 
            } else {
                echo full_name($feedback_order_info->user_id);
            }
            ?>
        </div>   
       
        <div class="clear_both"></div>
        <?php
        if ($succ_msg == '') {
            ?>
            <div class="left_f_c">Quality Level:</div>
            <div class="right_f_c">
                <?php for ($level = 5; $level >= 1; $level--) { ?>
                    <input style="cursor: pointer;" type="radio" name="rating" class="required" value="<?php echo $level; ?>"> <?php echo star_image_loop($level); ?>
                    <div class="clear_10px"></div>
                <?php } ?>
            </div>
            <div class="clear_both"></div>
            <div class="left_f_c">Your Comment:</div>
            <div class="right_f_c">
                <textarea name="comments" class="required" style="width:100%;height:70px;"></textarea>
            </div>
            <div class="clear_both"></div>
            <div class="left_f_c">&nbsp;</div>
            <div class="right_f_c">
                <input type="submit" name="submit_feedback_btn" class="orange_btn_c" value="Submit Feedback">
            </div>

            <?php
        } else {
            ?>
            <div class="left_f_c">Quality Level:</div>
            <div class="right_f_c">
                   <?php echo star_image_loop($_POST['rating']); ?>
                    <div class="clear_10px"></div>
            </div>
            <div class="clear_both"></div>
            <div class="left_f_c">Your Comment:</div>
            <div class="right_f_c">
               <?php echo $_POST['comments'];?>
            </div>
            <div class="clear_both"></div>
            <div style="text-align: center;">
            <input onclick="document.location.href='<?php echo $back_location; ?>'" type="button" class="orange_btn_c" value="Back To Order List">
            </div>
                <?php
        }
        ?>
    </form>
</div>
<script>
    jQuery('#feedback_Frm').validate();
</script>