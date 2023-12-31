<?php

class Post extends Model
{
    public ?int $id = null;
    public ?string $body = null;
    public ?int $user_id = null;

    protected static array $required = ["id", "body", "user_id"]; 
    protected static array $optional = [];
}

