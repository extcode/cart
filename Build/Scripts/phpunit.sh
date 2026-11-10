#!/usr/bin/env bash
#
# Runs the phpunit tests.
#
# Usage:
#   Build/Scripts/phpunit.sh [testsuite] [additional phpunit arguments]
#
# Without a testsuite every suite defined in Build/phpunit.xml.dist is run,
# each one in its own phpunit process. They cannot share a process, the
# functional bootstrap leaks global state into the unit tests. The first
# argument is taken as the testsuite name unless it starts with a dash.
#
# Environment variables used as overrides:
#   typo3DatabaseDriver  database driver to test against, defaults to "pdo_sqlite"

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

requireBinary phpunit

export typo3DatabaseDriver="${typo3DatabaseDriver:-pdo_sqlite}"

runSuite() {
    local suite="$1"
    shift

    runPhp "${BIN_DIR}/phpunit" \
        -c Build/phpunit.xml.dist \
        --testsuite "${suite}" \
        --display-warnings \
        --display-deprecations \
        --display-errors \
        --display-notices \
        "$@"
}

if [ $# -gt 0 ] && [ -n "$1" ] && [ "${1:0:1}" != "-" ]; then
    TESTSUITE="$1"
    shift
    runSuite "${TESTSUITE}" "$@"
    exit $?
fi

mapfile -t SUITES < <(runPhp "${BIN_DIR}/phpunit" -c Build/phpunit.xml.dist --list-suites \
    | sed -n 's/^ - \(.*\) ([0-9]\{1,\} tests\{0,1\})$/\1/p')

if [ ${#SUITES[@]} -eq 0 ]; then
    echo "No testsuites found in Build/phpunit.xml.dist." >&2
    exit 1
fi

STATUS=0
for suite in "${SUITES[@]}"; do
    echo "Running testsuite \"${suite}\" ..."
    runSuite "${suite}" "$@" || STATUS=$?
done

exit "${STATUS}"
