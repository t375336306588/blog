<?php

require_once '../vendor/autoload.php';

use App\Exception\NotFound;
use Dotenv\Dotenv;
use App\DB\Mysql;
use Smarty\Smarty;
use App\Layout\Smarty as Layout;
use App\Page\Home;
use App\Page\Error;
use App\Page\Creator\Article as AC;
use App\Page\Creator\Category as CC;

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

$smarty->setTemplateDir('../html/smarty/templates/');
$smarty->setCompileDir('../html/smarty/templates_c/');
$smarty->setCacheDir('../html/smarty/cache/');
$smarty->setConfigDir('../html/smarty/configs/');


$layout = new Layout($smarty);

$db = new Mysql(
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);

$articleCreator = new AC($db, $layout);
$categoryCreator = new CC($db, $layout);

$db->setArticleCreator($articleCreator);
$db->setCategoryCreator($categoryCreator);

if (isset($_GET["seed"])) {

    unset($categories);
    unset($articles);

    $fs = [
        "../data/seed/categories.php",
        "../data/seed/articles.php",
    ];

    foreach ($fs as $f) {
        if (file_exists($f)) {
            include $f;
        }
    }

    $categoriesExist = isset($categories) && is_array($categories) && count($categories);

    if ($categoriesExist) {
        $db->createCategories($categories);

        $categoriesLength = count($categories);

        $d = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        for ($i = 1; $i <= $categoriesLength; $i++) {
            $db->updateRow("description", "Description #$i: $d", "categories", $i);
        }

        echo "Categories created<br>";
    }

    if (isset($articles) && is_array($articles) && count($articles)) {

        $fileId1 = $db->addFile("/files/1.jpg");
        $fileId2 = $db->addFile("/files/2.jpg");

        foreach ($articles as $index => $article) {

            if ($index % 5 == 0) {
                sleep(4);
            }

            $a = $db->createArticle($article);
            $db->setViews(rand(1, 1000), $a->getId(), "article");
            $db->setAttachment($a->getId() % 2 == 0 ? $fileId1 : $fileId2, $a->getId(), "article");

            if ($categoriesExist) {
                $randomCategoryIds = array_slice(range(3, $categoriesLength), 0, rand(1, $categoriesLength));
                $db->setCategories($randomCategoryIds, $a->getId(), "article");
            }
        }

        echo "Articles created<br>";
    }

    exit;
}


$link = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

try {

    if ($link == "/") {

        $page = new Home($db, $layout);

    } else {

        if (preg_match("#^/category/(\d+)$#u", $link, $matches)) {
            $page = $categoryCreator->getById($matches[1]);
        } elseif (preg_match("#^/article/(\d+)$#u", $link, $matches)) {
            $page = $articleCreator->getById($matches[1]);
        } else {
            throw new NotFound("Page not found!");
        }

    }

} catch (\Exception $exception) {
    $page = new Error($db, $layout, $exception);
}

$page->render();
