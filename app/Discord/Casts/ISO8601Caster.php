<?php

namespace App\Discord\Casts;

use DateTime;
use DateTimeInterface;
use Spatie\DataTransferObject\Caster;

class ISO8601Caster implements Caster
{
    public function cast(mixed $value): DateTime
    {
        return new DateTime($value);
    }
}
