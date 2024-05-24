#!/bin/sh
#
# Sync local files to the final directories
#
# Parameters:
#   $1: web site dir
#   $2: Share files dir
#   $3: Language translation dir

if [ $# -ne 3 ]; then
    echo 'Usage: sync.sh <site dir> <share dir> <Translateion language>'
    exit -1
fi

# Setting up site code
mkdir -p $1
for i in display.php finish.php index.php logout.php punch.php start.php
do
    echo Adding site/$i to $1/$i.
    if [ -r $3/$i.sed ]; then
	sed -f $3/$i.sed site/$i >$1/$i
    else
	cp site/$i $1/$i
    fi
done
cp -r site/assets site/css site/js $1

# Setting up share
mkdir -p $2
for i in list.php  login.php  session.php
do
    if [ -r $3/$i.sed ]; then
        sed -f $3/$i.sed site/$i >$2/$i
    else
        cp site/$i $2/$i
    fi
done
