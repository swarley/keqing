<?php

namespace App\Discord;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class InteractionData extends DataTransferObject
{
    public string $id;
    public string $name;
    public int $type;
    public ?array $resolved;
    #[CastWith(ArrayCaster::class, itemType: InteractionOption::class)]
    public ?array $options;
    public ?string $custom_id;
    public ?string $component_type;
    public ?array $values;
    public ?string $target_id;
}
