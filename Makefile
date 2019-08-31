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
start:
	cd app/cloud;php bin/app --start --d --log=true --debug
	cd app/job;php bin/app --start --d --log=true --debug
	cd app/logic;php bin/app --start --d --log=true --debug
restart:
	cd app/cloud;php bin/app --restart --d --log=true --debug
	cd app/job;php bin/app --restart --d --log=true --debug
	cd app/logic;php bin/app --restart --d --log=true --debug
stop:
	cd app/cloud;php bin/app --stop
	cd app/job;php bin/app --stop
	cd app/logic;php bin/app --stop

push:
	docker build -f app/cloud/Dockerfile -t brewlin/cloud .;docker push brewlin/cloud;
	docker build -f app/job/Dockerfile -t brewlin/job .;docker push brewlin/job;
	docker build -f app/logic/Dockerfile -t brewlin/logic .;docker push brewlin/logic;

