<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'ip_address',
        'email',
        'user_agent',
        'email_verified_at',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
