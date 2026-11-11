#!/usr/bin/env bash
#
# Runs the static code analysis with phpstan.
#
# Usage:
#   Build/Scripts/phpstan.sh [additional phpstan arguments]

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

requireBinary phpstan

runPhp "${BIN_DIR}/phpstan" analyse -c Build/phpstan.neon "$@"
