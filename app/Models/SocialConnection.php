<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'name',
        'email',
        'avatar',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'metadata',
        'connection_type',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getProviders(): array
    {
        return ['google', 'facebook', 'github', 'twitter', 'linkedin', 'apple'];
    }

    public static function getConnectionTypes(): array
    {
        return ['login', 'connect'];
    }
}
