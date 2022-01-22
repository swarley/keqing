<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;
use Attribute;

#[Attribute]
class RoleArg extends Argument
{
    public const ARGUMENT_TYPE = 8;
}
