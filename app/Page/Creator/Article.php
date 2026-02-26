<?php

namespace App\Page\Creator;


use App\Exception\NotFound;
use App\Page\Article as Page;

class Article extends ICreator
{
    public function getById(int $id) {

        $data = $this->db->getArticleData($id);

        if ($data) {
            return new Page($data, $this->db, $this->layout, "article.tpl");
        }

        throw new NotFound("Article not found!");

    }

}