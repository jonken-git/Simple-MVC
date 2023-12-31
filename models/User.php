<?php

/**
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 */
class User extends Model
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    // public ?array $posts = null;

    protected static array $required = ["username", "email", "password"]; 
    protected static array $optional = ["id"];

    public function getFullName(): string
    {
        return $this->username . " " . $this->email;
    }
}
