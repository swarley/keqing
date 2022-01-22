<?php

return [
    'base_url' => 'https://discord.com/api/v9',
    'application_id' => env('DISCORD_APPLICATION_ID'),
    'public_key' => env('DISCORD_PUBLIC_KEY'),
    'token' => env('DISCORD_TOKEN'),
    'application_commands' => [
        App\Http\Controllers\ApplicationCommands\BlepController::class,
    ]
];
