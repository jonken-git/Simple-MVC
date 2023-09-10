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
        $user = User::find($id);
        $post = Post::find(1); 
        dd([$user, $post]);
        $this->renderView(["user" => $user, "title" => "Profile"]);
    }

    public function list(): void
    {
        $this->renderView([
            "title" => "Userlist",
            "users" => ["Jon", "Ken", "Doe"]
        ]);
    }
    public function my_profile(string $name) : void
    {
        // $this->renderView(["user" => $user, "title" => "My profile"]);
    }

}
