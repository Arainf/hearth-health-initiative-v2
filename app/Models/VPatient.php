<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VPatient extends Model
{
    protected $table = 'v_patients';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    /**
     * Make the model read-only
     */
    protected static function booted()
    {
        static::creating(fn () => false);
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }
}
