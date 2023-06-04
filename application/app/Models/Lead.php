<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'date',
        'person_id',
        'smartis_id',
        'lead_id',
        'first_click',
        'last_click',
        'send',
    ];
}
