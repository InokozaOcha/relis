<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class RelisAccount extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'name',
        'acocunt_icon',
        'relis_user_id',
        'is_default_account',
        'created_at',
        'updated_at',
    ];
    
    public function project()
    {
        return $this->belongsToMany(Project::class, 'account_project', 'account_id', 'project_id');
    }



}