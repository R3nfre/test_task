setup:
	@if [ ! -f .env ]; then \
    		cp .env.example .env; \
    		echo ".env file has been created from .env.example"; \
    	else \
    		echo ".env already exists, skipping copy"; \
    	fi
	docker-compose up -d
	cd app
	@if [ ! -f .env ]; then \
        	  ln -s ../.env .env; \
    fi
	docker-compose exec php composer update -d /var/www/html/app
	docker-compose exec php app/yii migrate

down:
	docker-compose down

restart:
	docker-compose restart