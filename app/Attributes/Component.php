<?php

namespace App\Attributes;

use Attribute;

#[Attribute]
class Component
{
    public function __construct(string $namespace)
    {}
}
