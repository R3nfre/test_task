setup:
	@if [ ! -f .env ]; then \
    		cp .env.example .env; \
    		echo ".env file has been created from .env.example"; \
    	else \
    		echo ".env already exists, skipping copy"; \
    	fi
	docker-compose up -d
	docker-compose exec cd app
	docker-compose exec ln -s ../.env .env
	docker-compose exec composer install
	docker-compose cd ..
	docker-compose exec php app/yii migrate

down:
	docker-compose down

restart:
	docker-compose restart