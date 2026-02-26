<?php

namespace App\Page;

use App\Layout\ILayout;

class Home extends Page
{

    public function __construct($db, ILayout $layout) {

        parent::__construct($db, $layout, "home.tpl");

        $this->layout->setVar("categories", $this->db->getHomeCategories());

    }

    public function getLink()
    {
        return "/";
    }

}
