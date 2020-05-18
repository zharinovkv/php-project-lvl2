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

h:
	bin/gendiff -h

v:
	bin/gendiff -v

g:
	bin/gendiff assets/before.json assets/after.json

g2:
	bin/gendiff assets/before_tree.json assets/after_tree.json

gp:
	bin/gendiff --format pretty assets/before.json assets/after.json

gp2:
	bin/gendiff --format pretty assets/before_tree.json assets/after_tree.json

y:
	bin/gendiff assets/before.yaml assets/after.yaml

y2:
	bin/gendiff before.yaml after.yaml

p:
	bin/gendiff --format plain assets/before.json assets/after.json

p2:
	bin/gendiff --format plain assets/before_tree.json assets/after_tree.json

j:
	bin/gendiff  --format json assets/before.json assets/after.json

j2:
	bin/gendiff  --format json assets/before_tree.json assets/after_tree.json

x:
	bin/gendiff ../../../Desktop/assets/before.json ../../../Desktop/assets/after.json

z:
	bin/gendiff /home/konstantin/Desktop/assets/before.json /home/konstantin/Desktop/assets/after.json
