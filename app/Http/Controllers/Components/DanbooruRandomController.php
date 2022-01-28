<?php

namespace App\Http\Controllers\Components;

use App\Attributes\Component;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Controller;
use App\Services\DanbooruService;

#[Component(namespace: 'danbooru.random')]
class DanbooruRandomController extends Controller
{
    public function random(Interaction $interaction, string $tags): InteractionResponse
    {
        return DanbooruService::renderRandomPost($interaction->response()->update(), $tags);
    }
}
