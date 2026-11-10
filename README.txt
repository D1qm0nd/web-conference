Конференция "Наука и технологии"

Запуск:
  docker compose down -v
  docker compose up -d --build

Сайт:       http://localhost:8080
phpMyAdmin: http://localhost:8081  (root / rootpass)

База и таблица создаются автоматически при первом старте (schema.sql).
Команда down -v нужна только если в базе остались старые тестовые записи.
