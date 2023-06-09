<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'form_id',
        'name',
        'choice_type',
        'choices',
        'is_required'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public $timestamps = false;

    public function answer()
    {
        return $this->hasOne(Answer::class);
    }
}
