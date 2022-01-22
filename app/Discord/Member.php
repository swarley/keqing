<?php

namespace App\Discord;

use App\Discord\Casts\ISO8601Caster;
use DateTime;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class Member extends DataTransferObject
{
    public ?User $user;
    public ?string $nick;
    public ?string $avatar;
    public ?array $roles;
    #[CastWith(ISO8601Caster::class)]
    public ?DateTime $joined_at;
    #[CastWith(ISO8601Caster::class)]
    public ?DateTime $premium_since;
    public ?bool $deaf;
    public ?bool $mute;
    public ?bool $pending;
    public ?string $permissions;
    #[CastWith(ISO8601Caster::class)]
    public ?DateTime $communication_disabled_until;
}
