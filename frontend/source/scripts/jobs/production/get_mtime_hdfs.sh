#!/bin/bash -x

## Please set your environment
hadoop=/usr/bin/hadoop

## Get arguments
hdfs_file=$1

## Get mtime in HDFS
$hadoop fs -test -e $hdfs_file
if [ $? -eq 0 ]; then
	$hadoop fs -stat "%y" $hdfs_file
	exit 0
fi
echo "FAILURE"
exit 0

