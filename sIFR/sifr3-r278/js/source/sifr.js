/*=:project
		parseSelector 2.0
		
	=:description
		Provides an extensible way of parsing CSS selectors against a DOM in 
		JavaScript.

  =:file
  	Copyright: 2006 Mark Wubben.
  	Author: Mark Wubben, <http://novemberborn.net/>
   		
	=:license
		This software is licensed and provided under the CC-GNU LGPL. 
		See <http://creativecommons.org/licenses/LGPL/2.1/>
		
	=:support
	  parseSelector supports the following user agents:
	    * Internet Explorer 6 and above
	    * Firefox 1.0 and above, and equivalent Gecko engine versions
	    * Safari 2.0 and above
	    * Opera 8.0 and above
	    * Konqueror 3.5.5 and above
	  It might work in other browsers and versions, but there are no guarantees. There is
	  no verification made when parseSelector is run to ascertain the browser is supported.
	
	=:notes
		The parsing of CSS selectors as streams has been based on Dean Edwards
		excellent work with cssQuery. See <http://dean.edwards.name/my/cssQuery/>
		for more info.
*/

var parseSelector = (function() {
	var SEPERATOR       = /\s*,\s*/
	var WHITESPACE      = /\s*([\s>+~(),]|^|$)\s*/g;
	var IMPLIED_ALL     = /([\s>+~,]|[^(]\+|^)([#.:@])/g;
	var STANDARD_SELECT = /^[^\s>+~]/;
	var STREAM          = /[\s#.:>+~()@]|[^\s#.:>+~()@]+/g;
	
	function parseSelector(selector, node) {
		node = node || document.documentElement;
		var argSelectors = selector.split(SEPERATOR), result = [];

		for(var i = 0; i < argSelectors.length; i++) {
			var nodes = [node], stream = toStream(argSelectors[i]);
			for(var j = 0; j < stream.length;) {
				var token = stream[j++], filter = stream[j++], args = '';
				if(stream[j] == '(') {
					while(stream[j++] != ')' && j < stream.length) args += stream[j];
					args = args.slice(0, -1);
				}
				nodes = select(nodes, token, filter, args);
			}
			result = result.concat(nodes);
		}
		
		return result;
	}

	function toStream(selector) {
		var stream = selector.replace(WHITESPACE, '$1').replace(IMPLIED_ALL, '$1*$2');
		if(STANDARD_SELECT.test(stream)) stream = ' ' + stream;
    return stream.match(STREAM) || [];
	}
	
	function select(nodes, token, filter, args) {
		return (selectors[token]) ? selectors[token](nodes, filter, args) : [];
	}
	
	var util = {
		toArray: function(enumerable) {
			var a = [];
			for(var i = 0; i < enumerable.length; i++) a.push(enumerable[i]);
			return a;
		}
	};
	
	var dom = {
		isTag: function(node, tag) {
			return (tag == '*') || (tag.toLowerCase() == node.nodeName.toLowerCase());
		},
	
		previousSiblingElement: function(node) {
			do node = node.previousSibling; while(node && node.nodeType != 1);
			return node;
		},
	
		nextSiblingElement: function(node) {
			do node = node.nextSibling; while(node && node.nodeType != 1);
			return node;
		},
	
		hasClass: function(name, node) {
			return (node.className || '').match('(^|\\s)'+name+'(\\s|$)');
		},
	
		getByTag: function(tag, node) {
			return node.getElementsByTagName(tag);
		}
	};

	var selectors = {
		'#': function(nodes, filter) {
			for(var i = 0; i < nodes.length; i++) {
				if(nodes[i].getAttribute('id') == filter) return [nodes[i]];
			}
			return [];
		},

		' ': function(nodes, filter) {
			var result = [];
			for(var i = 0; i < nodes.length; i++) {
				result = result.concat(util.toArray(dom.getByTag(filter, nodes[i])));
			}
			return result;
		},
		
		'>': function(nodes, filter) {
			var result = [];
			for(var i = 0, node; i < nodes.length; i++) {
				node = nodes[i];
				for(var j = 0, child; j < node.childNodes.length; j++) {
					child = node.childNodes[j];
					if(child.nodeType == 1 && dom.isTag(child, filter)) result.push(child);
				}
			}
			return result;
		},

		'.': function(nodes, filter) {
			var result = [];
			for(var i = 0, node; i < nodes.length; i++) {
				node = nodes[i];
				if(dom.hasClass([filter], node)) result.push(node);
			}
			return result;
		}, 
				
		':': function(nodes, filter, args) {
			return (pseudoClasses[filter]) ? pseudoClasses[filter](nodes, args) : [];
		}
		
	};

	parseSelector.selectors			= selectors;
	parseSelector.pseudoClasses = {};
	parseSelector.util 				  = util;
	parseSelector.dom 				  = dom;

	return parseSelector;
})();

/*=:project
    scalable Inman Flash Replacement (sIFR) version 3.

  =:file
    Copyright: 2006 Mark Wubben.
    Author: Mark Wubben, <http://novemberborn.net/>

  =:history
    * IFR: Shaun Inman
    * sIFR 1: Mike Davidson, Shaun Inman and Tomas Jogin
    * sIFR 2: Mike Davidson, Shaun Inman, Tomas Jogin and Mark Wubben

  =:documentation
    See <http://wiki.novemberborn.net/sifr3>

  =:license
    This software is licensed and provided under the CC-GNU LGPL.
    See <http://creativecommons.org/licenses/LGPL/2.1/>    
*/

var sIFR = new function() {
  //=:private Constant reference to the Singleton instance
  var SIFR = this;

  var CSS_ACTIVE        = 'sIFR-active';
  var CSS_REPLACED      = 'sIFR-replaced';
  var CSS_REPLACING     = 'sIFR-replacing';
  var CSS_FLASH         = 'sIFR-flash';
  var CSS_IGNORE        = 'sIFR-ignore';
  var CSS_ALTERNATE     = 'sIFR-alternate';
  var CSS_CLASS         = 'sIFR-class';
  var CSS_LAYOUT        = 'sIFR-layout';
  var MIN_FONT_SIZE     = 6;
  var MAX_FONT_SIZE     = 126;
  var MIN_FLASH_VERSION = 8;
  var PREFETCH_COOKIE   = 'SIFR-PREFETCHED';
  //= Whitespace string each whitespace character is replaced with, as per `preserveSingleWhitespace`.
  var DEFAULT_RATIOS    = [];
  var FLASH_PADDING_BOTTOM = 5;

  this.isActive                 = false;
  this.isEnabled                = true;
  this.hideElements             = true;
  this.preserveSingleWhitespace = false;
  this.fixWrap                  = true;
  this.fixHover                 = true;
  this.registerEvents           = true;
  this.setPrefetchCookie        = true;
  this.cookiePath               = '/';
  this.domains                  = [];
  this.fromLocal                = false;
  this.forceClear               = false;
  this.forceWidth               = false;
  this.fitExactly               = false;
  this.forceTextTransform       = true;
  this.useDomContentLoaded      = true;
  this.hasFlashClassSet         = false;
  this.delayCss                 = false;
  this.callbacks                = [];
  
  var elementCount = 0; // The number of replaced elements.
  var hasPrefetched = false, isInitialized = false;

  var dom = new function() {
    var XHTML_NS          = 'http://www.w3.org/1999/xhtml';
      
    this.getBody = function() {
      var nodes = document.getElementsByTagName('body');
      if(nodes.length == 1) return nodes[0];
      return null;
    };

    this.addClass = function(name, node) {
      if(node) node.className = ((node.className || '') == '' ? '' : node.className + ' ') + name;
    };
    
    this.removeClass = function(name, node) {
      if(node) node.className = node.className.replace(new RegExp('(^|\\s)' + name + '(\\s|$)'), '').replace(/^\s+|(\s)\s+/g, '$1');
    };

    this.hasClass = function(name, node) {
      return new RegExp('(^|\\s)' + name + '(\\s|$)').test(node.className);
    };
    
    this.hasOneOfClassses = function(names, node) {
      for(var i = 0; i < names.length; i++) {
        if(this.hasClass(names[i], node)) return true;
      }
      return false;
    };

    this.create = function(name) {
      if(document.createElementNS) return document.createElementNS(XHTML_NS, name);
      return document.createElement(name);
    };
    
    this.setInnerHtml = function(node, html) {
      if(ua.innerHtmlSupport) node.innerHTML = html;
      else if(ua.xhtmlSupport){
        html = ['<root xmlns="', XHTML_NS, '">', html, '</root>'].join('');
        var xml = (new DOMParser()).parseFromString(html, 'text/xml');
        xml = document.importNode(xml.documentElement, true);
        while(node.firstChild) node.removeChild(node.firstChild);
        while(xml.firstChild)  node.appendChild(xml.firstChild);
      }
    };
    
    this.nodeFromHtml = function(html) {
      var temp = this.create('div');
      temp.innerHTML = html;
      return temp.firstChild;
    };
    
    this.getComputedStyle = function(node, property) {
      var result;
      if(document.defaultView && document.defaultView.getComputedStyle) {
        result = document.defaultView.getComputedStyle(node, null)[property];
      } else if(node.currentStyle) result = node.currentStyle[property];
      return result || ''; // Ensuring a string.
    };

    this.getStyleAsInt = function(node, property, requirePx) {
      var value = this.getComputedStyle(node, property);
      if(requirePx && !/px$/.test(value)) return 0;
      
      value = parseInt(value);
      return isNaN(value) ? 0 : value;
    };

    this.getZoom = function() {
      return hacks.zoom.getLatest();
    };
  };
  this.dom = dom;

  var ua = new function() {
    var ua                = navigator.userAgent.toLowerCase();
    var product           = (navigator.product || '').toLowerCase();

    this.macintosh        = ua.indexOf('mac') > -1;
    this.windows          = ua.indexOf('windows') > -1;
    this.quicktime        = false;

    this.opera            = ua.indexOf('opera') > -1;
    this.konqueror        = product.indexOf('konqueror') > -1;
    this.ie               = false/*@cc_on || true @*/;
    this.ieSupported      = this.ie && !/ppc|smartphone|iemobile|msie\s5\.5/.test(ua)/*@cc_on && @_jscript_version >= 5.5 @*/
    this.ieWin            = this.ie && this.windows/*@cc_on && @_jscript_version >= 5.1 @*/;
    this.windows          = this.windows && (!this.ie || this.ieWin);
    this.ieMac            = this.ie && this.macintosh/*@cc_on && @_jscript_version < 5.1 @*/;
    this.macintosh        = this.macintosh && (!this.ie || this.ieMac);
    this.safari           = ua.indexOf('safari') > -1;
    this.webkit           = ua.indexOf('applewebkit') > -1 && !this.konqueror;
    this.khtml            = this.webkit || this.konqueror;
    this.gecko            = !this.webkit && product == 'gecko';

    this.operaVersion     = this.opera     && /.*opera(\s|\/)(\d+\.\d+)/.exec(ua) ? parseInt(RegExp.$2) : 0;
    this.webkitVersion    = this.webkit    && /.*applewebkit\/(\d+).*/.exec(ua)   ? parseInt(RegExp.$1) : 0;
    this.geckoBuildDate   = this.gecko     && /.*gecko\/(\d{8}).*/.exec(ua)       ? parseInt(RegExp.$1) : 0;
    this.konquerorVersion = this.konqueror && /.*konqueror\/(\d\.\d).*/.exec(ua)  ? parseInt(RegExp.$1) : 0;

    this.flashVersion     = 0;
    if(this.ieWin) {
      var axo;
      var stop = false;
      try {
	      axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.7');
      } catch(e) {
        // In case the Flash 7 registry key does not exist, we need to test for specific 
        // Flash 6 installs before we can use the general key. 
        // See also <http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/>.
        // Many thanks to Geoff Sterns and Bobby van der Sluis for clarifying problem and providing
        // examples of non-crashing code.
    		try {
    		  axo                   = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6');
      		this.flashVersion     = 6;
          axo.AllowScriptAccess = 'always';
        } catch(e) { stop = this.flashVersion == 6; }

				if(!stop) try { axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');	} catch(e) {}
			}

      if(!stop && axo) this.flashVersion = parseFloat(/([\d,?]+)/.exec(axo.GetVariable('$version'))[1].replace(/,/g, '.'));
    } else if(navigator.plugins && navigator.plugins['Shockwave Flash']) {
      var flashPlugin = navigator.plugins['Shockwave Flash'];
      this.flashVersion = parseFloat(/(\d+\.?\d*)/.exec(flashPlugin.description)[1]);

      // Watch out for QuickTime, which could be stealing the Flash handling!
      var i = 0;
      while(this.flashVersion >= MIN_FLASH_VERSION && i < navigator.mimeTypes.length) {
        var mime = navigator.mimeTypes[i];
        if(mime.type == 'application/x-shockwave-flash' && mime.enabledPlugin.description.toLowerCase().indexOf('quicktime') > -1) {
          this.flashVersion = 0;
          this.quicktime = true;
        }
        i++;
      }
    }

    this.flash = this.flashVersion >= MIN_FLASH_VERSION;

    // There are other conditions, but these are ruled out in `computedStyledSupport` or `supported`.
    this.transparencySupport  = this.macintosh || this.windows;

    this.computedStyleSupport = this.ie || document.defaultView && document.defaultView.getComputedStyle 
      && (!this.gecko || this.geckoBuildDate >= 20030624); // Older Geckos have trouble with `getComputedStyle()`

    this.css = true;
    // Verify CSS support. We'll be changing the color of the head element, as it won't affect
    // page rendering and no other elements (other than HTML) can be relied up-on at this point.
    if(this.computedStyleSupport) {
      try { // Not sure if there are user agents which will disallow this
        var node = document.getElementsByTagName('head')[0];
        node.style.backgroundColor = '#FF0000';
        var color = dom.getComputedStyle(node, 'backgroundColor'); // Safari will return null, Konqueror an empty string
        this.css = !color || /\#F{2}0{4}|rgb\(255,\s?0,\s?0\)/i.test(color);
        node.style.backgroundColor = '';
        node = null;
      } catch(e) {}
    }
    
    this.xhtmlSupport = !!window.DOMParser && !!document.importNode;
    try { 
      var n = dom.create('span');
      if(!this.ieMac) n.innerHTML = 'x'; // Need to test for IE/Mac, since the code will still be executed in IE/Mac.
      this.innerHtmlSupport = n.innerHTML == 'x';
    } catch (e) { this.innerHtmlSupport = false; }
    
    this.zoomSupport       = !!(this.opera && document.documentElement);
    this.geckoXml          = this.gecko && (document.contentType || '').indexOf('xml') > -1;

    this.requiresPrefetch  = this.ieWin || this.khtml;
    this.verifiedKonqueror = false;

    this.supported         = this.flash && this.css && (!this.ie || this.ieSupported) 
      && (!this.opera || this.operaVersion >= 8) && (!this.webkit || this.webkitVersion >= 412)  
      && (!this.konqueror || this.konquerorVersion > 3.5) && this.computedStyleSupport
      && (this.innerHtmlSupport || !this.khtml && this.xhtmlSupport)
      && (!this.gecko || this.geckoBuildDate > 20040804); // Had a few reports about Netscape 7.2 crashing, therefore 
                                                          // now requiring a newer Gecko version.
  };
  this.ua = ua;

  var util = new function() {
    var UNIT_REMOVAL_PROPERTIES = {leading: true, 'margin-left': true, 'margin-right': true, 'text-indent': true};
    var SINGLE_WHITESPACE = ' ';
    
    function capitalize($) {
      return $.toUpperCase();
    }
    
    this.normalize = function(str) {
      if(SIFR.preserveSingleWhitespace) return str.replace(/\s/g, SINGLE_WHITESPACE);
      // Normalize to the first whitespace, and then make sure no nbsp characters are preserved as Flash doesn't seem to support these.
      return str.replace(/(\s)\s+/g, '$1').replace(/\xA0/, SINGLE_WHITESPACE);
    };
    
    this.textTransform = function(type, str) {
      switch(type) {
        case 'uppercase':
          str = str.toUpperCase();
          break;
        
        case 'lowercase':
          str = str.toLowerCase();
          break;
          
        case 'capitalize':
          var strCopy = str;
          str = str.replace(/^\w|\s\w/g, capitalize);
          if(str.indexOf('function capitalize') != -1) {
            var substrs = strCopy.replace(/(^|\s)(\w)/g, '$1$1$2$2').split(/^\w|\s\w/g);
            str = '';
            for(var i = 0; i < substrs.length; i++) str += substrs[i].charAt(0).toUpperCase() + substrs[i].substring(1);
          }
          break;
      }
      
      return str;
    };
    
    this.toHexString = function(str) {
      if(typeof(str) != 'string' || !str.charAt(0) == '#' || str.length != 4 && str.length != 7) return str;
      
      str = str.replace(/#/, '');
      if(str.length == 3) str = str.replace(/(.)(.)(.)/, '$1$1$2$2$3$3');

      return '0x' + str;
    };

    this.toJson = function(obj) {
      var json = '';

      switch(typeof(obj)) {
        case 'string':
          json = '"' + obj + '"';
          break;
        case 'number':
        case 'boolean':
          json = obj.toString();
          break;
        case 'object':
          json = [];
          for(var prop in obj) {
            if(obj[prop] == Object.prototype[prop]) continue;
            json.push('"' + prop + '":' + util.toJson(obj[prop]));
          }
          json = '{' + json.join(',') + '}';
          break;
      }

      return json;
    };
    
    this.convertCssArg = function(arg) {
      if(!arg) return {};
      if(typeof(arg) == 'object') {
        if(arg.constructor == Array) arg = arg.join('');
        else return arg;
      }

      var obj = {};
      var rules = arg.split('}');

      for(var i = 0; i < rules.length; i++) {
        var $ = rules[i].match(/([^\s{]+)\s*\{(.+)\s*;?\s*/);
        if(!$ || $.length != 3) continue;

        if(!obj[$[1]]) obj[$[1]] = {};

        var properties = $[2].split(';');
        for(var j = 0; j < properties.length; j++) {
          var $2 = properties[j].match(/\s*([^:\s]+)\s*\:\s*([^\s;]+)/);
          if(!$2 || $2.length != 3) continue;
          obj[$[1]][$2[1]] = $2[2];
        }
      }

      return obj;
    };

    this.extractFromCss = function(css, selector, property, remove) {
      var value = null;

      if(css && css[selector] && css[selector][property]) {
        value = css[selector][property];
        if(remove) delete css[selector][property];
      }

      return value;
    };
    
    this.cssToString = function(arg) {
      var css = [];
      for(var selector in arg) {
        var rule = arg[selector];
        if(rule == Object.prototype[selector]) continue;

        css.push(selector, '{');
        for(var property in rule) {
          if(rule[property] == Object.prototype[property]) continue;
          var value = rule[property];
          if(UNIT_REMOVAL_PROPERTIES[property]) value = parseInt(value, 10);
          css.push(property, ':', value, ';');
        }
        css.push('}');
      }

      return css.join('');
    };

    this.bind = function(scope, method) {
      return function() {
        scope[method].apply(scope, arguments);
      };
    };

    this.escape = function(str) {
      return escape(str).replace(/\+/, '%2B');
    }
  };
  this.util = util;

  var hacks = {};
  hacks.fragmentIdentifier = new function() {
    this.fix = true;

    var cachedTitle;
    this.cache = function() {
      cachedTitle = document.title;
    };

    function doFix() {
      document.title = cachedTitle;
    }

    this.restore = function() {
      if(this.fix) setTimeout(doFix, 0);
    };
  };

  // The zoom hack needs to be run before replace(). The synchronizer can be
  // used to ensure this.
  hacks.synchronizer = new function() {
    this.isBlocked = false;

    this.block = function() {
      this.isBlocked = true;
    };

    this.unblock = function() {
      this.isBlocked = false;
      blockedReplaceKwargsStore.replaceAll();
    };
  };

  // Detect the page zoom in Opera. Adapted from <http://virtuelvis.com/archives/2005/05/opera-measure-zoom>.
  hacks.zoom = new function() {
    // Latest zoom, assume 100
    var latestZoom = 100;

    this.getLatest = function() {
      return latestZoom;
    }

    if(ua.zoomSupport && ua.opera) {
      // Create the DOM element used to calculate the zoom.
      var node = document.createElement('div');
      node.style.position = 'fixed';
      node.style.left = '-65536px';
      node.style.top = '0';
      node.style.height = '100%';
      node.style.width = '1px';
      node.style.zIndex = '-32';
      document.documentElement.appendChild(node);

      function updateZoom() {
        if(!node) return;

        var zoom = window.innerHeight / node.offsetHeight;

        var correction = Math.round(zoom * 100) % 10;
        if(correction > 5) zoom = Math.round(zoom * 100) + 10 - correction;
        else zoom = Math.round(zoom * 100) - correction;

        latestZoom = isNaN(zoom) ? 100 : zoom;
        hacks.synchronizer.unblock();

        document.documentElement.removeChild(node);
        node = null;
      }

      hacks.synchronizer.block();

      // We need to wait a few ms before Opera the offsetHeight of the node
      // becomes available.
      setTimeout(updateZoom, 54);
    }
  };
  this.hacks = hacks;

  this.errors = {
    isFile: 'sIFR: Did not activate because the page is being loaded from the filesystem.',
    getSource: 'sIFR: Could not determine appropriate source'
  };

  var replaceKwargsStore = {
    kwargs: [],
    replaceAll:  function(preserve) {
      for(var i = 0; i < this.kwargs.length; i++) SIFR.replace(this.kwargs[i]);
      if(!preserve) this.kwargs = [];
    }
  };

  var blockedReplaceKwargsStore = {
    kwargs: [],
    replaceAll: replaceKwargsStore.replaceAll
  };

  // The goal here is not to prevent usage of the Flash movie, but running sIFR on possibly translated pages
  function isValidDomain() {
    if(SIFR.domains.length == 0) return true;

    var domain = '';
    try { // When trying to access document.domain on a Google-translated page with Firebug, I got an exception.
      domain = document.domain;
    } catch(e) {};

    for(var i = 0; i < SIFR.domains.length; i++) {
      var match = SIFR.domains[i];
      if(match == '*' || match == domain) return true;

      var wildcard = match.lastIndexOf('*');
      if(wildcard > -1) {
        match = match.substr(wildcard + 1);
        var matchPosition = domain.lastIndexOf(match);
        if(matchPosition > -1 && (matchPosition + match.length) == domain.length) return true;
      }
    }

    return false;
  }

  function isFile() {
    if(!SIFR.fromLocal && document.location.protocol == 'file:') {
      if(SIFR.debug) throw new Error(SIFR.errors.isFile);
      return true;
    }
    return false;
  }

  this.activate = function(/* … */) {
    if(!ua.supported || !this.isEnabled || this.isActive || !isValidDomain() || isFile()) return;

    if(arguments.length > 0) this.prefetch.apply(this, arguments);

    this.isActive = true;

    if(this.hideElements) this.setFlashClass();

    if(ua.ieWin && hacks.fragmentIdentifier.fix && window.location.hash != '') {
      hacks.fragmentIdentifier.cache();
    } else hacks.fragmentIdentifier.fix = false;

    if(!this.registerEvents) return;

    function handler(evt, preserveReplacements) {
      SIFR.initialize(preserveReplacements);

      // Remove handlers to prevent memory leak in Firefox 1.5, but only after onload.
      if(evt && evt.type == 'load') {
        if(document.removeEventListener) document.removeEventListener('DOMContentLoaded', handler, false);
        if(window.removeEventListener) window.removeEventListener('load', handler, false);
      }
    }
    
    if(window.addEventListener) {
      // Opera and also Safari load JavaScript and CSS synchrously, so we can't rely on 
      // correct information to do the replacements. Hence, we only use DOMContentLoaded
      // for Gecko-based browsers. In other cases, add a normal load event to the document.
      //:note The load event might be redundant, needs testing!
      if(SIFR.useDomContentLoaded && ua.gecko) document.addEventListener('DOMContentLoaded', handler, false);
      window.addEventListener('load', handler, false);
    } else if(ua.ieWin) {
      if(SIFR.useDomContentLoaded) { // Replacing before onload breaks prefetching
        document.write('<scr' + 'ipt id=__sifr_ie_onload defer src=//:><\/script>');
        document.getElementById('__sifr_ie_onload').onreadystatechange = function() {
          if(this.readyState == 'complete') {
            handler(null, true); // Preserve replacements to fix the improper deferral bug.
            this.removeNode();
          }
        };
      }
      window.attachEvent('onload', handler);
    }
  };

  this.setFlashClass = function() {
    if(this.hasFlashClassSet) return;

    dom.addClass(CSS_ACTIVE, dom.getBody() || document.documentElement);
    this.hasFlashClassSet = true;
  };

  this.removeFlashClass = function() {
    if(!this.hasFlashClassSet) return;

    dom.removeClass(CSS_ACTIVE, dom.getBody());
    dom.removeClass(CSS_ACTIVE, document.documentElement);
    this.hasFlashClassSet = false;
  }

  this.initialize = function(preserveReplacements) {
    if(!this.isActive || !this.isEnabled) return;
    if(isInitialized) {
      if(!preserveReplacements) replaceKwargsStore.replaceAll(false);
      return;
    }

    isInitialized = true;
    replaceKwargsStore.replaceAll(preserveReplacements);
    clearPrefetch();
  };

  function getSource(src) {
    if(typeof(src) != 'string') {
      // This is a niciety to allow you to create general configuration objects
      // for the prefetch as well as the replacement. You could create constructs
      // like `{src: {src: { /*....*/ }}}`, but that's not really a problem.
      if(src.src) src = src.src;

      // It might be a string now...
      if(typeof(src) != 'string') {
        var versions = [];
        for(var version in src) if(src[version] != Object.prototype[version]) versions.push(version);
        versions.sort().reverse();

        var result = '';
        var i = -1;
        while(!result && ++i < versions.length) {
          if(parseFloat(versions[i]) <= ua.flashVersion) result = src[versions[i]];
        }
        
        src = result;
      }
    }
    
    if(!src && SIFR.debug) throw new Error(SIFR.errors.getSource);
    
    // Some IE installs refuse to show the Flash unless it gets the really absolute
    // URI of the file. I haven't been able to reproduce this behavior but let's
    // ensure a full URI none the less. This turns `/foo.swf` in `http://example.com/foo.swf`.
    if(ua.ie && src.charAt(0) == '/') {
      src = window.location.toString().replace(/([^:]+)(:\/?\/?)([^\/]+).*/, '$1$2$3') + src;
    }
    
    return src;
  }

  this.prefetch = function(/* … */) {
    if((!ua.requiresPrefetch && !this.isActive) || !ua.supported || !this.isEnabled || !isValidDomain()) return;
    if(this.setPrefetchCookie && new RegExp(';?' + PREFETCH_COOKIE + '=true;?').test(document.cookie)) return;

    try { // We don't know which DOM actions the user agent will allow
      hasPrefetched = true;

      if(ua.ieWin) prefetchIexplore(arguments);
      else prefetchLight(arguments);

      if(this.setPrefetchCookie) document.cookie = PREFETCH_COOKIE + '=true;path=' + this.cookiePath;
    } catch(e) { if(SIFR.debug) throw e; }
  };

  function prefetchIexplore(args) {
    for(var i = 0; i < args.length; i++) {
      document.write('<script defer type="sifr/prefetch" src="' + getSource(args[i]) + '"></script>');
    }
  }

  function prefetchLight(args) {
    for(var i = 0; i < args.length; i++) new Image().src = getSource(args[i]);
  }

  function clearPrefetch() {
    if(!ua.ieWin || !hasPrefetched) return;

    try {
      var nodes = document.getElementsByTagName('script');
      for(var i = nodes.length - 1; i >= 0; i--) {
        var node = nodes[i];
        if(node.type == 'sifr/prefetch') node.parentNode.removeChild(node);
      }
    } catch(e) {}
  }

  // Gives a font-size to required vertical space ratio
  function getRatio(size, ratios) {
    for(var i = 0; i < ratios.length; i += 2) {
      if(size <= ratios[i]) return ratios[i + 1];
    }
    return ratios[ratios.length - 1] || 1;
  }

  function getFilters(obj) {
    var filters = [];
    for(var filter in obj) {
      if(obj[filter] == Object.prototype[filter]) continue;

      var properties = obj[filter];
      filter = [filter.replace(/filter/i, '') + 'Filter'];

      for(var property in properties) {
        if(properties[property] == Object.prototype[property]) continue;
        filter.push(property + ':' + util.escape(util.toJson(util.toHexString(properties[property]))));
      }

      filters.push(filter.join(','));
    }

    return util.escape(filters.join(';'));
  }
  
  function calculate(node) {
    var lineHeight, lines;
    if(!ua.ie) { //:=todo Only do once for each selector?
      lineHeight = dom.getStyleAsInt(node, 'lineHeight');
      lines = Math.floor(dom.getStyleAsInt(node, 'height') / lineHeight);
    } else if(ua.ie) { // IE returs computed style in the original units, which is quite useless.
      var html = node.innerHTML;

      // Without these settings, we won't be able to get the rects properly. getClientRects()
      // won't work on elements having layout or that are hidden.
      node.style.visibility  = 'visible';
      node.style.overflow    = 'visible';
      node.style.position    = 'static';
      node.style.zoom        = 'normal';
      node.style.writingMode = 'lr-tb';
      node.style.width       = node.style.height = 'auto';
      node.style.maxWidth    = node.style.maxHeight = node.style.styleFloat  = 'none';
              
      var rectNode = node;
      var hasLayout = node.currentStyle.hasLayout;
      if(hasLayout) {
        dom.setInnerHtml(node, '<div class="' + CSS_LAYOUT + '">X<br />X<br />X</div>');
        rectNode = node.firstChild;
      } else dom.setInnerHtml(node, 'X<br />X<br />X');

      var rects = rectNode.getClientRects();
      lineHeight = rects[1].bottom - rects[1].top;

      // In IE, the lineHeight is about 1.25 times the height in other browsers.
      lineHeight = Math.ceil(lineHeight * 0.8);

      if(hasLayout) {
        dom.setInnerHtml(node, '<div class="' + CSS_LAYOUT + '">' + html + '</div>');
        rectNode = node.firstChild;
      } else dom.setInnerHtml(node, html);
      rects = rectNode.getClientRects();
      lines = rects.length;
      
      if(hasLayout) dom.setInnerHtml(node, html);

      // By setting an empty string, the values will fall back to those in the (non-inline) CSS.
      // When that CSS changes, the changes are reflected here. Setting explicit values would break
      // that behaviour.
      node.style.visibility = node.style.width = node.style.height = node.style.maxWidth 
      = node.style.maxHeight = node.style.overflow = node.style.styleFloat
      = node.style.position = node.style.zoom = node.style.writingMode = '';
    }
    
    return {lineHeight: lineHeight, lines: lines};
  }

  this.replace = function(kwargs, mergeKwargs) {
    if(!ua.supported) return;
    
    // This lets you specify to kwarg objects so you don't have to repeat common settings.
    // The first object will be merged with the second, while properties in the second 
    // object have priority over those in the first. The first object is unmodified
    // for further use, the resulting second object will be used in the replacement.
    if(mergeKwargs) {
      for(var property in kwargs) {
        if(typeof(mergeKwargs[property]) == 'undefined') mergeKwargs[property] = kwargs[property];
      }
      kwargs = mergeKwargs;
    }
    
    if(!isInitialized) return replaceKwargsStore.kwargs.push(kwargs);
    if(hacks.synchronizer.isBlocked) return blockedReplaceKwargsStore.kwargs.push(kwargs);

    var nodes = kwargs.elements;
    if(!nodes && parseSelector) nodes = parseSelector(kwargs.selector);
    if(nodes.length == 0) return;

    this.setFlashClass();

    var src = getSource(kwargs.src);
    var css = util.convertCssArg(kwargs.css);
    var filters = getFilters(kwargs.filters);
    
    var forceClear = (kwargs.forceClear == null) ? SIFR.forceClear : kwargs.forceClear;
    var fitExactly = (kwargs.fitExactly == null) ? SIFR.fitExactly : kwargs.fitExactly;
    var forceWidth = fitExactly || (kwargs.forceWidth == null ? SIFR.forceWidth : kwargs.forceWidth);

    var leading         = parseInt(util.extractFromCss(css, '.sIFR-root', 'leading')) || 0;
    var fontSize        = util.extractFromCss(css, '.sIFR-root', 'font-size', true) || 0;
    var backgroundColor = util.extractFromCss(css, '.sIFR-root', 'background-color', true) || '#FFFFFF';
    var kerning         = util.extractFromCss(css, '.sIFR-root', 'kerning', true) || '';
    var gridFitType     = kwargs.gridFitType || util.extractFromCss(css, '.sIFR-root', 'text-align') == 'right' ? 'subpixel' : 'pixel';
    var textTransform   = SIFR.forceTextTransform ? util.extractFromCss(css, '.sIFR-root', 'text-transform', true) || 'none' : 'none';
    var opacity         = util.extractFromCss(css, '.sIFR-root', 'opacity', true) || '100';
    var pixelFont       = kwargs.pixelFont || false;
    var ratios          = kwargs.ratios || DEFAULT_RATIOS;
    var tuneHeight      = parseInt(kwargs.tuneHeight) || 0;

    if(parseInt(fontSize).toString() != fontSize && fontSize.indexOf('px') == -1) fontSize = 0; // We only support pixel sizes
    else fontSize = parseInt(fontSize);
    if(parseFloat(opacity) < 1) opacity = 100 * parseFloat(opacity); // Make sure to support percentages and decimals

    var cssText = '';
    // Alignment is handled by the browser in this case.
    if(fitExactly) util.extractFromCss(css, '.sIFR-root', 'text-align', true);
    if(!kwargs.modifyCss) cssText = util.cssToString(css);
    
    var delayCss = !ua.opera && SIFR.delayCss;

    var wmode = kwargs.wmode || '';
    if(!wmode) {
      if(kwargs.transparent) wmode = 'transparent';
      else if(kwargs.opaque) wmode = 'opaque';
    } 
    if(wmode == 'transparent') {
			if(!ua.transparencySupport)	wmode = 'opaque';
			else backgroundColor = 'transparent';
		}

    for(var i = 0; i < nodes.length; i++) {
      var node = nodes[i];

      if(!ua.verifiedKonqueror) {
        if(dom.getComputedStyle(node, 'lineHeight').match(/e\+08px/)) {
          ua.supported = SIFR.isEnabled = false;
          this.removeFlashClass();
          return;
        }
        ua.verifiedKonqueror = true;
      }

      if(dom.hasOneOfClassses([CSS_REPLACED, CSS_REPLACING, CSS_IGNORE, CSS_ALTERNATE], node)) continue;

      // Opera does not allow communication with hidden Flash movies. Visibility is tackled by sIFR itself, but
      // `display:none` isn't. Additionally, WebKit does not return computed style information for elements with
      // `display:none`. We'll prevent elements which have `display:none` or are contained in such an element from
      // being replaced. It's a bit hard to detect this, but we'll check for the dimensions of the element and it's
      // `display` property.

      var height  = node.offsetHeight;
      var width   = node.offsetWidth;
      var display = dom.getComputedStyle(node, 'display');

      if(!height || !width || display == null || display == 'none') continue;

      if(forceClear && ua.gecko) node.style.clear = 'both';

      // If the text doesn't wrap nicely, the width becomes too large and Flash
      // can't adjust for it. By setting the text to just "X" we can be sure
      // we get the correct width.
      var html = null;
      if(SIFR.fixWrap && ua.ie && display == 'block') {
        html = node.innerHTML;
        dom.setInnerHtml(node, 'X');
      }

      // Get the width (again to approximate the final size). The computed width
      // may not be a pixel unit in IE, in which case we try to calculate using
      // padding and borders and the offsetWidth.
      width = dom.getStyleAsInt(node, 'width', ua.ie);
      if(width == 0) {
        var paddingRight  = dom.getStyleAsInt(node, 'paddingRight', true);
        var paddingLeft   = dom.getStyleAsInt(node, 'paddingLeft', true);
        var borderRight   = dom.getStyleAsInt(node, 'borderRightWidth', true);
        var borderLeft    = dom.getStyleAsInt(node, 'borderLeftWidth', true);
        width = node.offsetWidth - paddingLeft - paddingRight - borderLeft - borderRight;
      }

      if(html && SIFR.fixWrap && ua.ie) dom.setInnerHtml(node, html);

      var lineHeight, lines;
      if(!fontSize) {
        var calculation = calculate(node);
        lineHeight      = Math.min(MAX_FONT_SIZE, Math.max(MIN_FONT_SIZE, calculation.lineHeight));
        if(pixelFont) lineHeight = Math.max(8, 8 * Math.round(lineHeight / 8));

        lines = calculation.lines;
        if(isNaN(lines) || !isFinite(lines) || lines == 0) lines = 1;

        if(lines > 1 && leading) height += Math.round((lines - 1) * leading);
      } else {
        lineHeight = fontSize;
        lines      = 1;
      }

      height = Math.round(lines * lineHeight);

      // We have all the info we need, reset the display setting now.
      if(forceClear && ua.gecko) node.style.clear = '';

      // I wanted to use `noembed` here, but unfortunately FlashBlock only works with `span.sIFR-alternate`
      var alternate = dom.create('span');
      alternate.className = CSS_ALTERNATE;

      // Clone the original content to the alternate element.
      var contentNode = node.cloneNode(true);
      for(var j = 0, l = contentNode.childNodes.length; j < l; j++) {
        alternate.appendChild(contentNode.childNodes[j].cloneNode(true));
      }

      // Allow the sIFR content to be modified
      if(kwargs.modifyContent) kwargs.modifyContent(contentNode, kwargs.selector);
      if(kwargs.modifyCss) cssText = kwargs.modifyCss(css, contentNode, kwargs.selector);

      var content = handleContent(contentNode, textTransform);
      if(kwargs.modifyContentString) content.text = kwargs.modifyContentString(content.text, kwargs.selector);
      if(content == '') continue;
      var vars = ['content=' + util.escape(content.text), // Don't touch this line!
                  'width=' + width, 'height=' + height, 'fitexactly=' + (fitExactly ? 'true' : ''),
                  'tunewidth=' + (kwargs.tuneWidth || ''), 'tuneheight=' + tuneHeight,
                  'offsetleft=' + (kwargs.offsetLeft || ''), 'offsettop=' + (kwargs.offsetTop || ''),
                  'thickness=' + (kwargs.thickness || ''), 'sharpness=' + (kwargs.sharpness || ''), 
                  'kerning=' + kerning, 'gridfittype=' + gridFitType, 'zoomsupport=' + ua.zoomSupport, 
                  'flashfilters=' + filters, 'opacity=' + opacity, 'blendmode=' + (kwargs.blendMode || ''), 
                  'size=' + lineHeight, 'zoom=' + dom.getZoom(), 'css=' + util.escape(cssText),
                  'selectable=' + (kwargs.selectable == null ? 'true' : kwargs.selectable),
                  // Lines aren't strictly needed, but they help for the findRatios debug method
                  'lines=' + lines, 'fixhover=' + (SIFR.fixHover ? 'true' : ''), 
                  'antialiastype=' + (kwargs.antiAliasType || ''), 
                  'preventwrap=' + (kwargs.preventWrap ? 'true' : 'false'),
                  'link=' + util.escape(content.primaryLink[0] || ''), 'target=' + util.escape(content.primaryLink[1] || '')];
      var encodedVars = encodeVars(vars);

      var callbackName = 'sIFR_callback_' + elementCount++;
      var callbackInfo = new CallbackInfo(callbackName, vars, kwargs.onReplacement);
      window[callbackName + '_DoFSCommand'] = (function(callbackInfo) {
        return function(info, arg) {
          callbackInfo.handle(info, arg);
        }
      })(callbackInfo);

      // Approximate the final height to avoid annoying movements of the page
      height = Math.round(lines * getRatio(lineHeight, ratios) * lineHeight) + FLASH_PADDING_BOTTOM + tuneHeight;

      var forcedWidth = forceWidth ? width : '100%';

      var flash;
      if(ua.ie) {
        flash = [
          '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="', callbackName,
           '" sifr="true" width="', forcedWidth, '" height="', height, '" class="', CSS_FLASH, '">',
            '<param name="movie" value="', src, '"></param>',
            '<param name="flashvars" value="', encodedVars, '"></param>',
            '<param name="allowScriptAccess" value="always"></param>',
            '<param name="quality" value="best"></param>',
            '<param name="wmode" value="', wmode, '"></param>',
            '<param name="bgcolor" value="', backgroundColor, '"></param>',
            '<param name="name" value="', callbackName, '"></param>',
          '</object>',
          // Load in the callback code. Keep the <script> line exactly the same!!! (Yes, IE is that crappy)
          // Thanks to Tom Lee for the tip: <http://tom-lee.blogspot.com/2006/04/dynamically-inserting-fscommand.html>
          '<scr', 'ipt event=FSCommand(info,args) for=', callbackName, '>', 
            callbackName, '_DoFSCommand(info, args);',
          '</', 'script>' // End like this to prevent syntax error in IE/Mac.
        ].join('');
      } else {
        flash = [
          '<embed type="application/x-shockwave-flash"', (delayCss ? ' class="' + CSS_FLASH + '"' : ''),
          ' src="', src,'" quality="best" flashvars="', encodedVars, '" width="', forcedWidth, '" height="', height,
          '" wmode="', wmode, '" bgcolor="', backgroundColor, '" name="', callbackName, '" id="', callbackName,
          '" allowScriptAccess="always" sifr="true"></embed>'
        ].join('');
      }

      dom.setInnerHtml(node, flash);
      callbackInfo.flashNode = node.firstChild;
      callbackInfo.html = flash;
      SIFR.callbacks.push(callbackInfo);
      if(kwargs.selector) {
        if(!SIFR.callbacks[kwargs.selector]) SIFR.callbacks[kwargs.selector] = [callbackInfo];
        else SIFR.callbacks[kwargs.selector].push(callbackInfo);
      }
      node.appendChild(alternate);
      dom.addClass(delayCss ? CSS_REPLACING : CSS_REPLACED, node);
    }

    hacks.fragmentIdentifier.restore();
  };

  this.getCallbackByFlashElement = function(node) {
    for(var i = 0; i < SIFR.callbacks.length; i++) {
      if(SIFR.callbacks[i].id == node.getAttribute('id')) return SIFR.callbacks[i];
    }
  };
  
  this.redraw = function() {
    for(var i = 0; i < SIFR.callbacks.length; i++) SIFR.callbacks[i].resetMovie();
  };
  
  function encodeVars(vars) {
    return vars.join('&amp;').replace(/%/g, '%25');
  }

  /*=:private
    Walks through the childNodes of `source`. Generates a text representation of these childNodes.

    Returns:
    * string: the text representation.

    Notes:
    * A number of items are still to do. See the individual comments for that.
    * This method does not recursion because it'll be necessary to keep a 
      count of all links and their URIs. This is easier without recursion.
  */
  function handleContent(source, textTransform) {
    var stack = [], content = [], primaryLink = [];
    var nodes = source.childNodes;

    var i = 0;
    while(i < nodes.length) {
      var node = nodes[i];

      if(node.nodeType == 3) {
        var text = util.normalize(node.nodeValue);
        text = util.textTransform(textTransform, text);
        content.push(text);
      }

      if(node.nodeType == 1) {
        var attributes = [];
        var nodeName = node.nodeName.toLowerCase();

        var className = node.className || '';
        // If there are multiple classes, look for the specified sIFR class
        if(/\s+/.test(className)) {
          if(className.indexOf(CSS_CLASS) > -1) className = className.match('(\\s|^)' + CSS_CLASS + '-([^\\s$]*)(\\s|$)')[2];
          // or use the first class
          else className = className.match(/^([^\s]+)/)[1];
        }
        if(className != '') attributes.push('class="' + className + '"');

        if(nodeName == 'a') {
          var href = node.getAttribute('href') || '';
          var target = node.getAttribute('target') || '';
          attributes.push('href="' + href + '"', 'target="' + target + '"');
          
          if(primaryLink.length == 0) primaryLink = [href, target];
        }

        content.push('<' + nodeName + (attributes.length > 0 ? ' ' : '') + attributes.join(' ') + '>');

        if(node.hasChildNodes()) {
          // Push the current index to the stack and prepare to iterate
          // over the childNodes.
          stack.push(i);
          i = 0;
          nodes = node.childNodes;
          continue;
        } else if(!/^(br|img)$/i.test(node.nodeName)) content.push('</', node.nodeName.toLowerCase(), '>');
      }

      if(stack.length > 0 && !node.nextSibling) {
        // Iterating the childNodes has been completed. Go back to the position
        // before we started the iteration. If that position was the last child,
        // go back even further.
        do {
          i = stack.pop();
          nodes = node.parentNode.parentNode.childNodes;
          node = nodes[i];
          if(node) content.push('</', node.nodeName.toLowerCase(), '>');
        } while(i == nodes.length - 1 && stack.length > 0);
      }

      i++;
    }
  
    return {text: content.join('').replace(/\n|\r/g, ''), primaryLink: primaryLink};
  }

  function CallbackInfo(id, vars, replacementHandler, fixHover) {
    this.id         = id;
    this.vars       = vars;

    this._replacementHandler = replacementHandler;
    this._firedReplacementEvent = !(this._replacementHandler != null);
    this._fixHover = fixHover;
    this._setClasses = !SIFR.delayCss;
    this.html       = '';
  }
  
  CallbackInfo.prototype.getFlashElement = function() {
    return document.getElementById(this.id);
  };

  CallbackInfo.prototype.available = function() {
    var flashNode = this.getFlashElement();
    return flashNode && flashNode.parentNode;
  };
  
  CallbackInfo.prototype.handle = function(info, arg) {
    if(!this.available()) return;
    
    if(/(FSCommand\:)?resize/.test(info)) {
      var flashNode = this.getFlashElement();

      var $ = arg.split(/\:|,/);
      flashNode.setAttribute($[0], $[1]);
      if($.length > 2) flashNode.setAttribute($[2], $[3]);

      if(!this._setClasses) {
        // IE needs the Flash movie to be visible for the callbacks to work.
        if(!ua.ie && !ua.opera) dom.addClass(CSS_FLASH, flashNode);
        dom.removeClass(CSS_REPLACING, flashNode.parentNode);
        dom.addClass(CSS_REPLACED, flashNode.parentNode);
        this._setClasses = true;
      }
      
      // Here comes another story!
      //
      // Good old Safari (saw this in 2.0.3) does not see the resizing
      // of the Flash movie as requiring a repaint of the document. Because
      // of this, the movie won't be resized unless Safari is forced to.
      // This is done by requesting the `offsetHeight` on the Flash node.
      //
      // Just to be sure this hack is applied to all browsers which
      // implement the KHTML engine.
      if(ua.khtml) var repaint = flashNode.offsetHeight;
      
      if(!this._firedReplacementEvent) {
        this._replacementHandler(this);
        this._firedReplacementEvent = true;
      }
    } else if(/(FSCommand\:)?resetmovie/.test(info)) {
      this.resetMovie();
    } else if(this.debugHandler && /(FSCommand\:)?debug/.test(info)) {
      this.debugHandler(info, arg);
    }
  };
  
  CallbackInfo.prototype.call = function(type, value) {
    if(!this.available()) return false;

    var flashNode = this.getFlashElement();
    try {
      flashNode.SetVariable('callbackType', type);
      flashNode.SetVariable('callbackValue', value);
      flashNode.SetVariable('callbackTrigger', true);
    } catch(e) {
      return false;
    }
    
    return true;
  };

  // `content` must not be util.escaped.
  CallbackInfo.prototype.replaceText = function(content) {
    content = util.escape(content);
    this.vars[0] = 'content=' + content;
    this.html = this.html.replace(/(flashvars(=|\"\svalue=)\")[^\"]+/, '$1' + encodeVars(this.vars));
    return this.call('replacetext', content);
  };
  
  CallbackInfo.prototype.resetMovie = function() {
    if(!this.available()) return;
    
    var flashNode = this.getFlashElement();
    var node = flashNode.parentNode;
    // Not using outerHTML because the Flash vars will be lost.
    node.replaceChild(dom.nodeFromHtml(this.html), flashNode);
  };
};
