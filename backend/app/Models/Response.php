<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;
    protected $fillable = [
        'form_id',
        'user_id',
        'date'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public $timestamps = false;
}
