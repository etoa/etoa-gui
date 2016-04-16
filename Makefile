.DEFAULT_GOAL := help

install: ## Setup EtoA via vagrant
	./composer.phar install
	vagrant up --provision

.PHONY: help eventhandler

update: ## Update EtoA via vagrant
	./composer.phar install
	vagrant up --provision
	vagrant ssh -c "/var/www/etoa/bin/db.php migrate"

ci: ## Run continuous integration tasks (tests and code style fixes)
	./vendor/bin/phpunit tests
	./vendor/bin/php-cs-fixer fix src
	./vendor/bin/php-cs-fixer fix tests

deploy-update: ## Everything which needs to be run during deploy
	./composer.phar install -o
	bin/db.php migrate
	eventhandler/bin/build.sh
	@echo "Restart the event handler in the web-based admin tool."

eventhandler: ## Build the cpp eventhandler
	cd /var/www/etoa/eventhandler && rm -f CMakeCache.txt && cmake . && make

	sudo mkdir -p /etc/etoad
	sudo mkdir -p /var/log/etoad
	sudo mkdir -p /var/run/etoad
	sudo chmod -R 777 /var/log/etoad
	sudo chmod -R 777 /var/run/etoad
	sudo cp /var/www/etoa/vagrant/roundx.conf /etc/etoad/roundx.conf
	sudo su vagrant -c"./target/etoad roundx -k -d"

help: ## Helping devs since 2016
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
