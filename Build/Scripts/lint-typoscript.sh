#!/usr/bin/env bash
#
# Lints all TypoScript and TSconfig files of the extension with
# helmich/typo3-typoscript-lint, using Build/typoscriptlint.yaml.
# Warnings fail the lint as well.
#
# Usage:
#   Build/Scripts/lint-typoscript.sh

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

requireBinary typoscript-lint

paths=()
for path in Configuration Tests ext_typoscript_setup.typoscript; do
    if [ -e "${path}" ]; then
        paths+=("${path}")
    fi
done

runPhp "${BIN_DIR}/typoscript-lint" --config Build/typoscriptlint.yaml --fail-on-warnings "${paths[@]}"
