.PHONY: build install shell validate lint test test-coverage ci audit registry smoke
DOCKER_COMPOSE ?= docker compose
PHP = $(DOCKER_COMPOSE) run --rm php

build:
	$(DOCKER_COMPOSE) build

install: build
	$(PHP) composer install

shell:
	$(DOCKER_COMPOSE) run --rm php bash

validate:
	$(PHP) composer validate --strict

registry:
	$(PHP) composer verify:registry

smoke:
	$(PHP) composer verify:smoke

lint:
	$(PHP) composer lint

test:
	$(PHP) composer test

test-coverage:
	$(PHP) composer test:coverage

ci:
	$(PHP) composer ci

audit:
	$(PHP) composer audit
