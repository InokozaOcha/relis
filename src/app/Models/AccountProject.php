<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountProject extends Model
{
    use HasFactory;
    protected $table = 'account_project';
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'account_id',
        'project_id',
        'permissions',
        'created_at',
        'updated_at',
    ];

}