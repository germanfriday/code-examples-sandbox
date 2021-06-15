<?php 
/*
Template Name: Full Width
*/

global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
$dateformat = get_option('date_format');
$timeformat = get_option('time_format');
get_header(); ?>

  <div id="content">
    <div class="wrap">
    
      <div class="sep sepinside">&nbsp;</div>
    
      <?php wp_reset_query(); 
      if (have_posts()) : while (have_posts()) : the_post(); ?>

      <div class="column column-content column-full column-last">
      
        <h1><?php the_title(); ?></h1>
      
      </div><!-- end .column-full -->
      <div class="clear">&nbsp;</div>

      <div class="column column-content column-full column-last single">
      
        <?php the_content(''); ?>
        <?php wp_link_pages(array('before' => '<p class="pages"><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
        <p class="postmetadata"><?php edit_post_link( __('EDIT'), '', ''); ?></p>
        <?php comments_template(); ?>
      
      </div><!-- end .column-content -->

<?php endwhile; endif; ?>

      <div class="clear">&nbsp;</div>

    </div><!-- end .wrap -->
  </div><!-- end #content -->

<?php get_footer(); ?>
