<?php
	$option_fields[] = $enable_cbg = THEME_PREFIX . "enable_cbg";
	$option_fields[] = $background_img = THEME_PREFIX . "background_img";
	$option_fields[] = $background_fixed = THEME_PREFIX . "background_fixed";
	$option_fields[] = $background_color = THEME_PREFIX . "background_color";
	$option_fields[] = $background_vert = THEME_PREFIX . "background_vert";
	$option_fields[] = $background_horiz = THEME_PREFIX . "background_horiz";
	$option_fields[] = $background_repeat = THEME_PREFIX . "background_repeat";
	$option_fields[] = $background_text_color = THEME_PREFIX . "background_text_color";
	$option_fields[] = $background_link_color = THEME_PREFIX . "background_link_color";
	$option_fields[] = $background_link_hover_color = THEME_PREFIX . "background_link_hover_color";
	$option_fields[] = $background_nav_hover_color = THEME_PREFIX . "background_nav_hover_color";
	$option_fields[] = $background_border_color = THEME_PREFIX . "background_border_color";
?>

<div class="postbox">
    <h3>Custom Background, Border and Text Styles</h3>
    
    <div class="inside">
    	<p>
    		<label for="<?php echo $enable_cbg; ?>">
    	        <input class="checkbox" id="<?php echo $enable_cbg; ?>" type="checkbox" name="<?php echo $enable_cbg; ?>" value="true"<?php checked(TRUE, (bool) get_option($enable_cbg)); ?> /> <?php _e("Enable Custom Background, Border and Text Styles"); ?>
    	    </label>
    	</p>
    
    	<p>Use the options below to configure your site background as well as background text and border colors.</p>
		
		<p><a href="media-upload.php?post_id=22&amp;type=image&amp;TB_iframe=true&width=640&height=517" id="add_image" class="thickbox onclick" title="Add an Image">Upload</a> a custom background image, then enter "Link URL" below:</p>
		<p><input class="option-field" id="<?php echo $background_img; ?>" type="text" name="<?php echo $background_img; ?>" value="<?php echo get_option($background_img); ?>" /></p>
		
		<p>
			<label for="<?php echo $background_fixed; ?>">
		        <input class="checkbox" id="<?php echo $background_fixed; ?>" type="checkbox" name="<?php echo $background_fixed; ?>" value="true"<?php checked(TRUE, (bool) get_option($background_fixed)); ?> /> Fixed Background Position
		    </label>
		</p>
		
		<div class="table">
			<div class="row">
            	<div class="option">
                	<label class="config_level">
						<label>Background Color</label>
					</label>
				</div>
				
				<div class="option-select">	
					<script language="javascript">
					(function($){
						var initLayout = function() {
							$('#<?php echo $background_color; ?>').ColorPicker({
								onSubmit: function(hsb, hex, rgb, el) {
									$(el).val(hex);
									$(el).ColorPickerHide();
								},
								onBeforeShow: function () {
									$(this).ColorPickerSetColor(this.value);
								}
							})
							.bind('keyup', function(){
								$(this).ColorPickerSetColor(this.value);
							});
						};
						
						EYE.register(initLayout, 'init');
					})(jQuery)
					</script>
					
					#<input class="option-field-table" id="<?php echo $background_color; ?>" type="text" name="<?php echo $background_color; ?>" value="<?php echo get_option($background_color); ?>" />
				</div>
    		</div>
    		
    		<div class="row">
            	<div class="option">
                	<label class="config_level">
						<label>Background Vertical Alignment</label>
					</label>
				</div>

				<div class="option-select">	
					<?php $bgv = get_option(THEME_PREFIX . "background_vert"); ?>
				
					<select id="<?php echo $background_vert; ?>" name="<?php echo $background_vert; ?>">						
						<option value="top"<?php if ($bgv=="top") echo 'selected="selected"' ?>>Top</option>
						<option value="middle"<?php if ($bgv=="middle") echo 'selected="selected"' ?>>Middle</option>
						<option value="bottom"<?php if ($bgv=="bottom") echo 'selected="selected"' ?>>Bottom</option>
					</select>
				</div>
    		</div>
    		
    		<div class="row">
            	<div class="option">
                	<label class="config_level">
						<label>Background Horizontal Alignment</label>
					</label>
				</div>

				<div class="option-select">	
					<?php $bgh = get_option(THEME_PREFIX . "background_horiz"); ?>
				
					<select id="<?php echo $background_horiz; ?>" name="<?php echo $background_horiz; ?>">						
						<option value="left"<?php if ($bgh=="left") echo 'selected="selected"' ?>>Left</option>
						<option value="center"<?php if ($bgh=="center") echo 'selected="selected"' ?>>Center</option>
						<option value="right"<?php if ($bgh=="right") echo 'selected="selected"' ?>>Right</option>
					</select>
				</div>
    		</div>
    		
    		<div class="row">
            	<div class="option">
                	<label class="config_level">
						<label>Background Repeat</label>
					</label>
				</div>
				
				<div class="option-select">	
					<?php $bgr = get_option(THEME_PREFIX . "background_repeat"); ?>
				
					<select id="<?php echo $background_repeat; ?>" name="<?php echo $background_repeat; ?>">
						<option value="no-repeat"<?php if ($bgr=="no-repeat") echo 'selected="selected"' ?>>No Repeat</option>
						<option value="repeat-x"<?php if ($bgr=="repeat-x") echo 'selected="selected"' ?>>Repeat Horizontally</option>
						<option value="repeat-y"<?php if ($bgr=="repeat-y") echo 'selected="selected"' ?>>Repeat Vertically</option>
						<option value="repeat"<?php if ($bgr=="repeat") echo 'selected="selected"' ?>>Repeat Both</option>
					</select>
				</div>
    		</div>
    		
    		<div class="row">
    			<div class="option">
    		    	<label class="config_level">
    					<label>Site Text Color</label>
    				</label>
    			</div>
    			
    			<div class="option-select">	
    				<script language="javascript">
    				(function($){
    					var initLayout = function() {
    						$('#<?php echo $background_text_color; ?>').ColorPicker({
    							onSubmit: function(hsb, hex, rgb, el) {
    								$(el).val(hex);
    								$(el).ColorPickerHide();
    							},
    							onBeforeShow: function () {
    								$(this).ColorPickerSetColor(this.value);
    							}
    						})
    						.bind('keyup', function(){
    							$(this).ColorPickerSetColor(this.value);
    						});
    					};
    					
    					EYE.register(initLayout, 'init');
    				})(jQuery)
    				</script>
    				
    				#<input class="option-field-table" id="<?php echo $background_text_color; ?>" type="text" name="<?php echo $background_text_color; ?>" value="<?php echo get_option($background_text_color); ?>" />
    			</div>
    		</div>
    		
    		<div class="row">
    			<div class="option">
    		    	<label class="config_level">
    					<label>Link Color</label>
    				</label>
    			</div>
    			
    			<div class="option-select">	
    				<script language="javascript">
    				(function($){
    					var initLayout = function() {
    						$('#<?php echo $background_link_color; ?>').ColorPicker({
    							onSubmit: function(hsb, hex, rgb, el) {
    								$(el).val(hex);
    								$(el).ColorPickerHide();
    							},
    							onBeforeShow: function () {
    								$(this).ColorPickerSetColor(this.value);
    							}
    						})
    						.bind('keyup', function(){
    							$(this).ColorPickerSetColor(this.value);
    						});
    					};
    					
    					EYE.register(initLayout, 'init');
    				})(jQuery)
    				</script>
    				
    				#<input class="option-field-table" id="<?php echo $background_link_color; ?>" type="text" name="<?php echo $background_link_color; ?>" value="<?php echo get_option($background_link_color); ?>" />
    			</div>
    		</div>
    		
    		<div class="row">
    			<div class="option">
    		    	<label class="config_level">
    					<label>Link Hover Color</label>
    				</label>
    			</div>
    			
    			<div class="option-select">	
    				<script language="javascript">
    				(function($){
    					var initLayout = function() {
    						$('#<?php echo $background_link_hover_color; ?>').ColorPicker({
    							onSubmit: function(hsb, hex, rgb, el) {
    								$(el).val(hex);
    								$(el).ColorPickerHide();
    							},
    							onBeforeShow: function () {
    								$(this).ColorPickerSetColor(this.value);
    							}
    						})
    						.bind('keyup', function(){
    							$(this).ColorPickerSetColor(this.value);
    						});
    					};
    					
    					EYE.register(initLayout, 'init');
    				})(jQuery)
    				</script>
    				
    				#<input class="option-field-table" id="<?php echo $background_link_hover_color; ?>" type="text" name="<?php echo $background_link_hover_color; ?>" value="<?php echo get_option($background_link_hover_color); ?>" />
    			</div>
    		</div>
    		
    		<div class="row">
    			<div class="option">
    		    	<label class="config_level">
    					<label>Menu Background Color</label>
    				</label>
    			</div>
    			
    			<div class="option-select">	
    				<script language="javascript">
    				(function($){
    					var initLayout = function() {
    						$('#<?php echo $background_nav_hover_color; ?>').ColorPicker({
    							onSubmit: function(hsb, hex, rgb, el) {
    								$(el).val(hex);
    								$(el).ColorPickerHide();
    							},
    							onBeforeShow: function () {
    								$(this).ColorPickerSetColor(this.value);
    							}
    						})
    						.bind('keyup', function(){
    							$(this).ColorPickerSetColor(this.value);
    						});
    					};
    					
    					EYE.register(initLayout, 'init');
    				})(jQuery)
    				</script>
    				
    				#<input class="option-field-table" id="<?php echo $background_nav_hover_color; ?>" type="text" name="<?php echo $background_nav_hover_color; ?>" value="<?php echo get_option($background_nav_hover_color); ?>" />
    			</div>
    		</div>
    		
    		<div class="row last">
    			<div class="option">
    		    	<label class="config_level">
    					<label>Border Line Color</label>
    				</label>
    			</div>
    			
    			<div class="option-select">	
    				<script language="javascript">
    				(function($){
    					var initLayout = function() {
    						$('#<?php echo $background_border_color; ?>').ColorPicker({
    							onSubmit: function(hsb, hex, rgb, el) {
    								$(el).val(hex);
    								$(el).ColorPickerHide();
    							},
    							onBeforeShow: function () {
    								$(this).ColorPickerSetColor(this.value);
    							}
    						})
    						.bind('keyup', function(){
    							$(this).ColorPickerSetColor(this.value);
    						});
    					};
    					
    					EYE.register(initLayout, 'init');
    				})(jQuery)
    				</script>
    				
    				#<input class="option-field-table" id="<?php echo $background_border_color; ?>" type="text" name="<?php echo $background_border_color; ?>" value="<?php echo get_option($background_border_color); ?>" />
    			</div>
    		</div>
    		
    		<div class="clearfix"></div>
    	</div>
    	
        <p class="submit">
			<input type="submit" class="button" value="Save Changes" />
		</p>
    </div> <!-- inside -->
</div> <!-- postbox -->