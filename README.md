# REST API Справочника Организаций

REST API приложение для работы со справочником организаций, зданий и деятельностей.

## Описание

Приложение предоставляет API для работы с:
- **Организациями** - карточки организаций с телефонами, зданиями и видами деятельности
- **Зданиями** - адреса с географическими координатами
- **Деятельностями** - иерархическая классификация видов деятельности (до 3 уровней)

## Функционал API

### Организации
- Список всех организаций
- Информация об организации по ID
- Поиск организаций по названию
- Поиск организаций с фильтрами
- Поиск организаций в радиусе от точки
- Поиск организаций в прямоугольной области

### Здания
- Список всех зданий
- Информация о здании по ID

### Деятельности
- Список всех деятельностей с иерархией
- Информация о деятельности по ID
- Дочерние деятельности
- Создание деятельности
- Обновление деятельности
- Удаление деятельности

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

### Получение всех организаций
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations"
```

### Получение всех деятельностей
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/activities"
```

### Поиск организаций в радиусе
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/radius?latitude=55.7558&longitude=37.6176&radius=5"
```

### Поиск организаций по названию
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/search?name=Рога"
```

### Поиск организаций с фильтрами
```bash
curl -H "X-API-Key: test-api-key-123" \
  "http://localhost:81/api/organizations/search/filters?name=Рога&building_id=1&activity_id=2&sort_by=name&sort_order=asc"
```

### Создание деятельности
```bash
curl -X POST -H "X-API-Key: test-api-key-123" \
  -H "Content-Type: application/json" \
  -d '{"name":"Новая деятельность","parent_id":1}' \
  "http://localhost:81/api/activities"
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

### Organizations (`/api/organizations/`)
| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/` | Список всех организаций |
| GET | `/{id}` | Информация об организации |
| GET | `/search` | Поиск организаций по названию |
| GET | `/search/filters` | Поиск организаций с фильтрами |
| GET | `/radius` | Поиск организаций в радиусе |
| GET | `/area` | Поиск организаций в области |

### Buildings (`/api/buildings/`)
| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/` | Список всех зданий |
| GET | `/{id}` | Информация о здании |

### Activities (`/api/activities/`)
| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/` | Список всех деятельностей с иерархией |
| GET | `/{id}` | Информация о деятельности |
| GET | `/{parentId}/children` | Дочерние деятельности |
| POST | `/` | Создать деятельность |
| PUT | `/{id}` | Обновить деятельность |
| DELETE | `/{id}` | Удалить деятельность |

## Остановка

```bash
docker-compose down
```

## Требования

- Docker
- Docker Compose
- 2GB свободной RAM
- 1GB свободного места на диске
