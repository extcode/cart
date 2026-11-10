#!/usr/bin/env bash
#
# Checks the coding guidelines with php-cs-fixer.
#
# Usage:
#   Build/Scripts/cgl.sh          check only, fails on violations
#   Build/Scripts/cgl.sh --fix    fix the violations in place

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

requireBinary php-cs-fixer

FIXER_ARGS=(--config=Build/.php-cs-fixer.dist.php --using-cache=no)
if [ "${1:-}" = "--fix" ]; then
    shift
else
    FIXER_ARGS+=(-v --dry-run --diff)
fi

runPhp "${BIN_DIR}/php-cs-fixer" fix "${FIXER_ARGS[@]}" "$@"
