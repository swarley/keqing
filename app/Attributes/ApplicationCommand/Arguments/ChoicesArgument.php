<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;

class ChoicesArgument extends Argument
{
    public ?array $choices;

    public function __construct(string $name, string $description, bool $required = false, bool $autocomplete = false,
                                ?array $choices = null)
    {
        if ($choices && $autocomplete) {
            throw new \Exception('Cannot have autocomplete with choices.');
        }

        $this->choices = null;
        if ($choices) {
            $this->choices = [];

            foreach ($choices as $choiceName => $value) {
                $this->choices[] = ['name' => $choiceName, 'value' => $value];
            }
        }

        parent::__construct($name, $description, $required, $autocomplete);
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), ['choices' => $this->choices]);
    }
}
