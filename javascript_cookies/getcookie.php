<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><title>Untitled Document</title><SCRIPT LANGUAGE="JavaScript"> cookie_name = "dataCookie"; var YouWrote; function getName() { if(document.cookie) { index = document.cookie.indexOf(cookie_name); if (index != -1) { namestart = (document.cookie.indexOf("=", index) + 1); nameend = document.cookie.indexOf(";", index); if (nameend == -1) {nameend = document.cookie.length;} YouWrote = document.cookie.substring(namestart, nameend); return YouWrote; } } } YouWrote=getName(); if (YouWrote == "dataCookie") {YouWrote = "Nothing_Entered"} </SCRIPT> </head><body><SCRIPT LANGUAGE="javascript"> document.write("You Entered " +YouWrote+ "."); </SCRIPT></body></html>