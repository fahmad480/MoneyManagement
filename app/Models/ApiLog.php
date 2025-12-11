<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'api_key_id',
        'endpoint',
        'method',
        'ip_address',
        'status_code',
        'request_body',
        'response_body',
        'response_time',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the API key that owns the log
     */
    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }
}
