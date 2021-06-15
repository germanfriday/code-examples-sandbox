/*=:project
    scalable Inman Flash Replacement (sIFR) version 3, revision 278

    Provides debug information about sIFR.

  =:file
    Copyright: 2006 Mark Wubben.
    Author: Mark Wubben, <http://novemberborn.net/>

  =:license
    * This software is licensed and provided under the CC-GNU LGPL
    * See <http://creativecommons.org/licenses/LGPL/2.1/>    
*/

sIFR.debug = new function() {
  // Initialize sIFR
  sIFR.debugMode = true;
  sIFR.errors = {
    isFile: 'sIFR: Did not activate because the page is being loaded from the filesystem.',
    getSource: 'sIFR: Could not determine appropriate source'
  }; // Added by the export script.
  
  function log(msg) {
    if(!sIFR.ua.safari && window.console && console.log) console.log(msg);
    else alert(msg);
  }
  
  function merge(kwargs, mergeKwargs) {
    if(mergeKwargs) {
      for(var property in kwargs) {
        if(typeof(mergeKwargs[property]) == 'undefined') mergeKwargs[property] = kwargs[property];
      }
      kwargs = mergeKwargs;
    }
    
    return kwargs;
  }
  
  this.ua = function() {
    var info = [];
    
    for(var prop in sIFR.ua) {
      if(sIFR.ua[prop] == Object.prototype[prop]) continue;
      
      info.push(prop, ': ', sIFR.ua[prop], '\n');
    }
    
    log(info.join(''));
  };
  
  this.domains = function() {
    var valid = sIFR.domains.length == 0;
    
    // The validation code is copied from the private sIFR code. Please keep 
    // up to date!
    
    var domain = '';
    try { // When trying to access document.domain on a Google-translated page with Firebug, I got an exception.
      domain = document.domain;
    } catch(e) {};

    for(var i = 0; i < sIFR.domains.length; i++) {
      if(sIFR.domains[i] == '*' || sIFR.domains[i] == domain) {
        valid = true;
        break;
      }
    }
    
    log(['The domain "', domain, '" is ', valid ? 'valid' : 'invalid', '.\nList of checked domains: ', sIFR.domains].join(''));
  };

  this.ratios = function(kwargs, mergeKwargs) {
    kwargs = merge(kwargs, mergeKwargs);
    
    var running = false;
    kwargs.onReplacement = function(cb) {
      if(running) return; // Prevent duplicate results
      running = true;
      
      cb.debugHandler = function(info, args) {
        if(/(FSCommand\:)?debug\:ratios/.test(info)) prompt('The ratios for ' + kwargs.selector + ' are:', args);
      }
      cb.call('ratios', '');
    };

    sIFR.replace(kwargs);
  };
  
  function verifyResource(uri, fail, ok) {
    if(sIFR.ua.ie && uri.charAt(0) == '/') {
      uri = window.location.toString().replace(/([^:]+)(:\/?\/?)([^\/]+).*/, '$1$2$3') + uri;
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', uri, true);
    xhr.onreadystatechange = function() {
      if(xhr.readyState == 4) {
        if(xhr.status != 200) log(fail);
        else log(ok);
      }
    };
    xhr.send('');
  }

  this.test = function(kwargs, mergeKwargs) {
    kwargs = merge(kwargs, mergeKwargs);

    var src = kwargs.src;
    var checked = false;
    if(typeof(src) != 'string') {
      if(src.src) src = src.src;

      if(typeof(src) != 'string') {
        var versions = [];
        for(var version in src) if(src[version] != Object.prototype[version]) versions.push(version);
        versions.sort().reverse();

        var result = '';
        var i = -1;
        while(!result && ++i < versions.length) {
          if(parseFloat(versions[i]) <= ua.flashVersion) result = src[versions[i]];
          var msg = '<' + src[versions[i]] + '>, flash ' + parseFloat(versions[i]);
          verifyResource(src[versions[i]], 'FAILED: ' + msg, 'OK: ' + msg);
        }
        
        src = result;
        checked = true;
      }
    }
    
    if(!src) log('Could not determine appropriate source.');
    else if(!checked) verifyResource(src, 'FAILED: <' + src + '>', 'OK: <' + src + '>');
  };
  
  this.forceTest = function() {
    var replace = sIFR.replace;
    sIFR.replace = function(kwargs, mergeKwargs) {
      sIFR.debug.test(kwargs, mergeKwargs);
      replace.call(sIFR, kwargs, mergeKwargs);
    };
  }
};