<?php

namespace App\Http\Controllers;

use App\Attributes\ApplicationCommand;
use App\Attributes\Component;
use App\Discord\Interaction;
use App\Discord\InteractionOption;
use Illuminate\Http\JsonResponse;
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
            ($interaction->type === 2) ? "command:{$interaction->data->name}" : "namespace:" . $interaction->namespace(),
        ]);

        if ($interaction->type === 2) {
            return $this->dispatchCommand($interaction);
        }

        if ($interaction->type === 3) {
            return $this->dispatchComponent($interaction);
        }

        if ($interaction->type === 4) {
            return $this->dispatchAutocomplete($interaction);
        }

        Log::error("Invalid interaction type: $interaction->type");
        return response()->json(['message' => 'invalid interaction type'], 400);
    }

    private function dispatchCommand(Interaction $interaction): JsonResponse
    {
        $commandController = $this->findCommand($interaction->data->name);

        if (!$commandController) {
            Log::error("Failed to find controller for command {$interaction->data->name}");
            return response()->json([], 404);
        }

        $args = [];
        $options = $interaction->data->options;
        $firstOption = $options[0] ?? null;
        $subcommandName = null;

        if ($firstOption?->type === 1) {
            $subcommandName = Str::camel($firstOption->name);
            $options = $firstOption->options;
        }

        /** @var InteractionOption $option */
        foreach($options ?? [] as $option) {
            $args[Str::camel($option->name)] = $option->value;
        }
        Log::info('Passing arguments', $args);

        if ($subcommandName) {
            $response = (new $commandController)->{$subcommandName}($interaction, ...$args);
        }
        else {
            $response = (new $commandController)($interaction, ...$args);
        }
        $responseData = $response->toArray();

        Log::info('Responding with ' . $response->getTypeName(), $responseData);
        return response()->json($responseData);
    }

    private function dispatchComponent(Interaction $interaction): JsonResponse
    {
        @[$namespace, $method, $args] = explode("\0", $interaction->data->custom_id, 3);
        $args = explode("\0", $args ?? '');
        $component = $this->findComponent($namespace);

        if (!$component) {
            Log::error("Failed to find controller for component {$interaction->data->custom_id}");
            return response()->json([], 404);
        }

        Log::info('Passing arguments', $args);
        $response = (new $component)->{$method}($interaction, ...$args);
        $responseData = $response->toArray();

        Log::info('Responding with ' . $response->getTypeName(), $responseData);
        return response()->json($responseData);
    }

    private function dispatchAutocomplete(Interaction $interaction): JsonResponse
    {
        $commandController = $this->findCommand($interaction->data->name);

        if (!$commandController) {
            Log::error("Failed to find controller for command {$interaction->data->name}");
            return response()->json([], 404);
        }

        $options = $interaction->data->options;
        $firstOption = $options[0] ?? null;
        $subcommandName = null;

        if ($firstOption?->type === 1) {
            $subcommandName = Str::camel($firstOption->name);
            $options = $firstOption->options;
        }

        $method = (new ReflectionClass($commandController))->getMethod($subcommandName ?? '__invoke');
        $attr = $method->getAttributes(ApplicationCommand\Autocomplete::class)[0] ?? null;
        $controller = $attr->getArguments()[0];
        $focusedArgument = $interaction->focusedArgument();

        $values = (new $controller)->{Str::camel($focusedArgument->name)}($interaction, $focusedArgument->value);
        $choices = [];

        foreach ($values as $name => $value) {
            $choices[] = ['name' => $name, 'value' => $value];
        }

        return response()->json([
           'type' => 8,
           'data' => ['choices' => $choices],
        ]);
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

    private function findComponent(string $namespace): ?string
    {
        return collect(config('discord.components'))->first(function ($controller) use ($namespace) {
            try {
                $attributes = (new ReflectionClass($controller))->getAttributes(Component::class);
                $args = $attributes[0]->getArguments();
                return ($args['namespace'] ?? '') === $namespace;
            } catch (\Exception $ex) {
                report($ex);
                return false;
            }
        });
    }

}
