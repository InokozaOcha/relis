<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use Illuminate\Support\Facades\Log;


class TestController extends Controller
{
    //
    public function getTestAll() {
        Log::emergency('bbbbbbbbbbbbbb');
        Log::debug('cccccccccccccc');
        $test = Test::all();
        return response()->json(
            [
                'testData'=>$test
                //'testData' => 'hoge'
            ]);
    }
}
