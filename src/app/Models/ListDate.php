<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListDate extends Model
{
    use HasFactory;
    protected $table = 'lists_date';
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'id',
        'no',
        'date',
        'start',
        'end',
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

