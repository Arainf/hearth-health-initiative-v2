<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit_group extends Model
{
    use HasFactory;

    protected $table = 'unit_group';

    protected $fillable = [
        'unit_group_code',
        'unit_group_name',
    ];
}
