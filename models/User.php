<?php

class User extends Model
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    // public ?array $posts = null;

    protected static array $required = ["username", "email", "password"]; 
    protected static array $optional = ["id"];

    public static function selectWithPosts(int $id): User
    {
        return User::where("user.id = 1")
            ::join("post", "user_id")
            ::join("comment", "post_id", "post", "id")
            ::get();
    }
}
