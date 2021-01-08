<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;

class MongoController extends Controller
{
    function home() {
        $prodClient = new Mongo('mongodb+srv://daniil:Yyw2dRA7CMkYWah@mcassemblies.7vixr.mongodb.net/test?retryWrites=true&w=majority');
        $transactions = $prodClient->sample_analytics->transactions;
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
    }
}
