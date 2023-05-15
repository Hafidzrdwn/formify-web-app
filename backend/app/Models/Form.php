<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'limit_one_response',
        'creator_id'
    ];

    public $timestamps = false;
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function allowed_domains()
    {
        return $this->hasMany(AllowedDomain::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}
