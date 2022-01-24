<?php

namespace App\Discord;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class InteractionOption extends DataTransferObject
{
    public string $name;
    public int $type;
    public mixed $value;
    public ?bool $focused;
    #[CastWith(ArrayCaster::class, InteractionOption::class)]
    public ?array $options;
}
