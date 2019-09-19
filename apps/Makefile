install:
	cd cloud;composer install;
	cd job;composer install;
	cd logic;composer install;
update:
	cd cloud;composer update;
	cd job;composer update;
	cd logic;composer update;
clean:
	cd cloud;rm -rf composer.lock vendor;
	cd job;rm -rf composer.lock vendor;
	cd logic;rm -rf composer.lock vendor;
start:
	cd cloud;php bin/app --start --d --log=true --debug
	cd job;php bin/app --start --d --log=true --debug
	cd logic;php bin/app --start --d --log=true --debug
restart:
	cd cloud;php bin/app --restart --d --log=true --debug
	cd job;php bin/app --restart --d --log=true --debug
	cd appm/logic;php bin/app --restart --d --log=true --debug
stop:
	cd cloud;php bin/app --stop
	cd job;php bin/app --stop
	cd logic;php bin/app --stop

