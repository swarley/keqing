<?php

namespace App\Http\Controllers\ApplicationCommands;

use App\Attributes\ApplicationCommand;
use App\Attributes\ApplicationCommand\Arguments\StringArg;
use App\Discord\Interaction;
use App\Discord\InteractionResponse;
use App\Http\Controllers\Controller;

#[ApplicationCommand(
    name: 'blep',
    description: 'Blep blep blep'
)]
class BlepController extends Controller
{

    #[StringArg(name: 'option_a', description: 'The option', choices: ['First' => 'first', 'Second' => 'second'])]
    public function __invoke(Interaction $interaction, $optionA = 'first'): InteractionResponse
    {
        return $interaction->response()
            ->ephemeral()
            ->content("You chose $optionA");
    }
}
