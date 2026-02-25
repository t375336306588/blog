<?php

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable("..");

$dotenv->load();

ini_set("display_errors", mb_strtolower($_ENV['APP_DEBUG']) == 'true' ? true : false);

$smarty = new \Smarty\Smarty();

$smarty->setTemplateDir('../smarty/templates/');

$smarty->setCompileDir('../smarty/templates_c/');

$smarty->setCacheDir('../smarty/cache/');

$smarty->setConfigDir('../smarty/configs/');

$smarty->assign('name', 'Мир');

$smarty->display('index.tpl');