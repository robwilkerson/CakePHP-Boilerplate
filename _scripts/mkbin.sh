#!/bin/bash

if [ $# -eq 0 ]; then
     echo "Usage: ./mkbin.sh <instance root>"
     exit 1
fi

if [ ! -d $1 ]; then
     echo "The specified directory ($1) does not exist"
     exit 1
fi

basedir=$1
letters="a b c d e f g h i j k l m n o p q r s t u v q r s t u v w x y z"

cd $basedir

mkdir -p bin/_tmp
cd bin

for i in $letters
do
     for j in $letters
     do
          mkdir -p $i/$j
     done
done

exit 0
