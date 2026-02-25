<?php

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use App\DB\Mysql;

$dotenv = Dotenv::createImmutable("..");

$dotenv->load();

if (mb_strtolower($_ENV['APP_DEBUG']) == 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set("display_errors",  0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

$smarty = new \Smarty\Smarty();

$smarty->setTemplateDir('../smarty/templates/');
$smarty->setCompileDir('../smarty/templates_c/');
$smarty->setCacheDir('../smarty/cache/');
$smarty->setConfigDir('../smarty/configs/');

$template = "index";

try {

    $db = new Mysql(
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );

    $smarty->assign('name', 'xxxx');

} catch (PDOException $e) {
    $template = "500";
    var_dump($e->getMessage());
}

$smarty->display($template . '.tpl');
