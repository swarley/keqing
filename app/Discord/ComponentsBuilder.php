<?php

namespace App\Discord;

class ComponentsBuilder
{
    public array $components;

    public function __construct()
    {
        $this->components = [];
    }

    public function row($callback): static
    {
        $this->components[] = $callback(new RowBuilder())->toArray();
        return $this;
    }

    public function toArray(): array
    {
        return $this->components;
    }
}
