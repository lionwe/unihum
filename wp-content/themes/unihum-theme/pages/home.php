<?php
/*
Template Name: Home
*/
?>

<?php get_header(); ?>

<main id="home">
    <?php get_template_part('sections/home/hero'); ?>
    <?php get_template_part('sections/home/why-now'); ?>
    <?php get_template_part('sections/home/created'); ?>

</main>

<?php get_footer(); ?>
