<?php

namespace App\Http\Controllers\Autocomplete;

use App\Discord\Interaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DanbooruSearchController extends Controller
{
    public function tags(Interaction $interaction, ?string $value = '')
    {
        $parts = collect(explode(' ', $value));
        $value = '';
        $tag = $parts->last();

        if (count($parts) > 1) {
            $parts->pop();
            $value = implode(' ', $parts->toArray());
        }

        $results = Http::get(
            "https://danbooru.donmai.us/autocomplete.json?search[query]=$tag&search[type]=tag_query&limit=10"
        )->json();
        $tags = [];

        foreach ($results as $result) {
            $tags[$value . ' ' . $result['label']] = $value . ' ' . $result['value'];
        }
        return $tags;
    }
}
