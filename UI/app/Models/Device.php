<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'device_uid',
        'room_name',
        'is_active',
        'last_seen_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsOnlineAttribute()
    {
        return $this->last_seen_at !== null
            && $this->last_seen_at->gt(now()->subMinutes(5));
    }
}