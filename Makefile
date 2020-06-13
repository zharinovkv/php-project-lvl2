install:
	composer install

cu:
	composer update

cda:
	composer dump-autoload

lint:
	composer run-script phpcs -- --standard=PSR12 src tests

lf:
	composer run-script phpcbf -- --standard=PSR12 src tests

test:
	composer run-script phpunit tests

e1:
	bin/gendiff --format pretty tests/fixtures/before.json tests/fixtures/after.json