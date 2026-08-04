default: rebuild

rebuild:
	-clear
	-docker compose down
	-docker compose up -d --build
	-docker compose logs -f

stop:
	-docker compose down