;(function($){
	
	"use strict";
	
	var BaseControl = function(){
		this.options = {
			prefix:'ms-',
			autohide:true,
			overVideo:true	
		};
	};
	
	var p = BaseControl.prototype;
	
	/* -------------------------------- */
	
	p.slideAction = function(slide){

	};
	
	p.setup = function(){		
		this.cont = this.options.insertTo ? $(this.options.insertTo) : this.slider.$controlsCont;
		if(!this.options.overVideo) this._hideOnvideoStarts();

		if(this.options.hideUnder){
			//this.slider.api.addEventListener(MSSliderEvent.RESIZE, this.onSliderResize, this);
			$(window).bind('resize', {that:this}, this.onResize);
			this.onResize();
		}
	};

	/**
	 * hide control if width of slider changes to lower that specified value [hideUnder]
	 * @since 1.5.7
	 * @protected
	 */
	p.onResize = function(event){
		var that = (event && event.data.that) || this;
		var w = window.innerWidth;
		if( w <= that.options.hideUnder && !that.detached ){
			that.$element.css('display', 'none');
			that.detached = true;
			if( that.onDetach ){
				that.onDetach();
			}
		}else if( w >= that.options.hideUnder && that.detached ){
			that.detached = false;
			that.$element.css('display', '');
			that.visible();
			if( that.onAppend ){
				that.onAppend();
			}
		}
	};
	
	p.create = function(){
		var that = this;
		if(this.options.autohide && !window._touch){
			
			this.hide(true);
			
			this.slider.$controlsCont.mouseenter(function(){
				if(!that._disableAH && !that.mdown)that.visible();
				that.mleave = false;
			}).mouseleave(function(){
				that.mleave = true;
				if(!that.mdown)that.hide();
			}).mousedown(function(){
				that.mdown = true;
			});
			
			$(document).mouseup(function(){
				if(that.mdown && that.mleave)that.hide();
				that.mdown = false;
			});
		}

	};
	
	p._hideOnvideoStarts = function(){
		var that = this;
		slider.api.addEventListener(MSSliderEvent.VIDEO_PLAY , function(){
   			 that._disableAH = true;
   			 that.hide();
		});
		 
		slider.api.addEventListener(MSSliderEvent.VIDEO_CLOSE , function(){
		     that._disableAH = false;
   			 that.visible();
		});
	};
	
	p.hide = function(fast){
		if(fast) this.$element.css('opacity' , 0);
		else	 CTween.fadeOut(this.$element , 400 , false);
		
		this.$element.addClass('ms-ctrl-hide');
	};
	
	p.visible = function(){
		if(this.detached) return;
		CTween.fadeIn(this.$element , 400 );
		this.$element.removeClass('ms-ctrl-hide');
	};
	

	
	p.destroy = function(){
		if(this.options.hideUnder){
			//this.slider.api.removeEventListener(MSSliderEvent.RESIZE, this.onResize, this);
			$(window).unbind('resize', this.onResize);
		}
	};
	
	window.BaseControl = BaseControl;
	
})(jQuery);
