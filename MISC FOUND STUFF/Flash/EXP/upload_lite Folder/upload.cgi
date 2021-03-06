#!/usr/bin/perl

# Installation Instructions
# http://www.perlscriptsjavascripts.com/perl/upload_lite/users_guide.html

# To order a custom install, please visit our "Secure order" page
# and enter the standard installation fee in the "Custom Quote" field

#################################################################### 
#
#	Upload Lite.
#	?2002, PerlscriptsJavaScript.com
#
#	Requirements:		Perl5 WINDOWS NT or UNIX
#	Created:			Febuary , 2001
#	Author: 			John Krinelos
#	Version:			3.22
#
#	Based on Upload Gold, first release : September 2001
#
#	This script is free, as long as this header and any copyright messages 
#	remains in tact. To remove copyright messages from public web pages you
# 	must purchase copyright removal. 
#	http://www.perlscriptsjavascripts.com/copyright_fees.html
#
#	Agent for copyright : 
#	Gene Volovich
#	Law Partners, 
#	140 Queen St. 
#	Melbourne
#	Ph. +61 3 9602 2266 
#	gvolovich@lawpartners.com.au
#	http://www.lawpartners.com.au/
#	
#################################################################### 

# START USER EDITS

# absolute path to folder files will be uploaded to.
# WINDOWS users, your path would like something like : images\\uploads
# UNIX    users, your path would like something like : /home/www/images/uploads
# do not end the path with any slashes and if you're on a UNIX serv, make sure
# you CHMOD each folder in the path to 777

$dir = "/path/to/demo_uploads";  
#$dir = "d:\\html\\users\\html\\images";

# absolute URL to folder files will be uploaded to
$folder = "http://www.yourserver.com/demo_uploads/";

# maximum file size allowed (kilo bytes)
$max = 100;

# for security reasons, enter your domain name. 
# this is so uploads may only occur from your domain
# enter any part of your domain name, or leave this 
# blank if you don't mind other web sites using your copy
$domain = "";

# if a file is successfully uploaded, enter a URL to redirect to.
# leave this blank to have the default message printed
$redirect = "";

# if you would like to be notified of uploads, enter your email address
# between the SINGLE quotes. leave this blank if you would not like to be notified
$notify = 'you@yourserver.com';

# UNIX users, if you entered a value for $notify, you must also enter your
# server's sendmail path. It usually looks something like : /usr/sbin/sendmail
$send_mail_path = "/usr/sbin/sendmail";

# WINDOWS users, if you entered a value for $notify, you must also enter your
# server's SMTP path. It usually looks something like : mail.servername.com
$smtp_path = "mail.yourserver.com";

# set to 1 if you would like all files in the directory printed to the web page
# after a successful upload (only printed if redirect is off). Set to 0 if you 
# do not want filenames printed to web page
$print_contents = 1;

# allow overwrites? 1 = yes, 0 = no (0 will rename file with a number on the end, the 
# highest number is the latest file)
$overwrite = 0;

# file types allowed, enter each type on a new line
# Enter the word "ALL" in uppercase, to accept all file types.
@types = qw~

txt
jpeg
jpg
gif

~;

####################################################################
#    END USER EDITS
####################################################################

$folder =~ s/(\/|\\)$//ig;

$OS = $^O; # operating system name
if($OS =~ /win/i) { $isWIN = 1; }
else {$isUNIX = 1;}
	
if($isWIN){ $S{S} = "\\\\"; }
else { $S{S} = "/";} # seperator used in paths

$ScriptURL = "http://$ENV{'SERVER_NAME'}$ENV{'SCRIPT_NAME'}";

unless (-d "$dir"){
	mkdir ("$dir", 0777); # unless the dir exists, make it ( and chmod it on UNIX )
	chmod(0777, "$dir");
}

unless (-d "$dir"){
	# if there still is no dir, the path entered by the user is wrong and the upload will fail
	&PrintHead; #print the header
	
	# get the Win root
	$ENV{PATH_INFO} =~ s/\//$S{S}/gi;
	$ENV{PATH_TRANSLATED} =~ s/$ENV{PATH_INFO}//i;
	
	print qq~
	<table width="600">
	<tr>
	<td>
	
	<font face="Arial" size="2">
	<b>The path you entered is incorrect.</b> You entered : "$dir"
	<p>
	Your root path is (UNIX): $ENV{DOCUMENT_ROOT}
	<p>
	Your root path is (WINDOWS): $ENV{PATH_TRANSLATED}
	<p>
	Your path should contain your root path followed by a slash followed by the 
	destination folder's name. If you are on a WINDOWS server, each slash should 
	be escaped. Eg. each seperator should look like this : \\\\
	<p>
	Sometimes, the root returned is not the full path to your web space. In this case
	you should either check with your host  or if you are using an FTP client such as 
	CuteFTP, change to the folder you are trying to upload to and look at the path you 
	have taken. You can see this just above the list of files on your server.
	You must use the same path in the \$dir variable.
	</font>
	
	</td>
	</tr>
	</table>
	~;
	
	&PrintFoot; # print the footer
	exit;
}

use CGI; # load the CGI.pm module
my $GET = new CGI; # create a new object
my @VAL = $GET->param; #get all form field names

foreach(@VAL){
	$FORM{$_} = $GET->param($_); # put all fields and values in hash 
}

my @files;
foreach(keys %FORM){
	if($_ =~ /^FILE/){
		push(@files, $_); # place the field NAME in an array
	}
}

if(!$VAL[0]){
	# no form fields
	&PrintHead; #print the header
	
	print qq~
	<table width="760">
	<tr>
	<td>
	
	<font face="Arial" size="2">
	This script must be called using a form. Your form should point to this script. Your form tag must contain the following attributes : 
	<p>
	&lt;form <font color="#FF0000">action</font>="$ScriptURL" <font color="#FF0000">method</font>="post" <font color="#FF0000">enctype</font>="multipart/form-data"> 
	<p>
	The <font color="#FF0000">method</font> must equal <font color="#FF0000">post</font> and the <font color="#FF0000">enctype</font> must equal <font color="#FF0000">multipart/form-data</font>. The <font color="#FF0000">action</font> has to point to this script (on your server). If you are reading this, copy and paste the example above. It has the correct values.
	</font>
	
	</td>
	</tr>
	</table>
	~;
	
	&PrintFoot; # print the footer
	exit;
}

# check domain
if($domain =~ /\w+/){
	if($ENV{HTTP_REFERER} !~ /$domain/i){
		&PrintHead; #print the header

		print qq~
		<table width="600">
		<tr>
		<td>

		<font face="Arial" size="2">
		Invalid referrer.
		</font>

		</td>
		</tr>
		</table>
		~;
	
		&PrintFoot; # print the footer
		exit;
	}
}

my $failed; # results string = false
my $selected; # num of files selected by user

#################################################################### 

#################################################################### 

foreach (@files){
	# upload each file, pass the form field NAME if it has a value
	if($GET->param($_)){
		
		# if the form field contains a file name &psjs_upload subroutine
		# the file's name and path are passed to the subroutine 
		$returned = &psjs_upload($_); 
		
		if($returned =~ /^Success/i){
			# if the $returned message begins with "Success" the upload was succssful
			# remove the word "Success" and any spaces and we're left with the filename   
			$returned =~ s/^Success\s+//;
			push(@success, $returned);
		} else {
			# else if the word "success" is not returned, the message is the error encountered. 
			# add the error to the $failed scalar
			$failed .= $returned;
		}
		$selected++; # increment num of files selected for uploading by user
	}
}

if(!$selected){
	# no files were selected by user, so nothing is returned to either variable
	$failed .= qq~No files were selected for uploading~;
}

# if no error message is return ed, the upload was successful

my ($fNames, $aa, $bb, @current, @currentfiles );

if($failed){

	&PrintHead;	
	
	print qq~
	<table align="center" width="600">
	<tr>
	<td><font face="Arial" size="2">
	
	One or more files <font color="#ff0000">failed</font> to upload. The reasons returned are: 
	<p>
	
	$failed
	~;
	
	if($success[0]){
		# send email if valid email was entered
		if(check_email($notify)){
			
			# enter the message you would like to receive
			my $message = qq~
			The following files were uploaded to your server :
			~; 
			
			$folder =~ s/(\/|\\)$//ig;
			foreach(@success){
				$message .= qq~
				$folder/$_	
				~;
			}
			
			if($isUNIX){
				$CONFIG{mailprogram} = $send_mail_path;
				# enter your e-mail name here if you like
				# from e-mail, from name, to e-mail, to name, subject, body
				&send_mail($notify, 'Demo Upload', $notify, 'Demo Upload', 'Upload Notification', $message);
				
			} else {
				$CONFIG{smtppath} = $smtp_path;
				&send_mail_NT($notify, 'Your Name', $notify, 'Your Name', 'Upload Notification', $message);
			}
		}
	
		print qq~
		<p>
		The following files were <font color="#ff0000">successfully</font> uploaded :
		<p>
		~;	
		foreach(@success){
			print qq~
			$_<p>~;
		}
	}
	
	print qq~
	</font></td>
	</tr>
	</table>
	~;
	
	&PrintFoot;	
	
} else {
	# upload was successful
	
	# add a link to the file
	$folder =~ s/(\/|\\)$//ig;
	
	# send email if valid email was entered
	if(check_email($notify)){
		
		# enter the message you would like to receive
		my $message = qq~
		The following files were uploaded to your server :
		~; 
		
		foreach(@success){
			$message .= qq~
			$folder/$_	
			~;
		}
		
		if($isUNIX){
			$CONFIG{mailprogram} = $send_mail_path;
			# enter your e-mail name here if you like
			# from e-mail, from name, to e-mail, to name, subject, body
			&send_mail($notify, 'Demo Upload', $notify, 'Demo Upload', 'Upload Notification', $message);
			
		} else {
			$CONFIG{smtppath} = $smtp_path;
			&send_mail_NT($notify, 'Your Name', $notify, 'Your Name', 'Upload Notification', $message);
		}
	}
	
	if($redirect){
		# redirect user
		print qq~Location: $redirect\n\n~;
	} else {
		# print success page
		
		&PrintHead;	
		
		print qq~
		<table align="center" width="500">
		<tr>
		<th><font face="Arial" size="2"><font color="#ff0000">Success</font></font></th>
		</tr>
		<tr>
		<td><font face="Arial" size="2">The following files were successfully uploaded : 
		<p>
		~;
		
		foreach(@success){
			print qq~
			$_<p>~;
		}
		
		print qq~
		</font></td>
		</tr>
		</table>
		<br>
		~;
		
		if($print_contents){
			print qq~
			<table align="center" width="500">
			<tr><td><font face="Arial" size="2"><b>Current files in folder</b></td></tr>
			<tr>
			<td valign="top">
			<font face="Arial" size="2">
			~;
			
			opendir(DIR, "$dir");
			@current = readdir(DIR);
			closedir(DIR);
			
			foreach(@current){
				unless($_ eq '.' || $_ eq '..'){
					push(@currentfiles, $_);
				}
			}
			
			@currentfiles = sort { uc($a) cmp uc($b) } @currentfiles;
			
			for($aa = 0; $aa <= int($#currentfiles / 2); $aa++){
				print qq~
				<font color="#ff0000"><b>&#149;</b> 
				<a href="$folder/$currentfiles[$aa]" target="_blank">$currentfiles[$aa]</a></font><br>
				~;
			}
			
			print qq~</font></td><td valign="top"><font face="Arial" size="2">~;
			
			for($bb = $aa; $bb < @currentfiles; $bb++){
				print qq~
				<font color="#ff0000"><b>&#149;</b> 
				<a href="$folder/$currentfiles[$bb]" target="_blank">$currentfiles[$bb]</a></font><br>
				~;
			}
			
			
			print qq~
			</font></td>
			</tr>
			</table>~;
		}
		
		print qq~
<br>
<center><font face="Arial" size="2">
<a href="http://www.perlscriptsjavascripts.com/?ul">&copy; PerlScriptsJavaScripts.com</a>
&nbsp; &nbsp; 
<a href="http://www.perlscriptsjavascripts.com/psjs_faqs/index.html?ul">F.A.Q.</a>
&nbsp; &nbsp; 
<a href="http://www.perlscriptsjavascripts.com/perl/upload_lite/users_guide.html?ul">Users Guide</a>
</font></center>
~;
		
		&PrintFoot;	
	
	}
}

#################################################################### 

#################################################################### 

sub psjs_upload {

	my ( $type_ok, $file_contents, $buffer, $destination ); # declare some vars

	my $file = $GET->param($_[0]); # get the FILE name. $_[0] is the arg passed
	
	$destination = $dir;
	
	my $limit = $max;
	$limit *= 1024; # convert limit from bytes to kilobytes
	
	# create another instance of the $file var. This will allow the script to play 
	# with the new instance, without effecting the first instance. This was a major 
	# flaw I found in the psupload script. The author was replacing spaces in the path
	# with underscores, so the script could not find a file to upload. He blammed the 
	# error on browser problems.
	my $fileName    = $file; 
	
	# get the extension
	my @file_type   = split(/\./, $fileName);
	# we can assume everything after the last . found is the extension
	my $file_type   = $file_type[$#file_type];
	
	# get the file name, this removes everything up to and including the 
	# last slash found ( be it a forward or back slash )
	$fileName =~ s/^.*(\\|\/)//;
	
	# remove all spaces from new instance of filename var 
	$fileName =~ s/\s+//ig;
	
	# check for any any non alpha numeric characters in filename (allow dots and dahses)
	$fileName =~ s/\./PsJsDoT/g;
	$fileName =~ s/\-/PsJsDaSh/g;
	if($fileName =~ /\W/){
		$fileName =~ s/\W/n/ig; # replace any bad chars with the letter "n"
	}
	$fileName =~ s/PsJsDoT/\./g;
	$fileName =~ s/PsJsDaSh/\-/g;
	
	# if $file_type matchs one of the types specified, make the $type_ok var true
	for($b = 0; $b < @types; $b++){
		if($file_type =~ /^$types[$b]$/i){
			$type_ok++;
		}
		if($types[$b] eq "ALL"){
			$type_ok++; # if ALL keyword is found, increment $type_ok var.
		}
	}
	
	# if ok, check if overwrite is allowed
	if($type_ok){
		if(!$overwrite){ # if $overwite = 0 or flase, rename file using the checkex sub
			$fileName = check_existence($destination,$fileName);
		}
		# create a new file on the server using the formatted ( new instance ) filename
		if(open(NEW, ">$destination$S{S}$fileName")){
			if($isWIN){binmode NEW;} # if it's a WIN server, switch to binary mode
			# start reading users HD 1 kb at a time.
			while (read($file, $buffer, 1024)){ 
				# print each kb to the new file on the server 
				print NEW $buffer; 
			}
			# close the new file on the server and we're done
			close NEW;
		} else {
			# return the server's error message if the new file could not be created
			return qq~Error: Could not open new file on server. $!~;
		}

		# check limit hasn't just been overshot
		if(-s "$destination$S{S}$fileName" > $limit){ # -s is the file size
			unlink("$destination$S{S}$fileName"); # delete it if it's over the specified limit
			return qq~File exceeded limitations : $fileName~;
		}
	} else {
		return qq~Bad file type : $file_type~; 
	}
			
	# check if file has actually been uploaded, by checking the file has a size
	if(-s "$destination$S{S}$fileName"){
		return qq~Success $fileName~; #success 
	} else {
		# delete the file as it has no content
		unlink("$destination$S{S}$fileName");
		# user probably entered an incorrect path to file
		return qq~Upload failed : No data in $fileName. No size on server's copy of file. 
		Check the path entered.~; 
	}
}

#################################################################### 

#################################################################### 

sub check_existence {
	# $dir,$filename,$newnum are the args passed to this sub
	my ($dir,$filename,$newnum) = @_;
	
	my (@file_type, $file_type, $exists, $bareName); 
	# declare some vars we will use later on in this sub always use paranthesis 
	# when declaring more than one var! Some novice programmers will tell you 
	# this is not necessary. Tell them to learn how to program.
	
	if(!$newnum){$newnum = "0";} # new num is empty in first call, so set it to 0
	
	# read dir and put all files in an array (list)
	opendir(DIR, "$dir");
	@existing_files =  readdir(DIR);
	closedir(DIR);
	
	# if the filename passed exists, set $exists to true or 1
	foreach(@existing_files){
		if($_ eq $filename){
			$exists = 1;
		}
	}
	
	# if it exists, we need to rename the file being uploaded and then recheck it to 
	# make sure the new name does not exist
	if($exists){
		$newnum++; # increment new number (add 1)

		# get the extension
		@file_type   = split(/\./, $filename); # split the dots and add inbetweens to a list
		# put the first element in the $barename var
		$bareName    = $file_type[0]; 
		# we can assume everything after the last . found is the extension
		$file_type   = $file_type[$#file_type]; 
		# $#file_type is the last element (note the pound or hash is used)
		
		# remove all numbers from the end of the $bareName
		$bareName =~ s/\d+$//ig;
		
		# concatenate a new name using the barename + newnum + extension 
		$filename = $bareName . $newnum . '.' . $file_type;
		
		# reset $exists to 0 because the new file name is now being checked
		$exists = 0;
		
		# recall this subroutine
		&check_existence($dir,$filename,$newnum);
	} else {
		# the $filename, whether the first or one hundreth call, now does not exist
		# so return the name to be used
		return ($filename);
	}
}

#################################################################### 

#################################################################### 

sub send_mail {
	my ($from_email, $from_name, $to_email, $to_name, $subject, $message ) = @_;
	
	if(open(MAIL, "|$CONFIG{mailprogram} -t")) {
		print MAIL "From: $from_email ($from_name)\n";
		print MAIL "To: $to_email ($to_name)\n";
		print MAIL "Subject: $subject\n";
		print MAIL "$message\n\n$ENV{REMOTE_ADDR}";
		close MAIL;
		return(1);
	} else {
		return;
	}
}

#################################################################### 

#################################################################### 

sub send_mail_NT {
	
	my ($from_email, $from_name, $to_email, $to_name, $subject, $message ) = @_;
	
	my ($SMTP_SERVER, $WEB_SERVER, $status, $err_message);
	use Socket; 
    $SMTP_SERVER = "$CONFIG{smtppath}";                                 
	
	# correct format for "\n"
    local($CRLF) = "\015\012";
    local($SMTP_SERVER_PORT) = 25;
    local($AF_INET) = ($] > 5 ? AF_INET : 2);
    local($SOCK_STREAM) = ($] > 5 ? SOCK_STREAM : 1);
    local(@bad_addresses) = ();
    $, = ', ';
    $" = ', ';

    $WEB_SERVER = "$CONFIG{smtppath}\n";
    chop ($WEB_SERVER);

    local($local_address) = (gethostbyname($WEB_SERVER))[4];
    local($local_socket_address) = pack('S n a4 x8', $AF_INET, 0, $local_address);

    local($server_address) = (gethostbyname($SMTP_SERVER))[4];
    local($server_socket_address) = pack('S n a4 x8', $AF_INET, $SMTP_SERVER_PORT, $server_address);

    # Translate protocol name to corresponding number
    local($protocol) = (getprotobyname('tcp'))[2];

    # Make the socket filehandle
    if (!socket(SMTP, $AF_INET, $SOCK_STREAM, $protocol)) {
        return;
    }

	# Give the socket an address
	bind(SMTP, $local_socket_address);
	
	# Connect to the server
	if (!(connect(SMTP, $server_socket_address))) {
		return;
	}
	
	# Set the socket to be line buffered
	local($old_selected) = select(SMTP);
	$| = 1;
	select($old_selected);
	
	# Set regex to handle multiple line strings
	$* = 1;

    # Read first response from server (wait for .75 seconds first)
    select(undef, undef, undef, .75);
    sysread(SMTP, $_, 1024);
	#print "<P>1:$_";

    print SMTP "HELO $WEB_SERVER$CRLF";
    sysread(SMTP, $_, 1024);
	#print "<P>2:$_";

	while (/(^|(\r?\n))[^0-9]*((\d\d\d).*)$/g) { $status = $4; $err_message = $3}
	if ($status != 250) {
		return;
	}

	print SMTP "MAIL FROM:<$from_email>$CRLF";

	sysread(SMTP, $_, 1024);
	#print "<P>3:$_";
	if (!/[^0-9]*250/) {
		return;
	}

    # Tell the server where we're sending to
	print SMTP "RCPT TO:<$to_email>$CRLF";
	sysread(SMTP, $_, 1024);
	#print "<P>4:$_";
	/[^0-9]*(\d\d\d)/;

	# Give the server the message header
	print SMTP "DATA$CRLF";
	sysread(SMTP, $_, 1024);
	#print "<P>5:$_";
	if (!/[^0-9]*354/) {
		return;
	}

	print SMTP qq~From: $from_email ($from_name)$CRLF~;
	print SMTP qq~To: $to_email ($to_name)$CRLF~;
	if($cc){
		print SMTP "CC: $cc ($cc_name)\n";
	}
	print SMTP qq~Subject: $subject$CRLF$CRLF~;
	print SMTP qq~$message~;

	print SMTP "$CRLF.$CRLF";
	sysread(SMTP, $_, 1024);
	#print "<P>6:$_";
	if (!/[^0-9]*250/) {
		return;
	} else {
		return(1);
	}

	if (!shutdown(SMTP, 2)) {
		return;
    } 
}

#################################################################### 

#################################################################### 

sub PrintHead {
	print qq~Content-type: text/html\n\n~;
	print qq~
	<html>
	<title>PerlScriptsJavascript.com Free upload utility</title>
	<body bgcolor="#ffffff">
	~;
}

#################################################################### 

#################################################################### 

sub PrintFoot {
	print qq~
	</body>
	</html>
	~;
}

#################################################################### 

#################################################################### 

sub check_email {
	my($fe_email) = $_[0];
	if($fe_email) {
		if(($fe_email =~ /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)|(\.$)/) ||
		($fe_email !~ /^.+@\[?(\w|[-.])+\.[a-zA-Z]{2,3}|[0-9]{1,3}\]?$/)) {
			return;
		} else { return(1) }
	} else {
		return;
	}
}
