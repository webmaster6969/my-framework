
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
├── app/                # Контроллеры, сервисы и т.п.
├── public/             # Корневая директория (index.php)
├── config/             # Конфигурации
├── routes/             # Определения маршрутов
├── src/                # Вспомогательные классы и библиотеки
├── .env.example
├── Dockerfile
├── docker-compose.yml
└── README.md
```

---

## ✅ Тесты

```bash
php vendor/bin/phpunit
```

## ✨ Вклад

Пул-реквесты и предложения приветствуются!
