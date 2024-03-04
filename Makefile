docker_compose_file=docker-compose.yml
env_example_file=.env.example

# For local environment
info:
	@make exec cmd="php artisan --version"
	@make exec cmd="php --version"

status:
	@docker-compose ps

start:
	rm -rf .env
	cp $(env_example_file) .env
	@docker-compose -f $(docker_compose_file) up -d

stop:
	@docker-compose -f $(docker_compose_file) down

restart: stop start

ssh:
	@docker-compose -f $(docker_compose_file) exec web bash

rebuild:
	#sudo rm -rf .docker
	@docker-compose -f $(docker_compose_file) build --no-cache
	@docker-compose -f $(docker_compose_file) up -d

exec:
	@docker-compose -f $(docker_compose_file) exec web $$cmd

initialize:
	@make exec cmd="composer install"
	@make exec cmd="php artisan key:generate"
	@make exec cmd="php artisan migrate"
	@make exec cmd="php artisan db:seed"

migrate:
	@make exec cmd="php artisan migrate --force"

seed:
	@make exec cmd="php artisan db:seed --force"

config-clear:
	@docker-compose -f $(docker_compose_file) exec web php artisan config:clear
	@docker-compose -f $(docker_compose_file) exec web php artisan config:cache
	@docker-compose -f $(docker_compose_file) exec web php artisan cache:clear

logs:
	@docker-compose -f $(docker_compose_file) logs

# Web container
start-web:
	@docker-comppose -f $(docker_comppose_file) up -d web

stop-web:
	@docker-compose -f $(docker_compose_file) stop web

restart-web:
	@docker-compose -f $(docker_compose_file) restart web

ssh-web: ssh

rebuild-web:
	@docker-compose -f $(docker_compose_file) up --build -d web

logs-web:
	@docker-compose -f $(docker_compose_file) logs web

# Nginx container
start-nginx:
	@docker-compose -f $(docker_compose_file) up -d nginx

stop-nginx:
	@docker-compose -f $(docker_compose_file) stop nginx

restart-nginx:
	@docker-compose -f $(docker_compose_file) restart nginx

ssh-nginx:
	@docker-compose -f $(docker_compose_file) exec nginx sh

rebuild-nginx:
	@docker-compose -f $(docker_compose_file) up --build -d nginx

logs-nginx:
	@docker-compose -f $(docker_compose_file) logs nginx

# Mysql container
start-mysql:
	@docker-compose -f $(docker_compose_file) up -d mysql

stop-mysql:
	@docker-compose -f $(docker_compose_file) stop mysql

restart-mysql:
	@docker-compose -f $(docker_compose_file) restart mysql

ssh-mysql:
	@docker-compose -f $(docker_compose_file) exec mysql sh

rebuild-mysql:
	#sudo rm -rf .docker
	@docker-compose -f $(docker_compose_file) up --build -d mysql

logs-mysql:
	@docker-compose -f $(docker_compose_file) logs mysql

# Node container
start-node:
	@docker-compose -f $(docker_compose_file) up -d node

stop-node:
	@docker-compose -f $(docker_compose_file) stop node

restart-node:
	@docker-compose -f $(docker_compose_file) restart node

ssh-node:
	@docker-compose -f $(docker_compose_file) exec node sh

rebuild-node:
	@docker-compose -f $(docker_compose_file) up --build -d node

npm-install:
	@docker-compose -f $(docker_compose_file) exec node npm install

npm-update:
	@docker-compose -f $(docker_compose_file) exec node npm update

logs-node:
	@docker-compose -f $(docker_compose_file) logs node

# Composer
composer-install:
	@make exec cmd="composer install"

composer-update:
	@make exec cmd="composer update"

# DebugBar
enable-debugbar:
	@make exec cmd="composer require barryvdh/laravel-debugbar"

disable-debugbar:
	@make exec cmd="composer remove barryvdh/laravel-debugbar"