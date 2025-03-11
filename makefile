setup:
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate

down:
	docker-compose down

restart:
	docker-compose restart
