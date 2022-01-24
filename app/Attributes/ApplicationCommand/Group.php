<?php

namespace App\Attributes\ApplicationCommand;

use Attribute;

#[Attribute]
class Group
{
    public function __construct(string $name, ?string $description)
    {}
}
