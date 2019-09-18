install:
	cd appm/cloud;composer install;
	cd appm/job;composer install;
	cd appm/logic;composer install;
update:
	cd appm/cloud;composer update;
	cd appm/job;composer update;
	cd appm/logic;composer update;
clean:
	cd appm/cloud;rm -rf composer.lock vendor;
	cd appm/job;rm -rf composer.lock vendor;
	cd appm/logic;rm -rf composer.lock vendor;
start:
	cd appm/cloud;php bin/app --start --d --log=true --debug
	cd appm/job;php bin/app --start --d --log=true --debug
	cd appm/logic;php bin/app --start --d --log=true --debug
restart:
	cd appm/cloud;php bin/app --restart --d --log=true --debug
	cd appm/job;php bin/app --restart --d --log=true --debug
	cd appm/logic;php bin/app --restart --d --log=true --debug
stop:
	cd appm/cloud;php bin/app --stop
	cd appm/job;php bin/app --stop
	cd appm/logic;php bin/app --stop

pushm:
	docker build -f appm/cloud/Dockerfile -t brewlin/cloud-m .;docker push brewlin/cloud-m;
	docker build -f appm/job/Dockerfile -t brewlin/job-m .;docker push brewlin/job-m;
	docker build -f appm/logic/Dockerfile -t brewlin/logic-m .;docker push brewlin/logic-m;

