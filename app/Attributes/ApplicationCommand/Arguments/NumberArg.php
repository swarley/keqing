<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use Attribute;

#[Attribute]
class NumberArg extends ChoicesArgument
{

    public const ARGUMENT_TYPE = 10;

    public ?float $minValue;
    public ?float $maxValue;

    public function __construct(string $name, string $description, bool $required = false, bool $autocomplete = false,
                                ?array $choices = null, ?float $minValue = null, ?float $maxValue = null)
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
