<?php

namespace App\DB;

interface IResourceCategories {

    public function getChildrenCategories($id);

    public function getParentCategory($id);

    public function getCategoryLatestArticles($id, $limit);

}