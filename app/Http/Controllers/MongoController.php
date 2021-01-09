<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;

class MongoController extends Controller
{
    function home(Request $request) {
        $targetConnectionUrl = $request->get('targetConnectionUrl');
        $destinationConnnectionUrl = $request->get('destinationConnectionUrl');
        
        try {
            $targetClient = new Mongo($targetConnectionUrl);
            $destinationClient = new Mongo($destinationConnnectionUrl);
            $transactions = $targetClient->sample_analytics->transactions;
            $documents = $transactions->find([], [
                'projection' => [
                    'account_id' => 1,
                    'transaction_count' => 1,
                    'bucket_start_date' => 1,
                    'bucket_end_date' => 1,
                ],
                'limit' => 5
            ]);
            return $documents->toArray();
        } catch (Throwable $e) {
            // report($e);
            return redirect('/');
        }
    }
}
