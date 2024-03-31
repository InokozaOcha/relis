<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\AccountProject;
use App\Models\RelisAccount;
use App\Models\Task;
use App\Models\RelisList;
use App\Models\ListDate;
use App\Models\ListParticle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Str;
class ListController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $listId = (string) Str::uuid();
            $list_name = $request->input('listName');
            $list_description = $request->input('listDescription');
            $list_project_id = $request->input('projectId');
            $lsit_start = $request->input('start');
            $lsit_end = $request->input('end');
            $list_date = $request->input('date'); 
            
            $list = new RelisList();
            $list->id = $listId;
            $list->list_name = $list_name;
            $list->list_description = $list_description;
            $list->project_id = $list_project_id;
        
           
            
            $particle_list = [];
            for($i = $lsit_start; $i < $lsit_end + 1 ;$i++) {
                $particle_list[] = [
                    'id' => (string) Str::uuid(),
                    'no' => $i,
                    'project_id' => $list_project_id,
                    'list_id' => $listId,
                ];
            }
            Log::debug($particle_list);

            $particle_date = [];
            foreach($list_date as $key => $value) {
                $particle_date[] = [
                    'id' => (string) Str::uuid(),
                    'no' => $value['no'],
                    'date' => $value['date'],
                    'start' => $value['start'],
                    'end' => $value['end'],
                    'project_id' => $list_project_id,
                    'list_id' => $listId,
                ];
            }
            Log::debug($particle_date);

            $list->save();

            ListParticle::insert($particle_list);

        

           ListDate::insert($particle_date);

        
            DB::commit();
            // 成功した場合のレスポンスを返す
            return response()->json([
                'status' => 'success',
                'message' => 'taskが正常に作成されました。',
                //'project' => $addTask,
            ], 201);

        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        // リクエストから受け取ったデータを使って新しいプロジェクトを作成
        
    }

    // public function update_progress(Request $request) {
        
    //     $taskId = $request->input('taskId');
    //     // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
    //     $progress = $request->input('progress');
    //     $taskExists = Task::where('id', $taskId)->exists();
        
    //     if(!$taskExists ) {
    //         return response()->json(['message' => 'タスクがありません'], 200);
    //     }
    //         // トランザクションを開始
    //     DB::beginTransaction();
    //     try {
        
    //         Log::debug("bbbbbb");
    //         $task = Task::find($taskId);
    //         $task->progress = $progress;
    //         $task->save();
            
    //         // if($isSelected == 1) {
    //         //     RelisAccount::where('id', $is_selected_account)->exists();
    //         //     $account->save();
    //         // }
            
        
    //         DB::commit();
    //         Log::debug("進捗変更完了");
    //         // 成功メッセージを返すなどの処理を行う
    //         return response()->json(['message' => '進捗変更　successfully'], 201);
    //     } catch (\Exception $e) {
    //         // エラーが発生した場合はロールバックしてエラーメッセージを返す
    //         DB::rollback();
            
    //         return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    //     }
        
    // }


    public function get(Request $request)
    {
        try {
            
            
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

            $list = RelisList::join('projects','projects.id','=','lists.project_id')
            ->join('account_project','projects.id','=','account_project.project_id')
            ->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            ->whereIn('lists.project_id', $projectIds)
            ->select(
                'lists.*','lists.id as lists_id', 
                'projects.id as project_id', 
                'relis_accounts.id as account_id',
                'relis_accounts.name as account_name', 
                'relis_accounts.relis_user_id as user_id', 

                'projects.start_datetime as project_start_datetime')
            ->get();

            $lists_particle = ListParticle::join('projects','projects.id','=','lists_particle.project_id')
            //->join('account_project','projects.id','=','account_project.project_id')
            //->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            ->join('lists_date', function ($join){
                $join->on('lists_date.start', '<=' ,'lists_particle.no')
                ->on('lists_date.end', '>=' ,'lists_particle.no')
                ->on('lists_date.list_id', '=' ,'lists_particle.list_id');
            })
            ->whereIn('lists_particle.project_id', $projectIds)
        
          
            // ->whereHas('tableA', function ($query) use ($start, $end) {
            //     $query->where('no', '>=', $start)
            //         ->where('no', '<=', $end);
            // })
            ->select(
                'lists_particle.*','lists_particle.id as lists_particle_id', 
                'projects.id as project_id', 
                'lists_date.date as date',
              
                // 'relis_accounts.id as account_id',
                // 'relis_accounts.name as account_name', 
                // 'relis_accounts.relis_user_id as user_id', 

                'projects.start_datetime as project_start_datetime')
           
            ->get();
            //Log::debug($lists_particle);

            $lists_date = ListDate::join('projects','projects.id','=','lists_date.project_id')
            // ->join('account_project','projects.id','=','account_project.project_id')
            // ->join('relis_accounts','relis_accounts.id','=','account_project.account_id')
            ->join('lists','lists.id','=','lists_date.list_id')
            ->whereIn('lists_date.project_id', $projectIds)
            ->select(
                'lists_date.*','lists_date.id as lists_date_id', 
                'projects.id as project_id', 
                'projects.project_name as project_name',
                // 'relis_accounts.id as account_id',
                // 'relis_accounts.name as account_name', 
                // 'relis_accounts.relis_user_id as user_id', 
                'lists.list_name as list_name',
                'projects.start_datetime as project_start_datetime')
            ->get();
            

            

            // 成功した場合のレスポンスを返す
            return response()->json([
                'status' => 'success',
                'message' => 'taskが正常に作成されました。',
                'list' => $list,
                'lists_particle' => $lists_particle,
                'lists_date' => $lists_date,

            ], 201);

        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        // リクエストから受け取ったデータを使って新しいプロジェクトを作成
        
    }

    public function update_progress_particle(Request $request) {
        
        $particleId = $request->input('particleId');
        // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $progress = $request->input('progress');
        $particleExists = ListParticle::where('id', $particleId)->exists();
        
        if(!$particleExists ) {
            return response()->json(['message' => 'リスト要素がありません'], 200);
        }
            // トランザクションを開始
        DB::beginTransaction();
        try {
        
            //Log::debug("bbbbbb");
            $task = ListParticle::find($particleId);
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

    public function delete(Request $request)
    {
        // プロジェクトを更新する処理
         // POSTされたUUIDを取得
        $listId = $request->input('listId');
        // $isDefault = filter_var($request->input('isDefault'), FILTER_VALIDATE_BOOLEAN);
        $listExists = RelisList::where('id', $listId)->exists();
     
        if(!$listExists) {
            return response()->json(['message' => 'アカウントがありません'], 200);
        }
          // トランザクションを開始
        DB::beginTransaction();
  
        try {
            
            $list = RelisList::find($listId);
            $list->delete();
        
            DB::commit();
            //Log::debug("dddd");
            // 成功メッセージを返すなどの処理を行う
            return response()->json(['message' => 'List delete successfully'], 201);
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバックしてエラーメッセージを返す
            DB::rollback();
            
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    
}