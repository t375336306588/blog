<?php

namespace App\Page;

use App\Exception\NotFound;
use App\Layout\ILayout;

class Page
{
    protected $db;
    protected $layout;
    protected $template;
    protected $code;

    public function __construct($db, ILayout $layout, $template, $code = 200) {

        $this->db = $db;

        $this->layout = $layout;

        $this->template = $template;

        $this->code = $code;

    }

    public function render() {
        $this->layout->render($this->template, $this->code);
    }

    public function getLink()
    {
        return "#";
    }

}
