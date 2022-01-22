<?php

namespace App\Discord;

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
}
