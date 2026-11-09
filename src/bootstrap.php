<?php

define('APP_ROOT', dirname(__DIR__));

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
$parts = explode('/', trim($scriptName, '/'));
$publicIndex = array_search('public', $parts, true);
if ($publicIndex !== false) {
    $baseParts = array_slice($parts, 0, $publicIndex + 1);
    define('BASE_URL', '/' . implode('/', $baseParts) . '/');
} else {
    define('BASE_URL', '/');
}

require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/src/models.php';
require_once APP_ROOT . '/src/auth.php';

define('XSS_PROTECTION_LEVEL', $config['xss_protection'] ?? 'on');

function xss_escape($value, $level = null) {
	if ($level === null) {
		$level = XSS_PROTECTION_LEVEL;
	}
	if ($level === 'off') {
		return $value;
	}
	return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if (XSS_PROTECTION_LEVEL === 'max' && !headers_sent()) {
	header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; frame-ancestors 'none';");
}
