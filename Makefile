.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

qa: phpstan cs

cs:
	vendor/bin/ecs check src tests

csf:
	vendor/bin/ecs check src tests --fix

phpstan:
	vendor/bin/phpstan analyze -l max src

tests:
	vendor/bin/tester -C tests

coverage-clover:
	vendor/bin/tester -C --coverage coverage.xml --coverage-src src tests

coverage-html:
	vendor/bin/tester -C --coverage coverage.hzml --coverage-src src tests
