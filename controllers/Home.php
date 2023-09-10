<?php
require_once(__CONTROLLERS__ . "Controller.php");

class Home extends Controller
{

    public function index()
    {
        $this->renderView(["title" => "Homepage"]);
    }

    public function sign_in(int $param = null)
    {
        $this->renderView();
    }

    public function sign_up()
    {
        $this->renderView();
    }

}