<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Mild Framework (https://github.com/mildphp/mild)
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @since 2018
 * @package Mild Framework
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
define('ELAPSED', microtime(true));
/**
 * Register Autoloader
 */
require '../vendor/autoload.php';
/**
 * Run application
 */
new \App\Http\Kernel(new \App\Handlers\Handler(new Mild\App(dirname(__DIR__))));

echo number_format(microtime(true) - ELAPSED, 4);