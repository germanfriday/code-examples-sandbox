<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
get_header(); ?>


<?php
if ($proud_slideshow_enable == 'Yes')
{
?>
  <div id="homeGallery">
    <div class="wrap">
      <div id="slideShow">
      
      <?php
    
    $i = 0;
    $x = 5;
    
    while ($i < $x)
    {
    
      $i++;
      unset($img);
      
      $img = 'proud_slider_image' . $i;
      
      if (strlen($$img) > 0)
      {
          ?><img src="<?php bloginfo('stylesheet_directory'); ?>/scripts/timthumb.php?h=300&amp;w=960&amp;zc=1&amp;src=<?php echo $$img; ?>" alt="" height="300" width="960" /><?php 
      }
    
    }
     
    ?>
      </div><!-- end #slideShow -->
      <div class="clear">&nbsp;</div>
    </div><!-- end .wrap -->
  </div><!-- end #homeGallery -->
<?php }
else
{
  ?><div class="wrap"><div class="sep">&nbsp;</div></div><?php 
}
 ?>

  <div id="content">
    <div class="wrap">
    
      <div class="column column-narrow">&nbsp;</div><!-- end .column-narrow -->
      
      <div class="column column-double column-content column-last">
      
<?php 

$pagid = $proud_home_page;

if ($pagid > 0)
{

query_posts("page_id=$pagid&showposts=1");

//The Loop
if ( have_posts() ) : while ( have_posts() ) : the_post();
?>

        <h1><?php the_title(); ?></h1>
      
      </div><!-- end .column-double -->
      <div class="clear">&nbsp;</div>

      <div class="column column-narrow">
      
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Homepage (Left)') ) : ?>&nbsp;<?php endif; ?>
        
      </div><!-- end .column-narrow -->
      
      <div class="column column-content">
      
        <?php the_content(''); ?>
        
<?php endwhile; endif;
}
else
{
  ?>

        <h1><?php bloginfo('name'); ?></h1>
      
      </div><!-- end .column-double -->
      <div class="clear">&nbsp;</div>

      <div class="column column-narrow">
      
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Homepage (Left)') ) : ?>&nbsp;<?php endif; ?>
        
      </div><!-- end .column-narrow -->
      
      <div class="column column-content">

        <p>Please select the active Homepage page from<br /><strong>Dashboard > ProudThemes > Theme Options > Homepage Settings</strong>.</p>

<?php } ?>

        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Homepage (Middle)') ) : ?>&nbsp;<?php endif; ?>
        
      </div><!-- end .column-content -->
      
      <div class="column column-narrow column-last">
      
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar: Homepage (Right)') ) : ?>&nbsp;<?php endif; ?>
      
      </div><!-- end .column-narrow -->

      <div class="clear">&nbsp;</div>

    </div><!-- end .wrap -->
  </div><!-- end #content -->
  
<?php get_footer(); ?>
