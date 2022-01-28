<?php

return [
    'base_url' => 'https://discord.com/api/v9',
    'application_id' => env('DISCORD_APPLICATION_ID'),
    'public_key' => env('DISCORD_PUBLIC_KEY'),
    'token' => env('DISCORD_TOKEN'),
    'application_commands' => [
        \App\Http\Controllers\ApplicationCommands\DanbooruController::class,
    ],

    'components' => [
        \App\Http\Controllers\Components\DanbooruSearchController::class,
        \App\Http\Controllers\Components\DanbooruFavoritesController::class,
        \App\Http\Controllers\Components\DanbooruRandomController::class,
        \App\Http\Controllers\Components\DanbooruUtilityController::class,
    ],

    'safe_color' => 0x00FF00,
    'questionable_color' => 0xFFFF00,
    'explicit_color' => 0xFF0000,
];
