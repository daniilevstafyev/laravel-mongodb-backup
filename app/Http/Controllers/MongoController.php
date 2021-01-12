<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;
use MongoDB\BSON\UTCDateTime as MongoDate;
use Illuminate\Support\Facades\Log;

class MongoController extends Controller
{
    function home(Request $request) {
        
        try {
            
            // 1. Connect remote mongo db

            $targetConnectionUrl = $request->get('targetConnectionUrl');
            $destinationConnnectionUrl = $request->get('destinationConnectionUrl');
            $targetClient = new Mongo($targetConnectionUrl);
            $destinationClient = new Mongo($destinationConnnectionUrl);
            
            
            
            // 2. Dump and sanitize data

            date_default_timezone_set('UTC');
            // -5 Years is for test for sample database
            // Replace with '-3 Months' to get latest 3 months data
            $date3MonthsAgo = date('Y-m-d', strtotime("-5 Years"));
            // $start = new MongoDate(strtotime($date3MonthsAgo . " 00:00:00") * 1000);

            $start = new MongoDate(strtotime("2017-01-01 00:00:00") * 1000);
            Log::debug($start);
            
            // Change 'sample_analytics' to your database name
            // Change 'transactions' to your collection name, e.g. 'invoices' or 'events'
            $transactions = $targetClient->sample_analytics->transactions;
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
            $documents = $transactions->aggregate($pipline);
            $result = $documents->toArray();

            
            // 3. Duplicates db
            if (count($result) === 0) {
                return 'There isn\'t document to update';
            }

            $developers = $request->get('developers');
            foreach ($developers as $developer) {
                $dbName = $developer;
                $dbCount = 0;
                do {
                    // select a database. If it doesn't exist, it creates a new database.
                    $db = $destinationClient->$dbName;
                    $collection = $db->transactions;
                    $count = $collection->count();
                    Log::debug(gettype($count));
                    Log::debug($count);
                    if ($count > 0) {
                        // db exists
                        $dbCount++;
                        $dbName = $developer . '-' . $dbCount;
                    } else {
                        // db doesn't exist
                        $insertedCount = $collection->insertMany($result)->getInsertedCount();
                        Log::debug($insertedCount);
                        break;
                    }
                } while (true);
            }

            return 'Successfully Duplicated databases!';

            
        } catch (Throwable $e) {
            return json_encode($e);
        }
    }
}
