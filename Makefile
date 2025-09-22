PKG_NAME			:= disable-emails
PKG_VERSION			:= $(shell sed -rn 's/^Version: (.*)/\1/p' $(PKG_NAME).php)

ZIP					:= .dist/$(PKG_NAME)-$(PKG_VERSION).zip
FIND_PHP			:= find . -path ./vendor -prune -o -path ./node_modules -prune -o -path './.*' -o -name '*.php'
SRC_PHP				:= $(shell $(FIND_PHP) -print)

# environment variables for unit tests
export WP_PLUGIN_DIR	= $(shell cd ..; pwd)

all:
	@echo please see Makefile for available builds / commands

.PHONY: all lint lint-js lint-php test test-php7 test-php8 zip wpsvn js

# release product

zip: $(ZIP)

$(ZIP): $(SRC_PHP) static/css/* static/js/* *.md *.txt
	rm -rf .dist
	mkdir .dist
	git archive HEAD --prefix=$(PKG_NAME)/ --format=zip -9 -o $(ZIP)

# WordPress plugin directory

wpsvn: lint
	svn up .wordpress.org
	rm -rf .wordpress.org/trunk
	mkdir .wordpress.org/trunk
	git archive HEAD --format=tar | tar x --directory=.wordpress.org/trunk

# build JavaScript targets

JS_SRC_DIR		:= source/js
JS_TGT_DIR		:= static/js
JS_SRCS			:= $(shell find $(JS_SRC_DIR) -name '*.js' -print)
JS_TGTS			:= $(subst $(JS_SRC_DIR),$(JS_TGT_DIR),$(JS_SRCS))

js: $(JS_TGTS)

$(JS_TGTS): $(JS_TGT_DIR)/%.js: $(JS_SRC_DIR)/%.js
	npx babel --source-type script --presets @babel/preset-env --out-file $@ $<
	npx uglify-js $@ --output $(basename $@).min.js -b beautify=false,ascii_only -c -m --comments '/^!/'

# code linters

lint: lint-js lint-php

lint-js:
	@echo JavaScript lint...
	@npx eslint --ext js $(JS_SRC_DIR)

lint-php:
	@echo PHP lint...
	@$(FIND_PHP) -exec php5.6 -l '{}' \; >/dev/null
	@$(FIND_PHP) -exec php8.4 -l '{}' \; >/dev/null
	@vendor/bin/phpcs -ps
	@vendor/bin/phpcs -ps --standard=phpcs-5.2.xml

# tests

test: test-php74 test-php84

test-php74: /tmp/wordpress-tests-lib
	php7.4 vendor/bin/phpunit

test-php80: /tmp/wordpress-tests-lib
	php8.0 vendor/bin/phpunit

test-php81: /tmp/wordpress-tests-lib
	php8.1 vendor/bin/phpunit

test-php82: /tmp/wordpress-tests-lib
	php8.2 vendor/bin/phpunit

test-php83: /tmp/wordpress-tests-lib
	php8.3 vendor/bin/phpunit

test-php84: /tmp/wordpress-tests-lib
	php8.4 vendor/bin/phpunit

/tmp/wordpress-tests-lib:
	bin/install-wp-tests.sh wp_test website website localhost nightly
	sed -i "2i define('WP_DEBUG_LOG', __DIR__ . '/debug.log');" /tmp/wordpress-tests-lib/wp-tests-config.php
