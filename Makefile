install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src tests

test:
	composer run-script phpunit tests

h:
	gendiff -h

v:
	gendiff -v

g:
	bin/gendiff assets/before.json assets/after.json

g2:
	bin/gendiff assets/before_bar.json assets/after_bar.json

g3:
	bin/gendiff assets/before_tree.json assets/after_tree.json
