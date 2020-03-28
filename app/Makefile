.DEFAULT_GOAL:=help

.PHONY: install
install:
	composer install --no-interaction --no-suggest --ansi

.PHONY: clean
clean:
	bin/console ca:cl

.PHONY: dump
dump:
	composer dump-autoload -o

.PHONY: init
init: clean dump drop-and-create-read_model drop-and-create-event_store

.PHONY: drop-and-create-event_store
drop-and-create-event_store:
	bin/console a:e:d

.PHONY: drop-and-create-read_model
drop-and-create-read_model:
	bin/console a:r:d

.PHONY: test
test: phpunit behat

.PHONY: phpunit
phpunit:
	bin/phpunit --testdox --exclude-group=none --colors=always

.PHONY: behat
behat:
	vendor/bin/behat

.PHONY: cs
cs:
	vendor/bin/php-cs-fixer fix --no-interaction --diff --verbose
