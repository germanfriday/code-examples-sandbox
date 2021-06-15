// Version 2.0
// *** CROSS-BROWSER COMPATIBILITY ***

var isDOM = (document.getElementById ? true : false);
var isIE4 = ((document.all && !isDOM) ? true : false);
var isNS4 = (document.layers ? true : false);
var isDyn = (isDOM || isIE4 || isNS4);

function getRef(id)
{
 if (isDOM) return document.getElementById(id);
 if (isIE4) return document.all[id];
 if (isNS4) return document.layers[id];
}

function getSty(id)
{
 return (isNS4 ? getRef(id) : getRef(id).style);
}

// *** LOAD FILES INTO DIV FUNCTIONS ***

function scrLoad(name) { with (this)
{
 if (!name) return;

 // If you feel like extending this code a bit in an easy way, why not copy-and-paste the
 // visibility settings from the end of scrFileLoaded() to here... but hide the scroller and
 // show the loading message for a while? Just a thought...

 // The location of this file, used as a starting point for all relative files
 // e.g. loadFile('downloads/index.html').
 var path = location.href.substring(0, location.href.lastIndexOf('/') + 1);

 if (isDOM || isIE4)
 {
  // The document currently loaded into the buffer.
  oldLoc = bufRef.location.href;

  // If we haven't been given a full URL with a protocol, add the path to this point.
  if ((name.substring(0, 5) != 'http:') && (name.substring(0, 5) != 'file:'))
   name = path + name;

  // This is a kludge, since NS6 doesn't support Iframe onloads yet. Give it 5 secs to load.
  if (isDOM && !document.all) setTimeout(myName + '.bufRef.document.readyState="complete"', 5000);

  if (oldLoc != name)
  {
   // If we're not reloading the same file, set frame location and start checking routine.
   bufRef.location.href = name;
   checkBuffer(oldLoc);
  }
  // Else if we're reloading the same file, just pass a dummy old location so routine
  // will presume the new one is different and load immediately.
  else checkBuffer('dummyLocation');
 }
 else
 {
  // Once done, display it and scroll to top.
  divRef.onload = new Function(myName + '.fileLoaded()');
  // Netscape's load(URL, width) method...
  divRef.load(name, cWidth);
 }
}}

function scrCheckBuffer(oldLoc) { with (this)
{
 // If the frame hasn't started loading a new file or hasn't finished, recheck in 50ms.
 if ((bufRef.location.href == oldLoc) || (bufRef.document.readyState != 'complete'))
  setTimeout(myName + '.checkBuffer(\'' + oldLoc + '\')', 50);
 else
 {
  // Transfer frame's content to the div.
  divRef.innerHTML = bufRef.document.body.innerHTML;
  // Remove file from buffer, to allow refreshing - not strictly necessary.
  bufRef.location.href = 'about:blank';
  // That's it, we're done! The timeout helps IE4 giving it a chance to calculate div height.
  setTimeout(myName + '.fileLoaded()', 1);
 }
}}

function scrFileLoaded() { with (this)
{
 // Stop dragging any scroller while we switch files.
 activeScr = null;

 // Back to top...
 scrollBy(-100000);

 // And set visibilities now everything's finally ready.
 loadSty.visibility = 'hidden';
 for (count = 0; count < divs.length; count++)
  getSty(divs[count][0]).visibility = 'visible';
}}

// *** DIV SCROLLING FUNCTION ***

function scrScrollBy(amount) { with (this)
{
 if (!isDyn || !loaded) return;

 // Because this gets passed fractions sometimes.
 amount = parseInt(amount);

 // Height of div - update here as it may change often. Find it out somehow :).
 divHeight = document.all ? divRef.clientHeight :
  (isDOM ? divRef.offsetHeight : divRef.document.height);
 // Stops 'divide by zero'
 if (divHeight == 0) divHeight = 1;

 // Are we scrolling out of range? If so, return to top/bottom.
 if ((cBot + amount) > divHeight) amount = divHeight - cBot;
 if ((cTop + amount) < 0) amount = 0 - cTop;

 // Adjust clipping values & move div up/down... 'scrolling' div.
 cTop += amount;
 cBot += amount;
 if (isDOM || isIE4)
  divSty.clip = 'rect(' + cTop + 'px ' + cWidth + 'px ' + cBot + 'px ' + 0 + 'px)';
 else if (isNS4)
 {
  // NS4 bug fix, seems to be required, as sometimes these get undefined... why?
  // I think it's because one onMouseMove gets called before another finishes, and somehow
  // NS4 must lose track of its variables.
  if (isNaN(cTop) || isNaN(cBot) || isNaN(amount)) layout();

  // Order of adjustment matters, cannot overlap opposing clips.
  if (cBot < divSty.clip.top) divSty.clip.top = cTop;
  divSty.clip.bottom = cBot;
  divSty.clip.top = cTop;
 }

 divSty.top = eval(divs[0][2]) - cTop;

 // Define its height as the percentage of the clipping height vs div height.
 // Best to set it here as divHeight may change as images load etc.
 thmHeight = Math.ceil(barHeight * ((cBot - cTop) / divHeight));

 // Minimum and maximum heights for thumb...
 if (thmHeight < minThmHeight) thmHeight = minThmHeight;
 if (thmHeight > barHeight) thmHeight = barHeight;

 if (isDOM || isIE4) thmSty.height = thmHeight;
 else if (isNS4) thmSty.clip.height = thmHeight;

 // Adjust scrollbar thumb position only if we're not already dragging it.
 if (activeScr) return;

 // What fraction is the div of its total scrolling range? 0=top, 1=bottom.
 fracDivDown = (cTop / (divHeight - (cBot - cTop)));
 // Now, multiply that by the available space to move and assign its top.
 thmSty.top = parseInt(barSty.top) + fracDivDown * (barHeight - thmHeight);

 // If we're looping, set a timeout to scroll by amount again.
 if (loop) setTimeout(myName + '.scrollBy(' + amount + ')', loop);
}}

// *** SCROLL THUMB DRAGGING EVENT HANDLERS ***

function scrThumbDown(evt)
{
 // Set a variable pointing to the active scroller - this scroller object.
 activeScr = this;

 // Offset of mouse cursor within the scrollbar by browser, first IE then NS 6 then 4...
 if (document.all) scrOffset = event.offsetY;
 else scrOffset = evt.layerY;
 return false;
}

function scrThumbMove(evt)
{
 if (isNS4) document.routeEvent(evt);

 // Either return true if no scroller is being dragged (so selections work), or...
 if (!activeScr) return true;
 else { with (activeScr)
 {
  // Workaround for error in NS...
  if (isNaN(cTop) || isNaN(cBot)) divLayout();

  // If it's not scrollable, quit.
  if ((cBot > divHeight) || (thmHeight == barHeight)) return;

  if (document.all) newTop = document.body.scrollTop + event.clientY - scrOffset;
  else newTop = evt.pageY - scrOffset;

  // Test if the thumb is out of range, if so, bring it back, then assign its position.
  var barTop = parseInt(barSty.top);
  if (newTop < barTop) newTop = barTop;
  if (newTop + thmHeight > (barTop + barHeight))
   newTop = (barTop + barHeight) - thmHeight;
  thmSty.top = newTop;

  // Fractions of total range moved by the div and scrollbar...
  fracDivDown = cTop / (divHeight - (cBot - cTop));
  fracBarDown = (newTop - barTop) / (barHeight - thmHeight);

  // ... then scroll by the required difference to make up.
  scrollBy((fracBarDown - fracDivDown) * (divHeight - (cBot - cTop)));

  return false;
 }}
}

function scrThumbUp(evt)
{
 if (isNS4) document.routeEvent(evt);
 activeScr = null;
}

// *** SCROLLBAR BACKGROUND CLICK EVENT HANDLER ***

function scrBarClick(evt) { with (this)
{
 if (isNS4) document.routeEvent(evt);

 if (document.all) clickPos = document.body.scrollTop + event.clientY;
 else clickPos = evt.pageY;

 // Page up, or page down?
 if (clickPos < parseInt(thmSty.top)) scrollBy(cTop - cBot);
 if (clickPos > (parseInt(thmSty.top) + thmHeight)) scrollBy(cBot - cTop);
}}

// *** LAYOUT HANDLER FOR WINDOW RESIZE ETC ***

function scrLayout() { with (this)
{
 if (!isDyn) return;

 winWidth = document.all ? document.body.clientWidth : window.innerWidth;
 winHeight = document.all ? document.body.clientHeight : window.innerHeight;

 if (isNS4 && (scrFirstWidth != winWidth))
 {
  // Sorry folks, NS4 has major bugs resizing horizontally and must refresh...
  fileName = location.href;
  if (fileName.indexOf('?') != -1) fileName = fileName.substring(0, fileName.indexOf('?'));
  location.href = fileName + '?loadFile=' + divRef.src;
 }

 // If it's not yet initialised, go no further.
 if (!loaded) return;

 // Minimum values for window sizes.
 if (winWidth < minWinWidth) winWidth = minWinWidth;
 if (winHeight < minWinHeight) winHeight = minWinHeight;

 // Loop through divs array, positioning/sizing controls.
 for (count = 0; count < divs.length; count++)
 {
  var tmpObj = getSty(divs[count][0]);
  if (divs[count][1]) tmpObj.left = eval(divs[count][1]);
  if (divs[count][2]) tmpObj.top = eval(divs[count][2]);
  if (divs[count][3])
  {
   var tmpW = eval(divs[count][3]);
   isNS4 ? tmpObj.clip.width = tmpW : tmpObj.width = tmpW;
  }
  // Don't set the height of the first scrolling div, we have to pick this up later.
  if (divs[count][4] && count != 0)
  {
   var tmpH = eval(divs[count][4]);
   isNS4 ? tmpObj.clip.height = tmpH : tmpObj.height = tmpH;
  }
 }

 // Set the top, bottom & width clip variables.
 if ((isDOM || isIE4) && !cTop) cTop = 0;
 // This will fix 'undefined' errors in NS.
 if (isNS4) cTop = divSty.clip.top;

 // Bar height and clipping parameters, stored here as accessed often. Set width on-the-fly.
 barHeight = eval(divs[1][4]);
 cBot = cTop + eval(divs[0][4]);
 cWidth = eval(divs[0][3]);
 if (isDOM || isIE4) divSty.width = cWidth;

 // Now, display it using updated variables...
 scrollBy(0);
}}

// *** ON LOAD: CAPTURE EVENTS & MISC. SETUP ***

function scrSetup(defaultFile) { with (this)
{
 if (!isDyn) return;

 // References to document objects' properties, stored in scroller object.
 divRef = getRef(divs[0][0]);
 divSty = getSty(divs[0][0]);
 barSty = getSty(divs[1][0]);
 thmSty = getSty(divs[2][0]);
 // Convert these ID's to references now document has loaded.
 loadSty = getSty(loadSty);
 // NS6 has troubles with the frames array and iframes, so use a workaround.
 bufRef = eval('window.' + bufRef);

 // Other variables - references to bar and thumb divs, only used locally.
 barRef = getRef(divs[1][0]);
 thmRef = getRef(divs[2][0]);

 if (isNS4)
 {
  barRef.captureEvents(Event.CLICK);
  thmRef.captureEvents(Event.MOUSEDOWN);
  document.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
 }

 // Pass events to specific scrollers.
 barRef.onclick = new Function('evt', 'return ' + myName + '.barClick(evt)');
 thmRef.onmousedown = new Function('evt', 'return ' + myName + '.thumbDown(evt)');
 // The mouseMove and mouseUp events are global.
 document.onmousemove = scrThumbMove;
 document.onmouseup = scrThumbUp;

 // For IE4+/NS6, create a new function that stops selections being made when dragging.
 if (document.all) document.onselectstart = new Function('if (activeScr) return false');
 else if (isDOM) document.onselect = new Function('if (activeScr) return false');

 // It's now ready to go, call resize function to set the positions and variables...
 loaded = true;
 layout();

 // Get the name of the file to be loaded from a CGI query string...
 fileName = '';
 if (location.href.indexOf('?') != -1)
  fileName = location.href.substring(location.href.lastIndexOf('=') + 1);

 // Contains string '.htm'...? (can be '.html' file of course)
 if (fileName.indexOf('.htm') != -1) load(fileName)

 // Else load default file (if we're passed one) or scroll existing content without loading.
 else if (defaultFile) load(defaultFile);
 else fileLoaded();
}}

// Main object of which instances are created.
function DHTMLScroller(myName, bufferID, loadMess)
{
 // Some references to objects that are referred to repeatedly for speed.
 this.divRef = null;
 this.divSty = null;
 this.barSty = null;
 this.thmSty = null;

 // Array of objects to move when the window is resized (e.g. scrollbar, arrows).
 this.divs = new Array();

 // Store the ID's of these objects here for now. Convert to references later...
 this.loadSty = loadMess;
 this.bufRef = bufferID;

 // Properties.
 this.myName = myName;
 this.loaded = false;
 // A non-zero value for loop will cause a scrollBy call to repeat after that many milliseconds.
 this.loop = 0;
 // Minimum height of scrollbar thumb - defaults to 20, set to something else if you want.
 this.minThmHeight = 20;

 this.divHeight = 0;
 this.barHeight = 0;
 this.thmHeight = 0;
 this.cTop = 0;
 this.cBot = 0;
 this.cWidth = 0;

 // Methods - bind to functions above.
 this.load = scrLoad;
 this.checkBuffer = scrCheckBuffer;
 this.fileLoaded = scrFileLoaded;
 this.scrollBy = scrScrollBy;
 this.thumbDown = scrThumbDown;
 this.barClick = scrBarClick;
 this.setup = scrSetup;
 this.layout = scrLayout;
}

// *** GLOBAL VARIABLES - START EDITING HERE ***

// Some global variables for the scroller code - scrFirstWidth is only for NS4 resize bug.
var activeScr = null, scrOffset = 0, winWidth, winHeight, scrFirstWidth = window.innerWidth;

// You must declare all scrollers, timeouts as global variables as well.
var mainDiv;

// Minimum window sizes - script will use these if actual sizes are too low.
var minWinWidth = 500, minWinHeight = 100;

// *** SCROLLER OBJECT SETUP ***.

// name = new DHTMLScroller('name', 'iframe buffer id', 'loading message id');
mainDiv = new DHTMLScroller('mainDiv', 'bufferFrame', 'loadMess');

with (mainDiv)
{
 // divs[number] = new Array('id of div below', 'x', 'y', 'width', 'height');
 // Dimensions are evaluable strings so they can include variables. Null strings are skipped.
 // Div 0 is the main scroller, 1=Bar, 2=Thumb.
 divs[0] = new Array('mainContentDiv', '21', '190', 'winWidth - 100', 'winHeight - 50');
 divs[1] = new Array('scrollBar', 'winWidth - 95', '190', '15', 'winHeight - 175');
 // Don't bother passing Y and Height parameters for the thumb.
 divs[2] = new Array('scrollThumb', 'winWidth - 94', '', '13', '');

 // Anything past that is not special, just moved/sized with the window. Add extra divs!
 divs[3] = new Array('upArrows', 'winWidth - 95', '50', '', '');
 divs[4] = new Array('downArrows', 'winWidth - 95', 'winHeight - 85', '', '');
}


// *** SCROLLING BY KEYPRESS HANDLER - delete this if you want ***

// You must 'hardcode' a scroller name one the first line, as only one can respond to keys.
function scrCheckKeys(evt) { with (mainDiv)
{
 if (!loaded) return;
 if (document.all) key = event.keyCode;
 else if (isDOM) key = evt.charCode;
 else if (isNS4) key = evt.which;

 //alert(key);
 // Depending on key press (capital || lowercase), scroll div top/up/down/bottom.
 if ((key == 84) || (key == 116)) scrollBy(-100000); // 'T' = Top.
 if ((key == 65) || (key == 97))  scrollBy(-10);     // 'A' = Up.
 if ((key == 90) || (key == 122)) scrollBy(10);      // 'Z' = Down.
 if ((key == 66) || (key == 98))  scrollBy(100000);  // 'B' = Bottom.
}}

if (isNS4) document.captureEvents(Event.KEYPRESS);
document.onkeypress = scrCheckKeys;