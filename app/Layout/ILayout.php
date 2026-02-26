<?php

namespace App\Layout;

abstract class ILayout
{
    protected $vars = [];

    public function setVar($name, $value) {
        $this->vars[$name] = $value;
    }

    public function render($template, $code) {
        http_response_code($code);
    }

}