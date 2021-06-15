#!/usr/local/bin/perl -w

# PHP File Uploader with progress bar Version 1.35
# Copyright (C) Raditha Dissanyake 2003
# http://www.raditha.com

# Licence:
# The contents of this file are subject to the Mozilla Public
# License Version 1.1 (the "License"); you may not use this file
# except in compliance with the License. You may obtain a copy of
# the License at http://www.mozilla.org/MPL/
# 
# Software distributed under the License is distributed on an "AS
# IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
# implied. See the License for the specific language governing
# rights and limitations under the License.
# 
# The Initial Developer of the Original Code is Raditha Dissanayake.
# Portions created by Raditha are Copyright (C) 2003
# Raditha Dissanayake. All Rights Reserved.
# 

# CHANGES:
# As of version 1.00 cookies were abolished!
# as of version 1.02 stdin is no longer set to non blocking.
# 1.30 Reintroduced them as an optional add in. 


use CGI;
use Fcntl qw(:DEFAULT :flock);
use CGI::Cookie;

#use Carp; 	
#uncommment the above line if you want to debug. 

@qstring=split(/&/,$ENV{'QUERY_STRING'});
@p1 = split(/=/,$qstring[0]);
$sessionid = $p1[1];
$sessionid =~ s/[^a-zA-Z0-9]//g;  # sanitized as suggested by Terrence Johnson.

$content_type = $ENV{'CONTENT_TYPE'};
$len = $ENV{'CONTENT_LENGTH'};
$bRead=0;
$|=1;

sub bye_bye {
	$mes = shift;
	print "Content-type: text/html\n\n";
	print "<br>$mes<br>\n";

	exit;
}

require("./header.cgi");

# see if we are within the allowed limit.

if($len > $max_upload)
{
	close (STDIN);
	bye_bye("The maximum upload size has been exceeded");
}

#
# see if the directory exists, if it does, go ahead, else try to make it
# if you can't make the directory it's time for bye byes.
#
unless (-d "$user_dir"){
	mkdir ("$user_dir", 0777); # unless the dir exists, make it ( and chmod it on UNIX )
	chmod(0777, "$user_dir");
}


unless (-d "$user_dir"){
	# if there still is no dir, the path entered by the user is wrong and the upload will fail

	print "sorry could not save file to $user_dir";
	exit;

}

#carp "user dir= $user_dir";

#
# The thing to watch out for is file locking. Only
# one thread may open a file for writing at any given time.
# 

sysopen(FH, "$user_dir/flength", O_RDWR | O_CREAT)
	or die "can't open numfile: $!";
# autoflush FH
$ofh = select(FH); $| = 1; select ($ofh);
flock(FH, LOCK_EX)
	or die "can't write-lock numfile: $!";
seek(FH, 0, 0)
	or die "can't rewind numfile : $!";
print FH $len;	
close(FH);	
	
sleep(1);

#open(TMP, "$user_dir/postdata") or &bye_bye ("can't open temp file");

open(TMP,">","$user_dir/postdata") or &bye_bye ("can't open temp file");
 

# Christian fowler suggested that the next two lines of code are redundant. 
# i have commented them out but kept them in place temporarily. If someone
# doesn't write in with any other comments they get killed permanent.
 
#  fcntl(STDIN, F_SETFL, O_NONBLOCK)
#       or die "can't set non blocking: $!";



#
# read and store the raw post data on a temporary file so that we can
# pass it though to a CGI instance later on.
#

print "Content-type: text/html\n\n";
#carp "the sessionid = $sessionid";
#carp "the qstring = $qstring[0]";


# following cookie handling code was contributed by Yuri Weseman

%cookies = fetch CGI::Cookie;
$cookie_data = "";
foreach (keys %cookies) {
        $cookie_data .= "-H 'Cookie: ".$cookies{$_}."' ";
}

open (POST,"|$post_prog -c\"$content_type\" $cookie_data $php_uploader ");



my $i=0;

$ofh = select(TMP); $| = 1; select ($ofh);
			
while (read (STDIN ,$LINE, 4096) && $bRead < $len )
{
	$bRead += length $LINE;
	$size = -s "$user_dir/postdata";
	
	#carp "$bRead, $size";	
	
	# demo version on raditha.com: 
	# slow things down so that the user can actually see the progress bar.
	# the next line of code included in the demo version that you
	# see at http://www.raditha.com - helps keep my server alive :-)
	#
	
	select(undef, undef, undef,0.35);	# sleep for 0.2 of a second.
	
	# Many thanx to Patrick Knoell who came up with the optimized value for
	# the duration of the sleep

	
	$i++;
	print TMP $LINE;
	print POST $LINE;
}


#	or die "can't close numfile: $!";
#clean up operations.



close (TMP);
close (POST);


#
# We don't want to decode the post data ourselves. That's like
# reinventing the wheel. We also don't want to handle the data
# using the CGI module since the whole purpose of this excercise
# is to support file upload with progress bar for PHP scripts.
#



