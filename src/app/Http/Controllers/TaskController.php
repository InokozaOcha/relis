<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\AccountProject;
use App\Models\RelisAccount;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Str;
class TaskController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $taskId = (string) Str::uuid();
            $task_name = $request->input('taskName');
            $task_description = $request->input('taskDescription');
            $task_project_id = $request->input('projectId');
            $task_start_date = $request->input('startDate');
           
            $task = new Task();
            $task->id = $taskId;
            $task->task_name = $task_name;
            $task->task_description = $task_description;
            $task->project_id = $task_project_id;
            $task->start_datetime =$task_start_date;
           
            $task->save();
            
            $addTask =Task::join('projects','projects.id','=','tasks.project_id')
            ->join('account_project','projects.id','=','account_project.project_id')
            ->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            ->where('tasks.id', $taskId)
            ->where('tasks.project_id', $task_project_id)->first();
            //Log::debug($addTask);
            DB::commit();
            // 成功した場合のレスポンスを返す
            return response()->json([
                'status' => 'success',
                'message' => 'taskが正常に作成されました。',
                'project' => $addTask,
            ], 201);

        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        // リクエストから受け取ったデータを使って新しいプロジェクトを作成
        
    }

    public function update_progress(Request $request) {
        
        $taskId = $request->input('taskId');
        // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $progress = $request->input('progress');
        $taskExists = Task::where('id', $taskId)->exists();
        
        if(!$taskExists ) {
            return response()->json(['message' => 'タスクがありません'], 200);
        }
            // トランザクションを開始
        DB::beginTransaction();
        try {
        
            //Log::debug("bbbbbb");
            $task = Task::find($taskId);
            $task->progress = $progress;
            $task->save();
            
            // if($isSelected == 1) {
            //     RelisAccount::where('id', $is_selected_account)->exists();
            //     $account->save();
            // }
            
        
            DB::commit();
            //Log::debug("進捗変更完了");
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => '進捗変更　successfully'], 201);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        
    }


    public function get(Request $request)
    {
        try {
            // DB::beginTransaction();
            // $taskId = (string) Str::uuid();
            // $task_name = $request->input('taskName');
            // $task_description = $request->input('taskDescription');
            // $task_project_id = $request->input('projectId');
           
            // $task = new Task();
            // $task->id = $taskId;
            // $task->task_name = $task_name;
            // $task->task_description = $task_description;
            // $task->project_id = $task_project_id;
           
            // $task->save();
            
            // $addTask =Task::join('projects','projects.id','=','tasks.project_id')
            // ->join('account_project','projects.id','=','account_project.project_id')
            // ->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            // ->where('tasks.id', $taskId)
            // ->where('tasks.project_id', $task_project_id)->first();
            // Log::debug($addTask);
            // DB::commit();
            
            $userId = $request->input('uid');

            //手持ちのアカウント取得
            $myAccount = RelisAccount::where('relis_user_id', $userId)->pluck('id');
            // Log::debug("account");
            // Log::debug($myAccount);
            //アカウントに紐づくプロジェクトを取得
            $projectIds = Project::join('account_project','projects.id','=','account_project.project_id')
            //->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            ->whereIn('account_id', $myAccount)->pluck('projects.id');
            // Log::debug("projectID");
            // Log::debug($projectIds);
            $task = Task::join('projects','projects.id','=','tasks.project_id')
            ->join('account_project','projects.id','=','account_project.project_id')
            ->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            ->whereIn('tasks.project_id', $projectIds)
            ->select(
                'tasks.*','tasks.id as task_id', 
                'projects.id as project_id', 
                'projects.project_name as project_name',
                'relis_accounts.id as account_id',
                'relis_accounts.name as account_name', 
                'relis_accounts.relis_user_id as user_id', 

                'projects.start_datetime as project_start_datetime')
        //     ->select(
        //         'tasks.*',
        //         'projects.start_datetime as project_start_datetime',
        // 'tasks.start_datetime as task_start_datetime',
        //         'projects.*', // プロジェクトのすべてのカラムを取得
        //         'relis_accounts.*' // ユーザーアカウントのすべてのカラムを取得
        //     )
        ->get();
            // Log::debug("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
            // foreach ($task as $value) {
            //     Log::debug($value);
            // }
            

            // 成功した場合のレスポンスを返す
            return response()->json([
                'status' => 'success',
                'message' => 'taskが正常に作成されました。',
                'task' => $task,
            ], 201);

        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        // リクエストから受け取ったデータを使って新しいプロジェクトを作成
        
    }

    public function delete(Request $request)
    {
        // プロジェクトを更新する処理
         // POSTされたUUIDを取得
        $taskId = $request->input('taskId');
        // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $taskExists = Task::where('id', $taskId)->exists();
     
        if(!$taskExists) {
            return response()->json(['message' => 'アカウントがありません'], 200);
        }
          // トランザクションを開始
        DB::beginTransaction();
  
        try {
            
            $task = Task::find($taskId);
            $task->delete();
        
            DB::commit();
            Log::debug("dddd");
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => 'Task delete successfully'], 201);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    
}