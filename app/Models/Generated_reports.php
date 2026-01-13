<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Generated_reports extends Model
{
    protected $fillable = [
        'generated_text',
        'staff_generated',
        'staff_updates',
    ];



    public function records(): HasOne
    {
        return $this->hasOne(Records::class, 'generated_id');
    }

}
