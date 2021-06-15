#! /bin/sh

cd "$(dirname "$0")"

dest="masterslider.js"

rm $dest 2>/dev/null
for f in $(grep -vE "^$|^#" dev/files_shell.list); do
	echo "$f"
	printf "//$f\n" >> $dest;	
	cat "$f" >> $dest;	
	printf "\n" >> $dest;
done 
