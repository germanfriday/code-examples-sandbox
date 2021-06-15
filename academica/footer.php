<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
} 
?>

  <div id="footer">
    <div class="wrap">
    
        <div id="footerColumn"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Right') ) : ?>&nbsp;<?php endif; ?></div>
        
        <?php wp_nav_menu( array('container' => '', 'container_class' => '', 'menu_class' => '', 'depth' => '1', 'menu_id' => 'menufooter', 'sort_column' => 'menu_order', 'theme_location' => 'secondary' ) ); ?>
        <p class="copy">Copyright &copy; <?php echo date("Y",time()); ?> <?php bloginfo('name'); ?>. All rights reserved. Theme by <a href="http://www.proudthemes.com" rel="external" target="_blank">ProudThemes.com</a></p>
      
    <div class="clear">&nbsp;</div>
    
    </div><!-- end .wrap -->
  </div><!-- end #footer -->

</div><!-- end #wrap -->

<?php wp_footer(); ?>
<?php if ($proud_misc_analytics != '' && $proud_misc_analytics_show == 'Yes')
{
  echo stripslashes($proud_misc_analytics);
} ?>
</body>
</html>
