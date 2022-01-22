<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use Attribute;

#[Attribute]
class StringArg extends ChoicesArgument
{
    public const ARGUMENT_TYPE = 3;
}
