# План: Распространение для обычных пользователей

Цель: Максимально простое развертывание для пользователей без технических навыков.

---

## Концепция

**Архитектура:**
- Skills (PHP CLI) — работа с API (T-Invest, MOEX, News)
- AGENTS.md + SKILL.md — инструкции для AI
- Внешний AI-провайдер — OpenCode, Claude Code, Codex CLI, и т.д.

**Пользовательский путь:**
```
1. curl | bash install.sh
2. Редактировать .env.local (токен T-Invest)
3. docker-compose up -d
4. Запустить opencode в папке
5. "Проанализируй мой портфель"
```

---

## Целевая аудитория

| Уровень | Навыки | Путь установки |
|---------|--------|----------------|
| Обычный пользователь | Умеет копировать команды в терминал | install.sh |
| Технический пользователь | Знает Docker, git | docker-compose up |
| Разработчик | Знает PHP, Composer | composer install |

---

## Структура файлов для дистрибуции

```
t-invest-agent/
├── docker/
│   ├── Dockerfile              # PHP 8.2 + Composer + расширения
│   └── docker-entrypoint.sh    # Точка входа
├── docker-compose.yml          # Окружение для skills
├── .env.example                # Шаблон с комментариями
├── .dockerignore               # Исключения для Docker
├── install.sh                  # curl | bash установка
├── Makefile                    # Удобные команды
├── test-skills.sh              # Проверка работоспособности
├── examples/
│   ├── portfolio-analysis.md   # Пример промпта
│   ├── ticker-analysis.md      # Пример промпта
│   └── weekly-report.md        # Пример промпта
├── AGENTS.md                   # Инструкции для AI (уже есть ✅)
├── skills/                     # SKILL.md файлы (уже есть ✅)
└── README.md                   # Обновить: инструкция для пользователей
```

---

## Docker

### Dockerfile

```dockerfile
FROM php:8.2-cli

# Установка расширений
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libxml2-dev \
    && docker-php-ext-install xml

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Копируем только composer.json для кэширования
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Копируем остальное
COPY . .

# Установка зависимостей
RUN composer dump-autoload --optimize

ENTRYPOINT ["docker-entrypoint.sh"]
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  skills:
    build:
      context: .
      dockerfile: docker/Dockerfile
    container_name: t-invest-skills
    volumes:
      - .:/app
      - skills-data:/app/data
    environment:
      - TINKOFF_TOKEN=${TINKOFF_TOKEN}
      - TINKOFF_ACCOUNT_ID=${TINKOFF_ACCOUNT_ID}
      - NEWS_CACHE_DIR=/app/data/cache/news
    working_dir: /app
    # Не запускается автоматически — для exec команд
    command: ["tail", "-f", "/dev/null"]
    restart: unless-stopped

volumes:
  skills-data:
```

### .env.example

```env
# ===========================================
# T-Invest Agent Configuration
# ===========================================
# Получите токен в приложении Т-Инвестиции:
# Профиль → Настройки → API → Создать токен
# Важно: используйте read-only токен!

# T-Invest API (обязательно)
TINKOFF_TOKEN=your_token_here
TINKOFF_ACCOUNT_ID=your_account_id

# News Cache (опционально)
# NEWS_CACHE_DIR=/app/data/cache/news
```

---

## Установка

### install.sh

```bash
#!/bin/bash
# T-Invest Agent Quick Installer
# Usage: curl -sSL https://raw.githubusercontent.com/prikotov/t-invest-agent/main/install.sh | bash

set -e

REPO_URL="https://github.com/prikotov/t-invest-agent.git"
INSTALL_DIR="t-invest-agent"

echo "============================================"
echo "  T-Invest Agent Installer"
echo "============================================"
echo ""

# Проверка Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен"
    echo "   Установите Docker: https://docs.docker.com/get-docker/"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose не установлен"
    echo "   Установите Docker Compose: https://docs.docker.com/compose/install/"
    exit 1
fi

# Клонирование
echo "📦 Клонирование репозитория..."
if [ -d "$INSTALL_DIR" ]; then
    echo "⚠️  Папка $INSTALL_DIR уже существует"
    read -p "Удалить и переустановить? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm -rf "$INSTALL_DIR"
    else
        echo "Отменено"
        exit 1
    fi
fi

git clone "$REPO_URL" "$INSTALL_DIR"
cd "$INSTALL_DIR"

# Конфигурация
echo ""
echo "📝 Создание конфигурации..."
cp .env.example .env.local

echo ""
echo "============================================"
echo "  ✅ Установка завершена!"
echo "============================================"
echo ""
echo "接下来需要做的事情:"
echo ""
echo "1. Отредактируйте .env.local:"
echo "   cd $INSTALL_DIR"
echo "   nano .env.local"
echo ""
echo "   Добавьте токен T-Invest:"
echo "   TINKOFF_TOKEN=your_token_here"
echo "   TINKOFF_ACCOUNT_ID=your_account_id"
echo ""
echo "2. Запустите Docker:"
echo "   docker-compose up -d --build"
echo ""
echo "3. Запустите AI-ассистента (OpenCode/Claude Code):"
echo "   cd $INSTALL_DIR"
echo "   opencode"
echo "   > Проанализируй мой портфель"
echo ""
echo "4. Или используйте skills напрямую:"
echo "   docker-compose exec skills ./vendor/bin/t-invest portfolio:positions"
echo ""
```

---

## Makefile

```makefile
.PHONY: install build up down chat test clean help

# Default
help:
	@echo "T-Invest Agent Commands:"
	@echo "  make install  - Полная установка"
	@echo "  make up       - Запуск Docker"
	@echo "  make down     - Остановка Docker"
	@echo "  make test     - Проверка skills"
	@echo "  make shell    - Вход в контейнер"
	@echo "  make clean    - Очистка данных"

install: build up
	@echo "✅ Установка завершена"
	@echo "Отредактируйте .env.local и добавьте токен T-Invest"

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

shell:
	docker-compose exec skills bash

test:
	docker-compose exec skills ./test-skills.sh

clean:
	rm -rf data/*
	docker-compose down -v

# Skills shortcuts
positions:
	docker-compose exec skills ./vendor/bin/t-invest portfolio:positions

report:
	docker-compose exec skills ./vendor/bin/t-invest portfolio:report --period=week

ticker:
	@read -p "Тикер: " ticker; \
	docker-compose exec skills ./vendor/bin/moex security:trade-data $$ticker

news:
	@read -p "Тикер: " ticker; \
	docker-compose exec skills ./vendor/bin/news news:fetch --ticker $$ticker
```

---

## test-skills.sh

```bash
#!/bin/bash
# Проверка работоспособности skills

set -e

echo "=== T-Invest Agent: Проверка Skills ==="
echo ""

# Проверка переменных окружения
echo "1. Проверка конфигурации..."
if [ -z "$TINKOFF_TOKEN" ]; then
    echo "❌ TINKOFF_TOKEN не установлен"
    exit 1
fi
echo "   ✅ TINKOFF_TOKEN установлен"

# Проверка vendor
echo ""
echo "2. Проверка зависимостей..."
if [ ! -d "vendor" ]; then
    echo "   Установка composer зависимостей..."
    composer install --no-interaction
fi
echo "   ✅ Зависимости установлены"

# Тест skills
echo ""
echo "3. Тестирование skills..."

echo "   → portfolio:positions..."
./vendor/bin/t-invest portfolio:positions > /dev/null 2>&1 && echo "   ✅ OK" || echo "   ⚠️  Требуется токен"

echo "   → moex security:trade-data SBER..."
./vendor/bin/moex security:trade-data SBER > /dev/null 2>&1 && echo "   ✅ OK" || echo "   ❌ FAIL"

echo "   → news news:fetch..."
./vendor/bin/news news:fetch --limit=1 > /dev/null 2>&1 && echo "   ✅ OK" || echo "   ❌ FAIL"

echo ""
echo "=== Проверка завершена ==="
```

---

## README.md (секция Quick Start)

```markdown
## Quick Start

### Вариант 1: Автоматическая установка (рекомендуется)

```bash
curl -sSL https://raw.githubusercontent.com/prikotov/t-invest-agent/main/install.sh | bash
cd t-invest-agent
# Отредактируйте .env.local
docker-compose up -d --build
```

### Вариант 2: Docker вручную

```bash
git clone https://github.com/prikotov/t-invest-agent.git
cd t-invest-agent
cp .env.example .env.local
# Отредактируйте .env.local
docker-compose up -d --build
```

### Вариант 3: Для разработчиков

```bash
git clone https://github.com/prikotov/t-invest-agent.git
cd t-invest-agent
cp .env.example .env.local
composer install
composer skills:install
```

---

## Использование с AI-ассистентами

### OpenCode

```bash
cd t-invest-agent
opencode
```

```
> Проанализируй мой портфель
> Какие новости по Сбербанку?
> Что думаешь про GAZP?
```

### Claude Code

```bash
cd t-invest-agent
claude
```

### Codex CLI / Gemini CLI / Qwen CLI / KiloCode

Аналогично — запустите CLI в папке проекта.

---

## Прямое использование Skills

```bash
# Позиции портфеля
docker-compose exec skills ./vendor/bin/t-invest portfolio:positions

# Рыночные данные
docker-compose exec skills ./vendor/bin/moex security:trade-data SBER

# Новости
docker-compose exec skills ./vendor/bin/news news:fetch --ticker SBER

# Технический анализ
docker-compose exec skills ./vendor/bin/t-invest analyze:technical --ticker=SBER
```

Или с Makefile:

```bash
make positions
make ticker  # введёте тикер
make news    # введёте тикер
```
```

---

## Примеры промптов

### examples/portfolio-analysis.md

```markdown
Проанализируй мой портфель:

1. Получить позиции: skill portfolio:positions
2. Для каждой позиции > 5% веса:
   - Цена: moex security:trade-data $TICKER
   - Новости: news news:fetch --ticker $TICKER --days=7
3. Сформировать сводку с рекомендациями

Формат: таблица + краткий анализ.
Завершить: "Не является инвестиционной рекомендацией."
```

### examples/ticker-analysis.md

```markdown
Проанализируй {TICKER}:

1. Цена: moex security:trade-data {TICKER}
2. Фундаментал: skill analyze:fundamental --ticker={TICKER}
3. Техника: skill analyze:technical --ticker={TICKER}
4. Новости: news news:fetch --ticker {TICKER} --days=7

Формат: таблица метрик + тех. картина + рекомендация.
Завершить: "Не является инвестиционной рекомендацией."
```

---

## Задачи

### Приоритет: HIGH

| Задача | Статус | Описание |
|--------|--------|----------|
| Dockerfile | TODO | PHP 8.2 + Composer + расширения |
| docker-compose.yml | TODO | Окружение для skills |
| .env.example | TODO | Шаблон с комментариями |
| .dockerignore | TODO | Исключения для Docker |
| install.sh | TODO | Скрипт быстрой установки |
| README.md update | TODO | Секция Quick Start |

### Приоритет: MEDIUM

| Задача | Статус | Описание |
|--------|--------|----------|
| Makefile | TODO | Удобные команды |
| test-skills.sh | TODO | Проверка работоспособности |
| examples/*.md | TODO | Примеры промптов |

### Приоритет: LOW

| Задача | Статус | Описание |
|--------|--------|----------|
| GitHub Actions | TODO | Автоматическая сборка Docker образа |
| Docker Hub | TODO | Публикация готового образа |

---

## Оценка времени

| Задача | Время |
|--------|-------|
| Docker (Dockerfile + docker-compose) | 1 час |
| install.sh | 30 мин |
| README update | 30 мин |
| Makefile + test-skills.sh | 30 мин |
| examples/*.md | 30 мин |
| Тестирование | 1 час |
| **Итого** | **4 часа** |

---

## Чек-лист перед релизом

- [ ] Dockerfile собирается без ошибок
- [ ] docker-compose up -d работает
- [ ] install.sh успешно выполняется на чистой машине
- [ ] .env.example содержит все необходимые переменные
- [ ] README.md содержит понятную инструкцию
- [ ] Все skills работают в Docker
- [ ] Протестировано с OpenCode/Claude Code
