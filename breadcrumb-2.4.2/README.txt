File: $Id: class.breadcrumb.inc.php, 2004/04/30 00:56 PDT
-----------------------------------------------------------------------
Purpose of file: Show the directories and their links in path form
                 Home > Firstdir > Seconddir > Etc > filename.php
Information: If you use this script please contact me with a url or
             product information plus the product :) and please keep
             all header information intact. Cheers!
Pay-Pal info: paypal@baskettcase.com
-----------------------------------------------------------------------
@access public
@author Richard Baskett <rick@baskettcase.com>
@category directory structure
@copyright Copyright © 2004, Baskettcase Web Development
@example example.php
@link http://www.baskettcase.com/classes/breadcrumb/
@package breadcrumb
@version 2.4.2

SIMPLE EXAMPLE: (copy between the == lines and paste to your file
  between your php tags)
=======================================================================
<?php
 include_once('class.breadcrumb.inc.php');
 $breadcrumb = new breadcrumb;
 echo "<p>".$breadcrumb->show_breadcrumb()."</p>";
?>
=======================================================================

COMPLEX EXAMPLE: (copy between the == lines and paste to your file)
=======================================================================
<?php
 include_once('class.breadcrumb.inc.php');
 $breadcrumb = new breadcrumb;
 $breadcrumb->homepage='homepage'; // sets the home directory name
 $breadcrumb->dirformat='ucfirst'; // Show the directory in this style
 $breadcrumb->symbol=' || '; // set the separator between directories 
 $breadcrumb->showfile=TRUE; // shows the file name in the path
 $breadcrumb->special='elmer'; // special directory formatting
 $breadcrumb->changeName=array('dirname1'=>'Directory Name 1',
                               'dirname2'=>'Directory Name 2',
                               'dirname3'=>'Directory Name 3',
                               'dirname4'=>'Directory Name 4');
 $breadcrumb->changeFileName=array($_SERVER['PHP_SELF']=>'Example Page',
                                   '/index.htm'=>'Contact Us');
 $breadcrumb->fileExists=array('index.htm','index.php','default.htm');
 $breadcrumb->cssClass='crumb'; // css class to use
 $breadcrumb->target='_top'; // frame target
 $breadcrumb->linkFile=TRUE; // Link the file to itself
 $breadcrumb->_toSpace=TRUE; // converts underscores to spaces
 echo "<p>".$breadcrumb->show_breadcrumb()."</p>";
?>
=======================================================================

DEFINITIONS: (Not setting any of these variables will result in the
              DEFAULT settings)
              
-----------------------------------------------------------------------
GLOBAL OPTIONS
-----------------------------------------------------------------------
(homepage) sets the name of the home directory, leave empty if you do
   not want the home directory to show (DEFAULT = 'home')
-----------------------------------------------------------------------
(symbol) set the separator between directories (DEFAULT = ' > ')
-----------------------------------------------------------------------
(special) special directory formatting
   elmer = elmer fudd translation
   hacker = hacker speach translation
   pig = pig latin translation
   reverse = Reverses the text so it is backwards
   none = no special formatting (DEFAULT)
-----------------------------------------------------------------------
(cssClass) Use a css class to define the look of your breadcrumb.
  (DEFAULT = No css style
-----------------------------------------------------------------------
(_toSpace) Converts underscores '_' to a space in directory names.
  (DEFAULT = Keep underscores, no conversion
-----------------------------------------------------------------------
(target) set the frameset target value (DEFAULT '_self')
-----------------------------------------------------------------------

DIRECTORY OPTIONS
-----------------------------------------------------------------------
(dirformat) can be of type:
   ucwords = uppers case words (use with _toSpace)
   titlecase = upper case words except small words (the, is, with, etc)
   ucfirst = upper case first letter
   uppercase = all uppercase
   lowercase = all lowercase
   none = show directories as they are named in path structure (DEFAULT)
-----------------------------------------------------------------------
(imagedir) sets the image directory path, plus the image type. 
  (DEFAULT - Does not use images) You need to name your images the same
  as the actual directory names.
-----------------------------------------------------------------------
(changeName) Directory mapping/alias.  Alias your actual directory name
  to one of your choosing. For example if your actual directory
  structure is /temp/educ/science/bio/ you can map each directory name
  to /Temporary/Education/Science/Biology and display it as such.
  (DEFAULT = No aliasing, display the actual directory name)
-----------------------------------------------------------------------
(removeDirs) remove directories from the breadcrumb. So for example if
   your breadcrumb looked like this:
   home > classes > help > status > test.php
   Using this function you can hide/remove the directory to show this:
   home > classes > help > text.php
-----------------------------------------------------------------------
(unlinkCurrentDir) Remove the link to the current directory 
  (DEFAULT = FALSE)
-----------------------------------------------------------------------

FILE OPTIONS
-----------------------------------------------------------------------
(showfile) shows the file name in the path (DEFAULT = TRUE)
-----------------------------------------------------------------------
(fileExists) Link this directory only if one of the filenames exists
  Set your webservers default webpages that it checks for  when a 
  webpage has not been specified in the url.  So for example many 
  webservers look for a page called index.htm, index.html, index.php,
  default.htm, default.html, default.php.  Or you can use this variable 
  to just look for files you specify and link the directory if they
  exist.
  (DEFAULT = Link directories regardless of whether or not the
  directory has an index or default file)
-----------------------------------------------------------------------
(hideFileExt) hides the file extension (DEFAULT = FALSE)
-----------------------------------------------------------------------
(changeFileName) Filename mapping/alias.  Alias your file name to one 
  of your choosing. For example if your filename is example.php you can
  show it as 'Example Page'
  (DEFAULT = No aliasing
-----------------------------------------------------------------------
(linkFile) Links the file to itself (DEFAULT = FALSE)
-----------------------------------------------------------------------