start:
	cp .env.example .env
	chmod -R 777 .env
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan key:generate
	./vendor/bin/sail artisan migrate
	./vendor/bin/sail artisan db:seed

kill:
	./vendor/bin/sail down
