# OpenBuilder test env — PHP 8.3 + built-in server, with sqlite + extensions
FROM php:8.3-cli-bookworm

# System deps for sqlite3 + dev server
RUN apt-get update && apt-get install -y --no-install-recommends \
    sqlite3 libsqlite3-dev ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions: pdo_sqlite is the critical one (Database.php uses PDO SQLite)
# Note: php:8.3-cli-bookworm already has pdo_sqlite by default; we still install via docker-php-ext-install
# to be safe in case of slim variants.
RUN docker-php-ext-install pdo pdo_sqlite sqlite3 2>&1 | tail -3 || true

# Test environment defaults
ENV APP_ENV=development \
    APP_DEBUG=1 \
    GEMINI_API_KEY= \
    PHP_SESSION_DIR=/tmp

# Workdir
WORKDIR /var/www/html

# Copy app (use .dockerignore to keep tests/node_modules out if desired;
# for this test image we WANT tests/ and node_modules/ so playwright can run in-container too,
# but for the server image we only need the runtime files).
COPY . /var/www/html/

# Remove any pre-existing sqlite file from the COPY — the named volume will
# create a *directory* at the mount target, which must be a directory in the image.
# The DB will be initialized by Database::initTables() at first request.
RUN rm -f /var/www/html/database.sqlite

# Ensure writable dirs for sessions
RUN mkdir -p /tmp/sessions && chown www-data:www-data /tmp/sessions && chmod 1777 /tmp/sessions

# Seed/initialize database on every container start (idempotent — Database::initTables uses CREATE IF NOT EXISTS)
# and expose the built-in server.
EXPOSE 8080

# Healthcheck — hit a cheap endpoint
HEALTHCHECK --interval=10s --timeout=3s --start-period=5s --retries=5 \
    CMD curl -fsS "http://localhost:8080/?page=health" >/dev/null || exit 1

# Default: built-in PHP dev server, public doc root is repo root, index.php router.
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html", "/var/www/html/index.php"]
