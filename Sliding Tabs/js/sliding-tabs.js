// the Sliding Tabs mootools plugin is a creation of Jenna “Blueberry” Fox! Who
// releases it as public domain as of the 16th of September, 2007. More info and
// up to date downloads and demo's at: http://creativepony.com/journal/scripts/sliding-tabs/
// version: 1.4.2

var SlidingTabs = new Class({
	options: {
		startingSide: false, // sets the slide to start on, either an element or an id 
		activeButtonClass: 'active', // class to add to selected button
		activationEvent: 'click', // you can set this to ‘mouseover’ or whatever you like
		wrap: true, // calls to previous() and next() should wrap around?
		slideEffect: { // options for effect used to animate the sliding, see Fx.Base in mootools docs
			duration: 400 // half a second
		}
	},
	current: null, // zero based current pane number, read only
	buttons: false,
	outerSlidesBox: null,
	innerSlidesBox: null,
	panes: null,
	fx: null,
	
	
	initialize: function(buttonContainer, slideContainer, options) {
		if (buttonContainer) { this.buttons = $(buttonContainer).getChildren(); }
		this.outerSlidesBox = $(slideContainer);
		this.innerSlidesBox = this.outerSlidesBox.getFirst();
		this.panes = this.innerSlidesBox.getChildren();
		
		this.setOptions(options);
		
		this.fx = new Fx.Scroll(this.outerSlidesBox, this.options.slideEffect);
		
		// set up button highlight
		this.current = this.options.startingSlide ? this.panes.indexOf($(this.options.startingSlide)) : 0;
		if (this.buttons) { this.buttons[this.current].addClass(this.options.activeButtonClass); }
		
		// add needed stylings
		this.outerSlidesBox.setStyle('overflow', 'hidden');
		this.panes.each(function(pane, index) {
			pane.setStyle('float', 'left');
			pane.setStyle('width', this.outerSlidesBox.getStyle('width'));
		}.bind(this));
		
		// stupidness to make IE work - it boggles the mind why this has any effect
		// maybe it's something to do with giving it layout?
		this.innerSlidesBox.setStyle('float', 'left');
		
		this.innerSlidesBox.setStyle(
			'width', (this.outerSlidesBox.offsetWidth.toInt() * this.panes.length) + 'px'
		);
		
		if (this.options.startingSlide) this.fx.toElement(this.options.startingSlide);
		
		// add events to the buttons
		if (this.buttons) this.buttons.each( function(button) {
		  button.addEvent(this.options.activationEvent, this.buttonEventHandler.bindWithEvent(this, button));
		}.bind(this));
	},
	
	
	changeTo: function(element) {
		var event = { cancel: false, target: $(element) };
		this.fireEvent('change', event);
		if (event.cancel == true) { return; };
		
		if (this.buttons) { this.buttons[this.current].removeClass(this.options.activeButtonClass); };
		this.current = this.panes.indexOf($(event.target));
		if (this.buttons) { this.buttons[this.current].addClass(this.options.activeButtonClass); };
		this.fx.stop();
		this.fx.toElement(event.target);
	},
	
	// Handles a click
	buttonEventHandler: function(event, button) {
		if (event.target == this.buttons[this.current]) return;
		this.changeTo(this.panes[this.buttons.indexOf($(button))]);
	},
	
	next: function() {
		var next = this.current + 1;
		if (next == this.panes.length) {
			if (this.options.wrap == true) { next = 0 } else { return }
		}
		
		this.changeTo(this.panes[next]);
	},
	
	previous: function() {
		var prev = this.current - 1
		if (prev < 0) {
			if (this.options.wrap == true) { prev = this.panes.length - 1 } else { return }
		}
		
		this.changeTo(this.panes[prev]);
	}
});

SlidingTabs.implement(new Options, new Events);
