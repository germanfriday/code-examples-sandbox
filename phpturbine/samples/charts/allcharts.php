<?
# disable PHP notices to simplify:
error_reporting(E_ALL ^ E_NOTICE);

# be compatible with old PHP engine versions that don't define $_GET and $_SERVER (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;
if (!isset($_SERVER))
  $_SERVER=$HTTP_SERVER_VARS;


if($_GET["flash"]){ ?>
<HTML>
<HEAD>
<TITLE>Turbine Charts Sample</TITLE>
</HEAD>
<BODY>
<CENTER>
<? if($_GET["tooltip"] == "Yes"){ ?>
<font size=2 face="Verdana">Move the mouse over the chart columns for tooltip details...</font><br>
<? } ?>

<? if($_GET["url"] == "Yes"){ ?>
<font size=2 face="Verdana">Each chart column can have a separate link allowing for clickable drill-downs.</font><br>
<? } ?>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Area&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Area&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Area3d&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Area3d&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Pie&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Pie&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Pie3d&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Pie3d&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Column&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Column&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Column3d&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Column3d&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=CoolColumn&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=CoolColumn&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Glass&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Glass&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Line&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Line&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Line3d&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Line3d&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=LineMarker&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=LineMarker&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Plain&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Plain&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Scatter&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Scatter&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Stick&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Stick&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Triangle&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Triangle&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
        ID="sw" WIDTH="98%" HEIGHT="95%">
  <PARAM NAME=movie VALUE="chart.php?type=Triangle3d&<? echo $_SERVER['QUERY_STRING']; ?>">
  <PARAM NAME=quality VALUE="best">
  <PARAM NAME=bgcolor VALUE="#ffffff">

  <EMBED src="chart.php?type=Triangle3d&<? echo $_SERVER['QUERY_STRING']; ?>" 
         quality="best" bgcolor="#ffffff" 
         WIDTH=550 HEIGHT=400	swLiveConnect=false
         TYPE="application/x-shockwave-flash" 
         PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
  </EMBED>

</OBJECT>
<BR>

<font size=2 face="Verdana">The charts are dynamically built from the 'Transactions' DataSet, loaded from <a href="transactions_dataset.txt" target="_blank">here</a>.</font><br>

</CENTER>
</BODY>
</HTML>
<? } else {
# PDF charts - redirect to chart.php:
  header("Location: chart.php?" . $_SERVER['QUERY_STRING']);

}?>