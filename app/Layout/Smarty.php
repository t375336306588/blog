<?php

namespace App\Layout;

use Smarty\Smarty as S;

class Smarty extends ILayout
{
    protected $smarty;

    public function __construct(S $smarty) {
        $this->smarty = $smarty;
    }

    public function render($template, $code) {
        parent::render($template, $code);
        foreach ($this->vars as $n => $v) {
            $this->smarty->assign($n, $v);
        }
        $this->smarty->display($template);
    }


}