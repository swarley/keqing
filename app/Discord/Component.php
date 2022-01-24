<?php

namespace App\Discord;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class Component extends DataTransferObject
{
    public int $type;
    public ?string $custom_id;
    public ?bool $disabled;
    public ?int $style;
    public ?string $label;
    public ?array $emoji;
    public ?string $url;
    public ?array $options;
    public ?string $placeholder;
    public ?int $min_values;
    public ?int $max_values;
    #[CastWith(ArrayCaster::class, Component::class)]
    public ?array $components;
}
