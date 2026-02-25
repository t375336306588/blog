<?php

ini_set("display_errors", 1);

require_once '../vendor/autoload.php';

$smarty = new \Smarty\Smarty();

$smarty->setTemplateDir('../smarty/templates/');

$smarty->setCompileDir('../smarty/templates_c/');

$smarty->setCacheDir('../smarty/cache/');

$smarty->setConfigDir('../smarty/configs/');

$smarty->assign('name', 'Мир');

$smarty->display('index.tpl');