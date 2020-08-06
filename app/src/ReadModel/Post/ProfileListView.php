<?php

declare(strict_types=1);

namespace App\ReadModel\Post;

class ProfileListView
{
    public string $id;

    public string $title;

    public string $status;

    public string $likes;

    public string $created;

    public ?string $published;
}
