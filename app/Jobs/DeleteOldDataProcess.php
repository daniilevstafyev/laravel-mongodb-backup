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
use MongoDB\BSON\UTCDateTime as MongoDate;


class DeleteOldDataProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $devClusterUrl, $dbName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($devClusterUrl, $dbName)
    {
        $this->devClusterUrl = $devClusterUrl;
        $this->dbName = $dbName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $devMongoClient = new Mongo($this->devClusterUrl);
        $db = $this->dbName;
        
        // delete old invoices
        $invoices = $devMongoClient->$db->invoices;
        date_default_timezone_set('UTC');
        $date3MonthsAgo = date('Y-m-d', strtotime("-3 Months"));
        $date = new MongoDate(strtotime($date3MonthsAgo . " 00:00:00") * 1000);
        // $date = new MongoDate(strtotime("2017-01-01" . " 00:00:00") * 1000);
        $pipeline = array(
            'created_at' => array(
                '$lt' => $date
            )
        );
        $invoices->deleteMany($pipeline);

        // delete old events
        $events = $devMongoClient->$db->events;
        $events->deleteMany($pipeline);
    }
}
