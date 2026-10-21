#!/bin/bash
export PATH="/usr/local/bin:/usr/bin:/bin:/usr/local/sbin:/usr/sbin:/sbin:$PATH"
PHP_BIN=""
for bin in php84 php8.4 /usr/bin/php84 /usr/local/bin/php84 /usr/bin/php8.4 php; do
    if command -v $bin >/dev/null 2>&1; then PHP_BIN=$(command -v $bin); break; fi
done
[ -z "$PHP_BIN" ] && PHP_BIN="php84"
cd "$(dirname "$0")"
while true; do
  $PHP_BIN artisan schedule:run
  sleep 60
done
