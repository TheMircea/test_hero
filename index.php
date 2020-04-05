<?php
require __DIR__ . '/vendor/autoload.php';

/**
 * Require helpers
 */
require_once(__DIR__ . '/app/envHelper.php');

use app\App;
use app\Session;

/**
 * Load application environment from .env file
 */
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    echo App::init('emag', [
        'localhost',
        '127.0.0.1'
    ]);

} catch (Exception $e) {
    echo $e->getMessage();
}
