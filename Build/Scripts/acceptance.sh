#!/usr/bin/env bash
#
# Runs the acceptance tests with codeception.
#
# Usage:
#   Build/Scripts/acceptance.sh [--keep-running] [test] [additional codecept run arguments]
#   Build/Scripts/acceptance.sh --stop
#
# The tests run in the "acceptance" suite, so the first argument can be a
# single Cest, e.g. "DashboardCest".
#
# Options:
#   --keep-running  Keep geckodriver and the php built-in webserver running
#                   after the tests, e.g. to log into the backend of the test
#                   instance (http://127.0.0.1:8080/typo3/, admin / password).
#   --stop          Stop geckodriver and the php built-in webserver started
#                   with --keep-running.
#
# Requirements:
#   - firefox and geckodriver available in PATH
#   - php with pdo_sqlite
#
# This script starts geckodriver and the php built-in webserver and stops
# both when it exits, even on failure. Only the processes started by this
# script are stopped, their process ids are stored in a file.

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

TESTS_PATH="${PROJECT_ROOT}/.Build/public/typo3temp/var/tests"
LOGS_PATH="${TESTS_PATH}/acceptance-logs"
PID_FILE="${LOGS_PATH}/processes.pid"
WEB_SERVER="127.0.0.1:8080"
GECKODRIVER_PORT="4444"

stopProcesses() {
    if [ ! -f "${PID_FILE}" ]; then
        return
    fi
    local pids
    pids="$(cat "${PID_FILE}")"
    rm -f "${PID_FILE}"
    for pid in ${pids}; do
        kill "${pid}" 2> /dev/null || true
    done
    for pid in ${pids}; do
        for _ in $(seq 1 50); do
            kill -0 "${pid}" 2> /dev/null || break
            sleep 0.1
        done
    done
}

isPortOpen() {
    (exec 3<> "/dev/tcp/127.0.0.1/$1") 2> /dev/null
}

waitForPort() {
    for _ in $(seq 1 50); do
        if isPortOpen "$1"; then
            return
        fi
        sleep 0.2
    done
    echo "$2 did not start, see ${LOGS_PATH}." >&2
    exit 1
}

KEEP_RUNNING=false
case "${1:-}" in
    --stop)
        stopProcesses
        exit 0
        ;;
    --keep-running)
        KEEP_RUNNING=true
        shift
        ;;
esac

requireBinary codecept

for tool in firefox geckodriver; do
    if ! command -v "${tool}" > /dev/null 2>&1; then
        echo "Missing \"${tool}\" in PATH, it is required for the acceptance tests." >&2
        exit 1
    fi
done

# Processes left over by a previous run with --keep-running.
stopProcesses

for port in "${WEB_SERVER##*:}" "${GECKODRIVER_PORT}"; do
    if isPortOpen "${port}"; then
        echo "Port ${port} is already in use, stop the process using it first." >&2
        exit 1
    fi
done

mkdir -p \
    "${TESTS_PATH}/acceptance" \
    "${LOGS_PATH}" \
    "${TESTS_PATH}/acceptance-reports" \
    "${TESTS_PATH}/acceptance-sqlite-dbs"

export INSTANCE_PATH="${TESTS_PATH}/acceptance"

cleanup() {
    if [ "${KEEP_RUNNING}" = true ]; then
        echo
        echo "The test instance is still running: http://${WEB_SERVER}/typo3/ (admin / password)"
        echo "Stop it with: $0 --stop"
        return
    fi
    stopProcesses
}
trap cleanup EXIT

geckodriver --port "${GECKODRIVER_PORT}" < /dev/null > "${LOGS_PATH}/geckodriver.log" 2>&1 &
echo $! >> "${PID_FILE}"

TYPO3_PATH_APP="${INSTANCE_PATH}" TYPO3_PATH_ROOT="${INSTANCE_PATH}" \
    "${PHP_BIN}" -S "${WEB_SERVER}" -t "${INSTANCE_PATH}" "${PROJECT_ROOT}/Build/Scripts/acceptance-router.php" < /dev/null > "${LOGS_PATH}/php.log" 2>&1 &
echo $! >> "${PID_FILE}"

waitForPort "${GECKODRIVER_PORT}" geckodriver
waitForPort "${WEB_SERVER##*:}" "The php built-in webserver"

runPhp "${BIN_DIR}/codecept" build
runPhp "${BIN_DIR}/codecept" run acceptance "$@"
