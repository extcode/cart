#!/usr/bin/env bash
#
# Shared setup for all scripts in Build/Scripts.
#
# This file is sourced, not executed. It changes to the project root, so every
# script behaves the same no matter from where it has been called, and it
# provides the defaults all scripts rely on.
#
# Environment variables used as overrides:
#   PHP_BIN           php binary to use, defaults to "php"
#   PHP_MEMORY_LIMIT  memory_limit for all php calls, defaults to "-1"

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
readonly PROJECT_ROOT
cd "${PROJECT_ROOT}"

PHP_BIN="${PHP_BIN:-php}"
PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT:--1}"
BIN_DIR="${PROJECT_ROOT}/.build/bin"
readonly PHP_BIN PHP_MEMORY_LIMIT BIN_DIR

# Runs php with the configured memory limit, independent of the php.ini in use.
runPhp() {
    "${PHP_BIN}" -d memory_limit="${PHP_MEMORY_LIMIT}" "$@"
}

# Fails with a readable message if composer install has not been run yet.
requireBinary() {
    if [ ! -f "${BIN_DIR}/$1" ]; then
        echo "Missing ${BIN_DIR}/$1, please run \"composer install\" first." >&2
        exit 1
    fi
}
