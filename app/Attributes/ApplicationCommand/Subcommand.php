<?php

namespace App\Attributes\ApplicationCommand;

use Attribute;

#[Attribute]
class Subcommand
{
    public function __construct(?string $name = '', string $description = '')
    {}
}
