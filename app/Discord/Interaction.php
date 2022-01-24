<?php

namespace App\Discord;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\DataTransferObject\DataTransferObject;

class Interaction extends DataTransferObject
{
    public string $id;
    public ?string $name;
    public string $application_id;
    public int $type;
    public ?InteractionData $data;
    public ?string $guild_id;
    public ?string $channel_id;
    public ?Member $member;
    public ?User $user;
    public string $token;
    public int $version;
    public ?array $message;
    public ?string $locale;
    public ?string $guild_locale;

    public function response(): InteractionResponse
    {
        return new InteractionResponse($this);
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function group(): ?string
    {
        $firstOption = collect($this->data->options)->first();

        if ($firstOption->type === 2) {
            return $firstOption->name;
        }

        return null;
    }

    public function subcommand(): ?string
    {
        $firstOption = collect($this->data->options)->first();

        return match ($firstOption->type) {
            1 => $firstOption->name,
            2 => collect($firstOption->options)->first()->name,
            default => null,
        };
    }

    public function namespace(): string
    {
        if ($this->type === 2) {
            return implode('.', array_filter([$this->name(), $this->group(), $this->subcommand()]));
        }

        if ($this->type === 3) {
            return collect(explode("\0", $this->data->custom_id))->first();
        }

        return '';
    }

    public function userId(): string
    {
        return $this->user?->id ?? $this->member->user->id;
    }

    public function focusedArgument(): ?InteractionOption
    {
        $options = $this->data->options;

        if (($options[0] ?? null)?->type === 2) {
            $options = $options[0]->options;
        }

        if (($options[0] ?? null)?->type === 1) {
            $options = $options[0]->options;
        }

        return collect($options)->first(fn ($opt) => $opt->focused === true);
    }
}
