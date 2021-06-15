<?
# be compatible with old PHP engine versions that don't define $_POST (before PHP 4.1.0):
if (!isset($_POST))
  $_POST=$HTTP_POST_VARS;

# disable PHP notices to simplify:
error_reporting(E_ALL ^ E_NOTICE);

# create the turbine object:
$turbine = new Turbine7();

$turbine->setVariable("media.compress", $_POST["media_compress"]);

if ($_POST["media_pdf_subject"])
	$turbine->setVariable("media.pdf.subject", $_POST["media_pdf_subject"]);

if ($_POST["media_pdf_author"])
	$turbine->setVariable("media.pdf.author", $_POST["media_pdf_author"]);

if ($_POST["media_pdf_title"])
	$turbine->setVariable("media.pdf.title", $_POST["media_pdf_title"]);

if ($_POST["media_pdf_userpassword"])
	$turbine->setVariable("media.pdf.userpassword", $_POST["media_pdf_userpassword"]);

if ($_POST["media_pdf_ownerpassword"])
	$turbine->setVariable("media.pdf.ownerpassword", $_POST["media_pdf_ownerpassword"]);

if ($_POST["media_pdf_permissions"])
	$turbine->setVariable("media.pdf.permissions", join(", ", $_POST["media_pdf_permissions"]) );


$turbine->create("<text pos='10, 10' size='18'>Encryption and Permissions Demo</text>");
$turbine->create("<text pos='10, 30' size='7'>(Press 'Ctrl+D' on Adobe Acrobat Reader to see the document properties)</text>");

$turbine->create("<text pos='10, 50' size='14'>Compression (Media.Compress=" . $_POST["media_compress"] . "):</text>");
if ($_POST["media_compress"] != "0")
	$turbine->create("<text pos='10, 65' size='12'>This document is compressed</text>");
else
	$turbine->create("<text pos='10, 65' size='12'>This document is not compressed</text>");

$turbine->create("<text pos='10, 90' size='14'>Document Subject (Media.PDF.Subject):</text>");
if ($_POST["media_pdf_subject"])
	$turbine->create("<text pos='10, 105' size='12'>This document's subject is '" . $_POST["media_pdf_subject"] . "'</text>");
else
	$turbine->create("<text pos='10, 105' size='12'>This document has no subject</text>");

$turbine->create("<text pos='10, 130' size='14'>Document Author (Media.PDF.Author):</text>");
if ($_POST["media_pdf_author"])
	$turbine->create("<text pos='10, 145' size='12'>This document's author is '" . $_POST["media_pdf_author"] . "'</text>");
else
	$turbine->create("<text pos='10, 145' size='12'>This document has no author</text>");

$turbine->create("<text pos='10, 170' size='14'>Document Title (Media.PDF.Title):</text>");
if ($_POST["media_pdf_author"])
	$turbine->create("<text pos='10, 185' size='12'>This document's title is '" . $_POST["media_pdf_title"] . "'</text>");
else
	$turbine->create("<text pos='10, 185' size='12'>This document has no title</text>");

$turbine->create("<text pos='10, 210' size='14'>Document Encryption:</text>");
if ($_POST["media_pdf_userpassword"] || $_POST["media_pdf_ownerpassword"]) {
	if ($_POST["media_pdf_userpassword"])
		$turbine->create("<text pos='10, 225' size='12' width='600'>This document's open password (media.pdf.userpassword) is '" . $_POST["media_pdf_userpassword"] . "'</text>");
	else
		$turbine->create("<text pos='10, 225' size='12' width='600'>This document has no open password (media.pdf.userpassword)</text>");

	if ($_POST["media_pdf_ownerpassword"])
		$turbine->create("<text pos='10, 240' size='12' width='600'>This document's encryption password (media.pdf.ownerpassword) is '" . $_POST["media_pdf_ownerpassword"] . "'</text>");
	else
		$turbine->create("<text pos='10, 240' size='12' width='600'>This document has no encryption password (media.pdf.ownerpassword)</text>");

	if ($_POST["media_pdf_permissions"])
		$turbine->create("<text pos='10, 255' size='12' width='600'>This document's permissions (media.pdf.permissions) are '" . join(", ", $_POST["media_pdf_permissions"]) . "'</text>");
	else
		$turbine->create("<text pos='10, 255' size='12' width='600'>This document has no permissions (media.pdf.permissions) set</text>");
}
else
	$turbine->create("<text pos='10, 225' size='12'>This document is not encrypted and therefore it has no permissions set</text>");

# browsers should not cache this request:
header ("Expires: Sat, 01 Jan 2000 01:01:01 GMT");

# generate also to file
//$turbine->generateToFile("out.pdf", "pdf");

# now generate the media to the web browser
$turbine->generatePDF();

?>