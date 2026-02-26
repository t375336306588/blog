<?php

namespace App\Page;

interface ICategoryDB {

    public function getChildrenCategories(int $id);

    public function getCategoryArticles(int $id, string $orderBy, string $orderType, int $offset, int $limit);

    public function countCategoryArticles(int $id);

}