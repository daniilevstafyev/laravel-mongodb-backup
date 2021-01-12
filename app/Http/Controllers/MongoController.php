<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;
use MongoDB\BSON\UTCDateTime as MongoDate;
use Illuminate\Support\Facades\Log;

class MongoController extends Controller
{
    private $developers;
    private $collections;
    private $dbName;
    private $destinationClient;
    private $targetClient;
    private $databaseNames;

    function home(Request $request) {
        
        try {
            
            // 1. Connect remote mongo db

            $targetConnectionUrl = $request->get('targetConnectionUrl');
            $destinationConnnectionUrl = $request->get('destinationConnectionUrl');
            $this->targetClient = new Mongo($targetConnectionUrl);
            $this->destinationClient = new Mongo($destinationConnnectionUrl);


            // 2. Dump and sanitize data

            date_default_timezone_set('UTC');
            // -5 Years is for test for sample database
            // Replace with '-3 Months' to get latest 3 months data
            $date3MonthsAgo = date('Y-m-d', strtotime("-5 Years"));
            // $start = new MongoDate(strtotime($date3MonthsAgo . " 00:00:00") * 1000);
            
            //I manually set $start to '2017-01-01' for test sample database. 
            // Please commend this and use above code Line 31 for your database
            $start = new MongoDate(strtotime("2017-01-01 00:00:00") * 1000);
            Log::debug($start);
            
            $db = $request->get('dbName');
            $collections = $request->get('collections');
            $developers = $request->get('developers');
            $this->collections = $collections;
            $this->developers = $developers;
            $this->setDatabaseNames();
            return $this->databaseNames;

            $results = array();

            foreach($collections as $collectionName) {

                // Change 'sample_analytics' to your database name
                // Change 'transactions' to your collection name, e.g. 'invoices' or 'events'
                // Will need to dulicate code from line 39~59 for several collections copy.
                $collections = $targetClient->$db->$collectionName;
                $pipline = array(
                    array(
                        '$match' => array(
                            // write your field instead of 'bucket_end_date'
                            // This will be 'createdAt'
                            'bucket_end_date' => array(
                                '$gte' => $start
                            )
                        )
                    ),
                    array(
                        '$project' => array(
                            // add fields that you want to get
                            // fields you want to get from 'invoices' or 'events'
                            'bucket_end_date' => 1
                        )
                    )
                );
                $documents = $collections->aggregate($pipline);
                $result = $documents->toArray();

                $chunks = array_chunk($result, 500);
                foreach ($chunks as $key => $chunk) {
                    $name = "/" . $collectionName . "-{$key}.txt";
                    $path = resource_path('temp');
                    file_put_contents($path . $name, json_encode($chunk));
                }
            }

            return 'Successfully Duplicated databases!';

            
        } catch (Throwable $e) {
            return json_encode($e);
        }
    }

    public function setDatabaseNames() {
        $existingDatabases = [];
        foreach($this->destinationClient->listDatabases() as $database){
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
        $this->databaseNames = $dbNamesToAdd;
    }

    public function store() {
        $path = resource_path('temp');
        $files = glob("$path/*.txt");

        foreach ($files as $file) {
            $content = json_decode(file_get_contents($file));
            $collectionName = explode("-", $file)[0];            
        }
    }
}
