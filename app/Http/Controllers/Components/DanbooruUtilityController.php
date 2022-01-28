<?php

namespace App\Http\Controllers\Components;

use App\Attributes\Component;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Controller;
use App\Jobs\DeleteInteractionMessage;
use App\Models\Favorite;

#[Component(namespace: 'danbooru.utility')]
class DanbooruUtilityController extends Controller
{
    public function remove(Interaction $interaction): InteractionResponse
    {
        DeleteInteractionMessage::dispatch($interaction);

        return $interaction->response()->deferUpdate();
    }

    public function favorite(Interaction $interaction, string $id): InteractionResponse
    {
        $userId = $interaction->user?->id ?? $interaction->member->user->id;

        $favorite = Favorite::wherePostId($id)
            ->whereUserId($userId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return $interaction->response()->ephemeral()->content("Removed from favorites");
        }
        else {
            Favorite::create(['user_id' => $userId, 'post_id' => $id]);
            return $interaction->response()->ephemeral()->content("Added to favorites");
        }
    }
}
