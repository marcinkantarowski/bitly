<?php

namespace App\Controller;

use App\View\LinkView;

class LinkController implements ControllerInterface
{

    /**
     * @var
     */
    private $link;

    /**
     * LinkController constructor.
     * @param $link
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * @param $request
     */
    public function indexAction($request)
    {
        $View = new LinkView($this->link);
        $View->renderView($request);
    }

    /**
     * @param $request
     */
    public function viewAction($request)
    {
            $View = new LinkView($this->link);
            $View->renderView($request);
    }

    /**
     * @param $request
     */
    public function addlinksubmittedAction($request)
    {
        $res = null;
        $link = $request['link'];
        if(!strpos($request['link'],"http", 0)) {
            $link = "http://" . $request['link'];
        }
        // checking if it is a proper url
        preg_match('/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/\/=]*)/', $link, $output);
        if(isset($output[0]) && $output[0]) {
            $short = $this->generateShort();
            $res = $this->link->addLink($output[0], $short);
            $_SESSION['short'] = $short;
            $_SESSION['link'] = $output[0];
        }
        if ($res) {
            $this->redirectAction("/");
        }
        else {
            $this->redirectAction("/?action=index&error=error");
        }
    }

    /**
     * @param $request
     * @param null $route
     */
    public function externalredirectAction($request, $route = null)
    {
        //checking if the short code is in the database, redirecting or returning 404
        $short = ltrim($route, '/');
        if($link = $this->link->findLink($short)) {
            header("location: $link");
        } else {
            header("location: nolink");
        }
        exit;
    }

    /**
     * returning 404
     *
     * @param $request
     */
    public function nolinkAction($request)
    {
        $View = new LinkView($this->link);
        $View->renderView($request);
    }

    /**
     * @param string $route
     */
    public function redirectAction($route = "/")
    {
        header("location: $route");
        exit;
    }

    /**
     * simple generating the short
     * @return false|string
     */
    private function generateShort()
    {
        // generating the short code and making sure it is unique
        $unique = false;
        $charset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        do {
            $charset = str_shuffle($charset);
            $short = substr($charset, 0, 7);
            if($this->link->findLink($short)) {
                $unique = true;
            }
        }
        while($unique);

        return $short;
    }
}