<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RefreshTokensJob;
use App\Models\Oauth;

class RefreshTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh OAuth tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch the tokens and dispatch the job

     
            RefreshTokensJob::dispatch();


        $this->info('Tokens refresh job dispatched.');
    }
}
