#!/bin/bash -l

# curlerr () { curl -D/dev/stderr "$@"; } 

#export DOT_ENV=/T/trsys/timer/git/.env.test 

index=index.php

( sleep 1; touch $index) &


#shift #// run-it.sh
#cd `dirname $0`; cd ..

while true; do
	(find . -name \*php; 
	 find . -name '.env*'; 
	 find . -name 'run-*sh'; 
	 find . -name \*feature; 
	 find . -name \*tpl; 
	 find . -name \*.yaml; 
	 find . -name \*.sql ;
	 echo ./composer.json;
	) |
	inotifywait -e  modify -e move  -e attrib -e delete --fromfile - 2>/dev/null

	php -r 'echo str_repeat("\n", 3);'

	## avoid invisible black
	printf '\033]4;0;#686868\007'


    if [[ $1 = phpunit ]]; then "$@"
    elif [[ $1 =~ .*\\.php ]]; then php $1
    elif [[ $1 =~ .*\\.sh ]]; then bash -l $1;
    #elif [[ $1 =~ phpunit ]]; then shift; phpunit "$@";
    else
    	behat --stop-on-failure --colors -n "$@"

		#php \
		#	-d xdebug.default_enable=1 \
		#	-d xdebug.remote_autostart=1 \
		#	vendor/bin/phpunit "$@"


    fi
	sleep 0.1
done
