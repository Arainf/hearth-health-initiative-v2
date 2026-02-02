<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Automatically appended attributes
     */
    protected $appends = ['ai_ready'];

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'openai_api_key',
        'ai_prompt',
        'is_doctor',
        'is_admin',
        'ai_access',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'ai_access' => 'boolean',
        'is_admin'  => 'boolean',
        'is_doctor' => 'boolean',
        'password'  => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =========================================================
       AI HELPERS
    ========================================================= */


    public function hasCompleteAIConfig(): bool
    {
        return !empty($this->openai_api_key)
            && !empty($this->ai_prompt);
    }


    public function canUseAI(): bool
    {
        return $this->ai_access
            && $this->hasCompleteAIConfig();
    }

    public function is_Doctor(): bool
    {
        return $this->is_doctor;
    }

    public function getAiReadyAttribute(): bool
    {
        return $this->canUseAI();
    }
}
