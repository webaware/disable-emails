PKG_NAME			:= disable-emails
PKG_VERSION			:= $(shell sed -rn 's/^Version: (.*)/\1/p' $(PKG_NAME).php)

ZIP					:= .dist/$(PKG_NAME)-$(PKG_VERSION).zip
FIND_PHP			:= find . -path ./vendor -prune -o -path ./node_modules -prune -o -path './.*' -o -name '*.php'
LINT_PHP			:= $(FIND_PHP) -exec php -l '{}' \; >/dev/null
SNIFF_PHP			:= vendor/bin/phpcs -ps
SNIFF_PHP_5			:= $(SNIFF_PHP) --standard=phpcs-5.2.xml
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
	@$(LINT_PHP)
	@$(SNIFF_PHP)
	@$(SNIFF_PHP_5)

# tests

test: test-php7 test-php8

test-php7: /tmp/wordpress-tests-lib
	php7.4 vendor/bin/phpunit

test-php8: /tmp/wordpress-tests-lib
	php8.0 vendor/bin/phpunit

/tmp/wordpress-tests-lib:
	bin/install-wp-tests.sh wp_test website website localhost nightly
	sed -i "2i define('WP_DEBUG_LOG', __DIR__ . '/debug.log');" /tmp/wordpress-tests-lib/wp-tests-config.php
