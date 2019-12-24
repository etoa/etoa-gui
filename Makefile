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
	./vendor/bin/php-cs-fixer fix src --rules=@PSR2,binary_operator_spaces,blank_line_before_return,function_typehint_space,no_empty_comment,no_empty_phpdoc,no_empty_statement,no_extra_consecutive_blank_lines,no_leading_import_slash,no_leading_namespace_whitespace,trailing_comma_in_multiline_array,space_after_semicolon,single_quote,return_type_declaration,no_unused_imports,declare_strict_types --allow-risky=yes
	./vendor/bin/php-cs-fixer fix tests --rules=@PSR2,binary_operator_spaces,blank_line_before_return,function_typehint_space,no_empty_comment,no_empty_phpdoc,no_empty_statement,no_extra_consecutive_blank_lines,no_leading_import_slash,no_leading_namespace_whitespace,trailing_comma_in_multiline_array,space_after_semicolon,single_quote,return_type_declaration,no_unused_imports,declare_strict_types --allow-risky=yes
	./vendor/bin/phpstan analyse src tests --level=5 --no-progress

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
	sudo su vagrant -c"/var/www/etoa/eventhandler/target/etoad roundx -k -d -c /vagrant/htdocs/config/eventhandler.conf -p /var/www/etoa/htdocs/tmp/eventhandler.pid"

help: ## Helping devs since 2016
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
