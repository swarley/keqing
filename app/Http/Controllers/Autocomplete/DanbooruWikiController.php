<?php

namespace App\Http\Controllers\Autocomplete;

use App\Discord\Interaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DanbooruWikiController extends Controller
{
    public function tag(Interaction $interaction, ?string $value = '')
    {
        $results = Http::get(
            "https://danbooru.donmai.us/autocomplete.json?search[query]=$value&search[type]=wiki_page&limit=10"
        )->json();
        $pages = [];

        foreach ($results as $result) {
            $pages[$result['value']] = $result['value'];
        }

        return $pages;
    }
}
