<?php

namespace App\Danbooru;

use Illuminate\Support\Facades\Http;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class Artist extends DataTransferObject
{
    public int $id;
    public string $name;
    #[CastWith(ArrayCaster::class)]
    public array $other_names;

    public static function search(string $name): ?static
    {
        $data = Http::get("https://danbooru.donmai.us/artists.json?search[name]=$name")->json('0');

        if (!$data) {
            return null;
        }

        return new static($data);
    }
}
