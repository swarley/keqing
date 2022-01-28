<?php

namespace App\Http\Controllers\ApplicationCommands;

use App\Attributes\ApplicationCommand;
use App\Attributes\ApplicationCommand\Subcommand;
use App\Attributes\ApplicationCommand\Arguments\StringArg;
use App\Attributes\ApplicationCommand\Autocomplete;
use App\Danbooru\Artist;
use App\Danbooru\Post;
use App\Danbooru\Wiki;
use App\Discord\ComponentsBuilder;
use App\Discord\EmbedBuilder;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Autocomplete\DanbooruArtistController;
use App\Http\Controllers\Autocomplete\DanbooruSearchController;
use App\Http\Controllers\Autocomplete\DanbooruWikiController;
use App\Services\DanbooruService;
use Illuminate\Support\Facades\Http;

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

    #[Subcommand(description: "See information about a tag")]
    #[Autocomplete(DanbooruWikiController::class)]
    #[StringArg(name: 'tag', description: 'The tag to look up.', required: true, autocomplete: true)]
    public function wiki(Interaction $interaction, string $tag): InteractionResponse
    {
        $consequentTag = Http::get("https://danbooru.donmai.us/tag_aliases.json?search[name_matches]=$tag")->json('0.consequent_name');

        if (!$consequentTag) {
            return $interaction->response()
                ->ephemeral()
                ->content("Unable to find tag `$tag`.");
        }

        $wiki = Wiki::findForTag($consequentTag);

        if (!$wiki) {
            return $interaction->response()
                ->ephemeral()
                ->content("No wiki page for `$consequentTag`.");
        }

        $posts = Post::search($consequentTag, null, 4);

        $response = $interaction
            ->response()
            ->embed(fn (EmbedBuilder $builder) =>
                $builder
                    ->url("https://danbooru.donmai.us/wiki_pages/$wiki->title")
                    ->title($wiki->title)
                    ->description(dtext_to_markdown($wiki->body))
                    ->footer(text: "Wiki ID: $wiki->id")
            )
            ->components(fn (ComponentsBuilder $builder) =>
                $builder->row(fn ($row) =>
                    $row->dangerButton(
                        customId: encode_custom_id('danbooru.utility', 'remove'),
                        emoji: ['id' => config('danbooru.emoji.trash')]
                    )
                )
            );

        $posts->each(fn (Post $post) => $response
            ->embed(fn (EmbedBuilder $builder) =>
                $builder
                    ->url("https://danbooru.donmai.us/wiki_pages/$wiki->title")
                    ->image($post->large_file_url)
            )
        );

        return $response;
    }

    #[Subcommand(description: "Find random posts.")]
    #[Autocomplete(DanbooruSearchController::class)]
    #[StringArg(name: 'tags', description: 'Tags to search', required: false, autocomplete: true)]
    #[StringArg(
        name: 'rating',
        description: 'Rating to filter',
        choices: ['Explicit' => 'e', 'Questionable' => 'q', 'Safe' => 's'])
    ]
    public function random(Interaction $interaction, string $tags = '', string $rating = null): InteractionResponse
    {
        return DanbooruService::renderRandomPost($interaction->response(), $tags, $rating);
    }
}
