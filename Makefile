default: rebuild

rebuild:
	-clear
	-docker compose down -v --remove-orphans
	-docker compose up -d --build
	-docker compose logs -f

stop:
	-docker compose down -v --remove-orphans