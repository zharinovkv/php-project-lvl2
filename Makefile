install:
	composer install

cu:
	composer update

cda:
	composer dump-autoload

lint:
	composer run-script phpcs -- --standard=PSR12 src tests

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 src tests

test:
	composer run-script phpunit tests

st:
	git status

h:
	bin/gendiff -h

v:
	bin/gendiff -v

g:
	bin/gendiff tests/fixtures/before.json tests/fixtures/after.json

gp:
	bin/gendiff --format pretty tests/fixtures/before.json tests/fixtures/after.json


y:
	bin/gendiff tests/fixtures/before.yaml tests/fixtures/after.yaml

y2:
	bin/gendiff before.yaml after.yaml

p:
	bin/gendiff --format plain tests/fixtures/before.json tests/fixtures/after.json

j:
	bin/gendiff  --format json tests/fixtures/before.json tests/fixtures/after.json
