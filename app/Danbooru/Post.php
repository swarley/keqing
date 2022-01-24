<?php

namespace App\Danbooru;

use App\Discord\Casts\ISO8601Caster;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class Post extends DataTransferObject
{
    public const FIELDS = [
        'id', 'file_url', 'rating', 'file_ext'
    ];

    public int $id;
    public string $file_url;
    public string $file_ext;
    public string $rating;

    public const SEARCH_URL = 'https://danbooru.donmai.us/posts.json';

    public static function search(string $tags, ?string $rating = null, int $limit = 1, string $page = null): Collection
    {
        $tags = explode(' ', $tags);

        if (!empty($rating)) {
            $tags[] = "rating:$rating";
        }

        $query = "?limit=$limit&page=$page&tags=" . implode('+', $tags);
        $resp = Http::get(self::SEARCH_URL . $query);

        if (!$resp->ok()) {
            if ($resp->status() !== 404) {
                Log::error('Danbooru error', $resp->json());
            }
            return collect([]);
        }

        $postsArray = $resp->json();
        $posts = [];

        foreach ($postsArray as $post) {
            try {
                $posts[] = new static($post);
            } catch (UnknownProperties $ex) {
                report($ex);
                Log::error('Failed to load post DTO', $post);
            }
        }

        return collect($posts);
    }

    public static function find(string $id): ?Post
    {
        $resp = Http::get("https://danbooru.donmai.us/posts/$id.json");

        if (!$resp->ok()) {
            if ($resp->status() !== 404) {
                Log::error('Danbooru error', $resp->json());
            }
            return null;
        }

        try {
            return new static($resp->json());
        } catch (UnknownProperties $ex) {
            report($ex);
            Log::error('Failed to load post DTO', $resp->json());
            return null;
        }
    }
}
