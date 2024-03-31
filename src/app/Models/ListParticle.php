<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListParticle extends Model
{
    use HasFactory;
    protected $table = 'lists_particle';
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'id',
        'no',
        'description',
        'status',
        'progress',
        'category',
        'priority',
        'include_time',
        'project_id',
        'list_id',
        'created_at',
        'updated_at',
    ];

}

