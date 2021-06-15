<?php
/**
 * 
 * class uploader - File Upload Helper
 * 
 * @copyright   2002, 2005 
 * @author      David Fox, Dave Tufts
 * @version     3.1
 * @last_update 2005-09-06
 * @requires    PHP 4.1 or higher
 * 
 * @changes v3.1   - edited Swedish text by Birger Eriksson
 * @changes v3.0   - updated comments for phpdoc
 * @changes v2.16  - Added Swedish (se) error messaging
 * @changes v2.15  - Added Danish (da) error messaging
 * @changes v2.14  - Edited acceptable_file_types checks to be more lenient
 * @changes v2.13  - Added Spanish (es) and Norwegian (no) error messaging, converted all non-valid HTML language chars to named entities
 * @changes v2.12  - Added Finnish (fi) error messaging
 * @changes v2.11  - Fixed bug if $this->save_file::$path is ""
 * @changes v2.10  - Added var $path to class definition
 * @changes v2.9   - Updated error_message[5] for NL (Dutch)
 * @changes v2.8   - Cleaned up Italian error messaging (thanks to Maurizio Lemmo - http://www.tenzione.it/ )
 * @changes v2.7   - Added new error code [5] to save_file() method, fixed minor bug if unable to write to upload directory
 * @changes v2.6   - Added $this->acceptable_file_types. Fixed minor bug fix in upload() - if file 'type' is null
 * @changes v2.5.2 - Added Italian (it) error messgaing
 * @changes v2.5.1 - Added German (de) and Dutch (nl) error messgaing
 * @changes v2.4   - Added error messgae language preferences
 * @changes v2.3.1 - Bugfix for upload $path in $this->save_file()
 * @changes v2.3   - Initialized all variables (compatibale with PHP error notices)
 * @changes v2.2   - Changed ereg() to stristr() whenever possible
 * 
 * Language specific error messaging:
 *     + [fr] Frank from http://www.ibigin.com - initial code and French text
 *     + [de] lmg from http://www.kishalmi.net - German text
 *     + [nl] Andre, a.t.somers@student.utwente.nl - Dutch text
 *     + [it] Enrico Valsecchi http://www.hostyle.it <admin@hostyle.it> - Italian text
 *     + [fi] Dotcom Media Solutions, http://www.dotcom.ms - Finnish text
 *     + [es] Alejandro Ramirez <alex@cinenganos.com> - Spanish text
 *     + [no] Sigbjorn Eide <seide@tiscali.no> - Norwegian text
 *     + [da] Thomas Hannibal http://hannibalsoftware.dk/ - Danish Text
 *     + [se] Birger Eriksson, PCK art - Swedish Text
 * 
 *	Error code: available in multiple languages
 *     + [0] - "No file was uploaded"
 *     + [1] - "Maximum file size exceeded"
 *     + [2] - "Maximum image size exceeded"
 *     + [3] - "Only specified file type may be uploaded"
 *     + [4] - "File already exists" (save only)
 *     + [5] - "Permission denied. Unable to copy file"
 *
 */
class uploader {

	var $file;
	var $path;
	var $language;
	var $acceptable_file_types;
	var $error;
	var $errors; // Depreciated (only for backward compatability)
	var $accepted;
	var $max_filesize;
	var $max_image_width;
	var $max_image_height;


	/**
	 * uploader - Class constructor, sets error messaging language preference
	 * 
	 * @param string language	defaults to en (English).
	 * @return object
	 * 
	 */
	function uploader ( $language = 'en' ) {
		$this->language = strtolower($language);
		$this->error   = '';
	}
	
	
	/**
	 * max_filesize - Set the maximum file size in bytes ($size), allowable by the object.
	 * 
	 * NOTE: PHP's configuration file also can control the maximum upload size, which is set to 2 or 4 
	 * megs by default. To upload larger files, you'll have to change the php.ini file first.
	 * 
	 * @param int size file size in bytes
	 * @return void
	 * 
	 */
	function max_filesize($size){
		$this->max_filesize = (int) $size;
	}


	/**
	 * max_image_size- Sets the maximum pixel dimensions. 
	 * 
	 * Will only be checked if the uploaded file is an image
	 * 
	 * @param int width      maximum pixel width of image uploads
	 * @param int height     maximum pixel height of image uploads
	 * @return void
	 * 
	 */
	function max_image_size($width, $height){
		$this->max_image_width  = (int) $width;
		$this->max_image_height = (int) $height;
	}
	
	
	/**
	 * upload - Checks if the file is acceptable and uploads it to PHP's default upload diretory
	 * 
	 * @param filename		(string) form field name of uploaded file
	 * @param accept_type	(string) acceptable mime-types
	 * @param extension		(string) default filename extenstion
	 * @return bool
	 * 
	 */
	function upload($filename='', $accept_type='', $extention='') {
		
		$this->acceptable_file_types = trim($accept_type); // used by error messages
		
		if (!isset($_FILES) || !is_array($_FILES[$filename]) || !$_FILES[$filename]['name']) {
			$this->error = $this->get_error(0);
			$this->accepted  = FALSE;
			return FALSE;
		}
				
		// Copy PHP's global $_FILES array to a local array
		$this->file = $_FILES[$filename];
		$this->file['file'] = $filename;
		
		// Initialize empty array elements
		if (!isset($this->file['extention'])) $this->file['extention'] = "";
		if (!isset($this->file['type']))      $this->file['type']      = "";
		if (!isset($this->file['size']))      $this->file['size']      = "";
		if (!isset($this->file['width']))     $this->file['width']     = "";
		if (!isset($this->file['height']))    $this->file['height']    = "";
		if (!isset($this->file['tmp_name']))  $this->file['tmp_name']  = "";
		if (!isset($this->file['raw_name']))  $this->file['raw_name']  = "";
				
		// test max size
		if($this->max_filesize && ($this->file["size"] > $this->max_filesize)) {
			$this->error = $this->get_error(1);
			$this->accepted  = FALSE;
			return FALSE;
		}
		
		if(stristr($this->file["type"], "image")) {
			
			/* IMAGES */
			$image = getimagesize($this->file["tmp_name"]);
			$this->file["width"]  = $image[0];
			$this->file["height"] = $image[1];
			
			// test max image size
			if(($this->max_image_width || $this->max_image_height) && (($this->file["width"] > $this->max_image_width) || ($this->file["height"] > $this->max_image_height))) {
				$this->error = $this->get_error(2);
				$this->accepted  = FALSE;
				return FALSE;
			}
			// Image Type is returned from getimagesize() function
			switch($image[2]) {
				case 1:
					$this->file["extention"] = ".gif"; break;
				case 2:
					$this->file["extention"] = ".jpg"; break;
				case 3:
					$this->file["extention"] = ".png"; break;
				case 4:
					$this->file["extention"] = ".swf"; break;
				case 5:
					$this->file["extention"] = ".psd"; break;
				case 6:
					$this->file["extention"] = ".bmp"; break;
				case 7:
					$this->file["extention"] = ".tif"; break;
				case 8:
					$this->file["extention"] = ".tif"; break;
				default:
					$this->file["extention"] = $extention; break;
			}
		} elseif(!ereg("(\.)([a-z0-9]{3,5})$", $this->file["name"]) && !$extention) {
			// Try and autmatically figure out the file type
			// For more on mime-types: http://httpd.apache.org/docs/mod/mod_mime_magic.html
			switch($this->file["type"]) {
				case "text/plain":
					$this->file["extention"] = ".txt"; break;
				case "text/richtext":
					$this->file["extention"] = ".txt"; break;
				default:
					break;
			}
		} else {
			$this->file["extention"] = $extention;
		}
		
		// check to see if the file is of type specified
		if($this->acceptable_file_types) {
			if(trim($this->file["type"]) && (stristr($this->acceptable_file_types, $this->file["type"]) || stristr($this->file["type"], $this->acceptable_file_types)) ) {
				$this->accepted = TRUE;
			} else { 
				$this->accepted = FALSE;
				$this->error = $this->get_error(3);
			}
		} else { 
			$this->accepted = TRUE;
		}
		
		return (bool) $this->accepted;
	}


	/**
	 * save_file - Cleans up the filename, copies the file from PHP's temp location to $path, 
	 * 
	 * save_file() also checks the overwrite_mode and renames the file if needed
	 * 
	 * overwrite_mode:
	 *    + 1 = overwrite files with the same name
	 *    + 2 = rename files with the same name.
	 *    + 3 = do nothing if file name already exists
	 * 
	 * overwrite_mode 2 works like this:
	 *    file.txt  is uploaded
	 *    another 'file.txt' is uploaded, and renamed file_copy0.txt
	 *    another 'file.txt' is uploaded, and renamed file_copy1.txt
	 *
	 * 
	 * @param string path          File path to your upload directory
	 * @param int overwrite_mode   Must be 1, 2, or 3
	 * @return bool
	 * 
	 */
	function save_file($path, $overwrite_mode="3"){
		if ($this->error) {
			return false;
		}
		
		if (strlen($path)>0) {
			if ($path[strlen($path)-1] != "/") {
				$path = $path . "/";
			}
		}
		$this->path = $path;	
		$copy       = "";	
		$n          = 1;	
		$success    = false;	
				
		if($this->accepted) {
			// Clean up file name (only lowercase letters, numbers and underscores)
			$this->file["name"] = ereg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($this->file["name"]))));
			
			// Clean up text file breaks
			if(stristr($this->file["type"], "text")) {
				$this->cleanup_text_file($this->file["tmp_name"]);
			}
			
			// get the raw name of the file (without its extenstion)
			if(ereg("(\.)([a-z0-9]{2,5})$", $this->file["name"])) {
				$pos = strrpos($this->file["name"], ".");
				if(!$this->file["extention"]) { 
					$this->file["extention"] = substr($this->file["name"], $pos, strlen($this->file["name"]));
				}
				$this->file['raw_name'] = substr($this->file["name"], 0, $pos);
			} else {
				$this->file['raw_name'] = $this->file["name"];
				if ($this->file["extention"]) {
					$this->file["name"] = $this->file["name"] . $this->file["extention"];
				}
			}
			
			switch((int) $overwrite_mode) {
				case 1: // overwrite mode
					if (@copy($this->file["tmp_name"], $this->path . $this->file["name"])) {
						$success = true;
					} else {
						$success     = false;
						$this->error = $this->get_error(5);
					}
					break;
				case 2: // create new with incremental extention
					while(file_exists($this->path . $this->file['raw_name'] . $copy . $this->file["extention"])) {
						$copy = "_copy" . $n;
						$n++;
					}
					$this->file["name"]  = $this->file['raw_name'] . $copy . $this->file["extention"];
					if (@copy($this->file["tmp_name"], $this->path . $this->file["name"])) {
						$success = true;
					} else {
						$success     = false;
						$this->error = $this->get_error(5);
					}
					break;
				default: // do nothing if exists, highest protection
					if(file_exists($this->path . $this->file["name"])){
						$this->error = $this->get_error(4);
						$success     = false;
					} else {
						if (@copy($this->file["tmp_name"], $this->path . $this->file["name"])) {
							$success = true;
						} else {
							$success     = false;
							$this->error = $this->get_error(5);
						}
					}
					break;
			}
			
			if(!$success) { unset($this->file['tmp_name']); }
			return (bool) $success;
		} else {
			$this->error = $this->get_error(3);
			return FALSE;
		}
	}
	
	
	/**
	 * get_error - Gets the correct error message for language set by constructor
	 * 
	 * @param int error_code
	 * @return string
	 * 
	 */
	function get_error($error_code='') {
		$error_message = array();
		$error_code    = (int) $error_code;
		
		switch ( $this->language ) {
			// French (fr)
			case 'fr':
				$error_message[0] = "Aucun fichier n'a &eacute;t&eacute; envoy&eacute;";
				$error_message[1] = "Taille maximale autoris&eacute;e d&eacute;pass&eacute;e. Le fichier ne doit pas &ecirc;tre plus gros que " . $this->max_filesize/1000 . " Ko (" . $this->max_filesize . " octets).";
				$error_message[2] = "Taille de l'image incorrecte. L'image ne doit pas d&eacute;passer " . $this->max_image_width . " pixels de large sur " . $this->max_image_height . " de haut.";
				$error_message[3] = "Type de fichier incorrect. Seulement les fichiers de type " . str_replace("|", " or ", $this->acceptable_file_types) . " sont autoris&eacute;s.";
				$error_message[4] = "Fichier '" . $this->path . $this->file["name"] . "' d&eacute;j&aacute; existant, &eacute;crasement interdit.";
				$error_message[5] = "La permission a ni&eacute;. Incapable pour copier le fichier &aacute; '" . $this->path . "'";
			break;
			
			// German (de)
			case 'de':
				$error_message[0] = "Es wurde keine Datei hochgeladen";
				$error_message[1] = "Maximale Dateigr&ouml;sse &uuml;berschritten. Datei darf nicht gr&ouml;sser als " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes) sein.";
				$error_message[2] = "Maximale Bildgr&ouml;sse &uuml;berschritten. Bild darf nicht gr&ouml;sser als " . $this->max_image_width . " x " . $this->max_image_height . " pixel sein.";
				$error_message[3] = "Nur " . str_replace("|", " oder ", $this->acceptable_file_types) . " Dateien d&uuml;rfen hochgeladen werden.";
				$error_message[4] = "Datei '" . $this->path . $this->file["name"] . "' existiert bereits.";
				$error_message[5] = "Erlaubnis hat verweigert. Unf&amul;hig, Akte zu '" . $this->path . "'";
			break;

			// Dutch (nl)
			case 'nl':
				$error_message[0] = "Er is geen bestand geupload";
				$error_message[1] = "Maximum bestandslimiet overschreden. Bestanden mogen niet groter zijn dan " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes).";
				$error_message[2] = "Maximum plaatje omvang overschreven. Plaatjes mogen niet groter zijn dan " . $this->max_image_width . " x " . $this->max_image_height . " pixels.";
				$error_message[3] = "Alleen " . str_replace("|", " of ", $this->acceptable_file_types) . " bestanden mogen worden geupload.";
				$error_message[4] = "Bestand '" . $this->path . $this->file["name"] . "' bestaat al.";
				$error_message[5] = "Toestemming is geweigerd. Kon het bestand niet naar '" . $this->path . "' copieren.";
				//$error_message[5] = "Toestemming ontkende. Onbekwaam dossier aan '" . $this->path . "'";
			break;

			// Italian (it)
			case 'it':
				$error_message[0] = "Il file non e' stato salvato";
				$error_message[1] = "Il file e' troppo grande. La dimensione massima del file e' " . $this->max_filesize/1000 . " Kb (" . $this->max_filesize . " bytes).";
				$error_message[2] = "L'immagine e' troppo grande. Le dimensioni massime non possono essere superiori a " . $this->max_image_width . " pixel di larghezza per " . $this->max_image_height . " d'altezza.";
				$error_message[3] = "Il tipo di file non e' valido. Solo file di tipo " . str_replace("|", " o ", $this->acceptable_file_types) . " sono autorizzati.";
				$error_message[4] = "E' gia' presente un file con nome " . $this->path . $this->file["name"];
				$error_message[5] = "Permesso negato. Impossibile copiare il file in '" . $this->path . "'";
			break;

  			// Finnish
			case 'fi':
				$error_message[0] = "Tiedostoa ei l&amul;hetetty.";
				$error_message[1] = "Tiedosto on liian suuri. Tiedoston koko ei saa olla yli " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " tavua).";
				$error_message[2] = "Kuva on liian iso. Kuva ei saa olla yli " . $this->max_image_width . " x " . $this->max_image_height . " pikseli&amul;.";
				$error_message[3] = "Vain " . str_replace("|", " tai ", $this->acceptable_file_types) . " tiedostoja voi tallentaa kuvapankkiin.";
				$error_message[4] = "Tiedosto '" . $this->path . $this->file["name"] . "' on jo olemassa.";
				$error_message[5] = "Ei k&amul;ytt&ouml;oikeutta. Tiedostoa ei voi kopioida hakemistoon '" . $this->path . "'";
			break;

 			// Spanish
			case 'es':
				$error_message[0] = "No se subi&oacute; ning&uacute;n archivo.";
				$error_message[1] = "Se excedi&oacute; el tama&ntilde;o m&aacute;ximo del archivo. El archivo no puede ser mayor a " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes).";
				$error_message[2] = "Se excedieron las dimensiones de la imagen. La imagen no puede medir m&aacute;s de " . $this->max_image_width . " (w) x " . $this->max_image_height . " (h) pixeles.";
				$error_message[3] = "El tipo de archivo no es v&aacute;lido. S&oacute;lo los archivos " . str_replace("|", " o ", $this->acceptable_file_types) . " son permitidos.";
				$error_message[4] = "El archivo '" . $this->path . $this->file["name"] . "' ya existe.";
				$error_message[5] = "Permiso denegado. No es posible copiar el archivo a '" . $this->path . "'";
			break;		

			// Norwegian
			case 'no':
				$error_message[0] = "Ingen fil ble lastet opp.";
				$error_message[1] = "Max filst&oslash;rrelse ble oversteget. Filen kan ikke være st&oslash;rre ennn " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " byte).";
				$error_message[2] = "Max bildest&oslash;rrelse ble oversteget. Bildet kan ikke være st&oslash;rre enn " . $this->max_image_width . " x " . $this->max_image_height . " piksler.";
				$error_message[3] = "Bare " . str_replace("|", " tai ", $this->acceptable_file_types) . " kan lastes opp.";
				$error_message[4] = "Filen '" . $this->path . $this->file["name"] . "' finnes fra f&oslash;r.";
				$error_message[5] = "Tilgang nektet. Kan ikke kopiere filen til '" . $this->path . "'";
			break;

			// Danish
			case 'da':
				$error_message[0] = "Ingen fil blev uploaded";
				$error_message[1] = "Den maksimale filstørrelse er overskredet. Filerne må ikke være større end " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes).";
				$error_message[2] = "Den maksimale billedstørrelse er overskredet. Billeder må ikke være større end " . $this->max_image_width . " x " . $this->max_image_height . " pixels.";
				$error_message[3] = "Kun " . str_replace("|", " or ", $this->acceptable_file_types) . " kan uploades.";
				$error_message[4] = "Filen '" . $this->path . $this->file["name"] . "' eksisterer allerede.";
				$error_message[5] = "Adgang nægtet! Er ikke i stand til at kopiere filen til '" . $this->path . "'";
			break;


			// Swedish
			case 'se':
				$error_message[0] = "Ingen fil laddades upp";
				$error_message[1] = "Den maximala filstorleken &ouml;verskreds. Filer f&aring;r inte vara st&ouml;rre &auml;n " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes).";
				$error_message[2] = "Den maximala bildstorleken &ouml;verskreds. Bilder f&aring;r inte vara st&ouml;rre &auml;n " . $this->max_image_width . " x " . $this->max_image_height . " pixels.";
				$error_message[3] = "Endast " . str_replace("|", " or ", $this->acceptable_file_types) . " f&aring;r laddas upp.";
				$error_message[4] = "Filen '" . $this->path . $this->file["name"] . "' finns redan.";
				$error_message[5] = "Tillg&aring;ng nekad! Kan inte kopiera filen till '" . $this->path . "'";
			break;

			// English
			default:
				$error_message[0] = "No file was uploaded";
				$error_message[1] = "Maximum file size exceeded. File may be no larger than " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes).";
				$error_message[2] = "Maximum image size exceeded. Image may be no more than " . $this->max_image_width . " x " . $this->max_image_height . " pixels.";
				$error_message[3] = "Only " . str_replace("|", " or ", $this->acceptable_file_types) . " files may be uploaded.";
				$error_message[4] = "File '" . $this->path . $this->file["name"] . "' already exists.";
				$error_message[5] = "Permission denied. Unable to copy file to '" . $this->path . "'";
			break;
		}
		
		// for backward compatability:
		$this->errors[$error_code] = $error_message[$error_code];
		
		return $error_message[$error_code];
	}


	/**
	 * void cleanup_text_file (string file);
	 * 
	 * Convert Mac and/or PC line breaks to UNIX by opening
	 * and rewriting the file on the server
	 * 
	 * @param file			(string) Path and name of text file
	 * 
	 */
	function cleanup_text_file($file){
		// chr(13)  = CR (carridge return) = Macintosh
		// chr(10)  = LF (line feed)       = Unix
		// Win line break = CRLF
		$new_file  = '';
		$old_file  = '';
		$fcontents = file($file);
		while (list ($line_num, $line) = each($fcontents)) {
			$old_file .= $line;
			$new_file .= str_replace(chr(13), chr(10), $line);
		}
		if ($old_file != $new_file) {
			// Open the uploaded file, and re-write it with the new changes
			$fp = fopen($file, "w");
			fwrite($fp, $new_file);
			fclose($fp);
		}
	}

}


/*
<license>

	///// fileupload-class.php /////
	Copyright (c) 1999, 2002, 2005 
	Dave Tufts, http://imarc.net; David Fox, Angryrobot Productions
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without 
	modification, are permitted provided that the following conditions 
	are met:
	1. Redistributions of source code must retain the above copyright 
	   notice, this list of conditions and the following disclaimer.
	2. Redistributions in binary form must reproduce the above 
	   copyright notice, this list of conditions and the following 
	   disclaimer in the documentation and/or other materials provided 
	   with the distribution.
	3. Neither the name of author nor the names of its contributors 
	   may be used to endorse or promote products derived from this 
	   software without specific prior written permission.

	DISCLAIMER:
	THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS "AS IS" 
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
	TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
	PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR 
	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
	SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
	LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
	USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
	AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
	LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
	IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF 
	THE POSSIBILITY OF SUCH DAMAGE.

</license>
*/
?>