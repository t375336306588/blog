<?php

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use App\DB\Mysql;
use \Smarty\Smarty;

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

$smarty = new Smarty();

$smarty->setTemplateDir('../smarty/templates/');
$smarty->setCompileDir('../smarty/templates_c/');
$smarty->setCacheDir('../smarty/cache/');
$smarty->setConfigDir('../smarty/configs/');

$link = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

try {

    $db = new Mysql(
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    if ($link == "/") {
        $smarty->assign('categories', $db->getHomeCategories());
        $template = "home";
    } elseif (preg_match("#^/category/(\d+)$#u", $link, $matches)) {
        $smarty->assign('category', $db->getCategory($matches[1]));
        $template = "category";
    } else {
        $template = "404";
    }

    if (isset($_GET["seed"])) {

        unset($categories);
        unset($articles);

        $f = "../data/seed.php";

        if (file_exists($f)) {
            include $f;
        }

        $categoriesExist = isset($categories) && is_array($categories);

        if ($categoriesExist) {
            $db->createCategories($categories);
            echo "Categories created<br>";
            $categoriesLength = count($categories);
        }

        if (isset($articles) && is_array($articles)) {

            $fileId1 = $db->addFile("/files/1.jpg");
            $fileId2 = $db->addFile("/files/2.jpg");

            foreach ($articles as $article) {
                $a = $db->createArticle($article);
                $db->setViews(rand(1, 1000), $a->getId(), "article");
                $db->setAttachment($a->getId() % 2 == 0 ? $fileId1 : $fileId2, $a->getId(), "article");

                if ($categoriesExist) {
                    $randomCategoryIds = array_slice(range(1, $categoriesLength), 0, rand(1, $categoriesLength));
                    $db->setCategories($randomCategoryIds, $a->getId(), "article");
                }
            }

            echo "Articles created<br>";
        }

        exit;
    }



} catch (\PDOException $e) {
    $template = "500";
    $smarty->assign('error', $e);
}

$smarty->display($template . '.tpl');
