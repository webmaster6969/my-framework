
# My Framework

Лёгкий фреймворк на PHP с архитектурой, вдохновлённой Laravel и DDD. Предназначен для создания REST API и других серверных приложений.

---

## 🚀 Быстрый старт

1. Склонируй репозиторий:

   ```bash
   git clone https://github.com/webmaster6969/my-framework.git
   cd my-framework
   ```

2. Создай файл окружения:

   ```bash
   cp .env.example .env
   ```

3. Открой `.env` и установи следующие переменные:

   | Переменная       | Значение по умолчанию | Описание                            |
   |------------------|-----------------------|-------------------------------------|
   | `APP_ENV`        | `development`         | Среда окружения                     |
   | `APP_PORT`       | `8080`                | Порт, на котором запускается app    |
   | `DB_HOST`        | `db`                  | Хост базы данных                    |
   | `DB_PORT`        | `5432`                | Порт базы данных                    |
   | `DB_DATABASE`    | `app`                 | Имя базы данных                     |
   | `DB_USERNAME`    | `user`                | Логин для подключения к БД          |
   | `DB_PASSWORD`    | `password`            | Пароль от БД                        |
   | `ENCRYPTION_KEY` | `test`                | Ключ для кодирования данных в Crypt |
   | `APP_LOCALE`     | `en`                  | Язык по умолчанию                   |


4. Установи зависимости и запусти локально (если PHP установлен):

   ```bash
   composer install
   php -S localhost:8080 -t public
   ```

---

## 🐳 Запуск в Docker

1. Собери контейнер:

   ```bash
   docker build -t my-framework .
   ```

2. Запусти:

   ```bash
   docker run -d \
     --name my-framework \
     -p 8080:8080 \
     --env-file .env \
     my-framework
   ```

3. Или через `docker-compose.yml`:

   ```yaml
   version: "3.8"
   services:
     app:
       build: .
       ports:
         - "8080:8080"
       env_file:
         - .env
       depends_on:
         - db

     db:
       image: postgres:15
       environment:
         POSTGRES_USER: user
         POSTGRES_PASSWORD: password
         POSTGRES_DB: app
       ports:
         - "5432:5432"
   ```

   Запуск:

   ```bash
   docker compose up -d
   ```

---

## 📂 Структура проекта

```
my-framework/
├── apache/             # Настройки Apache
├── app/                # Контроллеры, сервисы и т.п
├── bootstrap/          # Загрузка зависимостей и обработка данных от клиента
├── config/             # Конфигурации фреймворка
├── core/               # Ядро фреймворка
├── lang/               # Языковые файлы
├── logs/               # Логи
├── migrations/         # Миграции
├── php/                # PHP конфигурации
├── public/             # Корневая директория (index.php)
├── resources/          # Ресурсы и views
├── storage/            # Хранилище
├── tests/              # Тесты
├── .env.example        # Файл окружения
├── .env-test           # Файл окружения для тестов
├── .gitattributes      # Файл атрибутов Git
├── .gitignore          # Файл игнорирования Git
├── cli-doctrine.php    # Консоль Doctrine
├── composer.json       # Файл конфигурации Composer
├── composer.lock       # Файл с зависимостями Composer
├── docker-compose.yml  # Docker-compose
├── Dockerfile          # Dockerfile
├── phpstan.neon        # PHPStan конфигурация
└── README.md           # README файл
```

---

## ✅ Тесты

```bash
php vendor/bin/phpunit
```
