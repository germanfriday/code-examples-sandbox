;(function($){
	
	window.MSLayerElement = function(){
		
		//this.$element = $('<div></div>').addClass('layer-element');
		this.$cont	  = $('<div></div>').addClass('layer-cont');//.append(this.$element);
			
		this.start_anim = {
			name		: 'fade',
			duration	: 1000,
			ease 		: 'linear',
			delay		: 0		
		};
		
		this.end_anim = {
			duration	: 1000,
			ease 		: 'linear'
		};
		
		this.type = 'text'; // video , image
		
		this.swipe 		= true;
		this.resizable 	= true;
		this.minWidth 	= -1;
			
		this.__cssConfig = [
			'margin-top' 	,      'padding-top'	,
			'margin-bottom'	,      'padding-left'	,
			'margin-right'	,      'padding-right'	,
			'margin-left'	,      'padding-bottom' ,
			
			'left'			,       'right'			, 
			'top'			,       'bottom'		,
			'font-size' 	,  		'line-height'	,
			'width'			,		'height'
		];
		
		this.baseStyle = {};
	};
	
	var p = MSLayerElement.prototype;
	
	/*-------------- METHODS --------------*/	
	p.__playAnimation = function(animation , css){	
		var options = {};
		//if(animation.delay > 0) options.delay = animation.delay;
		if(animation.ease)		options.ease = animation.ease;
		
		this.show_tween = CTween.animate(this.$element, animation.duration , css , options);					
	};
	
	p._randomParam = function(value){
		var min = Number(value.slice(0,value.indexOf('|')));
		var max = Number(value.slice(value.indexOf('|')+1));
		
		return min + Math.random() * (max - min);
	};
	
	p._parseEff = function(eff_name){
		
		var eff_params = [];
		
		if(eff_name.indexOf('(') !== -1){
			var temp   = eff_name.slice(0 , eff_name.indexOf('(')).toLowerCase();
			var	value;
			
			eff_params = eff_name.slice(eff_name.indexOf('(') + 1 , -1).replace(/\"|\'|\s/g , '').split(',');
			eff_name   = temp;
		
			for(var i = 0 , l = eff_params.length; i < l ; ++i){
				value = eff_params[i];
				
				if(value in MSLayerEffects.presetEffParams)
					value = MSLayerEffects.presetEffParams[value];
				
				eff_params[i] = value;
			}
		}
		
		return {eff_name:eff_name , eff_params:eff_params};
	};
	
	p._parseEffParams = function(params){
		var eff_params = [];
		for(var i = 0 , l = params.length; i < l ; ++i){
			var value = params[i];
			if(typeof value === 'string' && value.indexOf('|') !== -1) value = this._randomParam(value);
			
			eff_params[i] = value;
		}
		
		return eff_params;
	};
	
	p._checkPosKey = function(key , style){		
		if(key === 'left' && !(key in this.baseStyle) && 'right' in this.baseStyle){
			 style.right = -parseInt(style.left) + 'px';
			 delete style.left;
			 return true;
		}
		
		if(key === 'top'  && !(key in this.baseStyle) && 'bottom' in this.baseStyle){
			style.bottom = -parseInt(style.top) + 'px';
			delete style.top;
			return true;
		} 
		
		return false;
	};
	
	
	/*
	---------------------------------------------------
	 					Public Methods
	---------------------------------------------------
	*/	
	p.setStartAnim = function(anim){ 
		$.extend(this.start_anim , anim); $.extend(this.start_anim  , this._parseEff(this.start_anim.name)); 
		this.$element.css('visibility' , 'hidden');
	};
	p.setEndAnim   = function(anim){ $.extend(this.end_anim   , anim ); };
	
	p.create = function(){
		this.$element.css('display' , 'none');
		this.$element.removeAttr('data-delay')
					 .removeAttr('data-effect')
					 .removeAttr('data-duration')
					 .removeAttr('data-type');
					 
		if(!this.end_anim.name)		this.end_anim.name  = this.start_anim.name;
		if(this.end_anim.time)		this.autoHide = true;//this.end_anim.delay = this.slide.delay * 1000 - this.end_anim.duration;
		
		$.extend(this.end_anim  , this._parseEff(this.end_anim.name));
		
	};
	
	p.init = function(){
		//if(this.initialized) return;
		this.initialized = true;

		var value;
		
		this.$element.css('visibility' , '');
		// store initial layer styles
		for(var i = 0 , l = this.__cssConfig.length; i < l ; i ++){
			var key = this.__cssConfig[i];
			if( this.type === 'text' && key === 'width'){ // in some browsers using computed style for width in text layer causes unexpected word wrapping 
				value = this.$element[0].style.width;
			} else {
				value = this.$element.css(key);
			}
			if(value != 'auto' && value != "" && value != "normal") 
				this.baseStyle[key] = parseInt(value);
		}
	};
	
	p.locate = function(){
		
		var layer_cont 	= this.slide.$layers;
		var width 		= parseFloat(layer_cont.css('width'));
		
		this.visible(this.minWidth < width);
		
		if(!this.resizable) return;
		
		this.factor 		= width / this.slide.slider.options.width;
		
		for(var key in this.baseStyle)
			this.$element.css(key , this.baseStyle[key] * this.factor + 'px');
		
	};
	
	p.start = function(){

		if(this.isShowing) return;
		this.isShowing = true;
		
		var key , base;
		
		// reads css value form LayerEffects
		MSLayerEffects.rf = this.factor;
		var effect_css = MSLayerEffects[this.start_anim.eff_name].apply(null , this._parseEffParams(this.start_anim.eff_params));
		
		// checkes effect css and defines TO css values
		var start_css_eff = {};
			
		for(key in effect_css){
			if(this._checkPosKey(key , effect_css)) continue;
			if(MSLayerEffects.defaultValues[key] != null)
				start_css_eff[key] = MSLayerEffects.defaultValues[key];
			if(key in this.baseStyle){
				base = this.baseStyle[key];
				effect_css[key] = base + parseFloat(effect_css[key]) + 'px';
				start_css_eff[key] = base + 'px';
			}
			this.$element.css(key , effect_css[key]);
		}
		
		var that = this;
		clearTimeout(this.to);
		this.to = setTimeout(function(){
			that.$element.css('display' , '');
			that.__playAnimation(that.start_anim , start_css_eff);
		} , that.start_anim.delay || 0.01);
		
		
		this.cl_to = setTimeout(function(){
			that.show_cl = true;
		},(this.start_anim.delay || 0.01) + this.start_anim.duration);
		
		if(this.autoHide){
			clearTimeout(this.hto);
			this.hto = setTimeout(function(){that.hide();} , that.end_anim.time );
		}
	};
	
	p.hide = function(){
		this.isShowing = false;
		
		// reads css value form LayerEffects
		var effect_css = MSLayerEffects[this.end_anim.eff_name].apply(null , this._parseEffParams(this.end_anim.eff_params));
		
		for(key in effect_css){
			
			if(this._checkPosKey(key , effect_css)) continue;
			
			if( key === window._jcsspfx + 'TransformOrigin' ){
				this.$element.css(key , effect_css[key]);
			}

			if(key in this.baseStyle){
				effect_css[key] = this.baseStyle[key] + parseFloat(effect_css[key]) +  'px';
			}
				
		}
		
		this.__playAnimation(this.end_anim , effect_css);
		
		clearTimeout(this.to);
		clearTimeout(this.hto);		
		clearTimeout(this.cl_to);		
	};
	
	p.reset = function(){
		this.isShowing = false;
		//this.$element.css(window._csspfx + 'animation-name', ''	);
		this.$element[0].style.display = 'none';
		this.$element.css('opacity', '100');
		this.$element[0].style['transitionDuration'] = '0ms';
		
		if(this.show_tween)
			this.show_tween.stop(true);
		
		clearTimeout(this.to);
		clearTimeout(this.hto);
	};
		
	p.destroy = function(){
		this.reset();
		this.$element.remove();
		this.$cont.remove();
	};
	
	p.visible = function(value){
		if(this.isVisible == value) return;
		
		this.isVisible = value;
		
		this.$element.css('display' , (value ? '' : 'none'));		
	};
	
})(jQuery);
