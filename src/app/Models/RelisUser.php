<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelisUser extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'name',
        'user_icon',
        'created_at',
        'updated_at',
    ];

}