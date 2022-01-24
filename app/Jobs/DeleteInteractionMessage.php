<?php

namespace App\Jobs;

use App\Discord\Interaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteInteractionMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Interaction $interaction)
    {
        $this->token = $interaction->token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $baseUrl = config('discord.base_url');
        $applicationId = config('discord.application_id');

        try {
            Log::info('Attempting to delete message');
            $resp = Http::delete("$baseUrl/webhooks/$applicationId/$this->token/messages/@original");
        } catch (\Exception $ex) {
            report($ex);
        }
    }
}
