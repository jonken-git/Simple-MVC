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
        $user = User::with("comment", "user_id", $id)::with("post", "user_id", $id)::where("id", $id)::get();
        $user = User::where("username", "admin")::with("comment", "user_id", 2)::get(true);
        dd($user);
        $this->renderView(["user" => $user, "title" => "Profile"]);
    }

    public function list(): void
    {
        $users = User::join("post", "user_id")::join("comment", "post_id", "post", "id")::where("user.id", 1)::get();
        
        $this->renderView(["title" => "Userlist", "users" => $users]);
    }
    public function my_profile() : void
    {
        $user = User::find(1);
        $this->renderView(["user" => $user, "title" => "My profile"]);
    }

}
