<?php

namespace App\Attributes\ApplicationCommand\Arguments;

use App\Attributes\ApplicationCommand\Argument;
use Attribute;

#[Attribute]
class ChannelArg extends Argument
{
    public const ARGUMENT_TYPE = 7;

    public ?array $channelTypes;

    public function __construct(string $name, string $description, bool $required = false, bool $autocomplete = false,
                                ?array $channelTypes = null)
    {
        $this->channelTypes = $channelTypes;

        parent::__construct($name, $description, $required, $autocomplete);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'channel_types' => $this->channelTypes,
        ]);
    }
}
