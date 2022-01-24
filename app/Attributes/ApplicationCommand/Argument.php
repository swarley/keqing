<?php

namespace App\Attributes\ApplicationCommand;

use Attribute;
use Illuminate\Support\Str;

#[Attribute(Attribute::IS_REPEATABLE)]
class Argument
{
    public int $type;
    public string $name;
    public string $description;
    public bool $required;
    public bool $autocomplete;

    const ARGUMENT_TYPE = 0;

    public function __construct(string $name, string $description, bool $required = false, bool $autocomplete = false)
    {
        $this->type = static::ARGUMENT_TYPE;
        $this->name = $name;
        $this->description = $description;
        $this->required = $required;
        $this->autocomplete = $autocomplete;
    }

    public function toArray() {
        return [
            'type' => $this->type,
            'name' => Str::kebab($this->name),
            'description' => $this->description,
            'required' => $this->required,
        ];
    }
}
