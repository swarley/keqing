<?php

namespace App\Discord;

use Closure;
use Illuminate\Support\Facades\Log;

class InteractionResponse
{
    public const PONG = 1;
    public const CHANNEL_MESSAGE_WITH_SOURCE = 4;
    public const DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE = 5;
    public const DEFERRED_UPDATE_MESSAGE = 6;
    public const UPDATE_MESSAGE = 7;
    public const APPLICATION_COMMAND_AUTOCOMPLETE_RESULT = 8;

    public Interaction $interaction;
    public ?string $content;
    public int $flags;
    public int $type;
    public ?array $choices;
    public ?array $embeds;
    public ?array $components;
    public ?string $namespace;

    public function __construct(Interaction $interaction)
    {
        $this->interaction = $interaction;
        $this->namespace = $interaction->namespace();

        $this->flags = 0;
        $this->choices = null;
        $this->content = null;
        $this->type = self::CHANNEL_MESSAGE_WITH_SOURCE;
        $this->embeds = [];
        $this->components = null;
    }

    public function content(string $text): static
    {
        $this->content = $text;
        return $this;
    }

    public function ephemeral(bool $value = true): static
    {
        $this->flags |= ($value ? 1 << 6 : 0);
        return $this;
    }

    public function deferMessage(): static
    {
        $this->type = self::DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE;
        return $this;
    }

    public function deferUpdate(): static
    {
        $this->type = self::DEFERRED_UPDATE_MESSAGE;
        return $this;
    }

    public function update(): static
    {
        $this->type = self::UPDATE_MESSAGE;
        return $this;
    }

    public function pong(): static
    {
        $this->type = self::PONG;
        return $this;
    }

    public function results(array $results): static
    {
        $this->type = self::APPLICATION_COMMAND_AUTOCOMPLETE_RESULT;
        $this->choices = [];

        foreach ($results as $name => $value) {
            $this->choices[] = ['name' => $name, 'value' => $value];
        }

        return $this;
    }

    public function embed(Closure|EmbedBuilder $callback): static
    {
        if ($callback instanceof \Closure) {
            $this->embeds[] = $callback(new EmbedBuilder())->toArray();
        }
        else {
            $this->embeds[] = $callback->toArray();
        }

        return $this;
    }

    public function components(Closure $callback): static
    {
        $this->components = $callback(new ComponentsBuilder())->toArray();
        return $this;
    }

    public function getTypeName(): string
    {
        return match ($this->type) {
            self::PONG => 'PONG',
            self::CHANNEL_MESSAGE_WITH_SOURCE => 'CHANNEL_MESSAGE_WITH_SOURCE',
            self::DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE => 'DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE',
            self::DEFERRED_UPDATE_MESSAGE => 'DEFERRED_UPDATE_MESSAGE',
            self::UPDATE_MESSAGE => 'UPDATE_MESSAGE',
            self::APPLICATION_COMMAND_AUTOCOMPLETE_RESULT => 'APPLICATION_COMMAND_AUTOCOMPLETE_RESULT',
            default => "UNKNOWN ($this->type)",
        };
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'data' => in_array($this->type, [self::UPDATE_MESSAGE, self::CHANNEL_MESSAGE_WITH_SOURCE])
                ? array_filter([
                    'content'  => $this->content,
                    'flags' => $this->flags,
                    'choices' => $this->choices,
                    'embeds' => $this->embeds,
                    'components' => $this->components,
                ])
                : null,
        ]);
    }
}
