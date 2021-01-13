<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;
use MongoDB\BSON\UTCDateTime as MongoDate;
use App\Jobs\CommandProcess;
use App\Jobs\GenerateNewDBNamesProcess;
use Illuminate\Support\Facades\Log;

class MongoController extends Controller
{
    private $devMongoClient, $developers;

    function home(Request $request) {
        
        try {
            
            // 1. Connect remote mongo db

            $prodClusterUrl = $request->get('prodClusterUrl');
            $devClusterUrl = $request->get('devClusterUrl');


            // 2. Dump and sanitize data

            // date_default_timezone_set('UTC');
            // $start = new MongoDate(strtotime("2009-01-01 00:00:00") * 1000);
            // Log::debug($start);
            
            // PROCESS: Generate new db names based on provided slug names.
            $dbName = $request->get('dbName');
            $developers = $request->get('developers');
            GenerateNewDBNamesProcess::dispatch($devClusterUrl, $developers);

            // PROCESS: Dump production database
            $path = resource_path('temp');
            $command = "mongodump --uri=\"{$prodClusterUrl}/{$dbName}?retryWrites=true&w=majority\" --gzip --archive=$path/mclaravel.archive";
            CommandProcess::dispatch($command);

            return 'Successfully Duplicated databases!';

        } catch (Throwable $e) {
            return json_encode($e);
        }
    }
}
