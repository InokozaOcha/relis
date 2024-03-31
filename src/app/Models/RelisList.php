<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RelisList extends Model
{
    use HasFactory;
    protected $table = 'lists';
    protected $primaryKey = 'id'; // UUIDをプライマリキーとして指定
    public $incrementing = false; // インクリメントIDを使用しない

    protected $fillable = [
        'list_name',
        'prefix',
        'suffix',
        'list_description',
        'status',
        'progress',
        'category',
        'priority',
        'include_time',
        'project_id',
        'created_at',
        'updated_at',
    ];

}

