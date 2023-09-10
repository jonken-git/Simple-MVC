<?php

class Post extends Model
{
    public ?int $id = null;
    public ?string $body = null;

    protected static array $columns = ["id", "body"]; 
}

