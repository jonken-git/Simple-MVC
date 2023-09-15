<?php
require_once(__CONTROLLERS__ . "Controller.php");

class Users extends Controller
{

    public function index() : void
    {
        $user = User::selectWithPosts(1);
        dd($user);
        $this->renderView(["title" => "Users"]);
    }

    public function profile(int $id) : void
    {
        $user = User::find($id);
        $user->posts = Post::where("user_id = $id")->get();
        $this->renderView(["user" => $user, "title" => "Profile"]);
    }

    public function list(): void
    {
        $users = User::all();
        $this->renderView(["title" => "Userlist", "users" => $users]);
    }
    public function my_profile() : void
    {
        $user = User::find(1);
        $this->renderView(["user" => $user, "title" => "My profile"]);
    }

}
