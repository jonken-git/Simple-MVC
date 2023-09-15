<?php

class Comment extends Model
{
    public ?int $id = null;
    public ?string $body = null;
    public ?int $user_id = null;
    public ?int $post_id = null;

    protected static array $required = ["id", "content", "user_id", "post_id"]; 
    protected static array $optional = [];
}

