#!/usr/bin/env bash
#
# Checks the spelling of all files of the extension with typos, using
# Build/typos.toml. Files ignored by .gitignore are skipped.
#
# typos is not installed via composer. It is provided by shell.nix, in CI it
# is installed via pipx.
#
# Usage:
#   Build/Scripts/lint-typos.sh         report typos
#   Build/Scripts/lint-typos.sh --fix   fix unambiguous typos in place

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

if ! command -v typos > /dev/null; then
    echo "Missing typos, please install it first, e.g. \"pipx install typos\"." >&2
    exit 1
fi

if [ "${1:-}" = "--fix" ]; then
    typos --config Build/typos.toml --write-changes
else
    typos --config Build/typos.toml
fi
