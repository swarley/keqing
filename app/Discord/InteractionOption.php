<?php

namespace App\Discord;

use Spatie\DataTransferObject\DataTransferObject;

class InteractionOption extends DataTransferObject
{
    public string $name;
    public int $type;
    public mixed $value;
    public ?bool $focused;
}
