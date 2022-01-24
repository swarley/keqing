<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class StringArg extends ChoicesArgument
{
    public const ARGUMENT_TYPE = 3;
}
