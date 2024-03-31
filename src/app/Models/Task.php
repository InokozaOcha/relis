<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Task extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'task_name',
        'task_description',
        'status',
        'progress',
        'category',
        'priority',
        'start_datetime',
        'end_datetime',
        'include_time',
        'project_id',
    ];

    // public function Project(): BelongsToMany
    // {
    //     return $this->belongsToMany(Project::class, 'projects', 'project_id', 'account_id');
    // }

}
