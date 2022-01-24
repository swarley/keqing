<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE)]
class UserArg extends Argument
{
    const ARGUMENT_TYPE = 6;
}
