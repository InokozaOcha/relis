<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\AccountProject;
use App\Models\RelisAccount;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Str;
class ProjectController extends Controller
{
    public function index()
    {
        // プロジェクト一覧を表示する処理
        //$projects = Project::select('id', 'project_name')->get();
        $projects = Project::get();
        return response()->json($projects);
    }

    public function create()
    {
        // プロジェクト作成フォームを表示する処理
    }

    public function store(Request $request)
    {
        // プロジェクトを作成する処理
        // バリデーションルールを定義
        // $rules = [
        //     'project_name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'start_datetime' => 'nullable|date',
        //     'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
        //     'priority' => 'nullable|integer|min:0|max:10',
        //     // 他の属性に関するバリデーションルールをここに追加
        // ];

        // // カスタムバリデーションメッセージを定義
        // $messages = [
        //     'project_name.required' => 'プロジェクト名は必須項目です。',
        //     'start_datetime.date' => '開始日時は日付形式で指定してください。',
        //     'end_datetime.date' => '終了日時は日付形式で指定してください。',
        //     'end_datetime.after_or_equal' => '終了日時は開始日時と同じか後の日時を指定してください。',
        //     'priority.integer' => '優先度は整数で指定してください。',
        //     'priority.min' => '優先度は0以上で指定してください。',
        //     'priority.max' => '優先度は10以下で指定してください。',
        //     // 他の属性に関するエラーメッセージをここに追加
        // ];

        // // バリデータを生成
        // $validator = Validator::make($request->all(), $rules, $messages);

        // // バリデーションを実行
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => '入力内容にエラーがあります。',
        //         'errors' => $validator->errors(),
        //     ], 422);
        // }

        try {
            DB::beginTransaction();
            $projectId = (string) Str::uuid();

            $project = new Project();
            $project->id = $projectId;
            $project->project_name = $request->input('projectName');
            
            // $project->description = $request->input('description');
            // $project->start_datetime = $request->input('start_datetime');
            // $project->end_datetime = $request->input('end_datetime');
            // $project->priority = $request->input('priority');
            
            // 他の属性に関するデータをここで追加
            //$user

            // 新しいプロジェクトを保存
            $project->save();

            $ownerAccount = $request->input('accountId');
            

            $data = [
                [
                    'id'         => (string) Str::uuid(),
                    'account_id' => $ownerAccount,
                    'project_id' => $projectId,
                    'permissions'=> 'admin'
                ]
            ];

            $friends = $request->input('friendList');

            foreach ($friends as $friend) {
                $data[] = [
                    'id'         => (string) Str::uuid(),
                    'account_id' => $friend,
                    'project_id' => $projectId,
                    'permissions'=> 'editor'
                ];
            }
            Log::debug("friends");
            Log::debug($friends);
            
            AccountProject::insert($data);
            Log::debug("friends2222222");
            $addproject =Project::join('account_project','projects.id','=','account_project.project_id')->join('relis_accounts','relis_accounts.id','=','account_project.account_id')->where('account_id', $ownerAccount)->where('projects.id', $projectId)->first();
            DB::commit();
            // 成功した場合のレスポンスを返す
            return response()->json([
                'status' => 'success',
                'message' => 'プロジェクトが正常に作成されました。',
                'project' => $project,
                'addproject' => $addproject,
            ], 201);

        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        // リクエストから受け取ったデータを使って新しいプロジェクトを作成
        
    }

    public function show($id)
    {
        // プロジェクトの詳細を表示する処理
    }

    public function edit($id)
    {
        // プロジェクト編集フォームを表示する処理
    }

    public function update(Request $request, $id)
    {
        // プロジェクトを更新する処理
    }

    public function destroy($id)
    {
        // プロジェクトを削除する処理
    }

    public function delete(Request $request) 
    {
        $projectId = $request->input('projectId');
        $accountId = $request->input('accountId');
        Log::debug('$projectId');
        Log::debug($projectId);
        Log::debug('$accountId');
        Log::debug($accountId);

        try {
            

            $account = AccountProject::where('account_id',$accountId)->where('project_id',$projectId)->first();
            Log::debug($account);   
            if($account['permissions'] == 'admin') {
                DB::beginTransaction();
                $project = Project::find($projectId);
                $project->delete();
                DB::commit();
                Log::debug("削除されました");       
                return response()->json(['message' => 'プロジェクトは正常に削除されました'], 201);         
            } else {
                return response()->json(['message' => '削除権限がありません'], 403);
            }
            
            // 成功メッセージを返すなどの処理を行う
            
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());       
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    public function search_myproject(Request $request)
    {
        $userId = $request->input('uid');
        $accountId = $request->input('accountId');
        $searchWord = $request->input('searchWord');
        Log::debug("ここはOK");
        try {

            //手持ちのアカウント取得
            $myAccount = RelisAccount::where('relis_user_id', $userId)->pluck('id');
            //アカウントに紐づくプロジェクトを取得
            $projects = Project::join('account_project','projects.id','=','account_project.project_id')->join('relis_accounts','relis_accounts.id','=','account_project.account_id')->whereIn('account_id', $myAccount)->get();
            Log::debug($projects); 
            foreach ($projects as $project) {
                Log::debug($project->project_name);
            }    
            

            return response()->json([
                'projects' => $projects,
            ], 201);
            
            
            
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            Log::debug("エラーです");
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }

    }
}
