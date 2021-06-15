#!/usr/bin/perl
##############################################################################
# Define Variables							     #

$basedir = '/home/virtual/thegermanfriday.com/var/www/html/';
$baseurl = 'http://www.thegermanfriday.com/';
@files = ('*.php');
$title = "The German Friday";
$title_url = 'http://www.thegermanfriday.com/';
$search_url = 'http://www.thegermanfriday.com/search.php';

# Done									     #
##############################################################################

# Parse Form Search Information
&parse_form;

# Get Files To Search Through
&get_files;

# Search the files
&search;

# Print Results of Search
&return_html;


sub parse_form {

   # Get the input
   read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});

   # Split the name-value pairs
   @pairs = split(/&/, $buffer);

   foreach $pair (@pairs) {
      ($name, $value) = split(/=/, $pair);

      $value =~ tr/+/ /;
      $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

      $FORM{$name} = $value;
   }
}

sub get_files {

   chdir($basedir);
   foreach $file (@files) {
      $ls = `ls $file`;
      @ls = split(/\s+/,$ls);
      foreach $temp_file (@ls) {
         if (-d $file) {
            $filename = "$file$temp_file";
            if (-T $filename) {
               push(@FILES,$filename);
            }
         }
         elsif (-T $temp_file) {
            push(@FILES,$temp_file);
         }
      }
   }
}

sub search {

   @terms = split(/\s+/, $FORM{'terms'});

   foreach $FILE (@FILES) {

      open(FILE,"$FILE");
      @LINES = <FILE>;
      close(FILE);

      $string = join(' ',@LINES);
      $string =~ s/\n//g;
      if ($FORM{'boolean'} eq 'AND') {
         foreach $term (@terms) {
            if ($FORM{'case'} eq 'Insensitive') {
               if (!($string =~ /$term/i)) {
                  $include{$FILE} = 'no';
  		  last;
               }
               else {
                  $include{$FILE} = 'yes';
               }
            }
            elsif ($FORM{'case'} eq 'Sensitive') {
               if (!($string =~ /$term/)) {
                  $include{$FILE} = 'no';
                  last;
               }
               else {
                  $include{$FILE} = 'yes';
               }
            }
         }
      }
      elsif ($FORM{'boolean'} eq 'OR') {
         foreach $term (@terms) {
            if ($FORM{'case'} eq 'Insensitive') {
               if ($string =~ /$term/i) {
                  $include{$FILE} = 'yes';
                  last;
               }
               else {
                  $include{$FILE} = 'no';
               }
            }
            elsif ($FORM{'case'} eq 'Sensitive') {
               if ($string =~ /$term/) {
		  $include{$FILE} = 'yes';
                  last;
               }
               else {
                  $include{$FILE} = 'no';
               }
            }
         }
      }
      if ($string =~ /<title>(.*)<\/title>/i) {
         $titles{$FILE} = "$1";
      }
      else {
         $titles{$FILE} = "$FILE";
      }
   }
}
      
sub return_html {
   print "Content-type: text/html\n\n";
   print "<li><b>You Searched For:</b> ";
   print "<ul>\n";
   foreach $key (keys %include) {
      if ($include{$key} eq 'yes') {
         print "<li><a href=\"$baseurl$key\">$titles{$key}</a>\n";
      }
   }
   print "<ul>\n";
   $i = 0;
   foreach $term (@terms) {
      print "$term";
      $i++;
      if (!($i == @terms)) {
         print ", ";
      }
   }
   print "\n";
   print "<ul>\n<li><a href=\"$search_url\"><< Back</a>\n";
   print "</body>\n</html>\n";
}
   
