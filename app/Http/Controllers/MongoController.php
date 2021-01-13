<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;
use MongoDB\BSON\UTCDateTime as MongoDate;
use App\Jobs\CommandProcess;
use App\Jobs\DeleteOldDataProcess;
use App\Jobs\DropTempDatabaseProcess;
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
            
            // PROCESS: Generate new db names based on provided slug names.
            $dbName = $request->get('dbName');
            $developers = $request->get('developers');
            $newDBNames = $this->generateNewDbNames($devClusterUrl, $developers);

            // PROCESS: Dump production database
            $path = resource_path('temp');
            $command = "mongodump --uri=\"{$prodClusterUrl}/{$dbName}?retryWrites=true&w=majority\" --gzip --archive=$path/mclaravel.archive";
            CommandProcess::dispatch($command);

            // PROCESS: Restore production database to development cluster
            $tempDbName = "cloned_temp";
            $command = "mongorestore --uri=\"{$devClusterUrl}/{$dbName}?retryWrites=true&w=majority\" --gzip --archive=$path/mclaravel.archive --nsFrom \"${dbName}.*\" --nsTo \"${tempDbName}.*\"";
            CommandProcess::dispatch($command);

            // PROCESS: Remove data more than 3 months old from temp db
            DeleteOldDataProcess::dispatch($devClusterUrl, $tempDbName);

            // PROCESS: Download sanitized data
            $command = "mongodump --uri=\"{$devClusterUrl}/{$tempDbName}?retryWrites=true&w=majority\" --gzip --archive=$path/sanitizedDB.archive";
            CommandProcess::dispatch($command);

            // PROCESS: duplicate sanitized datbase with new db names.
            foreach ($newDBNames as $newDbName) {
                $command = "mongorestore --uri=\"{$devClusterUrl}/{$tempDbName}?retryWrites=true&w=majority\" --gzip --archive=$path/sanitizedDB.archive --nsFrom \"${tempDbName}.*\" --nsTo \"${newDbName}.*\"";
                CommandProcess::dispatch($command);
            }

            // PROCESS: delete temp sanitized database and temp files.
            DropTempDatabaseProcess::dispatch($devClusterUrl, $tempDbName);

            return 'Successfully Duplicated databases!';

        } catch (Throwable $e) {
            return json_encode($e);
        }
    }

    public function generateNewDbNames($devClusterUrl, $developers) {
        $existingDatabases = [];
        $devMongoClient = new Mongo($devClusterUrl);
        foreach($devMongoClient->listDatabases() as $database){
            array_push($existingDatabases, $database->getName());
        }
        $dbNamesToAdd = [];
        foreach ($developers as $developer) {
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
        return $dbNamesToAdd;
    }
}
