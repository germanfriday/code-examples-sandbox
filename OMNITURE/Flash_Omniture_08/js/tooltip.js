/**
 * Tooltip.js
 *
 * Advanced Tooltip class
 *
 * @copyright Davey Shafik (c) 2005 All Rights Reserved
 * @authors Davey Shafik <davey@php.net>
 * @version 0.4.0
 * @license MIT-style <http://tooltip.crtx.org/LICENSE>
 * @todo Add ability to have popup track mouse around
 * @todo Fix accessibility, make it possible to tab in/out of tooltips, and look into CSS Voice stuff
 * @todo Add ability to change the events *per* Tooltip
 */

/**
 * Add an Array.contains() method, mimics PHPs in_array() function
 */

Array.prototype.contains = function (value)
{
	var values = this.join('@#%');
	var re = new RegExp("^" +value+ "@#%|@#%" +value+ "@#%|@#%" +value+ "$");
	return re.test(values);
}
 
/**
 * Tooltip Object definition
 */
var Tooltip = {
	/**
	 * @var string|Array An event name or an array of event names on which to trigger showing the Tooltip
	 */
	showEvent: new Array("click","focus"),

	/**
	 * @var string|Array An event name or an array of event names on which to trigger hiding the Tooltip
	 */	
	hideEvent: new Array("click", "blur"),
        
        /**
         * @var float Duration of the fade events, in seconds
         * @author Idea contributed by Richard Thomas <cyberlot@cyberlot.net>
         */
        fade: 0.5,
	
	/**
	 * @var function Set the method which will be called for showing the tooltip
	 */
	showMethod: Effect.Appear,
	
	/**
	 * @var function Set the method which will be called for hiding the tooltip
	 */
	hideMethod: Effect.Fade,	
	
	/**
	 * Initial Setup
	 *
	 * Find all standard tooltips and auto-initialize them
	 *
	 * @return void
	 */
	setup: function ()
	{
		match_class = new RegExp("^(.*)\s?tooltip\s?(.*)$", 'i');
		match_for = new RegExp("^.*\s?for_(.*)\s?.*$", 'i');
		var divs = document.getElementsByTagName('div');
		var for_result;
		if (divs.length > 0) {
			for (var i = 0; i < divs.length; i++) {
				if (divs.item(i).className.match(match_class)) {
					for_result = divs.item(i).className.match(match_for);
					if (for_result && for_result.length > 0) {
						if (document.getElementById(for_result[1])) {
							var activator = document.getElementById(for_result[1]);
						}
					} else {
						var foundPrevious = false;
						var activator = divs.item(i);
						while (foundPrevious == false) {
							activator = activator.previousSibling;
							if (activator.tagName) {
								foundPrevious = true;
								break;
							}
						}
					}
					activator.Tooltip = divs.item(i);
	  				Tooltip.init(activator);
				}
			}
		}
	},


	/**
	 * Initiate an Activator/Tooltip for events and display
	 *
	 * @param activator DomElement The element to which the Tooltip show/hide events are attached
	 * @return void
	 */
	init: function (activator)
	{
		var tooltip = activator.Tooltip;
		tooltip.style.display = "none";
		
			Tooltip._attachEvent(activator, "toggle");
		
		
		// Remove Link Hrefs
		if (activator.tagName.toLowerCase() == "a") {
			try {
				activator.removeAttribute("href");
				activator.style.cursor = (document.links[0].style.cursor.length > 0) ? document.links[0].style.cursor : "pointer";
			}
			catch (e) {
				//DEBUG alert(e.message);
			}
		}
		
		// Create the <p><a href="#">Close</a></p> and add it to the Tooltip
		
		// <p> element
		var p = document.createElement('p');
		
		// <p> styles
		p.style.textAlign = "right";
		p.style.padding.padding = "0";
		p.style.margin = "0";
		
		// <p> class name
		p.className = "close";
		
		// <a> element
		var link = document.createElement('a');
		
		// Set the Tooltip var to the tooltip element
		link.Tooltip = tooltip;
		
		link.style.cursor = "pointer";
		
		// Add the click handler
		Tooltip._attachEvent(link, "click");
		
		// "Close" text node
		var close = document.createTextNode('Close');
		
		// Append the text to the <a> element
		link.appendChild(close);
		
		// Append the <a> to the <p> element
		p.appendChild(link);
		
		// Stick the entire thing on the end of the Tooltip
		tooltip.appendChild(p, tooltip.firstChild);
	},
	
	/**
	 * Manually add a Tooltip
	 *
	 * When passed an Activator and Tooltip element or ID, it is setup as a Tooltip
	 *
	 * @param activator Activator Element or ID, this is the element that activates the Tooltip
	 * @param tooltip Tooltip Element or ID, this is the Tooltip element itself that is shown/hidden
	 */
	add: function (activator, tooltip)
	{
		if (typeof activator == 'string') {
			activator = document.getElementById(activator);
		}
		if (typeof tooltip == 'string') {
			tooltip = document.getElementById(tooltip);
		}
		
		activator.Tooltip = tooltip;
		Tooltip.init(activator);
	},
	
	/**
	 * Toggle the Tooltip
         *
         * Shows or Hides the Tooltip
         *
         * @param activator Activator Element
         * @return void
	 */
	
	toggle: function (activator, event)
	{
		if (activator.Tooltip.isVisible) {
			Tooltip._hide(activator, event);
		} else {
			Tooltip._show(activator, event);
		}
	},
	
	/**
	 * Show the Tooltip
	 *
	 * Displays the Tooltip and sets the hide events up. You should never need to call this manually.
	 *
	 * @param activator Activator Element
         * @private
	 * @return void
	 */
	_show: function (activator, event)
	{
		var tooltip = activator.Tooltip;
		
		if (event == null || typeof Tooltip.showEvent == "string" || Tooltip.showEvent.constructor && Tooltip.showEvent.constructor == Array && Tooltip.showEvent.contains(event)) {
			activator.Tooltip.isVisible = true;
			try {
				if (typeof Effect) {
					Tooltip.showMethod(tooltip, {duration:Tooltip.fade});
				} else {
					tooltip.style.display = "block";
				}
			}
			catch (e) {
				try {
					tooltip.style.display = "block";
				}
				catch (e) {
					//DEBUG alert(tooltip.id);
				}
			}
			return;
		} else {
			alert (event);
		}
	},

	/**
	 * Hide the Tooltip
	 *
	 * Hides the Tooltip and sets the show events up. You should never need to call this manually.
	 *
	 * @param activator Activator Element
         * @private
	 * @return void
	 */ 	 
	_hide: function (activator, event)
	{
		var tooltip = activator.Tooltip;
		if (event == null || typeof Tooltip.hideEvent == "string" || Tooltip.hideEvent.constructor && Tooltip.hideEvent.constructor == Array && Tooltip.hideEvent.contains(event)) {
			activator.Tooltip.isVisible = false;
			try {
				if (typeof Effect) {
					Tooltip.hideMethod(tooltip, {duration:Tooltip.fade});
				} else {
					tooltip.style.display = "none";
				}
			}
			catch (e) {
				tooltip.style.display = "none";
			}
			
			return;
		}
	},
	
	/**
	 * Attach show/hide/load events
	 *
	 * This method removes any existing events first
	 * in case show/hide are the same.
	 *
	 * @param element Element to which events should be attached
	 * @param event Event for which events are being registered. One of show/hide/load/click.
	 */
	
	_attachEvent: function (element, event)
	{
		var i;
		var events = new Array();
			if (event == "toggle") {
				if (Tooltip.showEvent.constructor && Tooltip.showEvent.constructor == Array) {
					
					for (i = 0; i < Tooltip.showEvent.length; i++) {
						events.push(Tooltip.showEvent[i]);
						if (element.addEventListener) {
							element.addEventListener(Tooltip.showEvent[i], function (e) { Tooltip.toggle(element, e.type); return false; }, false);
						} else if (element.attachEvent) {
							element.attachEvent('on' + Tooltip.showEvent[i], function (e) { Tooltip.toggle(element, e.type); return false; });
						}
					}
				} else {
					events.push(Tooltip.showEvent);
					if (element.addEventListener) {
			  			element.addEventListener(Tooltip.showEvent, function (e) { Tooltip.toggle(element, e.type); return false; }, false);
					} else if (element.attachEvent) {
						element.attachEvent('on' + Tooltip.showEvent, function (e) { Tooltip.toggle(element, e.type); return false; });
					}
				}
				
				if (Tooltip.hideEvent.constructor && Tooltip.hideEvent.constructor == Array) {
					for (i = 0; i < Tooltip.hideEvent.length; i++) {
						if (!events.contains(Tooltip.hideEvent[i])) {
							events.push(Tooltip.hideEvent[i]);
							if (element.addEventListener) {
								element.addEventListener(Tooltip.hideEvent[i], function (e) { Tooltip.toggle(element, e.type); return false; }, false);
							} else if (element.attachEvent) {
								element.attachEvent('on' + hideEvent[i], function (e) { Tooltip.toggle(element, e.type); return false; });
							}
						}
					}
				} else {
					if (!events.contains(Tooltip.hideEvent)) {
						events.push(Tooltip.hideEvent);
						if (element.addEventListener) {
							element.addEventListener(Tooltip.hideEvent, function (e) { Tooltip.toggle(element, e.type); return false; }, false);
						} else if (element.attachEvent) {
							element.attachEvent('on' + Tooltip.hideEvent, function (e) { Tooltip.toggle(element, e.type); return false; });
						}
					}
				}
			} else if (event == "load") {
				if (element.addEventListener) {
		  			element.addEventListener("load", function () { Tooltip.setup(); }, false);
				} else if (element.attachEvent) {
					element.attachEvent('on' + "load", function () { Tooltip.setup(); });
				}
			} else if (event == "click") {
				if (element.addEventListener) {
		  			element.addEventListener("click", function () { Tooltip._hide(element, null); }, false);
				} else if (element.attachEvent) {
					element.attachEvent('on' + "click", function () { Tooltip._hide(element, null); });
				}
			}
	}
}

// Start the Tooltips in motion
try {
	Tooltip._attachEvent(window, 'load');
}
catch (e) { }