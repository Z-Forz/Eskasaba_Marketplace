<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'school_number',
        'name',
        'type',
        'birth_date',
        'class',
        'major',
        'phone',
    ];
}
