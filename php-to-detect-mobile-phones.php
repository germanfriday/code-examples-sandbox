<?php

/*
PHP to detect mobile phpnes
http://www.andymoore.info/php-to-detect-mobile-phones/
Last updated: 28th February 2009
Version: 3.00

Subscribe to my RSS feed to keep updated of changes to this script:
http://andymoore.info/feed/

Andy Moore - dotMobi Certified Mobile Web Developer
http://www.andymoore.info/

Need hosting or domain names?
https://www.nostinghosting.com/

Copyright 2008 Andy Moore (email : andy@andymoore.info)

SHARED SOURCE LICENSE

This License governs use of the accompanying Software, and your use of the Software constitutes acceptance of this license.

You may use this Software for any non-commercial purpose, subject to the restrictions in this license. Some purposes which can be non-commercial are teaching, academic research, and personal experimentation. You may also distribute this Software with books or other teaching materials, or publish the Software on websites, that are intended to teach the use of the Software.

You may not use or distribute this Software or any derivative works in any form for commercial purposes. Examples of commercial purposes would be running business operations, licensing, leasing, or selling the Software, or distributing the Software for use with commercial products.

You may modify this Software and distribute the modified Software for non-commercial purposes, however, you may not grant rights to the Software or derivative works that are broader than those provided by this License. For example, you may not distribute modifications of the Software under terms that would permit commercial use, or under terms that purport to require the Software or derivative works to be sublicensed to others.

You may use any information in intangible form that you remember after accessing the Software. However, this right does not grant you a license to any of Andy Moore's copyrights or patents for anything you might create using such information.

In return, we simply require that you agree:

   1. Not to remove any copyright or other notices from the Software.

   2. That if you distribute the Software in any form, you will include a verbatim copy of this license.

   3. That if you distribute derivative works of the Software in source code form you do so only under a license that includes all of the provisions of this License, and if you distribute derivative works of the Software solely in object form you do so only under a license that complies with this License.

   4. That if you have modified the Software or created derivative works, and distribute such modifications or derivative works, you will cause the modified files to carry prominent notices so that recipients know that they are not receiving the original Software. Such notices must state: (i) that you have changed the Software; and (ii) the date of any changes.

   5. THAT THE SOFTWARE COMES "AS IS", WITH NO WARRANTIES. THIS MEANS NO EXPRESS, IMPLIED OR STATUTORY WARRANTY, INCLUDING WITHOUT LIMITATION, WARRANTIES OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE OR ANY WARRANTY OF TITLE OR NON-INFRINGEMENT. ALSO, YOU MUST PASS THIS DISCLAIMER ON WHENEVER YOU DISTRIBUTE THE SOFTWARE OR DERIVATIVE WORKS.

   6. THAT ANDY MOORE WILL NOT BE LIABLE FOR ANY DAMAGES RELATED TO THE SOFTWARE OR THIS LICENSE, INCLUDING DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL OR INCIDENTAL DAMAGES, TO THE MAXIMUM EXTENT THE LAW PERMITS, NO MATTER WHAT LEGAL THEORY IT IS BASED ON. ALSO, YOU MUST PASS THIS LIMITATION OF LIABILITY ON WHENEVER YOU DISTRIBUTE THE SOFTWARE OR DERIVATIVE WORKS.

   7. That if you sue anyone over patents that you think may apply to the Software or anyone's use of the Software, your license to the Software ends automatically.

   8. That your rights under the License end automatically if you breach it in any way.

   9. Andy Moore reserves all rights not expressly granted to you in this license.

*/

function detect_mobile_device(){
  
  // check if the user agent value claims to be windows but not windows mobile
  if(stristr($_SERVER['HTTP_USER_AGENT'],'windows')&&!stristr($_SERVER['HTTP_USER_AGENT'],'windows ce')){
    return false;
  }
  // check if the user agent gives away any tell tale signs it's a mobile browser
  if(eregi('up.browser|up.link|windows ce|iemobile|mini|mmp|symbian|midp|wap|phone|pocket|mobile|pda|psp',$_SERVER['HTTP_USER_AGENT'])){
    return true;
  }
  // check the http accept header to see if wap.wml or wap.xhtml support is claimed
  if(stristr($_SERVER['HTTP_ACCEPT'],'text/vnd.wap.wml')||stristr($_SERVER['HTTP_ACCEPT'],'application/vnd.wap.xhtml+xml')){
    return true;
  }
  // check if there are any tell tales signs it's a mobile device from the _server headers
  if(isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])||isset($_SERVER['X-OperaMini-Features'])||isset($_SERVER['UA-pixels'])){
    return true;
  }
  // build an array with the first four characters from the most common mobile user agents
  $a = array(
                    'acs-'=>'acs-',
                    'alav'=>'alav',
                    'alca'=>'alca',
                    'amoi'=>'amoi',
                    'audi'=>'audi',
                    'aste'=>'aste',
                    'avan'=>'avan',
                    'benq'=>'benq',
                    'bird'=>'bird',
                    'blac'=>'blac',
                    'blaz'=>'blaz',
                    'brew'=>'brew',
                    'cell'=>'cell',
                    'cldc'=>'cldc',
                    'cmd-'=>'cmd-',
                    'dang'=>'dang',
                    'doco'=>'doco',
                    'eric'=>'eric',
                    'hipt'=>'hipt',
                    'inno'=>'inno',
                    'ipaq'=>'ipaq',
                    'java'=>'java',
                    'jigs'=>'jigs',
                    'kddi'=>'kddi',
                    'keji'=>'keji',
                    'leno'=>'leno',
                    'lg-c'=>'lg-c',
                    'lg-d'=>'lg-d',
                    'lg-g'=>'lg-g',
                    'lge-'=>'lge-',
                    'maui'=>'maui',
                    'maxo'=>'maxo',
                    'midp'=>'midp',
                    'mits'=>'mits',
                    'mmef'=>'mmef',
                    'mobi'=>'mobi',
                    'mot-'=>'mot-',
                    'moto'=>'moto',
                    'mwbp'=>'mwbp',
                    'nec-'=>'nec-',
                    'newt'=>'newt',
                    'noki'=>'noki',
                    'opwv'=>'opwv',
                    'palm'=>'palm',
                    'pana'=>'pana',
                    'pant'=>'pant',
                    'pdxg'=>'pdxg',
                    'phil'=>'phil',
                    'play'=>'play',
                    'pluc'=>'pluc',
                    'port'=>'port',
                    'prox'=>'prox',
                    'qtek'=>'qtek',
                    'qwap'=>'qwap',
                    'sage'=>'sage',
                    'sams'=>'sams',
                    'sany'=>'sany',
                    'sch-'=>'sch-',
                    'sec-'=>'sec-',
                    'send'=>'send',
                    'seri'=>'seri',
                    'sgh-'=>'sgh-',
                    'shar'=>'shar',
                    'sie-'=>'sie-',
                    'siem'=>'siem',
                    'smal'=>'smal',
                    'smar'=>'smar',
                    'sony'=>'sony',
                    'sph-'=>'sph-',
                    'symb'=>'symb',
                    't-mo'=>'t-mo',
                    'teli'=>'teli',
                    'tim-'=>'tim-',
                    'tosh'=>'tosh',
                    'treo'=>'treo',
                    'tsm-'=>'tsm-',
                    'upg1'=>'upg1',
                    'upsi'=>'upsi',
                    'vk-v'=>'vk-v',
                    'voda'=>'voda',
                    'wap-'=>'wap-',
                    'wapa'=>'wapa',
                    'wapi'=>'wapi',
                    'wapp'=>'wapp',
                    'wapr'=>'wapr',
                    'webc'=>'webc',
                    'winw'=>'winw',
                    'winw'=>'winw',
                    'xda-'=>'xda-'
                  );
  // check if the first four characters of the current user agent are set as a key in the array
  if(isset($a[substr($_SERVER['HTTP_USER_AGENT'],0,4)])){
    return true;
  }
}

function detect_iphone(){
  if(eregi('iphone',$_SERVER['HTTP_USER_AGENT'])||eregi('ipod',$_SERVER['HTTP_USER_AGENT'])){
    return true;
  }
}

// example 1 - detect and redirect mobile browsers
if(detect_mobile_device()){
  header('Location: http://andymoore.mobi/');
  exit;
}

// example 2 - detect and redirect desktop browsers
if(!detect_mobile_device()){
  header('Location: http://andymoore.info/');
  exit;
}

function detect_iphone(){
  if(eregi('iphone',$_SERVER['HTTP_USER_AGENT'])){
    return true;
  }
}

// example 3 - send iphone to specific site
if(detect_iphone()){
  header('Location: http://iphonesite.mobi/');
  exit;
}

?>
