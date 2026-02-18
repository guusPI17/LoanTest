# Loan API

REST API для подачи и обработки заявок на займ.  
Тестовое (https://docs.google.com/document/d/15BRZincw_j7dIdaKxQZNsy-L8h4OYE12/edit)

## Технологический стек

- **PHP** 8.2
- **Yii2** 2.x
- **PostgreSQL** 16
- **Nginx** (Alpine)
- **Docker Compose**

## Требования

- Docker >= 24
- Docker Compose >= 2.x

## Запуск проекта

```bash
# Клонировать репозиторий
git clone <repository-url>
cd loan

# Собрать и запустить контейнеры
docker-compose up --build -d

# Приложение будет доступно по адресу:
# http://localhost
```

Во время запуска PHP-контейнер автоматически:
1. Выполняется composer install при необходимости
2. Применяет миграции базы данных

## Параметры подключения к базе данных

| Параметр | Значение |
|----------|----------|
| Host     | localhost      |
| Port     | 5432     |
| Database | loans    |
| User     | user     |
| Password | password |

Проброс порта PostgreSQL на хост сделан только по требованию в задаче. В реальной разработке его нужно убрать и переопределять в `docker-compose.override.yml`.
## API Эндпоинты

### POST /requests — Подача заявки на займ

**Тело запроса:**
```json
{
  "user_id": 1,
  "amount": 3000,
  "term": 30
}
```

**Успешный ответ (HTTP 201):**
```json
{
  "result": true,
  "id": 42
}
```

**Ошибка валидации (HTTP 400):**
```json
{
  "result": false
}
```

Условия отказа:
- Отсутствует или некорректно одно из полей (`user_id`, `amount`, `term`)
- У пользователя уже есть одобренная заявка

### GET /processor — Обработка заявок

**Параметры запроса:**
- `delay` (целое число) — задержка принятия решения в секундах

**Пример:**
```
GET /processor?delay=5
```

**Ответ (HTTP 200):**
```json
{
  "result": true
}
```

Обрабатывает все необработанные заявки (`status IS NULL`). Каждая заявка получает статус `approved` (10% вероятность) или `declined`. У одного пользователя не может быть более одной одобренной заявки.

## Тестирование

```bash
# Все тесты
docker-compose exec php composer test

# Только unit-тесты
docker-compose exec php composer test:unit

# Только функциональные тесты
docker-compose exec php composer test:functional
```

## Проверка кодстайла

```bash
# Проверить без изменений
docker-compose exec php composer cs-check

# Автоматически исправить
docker-compose exec php composer cs-fix
```

## Примеры запросов

```bash
# Подать заявку
curl -X POST http://localhost/requests \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "amount": 3000, "term": 30}'

# Запустить обработку с задержкой 5 секунд
curl "http://localhost/processor?delay=5"
```