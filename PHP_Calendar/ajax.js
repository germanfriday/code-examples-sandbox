function createRequestObject() {
 var xmlhttp;
   /*@cc_on
   @if (@_jscript_version >= 5)
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
       xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } 
    catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;
}

var http = createRequestObject();
var targetDiv = "";

function sndReq(action) {
	if(action.indexOf('AddItem=') >= 0) {
  		var pos = action.indexOf('=');
  		if(pos >= 0) {
	  		var toks = action.substring(pos + 1, action.length).split(':');
			targetDiv = 'AddItemDiv';
		 	http.open("GET", "AddItemForm.php?month=" + toks[1] + "&date=" + toks[2] + "&year=" + toks[0], true);
	  		http.onreadystatechange = handleResponse;
	  		http.send(null);
	  	}
	}
  	if(action.indexOf('view_item=') >= 0) {
  		var pos = action.indexOf('=');
  		if(pos >= 0) {
	  		var toks = action.substring(pos + 1, action.length).split(':');
			var item_id = toks[0];
  			var page_num = toks[1];
	  		targetDiv = 'AddItemDiv';
		 	http.open("GET", "ViewItem.php?item_id=" + item_id + "&page=" + page_num, true);
	  		http.onreadystatechange = handleResponse;
	  		http.send(null);
	  	}
	}
  	if(action.indexOf('edit_item=') >= 0) {
  		var pos = action.indexOf('=');
  		if(pos >= 0) {
	  		var toks = action.substring(pos + 1, action.length).split(':');
			var item_id = toks[0];
  			var page_num = toks[1];
  			//document.write(item_id + '-' + page_num); 
  			targetDiv = 'AddItemDiv';
 		 	http.open("GET", "EditItem.php?item_id=" + item_id + "&page=" + page_num, true);
  			http.onreadystatechange = handleResponse;
  			http.send(null);
  		}
  	}
  	if(action == 'dispose') {
  		document.getElementById('AddItemDiv').innerHTML = '';
  	}
}

function handleResponse(action) {
	   if(http.readyState == 4){
	  		result = http.responseText;
			document.getElementById(targetDiv).innerHTML = result;
		}
}

function sayHello() {
	document.write("Hello");
}
