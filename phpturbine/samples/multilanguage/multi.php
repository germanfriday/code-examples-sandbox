<?
# disable PHP notices to simplify:
error_reporting(E_ALL ^ E_NOTICE);

# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# a function to convert UTF-8 chars into escaped XML entities:
function xml_escape_utf8($utf8){
  $escaped = "";
  $utf8_len=strlen($utf8);

  for ($n=0; $n<$utf8_len; $n++){

    if(ord($utf8[$n]) < 0x80)
      $escaped .= "&#" . ord($utf8[$n]) . ";";
    else 
    if (ord($utf8[$n]) < 0xe0){
      if($n+1 >= $utf8_len)
        break;
      $escaped .= "&#" . ( ((ord($utf8[$n]) & 0x1f) << 6) | (ord($utf8[$n+1]) & 0x3f) ) . ";";
      $n++;
    }
    else
    if (ord($utf8[$n]) < 0xf0) {
      if($n+2 >= $utf8_len)
        break;
      $escaped .= "&#" . (  ((ord($utf8[$n]) & 0x0f) << 12) | ((ord($utf8[$n+1]) & 0x3f) << 6) | (ord($utf8[$n+2]) & 0x3f)  ) . ";";
      $n+=2;
    }
    else {
      if($n+3 >= $utf8_len)
        break;
      $escaped .= "&#" . (  ((ord($utf8[$n]) & 0x07) << 18) | ((ord($utf8[$n+1]) & 0x3f) << 12) | ((ord($utf8[$n+2]) & 0x3f) << 6) | (ord($utf8[$n+3]) & 0x3f)  ) . ";";
      $n+=3;
    }

  } // for each utf8 char

  return $escaped;
}



# create the turbine object:
$turbine = new Turbine7();

# use the above funciont to convert UTF-8 into escaped XML (also get rid of PHP's \'):
$turbine->setVariable("txt", xml_escape_utf8( str_replace( "\'", "'", $_GET["txt"]) ) );

# create a static text that uses the {txt} Turbine variable defined above:
$turbine->create("<text font='MS Mincho' pos='10, 10' size='18'><pre>{txt}</pre><br/><br/><br/><br/><font size='11'>Note: You must have 'MS Mincho' font installed on the web server machine where Turbine is running for this sample to work correctly.</font></text>");

# browsers should not cache this request:
header ("Expires: Sat, 01 Jan 2000 01:01:01 GMT");

# now generate the media to the web browser
if ($_GET["pdf"])
	$turbine->generatePDF();
else
	$turbine->generateFlash();

?>