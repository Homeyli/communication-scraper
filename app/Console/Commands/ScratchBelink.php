<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BelinkCurl;

class ScratchBelink extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $companyCount = 0;

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

        // get first 50 companies

        $this->info('start get count of data..');

        $this->companyCount = $scraper->getCompaniesCount();

        $limit = 20;
        $intrator = 0;
        $offset = 0;
        $executionTime = (int)($this->companyCount / $limit);

        while($intrator <= $executionTime) {
            

            $limit = $executionTime == $intrator ? ($this->companyCount % $limit) : $limit;
            $this->error("limit $limit offset $offset");


            $data = $scraper->getLimitCompanies(
                limit: $limit,
                offset: $offset
            );

            print_r($data);
            die();


            $offset += $limit;
            $intrator++;
            
        }
        
        

        //$this->error();
        return Command::SUCCESS;
    }
}
