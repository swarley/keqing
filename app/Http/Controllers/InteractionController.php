<?php

namespace App\Http\Controllers;

use App\Attributes\ApplicationCommand;
use App\Discord\Interaction;
use App\Discord\InteractionOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use ReflectionClass;

class InteractionController extends Controller
{
    public function __invoke(Interaction $interaction)
    {

        Log::info("Received interaction", $interaction->toArray());
        Telescope::tag(fn () => [
            "guild_id:$interaction->guild_id",
            "channel_id:$interaction->channel_id",
            'user_id:' . ($interaction->user ? $interaction->user->id : $interaction->member->user->id),
            "command:{$interaction->data->name}",
        ]);
        $commandController = $this->findCommand($interaction->data->name);

        if (!$commandController) {
            Log::error("Failed to find controller for command {$interaction->data->name}");
            return response()->json([], 404);
        }

        $args = [];

        /** @var InteractionOption $option */
        foreach($interaction->data->options ?? [] as $option) {
            $args[Str::camel($option->name)] = $option->value;
        }

        $response = (new $commandController)($interaction, ...$args);
        $responseData = $response->toArray();

        Log::info('Responding with ' . $response->getTypeName(), $responseData);
        return response()->json($responseData);
    }

    private function findCommand(string $name): ?string
    {
        return collect(config('discord.application_commands'))->first(function ($controller) use ($name) {
            try {
                $attributes = (new ReflectionClass($controller))->getAttributes(ApplicationCommand::class);
                $args = $attributes[0]->getArguments();
                return ($args['name'] ?? '') === $name;
            } catch (\Exception $ex) {
                report($ex);
                return false;
            }
        });
    }
}
