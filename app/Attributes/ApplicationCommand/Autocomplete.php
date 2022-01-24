<?php

namespace App\Attributes\ApplicationCommand;

use Attribute;

#[Attribute]
class Autocomplete
{
    public string $class;

    public function __construct(string $class)
    {}
}
