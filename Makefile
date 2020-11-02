COMPOSER_ARGS += --no-progress --no-interaction

.PHONY: build
build: vendor

.PHONY: list
list:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$'

.PHONY: vendor
vendor: vendor/lock

vendor/lock: composer.json
	composer update $(COMPOSER_ARGS)
	touch vendor/lock

.PHONY: test
test:
	vendor/bin/phpunit $(PHPUNIT_ARGS)

.PHONY: cs
cs:
	vendor/bin/phpcs $(PHPCS_ARGS)

.PHONY: fix
fix:
	vendor/bin/phpcbf

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse $(PHPSTAN_ARGS)

.PHONY: psalm
psalm:
	vendor/bin/psalm $(PSALM_ARGS)

.PHONY: check
check: build cs phpstan psalm test

.PHONY: clean
clean: clean-vendor

.PHONY: clean-vendor
clean-vendor:
	rm -rf vendor
