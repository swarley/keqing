<?php

namespace App\Http\Controllers\ApplicationCommands;

use App\Attributes\ApplicationCommand;
use App\Attributes\ApplicationCommand\Subcommand;
use App\Danbooru\Post;
use App\Discord\ComponentsBuilder;
use App\Discord\EmbedBuilder;
use App\Attributes\ApplicationCommand\Arguments\StringArg;
use App\Attributes\ApplicationCommand\Group;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Discord\RowBuilder;
use App\Services\DanbooruService;

#[ApplicationCommand(
    name: 'danbooru',
    description: 'Find images on Danbooru.'
)]
class DanbooruController
{
    public const SAFE_COLOR = 0x00FF00;
    public const QUESTIONABLE_COLOR = 0xFFFF00;
    public const EXPLICIT_COLOR = 0xFF0000;

    #[Subcommand(description: 'Search for a post')]
    #[StringArg(name: 'tags', description: 'Tags to search', required: true)]
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
