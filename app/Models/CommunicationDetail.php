<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationDetail extends Model
{
    protected $fillable = [
        'communication_id',
        'key',
        'value',
    ];

    use HasFactory;
}
