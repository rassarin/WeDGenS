#!/bin/bash -x

## Please set your environment
hadoop=/usr/bin/hadoop

## Get arguments
hdfs_file=$1

## Check file exists in HDFS
$hadoop fs -test -e $hdfs_file
if [ $? -eq 0 ]; then
	echo "EXISTS"
	exit 0
fi
echo "FAILURE"
exit 0

