<?php

namespace App\Discord;

class RowBuilder
{
    public const PRIMARY_BUTTON = 1;
    public const SECONDARY_BUTTON = 2;
    public const SUCCESS_BUTTON = 3;
    public const DANGER_BUTTON = 4;
    public const LINK_BUTTON = 5;

    public array $components;

    public function __constructor()
    {
        $this->components = [];
    }

    public function button(?string $label = null, int $style, ?string $customId = null, bool $disabled = false,
                           ?array $emoji = null, ?string $url = null): static
    {
        $this->components[] = array_filter([
            'type' => 2,
            'label' => $label,
            'style' => $style,
            'custom_id' => $customId,
            'url' => $url,
            'emoji' => $emoji,
            'disabled' => $disabled,
        ]);

        return $this;
    }

    public function primaryButton(?string $label = null, string $customId, bool $disabled = false, ?array $emoji = null): static
    {
        return $this->button($label, self::PRIMARY_BUTTON, $customId, $disabled, $emoji);
    }

    public function secondaryButton(?string $label = null, string $customId, bool $disabled = false, ?array $emoji = null): static
    {
        return $this->button($label, self::SECONDARY_BUTTON, $customId, $disabled, $emoji);
    }

    public function successButton(?string $label = null, string $customId, bool $disabled = false, ?array $emoji = null): static
    {
        return $this->button($label, self::SUCCESS_BUTTON, $customId, $disabled, $emoji);
    }

    public function dangerButton(?string $label = null, string $customId, bool $disabled = false, ?array $emoji = null): static
    {
        return $this->button($label, self::DANGER_BUTTON, $customId, $disabled, $emoji);
    }

    public function linkButton(?string $label = null, string $url, bool $disabled = false,
                               ?array $emoji = null): static
    {
        return $this->button($label, self::LINK_BUTTON, null, $disabled, $emoji, $url);
    }

    public function toArray(): array
    {
        return [
            'type' => 1,
            'components' => $this->components
        ];
    }
}
