.PHONY: install
install:
	composer update

.PHONY: qa
qa: phpstan cs

.PHONY: cs
cs:
ifdef GITHUB_ACTION
	vendor/bin/phpcs --standard=ruleset.xml -q --report=checkstyle --extensions="php,phpt" src tests | cs2pr
else
	vendor/bin/phpcs --standard=ruleset.xml --extensions="php,phpt" src tests
endif

.PHONY: csf
csf:
	vendor/bin/phpcbf --standard=ruleset.xml --extensions="php,phpt" src tests

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyze -c phpstan.neon

.PHONY: tests
tests:
	vendor/bin/tester -C tests

.PHONY: coverage
coverage:
ifdef GITHUB_ACTION
	vendor/bin/tester -C --coverage coverage.xml --coverage-src src tests
else
	vendor/bin/tester -C --coverage coverage.html --coverage-src src tests
endif
