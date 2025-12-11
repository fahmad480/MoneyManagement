<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'key',
        'description',
        'last_used_at',
        'is_active',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Generate a unique API key
     */
    public static function generateKey(): string
    {
        return Str::random(64);
    }

    /**
     * Get the user that owns the API key
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the logs for the API key
     */
    public function logs()
    {
        return $this->hasMany(ApiLog::class);
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed()
    {
        $this->last_used_at = now();
        $this->save();
    }

    /**
     * Scope for active keys only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
