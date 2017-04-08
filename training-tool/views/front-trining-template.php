<?php
/*
Template Name: Training Tool
*/

get_header(); 
?>
<div class="container templatemain">
    <?php
        echo do_shortcode("[course_detail]");
    ?>
    </div>
<?php get_footer(); ?>