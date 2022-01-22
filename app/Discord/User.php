<?php

namespace App\Discord;

use Spatie\DataTransferObject\DataTransferObject;

class User extends DataTransferObject
{
    public string $id;
    public string $username;
    public string $discriminator;
    public ?string $avatar;
    public ?bool $bot;
    public ?bool $system;
    public ?bool $mfa_enabled;
    public ?string $banner;
    public ?int $accent_color;
    public ?string $locale;
    public ?bool $verified;
    public ?string $email;
    public ?int $flags;
    public ?int $premium_type;
    public ?int $public_flags;
}
