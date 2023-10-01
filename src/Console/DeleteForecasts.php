<?php

namespace PaulE\WeatherByIP\Console;

use Illuminate\Console\Command;

use \PaulE\WeatherByIP\Models\LocationLookup;

/*
 * Given an IP Address, this command will retrieve and display 
 * the five-day weather forecast and location for that IP.
 * If no address is provided, will default to the current user IP.
 */
class DeleteForecasts extends Command
{
    protected $signature = 'weatherbyip:delete';

    protected $description = 'Delete existing weather forecast and location lookup info.';

    public function handle()
    {
        $this->info(__('weatherbyip::cli.cli_delete_load'));

        //Get confirmation to delete. We want to get "delete"
        $name = $this->ask(__('weatherbyip::cli.cli_delete_confirm'));
        if($name != "delete") {
            return;
        }

        //Delete all LocationLookups (deletes forecasts with them)
        LocationLookup::select('*')->delete();
        $this->info(__('weatherbyip::cli.deleted'));
    }
}
