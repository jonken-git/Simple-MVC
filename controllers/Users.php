<?php
require_once(__CONTROLLERS__ . "Controller.php");

class Users extends Controller
{

    public function index() : void
    {
        $this->renderView(["title" => "Users"]);
    }

    public function profile(int $id) : void
    {
        $user = User::with("comment", "user_id", $id)::with("post", "user_id", $id)::where("id", $id)::get(true);
        $this->renderView(["user" => $user, "title" => "Profile"]);
    }

    public function list(): void
    {
        $users = User::with("post", "user_id")::get();
        
        $this->renderView(["title" => "Userlist", "users" => $users]);
    }
    public function my_profile() : void
    {
        $user = User::find(1);
        $this->renderView(["user" => $user, "title" => "My profile"]);
    }

}
