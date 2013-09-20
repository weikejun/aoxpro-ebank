#!/bin/bash

`ps -ef|grep "memcached "|grep -v grep > /dev/null`

if [ $? != '0' ]; then
	memcached -u nobody -p 12345 -l 127.0.0.1 -m 128 -d
	echo "start memcached"
fi

