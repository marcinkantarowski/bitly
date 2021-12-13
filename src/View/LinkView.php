<?php

namespace App\View;

use App\Model\Link;

class LinkView
{

    private $content;

    /**
     * getting controller name (first part) and action name to return the proper view
     *
     * LinkView constructor.
     * @param Link $converter
     */
    public function __construct(Link $converter)
    {
        $this->converter = $converter;
        $tmp = debug_backtrace();
        $this->controller = str_replace("controller", "", strtolower($tmp[1]['class']));
        $this->controller = str_replace("app\\\\", "", $this->controller);
        $this->action = str_replace("action", "", strtolower($tmp[1]['function']));
    }

    /**
     * LinkView destructor
     */
    public function __destruct()
    {
        include '../src/View/Layout/layout.phtml';
    }

    /**
     * rendering the view
     *
     * @param null $variables
     */
    public function renderView($variables = null)
    {
        ob_start();
        require "../src/View/{$this->controller}/{$this->action}.phtml";
        $this->content = ob_get_clean();
    }
}