<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE)]
class IntegerArg extends ChoicesArgument
{
    public const ARGUMENT_TYPE = 4;

    public ?int $minValue;
    public ?int $maxValue;

    public function __construct(string $name, string $description, bool $required = false, bool $autocomplete = false,
                                ?array $choices = null, ?int $minValue = null, ?int $maxValue = null)
    {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;

        parent::__construct($name, $description, $required, $autocomplete, $choices);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'min_value' => $this->minValue,
            'max_value' => $this->maxValue,
        ]);
    }
}
