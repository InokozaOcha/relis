<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RelisAccount;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Project extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'project_name',
        'description',
        'status',
        'category',
        'priority',
        'start_datetime',
        'end_datetime',
        'include_time',
    ];

    public function relisAccount(): BelongsToMany
    {
        return $this->belongsToMany(RelisAccount::class, 'account_project', 'project_id', 'account_id');
    }

}
