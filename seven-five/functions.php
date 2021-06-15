<?php

add_thickbox();

// unregister all default WP Widgets
function unregister_default_wp_widgets() {
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Links');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Search');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Text');
}
 
add_action('widgets_init', 'unregister_default_wp_widgets', 1);

// Include Custom Theme Widgets
include("widgets/posts.php");
include("widgets/flickr.php");
include("widgets/twitter.php");
include("widgets/delicious.php");
include("widgets/feed.php");

// Theme Location
define('THEME', get_bloginfo('template_url'), true);

// This Theme Uses Custom Menus
add_theme_support( 'nav-menus' );

// Add RSS Feed Links
add_theme_support( 'automatic-feed-links' );

// Theme Constants
define("THEME_PREFIX", "sevenfive_");

// Version Info for Upgrades
add_option("sevenfive_version", "1.5");

// The Admin Page
function sf_sevenfive_admin() {

	$option_fields = array();

	if ( $_GET['updated'] ) echo '<div id="message" class="updated fade"><p>Seven Five Options Saved.</p></div>';
	echo '<link rel="stylesheet" href="'.get_bloginfo('template_url').'/functions.css" type="text/css" media="all" />';
	echo '<link rel="stylesheet" href="'.get_bloginfo('template_url').'/scripts/colorpicker/style.css" type="text/css" media="all" />';
	echo '<script src="'.get_bloginfo('template_url').'/scripts/colorpicker/jquery.colorpicker.js" type="text/javascript"></script>';
	echo '<script src="'.get_bloginfo('template_url').'/scripts/colorpicker/jquery.eye.js" type="text/javascript"></script>';
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>

    <h2>Seven Five Theme Options</h2>

    <div class="metabox-holder">
    	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
    
        <div id="theme-options">
	        <div class="postbox-container left-column">
	            <?php 
	            	include("options/theme-support.php");
	            	include("options/custom-logo.php");
	            	include("options/custom-background-styles.php");
	            	include("options/footer-text.php");
	            	include("options/analytics-code.php");
	            	include("options/no-ie.php");
	            ?>
	        </div> <!-- postbox-container -->
	        
	       	<div class="postbox-container right-column">
	        	<?php
	        		include("options/featured-content.php");
	        		include("options/social-options.php");
	        	?>
	        </div> <!-- postbox-container -->
        </div> <!-- theme-options -->
        
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="<?php echo implode(",", $option_fields); ?>" />
        </form>
    </div> <!-- metabox-holder -->
</div> <!-- wrap -->

<?php
}

add_action('admin_menu', "sf_sevenfive_admin_init");

// Register Admin
function sf_sevenfive_admin_init()
{
	add_theme_page( "Seven Five Options", "Theme Options", 8, __FILE__, 'sf_sevenfive_admin');
}

/** Sidebar Widgets **/
if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'Home Page',
	'before_widget' => '<div id="%1$s" class="widget content-item %2$s">',
	'after_widget' => '</div></div>',
	'before_title' => '<div class="content-dets"><h3>',
	'after_title' => '</h3></div><div class="content-body">',
));
?>