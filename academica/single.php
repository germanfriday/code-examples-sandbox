<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
$dateformat = get_option('date_format');
$timeformat = get_option('time_format');
$layout = get_post_meta($post->ID, 'template', true);
get_header(); ?>


  <div id="content">
    <div class="wrap">
    
      <div class="sep sepinside">&nbsp;</div>
    
      <?php wp_reset_query(); 
      if (have_posts()) : while (have_posts()) : the_post(); ?>

      <?php if ($layout != 'full') { ?><div class="column column-narrow">&nbsp;</div><!-- end .column-narrow --><?php } ?>
      
      <div class="column <?php if ($layout != 'full') { echo"column-double";} else {echo"column-full";} ?> column-content column-last">
      
        <h1><?php the_title(); ?></h1>
      
      </div><!-- end .column-double -->
      <div class="clear">&nbsp;</div>

      <?php if ($layout != 'full') { ?>
      <div class="column column-narrow">
      
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Posts (Left)') ) : ?>&nbsp;<?php endif; ?>
        
      </div><!-- end .column-narrow -->
      <?php } ?>
      
      <div class="column <?php if ($layout == 'double') {echo"column-double column-last ";} if ($layout == 'full') {echo"column-full column-last ";} ?>column-content single">
      
        <?php the_content(''); ?>
        <?php wp_link_pages(array('before' => '<p class="pages"><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
        <?php the_tags( '<p class="tags"><strong>Tags:</strong> ', ', ', '</p>'); ?>
        <p class="postmetadata">By <?php the_author_posts_link(); ?> in <span class="category"><?php the_category(', '); ?></span> on <span class="datetime"><?php the_time("$dateformat"); ?><?php edit_post_link( __('Edit &raquo;'), ' | ', ''); ?></span></p>
        <?php comments_template(); ?>
      
      </div><!-- end .column-content -->

      <?php if ($layout != 'double' && $layout != 'full') { ?><div class="column column-narrow column-last">
      
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Posts (Right)') ) : ?>&nbsp;<?php endif; ?>
      
      </div><!-- end .column-narrow -->
      <?php } ?>

<?php endwhile; endif; ?>

      <div class="clear">&nbsp;</div>

    </div><!-- end .wrap -->
  </div><!-- end #content -->

<?php get_footer(); ?>