<?php

namespace App\Console\Commands;

use App\Attributes\ApplicationCommand;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use ReflectionClass;

class RegisterApplicationCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keqing:register-commands {guild? : The ID of the guild to register on, global if omitted.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register application commands on Discord.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $commandControllers = config('discord.application_commands', []);

        $commands = [];
        try {
            foreach ($commandControllers as $commandController) {
                $data = $this->getCommandDataFromController($commandController);
                $commands[] = $data;
                $this->info("Discovered {$data['name']}");
            }
        } catch (\Exception $exception) {
            return 1;
        }

        $resp = $this->registerCommands($commands);

        if ($resp->ok()) {
            $guildId = $this->argument('guild');
            $this->info($guildId ? "Commands updated for $guildId." : 'Global commands updated.');
            return 0;
        }

        $this->error("Failed to update commands: " . $resp->body());
        return 1;
    }

    private function getCommandDataFromController(string $controller): array
    {
        try {
            $class = new ReflectionClass($controller);
        } catch (\ReflectionException $ex) {
            report($ex);
            $this->error("Failed to reflect $controller");

            throw new \Exception();
        }

        $commandAttributes = $class->getAttributes(ApplicationCommand::class);

        if (empty($commandAttributes)) {
            $this->error("$controller does not have an InteractionData annotation");
            throw new \Exception();
        }

        $commandData = $commandAttributes[0]->getArguments();
        $commandData['options'] = [];

        foreach ($class->getMethods() as $method) {
            $arguments = $method->getAttributes(ApplicationCommand\Argument::class, 2);

            if (!empty($arguments)) {
                foreach ($arguments as $argument) {
                    $commandData['options'][] = $argument->newInstance()->toArray();
                }
            }
        }

        return $commandData;
    }

    private function registerCommands(array $commands): Response
    {
        $guildId = $this->argument('guild');
        $url = config('discord.base_url') . '/applications/' . config('discord.application_id');
        $url .= $guildId ? "/guilds/$guildId/commands" : '/commands';
        return Http::asJson()->withHeaders(['authorization' => config('discord.token')])->put($url, $commands);
    }
}
