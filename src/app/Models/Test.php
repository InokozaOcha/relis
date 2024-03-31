<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    protected $table = 'tests';
    protected $fillable = [
        'id',
        'user_name',
        'user_id',
        'user_password',
    ];
 
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
}
