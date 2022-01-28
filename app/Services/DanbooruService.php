<?php

namespace App\Services;

use App\Danbooru\Post;
use App\Discord\ComponentsBuilder;
use App\Discord\EmbedBuilder;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Discord\RowBuilder;
use App\Models\Favorite;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Log;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DanbooruService
{
    public static function renderPost(InteractionResponse $response, string $tags, ?string $rating, string $id): InteractionResponse
    {
        Log::info("Rendering post: $id");

        $post = null;
        $existsNext = true;

        /** @var ?Post $post */
        try {
            $post = Post::search($tags . ' -status:deleted -status:pending', $rating, 1, $id)->first();
        } catch (UnknownProperties $ex) {
            report ($ex);
        }

        if (!$post && !$id) {
            return $response->content("No results found for `$tags`");
        } else if (!$post) {
            $fallbackId = substr($id, 1, strlen($id) - 1);

            return $response
                ->components(fn(ComponentsBuilder $builder) =>
                    static::searchComponents($builder, $tags, $rating, $fallbackId, $id[0] == 'b', $id[0] == 'a')
                );
        }

        $ext = pathinfo($post->large_file_url, PATHINFO_EXTENSION);

        if (in_array($ext, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
            return $response
                ->embed(fn(EmbedBuilder $embed) => $embed
                    ->image($post->large_file_url)
                    ->author("Danbooru", "https://danbooru.donmai.us/posts/$post->id")
                    ->color(match ($post->rating[0]) {
                        's' => config('discord.safe_color'),
                        'q' => config('discord.questionable_color'),
                        'e' => config('discord.explicit_color'),
                        default => 0,
                    })
                    ->footer(text: "Post ID: $post->id")
                )
                ->components(fn(ComponentsBuilder $builder) =>
                    static::searchComponents($builder, $tags, $rating, $post->id)
                );
        }

        if (in_array($ext, ['mp4', 'webm'])) {
            return $response
                ->content($post->large_file_url)
                ->components(fn(ComponentsBuilder $builder) =>
                    static::searchComponents($builder, $tags, $rating, $post->id, withLink: true)
                );
        }

        return $response
            ->content("Can't display file of type `$ext`")
            ->components(fn(ComponentsBuilder $builder) =>
                static::searchComponents($builder, $tags, $rating, $post->id, withLink: true)
            );
    }

    public static function searchComponents(ComponentsBuilder $builder, ?string $tags, ?string $rating, ?string $id,
                    ?bool $forwardDisabled = false, ?bool $backDisabled = false, bool $withLink = false
    ): ComponentsBuilder
    {
        $data = [$tags, $rating, $id];

        $builder->row(fn (RowBuilder $row) =>
            $row
                ->primaryButton(
                    customId: encode_custom_id('danbooru.search', 'after', $data),
                    disabled: $backDisabled,
                    emoji: ['id' => config('danbooru.emoji.back_arrow')],
                )
                ->primaryButton(
                    customId: encode_custom_id('danbooru.search', 'before', $data),
                    disabled: $forwardDisabled,
                    emoji: ['id' => config('danbooru.emoji.next_arrow')],
                )
                ->dangerButton(
                    customId: encode_custom_id('danbooru.utility', 'remove'),
                    emoji: ['id' => config('danbooru.emoji.trash')]
                )
                ->secondaryButton(
                    customId: encode_custom_id('danbooru.utility', 'favorite', [$id]),
                    emoji: ['id' => config('danbooru.emoji.star')]
                )
        );

        if ($withLink) {
            $builder->row(fn (RowBuilder $row) =>
                $row->linkButton(
                    label: 'View On Danbooru',
                    url: "https://danbooru.donmai.us/posts/$id",
                )
            );
        }

        return $builder;
    }

    public static function renderFavorites(InteractionResponse $response, string $userId, ?string $encodedCursor = null): InteractionResponse
    {
        /** @var CursorPaginator $paginator */
        $paginator = Favorite::whereUserId($userId)->cursorPaginate(1, ['*'], 'favorites', $encodedCursor);
        $favorite = $paginator->first();

        if ($favorite === null) {
            return $response->content('User has no favorites.');
        }

        /** @var ?Post $post */
        $post = Post::find($favorite->post_id);

        $nextCursor = null;
        $previousCursor = null;

        if ($paginator->hasMorePages()) {
            $nextCursor = $paginator->nextCursor()->encode();
        }

        if (!$paginator->onFirstPage()) {
            $previousCursor = $paginator->previousCursor()->encode();
        }

        return $response
            ->embed(fn(EmbedBuilder $embed) => $embed
                ->author("Danbooru", "https://danbooru.donmai.us/posts/$favorite->id")
                ->image($post->large_file_url)
                ->color(match ($post->rating[0]) {
                    's' => config('discord.safe_color'),
                    'q' => config('discord.questionable_color'),
                    'e' => config('discord.explicit_color'),
                    default => 0,
                })
                ->footer(text: "Post ID: $post->id")
            )
            ->components(fn (ComponentsBuilder $builder) =>
                $builder->row(fn ($row) =>
                    $row
                        ->primaryButton(
                            customId: encode_custom_id('danbooru.favorites', 'back', [$previousCursor]),
                            disabled: !$previousCursor,
                            emoji: ['id' => config('danbooru.emoji.back_arrow')],
                        )
                        ->primaryButton(
                            customId: encode_custom_id('danbooru.favorites', 'next', [$nextCursor]),
                            disabled: !$nextCursor,
                            emoji: ['id' => config('danbooru.emoji.next_arrow')],
                        )
                        ->dangerButton(
                            customId: encode_custom_id('danbooru.utility', 'remove'),
                            emoji: ['id' => config('danbooru.emoji.trash')]
                        )
                        ->secondaryButton(
                            customId: encode_custom_id('danbooru.utility', 'favorite', [$post->id]),
                            emoji: ['id' => config('danbooru.emoji.star')]
                        )
                )
            );
    }

    public static function renderRandomPost(InteractionResponse $response, string $tags, string $rating = null): InteractionResponse
    {
        $tags = $tags . ($rating ? "rating:$rating" : '');
        $post = Post::random($tags);

        return $response
            ->embed(fn(EmbedBuilder $embed) => $embed
                ->author("Danbooru", "https://danbooru.donmai.us/posts/$post->id")
                ->image($post->large_file_url)
                ->color(match ($post->rating[0]) {
                    's' => config('discord.safe_color'),
                    'q' => config('discord.questionable_color'),
                    'e' => config('discord.explicit_color'),
                    default => 0,
                })
                ->footer(text: "Post ID: $post->id")
            )
            ->components(fn(ComponentsBuilder $builder) =>
                $builder->row(fn (RowBuilder $row) =>
                    $row->primaryButton(
                        customId: encode_custom_id('danbooru.random', 'random', [$tags]),
                        emoji: ['id' => config('danbooru.emoji.shuffle')],
                    )
                    ->dangerButton(
                        customId: encode_custom_id('danbooru.utility', 'remove'),
                        emoji: ['id' => config('danbooru.emoji.trash')]
                    )
                    ->secondaryButton(
                        customId: encode_custom_id('danbooru.utility', 'favorite', [$post->id]),
                        emoji: ['id' => config('danbooru.emoji.star')]
                    )
                )
            );
    }
}
