<?php

namespace App\Http\Controllers\Components;

use App\Attributes\Component;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Controller;
use App\Jobs\DeleteInteractionMessage;
use App\Models\Favorite;
use App\Services\DanbooruService;

#[Component(namespace: 'danbooru.search')]
class DanbooruSearchController extends Controller
{
    public function before(Interaction $interaction, string $tags, string $rating, string $id): InteractionResponse
    {
        return DanbooruService::renderPost($interaction->response()->update(), $tags, $rating, "b$id");
    }

    public function after(Interaction $interaction, string $tags, string $rating, string $id): InteractionResponse
    {
        return DanbooruService::renderPost($interaction->response()->update(), $tags, $rating, "a$id");
    }
}
