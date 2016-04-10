.DEFAULT_GOAL := help

install: ## Setup etoa via vagrant
	./composer.phar install
	vagrant up --provision

update: ## Update etoa
	./composer.phar install
	vagrant up --provision

ci: ## Run continuous integration tasks (tests and code style fixes)
	./vendor/bin/phpunit tests
	./vendor/bin/php-cs-fixer fix src
	./vendor/bin/php-cs-fixer fix tests

deploy-update: ## Everything which needs to be run during deploy
	./composer.phar install -o

.PHONY: help

help: ## Helping devs since 2016
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
