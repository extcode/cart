<?php

declare(strict_types=1);

$documentRoot = (string) $_SERVER['DOCUMENT_ROOT'];
$path = (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Existing files are delivered as they are, every other request is handled by
// the index.php of the test instance. Without this router the built-in
// webserver answers requests to directories without index.php, like /typo3/,
// with 404.
if ($path !== '/' && is_file($documentRoot . $path)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $documentRoot . '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

chdir($documentRoot);
require $documentRoot . '/index.php';
