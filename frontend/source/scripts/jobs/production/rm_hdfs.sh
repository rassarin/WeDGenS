#!/bin/bash -x

## Please set your environment
hadoop=/usr/bin/hadoop

## Get arguments
hdfs_file=$1

## Remove file in HDFS
$hadoop fs -test -e $hdfs_file
if [ $? -eq 0 ]; then
	$hadoop fs -rm $hdfs_file
	exit 0
fi
echo "FAILURE"
exit 0

