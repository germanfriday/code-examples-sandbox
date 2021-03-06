#--------------------------------#
# ABOUT
#--------------------------------#

fileupload.class.php can be used to upload image and text files with a web 
browser. The uploaded file's name will get cleaned up - special characters
will be deleted, and spaces get replaced with underscores, and moved to 
a specified directory (on your server). fileupload.class.php also does its
best to determine the file's type (text, GIF, JPEG, etc). If the user
has named the file with the correct extension (.txt, .gif, etc), then
the class will use that, but if the user tries to upload an ex tensionless
file, PHP does can identify text, gif, jpeg, and png files for you. As
a last resort, if there is no specified extension, and PHP can not 
determine the type, you can set a default extension to be added.

#--------------------------------#
# REQUIREMENTS
#--------------------------------#

To run and have fun with fileupload.class.php you will need access to a 
Unix/Linux/*nix web server running Apache with the PHP module, a web 
browser that supports uploading (like Netscape), and the 2 other files 
that came with this. 

#--------------------------------#
# QUICK SETUP
#--------------------------------#

(1) Make a new directory within your with you web directory 
called "fileupload"

(2) Upload the files "fileupload.class.php" and "upload.php" to your 
fileupload direcotory

(3) Make another directory within the "fileupload" directory called "uploads"
and give it enough upload permissions for you web server to upload to it.
(usually, this means making it world writable)
 - cd /your/web/dir/fileupload
 - mkdir uploads
 - chmod 777 uploads
4) Open your web browser and test it it out:
   http://www.yourdomain.com/fileupload/upload.php


#--------------------------------#
# DETAILED INSTRUCTIONS
#--------------------------------#

You should have downloaded 3 files and 1 folder:
 - README.txt (this file)
 - fileupload.class.php
 - upload.php
 - uploads (folder)
 
 Read the comments in upload.php and fileupload.class.php.


EXAMPLE FORM:
--------------
<form enctype="multipart/form-data" action="upload_page.php" method="POST">
<input type="hidden" name="submitted" value="true">
	Upload this file:<br>
	<input name="userfile" type="file">
	<br><br>
	<input type="submit" value="Send File">
</form>


EXAMPLE USAGE:
--------------
<?php
	require_once('fileupload.class.php');
	
	
	/* Create a new instance of the class
	 * 
	 * @examples:	$f = new uploader(); 		// English error messages
	 *				$f = new uploader('fr');	// French error messages
	 *				$f = new uploader('de');	// German error messages
	 *				$f = new uploader('nl');	// Dutch error messages
	 *				$f = new uploader('it');	// Italian error messages
	 *				$f = new uploader('fi');	// Finnish error messages
	 *				$f = new uploader('es');	// Spanish error messages
	 *				$f = new uploader('no');	// Norwegian error messages
	 *				$f = new uploader('da');	// Danish error messages
	 */
	$my_uploader = new uploader('en'); 
	
	
	// Set the max filesize of uploadable files in bytes
	$my_uploader->max_filesize(90000);
	
	
	// For images, you can set the max pixel dimensions 
	$my_uploader->max_image_size(150, 300); // ($width, $height)
	
	
	// UPLOAD the file
	$my_uploader->upload("userfile", "", ".jpg");
	
	
	// MOVE THE FILE to its final destination
	//	$mode = 1 ::	overwrite existing file
	//	$mode = 2 ::	rename new file if a file
	//	           		with the same name already 
	//             		exists: file.txt becomes file_copy0.txt
	//	$mode = 3 ::	do nothing if a file with the
	//	           		same name already exists
	$mode = 2;
	$my_uploader->save_file("uploads/", $mode);
	
	
	// Check if everything worked
	if ($my_uploader->error) {
		echo $my_uploader->error . "<br>";
	
	} else {
		// Successful upload!
		print($my_uploader->file['name'] . " was successfully uploaded!<br><br>\n");
		
		// If it's an image, let's display the file
		if(stristr($my_uploader->file['type'], "image")) {
			echo "<img src=\"uploads/" . $my_uploader->file['name'] . "\" border=\"0\" alt=\"\"><br><br>\n";
		}

	
	}
?>