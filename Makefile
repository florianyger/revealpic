.PHONY: up
up: server adminer yarn

.PHONY: start
server:
	php bin/console server:start &

.PHONY: adminer
adminer:
	./vendor/bin/adminer 8082 &

.PHONY: yarn
yarn:
	yarn encore dev --watch &

.PHONY: stop
stop:
	php bin/console server:stop

.PHONY: clean
clean:
	rm -rf assets/pictures/*