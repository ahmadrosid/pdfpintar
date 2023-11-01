.PHONY: build
build: 
	docker-compose build

start:
	docker-compose up -d

start-clean:
	docker-compose up --force-recreate --build

stop:
	docker-compose up -d

deploy:
	flyctl deploy

dev:
	npx concurrently "npm run dev" "php artisan serve"
