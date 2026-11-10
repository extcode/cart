#!/usr/bin/env bash
#
# Lints all php files of the extension for syntax errors.
#
# Usage:
#   Build/Scripts/lint-php.sh

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

find ./*.php Classes Configuration Tests -name '*.php' -print0 \
    | xargs -0 -n 1 -P 4 "${PHP_BIN}" -l
