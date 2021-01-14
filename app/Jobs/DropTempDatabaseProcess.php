<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MongoDB\Client as Mongo;

class DropTempDatabaseProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $clusterUrl, $dbName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clusterUrl, $dbName)
    {
        $this->clusterUrl = $clusterUrl;
        $this->dbName = $dbName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Remove temp database
        $devMongoClient = new Mongo($this->clusterUrl);
        $devMongoClient->dropDatabase($this->dbName);

        // Remove temp files
        $path = resource_path('temp');
        unlink($path . "/mclaravel.archive");
        unlink($path . "/sanitizedDB.archive");
    }
}
