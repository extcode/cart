#!/usr/bin/env bash
#
# Lints all yaml files of the extension with yamllint, using
# Build/yamllint.yaml. Files ignored by .gitignore are skipped.
#
# yamllint is not installed via composer. It is provided by shell.nix, in CI it
# is installed via pipx.
#
# Usage:
#   Build/Scripts/lint-yaml.sh

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

if ! command -v yamllint > /dev/null; then
    echo "Missing yamllint, please install it first, e.g. \"pipx install yamllint\"." >&2
    exit 1
fi

yamllint --config-file Build/yamllint.yaml .
