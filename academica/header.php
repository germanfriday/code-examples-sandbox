<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' | '; } ?><?php bloginfo('name'); if(is_home()) { echo ' | '; bloginfo('description'); } ?></title>
<?php if (is_single() || is_page() ) : if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<meta name="description" content="<?php the_excerpt_rss(); ?>" />
<?php csv_tags(); ?>
<?php endwhile; endif; elseif(is_home()) : ?>
<meta name="description" content="<?php bloginfo('description'); ?>" />
<?php endif; ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url');?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_enqueue_script('jquery');  ?>
<?php wp_head(); ?>
<script type='text/javascript'>  
jQuery(document).ready(function() {   
jQuery("#menuhead ul").css({display: "none"}); // Opera Fix  
jQuery("#menuhead li").hover(function(){  
        jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).show(268);  
        },function(){  
        jQuery(this).find('ul:first').css({visibility: "hidden"});  
        });  
});  
</script>
     
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/nivo.slider.pack.js" type="text/javascript"></script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/nivo.slider.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/js/nivo.css" />
</head>

<body>

<div id="wrap">
  
  <div id="mainNav">
    <div class="wrap">

      <?php wp_nav_menu( array('container' => '', 'container_class' => '', 'menu_class' => '', 'menu_id' => 'menuhead', 'sort_column' => 'menu_order', 'theme_location' => 'primary' ) ); ?>
      
    </div><!-- end .wrap -->
  </div><!-- end #mainNav -->
  
  <div id="crumbs">
    <div class="wrap">
      <p><?php if(function_exists('bcn_display'))
{
	echo"You are here: "; bcn_display();
}
?></p>
    </div><!-- end .wrap -->
  </div>

  <div id="header">
    <div class="wrap">
      <div id="logo"><a href="<?php echo get_option('home'); ?>"><?php if ($proud_misc_logo_path) { ?><img src="<?php echo "$proud_misc_logo_path";?>" alt="<?php bloginfo('name'); ?>" /><?php } else { ?><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>" /><?php } ?></a></div>
      <div id="search"> 
        <form method="get" action="<?php echo get_option('home'); ?>">
         <input type="text" name="s" id="setop" onblur="if (this.value == '') {this.value = 'search';}" onfocus="if (this.value == 'search') {this.value = '';}" value="search" />
         <input type="submit" id="searchsubmittop" class="submit" value="search" />
        </form>  
      </div><!-- end #search -->
      <?php include(TEMPLATEPATH . '/inc_social.php'); // calling social block ?>
      <div class="clear">&nbsp;</div>
    </div><!-- end .wrap -->
  </div><!-- end #header -->