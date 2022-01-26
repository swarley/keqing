<?php

namespace App\Danbooru;

use Illuminate\Support\Facades\Http;
use Spatie\DataTransferObject\DataTransferObject;

class Wiki extends DataTransferObject
{
    public int $id;
    public string $title;
    public string $body;

    public static function findForTag(string $name): ?static
    {
        $page = Http::get("https://danbooru.donmai.us/tags.json?search[name]=$name&only=wiki_page")->json('0.wiki_page');

        if (!$page) {
            return null;
        }

        return new static($page);
    }
}
