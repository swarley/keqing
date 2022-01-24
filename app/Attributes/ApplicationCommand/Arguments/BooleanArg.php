<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_FUNCTION)]
class BooleanArg extends Argument
{
    public const ARGUMENT_TYPE = 5;
}
