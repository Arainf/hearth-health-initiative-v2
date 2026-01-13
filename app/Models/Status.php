<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Status extends Model
{
    protected $table = 'status';
    public $timestamps = false;

    protected $fillable = [
        'status_name',
    ];

    public function records(): HasMany
    {
        return $this->hasmany(Records::class, 'status_id');
    }

}
