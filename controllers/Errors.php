<?php
require_once(__CONTROLLERS__ . "Controller.php");

class Errors extends Controller
{
    public Request $request;

    public function index()
    {
        $this->renderView(["title" => "Hoppsan!"]);
    }

    public function not_found()
    {
        $this->renderView(["title" => "404"]);
    }

    public function bad_request()
    {
        $this->renderView(["title" => "400"]);
    }

}