# REST API Справочника Организаций

REST API приложение для работы со справочником организаций, зданий и деятельностей.

## Описание

Приложение предоставляет API для работы с:
- **Организациями** - карточки организаций с телефонами, зданиями и видами деятельности
- **Зданиями** - адреса с географическими координатами
- **Деятельностями** - иерархическая классификация видов деятельности (до 3 уровней)

## Функционал API

### Организации
- Список организаций в конкретном здании
- Список организаций по виду деятельности (включая дочерние)
- Поиск организаций в радиусе от точки
- Поиск организаций в прямоугольной области
- Поиск организаций по названию
- Информация об организации по ID

### Здания
- Список всех зданий
- Информация о здании по ID

## Технологии

- **Backend**: Laravel 12
- **База данных**: MySQL 8.0
- **Контейнеризация**: Docker & Docker Compose
- **Документация**: Swagger UI

## Быстрый старт

### 1. Клонирование и настройка

```bash
git clone <repository-url>
cd REST_API_project
```

### 2. Запуск с Docker

```bash
# Запуск контейнеров
docker-compose up -d

# Ожидание запуска MySQL (30-60 секунд)
sleep 60

# Выполнение миграций и заполнение тестовыми данными
docker-compose exec php-fpm php artisan migrate:fresh --seed
```

### 3. Проверка работы

Откройте в браузере:
- **API Документация**: http://localhost:81/api-docs.html
- **Тестовый запрос**: http://localhost:81/api/buildings?api_key=test-api-key-123

## API Ключ

Для доступа к API используется статический ключ:
- **Ключ**: `test-api-key-123`
- **Передача**: В заголовке `X-API-Key` или параметре `api_key`

## Примеры запросов

### Получение списка зданий
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/buildings"
```

### Организации в здании
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/building/1"
```

### Поиск организаций по деятельности
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/activity/1"
```

### Поиск в радиусе
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/radius?latitude=55.7558&longitude=37.6176&radius=5"
```

### Поиск по названию
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/search?name=Рога"
```

## Структура базы данных

### Таблицы:
- `buildings` - здания с координатами
- `activities` - виды деятельности (иерархия)
- `organizations` - организации
- `organization_phones` - телефоны организаций
- `organization_activity` - связь организаций с деятельностями

### Тестовые данные:
- 4 здания в Москве
- 10 видов деятельности (3 уровня иерархии)
- 5 организаций с телефонами и деятельностями

## Docker контейнеры

- **web** (nginx): 81:80 - веб-сервер
- **php-fpm** (PHP 8.3): 9000 - обработка PHP
- **db** (MySQL 8.0): 3307:3306 - база данных

## Разработка

### Локальная разработка
```bash
# Подключение к PHP контейнеру
docker-compose exec php-fpm bash

# Выполнение команд Laravel
php artisan migrate
php artisan make:controller Api/NewController
```

### Логи
```bash
# Просмотр логов
docker-compose logs -f

# Логи конкретного сервиса
docker-compose logs -f php-fpm
docker-compose logs -f db
```

## API Endpoints

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/buildings` | Список всех зданий (с пагинацией) |
| GET | `/api/buildings/{id}` | Информация о здании |
| GET | `/api/organizations/building/{id}` | Организации в здании |
| GET | `/api/organizations/activity/{id}` | Организации по деятельности |
| GET | `/api/organizations/radius` | Поиск в радиусе |
| GET | `/api/organizations/area` | Поиск в области |
| GET | `/api/organizations/search` | Поиск по названию |
| GET | `/api/organizations/search/filters` | Поиск с фильтрами |
| GET | `/api/organizations/{id}` | Информация об организации |
| GET | `/api/activities` | Список деятельностей с иерархией |
| GET | `/api/activities/{id}` | Информация о деятельности |
| GET | `/api/activities/{parentId}/children` | Дочерние деятельности |
| POST | `/api/activities` | Создать деятельность |
| PUT | `/api/activities/{id}` | Обновить деятельность |
| DELETE | `/api/activities/{id}` | Удалить деятельность |

## Остановка

```bash
docker-compose down
```

## Требования

- Docker
- Docker Compose
- 2GB свободной RAM
- 1GB свободного места на диске
