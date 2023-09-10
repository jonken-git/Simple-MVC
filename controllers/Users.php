<?php
require_once(__CONTROLLERS__ . "Controller.php");

class Users extends Controller
{

    public function index() : void
    {
        $this->renderView();
    }

    public function profile(int $id) : void
    {
        $user = new class {
            public int $id;
            public string $username;
            function __construct()
            {
                $this->id = 1;
                $this->username = "JonKen";
            }
        };
        $user->id = $id;
        $this->renderView(["user" => $user, "title" => "Profile"]);
    }

    public function my_profile(string $name) : void
    {
        // $this->renderView(["user" => $user, "title" => "My profile"]);
    }

}
