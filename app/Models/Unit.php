<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';
    public $timestamps = false;
    protected $fillable = [
        'unit_code',
        'unit_name',
        'unit_abbr',
        'unit_group_code',
    ];
}
