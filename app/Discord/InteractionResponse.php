<?php

namespace App\Discord;

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

    public function __construct(Interaction $interaction)
    {
        $this->interaction = $interaction;
        $this->flags = 0;
        $this->choices = null;
        $this->content = null;
        $this->type = self::CHANNEL_MESSAGE_WITH_SOURCE;
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
        return [
            'type' => $this->type,
            'data' => array_filter([
                'content'  => $this->content,
                'flags' => $this->flags,
                'choices' => $this->choices,
            ]),
        ];
    }
}
