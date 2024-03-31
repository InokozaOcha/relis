<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


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

    public function addUserTest(Request $userDataRequest) {
        try {
            $userName = $userDataRequest['user_name'];
            $userId = $userDataRequest['user_id'];
            $userPassword = $userDataRequest['user_password'];

            if($userName == "") {
                return response()->json([
                    'code' => "E0001",
                    'message' => "user_nameがありません",
                ], 400); 
            }

            if($userId == "") {
                return response()->json([
                    'code' => "E0002",
                    'message' => "user_idがありません",
                ], 400); 
            }

            if($userPassword == "") {
                return response()->json([
                    'code' => "E0003",
                    'message' => "user_passwordがありません",
                ], 400); 
            }

            $idCount = Test::where('user_id', $userId)->count();

            if($idCount !== 0) {
                return response()->json([
                    'code' => "E0004",
                    'message' => "そのIDはすでに登録されてます",
                ], 400); 
            }

            Test::create([
                'id' => Str::uuid(),
                'user_name' => $userName,
                'user_id' => $userId,
                'user_password' => $userPassword,
            ]);

        } catch(Exception $e) {
            $e->getMessage();
            return response()->json([
                'code' => "E0000",
                'message' => "なんかミスってます",
            ], 400);
        }  
    }

    public function serchUserTest(Request $userDataRequest) {
        try {
            $userName = $userDataRequest['user_name'];
            Log::debug($userName);
            $selectUsers = Test::where('user_name', $userName)->get();
            $userArry =[];
            $response = response() -> json([],200);
            foreach ($selectUsers as $user) {
                Log::debug($user['user_name']);
                $response = response() -> json([
                    'id' => Str::uuid(),
                    'user_name' => $user['user_name'],
                    'user_id' => $user['user_id'],
                    'user_password' => $user['user_password'],
                    'created_at' => $user['created_at'],
                    'updated_at' => $user['updated_at'],
                ],200);
            }

            return $response;
            

            
        } catch(Exception $e) {
            $e->getMessage();
            return response()->json([
                'code' => "E0000",
                'message' => "なんかミスってます",
            ], 400);
        } 
    }
}
