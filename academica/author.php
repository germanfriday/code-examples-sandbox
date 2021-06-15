<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
$dateformat = get_option('date_format');
$timeformat = get_option('time_format');
get_header(); 

		if(get_query_var('author_name')) :
		$curauth = get_userdatabylogin(get_query_var('author_name'));
		else :
		$curauth = get_userdata(get_query_var('author'));
		endif;
?>


  <div id="content">
    <div class="wrap">
    
      <div class="sep sepinside">&nbsp;</div>
    
      <div class="column column-narrow">&nbsp;</div><!-- end .column-narrow -->
      
      <div class="column column-double column-content column-last">
      
        <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	      <h1>Author Archive: <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->display_name; ?></a></h1>
      
      </div><!-- end .column-double -->
      <div class="clear">&nbsp;</div>

      <div class="column column-narrow">
      
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Archive Pages') ) : ?>&nbsp;<?php endif; ?>
        
      </div><!-- end .column-narrow -->
      
      <div class="column column-double column-last">
      
      <div class="posts">

  <?php
  wp_reset_query();

  $i = 0;
  if (have_posts()) : while (have_posts()) : the_post();
  $i++;
  update_post_caches($posts);
  ?>
      
        <div class="post">
        
        <?php 
        unset($img);

				if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail() ) {
						$attachedFile = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), '' );
            $img = $attachedFile[0]; 
						}
				
				else{ 
				 
          if ($proud_cf_use == 'Yes')
          {
            $img = get_post_meta($post->ID, $proud_cf_photo, true);
          } // if CF used
          else
          {
            if (!$img)
            {
              $img = catch_that_image($post->ID);
            }
          } // if CF not used
        }
          
          if ($img)
          {
            ?>
            <div class="thumb"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?h=80&amp;w=120&amp;zc=1&amp;src=<?php echo $img ?>" alt="<?php the_title(); ?>" class="bordered" /></a></div>
            <?php 
          }

    ?>
          <h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
          <p class="postmetadata"><?php the_time("$dateformat $timeformat"); ?> / <a href="<?php the_permalink() ?>#commentspost" title="Jump to the comments" rel="nofollow"><?php comments_number('no comments','1 comment','% comments'); ?></a><?php edit_post_link( __('EDIT'), ' / ', ''); ?></p>
          <div class="info"><?php the_excerpt(); ?></div>
        
          <div class="clear">&nbsp;</div>  

        </div><!-- end .post -->

        <?php endwhile; 
        
        else:
        
        ?>
        <h2>Sorry, there are no posts in this category.</h2>
        <?php
        
        endif; ?>

      <div class="navigation">
      <p><span class="older"><?php next_posts_link('&lt; older entries') ?></span>  <span class="latest"><?php previous_posts_link('newer entries &gt;') ?></span></p>
      </div>

      </div><!-- end .posts -->
      
      </div><!-- end .column-content -->
      
      <div class="clear">&nbsp;</div>

    </div><!-- end .wrap -->
  </div><!-- end #content -->

<?php get_footer(); ?>