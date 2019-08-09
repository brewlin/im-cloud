install:
	cd app/cloud;composer install;
	cd app/job;composer install;
	cd app/logic;composer install;
update:
	cd app/cloud;composer update;
	cd app/job;composer update;
	cd app/logic;composer update;
clean:
	cd app/cloud;rm -rf composer.lock vendor;
	cd app/job;rm -rf composer.lock vendor;
	cd app/logic;rm -rf composer.lock vendor;

