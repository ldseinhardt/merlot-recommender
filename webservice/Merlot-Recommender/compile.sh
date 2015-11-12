#!/bin/bash

# java compiler shell script and generate jar file

PROGRAM_NAME=merlot.recommender.MerlotRecommender
JAR_NAME=Merlot-Recommender
PATH_LIB=lib
PATH_BUILD=build

for i in `ls $PATH_LIB/**/*.jar`; do
  LIBS=${LIBS}:${i}
done

mkdir $PATH_BUILD

javac -d $PATH_BUILD -cp "./ .:${LIBS}" "src/${PROGRAM_NAME//"."/"/"}.java"

cd $PATH_BUILD

echo "Class-Path: ${LIBS//":"/" "}" >> MANIFEST.MF
echo "Main-Class: ${PROGRAM_NAME}" >> MANIFEST.MF
echo "" >> MANIFEST.MF
echo "" >> MANIFEST.MF

jar -cvfm ../$JAR_NAME.jar MANIFEST.MF .

cd ../
rm -rf $PATH_BUILD

exit
