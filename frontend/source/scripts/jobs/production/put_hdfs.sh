#!/bin/bash -x

## Please set your environment
hadoop=/usr/bin/hadoop

## Get arguments
local_file=$1
hdfs_file=$2

## Store to HDFS
$hadoop fs -moveFromLocal $local_file $hdfs_file
$hadoop fs -test -e $hdfs_file
if [ $? -eq 0 ]; then
	echo "SUCCESS"
	exit 0
fi
echo "FAILURE"
exit 0

