<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MongoDB\Client as Mongo;

class GenerateNewDBNamesProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $devClusterUrl, $developers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($devClusterUrl, $developers)
    {
        $this->$devClusterUrl = $devClusterUrl;
        $this->developers = $developers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $existingDatabases = [];
        $devMongoClient = new Mongo($this->devClusterUrl);
        foreach($devMongoClient->listDatabases() as $database){
            array_push($existingDatabases, $database->getName());
        }
        $dbNamesToAdd = [];
        foreach ($this->developers as $developer) {
            $count = 0;
            $dbName = $developer;
            do {
                if (in_array($dbName, $existingDatabases)) {
                    $count++;
                    $dbName = $developer . "-" . $count;
                } else {
                    array_push($dbNamesToAdd, $dbName);
                    break;
                }
            } while (true);
        }
        // $this->databaseNames = $dbNamesToAdd;
        $name = "/newDBNames.txt";
        $path = resource_path('temp');
        file_put_contents($path . $name, json_encode($dbNamesToAdd));
    }
}
