<?php

namespace App\Http\Controllers\ApplicationCommands;

use App\Attributes\ApplicationCommand;
use App\Attributes\ApplicationCommand\Subcommand;
use App\Attributes\ApplicationCommand\Arguments\StringArg;
use App\Attributes\ApplicationCommand\Autocomplete;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Autocomplete\DanbooruSearchController;
use App\Services\DanbooruService;

#[ApplicationCommand(
    name: 'danbooru',
    description: 'Find images on Danbooru.'
)]
class DanbooruController
{
    #[Subcommand(description: 'Search for a post')]
    #[Autocomplete(DanbooruSearchController::class)]
    #[StringArg(name: 'tags', description: 'Tags to search', required: true, autocomplete: true)]
    #[StringArg(
        name: 'rating',
        description: 'Rating to filter',
        choices: ['Explicit' => 'e', 'Questionable' => 'q', 'Safe' => 's'])
    ]
    public function search(Interaction $interaction, string $tags, ?string $rating = null): InteractionResponse
    {
        return DanbooruService::renderPost($interaction->response(), $tags, $rating, '0');
    }

    #[Subcommand(description: 'Show favorites')]
    public function favorites(Interaction $interaction): InteractionResponse
    {
        $userId = $interaction->user?->id ?? $interaction->member->user->id;

        return DanbooruService::renderFavorites($interaction->response(), $userId);
    }
}
