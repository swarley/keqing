<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE)]
class MentionableArg extends Argument
{
    public const ARGUMENT_TYPE = 9;
}
