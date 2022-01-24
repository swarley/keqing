<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE)]
class RoleArg extends Argument
{
    public const ARGUMENT_TYPE = 8;
}
