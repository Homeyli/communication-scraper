<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = [
        'type',
        'uniqid',
        'slug',
        'name',
        'description',
        'source',

    ];

    use HasFactory;
}
