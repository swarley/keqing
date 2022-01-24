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
