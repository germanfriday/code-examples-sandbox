/***********************************************
* Scrollable content Script- © Dynamic Drive (www.dynamicdrive.com)
* This notice must stay intact for use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

var nsstyle='display:""'
if (document.layers)
var scrolldoc=document.scroll1.document.scroll2
function up(){
if (!document.layers) return
if (scrolldoc.top<0)

scrolldoc.top+=10
temp2=setTimeout("up()",50)
}
function down(){
if (!document.layers) return
if (scrolldoc.top-150>=scrolldoc.document.height*-1)
scrolldoc.top-=10
temp=setTimeout("down()",50)
}

function clearup(){
if (window.temp2)
clearInterval(temp2)
}

function cleardown(){
if (window.temp)
clearInterval(temp)
}

function closeit()
{
	window.close();
}

function resize(height)
{
    resizeTo(850,height);
}

function showDate(millis, serverOffset)
{
    var m_names = new Array("Jan", "Feb", "Mar",
                  "Apr", "May", "Jun", "Jul", "Aug", "Sep", 
                  "Oct", "Nov", "Dec");

    var clientDT=new Date();
    var clientOffset=clientDT.getTimezoneOffset()*60*1000;
    var finalOffset=Math.abs(serverOffset-clientOffset);

    var newMillis=millis+finalOffset;
    if(serverOffset<clientOffset)
    {
        newMillis=millis-finalOffset;
    }
    var dt=new Date(newMillis);
    var dd=dt.getDate();
    var mm=m_names[dt.getMonth()];
    var yyyy=dt.getFullYear();
    var curr_hour = dt.getHours();
    var a_p="pm";
    if (curr_hour < 12)
    {
        a_p = "am";
    }
    else
    {
        a_p = "pm";
    }
    if (curr_hour == 0)
    {
        curr_hour = 12;
    }
    if (curr_hour > 12)
    {
        curr_hour = curr_hour - 12;
    }

    var mins=dt.getMinutes();
    var minStr=mins;
    if(mins<10)
        minStr="0"+mins;

    var secs=dt.getSeconds();
    var secStr=secs;
    if(secs<10)
        secStr="0"+secs;

    document.write(mm+" "+dd+" "+curr_hour+":"+minStr+":"+secStr+" "+a_p);
}