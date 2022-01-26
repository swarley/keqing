<?php

if (!function_exists('encode_custom_id')) {
    function encode_custom_id(string $namespace, string $name, array $data = []): string
    {
        return implode("\0", [$namespace, $name, ...$data]);
    }
}

if (!function_exists('decode_custom_id')) {
    function decode_custom_id(string $data): array
    {
        return explode("\0", $data);
    }
}

if (!function_exists('whitespace_to_underscore')) {
    function whitespace_to_underscore(string $data): string
    {
        return preg_replace('/\s/', '_', $data);
    }
}

if (!function_exists('escape_symbols')) {
    function escape_symbols(string $data): string
    {
        return preg_replace('/([^\w])/', '\\\$1', $data);
    }
}


if (!function_exists('dtext_to_markdown')) {
    function dtext_to_markdown(string $data): string
    {
        $wikiLink = 'https://danbooru.donmai.us/wiki_pages';
        $str = preg_replace('/\[\/?b\]/', '**', $data);
        $str = preg_replace('/\[\/?spoiler\]/', '||', $str);
        $str = preg_replace_callback(
            '/\[\[([^\|\]]+)\]\]/',
            fn ($matches) => "[$matches[1]]($wikiLink/" . whitespace_to_underscore($matches[1]) . ')',
            $str
        );

        $str = preg_replace_callback(
            '/\[\[([^|]+)\|([^\]]+)\]\]/',
            fn ($matches) => "[$matches[2]]($wikiLink/" . escape_symbols(whitespace_to_underscore($matches[1])) . ')',
            $str
        );

        return $str;
    }
}
