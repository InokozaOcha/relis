<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RelisUser;
use App\Models\RelisAccount;
use App\Models\Friendship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class AccountController extends Controller
{
    public function index(Request $request)
    {
        // プロジェクト一覧を表示する処理
        $userId = $request->input('uid');
        $user = RelisUser::where('id', $userId)->first();
        $account = RelisAccount::where('relis_user_id', $userId)->get();

        $friendList = Friendship::join('relis_accounts as user_account', 'friendships.account_id', '=', 'user_account.id')
        ->join('relis_accounts as friend_account', 'friendships.friend_id', '=', 'friend_account.id')
        ->where('user_account.relis_user_id', $userId)
        ->select('user_account.*', 'friend_account.*')
        ->get();

        return response()->json([
            'user' => $user,
            'account' => $account,
            'friend' => $friendList,
        ], 200);

    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        // POSTされたUUIDを取得
        $userId = $request->input('uid');
        $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $userExists = RelisUser::where('id', $userId)->exists();
        $defaultAccountExists =  RelisAccount::where('relis_user_id', $userId)
            ->where('is_default_account', true)
            ->exists();
        
        if($userExists && $defaultAccountExists ) {
            Log::debug('存在してます');
            return response()->json(['message' => '既にアカウントは作成されています'], 200);
        }
        
        // トランザクションを開始
        DB::beginTransaction();

        try {
            if (!$userExists) {
                // relis_usersにレコードを挿入
                $user = new RelisUser();
                $user->id = $userId;
                $user->name = '';
                $user->save();
            }

            if(!$defaultAccountExists) {
                $account = new RelisAccount;
                $account->id = (string) Str::uuid();
                $account->relis_user_id = $userId;
                $account->name = 'デフォルトアカウント';
                if($isDefault === true) {
                    $account->is_default_account = true;
        
                } else {
                    $account->is_default_account = false;   
                }
                   
                $account->save();       
            }

            // トランザクションをコミット
            DB::commit();
    
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => 'User and Account created successfully'], 200);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function add_account(Request $request)
    {
        // POSTされたUUIDを取得
        $userId = $request->input('uid');
       // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $accountName = $request->input('accountName');
        
        $userExists = RelisUser::where('id', $userId)->exists();
        $defaultAccountExists =  RelisAccount::where('relis_user_id', $userId)
            ->where('is_default_account', true)
            ->exists();
            Log::debug("aaaaaaaaaaaaaaa");
        // トランザクションを開始
        DB::beginTransaction();

        try {
            if (!$userExists) {
                // relis_usersにレコードを挿入
                $user = new RelisUser();
                $user->id = $userId;
                $user->name = '';
                $user->save();
            }
            Log::debug("bbbbbb");
            $account = new RelisAccount;
            $account->id = (string) Str::uuid();
            $account->relis_user_id = $userId;
            $account->name = $accountName;
            $account->is_default_account = false; 
            Log::debug("cccc");
            // トランザクションをコミット
            $account->save();   
            DB::commit();
            Log::debug("dddd");
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => 'User and Account created successfully'], 201);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function friend_search(Request $request)
    {
        $userId = $request->input('uid');
        $accountId = $request->input('accountId');
        $searchWord = $request->input('searchWord');
        Log::debug("ここはOK");
        try {
            $aaa = RelisAccount::join('friendships', 'friendships.friend_id', '=', 'relis_accounts.id')
            ->get();
            
            $friendshipData = Friendship::where('account_id',$accountId)->get();
            Log::debug('POSTです');
            Log::debug($accountId);
            $friendshipArray = json_decode($friendshipData, true);
            $friendIds = array_column($friendshipArray, 'friend_id');
            Log::debug($friendIds);

            $account = RelisAccount::whereNotIn('id', $friendIds)
            ->where(function($query) use ($searchWord) {
                $query->where('id', 'like', '%' . $searchWord . '%')
                      ->orWhere('name', 'like', '%' . $searchWord . '%');
            })
            ->whereNot('relis_user_id', $userId)
            ->get();
            return response()->json([
                'account' => $account,
            ], 201);
            
            
            
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            Log::debug("エラーです");
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }

    }

    public function friend_add(Request $request)
    {
        $userId = $request->input('userId');
        $accountId = $request->input('accountId');
        $friendId = $request->input('friendId');
        

        try {
            
            Log::debug("bbbbbb");
            $checkAccount = RelisAccount::where('id',$friendId)->first();
            $friendExists =  Friendship::where('account_id', $accountId)->where('friend_id', $friendId)->exists();
            $defaultAccountExists =  RelisAccount::where('relis_user_id', $userId)
            ->where('is_default_account', true)
            ->exists();
            Log::debug("defaultチェック！");
                Log::debug($defaultAccountExists);
            if($checkAccount) {
                Log::debug("存在チェック！");
                Log::debug($friendExists);
                if(!$friendExists) {
                    Log::debug("突破");
                    $checkId = $checkAccount->relis_user_id;
                    if($checkId != $userId) {
                        DB::beginTransaction();
                        $friendship = new Friendship();
                        $friendship->id = (string) Str::uuid();
                        $friendship->account_id = $accountId;
                        $friendship->friend_id = $friendId;
                        $friendship->save();

                        $friendList = Friendship::join('relis_accounts as user_account', 'friendships.account_id', '=', 'user_account.id')
                        ->join('relis_accounts as friend_account', 'friendships.friend_id', '=', 'friend_account.id')
                        ->where('user_account.relis_user_id', $userId)
                        ->select('user_account.*', 'friend_account.*')
                        ->get();
                        
                        
                        DB::commit();
                        Log::debug("dddd");
                        return response()->json(['friend' =>$friendList], 201);

                    } else {
                        return response()->json(['message' => '自分のアカウントは登録できません' ], 500);
                    }

                } else {
                    return response()->json(['message' => 'すでに登録されています' ], 500);
                }
                
            } else {
                return response()->json(['message' => 'そもそも一致する友達がいません' ], 500);
            }


            
            // 成功メッセージを返すなどの処理を行う
            
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    

    public function show($id)
    {
        // プロジェクトの詳細を表示する処理
    }

    public function edit($id)
    {
        // プロジェクト編集フォームを表示する処理
    }

    public function update(Request $request)
    {
        // プロジェクトを更新する処理
         // POSTされたUUIDを取得
        $accountId = $request->input('accountId');
        // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $accountName = $request->input('accountName');
        $isDefault = $request->input('isDefaultAccount');
        $AccountExists = RelisAccount::where('id', $accountId)->exists();
     
        if(!$AccountExists) {
            return response()->json(['message' => 'アカウントがありません'], 200);
        }
          // トランザクションを開始
        DB::beginTransaction();
  
        try {
            
            Log::debug("bbbbbb");
            $account = RelisAccount::find($accountId);
            $account->name = $accountName;
            $account->save();
            
            // if($isSelected == 1) {
            //     RelisAccount::where('id', $is_selected_account)->exists();
            //     $account->save();
            // }
            
        
            DB::commit();
            Log::debug("dddd");
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => 'User and Account created successfully'], 201);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        // プロジェクトを更新する処理
         // POSTされたUUIDを取得
        $accountId = $request->input('accountId');
        // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $accountName = $request->input('accountName');
        $AccountExists = RelisAccount::where('id', $accountId)->exists();
     
        if(!$AccountExists) {
            return response()->json(['message' => 'アカウントがありません'], 200);
        }
          // トランザクションを開始
        DB::beginTransaction();
  
        try {
            
            $account = RelisAccount::find($accountId);
            $account->delete();
        
            DB::commit();
            Log::debug("dddd");
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => 'User and Account delete successfully'], 201);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        // プロジェクトを削除する処理
    }
}
