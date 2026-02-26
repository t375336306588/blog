<?php

namespace App\Page\Creator;


use App\Exception\NotFound;
use App\Page\Category as Page;

class Category extends ICreator
{
    public function getById(int $id) {

        $data = $this->db->getCategoryData($id);

        if ($data) {
            return new Page($data, $this->db, $this->layout, "category.tpl");
        }

        throw new NotFound("Category not found!");

    }

}