test -d apache/logs || mkdir apache/logs
httpd2 -f `pwd`/apache/conf/httpd.conf -d `pwd`


