#! /bin/sh
#Script install.sh for: PHP Turbine 7 for Apache
#(c) Blue Pacific Software 2004

clear
echo -e ""
echo -e "\033[47;34;7m-----------------------\033[47;34;7m   PHP Turbine 7   -----------------------\033[0m"
echo -e "\033[47;34;7m Author: Blue Pacific Software                                   \033[0m"
echo -e "\033[47;34;7m Version: 7                                                      \033[0m"
echo -e "\033[47;34;7m Web: http://www.blue-pacific.com                                \033[0m"
echo -e "\033[47;34;7m Email: support@blue-pacific.com                                 \033[0m"
echo -e ""
echo -e "\033[47;31;7m Instalation Script                                              \033[0m"
echo -e ""

echo -e "This install script copies the runtime elements of PHP Turbine 7 "
echo -e "into the PHP extensions directory and configures PHP with the "
echo -e "following lines lines to be added to the php.ini file:"
echo -e "\033[47;34;7m;===================================================\033[0m"
echo -e "\033[47;34;7m; PHP Turbine 7 settings:                           \033[0m"
echo -e "\033[47;34;7mextension=turbine7/php_turbine7*.so                 \033[0m"
echo -e "\033[47;34;7m; PHP Turbine 7 end of settings                     \033[0m"
echo -e "\033[47;34;7m;===================================================\033[0m"
echo -e "Where * is the version number of the PHP engine you are using."
echo -e ""

echo -e "Before any change is made to the php.ini file, a copy of the "
echo -e "original file is written with the name php.ini-before.turbine."
echo -e "The script also sets permissions so that the Turbine module "
echo -e "runtime user is able to process Turbine requests.      "
echo -e "\nWould you like to continue? (y/n)"
read addH

if [ "$addH" != "y" -a "$addH" != "yes" ]; then
  echo -e "Instalation Aborted."
  exit
fi


echo -e "\nWhich PHP engine version (#.#.#) do you have installed?"
read version

echo -e "\nDoes the PHP engine have thread safety enabled? (y/n)\nFor example PHP compiled as an Apache 2.0 module usually has thread safety enabled, but PHP for Apache 1.3.* does not. The PHP engine running as a CGI-Bin does not have thread safety enabled."
read zts

if [ "$zts" != "n" -a "$zts" != "no" ]; then
  version=$version'zts'
fi

pattern='/^('$version') *= *(.*)/ {print $3}'

modulename=`awk "$pattern" phptable`

if [ -z $modulename ] ; then
        echo -e "Could not find PHP engine version $version\nPlease make sure that version is listed on the included phptable file (on the left column).\nInstalation Aborted."
	exit
fi;

echo -e "Will be using the \"turbine7/$modulename.so\" module..."


echo -e "\nWhat is the path to your PHP installation?"
read prefix

majorversion=`echo -n "$version" | sed -e 's/\..*//g'`

if [ "$majorversion" != "4" ]; then
  extension_dir="$prefix"
else
  extension_dir="$prefix/extensions"
fi

if [ ! -d "$extension_dir" ] ; then
	echo -e "Could not find the \"$extension_dir\" directory inside your PHP installation directory.\nPlease verify that $prefix is indeed your working PHP installation directory and try again."
        echo -e "Instalation Aborted."
	exit
fi

ext_prefix="$extension_dir/turbine7"

echo -e "\nCreating runtime directory structure on $ext_prefix..."

if [ -d $ext_prefix ] ; then
	if [ -w $ext_prefix ] ; then
	    echo -e "Main directory already exists!"
	else 
	    echo -e "Main directory already exists and doesn't have write permissions. Please check it out and try again."
            echo -e "Instalation Aborted."
	    exit
	fi
else
	mkdir -p $ext_prefix
	if [ $? != '0' ] ; then
	    echo -e "Could not create main directory. Please check it out and try again."
            echo -e "Instalation Aborted."
	    exit
	fi
fi

mkdir -p $ext_prefix/cache
if [ $? != '0' ] ; then
	echo -e "Could not create $ext_prefix/cache directory. Please check it out and try again."
        echo -e "Instalation Aborted."
	exit
fi

cp -rf turbine7/* $ext_prefix
if [ $? != '0' ] ; then
	echo -e "Could not copy the Turbine runtime into the PHP extensions directory. Please check it out and try again."
        echo -e "Instalation Aborted."
	exit
fi
    

echo -e "Setting permissions..."

chmod o+rx $ext_prefix/*
if [ $? != '0' ] ; then
    echo -e "Could not change permissions to $ext_prefix/*. Please check it out and try again."
    echo -e "Instalation Aborted."
    exit
fi

chmod o+rw $ext_prefix/turbine.log
if [ $? != '0' ] ; then
    echo -e "Could not change permissions to $ext_prefix/turbine.log. Please check it out and try again."
    echo -e "Instalation Aborted."
    exit
fi

chmod o+rwx $ext_prefix/cache
if [ $? != '0' ] ; then
    echo -e "Could not change permissions to $ext_prefix/cache/. Please check it out and try again."
    echo -e "Instalation Aborted."
    exit
fi

chmod -R o+rx $ext_prefix/chart
if [ $? != '0' ] ; then
    echo -e "Could not change permissions to $ext_prefix/chart/. Please check it out and try again."
    echo -e "Instalation Aborted."
    exit
fi

chmod -R o+rx $ext_prefix/font
if [ $? != '0' ] ; then
    echo -e "Could not change permissions to $ext_prefix/font/. Please check it out and try again."
    echo -e "Instalation Aborted."
    exit
fi


echo -e "Looking for php.ini config...\n"

#look first on the php root
conffile="$prefix/php.ini"

if [ ! -f "$conffile" ] ; then
	conffile="$prefix/../php.ini"
fi	
    
if [ -f "$conffile" -a "$conffile" != '' ] ; then
	echo -e "Is $conffile your php.ini config file? (y/n)"
	read fRPM
	if [ "$fRPM" != "y" -a "$fRPM" != "yes" ] ; then
	    echo -e "Please enter the full path to the php.ini config file"
	    read conffile
	fi
else
	echo -e "Could not locate php.ini file.\nCould you please enter the full path to the php.ini config file?"
    	read conffile
fi


if [ -z `echo $conffile | grep php\.ini` ] ; then
	echo -e "\nCould not find the php.ini configuration file."
	echo -e "Please locate the path to the php.ini file and try again."
	echo -e "Instalation Aborted."
	exit
fi


if [ -f "$conffile" ]; then
# look for the entry
	if ( grep -q -i '^[ 	]*extension[ 	]*=[ 	]*turbine7/php_turbine7' $conffile ); then
	
	    echo -e "\nThe $conffile file already has an entry \"extension=turbine7/php_turbine7*.so\"."
	    echo -e "Please manually remove or comment the above entry by editing the $conffile config file and then try again."
            echo -e "Instalation Aborted."
	    exit
	fi

# look for extension_dir
	if ( grep -q -i '^[ 	]*extension_dir[ 	]*=' $conffile ); then
	    has_previous=1
	else
	    echo -e "\nLooks like the $conffile file doesn't have the \"extension_dir=...\" directive."
	    echo -e "Please manually edit the $conffile and add the extension_dir directive, or PHP will not be able to find extensions like PHP Turbine."
	    echo -e "Instalation Aborted."
	    exit
	fi

	
        echo -e "\nMaking a copy of the original php.ini file..."
        cp -f $conffile ${conffile}-before.turbine

        echo -e "Adding extension=turbine7/$modulename.so directive..."
	echo -e "\n\n; PHP Turbine 7 settings:" >> $conffile
	echo -e "extension=turbine7/$modulename.so" >> $conffile
	echo -e "; PHP Turbine 7 end of settings" >> $conffile
	
	if [ $? != "0" ]; then
	        echo -e "\tERROR: Could not update $conffile";
                echo -e "Instalation Aborted."
                exit
	fi

else
	echo -e "Could not find the php.ini configuration file."
	echo -e "Please locate the path to the php.ini file and try again."
        echo -e "Instalation Aborted."
	exit
fi
    
echo -e "\n\033[47;34;7m---------------------------------------------------------------------\033[0m"
echo -e "\033[47;34;7m PHP Turbine 7 is now installed on your PHP engine.                  \033[0m"
echo -e "\033[47;34;7m Please restart the Web Server and proceed to install the            \033[0m"
echo -e "\033[47;34;7m Turbine Samples, as described on the docs/install.htm page.         \033[0m"
echo -e "\033[47;34;7m Thank you for your interest on Turbine 7.                           \033[0m"
echo -e "\033[47;34;7m Enjoy!                                                              \033[0m"
echo -e "\033[47;34;7m---------------------------------------------------------------------\033[0m"
echo -e ""

exit;
#END