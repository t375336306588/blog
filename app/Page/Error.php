<?php

namespace App\Page;

use App\Layout\ILayout;

class Error extends Page
{

    public function __construct($db, ILayout $layout, $exception) {

        parent::__construct($db, $layout, "exception.tpl", $exception->getCode());

        $this->layout->setVar("exception", $exception);

    }

    public function getLink()
    {
        return $_SERVER["REQUEST_URI"];
    }

}
