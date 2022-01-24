<?php

namespace App\Discord;

class EmbedBuilder
{
    public ?string $title;
    public ?string $description;
    public ?string $url;
    public ?int $color;
    public ?DateTime $timestamp;
    public ?array $footer;
    public ?array $thumbnail;
    public ?array $image;
    public ?array $author;
    public ?array $fields;

    public function __construct(...$data)
    {
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->color = $data['color'] ?? null;
        $this->timestamp = $data['timestamp'] ?? null;
        $this->footer = $data['footer'] ?? null;
        $this->thumbnail = $data['thumbnail'] ?? null;
        $this->image = $data['image'] ?? null;
        $this->author = $data['author'] ?? null;
        $this->fields = $data['fields'] ?? null;
    }

    public function title(string $text): static
    {
        $this->title = $text;
        return $this;
    }

    public function description(string $text): static
    {
        $this->description = $text;
        return $this;
    }

    public function url(string $text): static
    {
        $this->url = $text;
        return $this;
    }

    public function color(int $num): static
    {
        $this->color = $num;
        return $this;
    }

    public function timestamp(DateTime $time): static
    {
        $this->timestamp = $time;
        return $this;
    }


    public function footer(?string $iconUrl = null, ?string $text): static
    {
        $this->footer = ['icon_url' => $iconUrl, 'text' => $text];
        return $this;
    }

    public function thumbnail(string $url): static
    {
        $this->thumbnail = ['url' => $url];
        return $this;
    }

    public function image(string $url): static
    {
        $this->image = ['url' => $url];
        return $this;
    }

    public function author(?string $name, ?string $url = null, ?string $iconUrl = null): static
    {
        $this->author = ['name' => $name, 'url' => $url, 'icon_url' => $iconUrl];
        return $this;
    }

    public function field(?string $name, ?string $value, ?bool $inline): static
    {
        $this->fields ??= [];
        $this->fields[] = ['name' => $name, 'value' => $value, 'inline' => $inline];
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'color' => $this->color,
            'timestamp' => $this->timestamp,
            'footer' => $this->footer,
            'thumbnail' => $this->thumbnail,
            'image' => $this->image,
            'author' => $this->author,
            'fields' => $this->fields,
        ]);
    }
}
