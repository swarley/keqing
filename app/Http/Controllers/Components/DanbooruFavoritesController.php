<?php

namespace App\Http\Controllers\Components;

use App\Attributes\Component;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Controller;
use App\Services\DanbooruService;

#[Component(namespace: 'danbooru.favorites')]
class DanbooruFavoritesController extends Controller
{
    public function next(Interaction $interaction, ?string $cursor = null): InteractionResponse
    {
        return DanbooruService::renderFavorites($interaction->response()->update(), $interaction->userId(), $cursor);
    }

    public function back(Interaction $interaction, ?string $cursor = null): InteractionResponse
    {
        return DanbooruService::renderFavorites($interaction->response()->update(), $interaction->userId(), $cursor);
    }
}
