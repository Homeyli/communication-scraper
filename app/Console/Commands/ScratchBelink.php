<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BelinkCurl;

class BelinkScraper extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'scratch:belink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */


    public function handle(BelinkCurl $scraper)
    {

        die($scraper->testcall());
        return Command::SUCCESS;
    }
}
