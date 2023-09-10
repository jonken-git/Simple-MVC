<?php

class User extends Model
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;

    protected static array $columns = ["id", "username", "email", "password"]; 
}
