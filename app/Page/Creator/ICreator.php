<?php

namespace App\Page\Creator;

abstract class ICreator
{
    protected $db;
    protected $layout;

    public function __construct($db, $layout) {
        $this->db = $db;
        $this->layout = $layout;
    }

    abstract public function getById(int $id);

}